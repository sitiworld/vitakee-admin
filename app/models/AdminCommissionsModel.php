<?php
require_once __DIR__ . '/../config/Database.php';

class AdminCommissionsModel
{
    private $db;
    private $table = 'admin_commissions';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE admin_commission_id = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

public function create($data)
{
    $this->db->begin_transaction();
    try {
        $adminId = $_SESSION['user_id'] ?? null;
        $env = new ClientEnvironmentInfo();
        $env->applyAuditContext($this->db, $adminId);
        (new TimezoneManager($this->db))->applyTimezone();
        $createdAt = $env->getCurrentDatetime();

        // Generar UUID para admin_commission_id
        $commissionId = $this->generateUUIDv4();

        $stmt = $this->db->prepare("INSERT INTO {$this->table}
            (admin_commission_id, transaction_id, commission_amount, transaction_type, created_at, created_by)
            VALUES (?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            throw new mysqli_sql_exception("Error preparing statement: " . $this->db->error);
        }

        $stmt->bind_param(
            "sssdss",
            $commissionId,
            $data['transaction_id'],
            $data['commission_amount'],
            $data['transaction_type'],
            $createdAt,
            $adminId
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
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}
    public function delete($id)
    {
        $this->db->begin_transaction();
        try {
            $adminId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo();
            $env->applyAuditContext($this->db, $adminId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $deletedAt = $env->getCurrentDatetime();

            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE admin_commission_id = ?");
            $stmt->bind_param("sss", $deletedAt, $adminId, $id);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
