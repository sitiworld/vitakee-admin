<?php

require_once __DIR__ . '/../config/Database.php';

/**
 * DashboardAdminModel
 *
 * ─── RECOMMENDED INDEXES (run once in MySQL) ────────────────────────────────
 *
 *  -- KPIs
 *  ALTER TABLE users                            ADD INDEX idx_users_deleted    (deleted_at);
 *  ALTER TABLE specialists                      ADD INDEX idx_specialists_del  (deleted_at);
 *  ALTER TABLE specialist_verification_requests ADD INDEX idx_svr_kpi          (deleted_at, verification_level, status);
 *
 *  -- Top Users by Exams (UNION ALL across 4 exam tables)
 *  ALTER TABLE body_composition     ADD INDEX idx_bc_user_del  (user_id, deleted_at);
 *  ALTER TABLE energy_metabolism    ADD INDEX idx_em_user_del  (user_id, deleted_at);
 *  ALTER TABLE lipid_profile_record ADD INDEX idx_lpr_user_del (user_id, deleted_at);
 *  ALTER TABLE renal_function       ADD INDEX idx_rf_user_del  (user_id, deleted_at);
 *
 *  -- Top Specialists by Consultations
 *  ALTER TABLE second_opinion_requests
 *      ADD INDEX idx_sor_specialist_consult (specialist_id, deleted_at, type_request, status);
 *
 * ────────────────────────────────────────────────────────────────────────────
 */
class DashboardAdminModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Todos los KPIs del dashboard en UNA sola consulta (1 round-trip a la BD).
     *  - total_users            : usuarios no eliminados
     *  - total_specialists      : especialistas no eliminados
     *  - standard_verifications : verif. STANDARD en estado activo
     *  - plus_verifications     : verif. PLUS en estado activo
     *
     * El SUM condicional evita escanear specialist_verification_requests dos veces.
     * Las subconsultas correlacionadas para users/specialists aprovechan el índice
     * en deleted_at y no requieren un JOIN costoso.
     */
    public function getKpiSummary(): array
    {
        $sql = "SELECT
                    (SELECT COUNT(*) FROM users       WHERE deleted_at IS NULL) AS total_users,
                    (SELECT COUNT(*) FROM specialists WHERE deleted_at IS NULL) AS total_specialists,
                    SUM(CASE WHEN verification_level = 'STANDARD' THEN 1 ELSE 0 END) AS standard_verifications,
                    SUM(CASE WHEN verification_level = 'PLUS'     THEN 1 ELSE 0 END) AS plus_verifications
                FROM specialist_verification_requests
                WHERE deleted_at IS NULL
                  AND status IN ('AWAITING_PAYMENT', 'APPROVED')
                  AND verification_level IN ('STANDARD', 'PLUS')";

        $result = $this->db->query($sql);
        if (!$result) {
            error_log('[DashboardAdminModel.getKpiSummary] Query error: ' . $this->db->error);
            return [
                'total_users'            => 0,
                'total_specialists'      => 0,
                'standard_verifications' => 0,
                'plus_verifications'     => 0,
            ];
        }

        $row = $result->fetch_assoc();
        return [
            'total_users'            => (int)($row['total_users']            ?? 0),
            'total_specialists'      => (int)($row['total_specialists']      ?? 0),
            'standard_verifications' => (int)($row['standard_verifications'] ?? 0),
            'plus_verifications'     => (int)($row['plus_verifications']     ?? 0),
        ];
    }

    /**
     * Top usuarios con más exámenes registrados.
     * Suma los registros de las 4 tablas de exámenes mediante UNION ALL.
     * Cada subtabla hace GROUP BY user_id ANTES del JOIN, de modo que el motor
     * sólo une filas ya agregadas (N usuarios) y no filas individuales de examen.
     *
     * @param int $limit Máximo de filas devueltas (1–100)
     */
    public function getTopUsersByExams(int $limit = 10): array
    {
        $limit = max(1, min(100, $limit));

        $sql = "SELECT
                    u.user_id,
                    CONCAT(u.first_name, ' ', u.last_name) AS full_name,
                    u.email,
                    SUM(exam_counts.cnt) AS total_exams
                FROM users u
                INNER JOIN (
                    SELECT user_id, COUNT(*) AS cnt FROM body_composition     WHERE deleted_at IS NULL GROUP BY user_id
                    UNION ALL
                    SELECT user_id, COUNT(*) AS cnt FROM energy_metabolism    WHERE deleted_at IS NULL GROUP BY user_id
                    UNION ALL
                    SELECT user_id, COUNT(*) AS cnt FROM lipid_profile_record WHERE deleted_at IS NULL GROUP BY user_id
                    UNION ALL
                    SELECT user_id, COUNT(*) AS cnt FROM renal_function       WHERE deleted_at IS NULL GROUP BY user_id
                ) AS exam_counts ON exam_counts.user_id = u.user_id
                WHERE u.deleted_at IS NULL
                GROUP BY u.user_id, full_name, u.email
                ORDER BY total_exams DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('[DashboardAdminModel.getTopUsersByExams] Prepare error: ' . $this->db->error);
            return [];
        }
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = [
                'user_id'     => $row['user_id'],
                'full_name'   => $row['full_name'],
                'email'       => $row['email'],
                'total_exams' => (int)$row['total_exams'],
            ];
        }
        $stmt->close();
        return $rows;
    }

    /**
     * Top especialistas con más consultas atendidas.
     * Filtra second_opinion_requests por tipo appointment_request y status COMPLETED.
     * El índice recomendado en (specialist_id, deleted_at, type_request, status)
     * permite que el engine use un index range scan en lugar de full table scan.
     *
     * @param int $limit Máximo de filas devueltas (1–100)
     */
    public function getTopSpecialistsByConsultations(int $limit = 10): array
    {
        $limit = max(1, min(100, $limit));

        $idioma   = strtoupper($_SESSION['idioma'] ?? 'EN');
        $titleCol = $idioma === 'ES' ? 'st.name_es' : 'st.name_en';

        $sql = "SELECT
                    s.specialist_id,
                    CONCAT(s.first_name, ' ', s.last_name) AS full_name,
                    {$titleCol} AS title_display,
                    s.email,
                    COUNT(sor.second_opinion_id) AS total_consultations
                FROM specialists s
                INNER JOIN second_opinion_requests sor
                    ON sor.specialist_id = s.specialist_id
                    AND sor.deleted_at   IS NULL
                    AND sor.type_request = 'appointment_request'
                    AND sor.status       = 'COMPLETED'
                LEFT JOIN specialists_titles st
                    ON st.title_id    = s.title_id
                    AND st.deleted_at IS NULL
                WHERE s.deleted_at IS NULL
                GROUP BY s.specialist_id, full_name, {$titleCol}, s.email
                ORDER BY total_consultations DESC
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('[DashboardAdminModel.getTopSpecialistsByConsultations] Prepare error: ' . $this->db->error);
            return [];
        }
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = [
                'specialist_id'       => $row['specialist_id'],
                'full_name'           => $row['full_name'],
                'title_display'       => $row['title_display'] ?? '',
                'email'               => $row['email'],
                'total_consultations' => (int)$row['total_consultations'],
            ];
        }
        $stmt->close();
        return $rows;
    }
}
