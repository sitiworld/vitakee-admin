<?php

require_once __DIR__ . '/../models/DashboardAdminModel.php';

class DashboardAdminController
{
    private $model;

    public function __construct()
    {
        $this->model = new DashboardAdminModel();
    }

    private function jsonResponse(bool $value, string $message = '', $data = null): void
    {
        header('Content-Type: application/json');
        echo json_encode([
            'value'   => $value,
            'message' => $message,
            'data'    => is_array($data) ? $data : ($data !== null ? [$data] : [])
        ]);
        exit;
    }

    private function errorResponse(int $httpCode, string $message): void
    {
        http_response_code($httpCode);
        $this->jsonResponse(false, $message);
    }

    /**
     * GET /admin-dashboard/kpis
     * Retorna el resumen de KPIs: total usuarios, especialistas, verificaciones standard y plus
     */
    public function getKpis($params = []): void
    {
        try {
            $kpis = $this->model->getKpiSummary();
            $this->jsonResponse(true, '', $kpis);
        } catch (\Exception $e) {
            error_log('[DashboardAdminController.getKpis] ' . $e->getMessage());
            $this->errorResponse(500, 'Error al obtener KPIs del dashboard: ' . $e->getMessage());
        }
    }

    /**
     * GET /admin-dashboard/top-users
     * Retorna los usuarios con más exámenes registrados
     */
    public function getTopUsersByExams($params = []): void
    {
        try {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $data  = $this->model->getTopUsersByExams($limit);
            $this->jsonResponse(true, '', $data);
        } catch (\Exception $e) {
            error_log('[DashboardAdminController.getTopUsersByExams] ' . $e->getMessage());
            $this->errorResponse(500, 'Error al obtener top usuarios: ' . $e->getMessage());
        }
    }

    /**
     * GET /admin-dashboard/top-specialists
     * Retorna los especialistas con más consultas atendidas
     */
    public function getTopSpecialistsByConsultations($params = []): void
    {
        try {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $data  = $this->model->getTopSpecialistsByConsultations($limit);
            $this->jsonResponse(true, '', $data);
        } catch (\Exception $e) {
            error_log('[DashboardAdminController.getTopSpecialistsByConsultations] ' . $e->getMessage());
            $this->errorResponse(500, 'Error al obtener top especialistas: ' . $e->getMessage());
        }
    }

    /**
     * GET /admin-dashboard/country-distribution
     * Retorna la distribución de usuarios y especialistas por país (identificado por prefijo telefónico).
     */
    public function getCountryDistribution($params = []): void
    {
        try {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 15;
            $type  = isset($_GET['type']) ? $_GET['type'] : 'users';
            $data  = $this->model->getCountryDistribution($limit, $type);
            $this->jsonResponse(true, '', $data);
        } catch (\Exception $e) {
            error_log('[DashboardAdminController.getCountryDistribution] ' . $e->getMessage());
            $this->errorResponse(500, 'Error al obtener distribución por país: ' . $e->getMessage());
        }
    }
}
