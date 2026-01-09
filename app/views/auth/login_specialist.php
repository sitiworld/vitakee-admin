<!DOCTYPE html>
<html lang="en">

<?php include __DIR__ . '/../partials/head.php'; ?>

<head>
    <style>
        .auth-fluid {
            background-image: url('<?= BASE_URL ?>public/assets/images/specialist_login.jpg');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>


<body class="auth-fluid-pages pb-0">

    <div class="auth-fluid">
        <div class="auth-fluid-form-box vh-100 overflow-auto">
            <div class="align-items-center justify-content-center d-flex ">
                <div class="p-3">

                    <div class="auth-brand">
                        <a href="<?= BASE_URL ?>specialist" class="logo logo-dark text-center">

                            <span class="logo-lg">
                                <img src="<?= BASE_URL ?>public/assets/images/logo-index.png" alt="" height="65">
                            </span>
                        </a>
                        <a href="<?= BASE_URL ?>specialist" class="logo logo-light text-center">

                            <span class="logo-lg">
                                <img src="<?= BASE_URL ?>public/assets/images/logo-index.png" alt="" height="65">
                            </span>
                        </a>
                    </div>

                    <div class="text-center mb-4">
                        <select id="language-switcher" class="form-select mx-auto" style="width: auto;">
                            <option value="es" <?= strtolower($_SESSION['lang']) == 'es' ? 'selected' : '' ?>>Español
                            </option>
                            <option value="en" <?= strtolower($_SESSION['lang']) == 'en' ? 'selected' : '' ?>>English
                            </option>
                        </select>
                    </div>

                    <ul class="nav nav-tabs nav-bordered">
                        <li class="nav-item">
                            <a href="#tab-login" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                                <?= $traducciones['log_in_tab_specialist'] ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-signup" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                <?= $traducciones['sign_up_tab_specialist'] ?>
                            </a>
                        </li>
                    </ul>


                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-login">
                            <p class="text-muted mb-3"><?= $traducciones['login_prompt_specialist'] ?></p>

                            <form id="loginForm" data-validation="reactive" novalidate>
                                <div class="mb-3">
                                    <label for="loginEmailaddress"
                                        class="form-label"><?= $traducciones['email_address_label_specialist'] ?></label>
                                    <input class="form-control" type="email" id="loginEmailaddress"
                                        name="loginEmailaddress"
                                        placeholder="<?= $traducciones['enter_email_placeholder_specialist'] ?>"
                                        data-rules="noVacio|email"
                                        data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>"
                                        data-message-email="<?= htmlspecialchars($traducciones['validation_email'] ?? 'Email inválido') ?>">
                                </div>
                                <div class="mb-3">
                                    <a href='<?= BASE_URL ?>reset_password/specialist' id="forgotPasswordLink" ;
                                        class="text-muted float-end"><small><?= $traducciones['forgot_password_link_specialist'] ?></small></a>
                                    <label for="loginPassword"
                                        class="form-label"><?= $traducciones['password_label_specialist'] ?></label>

                                    <div class="input-group">
                                        <input class="form-control" type="password" id="loginPassword"
                                            name="loginPassword"
                                            placeholder="<?= $traducciones['enter_password_placeholder_specialist'] ?>"
                                            data-rules="noVacio"
                                            data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>"
                                            data-error-container=".input-group">

                                        <button class="btn btn-toogle-password btn-sm me-1 toggle-password"
                                            data-target="loginPassword" type="button" id="togglePassword" tabindex="-1"
                                            style="background-color: #2852af;">
                                            <i class="mdi mdi-eye-outline" id="toggleIcon"></i>
                                        </button>
                                    </div>

                                </div>

                                <div class="text-center d-grid">
                                    <button class="btn btn-sign-up float-sm-end signup-button" type="submit"
                                        style="background-color: #2852af;"><?= $traducciones['login_button_text_specialist'] ?></button>
                                </div>

                            </form>
                        </div>
                        <div class="tab-pane fade" id="tab-signup">
                            <p class="text-muted mb-3"><?= $traducciones['signup_prompt_specialist'] ?></p>

                            <form id="specialistRegisterForm" data-validation="reactive" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="specialistFirstName"
                                            class="form-label"><?= $traducciones['first_name_label_specialist'] ?></label>
                                        <input class="form-control" type="text" id="specialistFirstName"
                                            name="specialistFirstName"
                                            placeholder="<?= $traducciones['enter_first_name_placeholder_specialist'] ?>"
                                            data-rules="noVacio"
                                            data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="specialistLastName"
                                            class="form-label"><?= $traducciones['last_name_label_specialist'] ?></label>
                                        <input class="form-control" type="text" id="specialistLastName"
                                            name="specialistLastName"
                                            placeholder="<?= $traducciones['enter_last_name_placeholder_specialist'] ?>"
                                            data-rules="noVacio"
                                            data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="specialistEmailaddress"
                                        class="form-label"><?= $traducciones['email_address_label_specialist'] ?></label>
                                    <input class="form-control" type="email" id="specialistEmailaddress" name="email"
                                        placeholder="<?= $traducciones['enter_email_placeholder_specialist'] ?>"
                                        data-rules="noVacio|email"
                                        data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>"
                                        data-message-email="<?= htmlspecialchars($traducciones['validation_email'] ?? 'Email inválido') ?>"
                                        data-validate-duplicate-url="specialist/check-email"
                                        data-message-duplicado="<?= htmlspecialchars($traducciones['email_already_registered'] ?? 'Email ya en uso') ?>">
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="country-select"
                                            class="form-label"><?= $traducciones['country_label_specialist'] ?></label>
                                        <div class="d-flex align-items-center">
                                            <div data-phone-select=""></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="specialistTelephone" id="signup_telephone_label"
                                            class="form-label"><?= $traducciones['telephone_label_specialist'] ?></label>
                                        <input class="form-control" type="text" id="specialistTelephone"
                                            name="telephone"
                                            placeholder="<?= $traducciones['telephone_placeholder_specialist'] ?>"
                                            data-rules="noVacio|longitudMinima:8"
                                            data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>"
                                            data-message-longitud-minima="<?= htmlspecialchars($traducciones['validation_phone_min_length'] ?? 'Teléfono muy corto') ?>"
                                            data-validate-duplicate-url="specialist/check-telephone"
                                            data-message-duplicado="<?= htmlspecialchars($traducciones['telephone_already_used'] ?? 'Teléfono ya en uso') ?>"
                                            data-validate-masked="true" />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="specialistSpecialty"
                                            class="form-label"><?= $traducciones['specialty_label_specialist'] ?></label>

                                        <div class="input-group">
                                            <select class="form-select" id="specialistSpecialty"
                                                name="specialistSpecialty" style="width: 100%;" data-rules="noVacio"
                                                data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>"
                                                data-error-container=".input-group">
                                                <option value="">
                                                    <?= $traducciones['select_specialty_option_specialist'] ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="specialistTitle"
                                            class="form-label"><?= $traducciones['title_label_specialist'] ?></label>
                                        <div class="input-group">
                                            <select class="form-select" id="specialistTitle" name="specialistTitle"
                                                style="width: 100%;" data-rules="noVacio"
                                                data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>"
                                                data-error-container=".input-group">
                                                <option value=""><?= $traducciones['select_title_option_specialist'] ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                </div>


                                <div class="mb-3">
                                    <label for="password_register"
                                        class="form-label"><?= $traducciones['password_label_specialist'] ?></label>
                                    <div class="input-group">
                                        <input class="form-control" type="password" id="password_register"
                                            placeholder="<?= $traducciones['enter_password_placeholder_specialist'] ?>"
                                            name="password_register" data-error-container=".input-group"
                                            data-rules="noVacio|longitudMinima:8"
                                            data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>"
                                            data-message-longitud-minima="<?= htmlspecialchars($traducciones['validation_min_length_8'] ?? 'Mínimo 8 caracteres') ?>"
                                            data-revalidate-targets="#confirm_password">
                                        <button class="btn btn-toogle-password btn-sm me-1 toggle-password"
                                            data-target="password_register" type="button" id="togglePassword"
                                            tabindex="-1" style="background-color: #2852af;">
                                            <i class="mdi mdi-eye-outline" id="toggleIcon"></i>
                                        </button>
                                    </div>

                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password"
                                        class="form-label signup-password-label"><?= $traducciones['signup_confirm_password'] ?></label>
                                    <div class="input-group">
                                        <input class="form-control" type="password" id="confirm_password"
                                            name="confirm_password"
                                            placeholder="<?= $traducciones['signup_confirm_password'] ?>"
                                            data-error-container=".input-group"
                                            data-rules="noVacio|coincideCon:#password_register"
                                            data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>"
                                            data-message-coincide-con="<?= htmlspecialchars($traducciones['validation_password_match'] ?? 'Las contraseñas no coinciden') ?>">
                                        <button class="btn btn-toogle-password btn-sm me-1 toggle-password"
                                            data-target="confirm_password" type="button" tabindex="-1"
                                            style="background-color: #2852af;">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="text-center d-grid">
                                    <button class="btn btn-sign-up waves-effect waves-light" type="submit"
                                        style="background-color: #2852af;">
                                        <?= $traducciones['signup_button_text_specialist'] ?> </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
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
    <script type="module">
        import { countrySelect } from "<?= BASE_URL ?>public/assets/js/components/countrySelect.js";
        countrySelect('specialistTelephone', '[data-phone-select]')
    </script>



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

        // 1. Importar el nuevo validador
        import "<?= BASE_URL ?>public/assets/js/helpers/validarFormulario.js";

        // 2. Quitar 'validateFormFields', 'validateFieldAsync' y 'clearValidationMessages'
        import { hideLoader, showLoader, showConfirmation } from "<?= BASE_URL ?>public/assets/js/helpers/helpers.js";
        import { selectSpecialisties, selectTitles } from "<?= BASE_URL ?>public/assets/js/apiConfig.js";


        // --- OBTENER DATOS Y LLENAR LOS SELECTS (Sin cambios) ---
        const currentLang = '<?= $_SESSION['lang'] ?? 'en' ?>';
        function populateSelect(selectId, data, lang) {
            const selectElement = $(`#${selectId}`);
            if (!selectElement.length || !Array.isArray(data)) return;

            // Limpiar opciones anteriores
            selectElement.empty();

            // --- CAMBIO ---
            // Agregar opción en blanco (value="" y texto vacío).
            // Esto es necesario para que el placeholder de Select2 funcione.
            selectElement.append(new Option('', '', true, true));

            // Determinar claves según el tipo de select
            const nameKey = lang === 'es' ? 'name_es' : 'name_en';
            let idKey = 'id'; // default
            if (selectId.toLowerCase().includes('specialty')) idKey = 'specialty_id';
            if (selectId.toLowerCase().includes('title')) idKey = 'title_id';

            data.forEach(item => {
                if (item[idKey] && item[nameKey]) {
                    const option = new Option(item[nameKey], item[idKey]);
                    selectElement.append(option);
                }
            });

            // Asegurarse de que el valor por defecto (vacío) esté seleccionado
            // selectElement.val(null).trigger('change');
        }

        Promise.all([
            selectSpecialisties(),
            selectTitles()
        ]).then(([specialtiesResponse, titlesResponse]) => {
            if (specialtiesResponse?.value) {
                populateSelect('specialistSpecialty', specialtiesResponse.data, currentLang);
            }
            if (titlesResponse?.value) {
                populateSelect('specialistTitle', titlesResponse.data, currentLang);
            }
            $('#specialistSpecialty, #specialistTitle').select2({
                placeholder: currentLang === 'es' ? 'Seleccione...' : 'Select...',
                width: '100%',
                allowClear: true
            });
        }).catch(error => {
            console.error("Error al cargar datos para los selects:", error);
        });


        // 3. ELIMINAR los 'blur' listeners
        // document.getElementById('specialistTelephone').addEventListener('blur', ...);
        // document.getElementById('specialistEmailaddress').addEventListener('blur', ...);


        $(document).ready(function () {

            // 4. REEMPLAZAR 'submit' por 'validation:success' para LOGIN
            document.getElementById('loginForm').addEventListener('validation:success', function (e) {

                // 5. Eliminada la validación manual
                const email = $('#loginEmailaddress').val();
                const password = $('#loginPassword').val();

                $.ajax({
                    url: '<?= BASE_URL ?>specialist/login',
                    type: 'POST',
                    dataType: 'json',
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
                        if (response.value) {
                            showConfirmation({
                                type: 'success', actionCallback: () => {
                                    window.location.href = "<?= BASE_URL ?>" + response.data.redirect;
                                },
                                message: {
                                    title: response.message,
                                    confirmButtonText: 'OK'
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
                        showConfirmation({
                            type: 'error', actionCallback: () => {
                                console.warn('Login failed: ' + xhr.responseJSON.message);
                            },
                            message: {
                                title: xhr.responseJSON?.message || 'Error',
                                confirmButtonText: 'OK'
                            }
                        });
                    }
                });
            });

            // 6. REEMPLAZAR 'submit' por 'validation:success' para REGISTRO
            document.getElementById('specialistRegisterForm').addEventListener('validation:success', function (e) {

                // 7. Eliminada la validación manual
                const firstName = $('#specialistFirstName').val();
                const lastName = $('#specialistLastName').val();
                const email = $('#specialistEmailaddress').val();

                // 8. Usar el valor 'unmaskedValue' del helper countrySelect
                const telephone = window.countrySelectMasks?.['specialistTelephone']?.unmaskedValue || $('#specialistTelephone').val();

                const password = $('#password_register').val();
                const specialty = $('#specialistSpecialty').val();
                const title = $('#specialistTitle').val();

                $.ajax({
                    url: '<?= BASE_URL ?>specialist/register',
                    type: 'POST',
                    dataType: 'json',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        first_name: firstName,
                        last_name: lastName,
                        email: email,
                        phone: telephone, // Usar el valor limpio
                        password: password,
                        specialty_id: specialty,
                        title_id: title,
                        language: currentLang
                    }),
                    success: function (response) {
                        if (response.value) {
                            showConfirmation({
                                type: 'success', actionCallback: () => {
                                    $('a[href="#tab-login"]').tab('show');
                                    $('#specialistRegisterForm')[0].reset();
                                    // 9. Limpiar selects de Select2
                                    $('#specialistSpecialty, #specialistTitle').val(null).trigger('change');
                                    // 10. Limpiar errores (el helper no lo hace en reset)
                                    // (Opcional, pero recomendado si 'clearValidationMessages' ya no se importa)
                                    document.getElementById('specialistRegisterForm').querySelectorAll('.error-message').forEach(el => el.remove());
                                    document.getElementById('specialistRegisterForm').querySelectorAll('.error').forEach(el => el.classList.remove('error'));
                                }
                            });
                        } else {
                            showConfirmation({
                                type: 'error', actionCallback: () => {
                                    console.log('Registration failed: ' + response.message);
                                },
                                message: {
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                }
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        showConfirmation({
                            type: 'error', actionCallback: () => {
                                console.warn('Registration failed: ' + xhr.responseJSON.message);
                            },
                            message: {
                                title: xhr.responseJSON?.message || 'Error',
                                confirmButtonText: 'OK'
                            }
                        });
                    }
                });
            });
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