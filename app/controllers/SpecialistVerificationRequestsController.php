<?php

require_once __DIR__ . '/../models/SpecialistVerificationRequestsModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class SpecialistVerificationRequestsController
{
    private $model;

    public function __construct()
    {
        $this->model = new SpecialistVerificationRequestsModel();
    }

    /* ========================
     * Helpers de mensajes (ES/EN)
     * ======================== */
    private function msg(string $key): string
    {
        $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');

        $en = [
            'invalid_id'          => 'Invalid ID',
            'invalid_spec_id'     => 'Invalid specialist_id',
            'not_found'           => 'Verification request not found',
            'not_found_for_spec'  => 'Verification request not found for this specialist',
            'list_error'          => 'Error retrieving requests: ',
            'get_error'           => 'Error retrieving request: ',
            'create_missing'      => 'Missing required field: specialist_id',
            'create_ok'           => 'Verification request submitted successfully',
            'create_error'        => 'Error creating verification request: ',
            'update_missing'      => 'Missing required fields',
            'update_ok'           => 'Verification request updated successfully',
            'update_error'        => 'Error updating request: ',
            'delete_missing'      => 'ID is required for deletion',
            'delete_ok'           => 'Verification request deleted successfully',
            'delete_error'        => 'Error deleting request: ',
            'method_put'          => 'Method not allowed. PUT required.',
            'method_delete'       => 'Method not allowed. DELETE required.',
            'approve_ok'          => 'Verification request approved successfully.',
            'approve_error'       => 'Error approving verification request: ',
            'reject_ok'           => 'Verification request rejected successfully.',
            'reject_error'        => 'Error rejecting verification request: ',
        ];

        $es = [
            'invalid_id'          => 'ID inválido',
            'invalid_spec_id'     => 'specialist_id inválido',
            'not_found'           => 'Solicitud de verificación no encontrada',
            'not_found_for_spec'  => 'No se encontró una solicitud de verificación para este especialista',
            'list_error'          => 'Error al obtener las solicitudes: ',
            'get_error'           => 'Error al obtener la solicitud: ',
            'create_missing'      => 'Falta el campo obligatorio: specialist_id',
            'create_ok'           => 'Solicitud de verificación enviada correctamente',
            'create_error'        => 'Error al crear la solicitud de verificación: ',
            'update_missing'      => 'Faltan campos obligatorios',
            'update_ok'           => 'Solicitud de verificación actualizada correctamente',
            'update_error'        => 'Error al actualizar la solicitud: ',
            'delete_missing'      => 'Se requiere el ID para eliminar',
            'delete_ok'           => 'Solicitud de verificación eliminada correctamente',
            'delete_error'        => 'Error al eliminar la solicitud: ',
            'method_put'          => 'Método no permitido. Se requiere PUT.',
            'method_delete'       => 'Método no permitido. Se requiere DELETE.',
            'approve_ok'          => 'Solicitud de verificación aprobada correctamente.',
            'approve_error'       => 'Error al aprobar la solicitud de verificación: ',
            'reject_ok'           => 'Solicitud de verificación rechazada correctamente.',
            'reject_error'        => 'Error al rechazar la solicitud de verificación: ',
        ];

        $map = ($idioma === 'ES') ? $es : $en;
        return $map[$key] ?? $key;
    }

    private function effectivePut(): bool
    {
        $m = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($m === 'PUT') return true;
        if ($m === 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PUT') return true;
        return false;
    }

    /* ========================
     * GET /verification-requests/{id}
     * ======================== */
    public function getById($parametros)
    {
        $id = $parametros['id'] ?? null;
        try {
            if (!is_string($id) || trim($id) === '') {
                return $this->jsonResponse(false, $this->msg('invalid_id'));
            }

            $record = $this->model->getByIdWithDetails($id);
            if ($record) {
                $this->jsonResponse(true, '', $record);
            } else {
                $this->jsonResponse(false, $this->msg('not_found'));
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('get_error') . $e->getMessage());
        }
    }

    /* ========================
     * GET /verification-requests
     * ======================== */
    public function getAll()
    {
        try {
            $records = $this->model->getAllWithDetails();
            $this->jsonResponse(true, '', $records);
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('list_error') . $e->getMessage());
        }
    }

    /* ========================
     * GET /verification-requests/by-specialist/{specialist_id}
     * ======================== */
    public function getBySpecialist($parametros)
    {
        $specialistId = $parametros['specialist_id'] ?? null;
        try {
            if (!is_string($specialistId) || trim($specialistId) === '') {
                return $this->jsonResponse(false, $this->msg('invalid_spec_id'));
            }

            $record = $this->model->getByIdSpecialistWithDetails($specialistId);
            if ($record) {
                $this->jsonResponse(true, '', $record);
            } else {
                $this->jsonResponse(false, $this->msg('not_found_for_spec'));
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('get_error') . $e->getMessage());
        }
    }

    /* ========================
     * POST /verification-requests
     * ======================== */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['specialist_id'])) {
            $data = [
                'specialist_id'      => $_POST['specialist_id'],
                'status'             => $_POST['status'] ?? 'PENDING',
                'verification_level' => $_POST['verification_level'] ?? 'STANDARD'
            ];

            try {
                $this->model->create($data);
                $this->jsonResponse(true, $this->msg('create_ok'));
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, $this->msg('create_error') . $e->getMessage());
            }
        } else {
            $this->errorResponse(400, $this->msg('create_missing'));
        }
    }

    /* ========================
     * PUT /verification-requests/{id}
     * ======================== */
    public function update($parametros)
    {
        if ($this->effectivePut()) {
            $id = $parametros['id'] ?? null;
            if (!is_string($id) || trim($id) === '' || !isset($_POST['status'])) {
                return $this->jsonResponse(false, $this->msg('update_missing'));
            }

            $data = [
                'status'             => $_POST['status'],
                'approved_at'        => $_POST['approved_at'] ?? date('Y-m-d H:i:s'),
                'verification_level' => $_POST['verification_level'] ?? 'STANDARD'
            ];

            try {
                $this->model->update($id, $data);
                $this->jsonResponse(true, $this->msg('update_ok'));
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, $this->msg('update_error') . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, $this->msg('method_put'));
        }
    }

    /* ========================
 * PUT/POST /verification-requests/{id}/approve
 * ======================== */
public function approve($parametros)
{
    $isPut = $this->effectivePut();
    $isPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';

    if (!($isPut || $isPost)) {
        return $this->errorResponse(405, $this->msg('method_put'));
    }

    $id = $parametros['id'] ?? null;
    if (!is_string($id) || trim($id) === '') {
        return $this->jsonResponse(false, $this->msg('invalid_id'));
    }

    try {
        $record = $this->model->getByIdWithDetails($id);
        if (!$record) {
            return $this->jsonResponse(false, 'Request not found');
        }

        $this->model->approveRequest($id);

        $notifModel = new NotificationModel();
        $notifModel->create([
            'user_id' => $record['specialist_id'],
            'rol' => 'specialist',
            'type' => 'VERIFICATION_APPROVED',
            'status' => 'UNREAD',
            'template_key' => 'verification_approved',
            'route' => '/profile',
            'template_params' => null
        ]);

        $this->jsonResponse(true, $this->msg('approve_ok'));
    } catch (mysqli_sql_exception $e) {
        $this->errorResponse(400, $this->msg('approve_error') . $e->getMessage());
    }
}
    /* ========================
 * PUT/POST /verification-requests/{id}/reject
 * ======================== */
public function reject($parametros)
{
    $isPut = $this->effectivePut();
    $isPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';

    if (!($isPut || $isPost)) {
        return $this->errorResponse(405, $this->msg('method_put'));
    }

    $id = $parametros['id'] ?? null;
    if (!is_string($id) || trim($id) === '') {
        return $this->jsonResponse(false, $this->msg('invalid_id'));
    }

    try {
        $record = $this->model->getByIdWithDetails($id);
        if (!$record) {
            return $this->jsonResponse(false, 'Request not found');
        }

        $this->model->rejectRequest($id);

        $notifModel = new NotificationModel();
        $notifModel->create([
            'user_id' => $record['specialist_id'],
            'rol' => 'specialist',
            'type' => 'VERIFICATION_REJECTED',
            'status' => 'UNREAD',
            'template_key' => 'verification_rejected',
            'route' => '/profile',
            'template_params' => null
        ]);

        $this->jsonResponse(true, $this->msg('reject_ok'));
    } catch (mysqli_sql_exception $e) {
        $this->errorResponse(400, $this->msg('reject_error') . $e->getMessage());
    }
}

    /* ========================
     * DELETE /verification-requests/{id}
     * ======================== */
    public function delete($parametros)
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'DELETE') {
            $id = $parametros['id'] ?? null;
            if (!is_string($id) || trim($id) === '') {
                return $this->jsonResponse(false, $this->msg('delete_missing'));
            }

            try {
                $this->model->delete($id);
                $this->jsonResponse(true, $this->msg('delete_ok'));
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, $this->msg('delete_error') . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, $this->msg('method_delete'));
        }
    }

    /* ========================
     * Respuestas JSON
     * ======================== */
    private function jsonResponse(bool $value, string $message = '', $data = null)
    {
        header('Content-Type: application/json');

        $response = [
            'value'   => $value,
            'message' => $message,
            'data'    => is_array($data) ? $data : ($data !== null ? [$data] : [])
        ];

        echo json_encode($response);
        exit;
    }

    private function errorResponse($http_code, $message)
    {
        http_response_code($http_code);
        $this->jsonResponse(false, $message);
    }
}
