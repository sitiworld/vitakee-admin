<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';

/**
 * PushSubscriptionModel
 * CRUD para las suscripciones de Web Push por usuario/dispositivo.
 */
class PushSubscriptionModel
{
    private $db;
    private string $table = 'push_subscriptions';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Guarda o actualiza una suscripción push (UPSERT).
     */
    public function subscribe(
        string $userId,
        string $userType,
        string $endpoint,
        string $p256dh,
        string $auth,
        ?string $userAgent = null
    ): bool {
        $sql = "INSERT INTO {$this->table} (user_id, user_type, endpoint, p256dh, auth, user_agent)
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    p256dh     = VALUES(p256dh),
                    auth       = VALUES(auth),
                    user_agent = VALUES(user_agent),
                    created_at = NOW()";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("[PushSubscriptionModel] prepare error: " . $this->db->error);
            return false;
        }
        $stmt->bind_param('ssssss', $userId, $userType, $endpoint, $p256dh, $auth, $userAgent);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * Elimina una suscripción por endpoint.
     */
    public function unsubscribe(string $userId, string $endpoint): bool
    {
        $sql  = "DELETE FROM {$this->table} WHERE user_id = ? AND endpoint = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param('ss', $userId, $endpoint);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * Obtiene todas las suscripciones activas de un usuario.
     * @return array<array{endpoint: string, p256dh: string, auth: string}>
     */
    public function getByUserId(string $userId): array
    {
        $sql  = "SELECT endpoint, p256dh, auth FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param('s', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $subs = [];
        while ($row = $result->fetch_assoc()) {
            $subs[] = $row;
        }
        $stmt->close();
        return $subs;
    }

    /**
     * Elimina un endpoint expirado (llamado cuando el push server responde 404/410).
     */
    public function deleteByEndpoint(string $endpoint): bool
    {
        $sql  = "DELETE FROM {$this->table} WHERE endpoint = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param('s', $endpoint);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
