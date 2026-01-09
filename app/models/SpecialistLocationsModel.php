<?php
require_once __DIR__ . '/../config/Database.php';
// Si no tienes autoload, descomenta estas líneas según tu estructura:
// require_once __DIR__ . '/../helpers/ClientEnvironmentInfo.php';
// require_once __DIR__ . '/../helpers/TimezoneManager.php';

class SpecialistLocationsModel
{
    private \mysqli $db;
    private string $table = 'specialist_locations';

    public function __construct(?\mysqli $db = null)
    {
        $this->db = $db ?: \Database::getInstance();
    }

    /* ===================== Helpers ===================== */

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

    private function exists(string $id): bool
    {
        $sql = "SELECT 1 FROM {$this->table} WHERE location_id = ? AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $ok = (bool) $res->fetch_row();
        $stmt->close();
        return $ok;
    }

    private function clearOtherPrimaries(string $specialistId, ?string $exceptLocationId = null): void
    {
        // Desmarca otras ubicaciones primarias del especialista
        $sql = "UPDATE {$this->table}
                SET is_primary = 0, updated_at = CURRENT_TIMESTAMP
                WHERE specialist_id = ?
                  AND deleted_at IS NULL"
               . ($exceptLocationId ? " AND location_id <> ?" : "");

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }

        if ($exceptLocationId) {
            $stmt->bind_param("ss", $specialistId, $exceptLocationId);
        } else {
            $stmt->bind_param("s", $specialistId);
        }
        $stmt->execute();
        $stmt->close();
    }

    /* ===================== Queries ===================== */

    public function getAll(): array
    {
        $sql = "SELECT
                    sl.*,
                    c.city_name,
                    s.state_name,
                    co.country_name
                FROM {$this->table} sl
                LEFT JOIN cities    c  ON c.city_id    = sl.city_id    AND c.deleted_at  IS NULL
                LEFT JOIN states    s  ON s.state_id    = sl.state_id   AND s.deleted_at  IS NULL
                LEFT JOIN countries co ON co.country_id = sl.country_id AND co.deleted_at IS NULL
                WHERE sl.deleted_at IS NULL
                ORDER BY sl.location_id ASC";

        $res = $this->db->query($sql);
        if (!$res) {
            throw new \mysqli_sql_exception("Query failed: " . $this->db->error);
        }
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function getById(string $id): ?array
    {
        $sql = "SELECT
                    sl.*,
                    c.city_name,
                    s.state_name,
                    co.country_name
                FROM {$this->table} sl
                LEFT JOIN cities    c  ON c.city_id    = sl.city_id    AND c.deleted_at  IS NULL
                LEFT JOIN states    s  ON s.state_id    = sl.state_id   AND s.deleted_at  IS NULL
                LEFT JOIN countries co ON co.country_id = sl.country_id AND co.deleted_at IS NULL
                WHERE sl.location_id = ? AND sl.deleted_at IS NULL
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc() ?: null;
        $stmt->close();

        return $row;
    }

    public function getByIdSpecialist(string $specialistId): array
    {
        $sql = "SELECT
                    sl.*,
                    c.city_name,
                    s.state_name,
                    co.country_name
                FROM {$this->table} sl
                LEFT JOIN cities    c  ON c.city_id    = sl.city_id    AND c.deleted_at  IS NULL
                LEFT JOIN states    s  ON s.state_id    = sl.state_id   AND s.deleted_at  IS NULL
                LEFT JOIN countries co ON co.country_id = sl.country_id AND co.deleted_at IS NULL
                WHERE sl.specialist_id = ? AND sl.deleted_at IS NULL
                ORDER BY sl.is_primary DESC, sl.created_at DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("s", $specialistId);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    public function create(array $data): array
    {
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;

            $env = new \ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new \TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $createdAt = $env->getCurrentDatetime();

            $locationId    = $this->generateUUIDv4();
            $specialistId  = $data['specialist_id'] ?? '';
            $cityId        = $data['city_id']      ?? null;
            $stateId       = $data['state_id']     ?? null;
            $countryId     = $data['country_id']   ?? null;
            $isPrimary     = (int) ($data['is_primary'] ?? 0);

            $stmt = $this->db->prepare(
                "INSERT INTO {$this->table}
                 (location_id, specialist_id, city_id, state_id, country_id, is_primary, created_at, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            if (!$stmt) {
                throw new \mysqli_sql_exception("Error preparing statement: " . $this->db->error);
            }

            $stmt->bind_param(
                "sssssiis",
                $locationId,
                $specialistId,
                $cityId,
                $stateId,
                $countryId,
                $isPrimary,
                $createdAt,
                $userId
            );
            $stmt->execute();
            $stmt->close();

            // Si se marca como principal, desmarcar las demás ubicaciones del especialista
            if ($isPrimary === 1 && $specialistId) {
                $this->clearOtherPrimaries($specialistId, $locationId);
            }

            $this->db->commit();

            return [
                'value'   => true,
                'message' => '',
                'id'      => $locationId
            ];
        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function update(string $id, array $data): array
    {
        if (!$this->exists($id)) {
            return ['value' => false, 'message' => 'Location not found'];
        }

        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;

            $env = new \ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new \TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $updatedAt = $env->getCurrentDatetime();

            // Obtén el specialist_id de la ubicación (lo usamos para limpiar primarias si aplica)
            $q = $this->db->prepare("SELECT specialist_id FROM {$this->table} WHERE location_id = ? LIMIT 1");
            if (!$q) {
                throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
            }
            $q->bind_param("s", $id);
            $q->execute();
            $res = $q->get_result();
            $row = $res->fetch_assoc();
            $q->close();
            $specialistId = $row['specialist_id'] ?? null;

            $cityId    = $data['city_id']    ?? null;
            $stateId   = $data['state_id']   ?? null;
            $countryId = $data['country_id'] ?? null;
            $isPrimary = isset($data['is_primary']) ? (int)$data['is_primary'] : 0;

            $stmt = $this->db->prepare(
                "UPDATE {$this->table}
                 SET city_id = ?, state_id = ?, country_id = ?, is_primary = ?, updated_at = ?, updated_by = ?
                 WHERE location_id = ?"
            );
            if (!$stmt) {
                throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
            }

            $stmt->bind_param(
                "sssisss",
                $cityId,
                $stateId,
                $countryId,
                $isPrimary,
                $updatedAt,
                $userId,
                $id
            );
            $stmt->execute();
            $stmt->close();

            if ($isPrimary === 1 && $specialistId) {
                $this->clearOtherPrimaries($specialistId, $id);
            }

            $this->db->commit();
            return ['value' => true, 'message' => ''];
        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function delete(string $id): array
    {
        if (!$this->exists($id)) {
            return ['value' => false, 'message' => 'Location not found'];
        }

        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;

            $env = new \ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new \TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $deletedAt = $env->getCurrentDatetime();

            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE location_id = ?");
            if (!$stmt) {
                throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
            }
            $stmt->bind_param("sss", $deletedAt, $userId, $id);
            $stmt->execute();
            $stmt->close();

            $this->db->commit();
            return ['value' => true, 'message' => ''];
        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
