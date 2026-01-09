<?php
require_once __DIR__ . '/../config/Database.php';


class CitiesModel
{
    private \mysqli $db;
    private string $table = 'cities';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /* =============== Helpers =============== */
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

    /* =============== Queries =============== */

    /**
     * Filtros opcionales:
     * - $countryId (UUID)
     * - $stateId   (UUID)
     * - $q (busca en city_name)
     */
public function getAll(?string $countryId = null, ?string $stateId = null, ?string $q = null): ?array
{
    $sql = "
        SELECT c.*, s.state_name, co.country_name
        FROM {$this->table} c
        INNER JOIN states s     ON s.state_id = c.state_id AND s.deleted_at IS NULL
        INNER JOIN countries co ON co.country_id = c.country_id AND co.deleted_at IS NULL
        WHERE c.deleted_at IS NULL
    ";
    $params = [];
    $types  = "";

    if (!empty($countryId)) {
        $sql .= " AND c.country_id = ?";
        $params[] = $countryId; $types .= "s";
    }
    if (!empty($stateId)) {
        $sql .= " AND c.state_id = ?";
        $params[] = $stateId; $types .= "s";
    }
    if (!empty($q)) {
        $sql .= " AND c.city_name LIKE ?";
        $params[] = "%{$q}%"; $types .= "s";
    }

    $sql .= " ORDER BY c.city_name ASC";

    // === Ejecutar ===
    if (empty($params)) {
        $res = $this->db->query($sql);
        if (!$res) throw new \mysqli_sql_exception("Query error: " . $this->db->error);
        $rows = $res->fetch_all(MYSQLI_ASSOC);
    } else {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    // === Normalizar salida estilo getById ===
    if (empty($rows)) {
        return null;
    }

    // Si solo hay un registro, devolverlo como array asociativo
    if (count($rows) === 1) {
        return $rows[0];
    }

    // Si hay varios, devolver array de registros
    return $rows;
}

public function getAllPaged(
    int $limit = 10,
    int $offset = 0,
    string $search = '',
    string $sort = 'city_name',
    string $order = 'ASC',
    ?string $countryId = null,
    ?string $stateId   = null
): array {
    // Whitelist de columnas ordenables para evitar SQL injection en ORDER BY
    $sortable = [
        'city_name'   => 'c.city_name',
        'state_id'    => 'c.state_id',
        'country_id'  => 'c.country_id',
        'timezone'    => 'c.timezone',
        'latitude'    => 'c.latitude',
        'longitude'   => 'c.longitude',
        // Campos unidos, si necesitas ordenar por nombres:
        'state_name'  => 's.state_name',
        'country_name'=> 'co.country_name',
    ];
    $sortCol = $sortable[$sort] ?? 'c.city_name';
    $order   = ($order === 'DESC') ? 'DESC' : 'ASC';

    // Base FROM + JOIN + WHERE
    $fromJoin = "
        FROM {$this->table} c
        INNER JOIN states s     ON s.state_id = c.state_id AND s.deleted_at IS NULL
        INNER JOIN countries co ON co.country_id = c.country_id AND co.deleted_at IS NULL
        WHERE c.deleted_at IS NULL
    ";

    $wheres = [];
    $params = [];
    $types  = '';

    // Filtros exactos
    if (!empty($countryId)) {
        $wheres[] = "c.country_id = ?";
        $params[] = $countryId;
        $types   .= 's';
    }
    if (!empty($stateId)) {
        $wheres[] = "c.state_id = ?";
        $params[] = $stateId;
        $types   .= 's';
    }

    // Búsqueda libre (city/state/country)
    if ($search !== '') {
        $wheres[] = "(c.city_name LIKE ? OR s.state_name LIKE ? OR co.country_name LIKE ?)";
        $like = "%{$search}%";
        $params[] = $like; $types .= 's';
        $params[] = $like; $types .= 's';
        $params[] = $like; $types .= 's';
    }

    // Concatena WHERE
    $whereSql = '';
    if (!empty($wheres)) {
        $whereSql = ' AND ' . implode(' AND ', $wheres);
    }

    // 1) Total
    $countSql = "SELECT COUNT(*) AS total {$fromJoin} {$whereSql}";
    $total = 0;

    if ($types === '') {
        // sin parámetros
        $res = $this->db->query($countSql);
        if (!$res) throw new \mysqli_sql_exception("Query error: " . $this->db->error);
        $row = $res->fetch_assoc();
        $total = (int)($row['total'] ?? 0);
    } else {
        $stmt = $this->db->prepare($countSql);
        if (!$stmt) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        $total = (int)($r['total'] ?? 0);
        $stmt->close();
    }

    // 2) Rows con ORDER/LIMIT/OFFSET
    $dataSql = "
        SELECT
            c.*,
            s.state_name,
            co.country_name
        {$fromJoin}
        {$whereSql}
        ORDER BY {$sortCol} {$order}
        LIMIT ? OFFSET ?
    ";

    // Agregar limit/offset a parámetros
    $params2 = $params;
    $types2  = $types . 'ii';
    $params2[] = $limit;
    $params2[] = $offset;

    $rows = [];
    $stmt2 = $this->db->prepare($dataSql);
    if (!$stmt2) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
    $stmt2->bind_param($types2, ...$params2);
    $stmt2->execute();
    $rows = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt2->close();

    // Retorna [total, rows] para que el controller entregue { total, rows }
    return [$total, $rows];
}

public function getById(string $id): ?array
{
    $sql = "
        SELECT c.*, s.state_name, co.country_name
        FROM {$this->table} c
        INNER JOIN states s     ON s.state_id = c.state_id AND s.deleted_at IS NULL
        INNER JOIN countries co ON co.country_id = c.country_id AND co.deleted_at IS NULL
        WHERE c.city_id = ?
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
     * Reglas:
     * - Unicidad: (country_id, state_id, city_name) sin eliminados
     * Campos:
     * city_id(UUID), state_id, country_id, city_name, timezone, latitude, longitude,
     * created_at, created_by
     */
    public function create(array $data): bool
    {
        $this->db->begin_transaction();
        try {
            // Normalización básica
            $countryId = $data['country_id'] ?? null;
            $stateId   = $data['state_id'] ?? null;
            $cityName  = trim($data['city_name'] ?? '');
            $timezone  = $data['timezone'] ?? null;
            $lat       = isset($data['latitude'])  && $data['latitude']  !== '' ? (string)$data['latitude']  : null;
            $lng       = isset($data['longitude']) && $data['longitude'] !== '' ? (string)$data['longitude'] : null;

            if (!$countryId || !$stateId || $cityName === '') {
                throw new \mysqli_sql_exception("country_id, state_id and city_name are required.");
            }

            // Unicidad
            $chk = $this->db->prepare("
                SELECT city_id FROM {$this->table}
                WHERE country_id = ? AND state_id = ? AND city_name = ? AND deleted_at IS NULL
                LIMIT 1
            ");
            if (!$chk) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
            $chk->bind_param("sss", $countryId, $stateId, $cityName);
            $chk->execute();
            $chk->store_result();
            if ($chk->num_rows > 0) {
                $chk->close();
                throw new \mysqli_sql_exception("City already exists for that state/country.");
            }
            $chk->close();

            // Auditoría
            $actorId = $_SESSION['user_id'] ?? null;
            [$createdAt, $createdBy] = $this->nowAudit($actorId);

            $uuid = $this->uuid();

            $sql = "INSERT INTO {$this->table}
                (city_id, state_id, country_id, city_name, timezone, latitude, longitude, created_at, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);

            // Usamos 's' para lat/lng para soportar NULL
            $stmt->bind_param(
                "sssssssss",
                $uuid, $stateId, $countryId, $cityName, $timezone, $lat, $lng, $createdAt, $createdBy
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
            // Existe
            $exists = $this->getById($id);
            if (!$exists) throw new \mysqli_sql_exception("City not found.");

            $countryId = $data['country_id'] ?? $exists['country_id'];
            $stateId   = $data['state_id']   ?? $exists['state_id'];
            $cityName  = array_key_exists('city_name', $data) ? trim((string)$data['city_name']) : $exists['city_name'];
            $timezone  = $data['timezone']  ?? $exists['timezone'];
            $lat       = (array_key_exists('latitude',  $data) ? ($data['latitude']  !== '' ? (string)$data['latitude']  : null) : $exists['latitude']);
            $lng       = (array_key_exists('longitude', $data) ? ($data['longitude'] !== '' ? (string)$data['longitude'] : null) : $exists['longitude']);

            if (!$countryId || !$stateId || $cityName === '') {
                throw new \mysqli_sql_exception("country_id, state_id and city_name are required.");
            }

            // Unicidad (excluyendo el propio id)
            $chk = $this->db->prepare("
                SELECT city_id FROM {$this->table}
                WHERE country_id = ? AND state_id = ? AND city_name = ? AND city_id <> ? AND deleted_at IS NULL
                LIMIT 1
            ");
            if (!$chk) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
            $chk->bind_param("ssss", $countryId, $stateId, $cityName, $id);
            $chk->execute();
            $chk->store_result();
            if ($chk->num_rows > 0) {
                $chk->close();
                throw new \mysqli_sql_exception("Another city with that name already exists for that state/country.");
            }
            $chk->close();

            // Auditoría
            $actorId = $_SESSION['user_id'] ?? null;
            [$updatedAt, $updatedBy] = $this->nowAudit($actorId);

            $sql = "UPDATE {$this->table} SET
                state_id = ?, country_id = ?, city_name = ?, timezone = ?, latitude = ?, longitude = ?,
                updated_at = ?, updated_by = ?
                WHERE city_id = ?";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);

            $stmt->bind_param(
                "sssssssss",
                $stateId, $countryId, $cityName, $timezone, $lat, $lng, $updatedAt, $updatedBy, $id
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
            throw new \mysqli_sql_exception("City not found.");
        }

        // 2) Verificar dependencias en specialist_locations
        //    Detectar si existe la columna deleted_at para contar sólo activas
        $hasDeletedAt = 0;
        $stmtHas = $this->db->prepare("
            SELECT COUNT(*) AS c
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'specialist_locations'
              AND COLUMN_NAME = 'deleted_at'
        ");
        if (!$stmtHas) {
            throw new \mysqli_sql_exception("Prepare error (deleted_at check): " . $this->db->error);
        }
        $stmtHas->execute();
        $resHas = $stmtHas->get_result();
        $hasDeletedAt = (int)($resHas->fetch_assoc()['c'] ?? 0);
        $stmtHas->close();

        if ($hasDeletedAt > 0) {
            $sqlDeps = "SELECT COUNT(*) AS deps FROM specialist_locations WHERE city_id = ? AND deleted_at IS NULL";
        } else {
            $sqlDeps = "SELECT COUNT(*) AS deps FROM specialist_locations WHERE city_id = ?";
        }

        $stmtDep = $this->db->prepare($sqlDeps);
        if (!$stmtDep) {
            throw new \mysqli_sql_exception("Prepare error (dependency count): " . $this->db->error);
        }
        $stmtDep->bind_param("s", $id);
        $stmtDep->execute();
        $resDep = $stmtDep->get_result();
        $deps = (int)($resDep->fetch_assoc()['deps'] ?? 0);
        $stmtDep->close();

        if ($deps > 0) {
            // Regla de negocio: con borrado lógico en cities no se dispara el FK (ON DELETE SET NULL),
            // por lo tanto bloqueamos si hay dependencias activas.
            throw new \mysqli_sql_exception("Cannot delete city: dependent specialist locations exist ({$deps}).");
        }

        // 3) Auditoría y borrado lógico
        $actorId = $_SESSION['user_id'] ?? null;
        [$deletedAt, $deletedBy] = $this->nowAudit($actorId);

        $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE city_id = ?");
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
