<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/NotificationModel.php'; // El modelo que acabamos de refactorizar
// (Asegúrate de incluir las dependencias que usaba tu 'buildPanelBiomarkerMappings')

class NotificationService
{
    private $db;
    private $notificationModel;

    public function __construct(Database $db, NotificationModel $notificationModel)
    {
        $this->db = $db;
        $this->notificationModel = $notificationModel;
    }

    /**
     * Esta es la función principal que revisa un registro y crea alertas si es necesario.
     * Deberías llamar a esto CADA VEZ que se crea o actualiza un registro de panel.
     *
     * @param string $userId El ID del usuario al que pertenece el registro.
     * @param string $panelId El ID del panel (de la tabla 'test_panels').
     * @param string $recordId El ID del registro específico (ej: el UUID de 'blood_test_results').
     * @return array Un resumen de las alertas creadas o errores.
     */
    public function checkAndCreateAlertsForRecord(string $userId, string $panelId, string $recordId): array
    {
        try {
            // 1. Obtener los mappings (qué tabla y campos usar)
            $mappings = $this->buildPanelBiomarkerMappings();
            if (isset($mappings['error'])) {
                throw new Exception("Error al mapear paneles: " . $mappings['error']);
            }
            if (!isset($mappings['table_map'][$panelId])) {
                 throw new InvalidArgumentException("Panel ID '{$panelId}' no encontrado en mappings.");
            }
            
            $tableName = $mappings['table_map'][$panelId];
            $idField = $mappings['id_fields'][$panelId] ?? 'id'; // Asumir 'id' si no se encuentra

            // 2. Obtener datos del usuario (para la edad)
            $userData = $this->getUserData($userId);
            if (!$userData) {
                 throw new Exception("Usuario '{$userId}' no encontrado.");
            }
            $age = $userData['age'];

            // 3. Obtener los biomarcadores que aplican a este panel
            $biomarkers = $this->getBiomarkersForPanel($panelId);
            if (empty($biomarkers)) {
                return ['status' => 'info', 'message' => 'No hay biomarcadores configurados para este panel.'];
            }

            // 4. Obtener los datos del registro (el resultado del laboratorio)
            $recordData = $this->getRecordData($tableName, $idField, $recordId);
            if (!$recordData) {
                 throw new Exception("Registro '{$recordId}' no encontrado en tabla '{$tableName}'.");
            }

            $alertsCreated = 0;
            $alertsSkipped = 0;
            $processed = [];

            // 5. Iterar sobre cada biomarcador y aplicar la lógica
            foreach ($biomarkers as $bm) {
                $biomarkerId = $bm['biomarker_id'];
                $field_key = strtolower(trim($bm['name_db'] ?? '')); // Usamos 'name_db' como la columna

                // Si el registro no tiene esta columna, o la columna no está en el mapping, saltar
                if ($field_key === '' || !isset($recordData[$field_key])) {
                    continue;
                }
                
                $valueStr = $recordData[$field_key];
                // Ignorar valores no numéricos para la lógica de rangos (excepto 'body_age' que debe ser numérico)
                if (!is_numeric($valueStr)) {
                    continue; 
                }
                $value = (float)$valueStr;

                // --- INICIO DE LÓGICA DE NEGOCIO (Tu lógica del modelo antiguo) ---
                $status = null;
                $ref_min = is_numeric($bm['reference_min']) ? (float)$bm['reference_min'] : null;
                $ref_max = is_numeric($bm['reference_max']) ? (float)$bm['reference_max'] : null;

                $bm_name_db = strtolower(trim($bm['name_db'] ?? ''));

                if ($bm_name_db === 'body_age' && $age !== null) {
                    if ($value > $age) $status = 'High';
                } elseif ($ref_min !== null && $value < $ref_min) {
                    $status = 'Low';
                } elseif ($ref_max !== null && $value > $ref_max) {
                    $status = 'High';
                }
                // --- FIN DE LÓGICA DE NEGOCIO ---

                // 6. Si se cumple la condición, crear la notificación
                if ($status !== null) {
                    
                    // 7. Verificar si ya existe (para no duplicar)
                    // Usamos la función 'exists' que busca en el JSON
                    $exists = $this->notificationModel->exists($userId, $recordId, $biomarkerId);

                    if (!$exists) {
                        // 8. Preparar datos para el NotificationModel (nuevo formato)
                        $notificationData = [
                            'user_id' => $userId,
                            'template_key' => 'biomarker_alert', // Clave estándar
                            'module' => 'Biomarkers',
                            'route' => "/biomarkers/record/{$recordId}?panel={$panelId}", // Ruta sugerida
                            'rol' => 'user',
                            'new' => 1, // Marcar como nueva
                            'no_alert_user' => 0, // Marcar como no leída (read_unread = 0)

                            // Datos que van al JSON 'template_params'
                            'id_panel' => $panelId,
                            'id_record' => $recordId,
                            'id_biomarker' => $biomarkerId,
                            'status' => $status,
                            'value' => $value, // Guardamos el valor que disparó la alerta
                            'biomarker_name' => $bm['name'] ?? '',
                            'reference_range' => "{$ref_min} - {$ref_max}"
                        ];
                        
                        $result = $this->notificationModel->create($notificationData);
                        if ($result['status'] === 'success') {
                            $alertsCreated++;
                        }
                    } else {
                        $alertsSkipped++;
                    }
                } // fin if (status)
                $processed[] = $field_key;
            } // fin foreach (biomarker)

            return [
                'status' => 'success',
                'alerts_created' => $alertsCreated,
                'alerts_skipped' => $alertsSkipped,
                'biomarkers_processed' => $processed
            ];

        } catch (\Throwable $e) {
            error_log("[NotificationService] Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }


    /* ========================================================================
     * SECCIÓN 2: AYUDANTES DE LÓGICA
     * ======================================================================== */

    /**
     * Obtiene los datos del usuario (edad).
     */
    private function getUserData(string $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT birthday FROM users WHERE user_id = ? LIMIT 1");
        $stmt->bind_param('s', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
        $stmt->close();

        if (!$userData || empty($userData['birthday'])) {
            return ['age' => null];
        }

        try {
            $birthdate = new DateTime($userData['birthday']);
            $today = new DateTime();
            $age = $today->diff($birthdate)->y;
            return ['age' => $age];
        } catch (Exception $e) {
            return ['age' => null];
        }
    }

    /**
     * Obtiene los datos del registro del panel específico.
     */
    private function getRecordData(string $tableName, string $idField, string $recordId): ?array
    {
        // Sanitización básica de nombres de tabla/campo
        $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
        $idField = preg_replace('/[^a-zA-Z0-9_]/', '', $idField);

        $sql = "SELECT * FROM `{$tableName}` WHERE `{$idField}` = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $recordId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }

    /**
     * Obtiene todos los biomarcadores (reglas) para un panel.
     */
    private function getBiomarkersForPanel(string $panelId): array
    {
        $sql = "SELECT biomarker_id, name, name_es, name_db, reference_min, reference_max
                FROM biomarkers
                WHERE panel_id = ? AND deleted_at IS NULL"; // Asumiendo soft-delete
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $panelId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }


    /**
     * Mapeador de Paneles y Biomarcadores (COPIADO DE TU MODELO ANTIGUO).
     * Esta función es la "inteligencia" que mapea paneles a tablas
     * y biomarcadores a columnas.
     */
    private function buildPanelBiomarkerMappings(): array
    {
        try {
            $idioma = $_SESSION['idioma'] ?? 'EN';

            // Obtener todos los paneles válidos
            $queryPanels = "SELECT panel_id, panel_name FROM test_panels WHERE deleted_at IS NULL ORDER BY display_name";
            $resultPanels = $this->db->query($queryPanels);
            if (!$resultPanels) {
                throw new mysqli_sql_exception("Error al obtener paneles: " . $this->db->error);
            }

            $table_map  = [];
            $date_fields = [];
            $time_fields = [];
            $id_fields   = [];

            while ($panel = $resultPanels->fetch_assoc()) {
                $panel_id   = $panel['panel_id'];
                $panel_name = strtolower(trim($panel['panel_name']));
                $table_map[$panel_id] = $panel_name;

                // Detectar campos de fecha/hora/id en la tabla correspondiente
                $resultDesc = $this->db->query("DESCRIBE `{$panel_name}`");
                if (!$resultDesc) {
                    throw new mysqli_sql_exception("Error al obtener estructura de tabla {$panel_name}: " . $this->db->error);
                }
                while ($col = $resultDesc->fetch_assoc()) {
                    if (preg_match('/_date$/', $col['Field'])) {
                        $date_fields[$panel_id] = $col['Field'];
                    }
                    if (preg_match('/_time$/', $col['Field'])) {
                        $time_fields[$panel_id] = $col['Field'];
                    }
                    // Cambio: busca el _id que coincide con el nombre de la tabla (ej: blood_test_id)
                    $expected_id_field = rtrim($panel_name, 's') . '_id'; // ej. blood_test_result -> blood_test_result_id
                    if ($col['Field'] === $expected_id_field || $col['Field'] === $panel_name . '_id') {
                         $id_fields[$panel_id] = $col['Field'];
                    } elseif (preg_match('/_id$/', $col['Field']) && !isset($id_fields[$panel_id])) {
                        // Fallback al primer _id si no se encuentra el específico
                        $id_fields[$panel_id] = $col['Field']; 
                    }
                }
            }

            // Traer todos los biomarcadores usando name, name_es y name_db
            $queryBiomarkers = "SELECT biomarker_id, panel_id, name, name_es, name_db FROM biomarkers ORDER BY name";
            $resultBiomarkers = $this->db->query($queryBiomarkers);
            if (!$resultBiomarkers) {
                throw new mysqli_sql_exception("Error al obtener biomarcadores: " . $this->db->error);
            }

            $field_map = [];

            while ($biomarker = $resultBiomarkers->fetch_assoc()) {
                $panel_id = $biomarker['panel_id'];

                // Todos en minúsculas y recortados
                $key_en  = strtolower(trim($biomarker['name'] ?? ''));
                $key_es  = strtolower(trim($biomarker['name_es'] ?? ''));
                $key_db  = strtolower(trim($biomarker['name_db'] ?? ''));

                if ($key_db === '') {
                    $key_db = preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $key_en);
                }
                
                // Aseguramos que el panel exista en el mapa de tablas antes de añadir
                if (!isset($table_map[$panel_id])) {
                    continue;
                }

                if ($key_en !== '') {
                    $field_map[$panel_id][$key_en] = [$key_db];
                }
                if ($key_es !== '') {
                    $field_map[$panel_id][$key_es] = [$key_db];
                }

                // (Opcional) variantes normalizadas
                $norm_en = preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $key_en);
                if ($norm_en !== '' && $norm_en !== $key_en) {
                    $field_map[$panel_id][$norm_en] = [$key_db];
                }
                $norm_es = preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $key_es);
                if ($norm_es !== '' && $norm_es !== $key_es) {
                    $field_map[$panel_id][$norm_es] = [$key_db];
                }
            }

            return [
                'table_map'   => $table_map,
                'id_fields'   => $id_fields,
                'date_fields' => $date_fields,
                'time_fields' => $time_fields,
                'field_map'   => $field_map
            ];

        } catch (mysqli_sql_exception $e) {
            return [
                'table_map'   => [],
                'id_fields'   => [],
                'date_fields' => [],
                'time_fields' => [],
                'field_map'   => [],
                'error'       => $e->getMessage()
            ];
        }
    }
}