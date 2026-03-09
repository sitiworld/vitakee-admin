<?php
// --- Idioma y traducciones ---
$currentLang = $_SESSION['idioma'] ?? 'EN';
$flagSrc = $currentLang === 'ES' ? 'public/assets/images/flags/spain.jpg' : 'public/assets/images/flags/us.jpg';
$altText = $currentLang === 'ES' ? 'Spanish' : 'English';

$userName = $_SESSION['user_name'] ?? 'Guest'; // Valor por defecto si no está definido
$userSex = $_SESSION['sex'] ?? 'u'; // 'm', 'f', o 'u' (unknown)
$userId = $_SESSION['user_id'] ?? null;


// Aseguramos valores válidos para el idioma
$idioma = strtoupper($_SESSION['idioma'] ?? 'ES');
if (!in_array($idioma, ['EN', 'ES'])) {
    $idioma = 'EN';
}
$archivo_idioma = PROJECT_ROOT . '/lang/' . $idioma . '.php';
if (file_exists($archivo_idioma)) {
    $traducciones = include $archivo_idioma;
} else {
    die("Archivo de idioma no encontrado: $archivo_idioma");
}

// --- Imagen de usuario ---
// Determinar subcarpeta según el rol (usando letras)
$role = $_SESSION['roles_user'] ?? 'User'; // Valor por defecto: 'User'
$folderMap = [
    'Administrator' => 'administrator',
    'User' => 'users',
    'Specialist' => 'specialist'
];

$profileLink = "profile_{$folderMap[$role]}";

$folder = $folderMap[$role] ?? 'users';
// 1. Verificar si hay avatar_url en sesión
if (!empty($_SESSION['avatar_url'])) {
    $userImagePath = $_SESSION['avatar_url'];
} else {
    $userImagePath = "uploads/{$folder}/user_" . $userId . ".jpg";
    if (file_exists(PROJECT_ROOT . '/' . $userImagePath)) {
        $userImagePath .= '?v=' . filemtime(PROJECT_ROOT . '/' . $userImagePath);
    } else {
        // 3. Fallback a imagen por defecto
        $userImagePath = "public/assets/images/users/user_boy.svg";
    }
}

// Ensure the path does not start with an extra slash if we are appending to BASE_URL which ends with /
$userImagePath = ltrim($userImagePath, '/');
$_SESSION['user_image'] = $userImagePath;


// --- Diferenciación por rol ---
$isAdmin = in_array($_SESSION['roles_user'], ["Administrator", "Specialist"]);

$rol = strtolower($_SESSION['roles_user']);

?>

<div class="navbar-custom bg-primary-app">
    <div class="topbar">
        <div class="topbar-menu d-flex align-items-center gap-1">

            <!-- Topbar Brand Logo -->
            <div class="logo-box">
                <a href="/dashboard" class="logo logo-dark text-center">
                    <span class="logo-sm">
                        <img src="public/assets/images/logo-sm.svg" alt="" height="23">
                    </span>
                    <span class="logo-lg">
                        <img src="public/assets/images/logo.png" alt="" height="35">
                    </span>
                </a>
                <a href="/dashboard" class="logo logo-light text-center">
                    <span class="logo-sm">
                        <img src="public/assets/images/logo-sm.svg" alt="" height="23">
                    </span>
                    <span class="logo-lg">
                        <img src="public/assets/images/logo.png" alt="" height="35">
                    </span>
                </a>
            </div>

            <!-- Sidebar Menu Toggle Button -->
            <button class="button-toggle-menu">
                <i class="mdi mdi-menu text-white"></i>
            </button>


            <!-- Mega Menu Dropdown -->

        </div>

        <ul class="topbar-menu d-flex align-items-center">
            <!-- Topbar Search Form -->

            <!-- Fullscreen Button -->
            <li class="d-none d-md-inline-block">
                <a class="nav-link waves-effect waves-light" href="" data-toggle="fullscreen">
                    <i class="fe-maximize font-22 text-white"></i>
                </a>
            </li>


            <!-- Language flag dropdown -->

            <!-- Idioma -->
            <li class="dropdown d-md-inline-block">
                <a class="nav-link dropdown-toggle waves-effect waves-light arrow-none" data-bs-toggle="dropdown"
                    href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <img src="<?= $flagSrc ?>" alt="<?= $altText ?>" class="me-0 me-sm-1" height="18">
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated">
                    <a href="?lang=EN" class="dropdown-item set-lang" data-lang="EN">
                        <img src="public/assets/images/flags/us.jpg" alt="English" class="me-1" height="12">
                        <span class="align-middle"><?= $traducciones['english'] ?></span>
                    </a>
                    <a href="?lang=ES" class="dropdown-item set-lang" data-lang="ES">
                        <img src="public/assets/images/flags/spain.jpg" alt="Spanish" class="me-1" height="12">
                        <span class="align-middle"><?= $traducciones['spanish'] ?></span>
                    </a>
                </div>
            </li>


            <!-- Alertas (notificaciones) -->
            <!-- Alertas (notificaciones) -->
            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle waves-effect waves-light arrow-none" data-bs-toggle="dropdown"
                    href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <i class="dripicons-bell font-22" style="color: white;"></i>
                    <span class="badge bg-danger rounded-circle noti-icon-badge" id="alert-count">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0"
                    id="dropdown-container">
                    <div class="p-2 border-top-0 border-start-0 border-end-0 border-dashed border">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0 font-16 fw-semibold">
                                    <?= $traducciones['dashboard_top_alerts_alerts'] ?? 'Alerts' ?>
                                </h6>
                            </div>
                            <div class="col-auto">
                                <a href="javascript:void(0);" id="clear-all-alerts"
                                    class="text-dark text-decoration-underline">
                                    <small><?= $traducciones['clear_all'] ?? 'Clear All' ?></small>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="p-2">
                        <div class="d-flex gap-1 align-items-center justify-content-between" id="notification-tabs" role="tablist">
                            <div class="d-flex gap-1">
                                <button class="btn notification-button btn-sm text-bold" id="all-tab" data-bs-toggle="tab"
                                    type="button" role="tab"
                                    aria-selected="false"><?= $traducciones['notification_all'] ?? 'All' ?></button>
                                <button class="btn notification-button btn-sm text-bold active" id="unread-tab"
                                    data-bs-toggle="tab" data-bs-target="#unread-tab-pane" type="button" role="tab"
                                    aria-selected="true"><?= $traducciones['notification_unread'] ?? 'Unread' ?> (<span
                                        id="unread-count">0</span>)</button>
                            </div>
                            <!-- Botón engranaje preferencias -->
                            <button type="button" id="notification-pref-toggle"
                                class="btn btn-sm btn-link text-muted p-1"
                                title="<?= $traducciones['notification_settings'] ?? 'Notification settings' ?>"
                                aria-expanded="false" aria-controls="notification-pref-panel">
                                <i class="mdi mdi-cog-outline font-18"></i>
                            </button>
                        </div>

                        <!-- Panel de preferencias (oculto por defecto) -->
                        <div id="notification-pref-panel" class="mt-2 px-1" style="display:none;">
                            <div class="d-flex flex-column gap-2 p-2 rounded"
                                style="background:#f8f9fa;border:1px solid #e9ecef;">
                                <!-- Push -->
                                <div class="d-flex align-items-center justify-content-between">
                                    <label for="pref-push" class="mb-0 d-flex align-items-center gap-1" style="font-size:13px;cursor:pointer;">
                                        <i class="mdi mdi-bell-outline font-16 text-primary-app"></i>
                                        <?= $traducciones['notification_push'] ?? 'Push notifications' ?>
                                    </label>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input notification-pref-switch" type="checkbox"
                                            role="switch" id="pref-push" data-pref="push_enabled">
                                    </div>
                                </div>
                                <!-- Email -->
                                <div class="d-flex align-items-center justify-content-between">
                                    <label for="pref-email" class="mb-0 d-flex align-items-center gap-1" style="font-size:13px;cursor:pointer;">
                                        <i class="mdi mdi-email-outline font-16 text-primary-app"></i>
                                        <?= $traducciones['notification_email'] ?? 'Email notifications' ?>
                                    </label>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input notification-pref-switch" type="checkbox"
                                            role="switch" id="pref-email" data-pref="email_enabled">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-1 overflow-auto" style="max-height: 300px;" data-simplebar="init">
                        <div class="simplebar-content" style="padding: 0px 6px;" data-rol="<?= $rol ?>"
                            id="alerts-container">

                        </div>
                    </div>
                    <a href="<?= !$isAdmin ? 'user_notifications' : 'specialist_notifications' ?>"
                        class="dropdown-item text-center text-primary notify-item border-top border-light py-2">
                        <?= $traducciones['view_full_list'] ?>
                    </a>
                </div>
            </li>

            <!-- User Dropdown -->
            <li class="dropdown">
                <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light header-link"
                    data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <img src="<?= BASE_URL ?><?= $userImagePath ?>" alt="user-image" class="rounded-circle"
                        id="topbar-profile-image">
                    <span class="ms-1 d-none d-md-inline-block">
                        <b id="topbar-username"><?= $userName ?></b> <i class="mdi mdi-chevron-down"></i>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                    <!-- item-->
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow m-0"><?= $traducciones['welcome'] ?>!</h6>
                    </div>

                    <!-- item-->
                    <a href="my_profile" class="dropdown-item notify-item">
                        <i class="fe-user"></i>
                        <span><?= $traducciones['profile_user_title'] ?></span>
                    </a>

                    <div class="dropdown-divider"></div>

                    <!-- item-->
                    <a href="./logout" class="dropdown-item notify-item">
                        <i class="fe-log-out"></i>
                        <span><?= $traducciones['logout'] ?></span>
                    </a>

                </div>
            </li>


        </ul>
    </div>
</div>

<script>
    // Convierte el array PHP a un objeto JS global
    const translations = <?= json_encode($traducciones); ?>;
</script>


<script type="text/javascript">
    document.querySelectorAll('.set-system-type').forEach(item => {
        item.addEventListener('click', function () {
            const selectedSystem = this.dataset.system.toLowerCase();
            const respuestasweet = '<?= $traducciones['titleSuccess_user'] ?>';
            // Tomamos el rol directamente desde PHP
            const userRole = '<?= $_SESSION['roles_user'] ?>';

            // Determinar ruta según el rol
            let endpoint = '';
            switch (userRole) {
                case 'Administrator':
                    endpoint = '/administrator/system_type/update/1';
                    break;
                case 'Specialist':
                    endpoint = '/specialist/system_type/update/1';
                    break;
                case 'User':
                    endpoint = '/User/system_type/update/1';
                    break;
                default:
                    Swal.fire('Error', 'Rol de usuario no reconocido.', 'error');
                    return;
            }

            const formData = new FormData();
            formData.append('system_type', selectedSystem);
            formData.append('_method', 'PUT');

            fetch(endpoint, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.value) {

                        Swal.fire(respuestasweet, data.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Error en la petición.', 'error');
                    console.error(error);
                });
        });
    });
</script>