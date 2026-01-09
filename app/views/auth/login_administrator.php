<!DOCTYPE html>
<html lang="">

<?php include __DIR__ . '/../partials/head.php'; ?>

<head>

    <style>
        /* Mantiene el estilo correcto del botón para mostrar contraseña dentro del input-group */
        .input-group .btn-toogle-password {
            border-color: #ced4da;
            border-left: 0;
        }

        .auth-fluid {
            background-image: url('<?= BASE_URL ?>public/assets/images/administrator_login2.jpg');
            background-size: cover;
            background-position: center;
        }

        #login-image {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20%;
            height: auto;
            max-width: 20em;
            /* Ajusta el tamaño máximo según sea necesario */
            max-height: 100%;
            /* Asegura que la imagen no exceda la altura del contenedor */
            object-fit: cover;
            /* Mantiene la proporción de la imagen */



        }

        #logo-image-container {
            position: relative;


        }

        .blue-overlay {
            height: 100%;
            width: 100%;
            background: #142851;
            mix-blend-mode: soft-light;
        }
    </style>

</head>

<body class="auth-fluid-pages pb-0">

    <div class="auth-fluid">
        <div class="auth-fluid-form-box vh-100 overflow-auto">
            <div class="align-items-center justify-content-center d-flex">
                <div class="p-3">

                    <div class="text-center mb-4">


                        <div class="auth-brand">
                            <a href="<?= BASE_URL ?>administrator" class="logo logo-dark text-center">

                                <span class="logo-lg">
                                    <img src="<?= BASE_URL ?>public/assets/images/logo-index.png" alt="" height="65">
                                </span>
                            </a>
                            <a href="<?= BASE_URL ?>administrator" class="logo logo-light text-center">

                                <span class="logo-lg">
                                    <img src="<?= BASE_URL ?>public/assets/images/logo-index.png" alt="" height="65">
                                </span>
                            </a>
                        </div>
                    </div>

                    <div class="text-center mb-4">

                        <select id="language-switcher" class="form-select mx-auto" style="width: auto;">

                            <option value="es" <?= strtolower($lang ?? 'es') == 'es' ? 'selected' : '' ?>>
                                Español
                            </option>
                            <option value="en" <?= strtolower($lang ?? 'en') == 'en' ? 'selected' : '' ?>>
                                English
                            </option>
                        </select>
                    </div>

                    <ul class="nav nav-tabs nav-bordered">
                        <li class="nav-item">
                            <a href="#tab-login" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                                <?= $traducciones['log_in_tab_administrator'] ?>
                            </a>
                        </li>

                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane show active" id="tab-login">
                            <p class="text-muted mb-3"><?= $traducciones['login_prompt_administrator'] ?></p>
                            <form id="adminLoginForm">
                                <div class="mb-3">
                                    <label for="loginEmail"
                                        class="form-label"><?= $traducciones['email_address_label_administrator'] ?></label>
                                    <input class="form-control" type="email" id="loginEmail"
                                        placeholder="<?= $traducciones['enter_email_placeholder_administrator'] ?>">
                                </div>


                                <div class="mb-3">
                                    <a href='<?= BASE_URL ?>reset_password/administrator/<?= $data['lang'] == 'en' ? '' : strtolower($data['lang']) ?>'
                                        id="forgotPasswordLink" ;
                                        class="text-muted float-end"><small><?= $traducciones['forgot_password_link_specialist'] ?></small></a>
                                    <label for="loginPassword"
                                        class="form-label"><?= $traducciones['password_label_administrator'] ?></label>
                                    <div class="input-group">
                                        <input class="form-control" type="password" id="loginPassword"
                                            placeholder="<?= $traducciones['enter_password_placeholder_administrator'] ?>"
                                            data-error-parent=".input-group">

                                        <button class="btn btn-light btn-toogle-password toggle-password"
                                            data-target="loginPassword" type="button" tabindex="-1"
                                            style="background-color: #06add9;">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="text-center d-grid">
                                    <button class="btn btn-sign-up" type="submit"
                                        style="background-color: #06add9;"><?= $traducciones['login_button_text_administrator'] ?></button>
                                </div>
                            </form>
                        </div>


                    </div>
                </div>
            </div>
        </div>
        <div id="logo-image-container"
            class="auth-fluid-right p-0 text-center d-flex justify-content-center align-items-center">
            <div class="blue-overlay h-100 w-100"></div>
            <img src="<?= BASE_URL ?>public/assets/images/bysiti.svg" id="login-image">
        </div>
    </div>
    <script src="<?= BASE_URL ?>public/assets/js/vendor.min.js"></script>
    <script type="module" src="<?= BASE_URL ?>public/assets/js/app2.js"></script>

    <script src="<?= BASE_URL ?>public/assets/js/imask.js"></script>
    <script src="<?= BASE_URL ?>public/assets/libs/select2/js/select2.min.js"></script>

    <script>
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = button.querySelector('i');

                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';

                if (isPassword) {
                    icon.classList.remove('mdi-eye-outline');
                    icon.classList.add('mdi-eye-off-outline');
                } else {
                    icon.classList.remove('mdi-eye-off-outline');
                    icon.classList.add('mdi-eye-outline');
                }
            });
        });
    </script>

    <!-- <script type="module">
        import { countrySelect } from "./<?= BASE_URL ?>public/assets/js/components/countrySelect.js";
        countrySelect('adminPhone', '[data-phone-select]');
    </script> -->

    <script type="module">
        function isMobileDevice() {
            return /Mobi|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        }

        function getDeviceId() {
            let deviceId = localStorage.getItem('device_id');
            if (!deviceId) {
                deviceId = crypto.randomUUID();
                localStorage.setItem('device_id', deviceId);
            }
            return deviceId;
        }

        import { validateFormFields, hideLoader, showLoader, showConfirmation } from "<?= BASE_URL ?>public/assets/js/helpers/helpers.js";

        // Obtiene el idioma actual directamente desde PHP para usarlo en JavaScript.
        const currentLang = '<?= strtolower($_SESSION['lang']) ?? 'en' ?>';

        $(document).ready(function () {
            // Manejador del formulario de Login de Administrador
            $('#adminLoginForm').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

                const email = $('#loginEmail').val();
                const password = $('#loginPassword').val();

                let fielList = [
                    'loginEmail',
                    'loginPassword'
                ];

                if (!validateFormFields(e.target, fielList, "<?= $traducciones['input_generic_error'] ?>")) {
                    return;
                }
                showLoader()

                $.ajax({
                    url: '<?= BASE_URL ?>administrator/login', // Your login route
                    type: 'POST',
                    dataType: 'json', // Expect JSON response

                    contentType: 'application/json',
                    data: JSON.stringify({
                        email: email,
                        password: password,
                        language: currentLang,
                        device_id: getDeviceId(),
                        is_mobile: isMobileDevice(),
                        user_agent: navigator.userAgent
                    }),

                    success: function (response) {
                        // Handle successful login
                        console.log(response);

                        if (response.value) {
                            showConfirmation({
                                type: 'success', actionCallback: () => {
                                    console.log(response);

                                    window.location.href = "<?= BASE_URL ?>" + response.data.redirect; // Example redirect
                                },
                                message: {
                                    title: response.message,
                                }
                            })

                        } else {
                            showConfirmation({
                                type: 'error', actionCallback: () => {
                                    console.log('Login failed: ' + response.message);
                                },
                                message: {
                                    title: response.message,
                                    confirmButtonText: 'OK'
                                }
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle errors
                        console.log(xhr);

                        showConfirmation({ type: 'error', message: { title: 'Error de inicio de sesión', text: xhr.responseText } });
                        console.error('Login error:', xhr.responseText);
                    },
                    complete: function (data) {

                        hideLoader()
                    }
                });
            });



            // Manejador del formulario de Registro de Administrador


        });
    </script>

    <script>
        document.getElementById('language-switcher').addEventListener('change', function () {
            // Obtiene el código del idioma seleccionado (ej. "es" o "en")
            const selectedLang = this.value;

            // Obtiene la ruta actual de la URL (ej. "/specialist/login/es")
            let basePath = window.location.pathname;

            // Lista de idiomas que usan un sufijo en la URL
            const langSuffixes = ['es']; // Nota: 'en' ya no está aquí
            const pathParts = basePath.split('/');
            const lastPart = pathParts[pathParts.length - 1];

            // Revisa si el último segmento de la URL es un código de idioma conocido
            if (langSuffixes.includes(lastPart)) {
                // Si es así, lo quita para obtener la ruta base limpia
                pathParts.pop();
                basePath = pathParts.join('/') || '/'; // Reconstruye la ruta
            }

            // Asegura que la ruta base no termine con una barra si no es la raíz
            if (basePath.length > 1 && basePath.endsWith('/')) {
                basePath = basePath.slice(0, -1);
            }

            let newUrl;

            // ===== INICIO DE LA NUEVA LÓGICA =====
            if (selectedLang === 'en') {
                // Si el idioma es inglés, la URL es simplemente la ruta base
                newUrl = basePath;
            } else {
                // Para cualquier otro idioma, añadimos el sufijo
                newUrl = basePath + '/' + selectedLang;
            }
            // ===== FIN DE LA NUEVA LÓGICA =====

            // Redirige a la nueva URL
            window.location.href = basePath + "?lang=" + selectedLang;
        });
    </script>
</body>

</html>