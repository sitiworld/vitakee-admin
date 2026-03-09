<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/NotificationPreferenceModel.php';

class NotificationPreferenceController
{
    private NotificationPreferenceModel $model;

    public function __construct()
    {
        $this->model = new NotificationPreferenceModel();
    }

    public function getPreferences(): void
    {
        [$userId, $userType] = $this->resolveUser();

        if (!$userId) {
            $this->errorResponse(401, 'User not authenticated.');
            return;
        }

        $prefs = $this->model->getPreferences($userId, $userType);
        $this->jsonResponse(true, 'Preferences retrieved.', $prefs);
    }

    public function updatePreferences(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->errorResponse(405, 'Method not allowed.');
            return;
        }

        [$userId, $userType] = $this->resolveUser();

        if (!$userId) {
            $this->errorResponse(401, 'User not authenticated.');
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true) ?? [];

        $pushEnabled  = isset($body['push_enabled'])  ? (int)(bool)$body['push_enabled']  : null;
        $emailEnabled = isset($body['email_enabled']) ? (int)(bool)$body['email_enabled'] : null;

        if ($pushEnabled === null && $emailEnabled === null) {
            $this->errorResponse(400, 'No preferences provided.');
            return;
        }

        $current = $this->model->getPreferences($userId, $userType);
        $push    = $pushEnabled  ?? $current['push_enabled'];
        $email   = $emailEnabled ?? $current['email_enabled'];

        $ok = $this->model->updatePreferences($userId, $userType, $push, $email);

        if ($ok) {
            $this->jsonResponse(true, 'Preferences updated.', [
                'push_enabled'  => $push,
                'email_enabled' => $email,
            ]);
        } else {
            $this->errorResponse(500, 'Failed to update preferences.');
        }
    }

    private function resolveUser(): array
    {
        // En vitakee-admin el ID se guarda en administrator_id
        $userId = $_SESSION['administrator_id'] ?? $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return [null, 'user'];
        }

        // Si tenemos administrator_id, sabemos que es administrador
        if (isset($_SESSION['administrator_id'])) {
            return [$userId, 'administrator'];
        }

        // Fallback por si acaso
        $role = $_SESSION['roles_user'] ?? '';
        if (strtolower($role) === 'administrator') {
            return [$userId, 'administrator'];
        }

        return [$userId, 'user'];
    }

    private function jsonResponse(bool $value, string $message, mixed $data = null): void
    {
        header('Content-Type: application/json');
        echo json_encode([
            'value'   => $value,
            'message' => $message,
            'data'    => $data ?? [],
        ]);
        exit;
    }

    private function errorResponse(int $code, string $message): void
    {
        http_response_code($code);
        $this->jsonResponse(false, $message);
    }
}
