<?php


class Header
{
    private $userName;
    private $userSex;
    private $userId;

    public function __construct(string $userName, string $userSex, $userId)
    {
        $this->userName = $userName;
        $this->userSex = $userSex;
        $this->userId = $userId;
    }

    public function render()
    {

        // --- Idioma y traducciones ---
        $currentLang = $_SESSION['idioma'] ?? 'EN';
        $flagSrc = $currentLang === 'ES' ? 'public/assets/images/flags/spain.jpg' : 'public/assets/images/flags/us.jpg';
        $altText = $currentLang === 'ES' ? 'Spanish' : 'English';

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

        $folder = $folderMap[$role] ?? 'users';
        $userImagePath = "uploads/{$folder}/user_" . $this->userId . ".jpg";

        // Verificar si la imagen existe
        if (!file_exists(PROJECT_ROOT . $userImagePath)) {
            $userImagePath = "public/assets/images/users/user_boy.svg";
        }

        $_SESSION['user_image'] = $userImagePath;


        // --- Diferenciación por rol ---
        $isAdmin = in_array($_SESSION['roles_user'], ["Administrator", "User", "Specialist"]);


        ?>

        <!-- Navbar -->
        <div class="navbar-custom bg-primary-app">
            <div class="container-fluid">

                <ul class="list-unstyled topnav-menu float-end mb-0">
                    <li class="dropdown d-none d-lg-inline-block">
                        <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" style="color:white !important;"
                            data-toggle="fullscreen" href="#">
                            <i class="fe-maximize noti-icon"></i>
                        </a>
                    </li>

                    <!-- Idioma -->
                    <li class="dropdown d-md-inline-block">
                        <a class="nav-link dropdown-toggle waves-effect waves-light arrow-none" data-bs-toggle="dropdown"
                            href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <img src="<?= $flagSrc ?>" alt="<?= $altText ?>" class="me-0 me-sm-1" height="18">
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated">
                            <a href="#" class="dropdown-item set-lang" data-lang="ES">
                                <img src="public/assets/images/flags/spain.jpg" alt="Spanish" class="me-1" height="12">
                                <span class="align-middle">Spanish</span>
                            </a>
                            <a href="#" class="dropdown-item set-lang" data-lang="EN">
                                <img src="public/assets/images/flags/us.jpg" alt="English" class="me-1" height="12">
                                <span class="align-middle">English</span>
                            </a>
                        </div>
                    </li>



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
                                            <?= $traducciones['dashboard_top_alerts_alerts'] ?>
                                        </h6>
                                    </div>
                                    <div class="col-auto">
                                        <a href="javascript:void(0);"
                                            id="<?= $isAdmin ? 'clear-all-alerts' : 'clear-all-admin-alerts' ?>"
                                            class="text-dark text-decoration-underline">
                                            <small><?= $traducciones['clear_all'] ?></small>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="p-2">
                                <div class="d-flex gap-1" id="notification-tabs" role="tablist">
                                    <button class="btn notification-button btn-sm text-bold" id="all-tab" data-bs-toggle="tab"
                                        type="button" role="tab"
                                        aria-selected="false"><?= $traducciones['notification_all'] ?></button>
                                    <button class="btn notification-button btn-sm text-bold active" id="unread-tab"
                                        data-bs-toggle="tab" data-bs-target="#unread-tab-pane" type="button" role="tab"
                                        aria-selected="true"><?= $traducciones['notification_unread'] ?> (<span
                                            id="unread-count">0</span>)</button>
                                </div>
                            </div>
                            <div class="px-1 overflow-auto" style="max-height: 300px;" data-simplebar="init">
                                <div class="simplebar-content" style="padding: 0px 6px;" data-rol="<?= $isAdmin ?>"
                                    id="alerts-container">

                                </div>
                            </div>
                            <a href="<?= $isAdmin ? 'user_notifications' : 'admin_notifications' ?>"
                                class="dropdown-item text-center text-primary notify-item border-top border-light py-2">
                                <?= $traducciones['view_full_list'] ?>
                            </a>
                        </div>
                    </li>

                    <!-- Usuario (avatar, nombre, menú) -->
                    <?php
                    $role = $_SESSION['roles_user'] ?? 'User'; // Valor por defecto
            
                    // Definir rutas según el rol
                    switch ($role) {
                        case 'Administrator':
                            $profileRoute = 'profile_administrator';
                            $securityRoute = 'security_question';
                            break;
                        case 'Specialist':
                            $profileRoute = 'profile_specialist';
                            $securityRoute = 'security_question';
                            break;
                        default: // User
                            $profileRoute = 'profile_user';
                            $securityRoute = 'security_question';
                            break;
                    }
                    ?>

                    <li class="dropdown notification-list topbar-dropdown">
                        <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown"
                            href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <img src="<?= $userImagePath ?>" alt="user-image" class="rounded-circle" id="user-image">
                            <span class="pro-user-name ms-1" style="color:white;">
                                <?= $this->userName; ?> <i class="mdi mdi-chevron-down"></i>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                            <div class="dropdown-header noti-title">
                                <h6 class="text-overflow m-0" style="color: white;">
                                    <?= $traducciones['welcome'] . ' ' . $this->userName; ?>!
                                </h6>
                            </div>
                            <a href="<?= $profileRoute ?>" class="dropdown-item notify-item">
                                <i class="fe-user"></i>
                                <span><?= $traducciones['my_account'] ?></span>
                            </a>

                            <a href="<?= $securityRoute ?>"
                                class="dropdown-item notify-item <?= $role != 'Specialist' ? 'd-none' : '' ?> ">
                                <i class="fe-user"></i>
                                <span><?= $traducciones['security_question'] ?></span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="logout" class="dropdown-item notify-item">
                                <i class="fe-log-out"></i>
                                <span><?= $traducciones['logout'] ?></span>
                            </a>
                        </div>
                    </li>

                </ul>

                <!-- Logo -->
                <div class="logo-box">
                    <a href="/dashboard" class="logo logo-dark text-center">
                        <span class="logo-sm">
                            <img src="public/assets/images/logo-sm.svg" alt="" height="80">
                        </span>
                        <span class="logo-lg">
                            <img src="public/assets/images/logo.png" alt="" height="90">
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

                <!-- Menú izquierdo: botón mobile y toggle -->
                <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
                    <li>
                        <button class="button-menu-mobile waves-effect waves-light" style="margin-left: 5px;">
                            <i class="mdi mdi-menu"></i>
                        </button>
                    </li>
                    <li>
                        <a class="navbar-toggle nav-link" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                            <div class="lines">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </a>
                    </li>
                </ul>

                <div class="clearfix"></div>
            </div>
        </div>

        <!-- Script para formateadores (si se usa en algún sitio) -->
        <script>
            document.querySelectorAll('[data-formatter]').forEach(elem => {
                const formatterName = elem.getAttribute('data-formatter');
                const formatterFn = window[formatterName];
                if (typeof formatterFn === 'function') {
                    const originalValue = elem.textContent.trim();
                    const formattedValue = formatterFn(originalValue);
                    elem.textContent = formattedValue;
                }
            });

            let language = <?= json_encode($traducciones) ?>;
            console.log(language);

        </script>

        <!-- Script para plantilla de alerta (reemplazar por AJAX en producción) -->
        <script>
            let html = '';
            html += `
            <a href="" class="dropdown-item p-0 notify-item card unread-noti shadow-none mb-1">
                <div class="card-body position-relative">
                    <button type="button" class="btn-close-alert float-end noti-close-btn text-muted"
                        data-record-id="" data-panel="" title="Hide alert">
                        <i class="mdi mdi-close"></i>
                    </button>
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="notify-icon">
                                <i class="mdi"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1  ms-2">
                            <h5 class="noti-item-title fw-semibold font-14  mb-1"></h5>
                            <small class="noti-item-subtitle text-muted"><?= $traducciones['value'] ?>: </small><br>
                            <small class="noti-item-subtitle text-muted"><?= $traducciones['status'] ?>: </small>
                        </div>
                    </div>
                </div>
            </a>`;
            html += `<div class="text-center"><i class="mdi mdi-dots-circle mdi-spin text-muted h3 mt-0"></i></div>`;
        </script>

        <?php
    }
}
?>