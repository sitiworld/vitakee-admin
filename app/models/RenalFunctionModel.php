<?php

require_once __DIR__ . '/../config/Database.php';

// === Dependencias ===
require_once __DIR__ . '/../models/BiomarkerModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../helpers/NotificationTemplateHelper.php'; // Incluir el Helper
require_once __DIR__ . '/../config/ClientEnvironmentInfo.php'; // Para auditoría
require_once __DIR__ . '/../config/TimezoneManager.php'; // Para auditoría

class RenalFunctionModel
{
    private $db;
    private $table = "renal_function";

    // Campos
    public $renal_function_id;
    public $user_id;
    public $renal_date;
    public $renal_time;
    public $albumin;            // 'N','1+','2+','3+'
    public $creatinine;         // '1+','2+','3+'
    public $urine_result;       // 'N'|'A' (GENERATED en DB)
    public $serum_creatinine;   // mg/dL
    public $uric_acid_blood;    // mg/dL
    public $bun_blood;          // mg/dL
    public $egfr;               // mL/min/1.73m2

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /* ===================== EXPORT ===================== */
    public function exportUserRecordsToCSV($user_id)
    {
        try {
            $sql = "SELECT 
                        renal_date, 
                        renal_time, 
                        albumin, 
                        creatinine, 
                        urine_result, 
                        serum_creatinine, 
                        uric_acid_blood, 
                        bun_blood, 
                        egfr,
                        CASE 
                          WHEN serum_creatinine IS NOT NULL 
                               AND serum_creatinine > 0 
                               AND bun_blood IS NOT NULL 
                               AND bun_blood >= 0
                          THEN ROUND(bun_blood / serum_creatinine, 2)
                          ELSE NULL
                        END AS bun_cr_ratio
                    FROM {$this->table}
                    WHERE user_id = ? AND deleted_at IS NULL
                    ORDER BY renal_date DESC, renal_time DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $filename = "renal_function_{$user_id}.csv";
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $output = fopen('php://output', 'w');

                fputcsv($output, [
                    'Date','Time',
                    'Albumin (dipstick)','Creatinine (dipstick)','Urine Result',
                    'Serum Cr (mg/dL)','Uric Acid (mg/dL)','BUN (mg/dL)','eGFR (mL/min/1.73m2)',
                    'BUN/Cr ratio'
                ]);

                while ($row = $result->fetch_assoc()) {
                    fputcsv($output, $row);
                }

                fclose($output);
                exit;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No data found for this user.']);
            }
        } catch (mysqli_sql_exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    /* ===================== READERS ===================== */

    public function getAll()
    {
        try {
            $query = "SELECT 
                        renal_function_id, user_id, renal_date, renal_time, 
                        albumin, creatinine, urine_result, 
                        serum_creatinine, uric_acid_blood, bun_blood, egfr,
                        CASE 
                          WHEN serum_creatinine IS NOT NULL AND serum_creatinine > 0 
                          THEN ROUND(bun_blood / serum_creatinine, 2)
                          ELSE NULL
                        END AS bun_cr_ratio
                      FROM {$this->table}
                      WHERE deleted_at IS NULL
                      ORDER BY renal_date DESC, renal_time DESC";
            $result = $this->db->query($query);
            if (!$result) {
                throw new mysqli_sql_exception("Error getting renal function records: " . $this->db->error);
            }

            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }

            return count($items) > 0 ? $items : ['status' => 'error', 'message' => 'No renal function records found.'];
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getByUserId($userId)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT 
                    renal_function_id, user_id, renal_date, renal_time, 
                    albumin, creatinine, urine_result, 
                    serum_creatinine, uric_acid_blood, bun_blood, egfr,
                    CASE 
                      WHEN serum_creatinine IS NOT NULL AND serum_creatinine > 0 
                      THEN ROUND(bun_blood / serum_creatinine, 2)
                      ELSE NULL
                    END AS bun_cr_ratio
                 FROM {$this->table}
                 WHERE user_id = ? AND deleted_at IS NULL
                 ORDER BY renal_date DESC, renal_time DESC"
            );
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
            }
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }

            return count($items) > 0 ? $items : ['value' => true, "data" => [], 'message' => 'No records found for this user.'];
        } catch (mysqli_sql_exception $e) {
            return ['value' => false, 'message' => $e->getMessage()];
        }
    }

    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT 
                    renal_function_id, user_id, renal_date, renal_time, 
                    albumin, creatinine, urine_result, 
                    serum_creatinine, uric_acid_blood, bun_blood, egfr,
                    CASE 
                      WHEN serum_creatinine IS NOT NULL AND serum_creatinine > 0 
                      THEN ROUND(bun_blood / serum_creatinine, 2)
                      ELSE NULL
                    END AS bun_cr_ratio
                 FROM {$this->table}
                 WHERE renal_function_id = ? AND deleted_at IS NULL
                 LIMIT 1"
            );
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
            }
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            return $data ? $this->jsonResponse(true, '', $data) : ['status' => 'error', 'message' => 'Record not found.'];
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /* ===================== HELPERS ===================== */

    private function countExamsByField($user_id, $field, $date): int
    {
        $query = "SELECT COUNT(*) AS total 
                  FROM {$this->table}
                  WHERE user_id = ?
                    AND renal_date = ?
                    AND {$field} IS NOT NULL
                    AND {$field} <> ''";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $user_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return (int)$row['total'];
    }

    private function computeEgfr(?float $scr_mg_dl, ?int $age_years, ?string $sex_biological): ?float
    {
        if ($scr_mg_dl === null || $scr_mg_dl <= 0 || $age_years === null || $age_years <= 0 || !in_array($sex_biological, ['M','F'])) {
            return null;
        }
        $kappa = ($sex_biological === 'F') ? 0.7 : 0.9;
        $alpha = ($sex_biological === 'F') ? -0.241 : -0.302;
        $minScr = min($scr_mg_dl / $kappa, 1.0);
        $maxScr = max($scr_mg_dl / $kappa, 1.0);
        $egfr = 142.0 * pow($minScr, $alpha) * pow($maxScr, -1.200) * pow(0.9938, $age_years);
        if ($sex_biological === 'F') $egfr *= 1.012;
        return round($egfr, 2);
    }

    private function calculateAge(?string $birthday, ?string $asOfDate = null): ?int
    {
        if (!$birthday) return null;

        try {
            $dob = new DateTime($birthday);
            $ref = $asOfDate ? new DateTime($asOfDate) : new DateTime();
            $diff = $dob->diff($ref);
            return max(0, (int)$diff->y);
        } catch (Exception $e) {
            return null;
        }
    }

    private function normalizeSexFromSession($sex_biological): ?string
    {
        if (!isset($sex_biological)) return null;
        $s = strtoupper(trim((string)$sex_biological));
        $s = substr($s, 0, 1);
        return in_array($s, ['M','F']) ? $s : null;
    }


    private function toNullIfEmpty($v)
    {
        if (!isset($v)) return null;
        if (is_string($v) && trim($v) === '') return null;
        return $v;
    }

    private function convertInputs(array &$src): void
    {
        if (!isset($src['serum_creatinine']) && isset($src['cr_mmol_l'])) {
            $src['serum_creatinine'] = round(floatval($src['cr_mmol_l']) * 11.3, 3);
        }
        if (!isset($src['uric_acid_blood']) && isset($src['ua_mmol_l'])) {
            $src['uric_acid_blood'] = round(floatval($src['ua_mmol_l']) * 16.81, 3);
        }
        if (!isset($src['bun_blood']) && isset($src['urea_mmol_l'])) {
            $src['bun_blood'] = round(floatval($src['urea_mmol_l']) * 2.8, 3);
        }
    }

    private function resolveBiomarkerIdByName(string $name): ?string
    {
        // Optimizamos para buscar por 'name' o 'name_db' ya que 'name' es el estándar EN
        $stmt = $this->db->prepare("SELECT biomarker_id FROM biomarkers WHERE name = ? OR name_db = ? LIMIT 1");
        if (!$stmt) return null;
        $stmt->bind_param("ss", $name, $name);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res['biomarker_id'] ?? null;
    }

    /* ===================== CREATE ===================== */

public function create($data)
{
    $this->db->begin_transaction();
    try {
        $lang       = $_SESSION['idioma'] ?? 'EN';
        $created_by = $_SESSION['user_id'] ?? null;

        // Modelos auxiliares
        require_once __DIR__ . '/../models/BiomarkerModel.php';
        require_once __DIR__ . '/../models/NotificationModel.php';
        require_once __DIR__ . '/../helpers/NotificationTemplateHelper.php';
        
        $biomarkerModel    = new BiomarkerModel();
        $notificationModel = new NotificationModel();

        $user_id = $data['user_id'];

        // Normalización entradas
        $src = $data;
        $src['albumin']    = isset($src['albumin'])    ? trim((string)$src['albumin'])    : null;
        $src['creatinine'] = isset($src['creatinine']) ? trim((string)$src['creatinine']) : null;

        $this->convertInputs($src);

        $serum_creatinine = $this->toNullIfEmpty($src['serum_creatinine'] ?? null);
        $uric_acid_blood  = $this->toNullIfEmpty($src['uric_acid_blood']  ?? null);
        $bun_blood        = $this->toNullIfEmpty($src['bun_blood']        ?? null);

        /* ================= AUDITORÍA / ZONA HORARIA ================= */
        $userId = $_SESSION['user_id'] ?? null;
        $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $userId);

        $tzManager = new TimezoneManager($this->db);
        $tzManager->applyTimezone();

        // 'created_at' es para la auditoría y SIEMPRE debe ser la del servidor.
        $created_at = $env->getCurrentDatetime(); 
        
        // CAMBIO: Usar la fecha y hora enviadas por el cliente (JS)
        $examDate = $data['renal_date'] ?? substr($created_at, 0, 10);
        $examTime = $data['renal_time'] ?? substr($created_at, 11, 8);

        // (Opcional) Sanity check por si el JS falla y manda un formato incorrecto
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $examDate)) {
            $examDate = substr($created_at, 0, 10);
        }
        if (!preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $examTime)) {
            $examTime = substr($created_at, 11, 8);
        }
        
        // Asignar al array $src que usa el resto de la función
        $src['renal_date'] = $examDate;
        $src['renal_time'] = $examTime;

        /* ================= eGFR (CKD-EPI 2021) ================= */
        $sex_biological = $this->normalizeSexFromSession($_SESSION['sex_biological'] ?? null);
        $refDate   = $src['renal_date']; // <-- Ahora usa la fecha del cliente
        $age_years = $this->calculateAge($_SESSION['birthday'] ?? null, $refDate);

        $egfr = $this->computeEgfr(
            $serum_creatinine !== null ? floatval($serum_creatinine) : null,
            $age_years,
            $sex_biological
        );
        
        $src['egfr'] = $egfr; // Asignar para el bucle de notificaciones

        // UUID
        $uuid = $this->generateUUIDv4();

        // INSERT
        $query = "INSERT INTO {$this->table}
                    (renal_function_id, user_id, renal_date, renal_time, albumin, creatinine, 
                     serum_creatinine, uric_acid_blood, bun_blood, egfr,
                     created_by, created_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
        }
        $stmt->bind_param(
            "ssssssdddsss", 
            $uuid,
            $user_id,
            $src['renal_date'], // <-- CAMBIO: Fecha del cliente
            $src['renal_time'], // <-- CAMBIO: Hora del cliente
            $src['albumin'],
            $src['creatinine'],
            $serum_creatinine,
            $uric_acid_blood,
            $bun_blood,
            $egfr,          
            $created_by,
            $created_at // <-- Columna de auditoría usa fecha del servidor
        );
        $stmt->execute();
        $stmt->close();
        
        // *** INICIO SECCIÓN DE NOTIFICACIONES MODIFICADA ***

        // Re-consultar la fila para obtener el 'urine_result' generado
        $stmtCheck = $this->db->prepare("SELECT urine_result, albumin, creatinine FROM {$this->table} WHERE renal_function_id = ?");
        $stmtCheck->bind_param("s", $uuid);
        $stmtCheck->execute();
        $checkResult = $stmtCheck->get_result()->fetch_assoc();
        $stmtCheck->close();

        if ($biomarkerModel && $notificationModel) {
            $panelId = '60819af9-0533-472c-9d5a-24a5df5a83f7';

            // 1) Chequeo de resultado de orina (Albumin/Creatinine)
            if ($checkResult && $checkResult['urine_result'] === 'A') {
                $params = [
                    'albumin' => $checkResult['albumin'] ?? 'N/A',
                    'creatinine' => $checkResult['creatinine'] ?? 'N/A',
                    'id_record' => $uuid // ID de este registro
                ];
                 $dataRow = NotificationTemplateHelper::buildForInsert([
                    'template_key'    => 'renal_urine_result_abnormal',
                    'template_params' => $params,
                    'route'           => 'component_renal?id=' . $uuid,
                    'module'          => 'renal_function',
                    'user_id'         => $user_id
                ]);
                $notificationModel->create($dataRow);
            }

            // 2) Chequeo de biomarcadores numéricos (Serum Cr, Uric Acid, BUN, eGFR)
            $numericMap = [
                'serum_creatinine' => 'Serum Creatinine',
                'uric_acid_blood'  => 'Uric Acid Blood',
                'bun_blood'        => 'Bun Blood',
                'egfr'             => 'EGFR'
            ];

            foreach ($numericMap as $field => $bmName) {
                $val = $src[$field] ?? null;
                // Solo procesar si es numérico y > 0 (egfr puede ser 0, pero lo filtramos en evaluate)
                if ($val === null || !is_numeric($val) || $val <= 0) continue; 

                $bmId = $this->resolveBiomarkerIdByName($bmName);
                if (!$bmId) continue; 

                // max_exam por día
                $maxResult = $biomarkerModel->getMaxExamById($bmId);
                if (($maxResult['status'] ?? '') === 'success' && ($maxResult['max_exam'] ?? 0) > 0) {
                    // $src['renal_date'] ahora es la fecha del cliente
                    $count = $this->countExamsByField($user_id, $field, $src['renal_date']);
                    if ($count >= intval($maxResult['max_exam'])) {
                        $bmInfo = $biomarkerModel->getById($bmId);
                        $bmLabel = $bmInfo['name_db'] ?? $bmName;
                        $msg = $lang === 'ES'
                            ? "Se alcanzó el límite diario para el biomarcador: {$bmLabel}"
                            : "Daily limit reached for biomarker: {$bmLabel}";
                        $this->db->rollback();
                        return ['status' => 'error', 'message' => $msg];
                    }
                }

                // Notificación según rango
                $eval = $biomarkerModel->evaluateBiomarkerValueStatus($bmId, floatval($val));
                if (
                    isset($eval['status'], $eval['result']) &&
                    $eval['status'] === 'success' &&
                    in_array($eval['result'], ['High', 'Low'])
                ) {
                    $biomarkerInfo = $biomarkerModel->getById($bmId);
                    $biomarkerName = $biomarkerInfo['name_db'] ?? $bmName;
                    $status_lang = ($lang === 'ES') ? (($eval['result'] === 'High') ? 'Alto' : 'Bajo') : $eval['result'];

                    $params = [
                        'biomarker_name' => $biomarkerName,
                        'value'          => $val,
                        'unit'           => $biomarkerInfo['unit'] ?? '',
                        'status'         => $status_lang,
                        'ref_min'        => $biomarkerInfo['reference_min'] ?? 'N/A',
                        'ref_max'        => $biomarkerInfo['reference_max'] ?? 'N/A',
                        'id_biomarker'   => $bmId,
                        'id_record'      => $uuid 
                    ];
                    
                    $dataRow = NotificationTemplateHelper::buildForInsert([
                        'template_key'    => 'biomarker_out_of_range',
                        'template_params' => $params,
                        'route'           => 'component_renal?id=' . $uuid,
                        'module'          => 'renal_function',
                        'user_id'         => $user_id
                    ]);

                    $notificationModel->create($dataRow);
                }
            }
        }
        // *** FIN SECCIÓN DE NOTIFICACIONES ***

        $this->db->commit();
        return ['status' => 'success', 'message' => 'Renal function record created successfully.'];
    } catch (mysqli_sql_exception $e) {
        $this->db->rollback();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

    /* ===================== UPDATE ===================== */

    public function update($data)
    {
        $lang = $_SESSION['idioma'] ?? 'EN';
        $updated_by = $_SESSION['user_id'] ?? null;

        $checkStmt = $this->db->prepare("SELECT renal_function_id, user_id FROM {$this->table} WHERE renal_function_id = ? LIMIT 1");
        if (!$checkStmt) {
            throw new mysqli_sql_exception("Error preparing check query: " . $this->db->error);
        }
        $checkStmt->bind_param("s", $data['id']);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows == 0) {
            $msg = ($lang === 'ES') ? 'Registro no encontrado para actualizar.' : 'Record not found for update.';
            return ['status' => 'error', 'message' => $msg];
        }

        $existing = $checkResult->fetch_assoc();
        $user_id = $existing['user_id'];
        $checkStmt->close();

        $this->db->begin_transaction();
        try {
            // Auditoría / TZ
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $updated_at = $env->getCurrentDatetime();

            // Normalizar/convertir
            $src = $data;
            $src['albumin']    = isset($src['albumin']) ? trim((string)$src['albumin']) : null;
            $src['creatinine'] = isset($src['creatinine']) ? trim((string)$src['creatinine']) : null;
            $this->convertInputs($src);

            $serum_creatinine = $this->toNullIfEmpty($src['serum_creatinine'] ?? null);
            $uric_acid_blood  = $this->toNullIfEmpty($src['uric_acid_blood'] ?? null);
            $bun_blood        = $this->toNullIfEmpty($src['bun_blood'] ?? null);

            // Recalcular eGFR
            $sex_biological = $this->normalizeSexFromSession($_SESSION['sex_biological'] ?? null);
            $refDate = $src['renal_date'] ?? null;
            $age_years = $this->calculateAge($_SESSION['birthday'] ?? null, $refDate);
            $egfr = $this->computeEgfr(
                $serum_creatinine !== null ? floatval($serum_creatinine) : null,
                $age_years,
                $sex_biological
            );
            $src['egfr'] = $egfr; // Asignar para el bucle de notificaciones

            $query = "UPDATE {$this->table}
                      SET renal_date = ?, renal_time = ?, albumin = ?, creatinine = ?, 
                          serum_creatinine = ?, uric_acid_blood = ?, bun_blood = ?, egfr = ?,
                          updated_by = ?, updated_at = ?
                      WHERE renal_function_id = ?";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing update query: " . $this->db->error);
            }
            $stmt->bind_param(
                "ssssdddssss",
                $src['renal_date'],
                $src['renal_time'],
                $src['albumin'],
                $src['creatinine'],
                $serum_creatinine,
                $uric_acid_blood,
                $bun_blood,
                $egfr,
                $updated_by,
                $updated_at,
                $src['id']
            );
            $stmt->execute();
            $stmt->close();
            
            // *** INICIO SECCIÓN DE NOTIFICACIONES MODIFICADA ***
            
            // Re-consultar la fila para obtener el 'urine_result' generado
            $stmtCheck = $this->db->prepare("SELECT urine_result, albumin, creatinine FROM {$this->table} WHERE renal_function_id = ?");
            $stmtCheck->bind_param("s", $src['id']);
            $stmtCheck->execute();
            $checkResult = $stmtCheck->get_result()->fetch_assoc();
            $stmtCheck->close();

            if (class_exists('BiomarkerModel') && class_exists('NotificationModel')) {
                require_once __DIR__ . '/../helpers/NotificationTemplateHelper.php';
                $biomarkerModel    = new BiomarkerModel();
                $notificationModel = new NotificationModel();
                $panelId = '60819af9-0533-472c-9d5a-24a5df5a83f7';

                // 1) Chequeo de resultado de orina (Albumin/Creatinine)
                // Usamos una 'proxy_key' para buscar la notificación de orina, ya que no tiene un $bmId único
                $urine_proxy_key = 'renal_urine_result_abnormal';
                
                $stmtNotif = $this->db->prepare("SELECT notification_id FROM notifications WHERE JSON_UNESCAPED(JSON_EXTRACT(template_params, '$.id_record')) = ? AND template_key = ? AND deleted_at IS NULL");
                $stmtNotif->bind_param("ss", $src['id'], $urine_proxy_key);
                $stmtNotif->execute();
                $notif = $stmtNotif->get_result()->fetch_assoc();
                $stmtNotif->close();

                if ($checkResult && $checkResult['urine_result'] === 'A') {
                    $params = [
                        'albumin' => $checkResult['albumin'] ?? 'N/A',
                        'creatinine' => $checkResult['creatinine'] ?? 'N/A',
                        'id_record' => $src['id'] // ID de este registro
                    ];
                    $route = 'component_renal?id=' . $src['id'];

                    if ($notif) {
                        $updateData = [
                            'id' => $notif['notification_id'],
                            'template_params' => $params,
                            'new' => 1, 'read_unread' => 0, 'route' => $route
                        ];
                        $notificationModel->update($updateData);
                    } else {
                        $dataRow = NotificationTemplateHelper::buildForInsert([
                            'template_key'    => $urine_proxy_key,
                            'template_params' => $params,
                            'route'           => $route,
                            'module'          => 'renal_function',
                            'user_id'         => $user_id
                        ]);
                        $notificationModel->create($dataRow);
                    }
                } else {
                    // Resultado 'N' o NULL
                    if ($notif) {
                        $notificationModel->delete($notif['notification_id']);
                    }
                }
                
                // 2) Chequeo de biomarcadores numéricos
                $numericMap = [
                    'serum_creatinine' => 'Serum Creatinine',
                    'uric_acid_blood'  => 'Uric Acid Blood',
                    'bun_blood'        => 'Bun Blood',
                    'egfr'             => 'EGFR'
                ];

                foreach ($numericMap as $field => $bmName) {
                    $val = $src[$field] ?? null;
                    $bmId = $this->resolveBiomarkerIdByName($bmName);
                    if (!$bmId) continue;

                    // Buscar notificación numérica (por id_record Y id_biomarker)
                    $stmtNotifNum = $this->db->prepare("SELECT notification_id FROM notifications WHERE JSON_UNESCAPED(JSON_EXTRACT(template_params, '$.id_record')) = ? AND JSON_UNESCAPED(JSON_EXTRACT(template_params, '$.id_biomarker')) = ? AND deleted_at IS NULL");
                    $stmtNotifNum->bind_param("ss", $src['id'], $bmId);
                    $stmtNotifNum->execute();
                    $notifNum = $stmtNotifNum->get_result()->fetch_assoc();
                    $stmtNotifNum->close();

                    if ($val !== null && is_numeric($val) && $val > 0) {
                        $eval = $biomarkerModel->evaluateBiomarkerValueStatus($bmId, floatval($val));
                        
                        if (
                            isset($eval['status'], $eval['result']) &&
                            $eval['status'] === 'success' &&
                            in_array($eval['result'], ['High', 'Low'])
                        ) {
                            $biomarkerInfo = $biomarkerModel->getById($bmId);
                            $biomarkerName = $biomarkerInfo['name_db'] ?? $bmName;
                            $status_lang = ($lang === 'ES') ? (($eval['result'] === 'High') ? 'Alto' : 'Bajo') : $eval['result'];

                            $params = [
                                'biomarker_name' => $biomarkerName,
                                'value'          => $val,
                                'unit'           => $biomarkerInfo['unit'] ?? '',
                                'status'         => $status_lang,
                                'ref_min'        => $biomarkerInfo['reference_min'] ?? 'N/A',
                                'ref_max'        => $biomarkerInfo['reference_max'] ?? 'N/A',
                                'id_biomarker'   => $bmId,
                                'id_record'      => $src['id']
                            ];
                            $route = 'component_renal?id=' . $src['id'];

                            if ($notifNum) {
                                $updateData = [
                                    'id' => $notifNum['notification_id'],
                                    'template_params' => $params,
                                    'new' => 1, 'read_unread' => 0, 'route' => $route
                                ];
                                $notificationModel->update($updateData);
                            } else {
                                $dataRow = NotificationTemplateHelper::buildForInsert([
                                    'template_key'    => 'biomarker_out_of_range',
                                    'template_params' => $params,
                                    'route'           => $route,
                                    'module'          => 'renal_function',
                                    'user_id'         => $user_id
                                ]);
                                $notificationModel->create($dataRow);
                            }
                        } else {
                            // Valor en rango normal
                            if ($notifNum) $notificationModel->delete($notifNum['notification_id']);
                        }
                    } else {
                        // Valor es NULL o 0
                        if ($notifNum) $notificationModel->delete($notifNum['notification_id']);
                    }
                }
            }
            // *** FIN SECCIÓN DE NOTIFICACIONES ***

            $this->db->commit();
            return ['status' => 'success', 'message' => 'Renal function record updated successfully.'];
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /* ============ HISTORIAL POR RECORD (campo) ============ */
public function getUserRenalFunctionHistoryByRecordId($recordId, $field)
{
    try {
        $this->db->begin_transaction();

        // campos válidos (incluye alias calculado bun_cr_ratio)
        $validFields = [
            'albumin','creatinine',
            'serum_creatinine','uric_acid_blood','bun_blood','egfr',
            'bun_cr_ratio'
        ];
        if (!in_array($field, $validFields)) {
            throw new Exception('Invalid field.');
        }

        // Obtener user_id a partir del record
        $stmt = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE renal_function_id = ?");
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

        // Consulta historial
        if ($field === 'bun_cr_ratio') {
            $query = "SELECT renal_date,
                             CASE 
                               WHEN serum_creatinine IS NOT NULL AND serum_creatinine > 0 
                                    AND bun_blood IS NOT NULL
                               THEN ROUND(bun_blood/serum_creatinine, 2)
                               ELSE NULL
                             END AS value
                      FROM {$this->table}
                      WHERE user_id = ? AND deleted_at IS NULL
                        AND serum_creatinine IS NOT NULL
                        AND bun_blood IS NOT NULL
                      ORDER BY renal_date DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $user_id);
        } else {
            $query = "SELECT renal_date, {$field} AS value
                      FROM {$this->table}
                      WHERE user_id = ? AND deleted_at IS NULL
                        AND {$field} IS NOT NULL 
                        AND {$field} <> '' -- Ignorar strings vacíos
                      ORDER BY renal_date DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $user_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = [
                'date'  => date('m/d/Y', strtotime($row['renal_date'])),
                'value' => $row['value']
            ];
        }

        $this->db->commit();
        return ['status' => 'success', 'data' => $history];

    } catch (Exception $e) {
        $this->db->rollback();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}


    /* ===================== SOFT DELETE ===================== */

    public function delete($id)
    {
        $lang = $_SESSION['idioma'] ?? 'EN';
        $deleted_by = $_SESSION['user_id'] ?? null;

        $checkStmt = $this->db->prepare("SELECT renal_function_id FROM {$this->table} WHERE renal_function_id = ? AND deleted_at IS NULL LIMIT 1");
        if (!$checkStmt) {
            throw new mysqli_sql_exception("Error preparing check query: " . $this->db->error);
        }

        $checkStmt->bind_param("s", $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows == 0) {
            $msg = $lang === 'ES' ? 'Registro no encontrado para eliminar.' : 'Record not found for deletion.';
            return ['value' => false, 'message' => $msg];
        }
        $checkStmt->close(); // Cerrar el checkStmt

        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $deleted_at = $env->getCurrentDatetime();

            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_by = ?, deleted_at = ? WHERE renal_function_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing delete query: " . $this->db->error);
            }

            $stmt->bind_param("sss", $deleted_by, $deleted_at, $id);
            $stmt->execute();
            $stmt->close();

            // *** NUEVO: Eliminar notificaciones asociadas ***
            if (class_exists('NotificationModel')) {
                 $notificationModel = new NotificationModel();
                 // Buscar CUALQUIER notificación (de biomarcador O de orina)
                 $stmtNotifs = $this->db->prepare("SELECT notification_id FROM notifications WHERE JSON_UNESCAPED(JSON_EXTRACT(template_params, '$.id_record')) = ?");
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
            return ['value' => true, 'message' => 'Record deleted successfully.'];
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return ['value' => false, 'message' => $e->getMessage()];
        }
    }

    /* ===================== UTILS ===================== */

    private function generateUUIDv4(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
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

