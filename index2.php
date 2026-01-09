<?php

use App\Core\ViewRenderer;
use App\Core\Language;
use App\Router;

session_start();
require_once 'vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Iniciar sesión si no está iniciada
}
define('APP_ROOT', __DIR__ . '/'); // Define la ruta raíz de la aplicación




if (isset($_GET['lang'])) {
    $_SESSION['lang'] = strtoupper($_GET['lang']);
    $_SESSION['idioma'] = strtoupper($_GET['lang']);
}

$lang = $_SESSION['lang'] ?? 'EN';
$traducciones = App\Core\Language::loadLanguage($lang);



/**
 * Obtiene la ruta solicitada sin el path base.
 */
function obtenerRutaLimpia()
{
    $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $scriptName = trim(dirname($_SERVER['SCRIPT_NAME']), '/');

    if ($scriptName && strpos($uri, $scriptName) === 0) {
        $uri = trim(substr($uri, strlen($scriptName)), '/');
    }

    return $uri ?: '';
}

// $_GET['route'] será poblado por la regla de .htaccess
$_GET['route'] = obtenerRutaLimpia();


define('PROJECT_ROOT', __DIR__);

// --- Carga de Clases ---
require_once "app/core/ViewRenderer.php";
require_once "app/Router.php";

// Middleware
require_once "app/middleware/AuthMiddleware.php";
require_once "app/middleware/SessionRedirectMiddleware.php";

// Controladores
require_once "app/controllers/AuditLogController.php";
require_once "app/controllers/RecoveryPasswordController.php";
require_once "app/controllers/NotificationController.php";
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
require_once "app/controllers/SpecialistAvailabilityController.php";
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



// --- Inicialización ---
$viewRenderer = new ViewRenderer($traducciones);
$router = new Router($viewRenderer);


// -----------------------------------------------------------------------------
// --- DEFINICIÓN DE RUTAS ---
// -----------------------------------------------------------------------------

// =============================================================================
// --- GRUPO PÚBLICO (Sin autenticación) ---
// =============================================================================
$router->group(['middleware' => 'SessionRedirectMiddleware'], function ($router) {
    // Vistas de Login y Registro con soporte de idioma
    $router->agregarRuta('GET', '', ['vista' => 'login', 'vistaData' => ['titulo' => 'Login', 'lang' => 'en']]);
    $router->agregarRuta('GET', 'es', ['vista' => 'login', 'vistaData' => ['titulo' => 'Login', 'lang' => 'es']]);
    $router->agregarRuta('GET', 'login', ['vista' => 'login', 'vistaData' => ['titulo' => 'Login', 'lang' => 'en']]);
    $router->agregarRuta('GET', 'login/es', ['vista' => 'login', 'vistaData' => ['titulo' => 'Login', 'lang' => 'es']]);
    $router->agregarRuta('GET', 'specialist', ['vista' => 'login_specialist', 'vistaData' => ['titulo' => 'Login', 'lang' => 'en']]);
    $router->agregarRuta('GET', 'specialist/login', ['vista' => 'login_specialist', 'vistaData' => ['titulo' => 'Specialist\' Login', 'lang' => 'en']]);
    $router->agregarRuta('GET', 'specialist/es', ['vista' => 'login_specialist', 'vistaData' => ['titulo' => 'Login', 'lang' => 'es']]);
    $router->agregarRuta('GET', 'specialist/login/es', ['vista' => 'login_specialist', 'vistaData' => ['titulo' => 'Login', 'lang' => 'es']]);
    $router->agregarRuta('GET', 'administrator', ['vista' => 'login_administrator', 'vistaData' => ['titulo' => 'Administrator login', 'lang' => 'en']]);
    $router->agregarRuta('GET', 'administrator/login', ['vista' => 'login_administrator', 'vistaData' => ['titulo' => 'Specialist Login', 'lang' => 'en']]);
    $router->agregarRuta('GET', 'administrator/es', ['vista' => 'login_administrator', 'vistaData' => ['titulo' => 'Administrator login', 'lang' => 'es']]);
    $router->agregarRuta('GET', 'administrator/login/es', ['vista' => 'login_administrator', 'vistaData' => ['titulo' => 'Administrator login', 'lang' => 'es']]);



    // Vistas y API de Recuperación de Contraseña
    $router->agregarRuta('GET', 'reset_password', ['vista' => 'reset_password', 'vistaData' => ['titulo' => 'Recovery User', 'userType' => 'User']]);
    $router->agregarRuta('GET', 'reset_password/es', ['vista' => 'reset_password', 'vistaData' => ['titulo' => 'Recuperar Usuario', 'lang' => 'es', 'userType' => 'User', 'backTo' => 'es']]);
    $router->agregarRuta('GET', 'reset_password/specialist', ['vista' => 'reset_password', 'vistaData' => ['titulo' => 'Recovery User', 'userType' => 'Specialist', 'backTo' => 'specialist']]);
    $router->agregarRuta('GET', 'reset_password/specialist/es', ['vista' => 'reset_password', 'vistaData' => ['titulo' => 'Recuperar Usuario', 'lang' => 'es', 'userType' => 'Specialist', 'backTo' => 'specialist/es']]);
    $router->agregarRuta('GET', 'reset_password/administrator', ['vista' => 'reset_password', 'vistaData' => ['titulo' => 'Recovery User', 'userType' => 'Administrator', 'backTo' => 'administrator']]);
    $router->agregarRuta('GET', 'reset_password/administrator/es', ['vista' => 'reset_password', 'vistaData' => ['titulo' => 'Recuperar Usuario', 'lang' => 'es', 'userType' => 'Administrator', 'backTo' => 'administrator/es']]);




});

$router->group([], function ($router) {
    // api publicas

    // API de Autenticación y Registro
    $router->agregarRuta('POST', 'login', ['controlador' => AuthController::class, 'accion' => 'login']);
    $router->agregarRuta('POST', 'register', ['controlador' => AuthController::class, 'accion' => 'registrar']);
    $router->agregarRuta('POST', 'specialist/login', ['controlador' => SpecialistController::class, 'accion' => 'login']);
    $router->agregarRuta('POST', 'specialist/register', ['controlador' => SpecialistController::class, 'accion' => 'register']);
    $router->agregarRuta('POST', 'administrator/login', ['controlador' => AdministratorController::class, 'accion' => 'login']);
    $router->agregarRuta('POST', 'administrator/register', ['controlador' => AdministratorController::class, 'accion' => 'register']);

    $router->agregarRuta('POST', 'password-recovery/verify-email', ['controlador' => RecoveryPasswordController::class, 'accion' => 'verifyEmail']);
    $router->agregarRuta('POST', 'password-recovery/verify-answers', ['controlador' => RecoveryPasswordController::class, 'accion' => 'verifySecurityAnswers']);
    $router->agregarRuta('POST', 'password-recovery/update-password', ['controlador' => RecoveryPasswordController::class, 'accion' => 'updatePassword']);
    $router->agregarRuta('POST', 'check/check-telephone', ['controlador' => RecoveryPasswordController::class, 'accion' => 'checkTelephone']);
    $router->agregarRuta('POST', 'check/check-email', ['controlador' => RecoveryPasswordController::class, 'accion' => 'checkEmail']);
    $router->agregarRuta('POST', 'check-email-specialist', ['controlador' => SpecialistController::class, 'accion' => 'checkEmail']);
    $router->agregarRuta('POST', 'check-telephone-specialist', ['controlador' => SpecialistController::class, 'accion' => 'checkTelephone']);
    $router->agregarRuta('POST', 'check-email-administrator', ['controlador' => AdministratorController::class, 'accion' => 'checkEmail']);
    $router->agregarRuta('POST', 'check-telephone-administrator', ['controlador' => AdministratorController::class, 'accion' => 'checkTelephone']);

    // API Pública General
    $router->agregarRuta('GET', 'countries/all', ['controlador' => UserController::class, 'accion' => 'getAllCountries']);
    $router->agregarRuta('GET', 'specialties', ['controlador' => SpecialtyController::class, 'accion' => 'showAll']);
    $router->agregarRuta('GET', 'titles', ['controlador' => TitleController::class, 'accion' => 'showAll']);
});



// =============================================================================
// --- GRUPO DE USUARIO (Rol: user) ---
// =============================================================================
$router->group(['middleware' => 'AuthMiddleware', 'roles' => ['user']], function ($router) {
    // Vistas
    $router->agregarRuta('GET', 'dashboard', ['vista' => 'dashboard', 'vistaData' => ['titulo' => 'Dashboard', '' => '']]);
    $router->agregarRuta('GET', 'second_opinion_view', ['vista' => 'user_second_opinion', 'vistaData' => ['titulo' => 'Second Opinion', '' => '']]);
    $router->agregarRuta('GET', 'energy_metabolism_view', ['vista' => 'user_energy_metabolism', 'vistaData' => ['titulo' => 'Energy Metabolism', '' => '']]);
    $router->agregarRuta('GET', 'body_composition', ['vista' => 'user_body_composition', 'vistaData' => ['titulo' => 'Body Composition', '' => '']]);
    $router->agregarRuta('GET', 'lipid_profile', ['vista' => 'user_lipid_profile', 'vistaData' => ['titulo' => 'Lipid Profile', '' => '']]);
    $router->agregarRuta('GET', 'renal_function', ['vista' => 'user_renal_function', 'vistaData' => ['titulo' => 'Renal Function', '' => '']]);
    $router->agregarRuta('GET', 'component_energy_metabolism', ['vista' => 'user_energy_metabolism_component', 'vistaData' => ['titulo' => 'Component Energy Metabolism', '' => '']]);
    $router->agregarRuta('GET', 'component_lipid', ['vista' => 'user_lipid_profile_component', 'vistaData' => ['titulo' => 'Component Lipid', '' => '']]);
    $router->agregarRuta('GET', 'component_renal', ['vista' => 'user_renal_function_component', 'vistaData' => ['titulo' => 'Component Renal', '' => '']]);
    $router->agregarRuta('GET', 'component_body_composition', ['vista' => 'user_body_composition_component', 'vistaData' => ['titulo' => 'Component Body', '' => '']]);
    $router->agregarRuta('GET', 'user_test_documents', ['vista' => 'user_test_documents', 'vistaData' => ['titulo' => 'Test-Documents', '' => '']]);
    $router->agregarRuta('GET', 'security_question', ['vista' => 'security_question', 'vistaData' => ['titulo' => 'Security Questions', '' => '']]);
    $router->agregarRuta('GET', 'profile_user', ['vista' => 'profile_user', 'vistaData' => ['titulo' => 'Profile User', '' => '']]);
    $router->agregarRuta('GET', 'user_notifications', ['vista' => 'user_notifications', 'vistaData' => ['titulo' => 'Notifications', '' => '']]);
    $router->agregarRuta('GET', 'chat', ['vista' => 'chat', 'vistaData' => ['titulo' => 'Chat', '' => '']]);

    // API
    $router->agregarRuta('POST', 'users_profile/{id}', ['controlador' => UserController::class, 'accion' => 'update_profile']);
    $router->agregarRuta('POST', 'user/system_type/update/{id}', ['controlador' => UserController::class, 'accion' => 'update_system_type_session_user']);
    // Body Composition
    $router->agregarRuta('GET', 'body-compositions', ['controlador' => BodyCompositionController::class, 'accion' => 'getAllByUserId']);
    $router->agregarRuta('GET', 'body-compositions/user/{user_id}', ['controlador' => BodyCompositionController::class, 'accion' => 'showByIdUser']);
    $router->agregarRuta('GET', 'body_composition/user/{user_id}', ['controlador' => BodyCompositionController::class, 'accion' => 'showByIdUser']);
    $router->agregarRuta('POST', 'body-compositions', ['controlador' => BodyCompositionController::class, 'accion' => 'create']);
    $router->agregarRuta('PUT', 'body-compositions/{id}', ['controlador' => BodyCompositionController::class, 'accion' => 'update']);
    $router->agregarRuta('DELETE', 'body-compositions/{id}', ['controlador' => BodyCompositionController::class, 'accion' => 'delete']);

    $router->agregarRuta('GET', 'body-compositions/export/{id}', ['controlador' => BodyCompositionController::class, 'accion' => 'exportCSVByUserId']);
    $router->agregarRuta('POST', 'body-compositions/no-alert-user', ['controlador' => BodyCompositionController::class, 'accion' => 'updateNoAlertUser']);
    $router->agregarRuta('POST', 'body-compositions/no-alert-admin', ['controlador' => BodyCompositionController::class, 'accion' => 'updateNoAlertAdmin']);
    $router->agregarRuta('POST', 'body-compositions/no-alert-user-by-user', ['controlador' => BodyCompositionController::class, 'accion' => 'updateNoAlertUserByUser']);
    $router->agregarRuta('POST', 'body-compositions/no-alert-admin-by-user', ['controlador' => BodyCompositionController::class, 'accion' => 'updateNoAlertAdminByUser']);
    // Energy Metabolism
    $router->agregarRuta('GET', 'energy_metabolism', ['controlador' => EnergyMetabolismController::class, 'accion' => 'showAll']);
    $router->agregarRuta('GET', 'energy_metabolism/user/{user_id}', ['controlador' => EnergyMetabolismController::class, 'accion' => 'showByIdUser']);
    $router->agregarRuta('POST', 'energy_metabolism/', ['controlador' => EnergyMetabolismController::class, 'accion' => 'create']);
    $router->agregarRuta('PUT', 'energy_metabolism/{id}', ['controlador' => EnergyMetabolismController::class, 'accion' => 'update']);
    $router->agregarRuta('DELETE', 'energy_metabolism/{id}', ['controlador' => EnergyMetabolismController::class, 'accion' => 'delete']);
    $router->agregarRuta('GET', 'energy_metabolism/export/{id}', ['controlador' => EnergyMetabolismController::class, 'accion' => 'exportCSVByUserId']);
    $router->agregarRuta('POST', 'energy_metabolism/no-alert-user', ['controlador' => EnergyMetabolismController::class, 'accion' => 'updateNoAlertUser']);
    $router->agregarRuta('POST', 'energy_metabolism/no-alert-admin', ['controlador' => EnergyMetabolismController::class, 'accion' => 'updateNoAlertAdmin']);
    $router->agregarRuta('POST', 'energy_metabolism/no-alert-user-by-user', ['controlador' => EnergyMetabolismController::class, 'accion' => 'updateNoAlertUserByUser']);
    $router->agregarRuta('POST', 'energy_metabolism/no-alert-admin-by-user', ['controlador' => EnergyMetabolismController::class, 'accion' => 'updateNoAlertAdminByUser']);
    // Lipid Profile
    $router->agregarRuta('GET', 'lipid-profile', ['controlador' => LipidProfileController::class, 'accion' => 'getAllByUser']);
    $router->agregarRuta('GET', 'lipid-profile/user/{user_id}', ['controlador' => LipidProfileController::class, 'accion' => 'getRecordUser']);
    $router->agregarRuta('GET', 'lipid_profile_record/user/{user_id}', ['controlador' => LipidProfileController::class, 'accion' => 'getRecordUser']);
    $router->agregarRuta('POST', 'lipid-profile', ['controlador' => LipidProfileController::class, 'accion' => 'create']);
    $router->agregarRuta('PUT', 'lipid-profile/{id}', ['controlador' => LipidProfileController::class, 'accion' => 'update']);
    $router->agregarRuta('DELETE', 'lipid-profile/{id}', ['controlador' => LipidProfileController::class, 'accion' => 'delete']);

    $router->agregarRuta('GET', 'lipid-profile/export/{id}', ['controlador' => LipidProfileController::class, 'accion' => 'exportCSVByUserId']);
    $router->agregarRuta('POST', 'lipid-profile/no-alert-user', ['controlador' => LipidProfileController::class, 'accion' => 'updateNoAlertUser']);
    $router->agregarRuta('POST', 'lipid-profile/no-alert-admin', ['controlador' => LipidProfileController::class, 'accion' => 'updateNoAlertAdmin']);
    $router->agregarRuta('POST', 'lipid-profile/no-alert-user-by-user', ['controlador' => LipidProfileController::class, 'accion' => 'updateNoAlertUserByUser']);
    $router->agregarRuta('POST', 'lipid-profile/no-alert-admin-by-user', ['controlador' => LipidProfileController::class, 'accion' => 'updateNoAlertAdminByUser']);
    // Renal Function
    $router->agregarRuta('GET', 'renal-function', ['controlador' => RenalFunctionController::class, 'accion' => 'showAll']);
    $router->agregarRuta('GET', 'renal-function/user/{user_id}', ['controlador' => RenalFunctionController::class, 'accion' => 'showByIdUser']);
    $router->agregarRuta('GET', 'renal_function/user/{user_id}', ['controlador' => RenalFunctionController::class, 'accion' => 'showByIdUser']);
    $router->agregarRuta('POST', 'renal-function/', ['controlador' => RenalFunctionController::class, 'accion' => 'create']);
    $router->agregarRuta('PUT', 'renal-function/{id}', ['controlador' => RenalFunctionController::class, 'accion' => 'update']);
    $router->agregarRuta('DELETE', 'renal-function/{id}', ['controlador' => RenalFunctionController::class, 'accion' => 'delete']);
    $router->agregarRuta('GET', 'renal-function/export/{id}', ['controlador' => RenalFunctionController::class, 'accion' => 'exportCSVByUserId']);

    // Security Questions
    $router->agregarRuta('GET', 'security-questions', ['controlador' => SecurityQuestionController::class, 'accion' => 'getByUser']);
    $router->agregarRuta('POST', 'security-questions', ['controlador' => SecurityQuestionController::class, 'accion' => 'create']);
    $router->agregarRuta('PUT', 'security-questions/{id}', ['controlador' => SecurityQuestionController::class, 'accion' => 'update']);
    $router->agregarRuta('DELETE', 'security-questions/{id}', ['controlador' => SecurityQuestionController::class, 'accion' => 'delete']);
    // Test Documents

    $router->agregarRuta('POST', 'biomarkers/in-out-range-percentage_user', [
        'controlador' => 'BiomarkerController',
        'accion' => 'getUserInOutRangePercentageByBiomarker'
    ]);



    // CRUD routes for second_opinion_requests
    $router->agregarRuta('GET', 'second-opinion-requests', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'getAll'
    ]);

    $router->agregarRuta('GET', 'second-opinion-requests/{id}', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'getById'
    ]);

    $router->agregarRuta('POST', 'second-opinion-requests', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'create'
    ]);

    $router->agregarRuta('PUT', 'second-opinion-requests/{id}', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'update'
    ]);

    $router->agregarRuta('DELETE', 'second-opinion-requests/{id}', [
        'controlador' => SecondOpinionRequestsController::class,
        'accion' => 'delete'
    ]);


});


// =============================================================================
// --- GRUPO DE ESPECIALISTA (Rol: specialist) ---
// =============================================================================
$router->group(['middleware' => 'AuthMiddleware', 'roles' => ['specialist']], function ($router) {
    // Vistas
    $router->agregarRuta('GET', 'dashboard_specialist', ['vista' => 'dashboard_specialists', 'vistaData' => ['titulo' => 'Dashboard', '' => '']]);
    $router->agregarRuta('GET', 'profile_specialist', ['vista' => 'profile_specialist', 'vistaData' => ['titulo' => 'Profile Specialist', '' => '']]);
    $router->agregarRuta('GET', 'panel-records', ['vista' => 'user_record', 'vistaData' => ['titulo' => 'Panel Records']]);
    $router->agregarRuta('GET', 'comment_biomarker', ['vista' => 'admin_comment_biomarker', 'vistaData' => ['titulo' => 'Comments', '' => '']]);


    // API
    $router->agregarRuta('POST', 'specialist/system_type/update/{id}', ['controlador' => SpecialistController::class, 'accion' => 'update_system_type_session_user']);
    $router->agregarRuta('POST', 'specialist/update-profile/{id}', ['controlador' => SpecialistController::class, 'accion' => 'updateProfile']);

    // CRUD routes for specialist_certifications
    $router->agregarRuta('GET', 'specialist-certifications', [
        'controlador' => SpecialistCertificationsController::class,
        'accion' => 'getAll'
    ]);
    $router->agregarRuta('GET', 'specialist-certifications/{id}', [
        'controlador' => SpecialistCertificationsController::class,
        'accion' => 'getById'
    ]);
    $router->agregarRuta('POST', 'specialist-certifications', [
        'controlador' => SpecialistCertificationsController::class,
        'accion' => 'create'
    ]);

    $router->agregarRuta('POST', 'specialist-certifications/{id}', [
        'controlador' => SpecialistCertificationsController::class,
        'accion' => 'update'
    ]);

    $router->agregarRuta('DELETE', 'specialist-certifications/{id}', [
        'controlador' => SpecialistCertificationsController::class,
        'accion' => 'delete'
    ]);

    // CRUD routes for specialist_social_links
    $router->agregarRuta('GET', 'specialist-social-links', [
        'controlador' => SpecialistSocialLinksController::class,
        'accion' => 'getAll'
    ]);

    $router->agregarRuta('GET', 'specialist-social-links/{id}', [
        'controlador' => SpecialistSocialLinksController::class,
        'accion' => 'getById'
    ]);

    $router->agregarRuta('POST', 'specialist-social-links', [
        'controlador' => SpecialistSocialLinksController::class,
        'accion' => 'create'
    ]);

    $router->agregarRuta('POST', 'specialist-social-links/{id}', [
        'controlador' => SpecialistSocialLinksController::class,
        'accion' => 'update'
    ]);

    $router->agregarRuta('DELETE', 'specialist-social-links/{id}', [
        'controlador' => SpecialistSocialLinksController::class,
        'accion' => 'delete'
    ]);


    // CRUD routes for specialist_locations
    $router->agregarRuta('GET', 'specialist-locations', [
        'controlador' => SpecialistLocationsController::class,
        'accion' => 'getAll'
    ]);

    $router->agregarRuta('GET', 'specialist-locations/{id}', [
        'controlador' => SpecialistLocationsController::class,
        'accion' => 'getById'
    ]);

    $router->agregarRuta('POST', 'specialist-locations', [
        'controlador' => SpecialistLocationsController::class,
        'accion' => 'create'
    ]);

    $router->agregarRuta('PUT', 'specialist-locations/{id}', [
        'controlador' => SpecialistLocationsController::class,
        'accion' => 'update'
    ]);

    $router->agregarRuta('DELETE', 'specialist-locations/{id}', [
        'controlador' => SpecialistLocationsController::class,
        'accion' => 'delete'
    ]);

    // CRUD routes for specialist_reviews
    $router->agregarRuta('GET', 'specialist-reviews', [
        'controlador' => SpecialistReviewsController::class,
        'accion' => 'getAll'
    ]);

    $router->agregarRuta('GET', 'specialist-reviews/{id}', [
        'controlador' => SpecialistReviewsController::class,
        'accion' => 'getById'
    ]);

    $router->agregarRuta('POST', 'specialist-reviews', [
        'controlador' => SpecialistReviewsController::class,
        'accion' => 'create'
    ]);

    $router->agregarRuta('PUT', 'specialist-reviews/{id}', [
        'controlador' => SpecialistReviewsController::class,
        'accion' => 'update'
    ]);

    $router->agregarRuta('DELETE', 'specialist-reviews/{id}', [
        'controlador' => SpecialistReviewsController::class,
        'accion' => 'delete'
    ]);


    // CRUD routes for specialist_availability
    $router->agregarRuta('GET', 'specialist-availability', [
        'controlador' => SpecialistAvailabilityController::class,
        'accion' => 'getAll'
    ]);

    $router->agregarRuta('GET', 'specialist-availability/{id}', [
        'controlador' => SpecialistAvailabilityController::class,
        'accion' => 'getById'
    ]);

    $router->agregarRuta('POST', 'specialist-availability', [
        'controlador' => SpecialistAvailabilityController::class,
        'accion' => 'create'
    ]);

    $router->agregarRuta('PUT', 'specialist-availability/{id}', [
        'controlador' => SpecialistAvailabilityController::class,
        'accion' => 'update'
    ]);

    $router->agregarRuta('DELETE', 'specialist-availability/{id}', [
        'controlador' => SpecialistAvailabilityController::class,
        'accion' => 'delete'
    ]);

    // CRUD routes for specialist_pricing
    $router->agregarRuta('GET', 'specialist-pricing', [
        'controlador' => SpecialistPricingController::class,
        'accion' => 'getAll'
    ]);

    $router->agregarRuta('GET', 'specialist-pricing/{id}', [
        'controlador' => SpecialistPricingController::class,
        'accion' => 'getById'
    ]);

    $router->agregarRuta('POST', 'specialist-pricing', [
        'controlador' => SpecialistPricingController::class,
        'accion' => 'create'
    ]);

    $router->agregarRuta('PUT', 'specialist-pricing/{id}', [
        'controlador' => SpecialistPricingController::class,
        'accion' => 'update'
    ]);

    $router->agregarRuta('DELETE', 'specialist-pricing/{id}', [
        'controlador' => SpecialistPricingController::class,
        'accion' => 'delete'
    ]);


    // CRUD routes for specialist_verification_requests
    $router->agregarRuta('GET', 'specialist-verification-requests', [
        'controlador' => SpecialistVerificationRequestsController::class,
        'accion' => 'getAll'
    ]);

    $router->agregarRuta('GET', 'specialist-verification-requests/{id}', [
        'controlador' => SpecialistVerificationRequestsController::class,
        'accion' => 'getById'
    ]);

    $router->agregarRuta('POST', 'specialist-verification-requests', [
        'controlador' => SpecialistVerificationRequestsController::class,
        'accion' => 'create'
    ]);

    $router->agregarRuta('PUT', 'specialist-verification-requests/{id}', [
        'controlador' => SpecialistVerificationRequestsController::class,
        'accion' => 'update'
    ]);

    $router->agregarRuta('DELETE', 'specialist-verification-requests/{id}', [
        'controlador' => SpecialistVerificationRequestsController::class,
        'accion' => 'delete'
    ]);
});


// =============================================================================
// --- GRUPO DE ADMINISTRADOR (Rol: administrator) ---
// =============================================================================
$router->group(['middleware' => 'AuthMiddleware', 'roles' => ['administrator']], function ($router) {
    // Vistas
    $router->agregarRuta('GET', 'dashboard_administrator', ['vista' => 'dashboard_administrator', 'vistaData' => ['titulo' => 'Dashboard', '' => '']]);
    $router->agregarRuta('GET', 'profile_administrator', ['vista' => 'profile_administrator', 'vistaData' => ['titulo' => 'Profile Administrator', '' => '']]);
    $router->agregarRuta('GET', 'backups_view', ['vista' => 'admin_backups', 'vistaData' => ['titulo' => 'Backups', '' => '']]);
    $router->agregarRuta('GET', 'biomarkers', ['vista' => 'admin_biomarkers', 'vistaData' => ['titulo' => 'Biomarkers', '' => '']]);
    $router->agregarRuta('GET', 'test_panels', ['vista' => 'admin_test_panels', 'vistaData' => ['titulo' => 'Test Panels', '' => '']]);
    $router->agregarRuta('GET', 'users_view', ['vista' => 'admin_users', 'vistaData' => ['titulo' => 'Users', '' => '']]);
    $router->agregarRuta('GET', 'specialists_view', ['vista' => 'admin_specialists', 'vistaData' => ['titulo' => 'Specialists', '' => '']]);
    $router->agregarRuta('GET', 'administrators_view', ['vista' => 'admin_administrators', 'vistaData' => ['titulo' => 'Administrators', '' => '']]);
    $router->agregarRuta('GET', 'admin_notifications', ['vista' => 'admin_notifications', 'vistaData' => ['titulo' => 'Notifications', '' => '']]);
    $router->agregarRuta('GET', 'audit_log_view', ['vista' => 'admin_audit_log', 'vistaData' => ['titulo' => 'Audit Logs', '' => '']]);
    $router->agregarRuta('GET', 'specialty_view', ['vista' => 'admin_specialty', 'vistaData' => ['titulo' => 'Specialty', '' => '']]);
    $router->agregarRuta('GET', 'title_view', ['vista' => 'admin_titles', 'vistaData' => ['titulo' => 'Specialists Titles', '' => '']]);
    $router->agregarRuta('GET', 'countries_view', ['vista' => 'admin_countries', 'vistaData' => ['titulo' => 'Countries', '' => '']]);

    // API de Gestión (CRUDs)
    $router->agregarRuta('POST', 'administrator/system_type/update/{id}', ['controlador' => AdministratorController::class, 'accion' => 'update_system_type_session_user']);
    $router->agregarRuta('GET', 'administrator_get', ['controlador' => AdministratorController::class, 'accion' => 'showAll']);
    $router->agregarRuta('GET', 'administrator_get/{id}', ['controlador' => AdministratorController::class, 'accion' => 'showById']);
    $router->agregarRuta('POST', 'administrator/create', ['controlador' => AdministratorController::class, 'accion' => 'create']);
    $router->agregarRuta('POST', 'administrator/update/{id}', ['controlador' => AdministratorController::class, 'accion' => 'update']);
    $router->agregarRuta('DELETE', 'administrator/{id}', ['controlador' => AdministratorController::class, 'accion' => 'delete']);
    $router->agregarRuta('POST', 'administrator/update-profile/{id}', ['controlador' => AdministratorController::class, 'accion' => 'update_profile']);
    $router->agregarRuta('GET', 'specialist_get', ['controlador' => SpecialistController::class, 'accion' => 'showAll']);
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






});


// =============================================================================
// --- GRUPO AUTENTICADO (Rutas compartidas por varios roles) ---
// =============================================================================
$router->group(['middleware' => 'AuthMiddleware'], function ($router) {
    // Logout (accesible para todos los roles autenticados)

    $router->agregarRuta('GET', 'users_records', ['vista' => 'admin_users_records', 'vistaData' => ['titulo' => 'Users Records', '' => ''], 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('GET', 'session_management_view', ['vista' => 'admin_session_management', 'vistaData' => ['titulo' => 'Session Audit', '' => ''], 'roles' => ['administrator']]);

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

    $router->agregarRuta('GET', 'specialist_get', ['controlador' => SpecialistController::class, 'accion' => 'showAll', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('GET', 'specialist_get/{id}', ['controlador' => SpecialistController::class, 'accion' => 'showById', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('POST', 'specialist/create', ['controlador' => SpecialistController::class, 'accion' => 'create', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('POST', 'specialist/update/{id}', ['controlador' => SpecialistController::class, 'accion' => 'update', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('DELETE', 'specialist/{id}', ['controlador' => SpecialistController::class, 'accion' => 'delete', 'roles' => ['specialist', 'administrator']]);


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
    $router->agregarRuta('GET', 'biomarkers/filtered/{id_biomarker}/{minDate}/{maxDate}/{tipo}', ['controlador' => 'BiomarkerController', 'accion' => 'getFilteredBiomarkerRecords', 'roles' => ['user', 'specialist', 'administrator']]);
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
    $router->agregarRuta('POST', 'biomarker-comments', ['controlador' => CommentBiomarkerController::class, 'accion' => 'createComment', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('POST', 'biomarker-comments/{id}', ['controlador' => CommentBiomarkerController::class, 'accion' => 'updateComment', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('DELETE', 'biomarker-comments/{id}', ['controlador' => CommentBiomarkerController::class, 'accion' => 'deleteComment', 'roles' => ['specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarker-comments/{panel}/{test}', ['controlador' => CommentBiomarkerController::class, 'accion' => 'showCommentsByPanelAndTest', 'roles' => ['user', 'specialist', 'administrator']]);
    $router->agregarRuta('GET', 'biomarker-comment/{id}', ['controlador' => CommentBiomarkerController::class, 'accion' => 'showCommentById', 'roles' => ['specialist', 'administrator']]);

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


// --- Ejecutar el Router ---
$router->route();
