<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/PushSubscriptionModel.php';

class PushSubscriptionController
{
    private PushSubscriptionModel $model;

    public function __construct()
    {
        $this->model = new PushSubscriptionModel();
    }

    public function subscribe(): void
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

        $endpoint = $body['endpoint']       ?? '';
        $p256dh   = $body['keys']['p256dh'] ?? '';
        $auth     = $body['keys']['auth']   ?? '';

        if (empty($endpoint) || empty($p256dh) || empty($auth)) {
            $this->errorResponse(400, 'Missing subscription data (endpoint, keys.p256dh, keys.auth).');
            return;
        }

        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $ok = $this->model->subscribe($userId, $userType, $endpoint, $p256dh, $auth, $userAgent);

        if ($ok) {
            $this->jsonResponse(true, 'Push subscription saved.');
        } else {
            $this->errorResponse(500, 'Failed to save push subscription.');
        }
    }

    public function unsubscribe(): void
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
        $endpoint = $body['endpoint'] ?? '';

        if (empty($endpoint)) {
            $this->errorResponse(400, 'Missing endpoint.');
            return;
        }

        $this->model->unsubscribe($userId, $endpoint);
        $this->jsonResponse(true, 'Push subscription removed.');
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
