<?php
require_once __DIR__ . '/../config/Database.php';

class SpecialistAvailabilityModel
{
    private $db;
    private $table = 'specialist_availability';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY FIELD(weekday, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE availability_id = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function getByIdSpecialist($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE specialist_id  = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
public function create($data)
{
    $this->db->begin_transaction();
    try {
        $userId = $_SESSION['user_id'] ?? null;
        $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $userId);
        $tzManager = new TimezoneManager($this->db);
        $tzManager->applyTimezone();
        $createdAt = $env->getCurrentDatetime();
        $timezone = $_SESSION['timezone'];
        // Generar UUID para availability_id
        $availabilityId = $this->generateUUIDv4();

        $stmt = $this->db->prepare("INSERT INTO {$this->table} 
            (availability_id, specialist_id, weekday, start_time, end_time, buffer_time_minutes, timezone, created_at, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            throw new mysqli_sql_exception("Error preparing statement: " . $this->db->error);
        }

        $stmt->bind_param(
            "sssssssss",
            $availabilityId,
            $data['specialist_id'],
            $data['weekday'],
            $data['start_time'],
            $data['end_time'],
            $data['buffer_time_minutes'],
            $timezone,
            $createdAt,
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
public function syncTimezoneFromSpecialist(string $specialistId): bool
{
    $this->db->begin_transaction();
    try {
        $userId = $_SESSION['user_id'] ?? null;

        // Aplicar contexto y zona horaria en la DB
        $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $userId);
        $tzManager = new TimezoneManager($this->db);
        $tzManager->applyTimezone();

        $updatedAt = $env->getCurrentDatetime();

        // 1. Consultar timezone del especialista
        $stmt = $this->db->prepare("SELECT timezone FROM specialists WHERE specialist_id = ? AND deleted_at IS NULL");
        if (!$stmt) {
            throw new mysqli_sql_exception("Error preparing statement: " . $this->db->error);
        }
        $stmt->bind_param("s", $specialistId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if (!$row || empty($row['timezone'])) {
            throw new Exception("No se encontró timezone para el especialista {$specialistId}");
        }
        $specialistTimezone = $row['timezone'];

        // 2. Actualizar todas las disponibilidades con ese timezone
        $stmt2 = $this->db->prepare("UPDATE {$this->table}
            SET timezone = ?, updated_at = ?, updated_by = ?
            WHERE specialist_id = ? AND deleted_at IS NULL");

        if (!$stmt2) {
            throw new mysqli_sql_exception("Error preparing update statement: " . $this->db->error);
        }

        $stmt2->bind_param("ssss", $specialistTimezone, $updatedAt, $userId, $specialistId);
        $stmt2->execute();

        $this->db->commit();
        return true;
    } catch (Exception $e) {
        $this->db->rollback();
        throw $e;
    }
}


public function update($id, $data)
{
    $this->db->begin_transaction();
    try {
        $userId = $_SESSION['user_id'] ?? null;
        $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $userId);
        $tzManager = new TimezoneManager($this->db);
        $tzManager->applyTimezone();
        $updatedAt = $env->getCurrentDatetime();
        $timezone = $_SESSION['timezone'];
        $stmt = $this->db->prepare("UPDATE {$this->table} 
            SET weekday = ?, start_time = ?, end_time = ?, buffer_time_minutes = ?, timezone = ?, updated_at = ?, updated_by = ?
            WHERE availability_id = ? AND deleted_at IS NULL");

        if (!$stmt) {
            throw new mysqli_sql_exception("Error preparing statement: " . $this->db->error);
        }

        $stmt->bind_param(
            "ssssssss",
            $data['weekday'],
            $data['start_time'],
            $data['end_time'],
            $data['buffer_time_minutes'],
            $timezone,
            $updatedAt,
            $userId,
            $id
        );

        $stmt->execute();
        $this->db->commit();
        return true;
    } catch (mysqli_sql_exception $e) {
        $this->db->rollback();
        throw $e;
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

    public function delete($id)
    {
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $deletedAt = $env->getCurrentDatetime();

            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE availability_id = ?");
            $stmt->bind_param("sss", $deletedAt, $userId, $id);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
