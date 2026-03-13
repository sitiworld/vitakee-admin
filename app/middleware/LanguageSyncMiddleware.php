<?php

class LanguageSyncMiddleware
{
    /**
     * Sincroniza el idioma de la URL con la base de datos si el usuario está autenticado.
     */
    public static function handle()
    {
        // 1. Si hay ?lang en URL, forzar ese idioma y actualizar DB
        if (!empty($_GET['lang'])) {
            $lang = strtoupper($_GET['lang']);
            $_SESSION['idioma'] = $lang;
            $_SESSION['lang'] = $lang;

            if (!empty($_SESSION['user_id']) && !empty($_SESSION['roles_user'])) {
                $role = strtolower($_SESSION['roles_user']);
                self::updateDatabaseLanguage($_SESSION['user_id'], $role, $lang);
            }
            return;
        }

        // 2. Si no hay ?lang en URL, pero el usuario está logueado, sincronizar SESIÓN con DB
        if (!empty($_SESSION['user_id']) && !empty($_SESSION['roles_user'])) {
            $userId = $_SESSION['user_id'];
            $role = strtolower($_SESSION['roles_user']);
            
            $dbLang = self::getLanguageFromDatabase($userId, $role);
            if ($dbLang && (!isset($_SESSION['idioma']) || $_SESSION['idioma'] !== $dbLang)) {
                $_SESSION['idioma'] = $dbLang;
                $_SESSION['lang'] = $dbLang;
            }
        }
    }

    private static function updateDatabaseLanguage($userId, $role, $lang)
    {
        if ($role === 'user') {
            self::updateUserLanguage($userId, $lang);
        } elseif ($role === 'specialist') {
            self::updateSpecialistLanguage($userId, $lang);
        } elseif ($role === 'administrator') {
            self::updateAdministratorLanguage($userId, $lang);
        }
    }

    private static function getLanguageFromDatabase($userId, $role)
    {
        require_once __DIR__ . '/../config/Database.php';
        $db = Database::getInstance();
        $table = '';
        $idCol = '';

        if ($role === 'user') {
            $table = 'users'; $idCol = 'user_id';
        } elseif ($role === 'specialist') {
            $table = 'specialists'; $idCol = 'specialist_id';
        } elseif ($role === 'administrator') {
            $table = 'administrators'; $idCol = 'administrator_id';
        } else {
            return null;
        }

        $stmt = $db->prepare("SELECT interface_language FROM $table WHERE $idCol = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['interface_language'] ?? null;
        }
        return null;
    }

    private static function updateUserLanguage($userId, $lang)
    {
        require_once __DIR__ . '/../models/UserModel.php';
        $userModel = new UserModel();
        $userModel->setIdioma($userId, $lang);
    }

    private static function updateSpecialistLanguage($specialistId, $lang)
    {
        require_once __DIR__ . '/../models/SpecialistModel.php';
        $specialistModel = new SpecialistModel();
        $specialistModel->setIdioma($specialistId, $lang);
    }

    private static function updateAdministratorLanguage($adminId, $lang)
    {
        require_once __DIR__ . '/../models/AdministratorModel.php';
        $adminModel = new AdministratorModel();
        $adminModel->setIdioma($adminId, $lang);
    }

}
