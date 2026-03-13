<?php

// ¡Haz esto al principio de la ejecución de tu app!
// date_default_timezone_set('America/Los_Angeles');



require_once "app/core/ViewRenderer.php";
require_once "app/core/Language.php";

use App\Core\ViewRenderer;
use App\Router;

session_start();
require_once 'vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Iniciar sesión si no está iniciada
}
define('APP_ROOT', __DIR__ . '/'); // Define la ruta raíz de la aplicación
define('PROJECT_ROOT', __DIR__);

// 1. Determinar el protocolo (http o https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";

// 2. Obtener el nombre del host (dominio o localhost)
$host = $_SERVER['HTTP_HOST'];

// 3. Obtener la ruta del directorio del proyecto dinámicamente
// rtrim() elimina la barra final si existe para evitar dobles barras '//'
// dirname() obtiene el directorio del script actual (ej: /vitakee)
$path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/';

// 4. Construir y definir la BASE_URL completa
define('BASE_URL', "$protocol://$host$path");




// Middleware (Carga temprana para sincronizar idioma desde DB)
require_once "app/middleware/LanguageSyncMiddleware.php";
LanguageSyncMiddleware::handle();

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'EN'; // Idioma por defecto
    $_SESSION['idioma'] = 'EN'; 
}

$lang = $_SESSION['lang'];



$traducciones = Language::loadLanguage($lang);



// --- Carga de Clases ---
require_once "app/core/ViewRenderer.php";
require_once "app/Router.php";

// Otros Middleware
require_once "app/middleware/AuthMiddleware.php";
require_once "app/middleware/SessionRedirectMiddleware.php";

// Controladores
require_once "app/controllers/AuditLogController.php";
require_once "app/controllers/RecoveryPasswordController.php";
require_once "app/controllers/NotificationController.php";
require_once "app/controllers/PushSubscriptionController.php";
require_once "app/controllers/NotificationPreferenceController.php";
require_once "app/controllers/UserController.php";
require_once "app/controllers/AuthController.php";
require_once "app/controllers/BackupController.php";
require_once "app/controllers/BiomarkerController.php";
require_once "app/controllers/BodyCompositionController.php";
require_once "app/controllers/CommentBiomarkerController.php";
require_once "app/controllers/EnergyMetabolismController.php";
require_once "app/controllers/LipidProfileController.php";
require_once "app/controllers/SecurityQuestionController.php";
require_once "app/controllers/TestDocumentsController.php";
require_once "app/controllers/TestPanelController.php";
require_once "app/controllers/SpecialtyController.php";
require_once "app/controllers/TitleController.php";
require_once "app/controllers/SpecialistController.php";
require_once "app/controllers/AdministratorController.php";
require_once "app/controllers/CountryController.php";
require_once "app/controllers/RenalFunctionController.php";
require_once "app/controllers/SpecialistAvailabilityController.php";
require_once "app/controllers/AdminCommissionsController.php";
require_once "app/controllers/SecondOpinionRequestsController.php";
require_once "app/controllers/SpecialistCertificationsController.php";
require_once "app/controllers/SpecialistLocationsController.php";
require_once "app/controllers/SpecialistPricingController.php";
require_once "app/controllers/SpecialistReviewsController.php";
require_once "app/controllers/SpecialistSocialLinksController.php";
require_once "app/controllers/SpecialistVerificationRequestsController.php";
require_once "app/controllers/TransactionsController.php";
require_once "app/controllers/VideoCallsController.php";
require_once "app/controllers/SessionManagementController.php";
require_once "app/controllers/SessionConfigController.php";
require_once "app/controllers/SecondOpinionDataController.php";
require_once "app/controllers/CitiesController.php";
require_once "app/controllers/StatesController.php";
require_once "app/controllers/ContactEmailController.php";
require_once "app/controllers/ContactPhoneController.php";
require_once "app/controllers/DashboardAdminController.php";


// --- Inicialización ---
$viewRenderer = new ViewRenderer($traducciones, 'app/views/', $_SESSION['lang']);
$router = new Router($viewRenderer, $traducciones);


// -----------------------------------------------------------------------------
// --- DEFINICIÓN DE RUTAS ---
// -----------------------------------------------------------------------------

// =============================================================================
// --- GRUPO PÚBLICO (Vistas de Login, Registro, Recuperación) ---
// =============================================================================
$router->group(['middleware' => SessionRedirectMiddleware::class], function ($router) {
    // Vistas de Login y Registro con soporte de idioma
    $router->get('/', ['vista' => 'auth/login_administrator', 'vistaData' => ['titulo' => 'Administrator login - VITAKEE', 'lang' => 'en', 'layout' => false]]);
    $router->get('/login', ['vista' => 'auth/login_administrator', 'vistaData' => ['titulo' => 'Administrator login - VITAKEE', 'lang' => 'en', 'layout' => false]]);
    $router->get('/es', ['vista' => 'auth/login_administrator', 'vistaData' => ['titulo' => 'Administrator login - VITAKEE', 'lang' => 'es', 'layout' => false]]);
    $router->get('/login/es', ['vista' => 'auth/login_administrator', 'vistaData' => ['titulo' => 'Administrator login - VITAKEE', 'lang' => 'es', 'layout' => false]]);
    $router->get('/administrator/es', ['vista' => 'auth/login_administrator', 'vistaData' => ['titulo' => 'Administrator login - VITAKEE', 'lang' => 'es', 'layout' => false]]);
    $router->get('/administrator', ['vista' => 'auth/login_administrator', 'vistaData' => ['titulo' => 'Administrator login - VITAKEE', 'lang' => 'en', 'layout' => false]]);
    $router->get('/administrator/login/es', ['vista' => 'auth/login_administrator', 'vistaData' => ['titulo' => 'Administrator login - VITAKEE', 'lang' => 'es', 'layout' => false]]);
    $router->get('/administrator/login/', ['vista' => 'auth/login_administrator', 'vistaData' => ['titulo' => 'Administrator login - VITAKEE', 'lang' => 'en', 'layout' => false]]);
    $router->get('/reset_password/administrator', ['vista' => 'auth/reset_password', 'vistaData' => ['titulo' => 'Recovery User - VITAKEE', 'userType' => 'Administrator', 'backTo' => 'administrator', 'layout' => false]]);
    $router->get('/reset_password/administrator/es', ['vista' => 'auth/reset_password', 'vistaData' => ['titulo' => 'Recuperar Usuario - VITAKEE', 'lang' => 'es', 'userType' => 'Administrator', 'backTo' => 'administrator/es', 'layout' => false]]);
});

// =============================================================================
// --- GRUPO DE API PÚBLICA (Endpoints sin autenticación) ---
// =============================================================================
$router->group(['prefix' => ''], function ($router) {
    $router->post('/administrator/login', ['controlador' => AdministratorController::class, 'accion' => 'login']);
    $router->post('/administrator/register', ['controlador' => AdministratorController::class, 'accion' => 'register']);

    // API de Recuperación de contraseña y verificación
    $router->post('/password-recovery/verify-email', ['controlador' => RecoveryPasswordController::class, 'accion' => 'verifyEmail']);
    $router->post('/password-recovery/verify-answers', ['controlador' => RecoveryPasswordController::class, 'accion' => 'verifySecurityAnswers']);
    $router->post('/password-recovery/update-password', ['controlador' => RecoveryPasswordController::class, 'accion' => 'updatePassword']);
    $router->post('/check/check-telephone', ['controlador' => RecoveryPasswordController::class, 'accion' => 'checkTelephone']);
    $router->post('/check/check-email', ['controlador' => RecoveryPasswordController::class, 'accion' => 'checkEmail']);

    $router->post('/check-email-specialist', ['controlador' => SpecialistController::class, 'accion' => 'checkEmail']);
    $router->post('/check-telephone-specialist', ['controlador' => SpecialistController::class, 'accion' => 'checkTelephone']);

    $router->post('/check-email-administrator', ['controlador' => AdministratorController::class, 'accion' => 'checkEmail']);
    $router->post('/check-telephone-administrator', ['controlador' => AdministratorController::class, 'accion' => 'checkTelephone']);

    // API de datos públicos
    $router->get('countries/all', ['controlador' => UserController::class, 'accion' => 'getAllCountries']);
    $router->get('/specialties', ['controlador' => SpecialtyController::class, 'accion' => 'showAll']);
    $router->get('/titles', ['controlador' => TitleController::class, 'accion' => 'showAll']);
});





// =============================================================================
// --- GRUPO DE ADMINISTRADOR (Rol: administrator) ---
// =============================================================================
$router->group(['middleware' => 'AuthMiddleware', 'roles' => ['administrator']], function ($router) {
    // Vistas
    if (isset($_SESSION['roles_user']) && strtolower($_SESSION['roles_user']) === 'administrator') {
        $router->agregarRuta('GET', 'my_profile', ['vista' => 'profile_administrator', 'vistaData' => ['titulo' => 'Profile Administrator', '' => '']]);
    }
    $router->agregarRuta('GET', 'dashboard_administrator', ['vista' => 'dashboard_administrator', 'vistaData' => ['titulo' => 'Dashboard - VITAKEE', '' => '']]);
    $router->agregarRuta('GET', 'dashboard_administrator2', ['vista' => 'dashboard_administrator2', 'vistaData' => ['titulo' => 'Dashboard IA Demo - VITAKEE', '' => '']]);
    $router->agregarRuta('GET', 'backups_view', ['vista' => 'admin_backups', 'vistaData' => ['titulo' => 'Backups - VITAKEE', '' => '']]);
    $router->agregarRuta('GET', 'biomarkers', ['vista' => 'admin_biomarkers', 'vistaData' => ['titulo' => 'Biomarkers - VITAKEE', '' => '']]);
    $router->agregarRuta('GET', 'test_panels', ['vista' => 'admin_test_panels', 'vistaData' => ['titulo' => 'Test Panels - VITAKEE', '' => '']]);
    $router->agregarRuta('GET', 'users_view', ['vista' => 'admin_users', 'vistaData' => ['titulo' => 'Users - VITAKEE', '' => '']]);
    $router->agregarRuta('GET', 'specialists_view', ['vista' => 'admin_specialists', 'vistaData' => ['titulo' => 'Specialists - VITAKEE', '' => '']]);
    $router->agregarRuta('GET', 'administrators_view', ['vista' => 'admin_administrators', 'vistaData' => ['titulo' => 'Administrators - VITAKEE', '' => '']]);
    $router->agregarRuta('GET', 'specialist_notifications', ['vista' => 'specialist_notifications', 'vistaData' => ['titulo' => 'Notifications - VITAKEE', '' => '']]);
    $router->agregarRuta('GET', 'audit_log_view', ['vista' => 'admin_audit_log', 'vistaData' => ['titulo' => 'Audit Logs - VITAKEE', '' => '']]);
    $router->agregarRuta('GET', 'verification_requests_view', ['vista' => 'admin_verification_requests', 'vistaData' => ['titulo' => $traducciones['verification_requests_title'] ?? 'Verification Requests - VITAKEE']]);
    $router->agregarRuta('GET', 'specialty_view', ['vista' => 'admin_specialty', 'vistaData' => ['titulo' => 'Specialty - VITAKEE', '' => '']]);
    $router->agregarRuta('GET', 'title_view', ['vista' => 'admin_titles', 'vistaData' => ['titulo' => 'Specialists Titles - VITAKEE', '' => '']]);
    $router->agregarRuta('GET', 'countries_view', ['vista' => 'admin_countries', 'vistaData' => ['titulo' => 'Countries - VITAKEE', '' => '']]);
    $router->agregarRuta('GET', 'states_view', ['vista' => 'admin_states', 'vistaData' => ['titulo' => 'States - VITAKEE', '' => '']]);
    $router->agregarRuta('GET', 'cities_view', ['vista' => 'admin_cities', 'vistaData' => ['titulo' => 'Cities - VITAKEE', '' => '']]);
    // API de Gestión (CRUDs)
    $router->agregarRuta('POST', 'administrator/system_type/update/{id}', ['controlador' => AdministratorController::class, 'accion' => 'update_system_type_session_user']);
    $router->agregarRuta('GET', 'administrator_get', ['controlador' => AdministratorController::class, 'accion' => 'showAll']);
    $router->agregarRuta('GET', 'administrator_get/{id}', ['controlador' => AdministratorController::class, 'accion' => 'showById']);
    $router->agregarRuta('POST', 'administrator/create', ['controlador' => AdministratorController::class, 'accion' => 'create']);
    $router->agregarRuta('POST', 'administrator/update/{id}', ['controlador' => AdministratorController::class, 'accion' => 'update']);
    $router->agregarRuta('DELETE', 'administrator/{id}', ['controlador' => AdministratorController::class, 'accion' => 'delete']);
    $router->agregarRuta('POST', 'administrator/update-profile/{id}', ['controlador' => AdministratorController::class, 'accion' => 'update_profile']);
    $router->agregarRuta('POST', 'specialist/create', ['controlador' => SpecialistController::class, 'accion' => 'create']);
    $router->agregarRuta('POST', 'specialist/update/{id}', ['controlador' => SpecialistController::class, 'accion' => 'update']);
    $router->agregarRuta('DELETE', 'specialist/{id}', ['controlador' => SpecialistController::class, 'accion' => 'delete']);
    $router->agregarRuta('GET', 'users', ['controlador' => UserController::class, 'accion' => 'showAll']);
    $router->agregarRuta('POST', 'users', ['controlador' => UserController::class, 'accion' => 'create']);
    $router->agregarRuta('POST', 'users/{id}', ['controlador' => UserController::class, 'accion' => 'update']);
    $router->agregarRuta('DELETE', 'users/{id}', ['controlador' => UserController::class, 'accion' => 'delete']);
    $router->agregarRuta('GET', 'backups', ['controlador' => BackupController::class, 'accion' => 'show']);
    $router->agregarRuta('GET', 'backups/{id}', ['controlador' => BackupController::class, 'accion' => 'showId']);
    $router->agregarRuta('POST', 'backups', ['controlador' => BackupController::class, 'accion' => 'create']);
    $router->agregarRuta('PUT', 'backups/{id}', ['controlador' => BackupController::class, 'accion' => 'update']);
    $router->agregarRuta('DELETE', 'backups/{id}', ['controlador' => BackupController::class, 'accion' => 'delete']);
    $router->agregarRuta('POST', 'backups/{id}/restore', ['controlador' => BackupController::class, 'accion' => 'restore']);
    $router->agregarRuta('GET', 'auditlog', ['controlador' => AuditLogController::class, 'accion' => 'getAll']);
    $router->agregarRuta('GET', 'auditlog/{id}', ['controlador' => AuditLogController::class, 'accion' => 'getById']);
    $router->agregarRuta('GET', 'auditlog/export/{id}', ['controlador' => AuditLogController::class, 'accion' => 'exportCSV']);
    $router->agregarRuta('GET', 'test-panels', ['controlador' => TestPanelController::class, 'accion' => 'getAll']);
    $router->agregarRuta('GET', 'test-panels/{id}', ['controlador' => TestPanelController::class, 'accion' => 'getById']);
    $router->agregarRuta('POST', 'test-panels', ['controlador' => TestPanelController::class, 'accion' => 'create']);
    $router->agregarRuta('PUT', 'test-panels/{id}', ['controlador' => TestPanelController::class, 'accion' => 'update']);
    $router->agregarRuta('DELETE', 'test-panels/{id}', ['controlador' => TestPanelController::class, 'accion' => 'delete']);
    $router->agregarRuta('GET', 'test-panels/export/{id}', ['controlador' => TestPanelController::class, 'accion' => 'exportCSVPanels']);
    $router->agregarRuta('GET', 'titles/{id}', ['controlador' => TitleController::class, 'accion' => 'showById']);
    $router->agregarRuta('POST', 'titles', ['controlador' => TitleController::class, 'accion' => 'create']);
    $router->agregarRuta('PUT', 'titles/{id}', ['controlador' => TitleController::class, 'accion' => 'update']);
    $router->agregarRuta('DELETE', 'titles/{id}', ['controlador' => TitleController::class, 'accion' => 'delete']);
    $router->agregarRuta('GET', 'titles/export/{id}', ['controlador' => TitleController::class, 'accion' => 'export']);
    $router->agregarRuta('GET', 'specialties/{id}', ['controlador' => SpecialtyController::class, 'accion' => 'showById']);
    $router->agregarRuta('POST', 'specialties', ['controlador' => SpecialtyController::class, 'accion' => 'create']);
    $router->agregarRuta('PUT', 'specialties/{id}', ['controlador' => SpecialtyController::class, 'accion' => 'update']);
    $router->agregarRuta('DELETE', 'specialties/{id}', ['controlador' => SpecialtyController::class, 'accion' => 'delete']);
    $router->agregarRuta('GET', 'specialties/export/{id}', ['controlador' => SpecialtyController::class, 'accion' => 'export']);
    $router->agregarRuta('GET', 'countries', ['controlador' => CountryController::class, 'accion' => 'showAll']);
    $router->agregarRuta('GET', 'countries/{id}', ['controlador' => CountryController::class, 'accion' => 'showById']);
    $router->agregarRuta('POST', 'countries', ['controlador' => CountryController::class, 'accion' => 'create']);
    $router->agregarRuta('PUT', 'countries/{id}', ['controlador' => CountryController::class, 'accion' => 'update']);
    $router->agregarRuta('DELETE', 'countries/{id}', ['controlador' => CountryController::class, 'accion' => 'delete']);
    $router->agregarRuta('GET', 'countries/export/{id}', ['controlador' => CountryController::class, 'accion' => 'export']);
    $router->agregarRuta('GET', 'biomarkers/all', ['controlador' => BiomarkerController::class, 'accion' => 'showAll']);
    $router->agregarRuta('GET', 'biomarkers/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'showById']);
    $router->agregarRuta('POST', 'biomarkers', ['controlador' => BiomarkerController::class, 'accion' => 'create']);
    $router->agregarRuta('PUT', 'biomarkers/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'update']);
    $router->agregarRuta('PUT', 'biomarkers/es/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'update_es']);
    $router->agregarRuta('DELETE', 'biomarkers/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'delete']);
    $router->agregarRuta('GET', 'biomarkers/export/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'exportBiomarkers']);


    // CRUD routes for video_calls
    $router->agregarRuta('GET', 'video-calls', [
        'controlador' => VideoCallsController::class,
        'accion' => 'getAll'
    ]);

    $router->agregarRuta('GET', 'video-calls/{id}', [
        'controlador' => VideoCallsController::class,
        'accion' => 'getById'
    ]);

    $router->agregarRuta('POST', 'video-calls', [
        'controlador' => VideoCallsController::class,
        'accion' => 'create'
    ]);

    $router->agregarRuta('PUT', 'video-calls/{id}', [
        'controlador' => VideoCallsController::class,
        'accion' => 'update'
    ]);

    $router->agregarRuta('DELETE', 'video-calls/{id}', [
        'controlador' => VideoCallsController::class,
        'accion' => 'delete'
    ]);

    // CRUD routes for transactions
    $router->agregarRuta('GET', 'transactions', [
        'controlador' => TransactionsController::class,
        'accion' => 'getAll'
    ]);

    $router->agregarRuta('GET', 'transactions/{id}', [
        'controlador' => TransactionsController::class,
        'accion' => 'getById'
    ]);

    $router->agregarRuta('POST', 'transactions', [
        'controlador' => TransactionsController::class,
        'accion' => 'create'
    ]);

    $router->agregarRuta('PUT', 'transactions/{id}', [
        'controlador' => TransactionsController::class,
        'accion' => 'update'
    ]);

    $router->agregarRuta('DELETE', 'transactions/{id}', [
        'controlador' => TransactionsController::class,
        'accion' => 'delete'
    ]);




    // CRUD routes for admin_commissions
    $router->agregarRuta('GET', 'admin-commissions', [
        'controlador' => AdminCommissionsController::class,
        'accion' => 'getAll'
    ]);

    $router->agregarRuta('GET', 'admin-commissions/{id}', [
        'controlador' => AdminCommissionsController::class,
        'accion' => 'getById'
    ]);

    $router->agregarRuta('POST', 'admin-commissions', [
        'controlador' => AdminCommissionsController::class,
        'accion' => 'create'
    ]);

    $router->agregarRuta('DELETE', 'admin-commissions/{id}', [
        'controlador' => AdminCommissionsController::class,
        'accion' => 'delete'
    ]);

    // ============================================================
    // --- DASHBOARD ADMIN API (KPIs y tablas de ranking) ---
    // ============================================================
    $router->agregarRuta('GET', 'admin-dashboard/kpis', [
        'controlador' => DashboardAdminController::class,
        'accion' => 'getKpis'
    ]);
    $router->agregarRuta('GET', 'admin-dashboard/top-users', [
        'controlador' => DashboardAdminController::class,
        'accion' => 'getTopUsersByExams'
    ]);
    $router->agregarRuta('GET', 'admin-dashboard/top-specialists', [
        'controlador' => DashboardAdminController::class,
        'accion' => 'getTopSpecialistsByConsultations'
    ]);
    $router->agregarRuta('GET', 'admin-dashboard/country-distribution', [
        'controlador' => DashboardAdminController::class,
        'accion' => 'getCountryDistribution'
    ]);

    // ============================================================
    // --- NOTIFICATIONS & PUSH ---
    // ============================================================
    $router->agregarRuta('GET', 'notifications/preferences', [
        'controlador' => NotificationPreferenceController::class,
        'accion' => 'getPreferences'
    ]);
    $router->agregarRuta('POST', 'notifications/preferences', [
        'controlador' => NotificationPreferenceController::class,
        'accion' => 'updatePreferences'
    ]);
    $router->agregarRuta('POST', 'push/subscribe', [
        'controlador' => PushSubscriptionController::class,
        'accion' => 'subscribe'
    ]);
    $router->agregarRuta('POST', 'push/unsubscribe', [
        'controlador' => PushSubscriptionController::class,
        'accion' => 'unsubscribe'
    ]);

});


// =============================================================================
// --- GRUPO AUTENTICADO (Rutas compartidas por varios roles) ---
// =============================================================================
$router->group(['middleware' => 'AuthMiddleware'], function ($router) {
    // Logout (accesible para todos los roles autenticados)

    $router->agregarRuta('GET', 'users_records', ['vista' => 'admin_users_records', 'vistaData' => ['titulo' => 'Users Records - VITAKEE', '' => ''], 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('GET', 'session_management_view', ['vista' => 'admin_session_management', 'vistaData' => ['titulo' => 'Session Audit - VITAKEE', '' => ''], 'roles' => ['administrator']]);
    $router->agregarRuta('GET', 'specialist_notifications', ['vista' => 'specialist_notifications', 'vistaData' => ['titulo' => 'Notifications - VITAKEE', '' => '']]);
    $router->agregarRuta('GET', 'logout', ['controlador' => AuthController::class, 'accion' => 'logout', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'set-language', ['controlador' => UserController::class, 'accion' => 'setLanguage', 'roles' => ['user', 'specialist', 'administrator']]);

    // Rutas accesibles para CUALQUIER rol autenticado
    $router->agregarRuta('GET', 'users/{id}', ['controlador' => UserController::class, 'accion' => 'showById', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'users', ['controlador' => UserController::class, 'accion' => 'showAll', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'tools/replace-ids', [
        'controlador' => TitleController::class,
        'accion' => 'replaceAllIds',
        'roles' => ['administrator'] // Puedes ajustar roles si hace falta
    ]);

    $router->agregarRuta('GET', 'users/session/{id}', ['controlador' => UserController::class, 'accion' => 'getSessionUserData', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'specialist/session/{id}', ['controlador' => SpecialistController::class, 'accion' => 'getSessionUserData', 'roles' => ['specialist']]); // Get session info for specialist
    $router->agregarRuta('GET', 'administrator/session/{id}', ['controlador' => AdministratorController::class, 'accion' => 'getSessionUserData', 'roles' => ['administrator']]); // Get session info for administrator
    $router->agregarRuta('POST', 'administrator/update-status/{id}', [
        'controlador' => AdministratorController::class,
        'accion' => 'updateStatus',
        'roles' => ['administrator']
    ]);

    $router->agregarRuta('POST', 'user/update-status/{id}', [
        'controlador' => UserController::class,
        'accion' => 'updateStatus',
        'roles' => ['administrator']
    ]);

    $router->agregarRuta('POST', 'specialist/update-status/{id}', [
        'controlador' => SpecialistController::class,
        'accion' => 'updateStatus',
        'roles' => ['administrator']
    ]);

    $router->agregarRuta('GET', 'security-questions/{id}', ['controlador' => SecurityQuestionController::class, 'accion' => 'getByUserReset', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'security-questions', ['controlador' => SecurityQuestionController::class, 'accion' => 'getByUser', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'test-documents/{id}', ['controlador' => TestDocumentsController::class, 'accion' => 'showImage', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'security-questions', ['controlador' => SecurityQuestionController::class, 'accion' => 'getByUser', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('POST', 'security-questions', ['controlador' => SecurityQuestionController::class, 'accion' => 'create', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('PUT', 'security-questions/{id}', ['controlador' => SecurityQuestionController::class, 'accion' => 'update', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('DELETE', 'security-questions/{id}', ['controlador' => SecurityQuestionController::class, 'accion' => 'delete', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'test-documents/panel/{test_panel_id}/test/{test_id}', ['controlador' => TestDocumentsController::class, 'accion' => 'getImagesByPanelAndTest', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'test-panels/user-records/{user_id}', ['controlador' => TestPanelController::class, 'accion' => 'getUserRecordCounts', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'test-panels/{user_id}/{panel_id}', ['controlador' => TestPanelController::class, 'accion' => 'getUserPanelRecords', 'roles' => ['user', 'specialist', 'administrator']]);




    $router->agregarRuta('GET', 'energy_metabolism/{id}', ['controlador' => EnergyMetabolismController::class, 'accion' => 'showById', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'body-compositions/{id}', ['controlador' => BodyCompositionController::class, 'accion' => 'getBodyCompositionData', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'lipid-profile/{record_id}', ['controlador' => LipidProfileController::class, 'accion' => 'getRecord', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'renal-function/{id}', ['controlador' => RenalFunctionController::class, 'accion' => 'showById', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'security_question', ['vista' => 'security_question', 'vistaData' => ['titulo' => 'Security Questions', '' => ''], 'roles' => ['user', 'specialist', 'administrator']]);

    $router->agregarRuta('POST', 'specialist/create', ['controlador' => SpecialistController::class, 'accion' => 'create', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('POST', 'specialist/update/{id}', ['controlador' => SpecialistController::class, 'accion' => 'update', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('DELETE', 'specialist/{id}', ['controlador' => SpecialistController::class, 'accion' => 'delete', 'roles' => ['specialist', 'administrator']]);
    $router->post('/specialists/search', [
        'controlador' => SpecialistController::class,
        'accion' => 'searchFilters',
        'roles' => ['specialist', 'administrator', 'user']
    ]);
    $router->get('/specialists/search', [
        'controlador' => SpecialistController::class,
        'accion' => 'searchByName',
        'roles' => ['specialist', 'administrator', 'user']
    ]);

    // Router (mismo estilo que los que ya tienes)
    $router->agregarRuta('GET', 'specialist/cards', [
        'controlador' => SpecialistController::class,
        'accion' => 'showCards',
        'roles' => ['specialist', 'administrator', 'user']
    ]);
    $router->agregarRuta('GET', 'specialist/cards/session', [
        'controlador' => SpecialistController::class,
        'accion' => 'showCardBySession',
        'roles' => ['specialist', 'administrator', 'user']
    ]);

    $router->agregarRuta('GET', 'body-compositions/history/{recId}/{type}', ['controlador' => BodyCompositionController::class, 'accion' => 'getHistoryByRecordId', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'body_composition/history/{recId}/{type}', ['controlador' => BodyCompositionController::class, 'accion' => 'getHistoryByRecordId', 'roles' => ['user', 'specialist', 'administrator']]);

    $router->agregarRuta('GET', 'energy_metabolism/history/{recId}/{type}', ['controlador' => EnergyMetabolismController::class, 'accion' => 'getHistoryByRecordId', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'lipid-profile/history/{recId}/{type}', ['controlador' => LipidProfileController::class, 'accion' => 'getHistoryByRecordId', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'lipid_profile_record/history/{recId}/{type}', ['controlador' => LipidProfileController::class, 'accion' => 'getHistoryByRecordId', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'renal-function/history/{recId}/{type}', ['controlador' => RenalFunctionController::class, 'accion' => 'getHistoryByRecordId', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'renal_function/history/{recId}/{type}', ['controlador' => RenalFunctionController::class, 'accion' => 'getHistoryByRecordId', 'roles' => ['user', 'specialist', 'administrator']]);

    // Rutas de Biomarcadores
    $router->agregarRuta('GET', 'biomarkers/all', ['controlador' => BiomarkerController::class, 'accion' => 'showAll', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarker/export/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'exportBiomarkers', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarkers/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'showById', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('POST', 'biomarkers/most-frequent', ['controlador' => BiomarkerController::class, 'accion' => 'getMostFrequentBiomarker', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('POST', 'biomarkers/most-frequent-global', [
        'controlador' => BiomarkerController::class,
        'accion' => 'getMostFrequentBiomarkerGlobal',
        'roles' => ['user', 'specialist', 'administrator']
    ]);
    $router->agregarRuta('GET', 'biomarkers/users-status/{id}', ['controlador' => 'BiomarkerController', 'accion' => 'getAllUsersBiomarkersWithStatus', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarkers/alerts_details_all/{id}', ['controlador' => 'BiomarkerController', 'accion' => 'getAllUsersAlertBiomarkerDetails', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarkers/out-of-range/{id}', ['controlador' => 'BiomarkerController', 'accion' => 'countAllUsersOutOfRange', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarkers/out-streak/{id}', ['controlador' => 'BiomarkerController', 'accion' => 'countUsersBiomarkersOutOfRange', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarkers/in-range-percentage/{id}', ['controlador' => 'BiomarkerController', 'accion' => 'getUsersBiomarkersInRangePercentage', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('POST', 'biomarkers/avg-out-range', ['controlador' => 'BiomarkerController', 'accion' => 'getUsersBiomarkerAvgAndOutRange', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarkers/today-count/{id}', ['controlador' => 'BiomarkerController', 'accion' => 'countTodayRecords', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarkers/today-count/{minDate}/{maxDate}', ['controlador' => 'BiomarkerController', 'accion' => 'countTodayRecords', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarkers/user_sex/{id_user}', ['controlador' => 'BiomarkerController', 'accion' => 'getUserBiomarkers', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarkers/resumen/{id}', ['controlador' => 'BiomarkerController', 'accion' => 'getBiomarkerResumen', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarkers/resumen-status/{id}', ['controlador' => 'BiomarkerController', 'accion' => 'getBiomarkerResumenWithStatus', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarkers/alerts_details/{id}', ['controlador' => 'BiomarkerController', 'accion' => 'getAlertBiomarkerDetailsByUser', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarkers/valid-count/{id}', ['controlador' => 'BiomarkerController', 'accion' => 'countUserValidBiomarkers', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarkers/user-valid-values/{id}', ['controlador' => 'BiomarkerController', 'accion' => 'countUserValidBiomarkerValuesInRange', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarkers/in-out-range/{id}', ['controlador' => 'BiomarkerController', 'accion' => 'countUserInOutRange', 'roles' => ['user', 'specialist', 'administrator']]);

    $router->agregarRuta('POST', 'biomarkers/in-out-range-percentage', ['controlador' => 'BiomarkerController', 'accion' => 'getUsersInOutRangePercentage', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarker_value/{panel}/{test}', ['controlador' => BiomarkerController::class, 'accion' => 'getBiomarkerValuesByPanelTest', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('GET', 'users/count/{id}', ['controlador' => UserController::class, 'accion' => 'countUsers', 'roles' => ['specialist', 'administrator']]);

    $router->agregarRuta('POST', 'biomarkers/info', [
        'controlador' => BiomarkerController::class,
        'accion' => 'getBiomarkersInfo',
        'roles' => ['user', 'specialist', 'administrator']
    ]);


    // Rutas de Auditoria de Sessiones
    // Obtener todas las sesiones (admin, specialist, etc.)
    $router->agregarRuta('GET', 'session-audit', [
        'controlador' => SessionManagementController::class,
        'accion' => 'showAll',
        'roles' => ['administrator']
    ]);

    // Obtener una sesión específica por ID
    $router->agregarRuta('GET', 'session-audit/{id}', [
        'controlador' => SessionManagementController::class,
        'accion' => 'showById',
        'roles' => ['administrator']
    ]);

    // Crear registro de sesión (desde login de cualquier tipo de usuario)
    $router->agregarRuta('POST', 'session-audit', [
        'controlador' => SessionManagementController::class,
        'accion' => 'create',
        'roles' => ['user', 'specialist', 'administrator']
    ]);
    $router->agregarRuta('GET', 'session-audit/export/{id}', [
        'controlador' => SessionManagementController::class,
        'accion' => 'export',
        'roles' => ['administrator']
    ]);
    $router->agregarRuta('POST', 'auth/check-user-image', [
        'controlador' => AuthController::class,
        'accion' => 'checkUserImage',
        'roles' => ['user', 'specialist', 'administrator']
    ]);

    $router->agregarRuta('POST', 'test-documents', ['controlador' => TestDocumentsController::class, 'accion' => 'create', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('POST', 'test-documents/{id}', ['controlador' => TestDocumentsController::class, 'accion' => 'update', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('DELETE', 'test-documents/{id}', ['controlador' => TestDocumentsController::class, 'accion' => 'delete', 'roles' => ['user', 'specialist', 'administrator']]);


    $router->agregarRuta('POST', 'session-status', ['controlador' => SessionManagementController::class, 'accion' => 'checkStatus', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('POST', 'session-audit/kick/{id}', ['controlador' => SessionManagementController::class, 'accion' => 'kick', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta(
        'POST',
        'session-audit/store-status',
        [
            'controlador' => SessionManagementController::class,
            'accion' => 'storeStatus',
            'roles' => ['user', 'specialist', 'administrator']
        ]
    );


    $router->agregarRuta('GET', 'session-config', [
        'controlador' => SessionConfigController::class,
        'accion' => 'show',
        'roles' => ['user', 'specialist', 'administrator']
    ]);

    $router->agregarRuta('POST', 'session-config', [
        'controlador' => SessionConfigController::class,
        'accion' => 'update',
        'roles' => ['administrator']
    ]);



    // Rutas de Comentarios
    // $router->agregarRuta('POST', 'biomarker-comments', ['controlador' => CommentBiomarkerController::class, 'accion' => 'createComment', 'roles' => ['specialist', 'administrator']]);
    // $router->agregarRuta('POST', 'biomarker-comments/{id}', ['controlador' => CommentBiomarkerController::class, 'accion' => 'updateComment', 'roles' => ['user', 'specialist', 'administrator']]);
    // $router->agregarRuta('DELETE', 'biomarker-comments/{id}', ['controlador' => CommentBiomarkerController::class, 'accion' => 'deleteComment', 'roles' => ['specialist', 'administrator']]);
    // $router->agregarRuta('GET', 'biomarker-comments/{panel}/{test}', ['controlador' => CommentBiomarkerController::class, 'accion' => 'showCommentsByPanelAndTest', 'roles' => ['user', 'specialist', 'administrator']]);
    // $router->agregarRuta('GET', 'biomarker-comment/{id}', ['controlador' => CommentBiomarkerController::class, 'accion' => 'showCommentById', 'roles' => ['specialist', 'administrator']]);

    // Rutas de Notificaciones
    $router->agregarRuta('GET', 'notifications', ['controlador' => NotificationController::class, 'accion' => 'showAll', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'notifications/{id}', ['controlador' => NotificationController::class, 'accion' => 'showById', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'notifications/all/{id}', ['controlador' => NotificationController::class, 'accion' => 'showAllAdmin', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'notifications/user-count/{id}', ['controlador' => NotificationController::class, 'accion' => 'countAlertsUser', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('POST', 'notifications', ['controlador' => NotificationController::class, 'accion' => 'create', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('PUT', 'notifications/{id}', ['controlador' => NotificationController::class, 'accion' => 'update', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('DELETE', 'notifications/{id}', ['controlador' => NotificationController::class, 'accion' => 'delete', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'notifications/by-user/{id}', ['controlador' => NotificationController::class, 'accion' => 'showByUserId', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('POST', 'notifications/update-new', ['controlador' => NotificationController::class, 'accion' => 'updateNew', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'notifications/active-by-user/{id}', ['controlador' => NotificationController::class, 'accion' => 'showActiveAlertsByUserId', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'notifications/dismissed-by-user/{id}', ['controlador' => NotificationController::class, 'accion' => 'showDismissedAlertsByUserId', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'notifications/all-by-user/{id}', ['controlador' => NotificationController::class, 'accion' => 'showAllAlertsByUserId', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'notifications/all-by-user-no-limit/{id}', ['controlador' => NotificationController::class, 'accion' => 'showByUserIdNotifications', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'notifications/by-user-view-all/{id}', ['controlador' => NotificationController::class, 'accion' => 'showByUserIdViewAll', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'notifications/by-status/{status}', ['controlador' => NotificationController::class, 'accion' => 'showByStatus', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'notifications/active-alerts', ['controlador' => NotificationController::class, 'accion' => 'showActiveAlerts', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'notifications/by-biomarker/{id_biomarker}', ['controlador' => NotificationController::class, 'accion' => 'showByBiomarkerId', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'notifications/by-user-biomarker/{id_biomarker}', ['controlador' => NotificationController::class, 'accion' => 'showByBiomarkerAndUser', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('POST', 'notifications/no-alert-user', ['controlador' => NotificationController::class, 'accion' => 'updateNoAlertUser', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('POST', 'notifications/no-alert-admin', ['controlador' => NotificationController::class, 'accion' => 'updateNoAlertAdmin', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('POST', 'notifications/no-alert-user-all', ['controlador' => NotificationController::class, 'accion' => 'updateNoAlertUserByUserId', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('POST', 'notifications/no-alert-admin-all', ['controlador' => NotificationController::class, 'accion' => 'updateNoAlertAdminAll', 'roles' => ['user', 'specialist', 'administrator']]);
});




// --- RUTAS DE API (Protegidas) ---
$router->group(['prefix' => ''], function ($router) {

    // Auth & General
    $router->get('/logout', ['controlador' => AuthController::class, 'accion' => 'logout']);
    $router->post('/auth/check-user-image', ['controlador' => AuthController::class, 'accion' => 'checkUserImage']);
    $router->get('/set-language', ['controlador' => UserController::class, 'accion' => 'setLanguage']);

    // User
    $router->get('/users', ['controlador' => UserController::class, 'accion' => 'showAll']);
    $router->get('/users/{id}', ['controlador' => UserController::class, 'accion' => 'showById']);
    $router->get('/users/session/{id}', ['controlador' => UserController::class, 'accion' => 'getSessionUserData']);
    $router->get('/users/count/{id}', ['controlador' => UserController::class, 'accion' => 'countUsers', 'roles' => ['specialist', 'administrator']]);
    $router->post('/users', ['controlador' => UserController::class, 'accion' => 'create', 'roles' => ['administrator']]);
    $router->post('/users/{id}', ['controlador' => UserController::class, 'accion' => 'update', 'roles' => ['administrator']]);
    $router->delete('/users/{id}', ['controlador' => UserController::class, 'accion' => 'delete', 'roles' => ['administrator']]);
    $router->post('/user/update-status/{id}', ['controlador' => UserController::class, 'accion' => 'updateStatus', 'roles' => ['administrator']]);
    $router->post('/users_profile/{id}', ['controlador' => UserController::class, 'accion' => 'update_profile', 'roles' => ['user']]);
    $router->post('/user/system_type/update/{id}', ['controlador' => UserController::class, 'accion' => 'update_system_type_session_user', 'roles' => ['user']]);

    // Specialist
    $router->get('/specialist_get', ['controlador' => SpecialistController::class, 'accion' => 'showAll', 'roles' => ['specialist', 'administrator']]);

    // OBTENER SPECIALISTA PARA SEGUNDA OPINIÓN
    $router->get('/specialist-second-opinion/{id}', ['controlador' => SpecialistController::class, 'accion' => 'showByIdSecondOpinion', 'roles' => ['specialist', 'administrator']]);

    $router->get('/second-opinion-slots', [
        'controlador' => SpecialistAvailabilityController::class,
        'accion' => 'getAvailableSlots',
        'roles' => ['user', 'specialist', 'administrator'] // Abierto a usuarios para que puedan ver disponibilidad
    ]);


    $router->get('/second-opinion-specialist/{id}', [
        'controlador' => SpecialistController::class,
        'accion' => 'showByIdWithFreeCheck',
        'roles' => ['specialist']
    ]);



    $router->post('/specialist/check-email', ['controlador' => SpecialistController::class, 'accion' => 'checkEmailSpecialist']);
    $router->post('/specialist/check-telephone', ['controlador' => SpecialistController::class, 'accion' => 'checkTelephoneSpecialist']);


    $router->get('/specialist_get/{id}', ['controlador' => SpecialistController::class, 'accion' => 'showById', 'roles' => ['specialist', 'administrator']]);
    $router->get('/specialist/session/{id}', ['controlador' => SpecialistController::class, 'accion' => 'getSessionUserData', 'roles' => ['specialist']]);
    $router->post('/specialist/create', ['controlador' => SpecialistController::class, 'accion' => 'create', 'roles' => ['administrator']]);
    $router->post('/specialist/update/{id}', ['controlador' => SpecialistController::class, 'accion' => 'update', 'roles' => ['administrator']]);
    $router->post('/specialist/update-status/{id}', ['controlador' => SpecialistController::class, 'accion' => 'updateStatus', 'roles' => ['administrator']]);
    $router->delete('/specialist/{id}', ['controlador' => SpecialistController::class, 'accion' => 'delete', 'roles' => ['administrator']]);
    $router->post('/specialist/update-profile/{id}', ['controlador' => SpecialistController::class, 'accion' => 'updateProfile', 'roles' => ['specialist']]);
    $router->post('/specialist/system_type/update/{id}', ['controlador' => SpecialistController::class, 'accion' => 'update_system_type_session_user', 'roles' => ['specialist']]);

    // Administrator
    $router->get('/administrator_get', ['controlador' => AdministratorController::class, 'accion' => 'showAll', 'roles' => ['administrator']]);
    $router->get('/administrator_get/{id}', ['controlador' => AdministratorController::class, 'accion' => 'showById', 'roles' => ['administrator']]);
    $router->get('/administrator/session/{id}', ['controlador' => AdministratorController::class, 'accion' => 'getSessionUserData', 'roles' => ['administrator']]);
    $router->post('/administrator/create', ['controlador' => AdministratorController::class, 'accion' => 'create', 'roles' => ['administrator']]);
    $router->post('/administrator/update/{id}', ['controlador' => AdministratorController::class, 'accion' => 'update', 'roles' => ['administrator']]);
    $router->post('/administrator/update-status/{id}', ['controlador' => AdministratorController::class, 'accion' => 'updateStatus', 'roles' => ['administrator']]);
    $router->post('/administrator/update-profile/{id}', ['controlador' => AdministratorController::class, 'accion' => 'update_profile', 'roles' => ['administrator']]);
    $router->post('/administrator/system_type/update/{id}', ['controlador' => AdministratorController::class, 'accion' => 'update_system_type_session_user', 'roles' => ['administrator']]);
    $router->delete('/administrator/{id}', ['controlador' => AdministratorController::class, 'accion' => 'delete', 'roles' => ['administrator']]);

    // Body Composition
    $router->get('/body-compositions', ['controlador' => BodyCompositionController::class, 'accion' => 'getAllByUserId', 'roles' => ['user']]);
    $router->get('/body-compositions/{id}', ['controlador' => BodyCompositionController::class, 'accion' => 'getBodyCompositionData']);
    $router->get('/body-compositions/user/{user_id}', ['controlador' => BodyCompositionController::class, 'accion' => 'showByIdUser', 'roles' => ['user']]);
    $router->get('/body-compositions/history/{recId}/{type}', ['controlador' => BodyCompositionController::class, 'accion' => 'getHistoryByRecordId']);
    $router->post('/body-compositions', ['controlador' => BodyCompositionController::class, 'accion' => 'create', 'roles' => ['user']]);
    $router->put('/body-compositions/{id}', ['controlador' => BodyCompositionController::class, 'accion' => 'update', 'roles' => ['user']]);
    $router->delete('/body-compositions/{id}', ['controlador' => BodyCompositionController::class, 'accion' => 'delete', 'roles' => ['user']]);
    $router->get('/body-compositions/export/{id}', ['controlador' => BodyCompositionController::class, 'accion' => 'exportCSVByUserId', 'roles' => ['user']]);
    $router->post('/body-compositions/no-alert-user', ['controlador' => BodyCompositionController::class, 'accion' => 'updateNoAlertUser', 'roles' => ['user']]);
    $router->post('/body-compositions/no-alert-admin', ['controlador' => BodyCompositionController::class, 'accion' => 'updateNoAlertAdmin', 'roles' => ['user']]);
    $router->post('/body-compositions/no-alert-user-by-user', ['controlador' => BodyCompositionController::class, 'accion' => 'updateNoAlertUserByUser', 'roles' => ['user']]);
    $router->post('/body-compositions/no-alert-admin-by-user', ['controlador' => BodyCompositionController::class, 'accion' => 'updateNoAlertAdminByUser', 'roles' => ['user']]);

    // Energy Metabolism
    $router->get('/energy_metabolism', ['controlador' => EnergyMetabolismController::class, 'accion' => 'showAll', 'roles' => ['user']]);
    $router->get('/energy_metabolism/{id}', ['controlador' => EnergyMetabolismController::class, 'accion' => 'showById']);
    $router->get('/energy_metabolism/user/{user_id}', ['controlador' => EnergyMetabolismController::class, 'accion' => 'showByIdUser', 'roles' => ['user']]);
    $router->get('/energy_metabolism/history/{recId}/{type}', ['controlador' => EnergyMetabolismController::class, 'accion' => 'getHistoryByRecordId']);
    $router->post('/energy_metabolism', ['controlador' => EnergyMetabolismController::class, 'accion' => 'create', 'roles' => ['user']]);
    $router->put('/energy_metabolism/{id}', ['controlador' => EnergyMetabolismController::class, 'accion' => 'update', 'roles' => ['user']]);
    $router->delete('/energy_metabolism/{id}', ['controlador' => EnergyMetabolismController::class, 'accion' => 'delete', 'roles' => ['user']]);
    $router->get('/energy_metabolism/export/{id}', ['controlador' => EnergyMetabolismController::class, 'accion' => 'exportCSVByUserId', 'roles' => ['user']]);
    $router->post('/energy_metabolism/no-alert-user', ['controlador' => EnergyMetabolismController::class, 'accion' => 'updateNoAlertUser', 'roles' => ['user']]);
    $router->post('/energy_metabolism/no-alert-admin', ['controlador' => EnergyMetabolismController::class, 'accion' => 'updateNoAlertAdmin', 'roles' => ['user']]);
    $router->post('/energy_metabolism/no-alert-user-by-user', ['controlador' => EnergyMetabolismController::class, 'accion' => 'updateNoAlertUserByUser', 'roles' => ['user']]);
    $router->post('/energy_metabolism/no-alert-admin-by-user', ['controlador' => EnergyMetabolismController::class, 'accion' => 'updateNoAlertAdminByUser', 'roles' => ['user']]);

    // Lipid Profile
    $router->get('/lipid-profile', ['controlador' => LipidProfileController::class, 'accion' => 'getAllByUser', 'roles' => ['user']]);
    $router->get('/lipid-profile/{record_id}', ['controlador' => LipidProfileController::class, 'accion' => 'getRecord']);
    $router->get('/lipid-profile/user/{user_id}', ['controlador' => LipidProfileController::class, 'accion' => 'getRecordUser', 'roles' => ['user']]);
    $router->get('/lipid-profile/history/{recId}/{type}', ['controlador' => LipidProfileController::class, 'accion' => 'getHistoryByRecordId']);
    $router->post('/lipid-profile', ['controlador' => LipidProfileController::class, 'accion' => 'create', 'roles' => ['user']]);
    $router->put('/lipid-profile/{id}', ['controlador' => LipidProfileController::class, 'accion' => 'update', 'roles' => ['user']]);
    $router->delete('/lipid-profile/{id}', ['controlador' => LipidProfileController::class, 'accion' => 'delete', 'roles' => ['user']]);
    $router->get('/lipid-profile/export/{id}', ['controlador' => LipidProfileController::class, 'accion' => 'exportCSVByUserId', 'roles' => ['user']]);
    $router->post('/lipid-profile/no-alert-user', ['controlador' => LipidProfileController::class, 'accion' => 'updateNoAlertUser', 'roles' => ['user']]);
    $router->post('/lipid-profile/no-alert-admin', ['controlador' => LipidProfileController::class, 'accion' => 'updateNoAlertAdmin', 'roles' => ['user']]);
    $router->post('/lipid-profile/no-alert-user-by-user', ['controlador' => LipidProfileController::class, 'accion' => 'updateNoAlertUserByUser', 'roles' => ['user']]);
    $router->post('/lipid-profile/no-alert-admin-by-user', ['controlador' => LipidProfileController::class, 'accion' => 'updateNoAlertAdminByUser', 'roles' => ['user']]);

    // Renal Function
    $router->get('/renal-function', ['controlador' => RenalFunctionController::class, 'accion' => 'showAll', 'roles' => ['user']]);
    $router->get('/renal-function/{id}', ['controlador' => RenalFunctionController::class, 'accion' => 'showById']);
    $router->get('/renal-function/user/{user_id}', ['controlador' => RenalFunctionController::class, 'accion' => 'showByIdUser', 'roles' => ['user']]);
    $router->get('/renal-function/history/{recId}/{type}', ['controlador' => RenalFunctionController::class, 'accion' => 'getHistoryByRecordId']);
    $router->post('/renal-function', ['controlador' => RenalFunctionController::class, 'accion' => 'create', 'roles' => ['user']]);
    $router->put('/renal-function/{id}', ['controlador' => RenalFunctionController::class, 'accion' => 'update', 'roles' => ['user']]);
    $router->delete('/renal-function/{id}', ['controlador' => RenalFunctionController::class, 'accion' => 'delete', 'roles' => ['user']]);
    $router->get('/renal-function/export/{id}', ['controlador' => RenalFunctionController::class, 'accion' => 'exportCSVByUserId', 'roles' => ['user']]);

    // Security Questions
    $router->get('/security-questions', ['controlador' => SecurityQuestionController::class, 'accion' => 'getByUser']);
    $router->get('/security-questions/{id}', ['controlador' => SecurityQuestionController::class, 'accion' => 'getByUserReset']);
    $router->post('/security-questions', ['controlador' => SecurityQuestionController::class, 'accion' => 'create']);
    $router->put('/security-questions/{id}', ['controlador' => SecurityQuestionController::class, 'accion' => 'update']);
    $router->delete('/security-questions/{id}', ['controlador' => SecurityQuestionController::class, 'accion' => 'delete']);

    // Test Documents
    $router->get('/test-documents/{id}', ['controlador' => TestDocumentsController::class, 'accion' => 'showImage']);
    $router->get('/test-documents/panel/{test_panel_id}/test/{test_id}', ['controlador' => TestDocumentsController::class, 'accion' => 'getImagesByPanelAndTest']);
    $router->post('/test-documents', ['controlador' => TestDocumentsController::class, 'accion' => 'create']);
    $router->post('/test-documents/{id}', ['controlador' => TestDocumentsController::class, 'accion' => 'update']);
    $router->delete('/test-documents/{id}', ['controlador' => TestDocumentsController::class, 'accion' => 'delete']);

    // Second Opinion Requests
// ========== STANDARD (no-block) ==========
    $router->post('/second-opinion/requests', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'createStandard',
        'roles' => ['user']
    ]);

    $router->post('/second-opinion/requests/{id}', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'updateStandard', // POST en lugar de PUT
        'roles' => ['user']
    ]);

    $router->get('/second-opinion/requests', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'listStandardForSpecialist',
        'roles' => ['specialist']
    ]);

    $router->get('/second-opinion/requests/{id}', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'getStandardByIdForSpecialist',
        'roles' => ['specialist']
    ]);

    // Exams/data de una solicitud STANDARD
    $router->get('/second-opinion/requests/{id}/exams', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'getStandardRequestData',
        'roles' => ['specialist', 'user']
    ]);
    // Acciones sobre una solicitud (confirm/reject/cancel)
    $router->post('/second-opinion/requests/action', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'action',
        'roles' => ['specialist', 'user']
    ]);

    // ========== BLOCKS ==========
    $router->post('/second-opinion/blocks', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'createBlock',
        'roles' => ['user']
    ]);

    $router->post('/second-opinion/blocks/{id}', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'updateBlock', // POST en lugar de PUT
        'roles' => ['user']
    ]);

    $router->get('/second-opinion/blocks', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'listBlocksForSpecialist',
        'roles' => ['specialist']
    ]);

    $router->get('/second-opinion/blocks/{id}', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'getBlockByIdForSpecialist',
        'roles' => ['specialist']
    ]);

    // ========== DELETE (aplica a ambos tipos) ==========
    $router->delete('/second-opinion/requests/{id}', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'delete',
        'roles' => ['user']
    ]);

    $router->delete('/second-opinion/blocks/{id}', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'delete',
        'roles' => ['user']
    ]);


    $router->post('/second-opinion-requests', ['controlador' => SecondOpinionRequestsController::class, 'accion' => 'createStandard', 'roles' => ['user']]);

    // SOLICITUDES PARA PANEL ESPECIALISTAS

    $router->get('/second-opinion-exams/{id}', ['controlador' => SecondOpinionRequestsController::class, 'accion' => 'getRequestData', 'roles' => ['specialist']]);
    $router->get('/second-opinion-requests', ['controlador' => SecondOpinionRequestsController::class, 'accion' => 'getRequestsForSpecialist', 'roles' => ['specialist']]);

    $router->get('/second-opinion-requests/{id}', ['controlador' => SecondOpinionRequestsController::class, 'accion' => 'getRequestsByIdForSpecialist', 'roles' => ['specialist']]);

    // SOLICITUDES PARA PANEL USUARIOS

    // $router->get('/user-second-opinion-exams/{id}', ['controlador' => SecondOpinionRequestsController::class, 'accion' => 'getRequestData', 'roles' => ['user, specialist']]);
    $router->get('/user-second-opinion-requests', ['controlador' => SecondOpinionRequestsController::class, 'accion' => 'getRequestsForUser', 'roles' => ['user, specialist']]);

    $router->get('/user-second-opinion-requests/{id}', ['controlador' => SecondOpinionRequestsController::class, 'accion' => 'getRequestsByIdForUser', 'roles' => ['user, specialist']]);

    // ====== CAMBIOS DE ESTADO ======
    $router->post('/second-opinion-requests-to-awaiting-payment/{id}', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'setAwaitingPayment',
        'roles' => ['specialist', 'user']
    ]);

    $router->post('/second-opinion-requests-to-upcoming/{id}', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'setUpcoming',
        'roles' => ['specialist', 'user']
    ]);


    $router->post('/second-opinion-requests-to-completed/{id}', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'setCompleted',
        'roles' => ['specialist', 'user']
    ]);

    $router->post('/second-opinion-requests-cancel/{id}', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'setCancelled',
        'roles' => ['specialist', 'user']
    ]);

    $router->post('/second-opinion-requests-reject/{id}', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'setRejected',
        'roles' => ['specialist', 'user']
    ]);



    // Specialist Certifications
    $router->get('/specialist-certifications', ['controlador' => SpecialistCertificationsController::class, 'accion' => 'getAll', 'roles' => ['specialist']]);
    $router->get('/specialist-certifications/{id}', ['controlador' => SpecialistCertificationsController::class, 'accion' => 'getById', 'roles' => ['specialist']]);
    $router->post('/specialist-certifications', ['controlador' => SpecialistCertificationsController::class, 'accion' => 'create', 'roles' => ['specialist']]);
    $router->post('/specialist-certifications/{id}', ['controlador' => SpecialistCertificationsController::class, 'accion' => 'update', 'roles' => ['specialist']]);
    $router->delete('/specialist-certifications/{id}', ['controlador' => SpecialistCertificationsController::class, 'accion' => 'delete', 'roles' => ['specialist']]);
    // Cities
// Cities
    $router->get('/cities', ['controlador' => CitiesController::class, 'accion' => 'getAll', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->get('/cities/all', ['controlador' => CitiesController::class, 'accion' => 'getAllForTable', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->get('/cities/{id}', ['controlador' => CitiesController::class, 'accion' => 'getById', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->post('/cities', ['controlador' => CitiesController::class, 'accion' => 'create', 'roles' => ['administrator']]);
    $router->post('/cities/{id}', ['controlador' => CitiesController::class, 'accion' => 'update', 'roles' => ['administrator']]);
    $router->delete('/cities/{id}', ['controlador' => CitiesController::class, 'accion' => 'delete', 'roles' => ['administrator']]);


    // States
    $router->get('/states', ['controlador' => StatesController::class, 'accion' => 'getAll', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->get('/states/{id}', ['controlador' => StatesController::class, 'accion' => 'getById', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->post('/states', ['controlador' => StatesController::class, 'accion' => 'create', 'roles' => ['administrator']]);
    $router->post('/states/{id}', ['controlador' => StatesController::class, 'accion' => 'update', 'roles' => ['administrator']]);
    $router->delete('/states/{id}', ['controlador' => StatesController::class, 'accion' => 'delete', 'roles' => ['administrator']]);


    $router->post('/contact-emails/check-email', [
        'controlador' => ContactEmailController::class,
        'accion' => 'showByEmail',
        'roles' => ['specialist', 'user', 'administrator']
    ]);

    $router->post('/contact-phones/check-telephone', [
        'controlador' => ContactPhoneController::class,
        'accion' => 'showByTelephone',
        'roles' => ['specialist', 'user', 'administrator']
    ]);

    // Contact Emails
    $router->get('/contact-emails', ['controlador' => ContactEmailController::class, 'accion' => 'getAll', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->get('/contact-emails/{id}', ['controlador' => ContactEmailController::class, 'accion' => 'getById', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->get('/contact-emails/entity/{type}/{id}', ['controlador' => ContactEmailController::class, 'accion' => 'getByEntity', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->get('/contact-emails/email/{email}', ['controlador' => ContactEmailController::class, 'accion' => 'getByEmail', 'roles' => ['specialist', 'administrator', 'user']]);

    $router->post('/contact-emails', ['controlador' => ContactEmailController::class, 'accion' => 'create', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->post('/contact-emails/{id}', ['controlador' => ContactEmailController::class, 'accion' => 'update', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->post('/contact-emails/{id}/set-primary', ['controlador' => ContactEmailController::class, 'accion' => 'setPrimary', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->delete('/contact-emails/{id}', ['controlador' => ContactEmailController::class, 'accion' => 'delete', 'roles' => ['specialist', 'administrator', 'user']]);


    // Contact Phones (mismos roles que contact-emails)
    $router->get('/contact-phones', ['controlador' => ContactPhoneController::class, 'accion' => 'getAll', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->get('/contact-phones/{id}', ['controlador' => ContactPhoneController::class, 'accion' => 'getById', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->get('/contact-phones/entity/{type}/{id}', ['controlador' => ContactPhoneController::class, 'accion' => 'getByEntity', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->get('/contact-phones/telephone/{telephone}', ['controlador' => ContactPhoneController::class, 'accion' => 'getByTelephone', 'roles' => ['specialist', 'administrator', 'user']]);

    $router->post('/contact-phones', ['controlador' => ContactPhoneController::class, 'accion' => 'create', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->post('/contact-phones/{id}', ['controlador' => ContactPhoneController::class, 'accion' => 'update', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->post('/contact-phones/{id}/set-primary', ['controlador' => ContactPhoneController::class, 'accion' => 'setPrimary', 'roles' => ['specialist', 'administrator', 'user']]);
    $router->delete('/contact-phones/{id}', ['controlador' => ContactPhoneController::class, 'accion' => 'delete', 'roles' => ['specialist', 'administrator', 'user']]);



    // Specialist Social Links
    $router->get('/specialist-social-links', ['controlador' => SpecialistSocialLinksController::class, 'accion' => 'getAll', 'roles' => ['specialist']]);
    $router->get('/specialist-social-links/{id}', ['controlador' => SpecialistSocialLinksController::class, 'accion' => 'getById', 'roles' => ['specialist']]);
    $router->post('/specialist-social-links', ['controlador' => SpecialistSocialLinksController::class, 'accion' => 'create', 'roles' => ['specialist']]);
    $router->post('/specialist-social-links/{id}', ['controlador' => SpecialistSocialLinksController::class, 'accion' => 'update', 'roles' => ['specialist']]);
    $router->delete('/specialist-social-links/{id}', ['controlador' => SpecialistSocialLinksController::class, 'accion' => 'delete', 'roles' => ['specialist']]);

    // Este archivo es incluido por index2_corregido.php y asume que
// la variable $router ya está definida dentro del grupo de rutas API.

    // --- CONTINUACIÓN DE RUTAS DE API PROTEGIDAS ---
    // CRUD routes for second_opinion_data
    $router->agregarRuta('GET', 'second_opinion_data', [
        'controlador' => SecondOpinionDataController::class,
        'accion' => 'index',
        'roles' => ['administrator', 'specialist', 'user']
    ]);

    $router->agregarRuta('GET', 'second_opinion_data/{id}', [
        'controlador' => SecondOpinionDataController::class,
        'accion' => 'show',
        'roles' => ['administrator', 'specialist', 'user']
    ]);

    // listado por second_opinion_id
    $router->agregarRuta('GET', 'second_opinion_data/by-request/{second_opinion_id}', [
        'controlador' => SecondOpinionDataController::class,
        'accion' => 'listByRequest',
        'roles' => ['administrator', 'specialist', 'user']
    ]);

    $router->agregarRuta('POST', 'second_opinion_data', [
        'controlador' => SecondOpinionDataController::class,
        'accion' => 'create',
        'roles' => ['administrator', 'specialist', 'user']
    ]);

    $router->agregarRuta('POST', 'second_opinion_data/{id}', [
        'controlador' => SecondOpinionDataController::class,
        'accion' => 'update',
        'roles' => ['administrator', 'specialist', 'user']
    ]);

    $router->agregarRuta('DELETE', 'second_opinion_data/{id}', [
        'controlador' => SecondOpinionDataController::class,
        'accion' => 'delete',
        'roles' => ['administrator', 'specialist', 'user']
    ]);


    // Specialist Locations
    $router->get('/specialist-locations', ['controlador' => SpecialistLocationsController::class, 'accion' => 'getAll', 'roles' => ['specialist']]);
    $router->get('/specialist-locations/{id}', ['controlador' => SpecialistLocationsController::class, 'accion' => 'getById', 'roles' => ['specialist']]);
    $router->post('/specialist-locations', ['controlador' => SpecialistLocationsController::class, 'accion' => 'create', 'roles' => ['specialist']]);
    $router->put('/specialist-locations/{id}', ['controlador' => SpecialistLocationsController::class, 'accion' => 'update', 'roles' => ['specialist']]);
    $router->delete('/specialist-locations/{id}', ['controlador' => SpecialistLocationsController::class, 'accion' => 'delete', 'roles' => ['specialist']]);

    // Specialist Reviews
    $router->get('/specialist-reviews', ['controlador' => SpecialistReviewsController::class, 'accion' => 'getAll', 'roles' => ['specialist']]);
    $router->get('/specialist-reviews/{id}', ['controlador' => SpecialistReviewsController::class, 'accion' => 'getById', 'roles' => ['specialist']]);
    $router->post('/specialist-reviews', ['controlador' => SpecialistReviewsController::class, 'accion' => 'create', 'roles' => ['specialist']]);
    $router->post('/specialist-reviews/{id}', ['controlador' => SpecialistReviewsController::class, 'accion' => 'update', 'roles' => ['specialist']]);
    $router->delete('/specialist-reviews/{id}', ['controlador' => SpecialistReviewsController::class, 'accion' => 'delete', 'roles' => ['specialist']]);

    // Specialist Availability
    $router->get('/specialist-availability', ['controlador' => SpecialistAvailabilityController::class, 'accion' => 'getAll', 'roles' => ['specialist']]);
    $router->get('/specialist-availability/{id}', ['controlador' => SpecialistAvailabilityController::class, 'accion' => 'getById', 'roles' => ['specialist']]);
    $router->post('/specialist-availability', ['controlador' => SpecialistAvailabilityController::class, 'accion' => 'create', 'roles' => ['specialist']]);
    $router->post('/specialist-availability/{id}', ['controlador' => SpecialistAvailabilityController::class, 'accion' => 'update', 'roles' => ['specialist']]);
    $router->delete('/specialist-availability/{id}', ['controlador' => SpecialistAvailabilityController::class, 'accion' => 'delete', 'roles' => ['specialist']]);
    $router->post('/specialist-sync-timezone', ['controlador' => SpecialistAvailabilityController::class, 'accion' => 'syncTimezoneFromSpecialist', 'roles' => ['specialist']]);

    // Specialist Pricing
    $router->get('/specialist-pricing', ['controlador' => SpecialistPricingController::class, 'accion' => 'getAll', 'roles' => ['specialist']]);
    $router->get('/specialist-pricing/{id}', ['controlador' => SpecialistPricingController::class, 'accion' => 'getById', 'roles' => ['specialist']]);
    $router->post('/specialist-pricing', ['controlador' => SpecialistPricingController::class, 'accion' => 'create', 'roles' => ['specialist']]);
    $router->put('/specialist-pricing/{id}', ['controlador' => SpecialistPricingController::class, 'accion' => 'update', 'roles' => ['specialist']]);
    $router->delete('/specialist-pricing/{id}', ['controlador' => SpecialistPricingController::class, 'accion' => 'delete', 'roles' => ['specialist']]);

// Specialist Verification Requests
$router->get('/specialist-verification-requests', [
    'controlador' => SpecialistVerificationRequestsController::class,
    'accion'      => 'getAll',
    'roles'       => ['specialist']
]);

$router->get('/specialist-verification-requests/{id}', [
    'controlador' => SpecialistVerificationRequestsController::class,
    'accion'      => 'getById',
    'roles'       => ['specialist']
]);

$router->get('/specialist-verification-requests/by-specialist/{specialist_id}', [
    'controlador' => SpecialistVerificationRequestsController::class,
    'accion'      => 'getBySpecialist',
    'roles'       => ['specialist']
]);

$router->post('/specialist-verification-requests', [
    'controlador' => SpecialistVerificationRequestsController::class,
    'accion'      => 'create',
    'roles'       => ['specialist']
]);

$router->put('/specialist-verification-requests/{id}', [
    'controlador' => SpecialistVerificationRequestsController::class,
    'accion'      => 'update',
    'roles'       => ['specialist']
]);

$router->put('/specialist-verification-requests/{id}/approve', [
    'controlador' => SpecialistVerificationRequestsController::class,
    'accion'      => 'approve',
    'roles'       => ['specialist']
]);

$router->put('/specialist-verification-requests/{id}/reject', [
    'controlador' => SpecialistVerificationRequestsController::class,
    'accion'      => 'reject',
    'roles'       => ['specialist']
]);

$router->delete('/specialist-verification-requests/{id}', [
    'controlador' => SpecialistVerificationRequestsController::class,
    'accion'      => 'delete',
    'roles'       => ['specialist']
]);

    // Backups
    $router->get('/backups', ['controlador' => BackupController::class, 'accion' => 'show', 'roles' => ['administrator']]);
    $router->get('/backups/{id}', ['controlador' => BackupController::class, 'accion' => 'showId', 'roles' => ['administrator']]);
    $router->post('/backups', ['controlador' => BackupController::class, 'accion' => 'create', 'roles' => ['administrator']]);
    $router->put('/backups/{id}', ['controlador' => BackupController::class, 'accion' => 'update', 'roles' => ['administrator']]);
    $router->delete('/backups/{id}', ['controlador' => BackupController::class, 'accion' => 'delete', 'roles' => ['administrator']]);
    $router->post('/backups/{id}/restore', ['controlador' => BackupController::class, 'accion' => 'restore', 'roles' => ['administrator']]);

    // AuditLog
    $router->get('/auditlog', ['controlador' => AuditLogController::class, 'accion' => 'getAll', 'roles' => ['administrator']]);
    $router->get('/auditlog/{id}', ['controlador' => AuditLogController::class, 'accion' => 'getById', 'roles' => ['administrator']]);
    $router->get('/auditlog/export/{id}', ['controlador' => AuditLogController::class, 'accion' => 'exportCSV', 'roles' => ['administrator']]);

    // Test Panels
    $router->get('/test-panels', ['controlador' => TestPanelController::class, 'accion' => 'getAll', 'roles' => ['administrator']]);
    $router->get('/test-panels/{id}', ['controlador' => TestPanelController::class, 'accion' => 'getById', 'roles' => ['administrator']]);
    $router->get('/test-panels/user-records/{user_id}', ['controlador' => TestPanelController::class, 'accion' => 'getUserRecordCounts']);
    $router->get('/test-panels/{user_id}/{panel_id}', ['controlador' => TestPanelController::class, 'accion' => 'getUserPanelRecords']);
    $router->post('/test-panels', ['controlador' => TestPanelController::class, 'accion' => 'create', 'roles' => ['administrator']]);
    $router->put('/test-panels/{id}', ['controlador' => TestPanelController::class, 'accion' => 'update', 'roles' => ['administrator']]);
    $router->delete('/test-panels/{id}', ['controlador' => TestPanelController::class, 'accion' => 'delete', 'roles' => ['administrator']]);
    $router->get('/test-panels/export/{id}', ['controlador' => TestPanelController::class, 'accion' => 'exportCSVPanels', 'roles' => ['administrator']]);

    // Additional routes for second test panel
    $router->get('/second-opinion-test-panels', ['controlador' => TestPanelController::class, 'accion' => 'getAllUserPanelsBiomarkerRecords', 'roles' => ['user', 'specialist', 'administrator']]);

    $router->get('/test-panels/second/records/{panel_id}', ['controlador' => TestPanelController::class, 'accion' => 'getPanelRecords', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->get('/test-panels/second/biomarkers/{panel_id}', ['controlador' => TestPanelController::class, 'accion' => 'getPanelBiomarkers', 'roles' => ['user', 'specialist', 'administrator']]);

    // Titles
    $router->get('/titles/{id}', ['controlador' => TitleController::class, 'accion' => 'showById', 'roles' => ['administrator']]);
    $router->post('/titles', ['controlador' => TitleController::class, 'accion' => 'create', 'roles' => ['administrator']]);
    $router->put('/titles/{id}', ['controlador' => TitleController::class, 'accion' => 'update', 'roles' => ['administrator']]);
    $router->delete('/titles/{id}', ['controlador' => TitleController::class, 'accion' => 'delete', 'roles' => ['administrator']]);
    $router->get('/titles/export/{id}', ['controlador' => TitleController::class, 'accion' => 'export', 'roles' => ['administrator']]);
    $router->get('/tools/replace-ids', ['controlador' => TitleController::class, 'accion' => 'replaceAllIds', 'roles' => ['administrator']]);

    // Specialties
    $router->get('/specialties/{id}', ['controlador' => SpecialtyController::class, 'accion' => 'showById', 'roles' => ['administrator']]);
    $router->post('/specialties', ['controlador' => SpecialtyController::class, 'accion' => 'create', 'roles' => ['administrator']]);
    $router->put('/specialties/{id}', ['controlador' => SpecialtyController::class, 'accion' => 'update', 'roles' => ['administrator']]);
    $router->delete('/specialties/{id}', ['controlador' => SpecialtyController::class, 'accion' => 'delete', 'roles' => ['administrator']]);
    $router->get('/specialties/export/{id}', ['controlador' => SpecialtyController::class, 'accion' => 'export', 'roles' => ['administrator']]);

    // Countries
    $router->get('/countries', ['controlador' => CountryController::class, 'accion' => 'showAll', 'roles' => ['administrator']]);
    $router->get('/countries/{id}', ['controlador' => CountryController::class, 'accion' => 'showById', 'roles' => ['administrator']]);
    $router->post('/countries', ['controlador' => CountryController::class, 'accion' => 'create', 'roles' => ['administrator']]);
    $router->put('/countries/{id}', ['controlador' => CountryController::class, 'accion' => 'update', 'roles' => ['administrator']]);
    $router->delete('/countries/{id}', ['controlador' => CountryController::class, 'accion' => 'delete', 'roles' => ['administrator']]);
    $router->get('/countries/export/{id}', ['controlador' => CountryController::class, 'accion' => 'export', 'roles' => ['administrator']]);

    // Biomarkers
    $router->get('/biomarkers/all', ['controlador' => BiomarkerController::class, 'accion' => 'showAll', 'roles' => ['specialist', 'administrator']]);
    $router->get('/biomarkers/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'showById', 'roles' => ['specialist', 'administrator']]);
    $router->post('/biomarkers', ['controlador' => BiomarkerController::class, 'accion' => 'create', 'roles' => ['administrator']]);
    $router->put('/biomarkers/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'update', 'roles' => ['administrator']]);
    $router->put('/biomarkers/es/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'update_es', 'roles' => ['administrator']]);
    $router->delete('/biomarkers/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'delete', 'roles' => ['administrator']]);
    $router->get('/biomarkers/export/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'exportBiomarkers', 'roles' => ['administrator']]);
    $router->post('/biomarkers/in-out-range-percentage_user', ['controlador' => BiomarkerController::class, 'accion' => 'getUserInOutRangePercentageByBiomarker', 'roles' => ['user']]);
    $router->get('/biomarker/export/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'exportBiomarkers', 'roles' => ['specialist', 'administrator']]);
    $router->post('/biomarkers/most-frequent', ['controlador' => BiomarkerController::class, 'accion' => 'getMostFrequentBiomarker']);
    $router->post('/biomarkers/most-frequent-global', ['controlador' => BiomarkerController::class, 'accion' => 'getMostFrequentBiomarkerGlobal']);
    $router->get('/biomarkers/users-status/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'getAllUsersBiomarkersWithStatus', 'roles' => ['specialist', 'administrator']]);
    $router->get('/biomarkers/alerts_details_all/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'getAllUsersAlertBiomarkerDetails', 'roles' => ['specialist', 'administrator']]);
    $router->get('/biomarkers/out-of-range/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'countAllUsersOutOfRange', 'roles' => ['specialist', 'administrator']]);
    $router->get('/biomarkers/out-streak/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'countUsersBiomarkersOutOfRange', 'roles' => ['specialist', 'administrator']]);
    $router->get('/biomarkers/in-range-percentage/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'getUsersBiomarkersInRangePercentage', 'roles' => ['specialist', 'administrator']]);
    $router->post('/biomarkers/avg-out-range', ['controlador' => BiomarkerController::class, 'accion' => 'getUsersBiomarkerAvgAndOutRange', 'roles' => ['specialist', 'administrator']]);
    $router->get('/biomarkers/today-count/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'countTodayRecords', 'roles' => ['specialist', 'administrator']]);
    $router->get('/biomarkers/today-count/{minDate}/{maxDate}', ['controlador' => BiomarkerController::class, 'accion' => 'countTodayRecords', 'roles' => ['specialist', 'administrator']]);
    $router->get('/biomarkers/user_sex/{id_user}', ['controlador' => BiomarkerController::class, 'accion' => 'getUserBiomarkers']);
    $router->get('/biomarkers/resumen/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'getBiomarkerResumen']);
    $router->get('/biomarkers/resumen-status/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'getBiomarkerResumenWithStatus']);
    $router->get('/biomarkers/alerts_details/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'getAlertBiomarkerDetailsByUser']);
    $router->get('/biomarkers/valid-count/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'countUserValidBiomarkers']);
    $router->get('/biomarkers/user-valid-values/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'countUserValidBiomarkerValuesInRange']);
    $router->get('/biomarkers/in-out-range/{id}', ['controlador' => BiomarkerController::class, 'accion' => 'countUserInOutRange']);
    $router->get('/biomarkers/filtered/{id_biomarker}/{minDate}/{maxDate}/{tipo}', ['controlador' => BiomarkerController::class, 'accion' => 'getFilteredBiomarkerRecords']);
    $router->post('/biomarkers/in-out-range-percentage', ['controlador' => BiomarkerController::class, 'accion' => 'getUsersInOutRangePercentage']);
    $router->get('/biomarker_value/{panel}/{test}', ['controlador' => BiomarkerController::class, 'accion' => 'getBiomarkerValuesByPanelTest', 'roles' => ['specialist', 'administrator']]);
    $router->post('/biomarkers/info', ['controlador' => BiomarkerController::class, 'accion' => 'getBiomarkersInfo']);

    // Video Calls
    $router->get('/video-calls', ['controlador' => VideoCallsController::class, 'accion' => 'getAll', 'roles' => ['administrator']]);
    $router->get('/video-calls/{id}', ['controlador' => VideoCallsController::class, 'accion' => 'getById', 'roles' => ['administrator']]);
    $router->post('/video-calls', ['controlador' => VideoCallsController::class, 'accion' => 'create', 'roles' => ['administrator']]);
    $router->put('/video-calls/{id}', ['controlador' => VideoCallsController::class, 'accion' => 'update', 'roles' => ['administrator']]);
    $router->delete('/video-calls/{id}', ['controlador' => VideoCallsController::class, 'accion' => 'delete', 'roles' => ['administrator']]);

    // Transactions
    $router->get('/transactions', ['controlador' => TransactionsController::class, 'accion' => 'getAll', 'roles' => ['administrator']]);
    $router->get('/transactions/{id}', ['controlador' => TransactionsController::class, 'accion' => 'getById', 'roles' => ['administrator']]);
    $router->post('/transactions', ['controlador' => TransactionsController::class, 'accion' => 'create', 'roles' => ['administrator']]);
    $router->put('/transactions/{id}', ['controlador' => TransactionsController::class, 'accion' => 'update', 'roles' => ['administrator']]);
    $router->delete('/transactions/{id}', ['controlador' => TransactionsController::class, 'accion' => 'delete', 'roles' => ['administrator']]);

    // Admin Commissions
    $router->get('/admin-commissions', ['controlador' => AdminCommissionsController::class, 'accion' => 'getAll', 'roles' => ['administrator']]);
    $router->get('/admin-commissions/{id}', ['controlador' => AdminCommissionsController::class, 'accion' => 'getById', 'roles' => ['administrator']]);
    $router->post('/admin-commissions', ['controlador' => AdminCommissionsController::class, 'accion' => 'create', 'roles' => ['administrator']]);
    $router->delete('/admin-commissions/{id}', ['controlador' => AdminCommissionsController::class, 'accion' => 'delete', 'roles' => ['administrator']]);

    // Session Management
    $router->get('/session-audit', ['controlador' => SessionManagementController::class, 'accion' => 'showAll', 'roles' => ['administrator']]);
    $router->get('/session-audit/{id}', ['controlador' => SessionManagementController::class, 'accion' => 'showById', 'roles' => ['administrator']]);
    $router->post('/session-audit', ['controlador' => SessionManagementController::class, 'accion' => 'create']);
    $router->get('/session-audit/export/{id}', ['controlador' => SessionManagementController::class, 'accion' => 'export', 'roles' => ['administrator']]);
    $router->post('/session-status', ['controlador' => SessionManagementController::class, 'accion' => 'checkStatus']);
    $router->post('/session-audit/kick/{id}', ['controlador' => SessionManagementController::class, 'accion' => 'kick']);
    $router->post('/session-audit/store-status', ['controlador' => SessionManagementController::class, 'accion' => 'storeStatus']);

    // Session Config
    $router->get('/session-config', ['controlador' => SessionConfigController::class, 'accion' => 'show']);
    $router->post('/session-config', ['controlador' => SessionConfigController::class, 'accion' => 'update']);

    // Comment Biomarker
    /* ============================================================
     * BIOMARKER COMMENTS ROUTES
     * ============================================================ */

    // Listar comentarios por panel y test (para usuario, especialista o admin)
    $router->get('/biomarker-comments/{panel}/{test}', [
        'controlador' => CommentBiomarkerController::class,
        'accion' => 'showCommentsByPanelAndTest',
        'roles' => ['user', 'specialist', 'administrator']
    ]);

    $router->get('/biomarker-comments/with-specialist/{panel}/{test}', [
        'controlador' => CommentBiomarkerController::class,
        'accion' => 'showCommentsByPanelAndTestWithSpecialist',
        'roles' => ['user', 'specialist', 'administrator'] // O los roles que necesites
    ]);

    // Obtener comentario específico por ID
    $router->get('/biomarker-comment/{id}', [
        'controlador' => CommentBiomarkerController::class,
        'accion' => 'showCommentById',
        'roles' => ['specialist', 'administrator']
    ]);

    // Listar todos los comentarios hechos por un especialista
    $router->get('/biomarker-comments/specialist/{id_specialist}', [
        'controlador' => CommentBiomarkerController::class,
        'accion' => 'showCommentsBySpecialist',
        'roles' => ['specialist', 'administrator']
    ]);

    // Crear nuevo comentario (solo especialista o admin)
    $router->post('/biomarker-comments', [
        'controlador' => CommentBiomarkerController::class,
        'accion' => 'createComment',
        'roles' => ['specialist', 'administrator']
    ]);

    // Actualizar comentario existente (solo especialista o admin)
    $router->post('/biomarker-comments/{id}', [
        'controlador' => CommentBiomarkerController::class,
        'accion' => 'updateComment',
        'roles' => ['specialist', 'administrator']
    ]);

    // Eliminar comentario
    $router->delete('/biomarker-comments/{id}', [
        'controlador' => CommentBiomarkerController::class,
        'accion' => 'deleteComment',
        'roles' => ['specialist', 'administrator']
    ]);


    // Notifications
    $router->get('/notifications', ['controlador' => NotificationController::class, 'accion' => 'showAll']);
    $router->get('/notifications/{id}', ['controlador' => NotificationController::class, 'accion' => 'showById']);
    $router->get('/notifications/all/{id}', ['controlador' => NotificationController::class, 'accion' => 'showAllAdmin']);
    $router->get('/notifications/user-count/{id}', ['controlador' => NotificationController::class, 'accion' => 'countAlertsUser']);
    $router->get('/notifications/count-new', ['controlador' => NotificationController::class, 'accion' => 'countNewBySession']);
    $router->post('/notifications', ['controlador' => NotificationController::class, 'accion' => 'create']);
    $router->put('/notifications/{id}', ['controlador' => NotificationController::class, 'accion' => 'update']);
    $router->delete('/notifications/{id}', ['controlador' => NotificationController::class, 'accion' => 'delete']);
    $router->get('/notifications/by-user/{id}', ['controlador' => NotificationController::class, 'accion' => 'showByUserId']);
    $router->post('/notifications/update-new', ['controlador' => NotificationController::class, 'accion' => 'updateNew']);
    $router->get('/notifications/active-by-user/{id}', ['controlador' => NotificationController::class, 'accion' => 'showActiveAlertsByUserId']);
    $router->get('/notifications/dismissed-by-user/{id}', ['controlador' => NotificationController::class, 'accion' => 'showDismissedAlertsByUserId']);
    $router->get('/notifications/all-by-user/{id}', ['controlador' => NotificationController::class, 'accion' => 'showAllAlertsByUserId']);
    $router->get('/notifications/all-by-user-no-limit/{id}', ['controlador' => NotificationController::class, 'accion' => 'showByUserIdNotifications']);
    $router->get('/notifications/by-user-view-all/{id}', ['controlador' => NotificationController::class, 'accion' => 'showByUserIdViewAll']);
    $router->get('/notifications/by-status/{status}', ['controlador' => NotificationController::class, 'accion' => 'showByStatus']);
    $router->get('/notifications/active-alerts', ['controlador' => NotificationController::class, 'accion' => 'showActiveAlerts']);
    $router->get('/notifications/by-biomarker/{id_biomarker}', ['controlador' => NotificationController::class, 'accion' => 'showByBiomarkerId']);
    $router->get('/notifications/by-user-biomarker/{id_biomarker}', ['controlador' => NotificationController::class, 'accion' => 'showByBiomarkerAndUser']);
    $router->post('/notifications/no-alert-user', ['controlador' => NotificationController::class, 'accion' => 'updateNoAlertUser']);
    $router->post('/notifications/no-alert-admin', ['controlador' => NotificationController::class, 'accion' => 'updateNoAlertAdmin']);
    $router->post('/notifications/no-alert-user-all', ['controlador' => NotificationController::class, 'accion' => 'updateNoAlertUserByUserId']);
    $router->post('/notifications/no-alert-admin-all', ['controlador' => NotificationController::class, 'accion' => 'updateNoAlertAdminAll']);

    // Notification Preferences
    $router->get('/notifications/preferences', ['controlador' => NotificationPreferenceController::class, 'accion' => 'getPreferences']);
    $router->post('/notifications/preferences', ['controlador' => NotificationPreferenceController::class, 'accion' => 'updatePreferences']);

    // Push Subscriptions
    $router->post('/notifications/push-subscribe', ['controlador' => PushSubscriptionController::class, 'accion' => 'subscribe']);
    $router->post('/notifications/push-unsubscribe', ['controlador' => PushSubscriptionController::class, 'accion' => 'unsubscribe']);
});


// --- Ejecutar el Router ---
$router->route();
