<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/NotificationModel.php';

class NotificationController
{
    private $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Muestra todas las notificaciones (no eliminadas).
     */
    public function showAll()
    {
        try {
            $records = $this->notificationModel->getAll();
            // $records = $this->unpackTemplateParams($records); // <-- ELIMINADO
            $this->jsonResponse(true, 'Notifications retrieved successfully.', $records);
        } catch (Exception $e) {
            $this->errorResponse(500, $e->getMessage());
        }
    }

    /**
     * Muestra una notificación por su ID.
     */
    public function showById($params)
    {
        $id = $params['id'] ?? null;
        if (!$id) {
            return $this->errorResponse(400, "Missing ID parameter.");
        }

        try {
            // getById del modelo ahora también traduce
            $record = $this->notificationModel->getById($id);
            
            if ($record) {
                // $record['template_params'] = json_decode(...); // <-- ELIMINADO (el modelo ya lo hace)
                $this->jsonResponse(true, 'Notification found.', $record);
            } else {
                $this->errorResponse(404, 'Notification not found.');
            }
        } catch (Exception $e) {
            $this->errorResponse(500, $e->getMessage());
        }
    }

    /**
     * Cuenta las notificaciones NUEVAS (new = 1) para el usuario.
     */
    public function countAlertsUser()
    {
        if (!isset($_SESSION['user_id'])) {
            return $this->errorResponse(401, "User not authenticated.");
        }
        $user_id = $_SESSION['user_id'];

        try {
            // Contamos las que tienen flag new = 1 (las "nuevas")
            $count = $this->notificationModel->countAlertsUser($user_id, 1);
            $this->jsonResponse(true, 'Notification count retrieved.', $count);
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }

    /**
     * Endpoint liviano para el polling del badge: devuelve solo el conteo
     * de notificaciones con new=1 para el usuario en sesión.
     * GET /notifications/count-new
     */
    public function countNewBySession()
    {
        if (!isset($_SESSION['user_id'])) {
            return $this->errorResponse(401, "User not authenticated.");
        }
        $user_id = $_SESSION['user_id'];

        try {
            $count = $this->notificationModel->countAlertsUser($user_id, 1);
            $this->jsonResponse(true, 'New notification count retrieved.', $count);
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }

    /**
     * Muestra notificaciones para el usuario actual (paginado).
     */
    public function showByUserId()
    {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            return $this->errorResponse(401, "User not authenticated.");
        }

        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

        try {
            // getByUserId del modelo ahora devuelve los datos traducidos
            $notifications = $this->notificationModel->getByUserId($user_id, $limit, $offset);
            // $notifications = $this->unpackTemplateParams($notifications); // <-- ELIMINADO

            // Contamos las "nuevas" (badge)
            $count = $this->notificationModel->countAlertsUser($user_id, 1);
            // Contamos las "no leídas" (lista de activas)
            $unread_count = $this->notificationModel->countAlertsUserUnread($user_id, 0);

            $this->jsonResponse(true, 'Notifications by user retrieved.', $notifications, null, $count, $unread_count);
        } catch (Exception $e) {
             $this->errorResponse(500, $e->getMessage());
        }
    }
    
    // ---
    // NOTA: 'showByUserIdNotifications' y 'showByUserIdViewAll' han sido eliminadas
    // porque sus funciones en el modelo ('getAllUserAlertsByFlag2', 'getByUserIdViewAll')
    // fueron eliminadas. La lógica de 'showByUserId' o 'showAllAlertsByUserId' las reemplaza.
    // ---


    /**
     * Muestra alertas ACTIVAS (no leídas, read_unread = 0) para el usuario.
     */
    public function showActiveAlertsByUserId()
    {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            return $this->errorResponse(401, "User not authenticated.");
        }

        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

        try {
            $notifications = $this->notificationModel->getActiveAlertsByUserId($user_id, $limit, $offset);
            // $notifications = $this->unpackTemplateParams($notifications); // <-- ELIMINADO

            $count = $this->notificationModel->countAlertsUser($user_id, 1); 
            $unread_count = $this->notificationModel->countAlertsUserUnread($user_id, 0); 

            $this->jsonResponse(true, 'Active notifications retrieved.', $notifications, null, $count, $unread_count);
        } catch (Exception $e) {
             $this->errorResponse(500, $e->getMessage());
        }
    }

    /**
     * Muestra alertas DESCARTADAS (leídas, read_unread = 1) para el usuario.
     */
    public function showDismissedAlertsByUserId()
    {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            return $this->errorResponse(401, "User not authenticated.");
        }

        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

        try {
            $notifications = $this->notificationModel->getDismissedAlertsByUserId($user_id, $limit, $offset);
            // $notifications = $this->unpackTemplateParams($notifications); // <-- ELIMINADO

            $count = $this->notificationModel->countAlertsUserUnread($user_id, 1); 
            $unread_count = $this->notificationModel->countAlertsUserUnread($user_id, 0); 

            $this->jsonResponse(true, 'Dismissed notifications retrieved.', $notifications, null, $count, $unread_count);
        } catch (Exception $e) {
             $this->errorResponse(500, $e->getMessage());
        }
    }

    /**
     * Muestra TODAS las alertas (activas y descartadas) para el usuario.
     */
    public function showAllAlertsByUserId()
    {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            return $this->errorResponse(401, "User not authenticated.");
        }

        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

        try {
            $notifications = $this->notificationModel->getAllAlertsByUserId($user_id, $limit, $offset);
            // $notifications = $this->unpackTemplateParams($notifications); // <-- ELIMINADO

            $count = $this->notificationModel->countAlertsUserUnread($user_id, [0, 1]);
            $unread_count = $this->notificationModel->countAlertsUserUnread($user_id, 0);

            $this->jsonResponse(true, 'All notifications retrieved.', $notifications, null, $count, $unread_count);
        } catch (Exception $e) {
             $this->errorResponse(500, $e->getMessage());
        }
    }

    /**
     * Muestra todas las notificaciones del sistema (para un Admin).
     */
    public function showAllAdmin()
    {
        try {
            $notifications = $this->notificationModel->getAll();
            // $notifications = $this->unpackTemplateParams($notifications); // <-- ELIMINADO
            $this->jsonResponse(true, 'Notifications for all users retrieved.', $notifications);
        } catch (Exception $e) {
            $this->errorResponse(500, 'Error retrieving notifications: ' . $e->getMessage());
        }
    }

    /**
     * Marca todas las notificaciones del usuario como "leídas" (read_unread = 1).
     */
    public function updateNoAlertUserByUserId()
    {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            return $this->errorResponse(401, "User not authenticated.");
        }

        try {
            $success = $this->notificationModel->updateAllNoAlertUserByUserId($user_id);
            if ($success) {
                $this->jsonResponse(true, "User notifications updated successfully.");
            } else {
                $this->errorResponse(500, "Failed to update user notifications.");
            }
        } catch (Exception $e) {
             $this->errorResponse(500, $e->getMessage());
        }
    }

    /**
     * Marca todas las notificaciones del sistema como "leídas por admin".
     */
    public function updateNoAlertAdminAll()
    {
        try {
            $success = $this->notificationModel->updateAllNoAlertAdmin();
            if ($success) {
                $this->jsonResponse(true, "All admin notifications updated successfully.");
            } else {
                $this->errorResponse(500, "Failed to update admin notifications.");
            }
        } catch (Exception $e) {
             $this->errorResponse(500, $e->getMessage());
        }
    }


    /**
     * Muestra notificaciones por 'status' (buscando en JSON).
     */
    public function showByStatus($params)
    {
        $status = $params['status'] ?? null;
        if (!$status) {
            return $this->errorResponse(400, "Missing status parameter.");
        }

        try {
            $items = $this->notificationModel->getByStatus($status);
            // $items = $this->unpackTemplateParams($items); // <-- ELIMINADO
            $this->jsonResponse(true, "Notifications with status '$status' retrieved.", $items);
        } catch (Exception $e) {
             $this->errorResponse(500, $e->getMessage());
        }
    }

    /**
     * Muestra alertas activas (status != 'ok' en JSON).
     */
    public function showActiveAlerts()
    {
        try {
            $items = $this->notificationModel->getActiveAlerts();
            // $items = $this->unpackTemplateParams($items); // <-- ELIMINADO
            $this->jsonResponse(true, 'Active alert notifications retrieved.', $items);
        } catch (Exception $e) {
             $this->errorResponse(500, $e->getMessage());
        }
    }

    /**
     * Muestra notificaciones por 'biomarker_id' (buscando en JSON).
     */
    public function showByBiomarkerId($params)
    {
        $id_biomarker = $params['id_biomarker'] ?? null;
        if (!$id_biomarker) {
            return $this->errorResponse(400, "Missing biomarker ID.");
        }

        try {
            $items = $this->notificationModel->getByBiomarkerId($id_biomarker);
            // $items = $this->unpackTemplateParams($items); // <-- ELIMINADO
            $this->jsonResponse(true, "Notifications for biomarker #$id_biomarker retrieved.", $items);
        } catch (Exception $e) {
             $this->errorResponse(500, $e->getMessage());
        }
    }

    /**
     * Muestra notificaciones por 'biomarker_id' y 'user_id' (buscando en JSON).
     */
    public function showByBiomarkerAndUser($params)
    {
        session_start(); // (Tu código original)
        $user_id = $_SESSION['user_id'] ?? null;
        $id_biomarker = $params['id_biomarker'] ?? null;

        if (!$user_id) {
            return $this->errorResponse(401, "User not authenticated.");
        }
        if (!$id_biomarker) {
            return $this->errorResponse(400, "Missing biomarker ID.");
        }

        try {
            $notifications = $this->notificationModel->getByBiomarkerAndUser($id_biomarker, $user_id);
            // $notifications = $this->unpackTemplateParams($notifications); // <-- ELIMINADO
            $this->jsonResponse(true, "User notifications for biomarker #$id_biomarker retrieved.", $notifications);
        } catch (Exception $e) {
             $this->errorResponse(500, $e->getMessage());
        }
    }

    /**
     * Marca una notificación como "leída" (read_unread = 1).
     */
    public function updateNoAlertUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse(405, "Method not allowed. POST is required.");
        }
        
        $notificationId = $this->getJsonInput()['notification_id'] ?? $this->getJsonInput()['record_id'] ?? null;
        
        if (!$notificationId) {
            return $this->errorResponse(400, "Missing notification_id.");
        }

        try {
            $success = $this->notificationModel->updateNoAlertUser($notificationId);
            if ($success) {
                $this->jsonResponse(true, "read_unread updated for notification #$notificationId.");
            } else {
                $this->errorResponse(400, "Error updating read_unread.");
            }
        } catch (Exception $e) {
             $this->errorResponse(500, $e->getMessage());
        }
    }

    /**
     * Marca las notificaciones de un usuario como "nuevas" (new = 1).
     */
public function updateNew()
{
    // Asegura sesión iniciada (opcional si ya la inicias en el bootstrap)
    if (session_status() === PHP_SESSION_NONE) session_start();

    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        return $this->errorResponse(401, "User not authenticated.");
    }

    try {
        $success = $this->notificationModel->updateNew($user_id);
        if ($success) {
            // AQUÍ estaba el typo
            $this->jsonResponse(true, "User notifications updated successfully.");
        } else {
            $this->errorResponse(500, "Failed to update user notifications.");
        }
    } catch (Exception $e) {
        $this->errorResponse(500, $e->getMessage());
    }
}


    /**
     * Marca una notificación como "leída por admin" (en JSON).
     */
    public function updateNoAlertAdmin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse(405, "Method not allowed. POST is required.");
        }
        
        $data = $this->getJsonInput();
        $notificationId = $data['notification_id'] ?? $_POST['notification_id'] ?? $data['record_id'] ?? $_POST['record_id'] ?? null;

        if (!$notificationId) {
            return $this->errorResponse(400, "Missing notification_id.");
        }

        try {
            $success = $this->notificationModel->updateNoAlertAdmin($notificationId);
            if ($success) {
                $this->jsonResponse(true, "admin_read updated for notification #$notificationId.");
            } else {
                $this->errorResponse(400, "Error updating admin_read.");
            }
        } catch (Exception $e) {
             $this->errorResponse(500, $e->getMessage());
        }
    }

    /**
     * Crea una notificación (usado por el NotificationService).
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse(405, "Method not allowed. POST is required.");
        }
        $data = $this->getJsonInput();

        try {
            $result = $this->notificationModel->create($data);
            $this->jsonResponse($result['status'] === 'success', $result['message'], $result);
        } catch (Exception $e) {
             $this->errorResponse(500, $e->getMessage());
        }
    }

    /**
     * Actualiza una notificación.
     */
    public function update($params)
    {
        $id = $params['id'] ?? null;
        if (!$id) {
            return $this->errorResponse(400, "Missing ID parameter.");
        }
        $data = $this->getJsonInput();
        $data['id'] = $id;

        try {
            $result = $this->notificationModel->update($data);
            $this->jsonResponse($result['status'] === 'success', $result['message'], $result);
        } catch (Exception $e) {
             $this->errorResponse(500, $e->getMessage());
        }
    }

    /**
     * Elimina (lógicamente) una notificación.
     */
    public function delete($params)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            return $this->errorResponse(405, "Method not allowed. DELETE is required.");
        }
        $id = $params['id'] ?? null;
        if (!$id) {
            return $this->errorResponse(400, "Invalid or missing ID.");
        }

        try {
            $result = $this->notificationModel->delete($id);
            $this->jsonResponse($result['status'] === 'success', $result['message']);
        } catch (Exception $e) {
             $this->errorResponse(500, $e->getMessage());
        }
    }

    // --- Helpers ---

    /**
     * Helper para desempaquetar el JSON de template_params en un array.
     * *** ESTA FUNCIÓN HA SIDO ELIMINADA PORQUE AHORA ES REDUNDANTE ***
     */
    /*
    private function unpackTemplateParams(array $notifications): array
    {
        $processed = [];
        foreach ($notifications as $noti) {
            $noti['template_params'] = json_decode($noti['template_params'] ?? 'null', true);
            $processed[] = $noti;
        }
        return $processed;
    }
    */

    private function getJsonInput(): array
    {
        $input = file_get_contents("php://input");
        return json_decode($input, true) ?? [];
    }

    /**
     * Helper de respuesta JSON modificado.
     */
    private function jsonResponse($value, $message = '', $data = null, $labels = null, $count = null, $unread_count = null)
    {
        header('Content-Type: application/json');
        $response = [
            'value' => $value,
            'message' => $message,
            'data' => $data ?? [], // Asegura que 'data' nunca sea null
        ];
        
        if ($count !== null) {
            $response['count'] = $count;
        }
        if ($unread_count !== null) {
            $response['unread_count'] = $unread_count;
        }
        if ($labels !== null) {
             $response['labels'] = $labels;
        }

        echo json_encode($response);
        exit;
    }


    private function errorResponse(int $http_code, string $message)
    {
        http_response_code($http_code);
        $this->jsonResponse(false, $message);
    }
}

