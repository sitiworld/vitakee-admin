<?php

require_once __DIR__ . '/../models/CommentBiomarkerModel.php';
require_once "app/core/Language.php";


class CommentBiomarkerController
{
    private CommentBiomarkerModel $commentBiomarkerModel;
    private array $traducciones; // <-- AÑADIDO

    public function __construct()
    {
        $this->commentBiomarkerModel = new CommentBiomarkerModel();

        // --- AÑADIDO: Carga las traducciones globales ---        
        $this->traducciones = Language::loadLanguage($_SESSION['lang']);


        // ------------------------------------------------
    }

    /* ============================================================
     * READ
     * ============================================================ */

    public function showCommentsByPanelAndTest(array $parametros): void
    {
        $panel = $parametros['panel'] ?? null;
        $test = $parametros['test'] ?? null;

        // --- AÑADIDO: Obtener especialista de la sesión ---
        $specialistId = $_SESSION['user_id'] ?? null;

        try {
            if (!$panel || !$test) {
                $this->jsonResponse(false, $this->traducciones['error_invalid_panel_test_id']);
            }

            // --- AÑADIDO: Validar sesión ---
            if (!$specialistId) {
                // Puedes crear una clave de traducción para esto
                $this->jsonResponse(false, 'Error: Specialist not authenticated.');
            }

            // --- MODIFICADO: Pasar el $specialistId al modelo ---
            $data = $this->commentBiomarkerModel->getCommentsByPanelAndTest($panel, $test, $specialistId);

            if (!$data) {
                $this->jsonResponse(true, $this->traducciones['info_no_comments_found']);
            }

            $this->jsonResponse(true, $this->traducciones['success_comments_fetched'], $data);
        } catch (\mysqli_sql_exception $e) {
            $this->jsonResponse(false, $this->traducciones['error_loading_comment'] . ': ' . $e->getMessage());
        }
    }

    public function showCommentsByPanelAndTestWithSpecialist(array $parametros): void
    {
        $panel = $parametros['panel'] ?? null;
        $test = $parametros['test'] ?? null;

        try {
            if (!$panel || !$test) {
                $this->jsonResponse(false, $this->traducciones['error_invalid_panel_test_id']);
            }

            $data = $this->commentBiomarkerModel->getCommentsByPanelAndTestWithSpecialist($panel, $test);

            if (!$data) {
                $this->jsonResponse(true, $this->traducciones['info_no_comments_found']);
            }

            $this->jsonResponse(true, $this->traducciones['success_comments_fetched'], $data);
        } catch (\mysqli_sql_exception $e) {
            $this->jsonResponse(false, $this->traducciones['error_loading_comment'] . ': ' . $e->getMessage());
        }
    }

    public function showCommentById(array $parametros): void
    {
        $id = $parametros['id'] ?? null;

        try {
            if (!$id) {
                $this->jsonResponse(false, $this->traducciones['error_invalid_comment_id']);
            }

            $data = $this->commentBiomarkerModel->getCommentById($id);
            if (!$data) {
                $this->jsonResponse(false, $this->traducciones['error_comment_not_found']);
            }

            $this->jsonResponse(true, $this->traducciones['success_comment_fetched'], $data);
        } catch (\mysqli_sql_exception $e) {
            $this->jsonResponse(false, $this->traducciones['error_loading_comment'] . ': ' . $e->getMessage());
        }
    }

    public function showCommentsBySpecialist(array $parametros): void
    {
        $specialistId = $parametros['id_specialist'] ?? null;

        try {
            if (!$specialistId) {
                $this->jsonResponse(false, $this->traducciones['error_invalid_specialist_id']);
            }

            $data = $this->commentBiomarkerModel->getCommentsBySpecialist($specialistId);
            if (!$data) {
                $this->jsonResponse(false, $this->traducciones['info_no_comments_specialist']);
            }

            $this->jsonResponse(true, $this->traducciones['success_comments_fetched'], $data);
        } catch (\mysqli_sql_exception $e) {
            $this->jsonResponse(false, $this->traducciones['error_loading_comment'] . ': ' . $e->getMessage());
        }
    }

    public function getUserAndTestByComment(array $parametros): void
    {
        $commentId = $parametros['comment_id'] ?? null;

        try {
            if (!$commentId) {
                $this->jsonResponse(false, $this->traducciones['error_invalid_comment_id']);
            }

            $data = $this->commentBiomarkerModel->getUserAndTestByCommentId($commentId);
            if (!$data) {
                $this->jsonResponse(false, $this->traducciones['error_comment_not_found']);
            }

            $this->jsonResponse(true, $this->traducciones['success_user_test_data_fetched'], $data);
        } catch (\mysqli_sql_exception $e) {
            $this->jsonResponse(false, $this->traducciones['error_loading_comment'] . ': ' . $e->getMessage());
        }
    }

    /* ============================================================
     * CREATE / UPDATE
     * ============================================================ */

    // --- MODIFICADO: Mantiene el fix de 'upsert' y usa traducciones ---
    public function createComment(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, $this->traducciones['error_method_not_allowed_post']);
        }

        $data = $_POST;
        try {
            // Llama a 'upsert' como corregimos, que devuelve el ID
            $commentId = $this->commentBiomarkerModel->upsert($data);

            // Prepara el ID para el frontend
            $responseData = [['id' => $commentId]];

            // Usa la nueva clave de éxito genérica
            $this->jsonResponse(true, $this->traducciones['success_create_update_comment'], $responseData);

        } catch (\mysqli_sql_exception $e) {
            // Usa la clave de error de creación para el catch
            $this->jsonResponse(false, $this->traducciones['error_create_comment'] . ': ' . $e->getMessage());
        }
    }

    public function updateComment(array $parametros): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, $this->traducciones['error_method_not_allowed_post']);
        }

        $id = $parametros['id'] ?? null;
        $data = $_POST;

        try {
            if (!$id) {
                $this->jsonResponse(false, $this->traducciones['error_missing_comment_id']);
            }

            $ok = $this->commentBiomarkerModel->update($id, $data);

            // Usa las claves que proporcionaste
            $message = $ok ? $this->traducciones['success_update_comment'] : $this->traducciones['error_update_comment'];
            $this->jsonResponse($ok, $message);

        } catch (\mysqli_sql_exception $e) {
            $this->jsonResponse(false, $this->traducciones['error_exception_update_comment'] . ': ' . $e->getMessage());
        }
    }

    /* ============================================================
     * DELETE
     * ============================================================ */

    public function deleteComment(array $parametros): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->jsonResponse(false, $this->traducciones['error_method_not_allowed_delete']);
        }

        $id = $parametros['id'] ?? null;

        try {
            if (!$id) {
                $this->jsonResponse(false, $this->traducciones['error_missing_comment_id']);
            }

            $ok = $this->commentBiomarkerModel->delete($id);

            // Usa las claves que proporcionaste (y la nueva para "failed")
            $message = $ok ? $this->traducciones['success_delete_comment'] : $this->traducciones['error_failed_delete_comment'];
            $this->jsonResponse($ok, $message);

        } catch (\mysqli_sql_exception $e) {
            // Usa la clave 'error_delete_comment' que diste para errores de conexión/excepción
            $this->jsonResponse(false, $this->traducciones['error_delete_comment'] . ': ' . $e->getMessage());
        }
    }

    /* ============================================================
     * Helpers
     * ============================================================ */

    private function jsonResponse(bool $value, string $message = '', $data = null): void
    {
        header('Content-Type: application/json');
        echo json_encode([
            'value' => $value,
            'message' => $message,
            'data' => $data ?? []
        ]);
        exit;

    }

    protected function view(string $view, array $data = []): void
    {
        $path = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($path)) {
            $this->jsonResponse(false, $this->traducciones['error_view_not_found']);
        }
        extract($data);
        include $path;
    }
}