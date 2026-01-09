<?php
require_once __DIR__ . '/../config/Database.php';

class SpecialistCertificationsModel
{
    private $db;
    private $table = 'specialist_certifications';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY certification_id  ASC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE certification_id  = ? AND deleted_at IS NULL");
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

            // Generar UUID para certification_id
            $certificationId = $this->generateUUIDv4();

            $stmt = $this->db->prepare("INSERT INTO {$this->table} 
            (certification_id, specialist_id, file_url, title, description, visibility, created_at, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing statement: " . $this->db->error);
            }

            $stmt->bind_param(
                "ssssssss",
                $certificationId,
                $data['specialist_id'],
                $data['file_url'],
                $data['title'],
                $data['description'],
                $data['visibility'],
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

            // Generar SQL dinámico según si se incluye file_url
            $sql = "UPDATE {$this->table} SET ";
            $params = [];
            $types = "";

            if (!empty($data['file_url'])) {
                $sql .= "file_url = ?, ";
                $params[] = $data['file_url'];
                $types .= "s";
            }

            $sql .= "title = ?, description = ?, visibility = ?, updated_at = ?, updated_by = ? WHERE certification_id  = ?";
            array_push($params, $data['title'], $data['description'], $data['visibility'], $updatedAt, $userId, $id);
            $types .= "ssssss";

            $stmt = $this->db->prepare($sql);
            if (!$stmt)
                throw new Exception("Error preparing statement: " . $this->db->error);

            $stmt->bind_param($types, ...$params);
            $stmt->execute();

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
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $deletedAt = $env->getCurrentDatetime();

            $stmt = $this->db->prepare("UPDATE {$this->table} 
                SET deleted_at = ?, deleted_by = ? WHERE certification_id  = ?");
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
