<?php
require_once __DIR__ . '/../config/Database.php';

class TransactionsModel
{
    private $db;
    private $table = 'transactions';

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
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE transaction_id = ? AND deleted_at IS NULL");
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

            // Generar UUID para transaction_id
            $transactionId = $this->generateUUIDv4();

            $stmt = $this->db->prepare("INSERT INTO {$this->table}
            (transaction_id, user_id, specialist_id, pricing_id, amount_usd, type, platform_fee, status, payment_reference, created_at, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "ssssssdssss",
                $transactionId,
                $data['user_id'],
                $data['specialist_id'],
                $data['pricing_id'],
                $data['amount_usd'],
                $data['type'],
                $data['platform_fee'],
                $data['status'],
                $data['payment_reference'],
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

            $stmt = $this->db->prepare("UPDATE {$this->table}
                SET amount_usd = ?, platform_fee = ?, status = ?, payment_reference = ?, updated_at = ?, updated_by = ?
                WHERE transaction_id = ?");

            $stmt->bind_param(
                "dsssss",
                $data['amount_usd'],
                $data['platform_fee'],
                $data['status'],
                $data['payment_reference'],
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

            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE transaction_id = ?");
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
