<!DOCTYPE html>
<html lang="en">

<head>
    <?php include __DIR__ . '/../partials/head.php'; ?>
</head>

<body class="authentication-bg authentication-bg-pattern bg-success-light">
    <div class="account-pages mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">


                                <div class="auth-brand">
                                    <a href="<?= BASE_URL ?>" class="logo logo-dark text-center">
                                        <span class="logo-lg">
                                            <img src="<?= BASE_URL ?>public/assets/images/logo-index.png" alt=""
                                                height="65">
                                        </span>
                                    </a>
                                    <a href="<?= BASE_URL ?>" class="logo logo-light text-center">

                                        <span class="logo-lg">
                                            <img src="<?= BASE_URL ?>/public/assets/images/logo-index.png" alt=""
                                                height="65">
                                        </span>
                                    </a>
                                </div>
                            </div>
                            <div class="text-center mb-4">
                                <select id="language-switcher" class="form-select mx-auto" style="width: auto;">
                                    <option value="es" <?= strtolower($_SESSION['lang']) == 'es' ? 'selected' : '' ?>>
                                        Español</option>
                                    <option value="en" <?= strtolower($_SESSION['lang']) == 'en' ? 'selected' : '' ?>>
                                        English</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="p-sm-3">
                                        <h4 class="mt-0"><?= $traducciones['signin_title'] ?></h4>
                                        <p class="text-muted mb-3">
                                            <?= htmlspecialchars($traducciones['signin_description']) ?>
                                        </p>
                                        
                                        <!-- Social Login Buttons -->
                                        <div class="mb-3">
                                            <div class="d-grid gap-2">
                                                <a href="<?= BASE_URL ?>auth/google" class="btn btn-light border">
                                                    <i class="mdi mdi-google text-danger me-1"></i>
                                                    Sign in with Google
                                                </a>
                                            </div>
                                            <div class="text-center my-3">
                                                <span class="text-muted">or</span>
                                            </div>
                                        </div>

                                        <form id="login-form" method="POST" data-validation="reactive" novalidate>
                                            <div class="mb-3">
                                                <label for="email_login"
                                                    class="form-label"><?= htmlspecialchars($traducciones['signin_email_label']) ?></label>
                                                <input class="form-control" type="email" id="email_login"
                                                    name="email_login"
                                                    placeholder="<?= htmlspecialchars($traducciones['signin_email_placeholder']) ?>"
                                                    data-rules="noVacio|email"
                                                    data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required']) ?>"
                                                    data-message-email="<?= htmlspecialchars($traducciones['validation_email'] ?? $traducciones['validation_required']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <a href='reset_password'
                                                    class="text-muted font-13 float-end"><?= htmlspecialchars($traducciones['signin_forgot_password']) ?></a>
                                                <label for="password_login"
                                                    class="form-label"><?= htmlspecialchars($traducciones['signin_password_label']) ?></label>
                                                <div class="input-group">
                                                    <input class="form-control" type="password" id="password_login"
                                                        name="password_login"
                                                        placeholder="<?= htmlspecialchars($traducciones['signin_password_placeholder']) ?>"
                                                        data-rules="noVacio"
                                                        data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required']) ?>"
                                                        data-error-container=".input-group">
                                                    <button class="btn btn-toogle-password btn-sm me-1 toggle-password"
                                                        data-target="password_login" type="button" tabindex="-1">
                                                        <i class="mdi mdi-eye-outline"></i>
                                                    </button>
                                                </div>
                                            </div>


                                            <div class="mb-3">
                                                <button class="btn btn-sign-up float-sm-end"
                                                    type="submit"><?= htmlspecialchars($traducciones['signin_button']) ?></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="p-sm-3">
                                        <h4 class="mt-3 mt-lg-0"><?= htmlspecialchars($traducciones['signup_title']) ?>
                                        </h4>
                                        <p class="text-muted mb-4">
                                            <?= htmlspecialchars($traducciones['signup_description']) ?>
                                        </p>
                                        
                                        <!-- Social Register Buttons -->
                                        <div class="mb-3">
                                            <div class="d-grid gap-2">
                                                <a href="<?= BASE_URL ?>auth/google" class="btn btn-light border">
                                                    <i class="mdi mdi-google text-danger me-1"></i>
                                                    Sign up with Google
                                                </a>
                                            </div>
                                            <div class="text-center my-3">
                                                <span class="text-muted">or</span>
                                            </div>
                                        </div>

                                        <form id="register-form" method="POST" data-validation="reactive" novalidate>
                                            <div class="mb-3">
                                                <label for="first_name"
                                                    class="form-label"><?= htmlspecialchars($traducciones['signup_first_name_label']) ?></label>
                                                <input class="form-control" type="text" id="first_name"
                                                    name="first_name"
                                                    placeholder="<?= htmlspecialchars($traducciones['signup_first_name_placeholder']) ?>"
                                                    data-rules="noVacio"
                                                    data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="last_name"
                                                    class="form-label"><?= htmlspecialchars($traducciones['signup_last_name_label']) ?></label>
                                                <input class="form-control" type="text" id="last_name" name="last_name"
                                                    placeholder="<?= htmlspecialchars($traducciones['signup_last_name_placeholder']) ?>"
                                                    data-rules="noVacio"
                                                    data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label d-block">
                                                    <?= htmlspecialchars($traducciones['signup_gender_label']) ?>
                                                    <span class="ms-1" data-bs-toggle="tooltip"
                                                        data-bs-placement="right"
                                                        title="<?= ($_SESSION['idioma'] ?? 'ES') === 'ES'
                                                            ? 'Algunos biomarcadores requieren el sexo asignado al nacer para calcular valores de referencia con precisión.'
                                                            : 'Some biomarkers may require sex assigned at birth to calculate reference values accurately.' ?>">
                                                        <i class="mdi mdi-information-outline text-muted"
                                                            style="cursor: help;"></i>
                                                    </span>
                                                </label>

                                                <div id="gender-radio-group">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="sex_biological" id="sex_m" value="m"
                                                            data-rules="noVacio"
                                                            data-message-no-vacio="<?= htmlspecialchars($traducciones['signup_gender_required']) ?>"
                                                            data-error-container="#gender-radio-group">
                                                        <label class="form-check-label" for="sex_m">
                                                            <?= htmlspecialchars($traducciones['signup_gender_male']) ?>
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="sex_biological" id="sex_f" value="f">
                                                        <label class="form-check-label" for="sex_f">
                                                            <?= htmlspecialchars($traducciones['signup_gender_female']) ?>
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="sex_biological" id="sex_u" value="M">
                                                        <label class="form-check-label" for="sex_u">
                                                            <?= htmlspecialchars($traducciones['signup_gender_undefined']) ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <script>
                                                document.addEventListener('DOMContentLoaded', function () {
                                                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                                                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                                                        return new bootstrap.Tooltip(tooltipTriggerEl)
                                                    })
                                                });
                                            </script>

                                            <div class="mb-3">
                                                <label for="birthday"
                                                    class="form-label"><?= htmlspecialchars($traducciones['signup_birthday_label']) ?></label>
                                                <input class="form-control" type="text" id="birthday" name="birthday"
                                                    placeholder="<?= htmlspecialchars($traducciones['birthday_placeholder'] ?? 'Selecciona tu fecha') ?>"
                                                    data-rules="noVacio"
                                                    data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="email_register"
                                                    class="form-label"><?= htmlspecialchars($traducciones['signup_email_label']) ?></label>
                                                <input class="form-control" type="email" id="email_register"
                                                    name="email"
                                                    placeholder="<?= htmlspecialchars($traducciones['signup_email_placeholder']) ?>"
                                                    data-rules="noVacio|email"
                                                    data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required']) ?>"
                                                    data-message-email="<?= htmlspecialchars($traducciones['validation_email'] ?? $traducciones['validation_required']) ?>"
                                                    data-validate-duplicate-url="check/check-email"
                                                    data-message-duplicado="<?= htmlspecialchars($traducciones['validation_email_duplicate'] ?? 'El email ya está en uso') ?>">
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="country-select"
                                                        class="form-label"><?= htmlspecialchars($traducciones['signup_country_label']) ?></label>
                                                    <div class="d-flex align-items-center">
                                                        <div data-phone-select=""></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="telephone"
                                                        class="form-label"><?= htmlspecialchars($traducciones['signup_telephone_label']) ?></label>
                                                    <input class="form-control" type="text" id="telephone"
                                                        name="telephone"
                                                        placeholder="<?= htmlspecialchars($traducciones['signup_telephone_placeholder']) ?>"
                                                        data-rules="noVacio"
                                                        data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required']) ?>"
                                                        data-validate-duplicate-url="check/check-telephone"
                                                        data-validate-masked="true"
                                                        data-message-duplicado="<?= htmlspecialchars($traducciones['validation_duplicate_phone'] ?? 'El teléfono ya está en uso') ?>" />
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="password_register"
                                                    class="form-label"><?= htmlspecialchars($traducciones['signup_password_label']) ?></label>
                                                <div class="input-group">
                                                    <input class="form-control" type="password" id="password_register"
                                                        name="password_register"
                                                        placeholder="<?= htmlspecialchars($traducciones['signup_password_placeholder']) ?>"
                                                        data-rules="noVacio"
                                                        data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required']) ?>"
                                                        data-error-container=".input-group"
                                                        data-revalidate-targets="#confirm_password">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="confirm_password"
                                                    class="form-label"><?= htmlspecialchars($traducciones['signup_confirm_password']) ?></label>
                                                <div class="input-group">
                                                    <input class="form-control" type="password" id="confirm_password"
                                                        name="confirm_password"
                                                        placeholder="<?= htmlspecialchars($traducciones['signup_confirm_password']) ?>"
                                                        data-rules="noVacio|coincideCon:#password_register"
                                                        data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required']) ?>"
                                                        data-message-coincide-con="<?= htmlspecialchars($traducciones['validation_password_match'] ?? 'Las contraseñas no coinciden') ?>"
                                                        data-error-container=".input-group">
                                                    <button class="btn btn-toogle-password btn-sm me-1 toggle-password"
                                                        data-target="confirm_password" type="button" tabindex="-1">
                                                        <i class="mdi mdi-eye-outline"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Terms and Conditions Checkbox -->
                                            <div class="mb-3">
                                                <div class="form-check" id="terms-checkbox-group">
                                                    <input class="form-check-input" type="checkbox" id="accept_terms" 
                                                        name="accept_terms"
                                                        data-rules="noVacio"
                                                        data-message-no-vacio="<?= htmlspecialchars($traducciones['terms_required']) ?>"
                                                        data-error-container="#terms-checkbox-group">
                                                    <label class="form-check-label" for="accept_terms">
                                                        <?= htmlspecialchars($traducciones['accept_terms']) ?> 
                                                        <a href="#" id="terms-link" class="text-primary" data-bs-toggle="modal" data-bs-target="#termsModal">
                                                            <?= htmlspecialchars($traducciones['terms_and_conditions']) ?>
                                                        </a>
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-0">
                                                <button class="btn btn-sign-up float-sm-end"
                                                    type="submit"><?= htmlspecialchars($traducciones['signup_button']) ?></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel"><?= htmlspecialchars($traducciones['terms_modal_title']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="lead"><?= htmlspecialchars($traducciones['terms_intro']) ?></p>
                    
                    <div class="mt-4">
                        <h6 class="fw-bold"><?= htmlspecialchars($traducciones['terms_section_1_title']) ?></h6>
                        <p><?= htmlspecialchars($traducciones['terms_section_1_content']) ?></p>
                    </div>
                    
                    <div class="mt-3">
                        <h6 class="fw-bold"><?= htmlspecialchars($traducciones['terms_section_2_title']) ?></h6>
                        <p><?= htmlspecialchars($traducciones['terms_section_2_content']) ?></p>
                    </div>
                    
                    <div class="mt-3">
                        <h6 class="fw-bold"><?= htmlspecialchars($traducciones['terms_section_3_title']) ?></h6>
                        <p><?= htmlspecialchars($traducciones['terms_section_3_content']) ?></p>
                    </div>
                    
                    <div class="mt-3">
                        <h6 class="fw-bold"><?= htmlspecialchars($traducciones['terms_section_4_title']) ?></h6>
                        <p><?= htmlspecialchars($traducciones['terms_section_4_content']) ?></p>
                    </div>
                    
                    <div class="mt-3">
                        <h6 class="fw-bold"><?= htmlspecialchars($traducciones['terms_section_5_title']) ?></h6>
                        <p><?= htmlspecialchars($traducciones['terms_section_5_content']) ?></p>
                    </div>
                    
                    <div class="mt-3">
                        <h6 class="fw-bold"><?= htmlspecialchars($traducciones['terms_section_6_title']) ?></h6>
                        <p><?= htmlspecialchars($traducciones['terms_section_6_content']) ?></p>
                    </div>
                    
                    <div class="mt-3">
                        <h6 class="fw-bold"><?= htmlspecialchars($traducciones['terms_section_7_title']) ?></h6>
                        <p><?= htmlspecialchars($traducciones['terms_section_7_content']) ?></p>
                    </div>
                    
                    <div class="mt-3">
                        <h6 class="fw-bold"><?= htmlspecialchars($traducciones['terms_section_8_title']) ?></h6>
                        <p><?= htmlspecialchars($traducciones['terms_section_8_content']) ?></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= htmlspecialchars($traducciones['terms_close']) ?></button>
                </div>
            </div>
        </div>
    </div>



    <script src="<?= BASE_URL ?>public/assets/js/vendor.min.js"></script>
    <script type="module" src="<?= BASE_URL ?>public/assets/js/app2.js"></script>

    <script src="<?= BASE_URL ?>public/assets/js/imask.js"></script>
    <script src="<?= BASE_URL ?>public/assets/libs/select2/js/select2.min.js"></script>
    <script src="<?= BASE_URL ?>public/assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="<?= BASE_URL ?>public/assets/libs/flatpickr/l10n/es.js"></script>


    <script>
        // ... (EL SCRIPT DEL LANGUAGE-SWITCHER PERMANECE IGUAL) ...
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
                let addSlash = basePath !== '/' ? '/' : '';
                newUrl = basePath + addSlash + selectedLang;
            }
            // ===== FIN DE LA NUEVA LÓGICA =====

            // Redirige a la nueva URL
            console.log(`Redirigiendo a: ${newUrl}`, basePath); // Para depuración

            window.location.href = basePath + "?lang=" + selectedLang;
        });
    </script>

    <script>
        // ... (EL SCRIPT DEL TOGGLE-PASSWORD PERMANECE IGUAL) ...
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = button.querySelector('i');
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                icon.classList.toggle('mdi-eye-outline', !isPassword);
                icon.classList.toggle('mdi-eye-off-outline', isPassword);
            });
        });
    </script>
    <script type="module">
        import { countrySelect } from "<?= BASE_URL ?>public/assets/js/components/countrySelect.js";
        countrySelect('telephone', '[data-phone-select]');
    </script>
    <script type="module">
        // ==================================================
        // SCRIPT DE FORMULARIOS MODIFICADO
        // ==================================================

        // 1. Importar el nuevo validador (se ejecutará automáticamente)
        import "<?= BASE_URL ?>public/assets/js/helpers/validarFormulario.js";

        // 2. Importar solo los helpers que aún se usan
        import { clearValidationMessages, showConfirmation } from "<?= BASE_URL ?>public/assets/js/helpers/helpers.js";

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

        $(function () {

            flatpickr("#birthday", {
                locale: "<?= ($_SESSION['idioma'] ?? 'ES') === 'ES' ? 'es' : 'en' ?>",
                maxDate: "today", // Evita que se seleccionen fechas futuras
                dateFormat: "M-d-Y" // Asegura el formato de la fecha
                // generar un placeholder


            });

            // ====== REGISTER ======
            // 3. Cambiar el listener de 'submit' a 'validation:success'
            document.getElementById('register-form').addEventListener('validation:success', function (e) {
                // 4. La validación ya está hecha. Se elimina 'validateFormFields' y el check de 'sex_biological'.

                // 5. Construir el objeto de datos.
                // NOTA: Usamos el valor 'unmaskedValue' de tu helper countrySelect para el teléfono.
                const data = {
                    first_name: $('#first_name').val(),
                    last_name: $('#last_name').val(),
                    sex_biological: $('input[name="sex_biological"]:checked').val(),
                    birthday: $('#birthday').val(),
                    email: $('#email_register').val(),
                    telephone: $('#telephone').val(),
                    password: $('#password_register').val(),
                    language: $('#language-switcher').val(),
                };

                // 6. El resto del AJAX es idéntico.
                $.ajax({
                    url: '<?= BASE_URL ?>register',
                    type: 'POST',
                    data: JSON.stringify(data),
                    contentType: 'application/json',
                    dataType: 'json',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    },
                    success: function (res) {
                        Swal.fire({
                            title: res.value ? '<?= htmlspecialchars($traducciones['alert_success_title']) ?>' : '<?= htmlspecialchars($traducciones['alert_error_title']) ?>',
                            text: res.message,
                            icon: res.value ? 'success' : 'error',
                            confirmButtonColor: '#5369f8',
                        }).then(() => {
                            if (res.value) {
                                $('#register-form')[0].reset();
                                $('#email_login').focus();
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                                // Se mantiene el helper para limpiar
                                clearValidationMessages(document.getElementById('register-form'));
                            }
                        });
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.message || '<?= htmlspecialchars($traducciones['error_generic_register']) ?>';
                        Swal.fire({
                            title: '<?= htmlspecialchars($traducciones['alert_error_title']) ?>',
                            text: msg,
                            icon: 'error',
                            confirmButtonColor: '#5369f8',
                        });
                        console.warn('[REGISTER] HTTP', xhr.status, msg, xhr);
                    },
                });
            });

            // ====== LOGIN ======
            // 7. Cambiar el listener de 'submit' a 'validation:success'
            document.getElementById('login-form').addEventListener('validation:success', function (e) {
                // 8. La validación ya está hecha. Se elimina 'validateFormFields'.

                // 9. El objeto 'data' y el AJAX son idénticos.
                const data = {
                    email: $('#email_login').val(),
                    password: $('#password_login').val(),
                    language: $('#language-switcher').val(),
                    device_id: getDeviceId(),
                    is_mobile: isMobileDevice() ? 1 : 0,
                    user_agent: navigator.userAgent
                };

                $.ajax({
                    url: '<?= BASE_URL ?>login',
                    type: 'POST',
                    data: JSON.stringify(data),
                    contentType: 'application/json',
                    dataType: 'json',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    },
                    success: function (res) {
                        if (res.value) {
                            showConfirmation({
                                type: 'success',
                                actionCallback: () => {
                                    console.log('[LOGIN OK]', res);
                                    window.location.href = "<?= BASE_URL ?>" + (res.data?.redirect || '');
                                },
                                message: {
                                    title: res.message || 'OK',
                                    confirmButtonText: 'OK'
                                }
                            });
                        } else {
                            showConfirmation({
                                type: 'error',
                                actionCallback: () => console.log('[LOGIN FAIL]', res.message),
                                message: {
                                    title: res.message || 'Login failed',
                                    confirmButtonText: 'OK'
                                }
                            });
                        }
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.message || 'Login failed';
                        showConfirmation({
                            type: 'error',
                            actionCallback: () => console.warn('[LOGIN ERROR]', xhr.status, msg, xhr),
                            message: {
                                title: msg,
                                confirmButtonText: 'OK'
                            }
                        });
                    },
                });
            });

            // 10. ELIMINAR los 'blur' listeners para 'telephone' y 'email_register'.
            // 'validarFormulario.js' maneja esto automáticamente gracias a los atributos
            // 'data-validate-duplicate-url' y los eventos 'input'/'change'.
        });
    </script>
</body>

</html>