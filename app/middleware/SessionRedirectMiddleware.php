<?php
require_once 'Middleware.php';

class SessionRedirectMiddleware implements Middleware
{
    public function handle()
    {
        if (isset($_SESSION['user_id']) && isset($_SESSION['roles_user'])) {
            $role = strtolower($_SESSION['roles_user']);

            switch ($role) {
                case 'user':
                    header('Location: dashboard');
                    break;
                case 'specialist':
                    header('Location: dashboard_specialist');
                    break;
                case 'administrator':
                    header('Location: dashboard_administrator');
                    break;
                default:
                    header('Location: dashboard');
                    break;
            }

            exit();
        }

        // Si no hay sesión, permitir continuar normalmente.
    }
}
