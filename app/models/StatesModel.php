<?php
require_once __DIR__ . '/../config/Database.php';


class StatesModel
{
    private \mysqli $db;
    private string  $table = 'states';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /* ================= Helpers ================= */

    private function uuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0,0xffff), mt_rand(0,0xffff),
            mt_rand(0,0xffff),
            (mt_rand(0,0x0fff) | 0x4000),
            (mt_rand(0,0x3fff) | 0x8000),
            mt_rand(0,0xffff), mt_rand(0,0xffff), mt_rand(0,0xffff)
        );
    }

    private function nowAudit(?string $actorId): array
    {
        $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $actorId);
        (new TimezoneManager($this->db))->applyTimezone();
        return [$env->getCurrentDatetime(), $actorId];
    }

    /* ================= Queries ================= */

    /**
     * Filtros opcionales:
     * - $countryId (UUID)
     * - $q: busca en state_name
     * - $code: state_code exacto
     * - $iso: iso3166_2 exacto
     * - $type: tipo administrativo (e.g., 'state','province','region')
     */
public function getAll(?string $countryId = null, ?string $q = null, ?string $code = null, ?string $iso = null, ?string $type = null): array
{
    $sql = "
        SELECT s.*, c.country_name
        FROM {$this->table} s
        INNER JOIN countries c ON c.country_id = s.country_id
        WHERE s.deleted_at IS NULL
          AND c.deleted_at IS NULL
    ";
    $params = [];
    $types  = "";

    if (!empty($countryId)) {
        $sql .= " AND s.country_id = ?";
        $params[] = $countryId; $types .= "s";
    }
    if (!empty($q)) {
        $sql .= " AND s.state_name LIKE ?";
        $params[] = "%{$q}%"; $types .= "s";
    }
    if (!empty($code)) {
        $sql .= " AND s.state_code = ?";
        $params[] = $code; $types .= "s";
    }
    if (!empty($iso)) {
        $sql .= " AND s.iso3166_2 = ?";
        $params[] = $iso; $types .= "s";
    }
    if (!empty($type)) {
        $sql .= " AND s.type = ?";
        $params[] = $type; $types .= "s";
    }

    $sql .= " ORDER BY s.state_name ASC";

    if (empty($params)) {
        $res = $this->db->query($sql);
        if (!$res) throw new \mysqli_sql_exception("Query error: " . $this->db->error);
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    $stmt = $this->db->prepare($sql);
    if (!$stmt) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

public function getById(string $id): ?array
{
    $sql = "
        SELECT s.*, c.country_name
        FROM {$this->table} s
        INNER JOIN countries c ON c.country_id = s.country_id
        WHERE s.state_id = ? 
          AND s.deleted_at IS NULL
          AND c.deleted_at IS NULL
    ";
    $stmt = $this->db->prepare($sql);
    if (!$stmt) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}


    /**
     * Reglas de unicidad (ignorando eliminados):
     *  - (country_id, state_name)
     *  - (country_id, state_code) si state_code no es null/vacío
     *  - (country_id, iso3166_2) si iso3166_2 no es null/vacío
     */
    public function create(array $data): bool
    {
        $this->db->begin_transaction();
        try {
            $countryId = $data['country_id'] ?? null;
            $stateName = trim($data['state_name'] ?? '');
            $stateCode = isset($data['state_code']) ? trim((string)$data['state_code']) : null;
            $iso       = isset($data['iso3166_2']) ? trim((string)$data['iso3166_2']) : null;
            $type      = isset($data['type']) ? trim((string)$data['type']) : null;
            $timezone  = $data['timezone'] ?? null;
            $lat       = isset($data['latitude'])  && $data['latitude']  !== '' ? (string)$data['latitude']  : null;
            $lng       = isset($data['longitude']) && $data['longitude'] !== '' ? (string)$data['longitude'] : null;

            if (!$countryId || $stateName === '') {
                throw new \mysqli_sql_exception("country_id and state_name are required.");
            }

            // Unicidad (country_id, state_name)
            $chk = $this->db->prepare("
                SELECT state_id FROM {$this->table}
                WHERE country_id = ? AND state_name = ? AND deleted_at IS NULL
                LIMIT 1
            ");
            if (!$chk) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
            $chk->bind_param("ss", $countryId, $stateName);
            $chk->execute();
            $chk->store_result();
            if ($chk->num_rows > 0) { $chk->close(); throw new \mysqli_sql_exception("State name already exists in that country."); }
            $chk->close();

            // Unicidad (country_id, state_code) si aplica
            if (!empty($stateCode)) {
                $chk2 = $this->db->prepare("
                    SELECT state_id FROM {$this->table}
                    WHERE country_id = ? AND state_code = ? AND deleted_at IS NULL
                    LIMIT 1
                ");
                if (!$chk2) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
                $chk2->bind_param("ss", $countryId, $stateCode);
                $chk2->execute();
                $chk2->store_result();
                if ($chk2->num_rows > 0) { $chk2->close(); throw new \mysqli_sql_exception("State code already exists in that country."); }
                $chk2->close();
            }

            // Unicidad (country_id, iso3166_2) si aplica
            if (!empty($iso)) {
                $chk3 = $this->db->prepare("
                    SELECT state_id FROM {$this->table}
                    WHERE country_id = ? AND iso3166_2 = ? AND deleted_at IS NULL
                    LIMIT 1
                ");
                if (!$chk3) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
                $chk3->bind_param("ss", $countryId, $iso);
                $chk3->execute();
                $chk3->store_result();
                if ($chk3->num_rows > 0) { $chk3->close(); throw new \mysqli_sql_exception("ISO 3166-2 already exists in that country."); }
                $chk3->close();
            }

            // Auditoría
            $actorId = $_SESSION['user_id'] ?? null;
            [$createdAt, $createdBy] = $this->nowAudit($actorId);
            $uuid = $this->uuid();

            $sql = "INSERT INTO {$this->table}
                (state_id, country_id, state_name, state_code, iso3166_2, type, timezone, latitude, longitude, created_at, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);

            $stmt->bind_param(
                "sssssssssss",
                $uuid, $countryId, $stateName, $stateCode, $iso, $type, $timezone, $lat, $lng, $createdAt, $createdBy
            );

            if (!$stmt->execute()) {
                throw new \mysqli_sql_exception("Execute error: " . $stmt->error);
            }
            $stmt->close();

            $this->db->commit();
            return true;
        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function update(string $id, array $data): bool
    {
        $this->db->begin_transaction();
        try {
            $exists = $this->getById($id);
            if (!$exists) throw new \mysqli_sql_exception("State not found.");

            $countryId = $data['country_id'] ?? $exists['country_id'];
            $stateName = array_key_exists('state_name', $data) ? trim((string)$data['state_name']) : $exists['state_name'];
            $stateCode = array_key_exists('state_code', $data) ? trim((string)$data['state_code']) : $exists['state_code'];
            $iso       = array_key_exists('iso3166_2', $data) ? trim((string)$data['iso3166_2']) : $exists['iso3166_2'];
            $type      = array_key_exists('type', $data) ? trim((string)$data['type']) : $exists['type'];
            $timezone  = $data['timezone'] ?? $exists['timezone'];
            $lat       = (array_key_exists('latitude',  $data) ? ($data['latitude']  !== '' ? (string)$data['latitude']  : null) : $exists['latitude']);
            $lng       = (array_key_exists('longitude', $data) ? ($data['longitude'] !== '' ? (string)$data['longitude'] : null) : $exists['longitude']);

            if (!$countryId || $stateName === '') {
                throw new \mysqli_sql_exception("country_id and state_name are required.");
            }

            // Unicidad (country_id, state_name) excluyendo id
            $chk = $this->db->prepare("
                SELECT state_id FROM {$this->table}
                WHERE country_id = ? AND state_name = ? AND state_id <> ? AND deleted_at IS NULL
                LIMIT 1
            ");
            if (!$chk) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
            $chk->bind_param("sss", $countryId, $stateName, $id);
            $chk->execute();
            $chk->store_result();
            if ($chk->num_rows > 0) { $chk->close(); throw new \mysqli_sql_exception("Another state with that name already exists in that country."); }
            $chk->close();

            // Unicidad (country_id, state_code) si aplica
            if (!empty($stateCode)) {
                $chk2 = $this->db->prepare("
                    SELECT state_id FROM {$this->table}
                    WHERE country_id = ? AND state_code = ? AND state_id <> ? AND deleted_at IS NULL
                    LIMIT 1
                ");
                if (!$chk2) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
                $chk2->bind_param("sss", $countryId, $stateCode, $id);
                $chk2->execute();
                $chk2->store_result();
                if ($chk2->num_rows > 0) { $chk2->close(); throw new \mysqli_sql_exception("Another state with that code already exists in that country."); }
                $chk2->close();
            }

            // Unicidad (country_id, iso3166_2) si aplica
            if (!empty($iso)) {
                $chk3 = $this->db->prepare("
                    SELECT state_id FROM {$this->table}
                    WHERE country_id = ? AND iso3166_2 = ? AND state_id <> ? AND deleted_at IS NULL
                    LIMIT 1
                ");
                if (!$chk3) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
                $chk3->bind_param("sss", $countryId, $iso, $id);
                $chk3->execute();
                $chk3->store_result();
                if ($chk3->num_rows > 0) { $chk3->close(); throw new \mysqli_sql_exception("Another ISO 3166-2 already exists in that country."); }
                $chk3->close();
            }

            // Auditoría
            $actorId = $_SESSION['user_id'] ?? null;
            [$updatedAt, $updatedBy] = $this->nowAudit($actorId);

            $sql = "UPDATE {$this->table} SET
                country_id = ?, state_name = ?, state_code = ?, iso3166_2 = ?, type = ?,
                timezone = ?, latitude = ?, longitude = ?,
                updated_at = ?, updated_by = ?
                WHERE state_id = ?";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);

            $stmt->bind_param(
                "sssssssssss",
                $countryId, $stateName, $stateCode, $iso, $type,
                $timezone, $lat, $lng,
                $updatedAt, $updatedBy, $id
            );

            if (!$stmt->execute()) {
                throw new \mysqli_sql_exception("Execute error: " . $stmt->error);
            }
            $stmt->close();

            $this->db->commit();
            return true;
        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

public function delete(string $id): bool
{
    $this->db->begin_transaction();
    try {
        // 1) Verificar existencia
        $row = $this->getById($id);
        if (!$row) {
            throw new \mysqli_sql_exception("State not found.");
        }

        // 2) Verificar dependencias en hijos directos del estado
        //    - cities.state_id
        //    - specialist_locations.state_id
        $DEPENDENCIAS = [
            ['table' => 'cities',               'fk' => 'state_id'],
            ['table' => 'specialist_locations', 'fk' => 'state_id'],
        ];

        $totalDeps = 0;
        $detalle   = [];

        foreach ($DEPENDENCIAS as $dep) {
            $table = $dep['table'];
            $fk    = $dep['fk'];

            // ¿La tabla hija tiene deleted_at? -> contar solo activas
            $checkDel = $this->db->prepare("
                SELECT COUNT(*) AS c
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME   = ?
                  AND COLUMN_NAME  = 'deleted_at'
            ");
            if (!$checkDel) {
                throw new \mysqli_sql_exception("Prepare error (deleted_at check for {$table}): " . $this->db->error);
            }
            $checkDel->bind_param("s", $table);
            $checkDel->execute();
            $hasDelRes    = $checkDel->get_result();
            $hasDeletedAt = (int)($hasDelRes->fetch_assoc()['c'] ?? 0);
            $checkDel->close();

            $sql = $hasDeletedAt > 0
                ? "SELECT COUNT(*) AS deps FROM `{$table}` WHERE `{$fk}` = ? AND `deleted_at` IS NULL"
                : "SELECT COUNT(*) AS deps FROM `{$table}` WHERE `{$fk}` = ?";

            $stmtDep = $this->db->prepare($sql);
            if (!$stmtDep) {
                throw new \mysqli_sql_exception("Prepare error (dependency count for {$table}): " . $this->db->error);
            }
            $stmtDep->bind_param("s", $id);
            $stmtDep->execute();
            $resDep = $stmtDep->get_result();
            $cnt    = (int)($resDep->fetch_assoc()['deps'] ?? 0);
            $stmtDep->close();

            $totalDeps        += $cnt;
            $detalle[$table]   = $cnt;
        }

        if ($totalDeps > 0) {
            $parts = [];
            foreach ($detalle as $t => $c) if ($c > 0) $parts[] = "{$t}={$c}";
            $msg = "Cannot delete state: dependent records exist";
            if ($parts) $msg .= " (" . implode(", ", $parts) . ")";
            $msg .= ".";
            throw new \mysqli_sql_exception($msg);
        }

        // 3) Auditoría + borrado lógico
        $actorId = $_SESSION['user_id'] ?? null;
        [$deletedAt, $deletedBy] = $this->nowAudit($actorId);

        $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE state_id = ?");
        if (!$stmt) {
            throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
        }
        $stmt->bind_param("sss", $deletedAt, $deletedBy, $id);

        if (!$stmt->execute()) {
            throw new \mysqli_sql_exception("Execute error: " . $stmt->error);
        }
        $stmt->close();

        $this->db->commit();
        return true;
    } catch (\mysqli_sql_exception $e) {
        $this->db->rollback();
        throw $e;
    }
}

}
