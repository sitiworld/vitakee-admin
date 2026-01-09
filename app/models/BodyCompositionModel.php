<?php

require_once __DIR__ . '/../config/Database.php';

// === Dependencias ===
require_once __DIR__ . '/../models/BiomarkerModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../helpers/NotificationTemplateHelper.php'; // Incluir el Helper
require_once __DIR__ . '/../config/ClientEnvironmentInfo.php'; // Para auditoría
require_once __DIR__ . '/../config/TimezoneManager.php'; // Para auditoría


class BodyCompositionModel
{
    private $db;
    private $table = "body_composition";

    public $body_composition_id;
    public $user_id;
    public $composition_date;
    public $composition_time;
    public $weight_lb;
    public $bmi;
    public $body_fat_pct;
    public $water_pct;
    public $muscle_pct;
    public $resting_metabolism;
    public $visceral_fat;
    public $body_age;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllByUserId($user_id)
    {
        try {
            $query = "SELECT body_composition_id, composition_date, composition_time
                      FROM {$this->table}
                      WHERE user_id = ?
                      ORDER BY composition_date DESC";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
            }

            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }

            $stmt->close();
            return $items;

        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT *
                                        FROM {$this->table} WHERE body_composition_id = ? LIMIT 1");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar consulta: " . $this->db->error);
            }
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if ($data) {
                return $this->jsonResponse(true, '', $data);
            } else {
                return ['status' => 'error', 'message' => 'Record not found.'];
            }
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    public function getByIdUser($user_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar consulta: " . $this->db->error);
            }
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);

            if ($data) {
                return $this->jsonResponse(true, '', $data);
            } else {
                return $this->jsonResponse(false, 'No records found.', []);
            }
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    private function buildPanelBiomarkerMappingsByPanelId($panelId): array
    {
        try {
            $sex_biological = strtolower($_SESSION['sex_biological'] ?? 'm');

            $query = "SELECT biomarker_id, name, name_db FROM biomarkers WHERE panel_id = ? AND deleted_at IS NULL ORDER BY name";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparando query: " . $this->db->error);
            }
            $stmt->bind_param("s", $panelId);
            $stmt->execute();
            $result = $stmt->get_result();

            // Este es el mapeo fijo que respetamos siempre
            $biomarkerNameMapping = [
                'body mass index (bmi)' => 'bmi',
                'body fat percentage - male' => 'body_fat_pct',
                'body fat percentage - female' => 'body_fat_pct',
                'total body water percentage' => 'water_pct',
                'muscle mass percentage - male' => 'muscle_pct',
                'muscle mass percentage - female' => 'muscle_pct',
                'resting metabolic rate (rmr)' => 'resting_metabolism',
                'visceral fat level' => 'visceral_fat',
                'body age' => 'body_age'
            ];

            $biomarkers = [];
            $validFields = [];

            while ($row = $result->fetch_assoc()) {
                $id = $row['biomarker_id'];
                $name = strtolower(trim($row['name']));

                if (!isset($biomarkerNameMapping[$name])) { // ignoramos los que no estén mapeados explícitamente
                    continue;
                }

                $fieldKey = $biomarkerNameMapping[$name];

                // Controlamos los campos dependientes de sex_biologicalo
                if ($name === 'body fat percentage - male' && $sex_biological === 'm') {
                    $biomarkers[$fieldKey] = $id;
                } elseif ($name === 'body fat percentage - female' && $sex_biological === 'f') {
                    $biomarkers[$fieldKey] = $id;
                } elseif ($name === 'muscle mass percentage - male' && $sex_biological === 'm') {
                    $biomarkers[$fieldKey] = $id;
                } elseif ($name === 'muscle mass percentage - female' && $sex_biological === 'f') {
                    $biomarkers[$fieldKey] = $id;
                } elseif (!str_contains($name, 'male') && !str_contains($name, 'female')) {
                    // Los que no dependen de sex_biologicalo
                    $biomarkers[$fieldKey] = $id;
                }

                $validFields[] = $fieldKey;
            }

            return [
                'biomarkers' => $biomarkers,
                'validFields' => array_unique($validFields)
            ];

        } catch (mysqli_sql_exception $e) {
            return [
                'biomarkers' => [],
                'validFields' => [],
                'error' => $e->getMessage()
            ];
        }
    }


    public function getBodyCompositionData($id)
    {
        try {
            $query = "
            SELECT bc.*, u.birthday, TIMESTAMPDIFF(YEAR, u.birthday, CURDATE()) AS age
            FROM {$this->table} bc
            JOIN users u ON bc.user_id = u.user_id
            WHERE bc.body_composition_id = ?
        ";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Prepare failed: " . $this->db->error);
            }

            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            $data = $result->fetch_assoc();
            $stmt->close();

            if ($data) {
                $data['age'] = (int) $data['age'];
                return $data;
            }

            return ['status' => 'error', 'message' => 'No data found'];
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }


    public function getUserBodyCompositionHistoryByRecordId($recordId, $field)
    {
        try {
            $this->db->begin_transaction();

            // Obtenemos el mapeo dinámico para el panel 2 (Body Composition)
            $mappings = $this->buildPanelBiomarkerMappingsByPanelId('81054d57-92c9-4df8-a6dc-51334c1d82c4');
            $validFields = $mappings['validFields'];

            if (!in_array($field, $validFields)) {
                throw new Exception('Invalid field.');
            }

            // Obtener el user_id a partir del record ID
            $stmt = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE body_composition_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing statement: " . $this->db->error);
            }

            $stmt->bind_param("s", $recordId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception('No record found for the given ID.');
            }

            $user_id = $result->fetch_assoc()['user_id'];
            $stmt->close();

            // Consultar el historial para el campo solicitado
            $query = "SELECT composition_date, {$field} FROM {$this->table} WHERE user_id = ? ORDER BY composition_date DESC";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
            }

            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $history = [];
            while ($row = $result->fetch_assoc()) {
                $history[] = [
                    'date' => date('m/d/Y', strtotime($row['composition_date'])),
                    'value' => $row[$field]
                ];
            }

            $this->db->commit();

            return [
                'status' => 'success',
                'data' => $history
            ];

        } catch (Exception $e) {
            $this->db->rollback();
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function exportBodyCompositionByUserToCSV($user_id)
    {
        try {
            $sql = "SELECT user_id, composition_date, composition_time, weight_lb, bmi, body_fat_pct,
                       water_pct, muscle_pct, resting_metabolism, visceral_fat, body_age
                FROM {$this->table}
                WHERE user_id = ?
                ORDER BY composition_date DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $filename = "body_composition_user_{$user_id}.csv";
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $output = fopen('php://output', 'w');

                fputcsv($output, [
                    'User ID',
                    'Date',
                    'Time',
                    'Weight (lb)',
                    'BMI',
                    'Body Fat (%)',
                    'Water (%)',
                    'Muscle (%)',
                    'Resting Metabolism',
                    'Visceral Fat',
                    'Body Age'
                ]);

                while ($row = $result->fetch_assoc()) {
                    fputcsv($output, $row);
                }

                fclose($output);
                exit;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No records found for this user.']);
            }
        } catch (mysqli_sql_exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }
    private function countExamsByBiomarker($user_id, $biomarker_field, $date)
    {
        $query = "SELECT COUNT(*) AS total 
              FROM {$this->table}
              WHERE user_id = ? 
                AND composition_date = ?
                AND {$biomarker_field} IS NOT NULL 
                AND {$biomarker_field} > 0";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $user_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return (int) $row['total'];
    }


public function create($data = null)
{
    $this->db->begin_transaction();
    try {
        // 1) Entrada segura
        if (!is_array($data) || empty($data)) {
            $raw = file_get_contents('php://input');
            $json = json_decode($raw, true);
            $data = is_array($json) && !empty($json) ? $json : $_POST;
        }

        $lang       = $_SESSION['idioma'] ?? 'EN';
        $created_by = $_SESSION['user_id'] ?? null;
        $userId     = $_SESSION['user_id'] ?? null;  // <-- usa el de sesión

        if (!$userId) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => 'User not authenticated'];
        }

        require_once __DIR__ . '/../models/BiomarkerModel.php';
        require_once __DIR__ . '/../models/NotificationModel.php';
        require_once __DIR__ . '/../helpers/NotificationTemplateHelper.php';


        $biomarkerModel    = new BiomarkerModel();
        $notificationModel = new NotificationModel();

        // Panel de Body Composition
        $mapping    = $this->buildPanelBiomarkerMappingsByPanelId('81054d57-92c9-4df8-a6dc-51334c1d82c4');
        $biomarkers = $mapping['biomarkers'];
        
        // ===== Auditoría + zona horaria
        $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $userId);
        $tzManager = new TimezoneManager($this->db);
        $tzManager->applyTimezone();

        // Esto es para la columna de auditoría 'created_at' y SÍ debe ser la del servidor.
        $createdAt = $env->getCurrentDatetime();
        
        // CAMBIO:
        // Ya que el frontend ahora SIEMPRE envía la fecha y hora (desde el JS),
        // usamos esos valores en lugar de autogenerarlos en el backend.
        // Usamos la fecha del servidor solo como un fallback de seguridad.
        
        $examDate = $data['composition_date'] ?? substr($createdAt, 0, 10);
        $examTime = $data['composition_time'] ?? substr($createdAt, 11, 8);
        
        // (Opcional) Sanity check por si el JS falla y manda un formato incorrecto
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $examDate)) {
            $examDate = substr($createdAt, 0, 10);
        }
        if (!preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $examTime)) {
            $examTime = substr($createdAt, 11, 8);
        }

        // Helper números: coma->punto
        $num = function ($key) use ($data) {
            if (!isset($data[$key])) return 0.0;
            $v = str_replace(',', '.', trim((string)$data[$key]));
            return is_numeric($v) ? (float)$v : 0.0;
        };

        // ===== Chequeo de límite diario (con user de sesión)
        foreach ($biomarkers as $field => $biomarkerId) {
            $value = $num($field);
            if ($value <= 0) continue;

            $maxResult = $biomarkerModel->getMaxExamById($biomarkerId);
            if (($maxResult['status'] ?? '') === 'success' && ($maxResult['max_exam'] ?? 0) > 0) {
                // Usamos $examDate (que ahora viene del cliente) para el chequeo
                $count = $this->countExamsByBiomarker($userId, $field, $examDate); 
                if ($count >= (int)$maxResult['max_exam']) {
                    
                    $bmInfo = $biomarkerModel->getById($biomarkerId);
                    $biomarkerName = $bmInfo['name_db'] ?? "Biomarker #$biomarkerId";

                    $message = $lang === 'ES'
                        ? "Se alcanzó el límite diario para el biomarcador: {$biomarkerName}"
                        : "Daily limit reached for biomarker: {$biomarkerName}";
                    $this->db->rollback();
                    return ['status' => 'error', 'message' => $message];
                }
            }
        }

        // ===== Normalizar numéricos
        $weight_lb          = $num('weight_lb');
        $bmi                = $num('bmi');
        $body_fat_pct       = $num('body_fat_pct');
        $water_pct          = $num('water_pct');
        $muscle_pct         = $num('muscle_pct');
        $resting_metabolism = $num('resting_metabolism');
        $visceral_fat       = $num('visceral_fat');
        $body_age           = $num('body_age');

        // ===== UUID
        $uuid = $this->generateUUIDv4();

        // ===== INSERT usando ahora la fecha/hora del CLIENTE
        $query = "INSERT INTO {$this->table}
                    (body_composition_id, user_id, composition_date, composition_time,
                     weight_lb, bmi, body_fat_pct, water_pct, muscle_pct, resting_metabolism, visceral_fat, body_age,
                     created_by, created_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new mysqli_sql_exception("Error preparing insert query: " . $this->db->error);
        }

        $stmt->bind_param(
            "ssssddddddddss",
            $uuid,
            $userId,       // <-- usa sesión, no $data['user_id']
            $examDate,     // <-- CAMBIO: Ahora es la fecha enviada por el cliente
            $examTime,     // <-- CAMBIO: Ahora es la hora enviada por el cliente
            $weight_lb,
            $bmi,
            $body_fat_pct,
            $water_pct,
            $muscle_pct,
            $resting_metabolism,
            $visceral_fat,
            $body_age,
            $created_by,
            $createdAt      // <-- La columna 'created_at' (auditoría) SÍ usa la fecha del servidor
        );
        $stmt->execute();
        $stmt->close();

        // ===== Notificaciones por rango
        foreach ($biomarkers as $field => $biomarkerId) {
            $value = $num($field);
            if ($value <= 0) continue;

            $eval = $biomarkerModel->evaluateBiomarkerValueStatus($biomarkerId, $value);
            if (isset($eval['status'], $eval['result'])
                && $eval['status'] === 'success'
                && in_array($eval['result'], ['High', 'Low'])) {
                
                // *** INICIO BLOQUE MODIFICADO ***
                
                // 1. Obtener datos completos del biomarcador
                $biomarkerInfo = $biomarkerModel->getById($biomarkerId);
                $biomarkerName = $biomarkerInfo['name_db'] ?? $field; // <-- USAR NAME_DB
                $status_lang = $eval['result'];
                if ($lang === 'ES') {
                    $status_lang = ($eval['result'] === 'High') ? 'Alto' : 'Bajo';
                }

                // 2. Preparar parámetros para la plantilla
                $params = [
                    'biomarker_name' => $biomarkerName,
                    'value'          => $value,
                    'unit'           => $biomarkerInfo['unit'] ?? '',
                    'status'         => $status_lang,
                    'ref_min'        => $biomarkerInfo['reference_min'] ?? 'N/A',
                    'ref_max'        => $biomarkerInfo['reference_max'] ?? 'N/A',
                    'id_biomarker'   => $biomarkerId,
                    'id_record'      => $uuid 
                ];
                
                // 3. Construir la fila de notificación
                $dataRow = NotificationTemplateHelper::buildForInsert([
                    'template_key'    => 'biomarker_out_of_range',
                    'template_params' => $params,
                    'route'           => 'component_body_composition?id=' . $uuid, // Ruta (ejemplo)
                    'module'          => 'body_composition',
                    'user_id'         => $userId, // <-- sesión
                ]);

                // 4. Crear la notificación
                $notificationModel->create($dataRow);
                // *** FIN BLOQUE MODIFICADO ***
            }
        }

        $this->db->commit();
        return ['status' => 'success', 'message' => 'Body composition record created successfully.'];

    } catch (mysqli_sql_exception $e) {
        $this->db->rollback();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}




    private function generateUUIDv4(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }




    public function update($data)
    {
        $this->db->begin_transaction();
        try {
            $lang = $_SESSION['idioma'] ?? 'EN';
            $updated_by = $_SESSION['user_id'] ?? null; // Usuario que realiza la acción

            $checkQuery = "SELECT body_composition_id, user_id FROM {$this->table} WHERE body_composition_id = ?";
            $checkStmt = $this->db->prepare($checkQuery);
            if (!$checkStmt) {
                throw new mysqli_sql_exception("Error preparing check query: " . $this->db->error);
            }

            $checkStmt->bind_param("s", $data['id']); // UUID como string
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            if ($result->num_rows === 0) {
                $checkStmt->close();
                $this->db->rollback();

                $msg = ($lang === 'ES')
                    ? 'Registro de composición corporal no encontrado.'
                    : 'Body composition record not found.';

                return ['status' => 'error', 'message' => $msg];
            }

            $existing = $result->fetch_assoc();
            $user_id = $existing['user_id']; // Usuario al que pertenece el registro
            $checkStmt->close();

            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId); // Auditoría con usuario en sesión
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $updatedAt = $env->getCurrentDatetime();

            $query = "UPDATE {$this->table} SET
                    composition_date = ?, composition_time = ?, weight_lb = ?, bmi = ?, 
                    body_fat_pct = ?, water_pct = ?, muscle_pct = ?, resting_metabolism = ?, 
                    visceral_fat = ?, body_age = ?, updated_by = ?, updated_at = ?
                  WHERE body_composition_id = ?";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing update query: " . $this->db->error);
            }

            $stmt->bind_param(
                "ssdddddddsiss",
                $data['composition_date'],
                $data['composition_time'],
                $data['weight_lb'],
                $data['bmi'],
                $data['body_fat_pct'],
                $data['water_pct'],
                $data['muscle_pct'],
                $data['resting_metabolism'],
                $data['visceral_fat'],
                $data['body_age'],
                $updated_by,
                $updatedAt,
                $data['id']
            );
            $stmt->execute();
            $stmt->close();

            require_once __DIR__ . '/../models/BiomarkerModel.php';
            require_once __DIR__ . '/../models/NotificationModel.php';
            require_once __DIR__ . '/../helpers/NotificationTemplateHelper.php';


            $biomarkerModel = new BiomarkerModel();
            $notificationModel = new NotificationModel();

            $mapping = $this->buildPanelBiomarkerMappingsByPanelId('81054d57-92c9-4df8-a6dc-51334c1d82c4');
            $biomarkers = $mapping['biomarkers'];

            foreach ($biomarkers as $field => $biomarkerId) {
                $value = floatval($data[$field]);
                $result = $biomarkerModel->evaluateBiomarkerValueStatus($biomarkerId, $value);

                $stmtNotif = $this->db->prepare("SELECT notification_id FROM notifications WHERE id_record = ? AND id_biomarker = ? AND deleted_at IS NULL");
                $stmtNotif->bind_param("ss", $data['id'], $biomarkerId);
                $stmtNotif->execute();
                $notifResult = $stmtNotif->get_result();
                $notif = $notifResult->fetch_assoc();
                $stmtNotif->close();

                if (
                    isset($result['status'], $result['result']) &&
                    $result['status'] === 'success' &&
                    in_array($result['result'], ['High', 'Low'])
                ) {
                    // *** INICIO BLOQUE MODIFICADO (Crear o Actualizar) ***
                
                    // 1. Obtener datos completos del biomarcador
                    $biomarkerInfo = $biomarkerModel->getById($biomarkerId);
                    $biomarkerName = $biomarkerInfo['name_db'] ?? $field; // <-- USAR NAME_DB
                    $status_lang = $result['result'];
                    if ($lang === 'ES') {
                        $status_lang = ($result['result'] === 'High') ? 'Alto' : 'Bajo';
                    }

                    // 2. Preparar parámetros para la plantilla
                    $params = [
                        'biomarker_name' => $biomarkerName,
                        'value'          => $value,
                        'unit'           => $biomarkerInfo['unit'] ?? '',
                        'status'         => $status_lang,
                        'ref_min'        => $biomarkerInfo['reference_min'] ?? 'N/A',
                        'ref_max'        => $biomarkerInfo['reference_max'] ?? 'N/A',
                        'id_biomarker'   => $biomarkerId,
                        'id_record'      => $data['id']
                    ];

                    $route = 'component_body_composition?id=' . $data['id']; // Ruta (ejemplo)

                    if ($notif) {
                        // 3A. Actualizar notificación existente
                        $updateData = [
                            'id' => $notif['notification_id'],
                            'template_params' => $params,
                            'new' => 1,
                            'read_unread' => 0,
                            'route' => $route,
                            'module' => 'body_composition',
                        ];
                        $notificationModel->update($updateData); 
                    
                    } else {
                        // 3B. Crear notificación (no existía)
                        $dataRow = NotificationTemplateHelper::buildForInsert([
                            'template_key'    => 'biomarker_out_of_range',
                            'template_params' => $params,
                            'route'           => $route,
                            'module'          => 'body_composition',
                            'user_id'         => $user_id // <-- El ID del dueño del registro
                        ]);
                        $notificationModel->create($dataRow);
                    }
                    // *** FIN BLOQUE MODIFICADO ***

                } else {
                    // Valor en rango normal
                    if ($notif) {
                        $notificationModel->delete($notif['notification_id']); // Soft delete
                    }
                }
            }

            $this->db->commit();
            return ['status' => 'success', 'message' => 'Body composition record updated successfully.'];

        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }




    public function delete($id)
    {
        $lang = $_SESSION['idioma'] ?? 'EN';
        $deleted_by = $_SESSION['user_id'] ?? null;

        $this->db->begin_transaction();
        try {
            // Verificar si el registro existe y no ha sido eliminado previamente
            $checkQuery = "SELECT body_composition_id FROM {$this->table} WHERE body_composition_id = ? AND deleted_at IS NULL";
            $checkStmt = $this->db->prepare($checkQuery);
            if (!$checkStmt) {
                throw new mysqli_sql_exception("Error preparing check query: " . $this->db->error);
            }

            $checkStmt->bind_param("s", $id);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows === 0) {
                $checkStmt->close();
                $this->db->rollback();

                $msg = $lang === 'ES'
                    ? 'Registro de composición corporal no encontrado.'
                    : 'Body composition record not found.';
                return ['status' => 'error', 'message' => $msg];
            }
            $checkStmt->close();

            // Aplicar contexto de auditoría y zona horaria
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $deleted_at = $env->getCurrentDatetime(); // Fecha/hora ajustada

            // Soft delete con campos de auditoría
            $query = "UPDATE {$this->table} SET deleted_by = ?, deleted_at = ? WHERE body_composition_id = ?";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing delete query: " . $this->db->error);
            }

            $stmt->bind_param("sss", $deleted_by, $deleted_at, $id);
            $stmt->execute();
            $stmt->close();
            
            // *** NUEVO: Eliminar notificaciones asociadas ***
            if (class_exists('NotificationModel')) {
                 $notificationModel = new NotificationModel();
                 $stmtNotifs = $this->db->prepare("SELECT notification_id FROM notifications WHERE id_record = ?");
                 $stmtNotifs->bind_param("s", $id);
                 $stmtNotifs->execute();
                 $resNotifs = $stmtNotifs->get_result();
                 while ($notif = $resNotifs->fetch_assoc()) {
                     $notificationModel->delete($notif['notification_id']); // Usar soft delete
                 }
                 $stmtNotifs->close();
            }
            // *** FIN BLOQUE NUEVO ***

            $this->db->commit();

            return ['status' => 'success', 'message' => 'Body composition record deleted successfully.'];
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }



    private function getJsonInput(): array
    {
        $input = file_get_contents("php://input");
        return json_decode($input, true) ?? [];
    }

    private function jsonResponse(bool $value, string $message = '', $data = null)
    {
        header('Content-Type: application/json');

        $response = [
            'value' => $value,
            'message' => $message,
            'data' => is_array($data) ? $data : ($data !== null ? [$data] : [])
        ];

        echo json_encode($response);
        exit;
    }
}
