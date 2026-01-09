<?php

require_once __DIR__ . '/../models/SecondOpinionRequestsModel.php';

class SecondOpinionRequestsController
{
    private $model;
    private $i18n = [];
    private $lang = 'EN'; // EN | ES

    /**
     * Fallbacks internos por clave: EN/ES.
     * Si no existe en /lang/{EN|ES}.php, se usa el que toque por idioma.
     */
    private $defaults = [
        'method_not_allowed'              => ['EN' => 'Method not allowed. %s required.',                'ES' => 'Método no permitido. Se requiere %s.'],
        'unauthorized_missing_session'    => ['EN' => 'Unauthorized: missing session user_id.',          'ES' => 'No autorizado: falta user_id en la sesión.'],
        'invalid_action'                  => ['EN' => 'Invalid action. Use: confirm, reject, cancel.',    'ES' => 'Acción inválida. Usa: confirm, reject, cancel.'],
        'second_opinion_id_required'      => ['EN' => 'second_opinion_id is required.',                  'ES' => 'second_opinion_id es requerido.'],
        'reject_message_required'         => ['EN' => 'reject_message is required when action = reject.', 'ES' => 'reject_message es requerido cuando acción = reject.'],
        'request_confirmed'               => ['EN' => 'Request confirmed.',                              'ES' => 'Solicitud confirmada.'],
        'request_rejected'                => ['EN' => 'Request rejected.',                               'ES' => 'Solicitud rechazada.'],
        'request_cancelled'               => ['EN' => 'Request cancelled.',                              'ES' => 'Solicitud cancelada.'],
        'error_applying_action'           => ['EN' => 'Error applying action: %s',                       'ES' => 'Error al aplicar la acción: %s'],

        'missing_or_invalid_second_opinion_id' => ['EN' => 'Missing or invalid second_opinion_id.',     'ES' => 'Falta o es inválido el second_opinion_id.'],
        'status_updated_successfully'     => ['EN' => 'Status updated successfully.',                    'ES' => 'Estado actualizado correctamente.'],
        'cannot_change_status_from'       => ['EN' => 'The request was not in "%s" status to move forward.', 'ES' => 'La solicitud no estaba en estado "%s" para avanzar.'],
        'error_updating_status'           => ['EN' => 'Error updating status: %s',                       'ES' => 'Error al actualizar el estado: %s'],

        'cannot_cancel_status'            => ['EN' => 'This request could not be cancelled.',            'ES' => 'No se pudo cancelar esta solicitud.'],
        'block_created'                   => ['EN' => 'Block created.',                                  'ES' => 'Bloque creado.'],
        'error_creating_block'            => ['EN' => 'Error creating block: %s',                        'ES' => 'Error al crear el bloque: %s'],
        'block_updated'                   => ['EN' => 'Block updated.',                                  'ES' => 'Bloque actualizado.'],
        'error_updating_block'            => ['EN' => 'Error updating block: %s',                        'ES' => 'Error al actualizar el bloque: %s'],
        'error_retrieving_blocks'         => ['EN' => 'Error retrieving blocks: %s',                     'ES' => 'Error al obtener los bloques: %s'],
        'endpoint_only_block'             => ['EN' => 'This endpoint is for block requests only.',       'ES' => 'Este endpoint es solo para solicitudes de bloque.'],
        'error_retrieving_block'          => ['EN' => 'Error retrieving block: %s',                      'ES' => 'Error al obtener el bloque: %s'],

        'id_required_for_deletion'        => ['EN' => 'ID is required for deletion.',                    'ES' => 'Se requiere ID para eliminar.'],
        'request_deleted_successfully'    => ['EN' => 'Request deleted successfully.',                   'ES' => 'Solicitud eliminada correctamente.'],
        'error_deleting_request'          => ['EN' => 'Error deleting request: %s',                      'ES' => 'Error al eliminar la solicitud: %s'],

        'missing_required_query_params'   => ['EN' => 'Missing required query parameters: specialist_id, pricing_id, start, end.', 'ES' => 'Faltan parámetros requeridos: specialist_id, pricing_id, start, end.'],
        'invalid_date_format'             => ['EN' => 'Invalid date format. Please use YYYY-MM-DD.',     'ES' => 'Formato de fecha inválido. Usa YYYY-MM-DD.'],
        'calendar_data_retrieved'         => ['EN' => 'Calendar data retrieved successfully.',           'ES' => 'Datos de calendario obtenidos correctamente.'],
        'error_fetching_calendar'         => ['EN' => 'An error occurred while fetching calendar data: %s', 'ES' => 'Ocurrió un error al obtener los datos del calendario: %s'],

        'standard_created'                => ['EN' => 'Second opinion (standard) created.',              'ES' => 'Solicitud de segunda opinión (estándar) creada.'],
        'error_creating_request'          => ['EN' => 'Error creating request: %s',                      'ES' => 'Error al crear la solicitud: %s'],
        'missing_or_invalid_id'           => ['EN' => 'Missing or invalid id in route.',                 'ES' => 'Falta id en la ruta o es inválido.'],
        'missing_status'                  => ['EN' => 'Missing required field: status.',                 'ES' => 'Falta el campo requerido: status.'],
        'invalid_type_for_endpoint_id'    => ['EN' => 'Invalid type_request for this endpoint (use /blocks/{id}).', 'ES' => 'type_request inválido para este endpoint (usa /blocks/{id}).'],
        'standard_updated'                => ['EN' => 'Standard request updated.',                       'ES' => 'Solicitud estándar actualizada.'],
        'error_updating_request'          => ['EN' => 'Error updating request: %s',                      'ES' => 'Error al actualizar la solicitud: %s'],
        'error_retrieving_requests'       => ['EN' => 'Error retrieving requests: %s',                   'ES' => 'Error al obtener las solicitudes: %s'],
        'endpoint_only_standard'          => ['EN' => 'This endpoint is for standard requests only.',    'ES' => 'Este endpoint es solo para solicitudes estándar.'],
        'error_retrieving_request'        => ['EN' => 'Error retrieving request: %s',                    'ES' => 'Error al obtener la solicitud: %s'],
        'blocks_do_not_contain_exams'     => ['EN' => 'Blocks do not contain exams/data.',               'ES' => 'Los bloques no contienen exámenes/datos.'],
        'error_retrieving_request_exams'  => ['EN' => 'Error retrieving request exams data: %s',         'ES' => 'Error al obtener los exámenes de la solicitud: %s'],

        'missing_required_fields'         => ['EN' => 'Missing required fields: session user_id and specialist_id', 'ES' => 'Faltan campos requeridos: user_id de sesión y specialist_id'],
        'invalid_type_for_endpoint'       => ['EN' => 'Invalid type_request for this endpoint (use /blocks).', 'ES' => 'type_request inválido para este endpoint (usa /blocks).'],

        'requests_found'                  => ['EN' => 'Requests found.',                                 'ES' => 'Solicitudes encontradas.'],
        'unauthorized'                    => ['EN' => 'Unauthorized.',                                   'ES' => 'No autorizado.'],
    ];

    public function __construct()
    {
        $this->model = new SecondOpinionRequestsModel();

        // Normaliza idioma desde la sesión
        $rawLang = strtoupper($_SESSION['idioma'] ?? $_SESSION['lang'] ?? 'EN');
        $this->lang = in_array($rawLang, ['ES', 'EN'], true) ? $rawLang : 'EN';

        // Carga archivo de idioma si existe: /lang/EN.php o /lang/ES.php
        $langFile = __DIR__ . "/../lang/{$this->lang}.php";
        if (is_file($langFile)) {
            $loaded = include $langFile;
            if (is_array($loaded)) {
                $this->i18n = $loaded;
            }
        }
    }

    /* ===================== Helpers comunes ===================== */

    private function readJsonOrPost(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? '');
        $body = [];
        if (stripos($contentType, 'application/json') !== false) {
            $raw = file_get_contents('php://input') ?: '';
            $json = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                $body = $json;
            }
        }
        return array_merge($_POST, $body);
    }

    private function requireMethod(string $method)
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== $method) {
            $this->errorResponse(405, $this->msg('method_not_allowed', [$method]));
            exit;
        }
    }

    /**
     * Devuelve el mensaje por clave:
     * 1) Usa $this->i18n[$key] si existe.
     * 2) Si no, usa fallback interno según $this->lang con ??.
     * Soporta placeholders con vsprintf.
     */
    private function msg(string $key, array $vars = []): string
    {
        $fallback = $this->defaults[$key] ?? ['EN' => $key, 'ES' => $key];
        $template = $this->i18n[$key] ?? ($this->lang === 'ES' ? ($fallback['ES'] ?? $key) : ($fallback['EN'] ?? $key));
        return $vars ? vsprintf($template, $vars) : $template;
    }

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

    /* ===================== ENDPOINT DE ACCIONES ===================== */
    // POST /second-opinion/requests/action
    // Body JSON/POST: { accion: "confirm|reject|cancel", second_opinion_id: "...", reject_message?: "..." }
    public function action(): void
    {
        $this->requireMethod('POST');

        $in = $this->readJsonOrPost();
        $accion = strtolower(trim((string) ($in['accion'] ?? '')));
        $secondOpinionId = (string) ($in['second_opinion_id'] ?? '');

        $sessionUserId = $_SESSION['user_id'] ?? null;
        if (!$sessionUserId) {
            $this->errorResponse(401, $this->msg('unauthorized_missing_session'));
            return;
        }
        if (!in_array($accion, ['confirm', 'reject', 'cancel'], true)) {
            $this->errorResponse(400, $this->msg('invalid_action'));
            return;
        }
        if ($secondOpinionId === '') {
            $this->errorResponse(400, $this->msg('second_opinion_id_required'));
            return;
        }
        if ($accion === 'reject' && trim((string) ($in['reject_message'] ?? '')) === '') {
            $this->errorResponse(400, $this->msg('reject_message_required'));
            return;
        }

        try {
            $ok = $this->model->applyAction($in);
            $msg = $accion === 'confirm'
                ? $this->msg('request_confirmed')
                : ($accion === 'reject'
                    ? $this->msg('request_rejected')
                    : $this->msg('request_cancelled'));
            $this->jsonResponse(true, $msg, ['ok' => $ok]);
            return;
        } catch (\Throwable $e) {
            $this->errorResponse(400, $this->msg('error_applying_action', [$e->getMessage()]));
            return;
        }
    }

    // ====== CAMBIOS DE ESTADO SECUENCIALES ======

    // POST /second-opinion/requests/{id}/to-awaiting-payment
    public function setAwaitingPayment($parametros)
    {
        $this->requireMethod('POST');

        $id = $parametros['id'] ?? null;
        if (empty($id) || !is_string($id)) {
            return $this->jsonResponse(false, $this->msg('missing_or_invalid_second_opinion_id'));
        }

        try {
            $ok = $this->model->setAwaitingPayment($id);
            if ($ok) {
                $this->jsonResponse(true, $this->msg('status_updated_successfully'));
            } else {
                $this->jsonResponse(false, $this->msg('cannot_change_status_from', ['pending']));
            }
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_updating_status', [$e->getMessage()]));
        }
    }

    // POST /second-opinion/requests/{id}/to-upcoming
    public function setUpcoming($parametros)
    {
        $this->requireMethod('POST');

        $id = $parametros['id'] ?? null;
        if (empty($id) || !is_string($id)) {
            return $this->jsonResponse(false, $this->msg('missing_or_invalid_second_opinion_id'));
        }

        try {
            $ok = $this->model->setUpcoming($id);
            if ($ok) {
                $this->jsonResponse(true, $this->msg('status_updated_successfully'));
            } else {
                $this->jsonResponse(false, $this->msg('cannot_change_status_from', ['awaiting_payment']));
            }
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_updating_status', [$e->getMessage()]));
        }
    }

    // POST /second-opinion/requests/{id}/to-completed
    public function setCompleted($parametros)
    {
        $this->requireMethod('POST');

        $id = $parametros['id'] ?? null;
        if (empty($id) || !is_string($id)) {
            return $this->jsonResponse(false, $this->msg('missing_or_invalid_second_opinion_id'));
        }

        try {
            $ok = $this->model->setCompleted($id);
            if ($ok) {
                $this->jsonResponse(true, $this->msg('status_updated_successfully'));
            } else {
                $this->jsonResponse(false, $this->msg('cannot_change_status_from', ['upcoming']));
            }
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_updating_status', [$e->getMessage()]));
        }
    }

    // ====== CAMBIOS UNIVERSALES (SIEMPRE PERMITIDOS) ======

    // POST /second-opinion/requests/{id}/cancel
    public function setCancelled($parametros)
    {
        $this->requireMethod('POST');

        $id = $parametros['id'] ?? null;
        if (empty($id) || !is_string($id)) {
            return $this->jsonResponse(false, $this->msg('missing_or_invalid_second_opinion_id'));
        }

        try {
            $ok = $this->model->setCancelled($id);
            if ($ok) {
                $this->jsonResponse(true, $this->msg('request_cancelled'));
            } else {
                $this->jsonResponse(false, $this->msg('cannot_cancel_status'));
            }
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_updating_status', [$e->getMessage()]));
        }
    }

    // POST /second-opinion/requests/{id}/reject
    public function setRejected($parametros)
    {
        $this->requireMethod('POST');

        $rejectedMessage = $this->readJsonOrPost()['reject_message'] ?? null;

        $id = $parametros['id'] ?? null;
        if (empty($id) || !is_string($id)) {
            return $this->jsonResponse(false, $this->msg('missing_or_invalid_second_opinion_id'));
        }

        try {
            $ok = $this->model->setRejected($id, $rejectedMessage);
            if ($ok) {
                $this->jsonResponse(true, $this->msg('request_rejected'));
            } else {
                $this->jsonResponse(false, $this->msg('cannot_reject_status'));
            }
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_updating_status', [$e->getMessage()]));
        }
    }

    /* ===================== STANDARD (no-block) ===================== */

    // POST /second-opinion/requests
    public function createStandard()
    {
        $this->requireMethod('POST');

        $src = $this->readJsonOrPost();
        $sessionUserId = $_SESSION['user_id'] ?? null;

        if (!$sessionUserId || empty($src['specialist_id'])) {
            return $this->errorResponse(400, $this->msg('missing_required_fields'));
        }

        // Forzamos type_request distinto a block (por si viene mal)
        $typeReq = strtolower(trim((string) ($src['type_request'] ?? 'appointment_request')));
        if ($typeReq === 'block') {
            return $this->errorResponse(400, $this->msg('invalid_type_for_endpoint'));
        }

        $data = [
            'user_id'          => $sessionUserId,
            'specialist_id'    => $src['specialist_id'],
            'type_request'     => $typeReq,
            'status'           => isset($src['status']) ? strtolower($src['status']) : 'pending',
            'timezone'         => $_SESSION['timezone'] ?? 'UTC',
            'request_date_to'  => $src['request_date_to'] ?? null,
            'notes'            => $src['notes'] ?? '',
            'shared_until'     => $src['shared_until'] ?? null,
        ];
        if (isset($src['request_date_end']))   $data['request_date_end']   = $src['request_date_end'];
        if (isset($src['pricing_id']))         $data['pricing_id']         = $src['pricing_id'];
        if (isset($src['scope_request']))      $data['scope_request']      = $src['scope_request'];
        if (isset($src['cost_request']))       $data['cost_request']       = $src['cost_request'];
        if (isset($src['duration_request']))   $data['duration_request']   = $src['duration_request'];
        if (isset($src['data']) && is_array($src['data'])) {
            $data['data'] = $src['data']; // items: { panel_id, biomarkers_selected, exams }
        }

        try {
            $secondOpinionId = $this->model->createStandard($data);
            $this->jsonResponse(true, $this->msg('standard_created'), [
                'second_opinion_id' => $secondOpinionId
            ]);
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_creating_request', [$e->getMessage()]));
        }
    }

    // POST /second-opinion/requests/{id}
    public function updateStandard($parametros)
    {
        $this->requireMethod('POST');

        $id = $parametros['id'] ?? null;
        if (empty($id) || !is_string($id)) {
            return $this->jsonResponse(false, $this->msg('missing_or_invalid_id'));
        }

        $src = $this->readJsonOrPost();
        if (!isset($src['status'])) {
            return $this->jsonResponse(false, $this->msg('missing_status'));
        }

        // Rechazar block aquí
        if (isset($src['type_request']) && strtolower((string) $src['type_request']) === 'block') {
            return $this->errorResponse(400, $this->msg('invalid_type_for_endpoint_id'));
        }

        $data = [
            'status'          => strtolower($src['status']),
            'notes'           => $src['notes'] ?? '',
            'shared_until'    => $src['shared_until'] ?? null,
            'request_date_to' => $src['request_date_to'] ?? null,
        ];
        if (array_key_exists('request_date_end', $src)) $data['request_date_end'] = $src['request_date_end'];
        if (array_key_exists('type_request', $src))     $data['type_request']     = $src['type_request'];
        if (array_key_exists('scope_request', $src))    $data['scope_request']    = $src['scope_request'];
        if (array_key_exists('cost_request', $src))     $data['cost_request']     = $src['cost_request'];
        if (array_key_exists('pricing_id', $src))       $data['pricing_id']       = $src['pricing_id'];
        if (array_key_exists('duration_request', $src)) $data['duration_request'] = $src['duration_request'];
        if (array_key_exists('data', $src) && is_array($src['data'])) {
            $data['data'] = $src['data'];
        }

        try {
            $this->model->updateStandard($id, $data);
            $this->jsonResponse(true, $this->msg('standard_updated'));
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_updating_request', [$e->getMessage()]));
        }
    }

    // GET /second-opinion/requests
    public function listStandardForSpecialist()
    {
        try {
            $specialistId = $_SESSION['user_id'] ?? null;
            if (!$specialistId) {
                return $this->errorResponse(401, $this->msg('unauthorized_missing_session'));
            }
            $records = $this->model->getRequestsForSpecialist($specialistId);
            // Filtrar solo no-block
            $records = array_values(array_filter($records, function ($r) {
                return isset($r['type_request']) && strtolower($r['type_request']) !== 'block';
            }));
            $this->jsonResponse(true, '', $records);
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_retrieving_requests', [$e->getMessage()]));
        }
    }

    public function getRequestsForSpecialist()
    {
        $specialist_id = $_SESSION['user_id'] ?? null;
        if (!$specialist_id) {
            $this->jsonResponse(false, $this->msg('unauthorized'), null);
            return;
        }

        // Filtros desde $_GET
        $filters = [
            'status' => $_GET['status'] ?? 'all',
            'type'   => $_GET['type'] ?? 'all',
            'search' => $_GET['search'] ?? ''
        ];

        try {
            $requests = $this->model->getRequestsForSpecialist($specialist_id, $filters);
            $this->jsonResponse(true, $this->msg('requests_found'), $requests);
        } catch (\Throwable $e) {
            $this->jsonResponse(false, "Error: " . $e->getMessage(), null);
        }
    }

    public function getRequestsByIdForSpecialist($parametros)
    {
        try {
            $specialistId = $_SESSION['user_id'] ?? null;
            $second_opinion_id = $parametros['id'] ?? null;
            $records = $this->model->getRequestByIdForSpecialist($second_opinion_id, $specialistId);
            $this->jsonResponse(true, '', $records);
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_retrieving_requests', [$e->getMessage()]));
        }
    }

    public function getRequestsForUser()
    {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            $this->jsonResponse(false, $this->msg('unauthorized'), null);
            return;
        }

        $filters = [
            'status' => $_GET['status'] ?? 'all'
        ];

        try {
            $requests = $this->model->getRequestsForUser($user_id, $filters);
            $this->jsonResponse(true, $this->msg('requests_found'), $requests);
        } catch (\Throwable $e) {
            $this->jsonResponse(false, "Error: " . $e->getMessage(), null);
        }
    }

    public function getRequestsByIdForUser($parametros)
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $second_opinion_id = $parametros['id'] ?? null;
            $records = $this->model->getRequestByIdForUser($second_opinion_id);
            $this->jsonResponse(true, '', $records);
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_retrieving_requests', [$e->getMessage()]));
        }
    }

    public function getRequestData($parametros)
    {
        try {
            $second_opinion_id = $parametros['id'] ?? null;
            $records = $this->model->getRequestData($second_opinion_id);
            $this->jsonResponse(true, '', $records);
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_retrieving_request_exams', [$e->getMessage()]));
        }
    }

    // GET /second-opinion/requests/{id}
    public function getStandardByIdForSpecialist($parametros)
    {
        try {
            $specialistId = $_SESSION['user_id'] ?? null;
            if (!$specialistId) {
                return $this->errorResponse(401, $this->msg('unauthorized_missing_session'));
            }
            $second_opinion_id = $parametros['id'] ?? null;
            if (empty($second_opinion_id) || !is_string($second_opinion_id)) {
                return $this->errorResponse(400, $this->msg('missing_or_invalid_second_opinion_id'));
            }
            $record = $this->model->getRequestByIdForSpecialist($second_opinion_id, $specialistId);
            if ($record && isset($record['type_request']) && strtolower($record['type_request']) === 'block') {
                return $this->errorResponse(400, $this->msg('endpoint_only_standard'));
            }
            $this->jsonResponse(true, '', $record);
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_retrieving_request', [$e->getMessage()]));
        }
    }

    // GET /second-opinion/requests/{id}/exams
    public function getStandardRequestData($parametros)
    {
        try {
            $second_opinion_id = $parametros['id'] ?? null;
            if (empty($second_opinion_id) || !is_string($second_opinion_id)) {
                return $this->errorResponse(400, $this->msg('missing_or_invalid_second_opinion_id'));
            }

            // Si fuera un 'block', devolvemos error claro
            $req = $this->model->getById($second_opinion_id);
            if ($req && isset($req['type_request']) && strtolower($req['type_request']) === 'block') {
                return $this->errorResponse(400, $this->msg('blocks_do_not_contain_exams'));
            }

            $records = $this->model->getRequestData($second_opinion_id);
            $this->jsonResponse(true, '', $records);
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_retrieving_request_exams', [$e->getMessage()]));
        }
    }

    /* ===================== BLOCKS ===================== */

    // POST /second-opinion/blocks
    public function createBlock()
    {
        $this->requireMethod('POST');

        $src = $this->readJsonOrPost();
        $sessionUserId = $_SESSION['user_id'] ?? null;
        $sessionTimezone = $_SESSION['timezone'] ?? 'UTC';

        if (!$sessionUserId || empty($src['specialist_id'])) {
            return $this->errorResponse(400, $this->msg('missing_required_fields'));
        }

        // Forzamos block aquí
        $data = [
            'user_id'          => $sessionUserId,
            'specialist_id'    => $src['specialist_id'],
            'type_request'     => 'block',
            'status'           => 'pending',
            'request_date_to'  => $src['request_date_to'] ?? null,
            'request_date_end' => $src['request_date_end'] ?? null,
            'timezone'         => $sessionTimezone,
            'notes'            => array_key_exists('notes', $src) ? ($src['notes'] ?? null) : null,
            'shared_until'     => null,
            'pricing_id'       => null,
            'scope_request'    => null,
            'cost_request'     => null,
            'duration_request' => null,
        ];

        try {
            $secondOpinionId = $this->model->createBlock($data);
            $this->jsonResponse(true, $this->msg('block_created'), [
                'second_opinion_id' => $secondOpinionId
            ]);
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_creating_block', [$e->getMessage()]));
        }
    }

    // POST /second-opinion/blocks/{id}
    public function updateBlock($parametros)
    {
        $this->requireMethod('POST');

        $id = $parametros['id'] ?? null;
        if (empty($id) || !is_string($id)) {
            return $this->jsonResponse(false, $this->msg('missing_or_invalid_id'));
        }

        $src = $this->readJsonOrPost();
        $sessionTimezone = $_SESSION['timezone'] ?? null;

        $data = [
            'type_request' => 'block',
            'timezone'     => $sessionTimezone,
        ];

        if (array_key_exists('request_date_to', $src))  $data['request_date_to']  = $src['request_date_to'];
        if (array_key_exists('request_date_end', $src)) $data['request_date_end'] = $src['request_date_end'];
        if (array_key_exists('notes', $src))            $data['notes']            = $src['notes'];
        if (array_key_exists('status', $src))           $data['status']           = strtolower($src['status']);

        try {
            $this->model->updateBlock($id, $data);
            $this->jsonResponse(true, $this->msg('block_updated'));
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_updating_block', [$e->getMessage()]));
        }
    }

    // GET /second-opinion/blocks
    public function listBlocksForSpecialist()
    {
        try {
            $specialistId = $_SESSION['user_id'] ?? null;
            if (!$specialistId) {
                return $this->errorResponse(401, $this->msg('unauthorized_missing_session'));
            }
            $records = $this->model->getRequestsForSpecialist($specialistId);
            $records = array_values(array_filter($records, function ($r) {
                return isset($r['type_request']) && strtolower($r['type_request']) === 'block';
            }));
            $this->jsonResponse(true, '', $records);
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_retrieving_blocks', [$e->getMessage()]));
        }
    }

    // GET /second-opinion/blocks/{id}
    public function getBlockByIdForSpecialist($parametros)
    {
        try {
            $specialistId = $_SESSION['user_id'] ?? null;
            if (!$specialistId) {
                return $this->errorResponse(401, $this->msg('unauthorized_missing_session'));
            }
            $second_opinion_id = $parametros['id'] ?? null;
            if (empty($second_opinion_id) || !is_string($second_opinion_id)) {
                return $this->errorResponse(400, $this->msg('missing_or_invalid_second_opinion_id'));
            }
            $record = $this->model->getRequestByIdForSpecialist($second_opinion_id, $specialistId);
            if ($record && isset($record['type_request']) && strtolower($record['type_request']) !== 'block') {
                return $this->errorResponse(400, $this->msg('endpoint_only_block'));
            }
            $this->jsonResponse(true, '', $record);
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_retrieving_block', [$e->getMessage()]));
        }
    }

    /* ===================== DELETE (común) ===================== */

    // DELETE /second-opinion/requests/{id}
    // DELETE /second-opinion/blocks/{id}
    public function delete($parametros)
    {
        $this->requireMethod('DELETE');

        $id = $parametros['id'] ?? null;
        if (empty($id) || !is_string($id)) {
            return $this->jsonResponse(false, $this->msg('id_required_for_deletion'));
        }

        try {
            $this->model->delete($id);
            $this->jsonResponse(true, $this->msg('request_deleted_successfully'));
        } catch (\mysqli_sql_exception $e) {
            $this->errorResponse(400, $this->msg('error_deleting_request', [$e->getMessage()]));
        }
    }

    // GET /second-opinion-slots
    public function getCalendarData()
    {
        $this->requireMethod('GET');
        $pricingId = $_GET['pricing_id'] ?? null;

        try {
            $specialistId = $_GET['specialist_id'] ?? null;
            $start = $_GET['start'] ?? null;
            $end   = $_GET['end'] ?? null;
            $userTimezone = $_SESSION['timezone'] ?? 'UTC';

            if (!$specialistId || !$pricingId || !$start || !$end) {
                $this->errorResponse(400, $this->msg('missing_required_query_params'));
                return;
            }

            $dateRegex = '/^\d{4}-\d{2}-\d{2}$/';
            if (!preg_match($dateRegex, $start) || !preg_match($dateRegex, $end)) {
                $this->errorResponse(400, $this->msg('invalid_date_format'));
                return;
            }

            require_once __DIR__ . '/../models/SpecialistPricingModel.php';
            $pricingModel = new SpecialistPricingModel();

            $calendarData = $pricingModel->getCalendarData($specialistId, $pricingId, $start, $end, $userTimezone);

            $this->jsonResponse(true, $this->msg('calendar_data_retrieved'), $calendarData);
        } catch (\Throwable $e) {
            $this->errorResponse(500, $this->msg('error_fetching_calendar', [$e->getMessage()]));
        }
    }
}
