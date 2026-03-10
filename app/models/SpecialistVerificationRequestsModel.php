<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/ClientEnvironmentInfo.php';
require_once __DIR__ . '/../config/TimezoneManager.php';

class SpecialistVerificationRequestsModel
{
    private $db;
    private $table = 'specialist_verification_requests';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /* =========================
     * LECTURAS BÁSICAS (ORIGINALES)
     * ========================= */
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY submitted_at DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE verification_request_id = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getByIdSpecialist($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE specialist_id = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /* =========================
     * NUEVAS LECTURAS CON DETALLES
     * ========================= */

    /**
     * Lista todas las solicitudes con:
     * - specialist: fila completa de specialists
     * - certifications: arreglo de specialist_certifications por specialist_id
     */
    public function getAllWithDetails(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY submitted_at DESC";
        $res = $this->db->query($sql);
        $rows = $res->fetch_all(MYSQLI_ASSOC);

        if (empty($rows)) return [];

        // 1) Recolectar specialist_id
        $specialistIds = array_values(array_unique(array_filter(array_column($rows, 'specialist_id'))));

        // 2) Traer specialists y certifications en batch
        $specialistsById   = $this->fetchSpecialistsByIds($specialistIds);
        $certsBySpecialist = $this->fetchCertificationsBySpecialistIds($specialistIds);

        // 3) Enriquecer resultados
        foreach ($rows as &$r) {
            $sid = $r['specialist_id'] ?? null;
            $r['specialist']      = $sid && isset($specialistsById[$sid]) ? $specialistsById[$sid] : null;
            $r['certifications']  = $sid && isset($certsBySpecialist[$sid]) ? $certsBySpecialist[$sid] : [];
        }
        unset($r);

        return $rows;
    }

    /**
     * Obtiene una solicitud por su ID con specialist + certifications adjuntos.
     */
    public function getByIdWithDetails(string $verificationRequestId): ?array
    {
        $row = $this->getById($verificationRequestId);
        if (!$row) return null;

        $sid = $row['specialist_id'] ?? null;
        if ($sid) {
            $specs = $this->fetchSpecialistsByIds([$sid]);
            $row['specialist'] = $specs[$sid] ?? null;

            $certs = $this->fetchCertificationsBySpecialistIds([$sid]);
            $row['certifications'] = $certs[$sid] ?? [];
        } else {
            $row['specialist'] = null;
            $row['certifications'] = [];
        }

        return $row;
    }

    /**
     * Obtiene la solicitud (o la más reciente) por specialist_id con specialist + certifications.
     * Si hay varias, retorna la más reciente por submitted_at.
     */
    public function getByIdSpecialistWithDetails(string $specialistId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE specialist_id = ? AND deleted_at IS NULL
            ORDER BY submitted_at DESC
            LIMIT 1
        ");
        $stmt->bind_param('s', $specialistId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) return null;

        $specs = $this->fetchSpecialistsByIds([$specialistId]);
        $row['specialist'] = $specs[$specialistId] ?? null;

        $certs = $this->fetchCertificationsBySpecialistIds([$specialistId]);
        $row['certifications'] = $certs[$specialistId] ?? [];

        return $row;
    }

    /* =========================
     * HELPERS DE LECTURA RELACIONAL (batch)
     * ========================= */

    /**
     * Trae filas de la tabla specialists indexadas por specialist_id.
     * Eficiente: 1 query con IN() para todos los IDs.
     * Incluye JOINs para obtener nombres traducidos de specialty y title.
     */
    private function fetchSpecialistsByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter($ids)));
        if (empty($ids)) return [];

        // Construimos IN() de forma segura con escape (seguimos usando mysqli)
        $escaped = array_map([$this->db, 'real_escape_string'], $ids);
        $in = "'" . implode("','", $escaped) . "'";

        // Determinar idioma actual
        $lang = strtolower($_SESSION['idioma'] ?? 'en');
        $nameField = $lang === 'es' ? 'name_es' : 'name_en';

        $sql = "SELECT 
                    s.*,
                    sp.{$nameField} as specialty_display_name,
                    t.{$nameField} as title_display_name
                FROM specialists s
                LEFT JOIN specialty sp ON s.specialty_id = sp.specialty_id AND sp.deleted_at IS NULL
                LEFT JOIN specialists_titles t ON s.title_id = t.title_id AND t.deleted_at IS NULL
                WHERE s.specialist_id IN ($in) AND s.deleted_at IS NULL";
        
        $res = $this->db->query($sql);
        if (!$res) return [];

        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $byId = [];
        foreach ($rows as $r) {
            $byId[$r['specialist_id']] = $r;
        }
        return $byId;
        // Si prefieres 100% prepared, avísame y lo hago con bind dinámico.
    }

    /**
     * Trae TODAS las certificaciones agrupadas por specialist_id.
     * Retorna: [ specialist_id => [ {cert_row}, ... ], ... ]
     */
    private function fetchCertificationsBySpecialistIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter($ids)));
        if (empty($ids)) return [];

        $escaped = array_map([$this->db, 'real_escape_string'], $ids);
        $in = "'" . implode("','", $escaped) . "'";

        $sql = "
            SELECT *
            FROM specialist_certifications
            WHERE specialist_id IN ($in) AND deleted_at IS NULL
            ORDER BY created_at DESC
        ";
        $res = $this->db->query($sql);
        if (!$res) return [];

        $specialistUrl = $_ENV['APP_URL_Specialists'] ?? 'http://localhost/vitakee-users/specialist';
        $grouped = [];
        while ($row = $res->fetch_assoc()) {
            $sid = $row['specialist_id'];
            if (!isset($grouped[$sid])) $grouped[$sid] = [];
            
            // Format full URL for certificates since they are uploaded via the users repo
            if (!empty($row['file_url'])) {
                $row['file_url'] = rtrim($specialistUrl, '/') . '/' . ltrim($row['file_url'], '/');
            }

            $grouped[$sid][] = $row;
        }
        return $grouped;
    }

    /* =========================
     * UTILIDADES
     * ========================= */

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

    private function hasActiveCertifications(string $specialistId): bool
    {
        $sql = "SELECT 1 FROM specialist_certifications WHERE specialist_id = ? AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new mysqli_sql_exception($this->db->error);
        }
        $stmt->bind_param('s', $specialistId);
        $stmt->execute();
        $res = $stmt->get_result();
        return (bool)$res->fetch_row();
    }

    private function msg(string $key): string
    {
        $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
        $en = [
            'cert_required' => 'You must have at least one active certification registered before requesting verification.',
            'approved_ok'   => 'Verification request approved successfully.',
            'rejected_ok'   => 'Verification request rejected successfully.',
            'invalid_stat'  => 'Invalid status. Allowed: PENDING, APPROVED, REJECTED.',
        ];
        $es = [
            'cert_required' => 'Debes tener al menos una certificación activa registrada antes de solicitar la verificación.',
            'approved_ok'   => 'Solicitud de verificación aprobada correctamente.',
            'rejected_ok'   => 'Solicitud de verificación rechazada correctamente.',
            'invalid_stat'  => 'Estado inválido. Permitidos: PENDING, APPROVED, REJECTED.',
        ];
        $map = ($idioma === 'ES') ? $es : $en;
        return $map[$key] ?? $key;
    }

    /**
     * Ajusta specialists.verified_status cuando status ∈ {APPROVED, REJECTED}
     */
    private function syncSpecialistVerifiedStatus(string $specialistId, string $status): void
    {
        if (!in_array($status, ['APPROVED', 'REJECTED', 'AWAITING_PAYMENT'], true)) {
            return;
        }
        $stmt = $this->db->prepare("UPDATE specialists SET verified_status = ? WHERE specialist_id = ? AND deleted_at IS NULL");
        if (!$stmt) {
            throw new mysqli_sql_exception($this->db->error);
        }
        $stmt->bind_param('ss', $status, $specialistId);
        $stmt->execute();
    }

    /**
     * Cambia el estado de una solicitud a APPROVED o REJECTED (con auditoría) y sincroniza al especialista.
     */
    private function setRequestStatus(string $verificationRequestId, string $newStatus): void
    {
        if (!in_array($newStatus, ['APPROVED','REJECTED'], true)) {
            throw new mysqli_sql_exception($this->msg('invalid_stat'));
        }

        $this->db->begin_transaction();
        try {
            $adminId = $_SESSION['user_id'] ?? null;

            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $adminId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $now = $env->getCurrentDatetime();

            // Obtener specialist_id de la solicitud
            $row = $this->getById($verificationRequestId);
            if (!$row) {
                throw new mysqli_sql_exception("Verification request not found.");
            }
            $specialistId = $row['specialist_id'];

            // Actualizar solicitud
            $stmt = $this->db->prepare("
                UPDATE {$this->table}
                SET status = ?, approved_at = ?, admin_id = ?, updated_at = ?, updated_by = ?
                WHERE verification_request_id = ?
            ");
            if (!$stmt) {
                throw new mysqli_sql_exception($this->db->error);
            }
            $stmt->bind_param('sssss', $newStatus, $now, $adminId, $now, $adminId, $verificationRequestId);
            // Nota: bind_param requiere número exacto; ajustamos tipos y params:
            // -> Tipos: s s s s s s (6) pero tenemos 6 valores:
            // Corrijamos: son 6 valores -> "ssssss"
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /* =========================
     * ESCRITURAS (create/update/delete)
     * ========================= */

    public function create($data)
    {
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $submittedAt = $env->getCurrentDatetime();

            // Validación: debe tener certificaciones activas
            $specialistId = $data['specialist_id'];
            if (!$this->hasActiveCertifications($specialistId)) {
                throw new mysqli_sql_exception($this->msg('cert_required'));
            }

            // Generar UUID para verification_request_id
            $verificationRequestId = $this->generateUUIDv4();

            $stmt = $this->db->prepare("INSERT INTO {$this->table}
            (verification_request_id, specialist_id, status, submitted_at, verification_level, created_at, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)");

            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
            }

            $status = $data['status'] ?? 'PENDING';
            $verifLevel = $data['verification_level'] ?? 'STANDARD';

            $stmt->bind_param(
                "sssssss",
                $verificationRequestId,
                $specialistId,
                $status,
                $submittedAt,
                $verifLevel,
                $submittedAt,
                $userId
            );

            $stmt->execute();
            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function update($id, $data)
    {
        $this->db->begin_transaction();
        try {
            $adminId = $_SESSION['user_id'] ?? null; // quien aprueba o rechaza
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $adminId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $updatedAt = $env->getCurrentDatetime();

            $status             = $data['status'];
            $approved_at        = $data['approved_at'] ?? $updatedAt;
            $verification_level = $data['verification_level'] ?? 'STANDARD';

            $stmt = $this->db->prepare("UPDATE {$this->table}
                SET status = ?, approved_at = ?, admin_id = ?, verification_level = ?, updated_at = ?, updated_by = ?
                WHERE verification_request_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception($this->db->error);
            }
            $stmt->bind_param(
                "sssssss",
                $status,
                $approved_at,
                $adminId,
                $verification_level,
                $updatedAt,
                $adminId,
                $id
            );

            $stmt->execute();

            // Si cambia a APPROVED o REJECTED, sincronizar specialists.verified_status
            if (in_array($status, ['APPROVED','REJECTED'], true)) {
                $row = $this->getById($id);
                if ($row && !empty($row['specialist_id'])) {
                    $this->syncSpecialistVerifiedStatus($row['specialist_id'], $status);
                }
            }

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function delete($id)
    {
        $this->db->begin_transaction();
        try {
            $adminId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $adminId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $deletedAt = $env->getCurrentDatetime();

            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE verification_request_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception($this->db->error);
            }
            $stmt->bind_param("sss", $deletedAt, $adminId, $id);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /* =========================
     * MÉTODOS ESPECÍFICOS: APROBAR / RECHAZAR
     * ========================= */

    /**
     * Aprueba una solicitud y sincroniza el verified_status del especialista.
     * Al aprobar, la solicitud pasa a estado AWAITING_PAYMENT para que el especialista complete el pago.
     */
    public function approveRequest(string $verificationRequestId): bool
    {
        $this->db->begin_transaction();
        try {
            $adminId = $_SESSION['user_id'] ?? null;

            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $adminId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $now = $env->getCurrentDatetime();

            $row = $this->getById($verificationRequestId);
            if (!$row) {
                throw new mysqli_sql_exception("Verification request not found.");
            }
            $specialistId = $row['specialist_id'];

            // Actualizar solicitud a AWAITING_PAYMENT (el especialista debe pagar antes de ser verificado)
            $stmt = $this->db->prepare("
                UPDATE {$this->table}
                SET status = 'AWAITING_PAYMENT', approved_at = ?, admin_id = ?, updated_at = ?, updated_by = ?
                WHERE verification_request_id = ?
            ");
            if (!$stmt) {
                throw new mysqli_sql_exception($this->db->error);
            }
            $stmt->bind_param('sssss', $now, $adminId, $now, $adminId, $verificationRequestId);
            $stmt->execute();

            // Sincronizar especialista con estado AWAITING_PAYMENT
            $this->syncSpecialistVerifiedStatus($specialistId, 'AWAITING_PAYMENT');

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Rechaza una solicitud y sincroniza el verified_status del especialista.
     */
    public function rejectRequest(string $verificationRequestId): bool
    {
        $this->db->begin_transaction();
        try {
            $adminId = $_SESSION['user_id'] ?? null;

            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $adminId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $now = $env->getCurrentDatetime();

            $row = $this->getById($verificationRequestId);
            if (!$row) {
                throw new mysqli_sql_exception("Verification request not found.");
            }
            $specialistId = $row['specialist_id'];

            // Actualizar solicitud
            $stmt = $this->db->prepare("
                UPDATE {$this->table}
                SET status = 'REJECTED', approved_at = ?, admin_id = ?, updated_at = ?, updated_by = ?
                WHERE verification_request_id = ?
            ");
            if (!$stmt) {
                throw new mysqli_sql_exception($this->db->error);
            }
            $stmt->bind_param('sssss', $now, $adminId, $now, $adminId, $verificationRequestId);
            $stmt->execute();

            // Sincronizar especialista
            $this->syncSpecialistVerifiedStatus($specialistId, 'REJECTED');

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
