<?php

require_once __DIR__ . '/../config/Database.php';

// Dependencias para auditoría y zona horaria (basado en tus otros modelos)
require_once __DIR__ . '/../config/ClientEnvironmentInfo.php';
require_once __DIR__ . '/../config/TimezoneManager.php';

class LipidProfileModel
{
    private $db;
    private $table = "lipid_profile_record";

    public $lipid_profile_record_id;
    public $user_id;
    public $lipid_profile_date;
    public $lipid_profile_time;
    public $ldl;
    public $hdl;
    public $total_cholesterol;
    public $triglycerides;
    public $non_hdl;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        try {
            $query = "SELECT *
                      FROM {$this->table}
                      ORDER BY lipid_profile_date DESC";
            $result = $this->db->query($query);
            if (!$result) {
                throw new mysqli_sql_exception("Error fetching lipid profile records: " . $this->db->error);
            }

            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }

            if (count($items) > 0) {
                return ['status' => 'success', 'data' => $items];
            } else {
                return ['status' => 'error', 'message' => 'No records found.'];
            }
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function exportLipidProfilesByUserToCSV($user_id)
    {
        try {
            $sql = "SELECT lipid_profile_record_id , user_id, lipid_profile_date, lipid_profile_time, ldl, hdl, total_cholesterol, triglycerides, non_hdl
                FROM {$this->table}
                WHERE user_id = ?
                ORDER BY lipid_profile_date DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $filename = "lipid_profiles_user_{$user_id}.csv";
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $output = fopen('php://output', 'w');

                // CSV Header
                fputcsv($output, ['lipid_profile_record_id ', 'User ID', 'Date', 'Time', 'LDL', 'HDL', 'Total Cholesterol', 'Triglycerides', 'Non-HDL']);

                // Data rows
                while ($row = $result->fetch_assoc()) {
                    fputcsv($output, $row);
                }

                fclose($output);
                exit;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No lipid profile records found for this user.']);
            }
        } catch (mysqli_sql_exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }



    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE lipid_profile_record_id  = ? LIMIT 1");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
            }
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if ($data) {
                return $data;
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
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
            }
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);

            if (!empty($data)) {
                return $this->jsonResponse(true, '', $data);
            } else {
                return $this->jsonResponse(false, 'No records found.', []);
            }
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, $e->getMessage(), []);
        }
    }
    public function getUserLipidProfileHistoryByRecordId($recordId, $field)
    {
        try {
            $this->db->begin_transaction();

            // Obtenemos el mapeo dinámico para el panel 3 (Lipid Profile)
            $mapping = $this->buildLipidProfileBiomarkerMappings();
            $validFields = $mapping['validFields'];

            if (!in_array($field, $validFields)) {
                throw new Exception('Invalid field.');
            }

            // Obtener el user_id a partir del recordId
            $stmt = $this->db->prepare("SELECT user_id FROM lipid_profile_record WHERE lipid_profile_record_id = ?");
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

            // Consultar historial para ese campo
            $query = "SELECT lipid_profile_date, {$field} FROM lipid_profile_record WHERE user_id = ? ORDER BY lipid_profile_date DESC";
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
                    'date' => date('m/d/Y', strtotime($row['lipid_profile_date'])),
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



    public function getByUserId($user_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT lipid_profile_record_id , lipid_profile_date, lipid_profile_time FROM {$this->table} WHERE user_id = ? ORDER BY lipid_profile_date DESC");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
            }
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $records = [];

            while ($row = $result->fetch_assoc()) {
                $records[] = $row;
            }

            if (count($records) > 0) {
                return $records;
            } else {
                return ['status' => 'error', 'message' => 'No records found for this user.'];
            }
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    private function countExamsByBiomarker($user_id, $biomarker_field, $date)
    {
        $query = "SELECT COUNT(*) AS total 
              FROM {$this->table}
              WHERE user_id = ? 
                AND lipid_profile_date = ?
                AND {$biomarker_field} IS NOT NULL 
                AND {$biomarker_field} > 0";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $user_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return (int) $row['total'];
    }
    private function buildLipidProfileBiomarkerMappings(): array
    {
        try {
            $sex_biological = strtolower($_SESSION['sex_biological'] ?? 'm');

            // Panel 3 → Lipid Profile
            $panelId = 'e6861593-7327-4f63-9511-11d56f5398dc';

            $query = "SELECT biomarker_id, name, name_db FROM biomarkers WHERE panel_id = ? AND deleted_at IS NULL ORDER BY name";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparando query: " . $this->db->error);
            }
            $stmt->bind_param("s", $panelId);
            $stmt->execute();
            $result = $stmt->get_result();

            // Mapeo exacto de nombres a keys
            $biomarkerNameMapping = [
                'ldl cholesterol' => 'ldl',
                'hdl cholesterol - male' => 'hdl',
                'hdl cholesterol - female' => 'hdl',
                'total cholesterol' => 'total_cholesterol',
                'triglycerides' => 'triglycerides',
                'non-hdl cholesterol' => 'non_hdl'
            ];

            $biomarkers = [];
            $validFields = [];

            while ($row = $result->fetch_assoc()) {
                $id = $row['biomarker_id'];
                $name = strtolower(trim($row['name']));

                if (!isset($biomarkerNameMapping[$name])) {
                    continue;
                }

                $fieldKey = $biomarkerNameMapping[$name];

                if ($name === 'hdl cholesterol - male' && $sex_biological === 'm') {
                    $biomarkers[$fieldKey] = $id;
                } elseif ($name === 'hdl cholesterol - female' && $sex_biological === 'f') {
                    $biomarkers[$fieldKey] = $id;
                } elseif (!str_contains($name, 'male') && !str_contains($name, 'female')) {
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


   public function create($data)
{
    $this->db->begin_transaction();
    try {
        $lang   = $_SESSION['idioma'] ?? 'EN';
        $userId = $_SESSION['user_id'] ?? null;

        require_once __DIR__ . '/../models/BiomarkerModel.php';
        require_once __DIR__ . '/../models/NotificationModel.php';
        // *** NUEVO: Incluir el Helper ***
        require_once __DIR__ . '/../helpers/NotificationTemplateHelper.php';


        $biomarkerModel    = new BiomarkerModel();
        $notificationModel = new NotificationModel();

        // ===== Mapeo dinámico panel (Lipid Profile)
        $mapping     = $this->buildLipidProfileBiomarkerMappings();
        $biomarkers  = $mapping['biomarkers'];
        $namesByLang = $biomarkerModel->getBiomarkerNamesIndexed($lang); // <-- Esto ya no se usa para el nombre

        // ===== Auditoría + zona horaria
        $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $userId);
        $tzManager = new TimezoneManager($this->db);
        $tzManager->applyTimezone();

        // 'created_at' es para la auditoría y SIEMPRE debe ser la del servidor.
        $created_at = $env->getCurrentDatetime();   // p.ej. '2025-10-03 13:22:45'
        
        // CAMBIO: Usar la fecha y hora enviadas por el cliente (JS)
        $examDate   = $data['lipid_profile_date'] ?? substr($created_at, 0, 10);
        $examTime   = $data['lipid_profile_time'] ?? substr($created_at, 11, 8);

        // (Opcional) Sanity check por si el JS falla y manda un formato incorrecto
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $examDate)) {
            $examDate = substr($created_at, 0, 10);
        }
        if (!preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $examTime)) {
            $examTime = substr($created_at, 11, 8);
        }

        // ===== Chequeo límite diario (usar $examDate del cliente)
        foreach ($biomarkers as $field => $biomarkerId) {
            $value = isset($data[$field]) ? floatval($data[$field]) : 0;
            if ($value <= 0) continue;

            $maxResult = $biomarkerModel->getMaxExamById($biomarkerId);
            if (($maxResult['status'] ?? '') === 'success' && ($maxResult['max_exam'] ?? 0) > 0) {
                // Ahora $examDate es la fecha que mandó el cliente
                $count = $this->countExamsByBiomarker($data['user_id'], $field, $examDate);
                if ($count >= intval($maxResult['max_exam'])) {
                    
                    // Obtener name_db para el mensaje de error
                    $bmInfo = $biomarkerModel->getById($biomarkerId);
                    $biomarkerName = $bmInfo['name_db'] ?? "Biomarker #$biomarkerId";

                    $message = $lang === 'ES'
                        ? "Se alcanzó el límite diario para el biomarcador: {$biomarkerName}"
                        : "Daily limit reached for biomarker: {$biomarkerName}";
                    $this->db->rollback();
                    return ['value' => false, 'message' => $message];
                }
            }
        }

        // ===== Generar UUID
        $uuid = $this->generateUUIDv4();

        // ===== Normalizar numéricos (si no vienen, 0.0)
        $ldl            = isset($data['ldl'])             ? floatval($data['ldl'])             : 0.0;
        $hdl            = isset($data['hdl'])             ? floatval($data['hdl'])             : 0.0;
        $triglycerides  = isset($data['triglycerides'])   ? floatval($data['triglycerides'])   : 0.0;
        $total_chol     = isset($data['total_cholesterol']) ? floatval($data['total_cholesterol']) : ($ldl + $hdl + ($triglycerides/5));
        $non_hdl        = isset($data['non_hdl'])         ? floatval($data['non_hdl'])         : ($total_chol - $hdl);

        // ===== INSERT (usa la fecha/hora del CLIENTE)
        $query = "INSERT INTO {$this->table}
                    (lipid_profile_record_id, user_id, lipid_profile_date, lipid_profile_time,
                     ldl, hdl, total_cholesterol, triglycerides, non_hdl,
                     created_by, created_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
        }

        // Tipos: s s s s d d d d d s s
        $stmt->bind_param(
            "ssssdddddss",
            $uuid,
            $data['user_id'],
            $examDate, // <-- CAMBIO: Fecha del cliente
            $examTime, // <-- CAMBIO: Hora del cliente
            $ldl,
            $hdl,
            $total_chol,
            $triglycerides,
            $non_hdl,
            $userId,
            $created_at // <-- Columna de auditoría usa fecha del servidor
        );
        $stmt->execute();
        $stmt->close();

        // ===== Notificaciones (según rangos) - LÓGICA MODIFICADA =====
        foreach ($biomarkers as $field => $biomarkerId) {
            $value = isset($data[$field]) ? floatval($data[$field]) : 0;
            if ($value <= 0) continue;

            $eval = $biomarkerModel->evaluateBiomarkerValueStatus($biomarkerId, $value);
            if (
                isset($eval['status'], $eval['result']) &&
                $eval['status'] === 'success' &&
                in_array($eval['result'], ['High', 'Low'])
            ) {
                // *** INICIO BLOQUE MODIFICADO ***
                
                // 1. Obtener datos completos del biomarcador
                $biomarkerInfo = $biomarkerModel->getById($biomarkerId);
                // ----- CAMBIO SOLICITADO -----
                $biomarkerName = $biomarkerInfo['name_db'] ?? $field; // Usar name_db
                // ----- FIN CAMBIO -----
                
                $status_lang = $eval['result'];

                // 2. Preparar parámetros para la plantilla
                $params = [
                    'biomarker_name' => $biomarkerName, // <-- AHORA USA name_db
                    'value'          => $value,
                    'unit'           => $biomarkerInfo['unit'] ?? '',
                    'status'         => $status_lang,
                    'ref_min'        => $biomarkerInfo['reference_min'] ?? 'N/A',
                    'ref_max'        => $biomarkerInfo['reference_max'] ?? 'N/A',
                    'id_biomarker'   => $biomarkerId, // Incluir ID para 'exists'
                    'id_record'      => $uuid       // Incluir ID para 'exists'
                ];
                
                // 3. Construir la fila de notificación
                $dataRow = NotificationTemplateHelper::buildForInsert([
                    'template_key'    => 'biomarker_out_of_range',
                    'template_params' => $params,
                    'route'           => 'component_lipid?id=' . $uuid, // Ruta al nuevo registro
                    'module'          => 'lipid_profile',
                    'user_id'         => $data['user_id']
                ]);

                // 4. Crear la notificación
                $notificationModel->create($dataRow);
                
                // *** FIN BLOQUE MODIFICADO ***
            }
        }

        $this->db->commit();
        return ['value' => true, 'message' => 'Lipid profile record created successfully.'];

    } catch (mysqli_sql_exception $e) {
        $this->db->rollback();
        return ['value' => false, 'message' => $e->getMessage()];
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
        $lang = $_SESSION['idioma'] ?? 'EN';
        $userId = $_SESSION['user_id'] ?? null; // Usuario que realiza la acción

        $checkStmt = $this->db->prepare("SELECT lipid_profile_record_id , user_id FROM {$this->table} WHERE lipid_profile_record_id  = ? LIMIT 1");
        if (!$checkStmt) {
            throw new mysqli_sql_exception("Error preparing check query: " . $this->db->error);
        }

        $checkStmt->bind_param("s", $data['id']);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows == 0) {
            $msg = ($lang === 'ES')
                ? 'Registro no encontrado para actualizar.'
                : 'Record not found for update.';
            return ['value' => false, 'message' => $msg];
        }

        $existing = $checkResult->fetch_assoc();
        $user_id = $existing['user_id']; // El user_id al que pertenece el registro
        $checkStmt->close();

        $this->db->begin_transaction();
        try {
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId); // $userId es el admin/usuario logueado
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $updated_at = $env->getCurrentDatetime();

            $query = "UPDATE {$this->table}
                  SET lipid_profile_date = ?, lipid_profile_time = ?, ldl = ?, hdl = ?, total_cholesterol = ?, 
                      triglycerides = ?, non_hdl = ?, updated_by = ?, updated_at = ?
                  WHERE lipid_profile_record_id  = ?";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
            }

            $stmt->bind_param(
                "ssdddddsss",
                $data['lipid_profile_date'],
                $data['lipid_profile_time'],
                $data['ldl'],
                $data['hdl'],
                $data['total_cholesterol'],
                $data['triglycerides'],
                $data['non_hdl'],
                $userId, // El admin/usuario que *está haciendo* el update
                $updated_at,
                $data['id']
            );

            $stmt->execute();
            $stmt->close();

            require_once __DIR__ . '/../models/BiomarkerModel.php';
            require_once __DIR__ . '/../models/NotificationModel.php';
            // *** NUEVO: Incluir el Helper ***
            require_once __DIR__ . '/../helpers/NotificationTemplateHelper.php';

            $biomarkerModel = new BiomarkerModel();
            $notificationModel = new NotificationModel();

            // Mapeo dinámico para el panel 3 (Lipid Profile)
            $mapping = $this->buildLipidProfileBiomarkerMappings();
            $biomarkers = $mapping['biomarkers'];

            // ===== Notificaciones (según rangos) - LÓGICA MODIFICADA =====
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
                    // ----- CAMBIO SOLICITADO -----
                    $biomarkerName = $biomarkerInfo['name_db'] ?? $field; // Usar name_db
                    // ----- FIN CAMBIO -----
                    
                    $status_lang = $result['result'];

                    // 2. Preparar parámetros para la plantilla
                    $params = [
                        'biomarker_name' => $biomarkerName, // <-- AHORA USA name_db
                        'value'          => $value,
                        'unit'           => $biomarkerInfo['unit'] ?? '',
                        'status'         => $status_lang,
                        'ref_min'        => $biomarkerInfo['reference_min'] ?? 'N/A',
                        'ref_max'        => $biomarkerInfo['reference_max'] ?? 'N/A',
                        'id_biomarker'   => $biomarkerId, // Incluir ID para 'exists'
                        'id_record'      => $data['id'] // Incluir ID para 'exists'
                    ];

                    $route = 'component_lipid?id=' . $data['id'];

                    if ($notif) {
                        // 3A. Actualizar notificación existente
                        $updateData = [
                            'id' => $notif['notification_id'],
                            'template_params' => $params, // Actualiza los params
                            'new' => 1,             // La marca como no vista
                            'read_unread' => 0,     // La marca como no leída
                            'route' => $route,
                            'module' => 'lipid_profile',
                        ];
                        $notificationModel->update($updateData); 
                    
                    } else {
                        // 3B. Crear notificación (no existía)
                        $dataRow = NotificationTemplateHelper::buildForInsert([
                            'template_key'    => 'biomarker_out_of_range',
                            'template_params' => $params,
                            'route'           => $route,
                            'module'          => 'lipid_profile',
                            'user_id'         => $user_id // <-- El ID del dueño del registro
                        ]);
                        $notificationModel->create($dataRow);
                    }
                    // *** FIN BLOQUE MODIFICADO ***

                } else {
                    // Valor en rango normal
                    if ($notif) {
                        // Eliminar la notificación existente (soft delete)
                        $notificationModel->delete($notif['notification_id']);
                    }
                }
            }

            $this->db->commit();
            return ['value' => true, 'message' => 'Lipid profile record updated successfully.'];

        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return ['value' => false, 'message' => $e->getMessage()];
        }
    }






    public function delete($id)
    {
        $lang = $_SESSION['idioma'] ?? 'EN';
        $deleted_by = $_SESSION['user_id'] ?? null;

        // Verificar si el registro existe y no ha sido eliminado
        $checkStmt = $this->db->prepare("SELECT lipid_profile_record_id  FROM {$this->table} WHERE lipid_profile_record_id  = ? AND deleted_at IS NULL LIMIT 1");
        if (!$checkStmt) {
            throw new mysqli_sql_exception("Error preparing check query: " . $this->db->error);
        }

        $checkStmt->bind_param("s", $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows == 0) {
            return ['status' => 'error', 'message' => $lang === 'ES' ? 'Registro no encontrado.' : 'Record not found.'];
        }

        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $deleted_at = $env->getCurrentDatetime(); // Fecha con zona horaria correcta

            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_by = ?, deleted_at = ? WHERE lipid_profile_record_id  = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
            }

            $stmt->bind_param("sss", $deleted_by, $deleted_at, $id);
            $stmt->execute();
            $stmt->close(); // Cerrar el statement aquí

            // *** NUEVO: Eliminar notificaciones asociadas ***
            // (Asumiendo que NotificationModel está cargado y tiene el método delete)
            if (class_exists('NotificationModel')) {
                 $notificationModel = new NotificationModel();
                 // Obtenemos todas las notificaciones (incluso las borradas) ligadas a este 'id_record'
                 $stmtNotifs = $this->db->prepare("SELECT notification_id FROM notifications WHERE id_record = ?");
                 $stmtNotifs->bind_param("s", $id);
                 $stmtNotifs->execute();
                 $resNotifs = $stmtNotifs->get_result();
                 while ($notif = $resNotifs->fetch_assoc()) {
                     $notificationModel->delete($notif['notification_id']); // Usamos el soft delete del modelo
                 }
                 $stmtNotifs->close();
            }
            // *** FIN BLOQUE NUEVO ***
            
            $this->db->commit();

            return ['status' => 'success', 'message' => 'Lipid profile record deleted successfully.'];
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

