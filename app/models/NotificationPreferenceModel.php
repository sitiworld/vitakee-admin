<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/UuidHelper.php';

/**
 * NotificationPreferenceModel
 * Gestiona las preferencias de notificación (push / email) por usuario.
 */
class NotificationPreferenceModel
{
    private $db;
    private string $table = 'user_notification_preferences';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene las preferencias del usuario. Si no existen, las crea con defaults.
     *
     * @param string $userId
     * @param string $userType  'user' | 'specialist' | 'administrator'
     * @return array  ['push_enabled' => 1, 'email_enabled' => 1]
     */
    public function getPreferences(string $userId, string $userType = 'user'): array
    {
        $sql  = "SELECT push_enabled, email_enabled FROM {$this->table}
                 WHERE user_id = ? AND user_type = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("[NotificationPreferenceModel] prepare error: " . $this->db->error);
            return $this->defaults();
        }
        $stmt->bind_param('ss', $userId, $userType);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        if ($row) {
            return [
                'push_enabled'  => (int)$row['push_enabled'],
                'email_enabled' => (int)$row['email_enabled'],
            ];
        }

        // No existe → insertar con valores por defecto y devolver defaults
        $this->createDefaults($userId, $userType);
        return $this->defaults();
    }

    /**
     * Actualiza las preferencias de notificación del usuario.
     *
     * @param string $userId
     * @param string $userType
     * @param int    $pushEnabled   0 | 1
     * @param int    $emailEnabled  0 | 1
     * @return bool
     */
    public function updatePreferences(
        string $userId,
        string $userType,
        int $pushEnabled,
        int $emailEnabled
    ): bool {
        // UPSERT: inserta o actualiza si ya existe
        $uuid = UuidHelper::generateUUIDv4();
        $sql = "INSERT INTO {$this->table} (preferences_id, user_id, user_type, push_enabled, email_enabled)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    push_enabled  = VALUES(push_enabled),
                    email_enabled = VALUES(email_enabled),
                    updated_at    = NOW()";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("[NotificationPreferenceModel] prepare error: " . $this->db->error);
            return false;
        }
        $stmt->bind_param('sssii', $uuid, $userId, $userType, $pushEnabled, $emailEnabled);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * Verifica rápidamente si el usuario tiene email habilitado.
     *
     * @param string $userId
     * @param string $userType
     * @return bool
     */
    public function isEmailEnabled(string $userId, string $userType = 'user'): bool
    {
        $sql  = "SELECT email_enabled FROM {$this->table}
                 WHERE user_id = ? AND user_type = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param('ss', $userId, $userType);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        return $row ? (bool)$row['email_enabled'] : false;
    }

    /**
     * Verifica rápidamente si el usuario tiene push habilitado.
     *
     * @param string $userId
     * @param string $userType
     * @return bool
     */
    public function isPushEnabled(string $userId, string $userType = 'user'): bool
    {
        $sql  = "SELECT push_enabled FROM {$this->table}
                 WHERE user_id = ? AND user_type = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return true; // Default: enabled

        $stmt->bind_param('ss', $userId, $userType);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        return $row ? (bool)$row['push_enabled'] : true;
    }

    // -------------------------------------------------------------------------
    // Helpers privados
    // -------------------------------------------------------------------------

    private function defaults(): array
    {
        return ['push_enabled' => 0, 'email_enabled' => 0];
    }

    private function createDefaults(string $userId, string $userType): void
    {
        $uuid = UuidHelper::generateUUIDv4();
        $sql  = "INSERT IGNORE INTO {$this->table} (preferences_id, user_id, user_type, push_enabled, email_enabled)
                 VALUES (?, ?, ?, 0, 0)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return;
        $stmt->bind_param('sss', $uuid, $userId, $userType);
        $stmt->execute();
        $stmt->close();
    }
}
