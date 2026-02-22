<head>

    <style>
        /* Estilo para que la imagen de perfil en la card sea un puntero */
        #profile-image {
            cursor: pointer;
        }

        /* Tabs con iconos y negrita al estar activa */
        .nav-tabs .nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
            /* espacio entre icono y texto */
            font-weight: 500;
            /* normal */
            transition: color .2s ease;
        }

        .nav-tabs .nav-link i {
            font-size: 18px;
            line-height: 1;
        }

        .nav-tabs .nav-link.active {
            font-weight: 700;
            /* negrita cuando está activa */
        }
    </style>
</head>

<div class="container-fluid">

    <div class="card col-lg-8">
        <div class="card-body">
            <div id="toolbar mb-2">
                <h4 class="page-title m-1"><?= $traducciones['page_title_profile'] ?></h4>


                <!-- Tabs del perfil con iconos e indicador de activo -->
                <ul class="nav nav-tabs" id="myProfileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="profile-tab" class="nav-link text-gray active" id="profile-tab" data-bs-toggle="tab"
                            data-bs-target="#profile-tab-pane" role="tab" aria-controls="profile-tab-pane"
                            aria-selected="true">
                            <i class="mdi mdi-account-circle-outline"></i>
                            <?= $traducciones['profile'] ?>
                        </a>
                    </li>

                    <li class="nav-item" role="presentation">
                        <a href="security-tab" class="nav-link text-gray" id="security-tab" data-bs-toggle="tab"
                            data-bs-target="#security-tab-pane" role="tab" aria-controls="security-tab-pane"
                            aria-selected="false" tabindex="-1">
                            <i class="mdi mdi-shield-lock-outline"></i>
                            <?= $traducciones['security'] ?>
                        </a>
                    </li>
                </ul>

            </div>

            <div class="tab-content p-0" id="myProfileTabsContent">
                <div class="tab-pane fade show active" id="profile-tab-pane" role="tabpanel"
                    aria-labelledby="profile-tab" tabindex="0">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <img class="d-flex me-3 rounded-circle avatar-lg" id="profile-image"
                                src="public/assets/images/users/user_boy.jpeg" alt="Admin Profile Image">
                            <div class="w-100">
                                <h4 class="mt-0 mb-1" id="profile-name"></h4>
                                <p class="text-muted" id="profile-role">
                                    <?= $traducciones['administrator'] ?? 'Administrator' ?>
                                </p>
                            </div>
                            <a href="javascript:void(0);" class="editUserBtn action-icon" id="btn-edit"><i
                                    class="mdi mdi-pencil"></i></a>
                        </div>

                        <h5 class="mb-3 mt-4 text-uppercase bg-light p-2"><i class="mdi mdi-account-circle me-1"></i>
                            <?= $traducciones['personal_information'] ?? 'Personal Information' ?>
                        </h5>
                        <div class="">
                            <h4 class="font-13 text-muted text-uppercase mb-1">
                                <?= $traducciones['email_label'] ?>
                            </h4>
                            <p class="mb-3" id="profile-email"></p>

                            <h4 class="font-13 text-muted text-uppercase mb-1">
                                <?= $traducciones['telephone_label'] ?>:
                            </h4>
                            <p class="mb-3" id="profile-telephone"></p>

                            <p class="mb-3" id="profile-telephone"></p>
                            <div id="additional-contacts-container" class="mt-2" style="display: none;">
                                <h4 class="font-13 text-muted text-uppercase mb-1 border-top pt-2">
                                    <?= $traducciones['additional_contacts'] ?? 'Contactos Adicionales' ?>:
                                </h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label
                                            class="form-label fw-bold small text-muted"><?= $traducciones['emails'] ?? 'Emails' ?></label>
                                        <div id="additional-emails-list">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label
                                            class="form-label fw-bold small text-muted"><?= $traducciones['telephones'] ?? 'Teléfonos' ?></label>
                                        <div id="additional-phones-list">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h4 class="font-13 text-muted text-uppercase mb-1">
                                <?= $traducciones['timezone'] ?? 'TimeZone' ?>:
                            </h4>
                            <p class="mb-3" id="profile-timezone"></p>
                            <h4 class="font-13 text-muted text-uppercase mb-1">
                                <?= $traducciones['metrical_system'] ?? 'Measurement System' ?>:
                            </h4>
                            <div class="mb-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input system-update-radio" type="radio"
                                        name="card_height_system" id="cardHeightAmerican" value="us">
                                    <label class="form-check-label" for="cardHeightAmerican">
                                        <?= $traducciones['height_american'] ?? 'Americano' ?>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input system-update-radio" type="radio"
                                        name="card_height_system" id="cardHeightEuropean" value="eu">
                                    <label class="form-check-label" for="cardHeightEuropean">
                                        <?= $traducciones['height_european'] ?? 'Europeo' ?>
                                    </label>
                                </div>
                            </div>

                            <h4 class="font-13 text-muted text-uppercase mb-1">
                                <?= $traducciones['updated_date'] ?? 'Updated Date' ?>:
                            </h4>
                            <p class="mb-0" id="profile-updated-date"></p>
                        </div>
                    </div>

                </div>
                <div class="tab-pane fade" id="security-tab-pane" role="tabpanel" aria-labelledby="security-tab"
                    tabindex="0">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            <?= $traducciones['modal_security_title'] ?? 'Security Questions' ?>
                        </h4>

                        <div id="setup-view">
                            <p><?= $traducciones['no_questions_prompt'] ?? 'You have not set up your security questions yet.' ?>
                            </p>
                            <button id="btn-setup" class="btn btn-add">
                                <i class="mdi mdi-plus"></i>
                                <?= $traducciones['register_questions'] ?? 'Set up Questions' ?>
                            </button>
                        </div>

                        <div id="display-view" class="d-none">
                            <div class="question-block">
                                <p class="question-text fw-bold" id="display_question1"></p>
                                <p class="answer-text fst-italic" id="display_answer1"></p>
                            </div>
                            <div class="question-block">
                                <p class="question-text fw-bold" id="display_question2"></p>
                                <p class="answer-text fst-italic" id="display_answer2"></p>
                            </div>
                            <button id="btn-edit-questions" class="btn btn-pencil mt-2 float-end">
                                <i class="mdi mdi-pencil-outline action-icon"></i>
                                <?= $traducciones['profile_edit'] ?? 'Edit' ?>
                            </button>
                        </div>

                        <div id="form-view-questions" class="d-none">
                            <form id="security-question-form" data-validation="reactive" novalidate>
                                <input type="hidden" id="security_id" name="security_id">
                                <div class="mb-3">
                                    <label for="question1"
                                        class="form-label"><?= $traducciones['question1'] ?? 'Security Question 1' ?></label>
                                    <input class="form-control" type="text" id="question1" name="question1"
                                        placeholder="<?= $traducciones['enter_first_question'] ?? 'Enter your first question' ?>"
                                        data-rules="noVacio"
                                        data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="answer1"
                                        class="form-label"><?= $traducciones['answer1'] ?? 'Answer 1' ?></label>
                                    <input class="form-control" type="text" id="answer1" name="answer1"
                                        placeholder="<?= $traducciones['enter_answer'] ?? 'Enter answer' ?>"
                                        data-rules="noVacio"
                                        data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>">
                                </div>
                                <hr class="my-4">
                                <div class="mb-3">
                                    <label for="question2"
                                        class="form-label"><?= $traducciones['question2'] ?? 'Security Question 2' ?></label>
                                    <input class="form-control" type="text" id="question2" name="question2"
                                        placeholder="<?= $traducciones['enter_second_question'] ?? 'Enter your second question' ?>"
                                        data-rules="noVacio"
                                        data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="answer2"
                                        class="form-label"><?= $traducciones['answer2'] ?? 'Answer 2' ?></label>
                                    <input class="form-control" type="text" id="answer2" name="answer2"
                                        placeholder="<?= $traducciones['enter_answer'] ?? 'Enter answer' ?>"
                                        data-rules="noVacio"
                                        data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>">
                                </div>
                                <div class="mt-4 d-flex justify-content-end gap-2">
                                    <button id="security-submit-btn" type="submit" class="btn btn-save"><i
                                            class="mdi mdi-content-save-outline"></i>
                                        <?= $traducciones['save'] ?? 'Save Questions' ?></button>
                                    <button id="btn-cancel-edit-questions" type="button" class="btn btn-cancel"><i
                                            class="mdi mdi-cancel"></i>
                                        <?= $traducciones['cancel'] ?? 'Cancel' ?></button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>






    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-body p-0 d-flex justify-content-center">
                    <img src="" alt="Profile Image Preview" style="max-width:100%; height:80vh; padding: 1rem;"
                        class="img-fluid rounded">
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                        <i class="mdi mdi-close"></i><?= $traducciones['close'] ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editUserForm" data-validation="reactive" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">
                            <?= $traducciones['edit_modal_title_profile'] ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="user_id" name="user_id">

                        <div class="mb-3">
                            <label for="first_name" class="form-label"><?= $traducciones['first_name_label'] ?></label>
                            <input type="text" id="first_name" name="first_name" class="form-control"
                                data-toggle="input-mask" data-mask-format="LLLLLLLLLLLLLLLLLLLLLLLLLLL"
                                data-rules="noVacio"
                                data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label"><?= $traducciones['last_name_label'] ?></label>
                            <input type="text" id="last_name" name="last_name" class="form-control"
                                data-toggle="input-mask" data-mask-format="LLLLLLLLLLLLLLLLLLLLLLLLLLL"
                                data-rules="noVacio"
                                data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>">
                        </div>

                        <div class="mb-3">
                            <label
                                class="form-label"><?= $traducciones['metrical_system'] ?? 'Sistema de medida' ?></label>
                            <div id="heightSystemOptions">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="height_system"
                                        id="heightAmerican" value="us">
                                    <label class="form-check-label"
                                        for="heightAmerican"><?= $traducciones['height_american'] ?? 'Americano' ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="height_system"
                                        id="heightEuropean" value="eu">
                                    <label class="form-check-label"
                                        for="heightEuropean"><?= $traducciones['height_european'] ?? 'Europeo' ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label"><?= $traducciones['email_label'] ?></label>
                            <input type="email" id="email" name="email" class="form-control" data-rules="noVacio|email"
                                data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>"
                                data-message-email="<?= htmlspecialchars($traducciones['validation_email'] ?? 'Email inválido') ?>"
                                data-validate-duplicate-url="check-email-administrator"
                                data-message-duplicado="<?= htmlspecialchars($traducciones['validation_email_duplicate'] ?? 'Email ya en uso') ?>"
                                data-record-id-selector="#user_id">
                        </div>

                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label class="form-label"
                                    for="country-select"><?= $traducciones['signup_country'] ?></label>
                                <div data-phone-select=""></div>
                            </div>
                            <div class="col-md-6">
                                <label for="telephone"
                                    class="form-label"><?= $traducciones['telephone_label'] ?></label>
                                <input type="text" id="telephone" name="telephone" class="form-control"
                                    data-rules="noVacio|longitudMinima:8"
                                    data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>"
                                    data-message-longitud-minima="<?= htmlspecialchars($traducciones['validation_phone_min_length'] ?? 'Teléfono muy corto') ?>"
                                    data-validate-duplicate-url="check-telephone-administrator"
                                    data-message-duplicado="<?= htmlspecialchars($traducciones['validation_duplicate_phone'] ?? 'Teléfono ya en uso') ?>"
                                    data-record-id-selector="#user_id" data-validate-masked="true">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="profile_image" class="form-label"><?= $traducciones['profile_image'] ?></label>
                            
                            <div class="mb-2 d-flex align-items-center gap-3">
                                <div>
                                    <p class="text-muted small mb-1 fw-bold"><?= $traducciones['current_image'] ?? 'Imagen actual' ?>:</p>
                                    <img id="current_profile_image" src="" alt="Current Profile" class="img-thumbnail rounded-circle" style="width: 80px; height: 80px; object-fit: cover; display: none;">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" id="profile_image" name="profile_image" class="form-control"
                                        accept="image/jpeg,image/png,image/webp"
                                        data-rules="esTipoArchivo:image/jpeg,image/png,image/webp|tamanoMaximoArchivo:5"
                                        data-message-es-tipo-archivo="<?= htmlspecialchars($traducciones['validation_file_type'] ?? 'Solo se permiten imágenes (jpg, png, webp)') ?>"
                                        data-message-tamano-maximo-archivo="<?= htmlspecialchars($traducciones['validation_file_size'] ?? 'La imagen no debe superar los 5MB') ?>">
                                </div>
                            </div>
                            
                            <div class="mt-2 text-center" style="width: 100%;">
                                <img id="preview_cropper" style="display:none; max-width:100%; margin: 0 auto;">
                            </div>
                            <div class="mt-2 text-center" id="cropper-buttons" style="display:none;">
                                <button type="button" id="flipHorizontal" class="btn btn-sm btn-add me-2">
                                    <i class="mdi mdi-flip-horizontal"></i> <?= $traducciones['flip_horizontal'] ?? 'Horizontal' ?>
                                </button>
                                <button type="button" id="flipVertical" class="btn btn-sm btn-add">
                                    <i class="mdi mdi-flip-vertical"></i> <?= $traducciones['flip_vertical'] ?? 'Vertical' ?>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="timezoneSelect"
                                class="form-label"><?= $traducciones['timezone_label'] ?? 'Zona horaria' ?></label>
                            <select name="timezone" id="timezoneSelect" class="form-control select2"
                                style="width: 100%;" data-rules="noVacio"
                                data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>">
                                <?php
                                $timezones = DateTimeZone::listIdentifiers();
                                foreach ($timezones as $tz) {
                                    $dt = new DateTime('now', new DateTimeZone($tz));
                                    $offset = $dt->format('P');
                                    echo "<option value=\"$tz\">(GMT $offset) $tz</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <hr class="my-3">
                        <h5 class="mb-3 text-uppercase bg-light p-2 rounded">
                            <i class="mdi mdi-card-account-phone-outline me-1"></i>
                            <?= $traducciones['additional_contacts'] ?? 'Contactos Adicionales' ?>
                        </h5>
                        <div class="row">
                            <div class="mb-3 col-lg-6">
                                <label class="form-label fw-bold"><?= $traducciones['emails'] ?? 'Emails' ?></label>
                                <div id="email-list" class="vstack gap-2"></div>
                                <button type="button" class="btn btn-sm btn-add mt-2" id="btn-add-email"><?= $traducciones['add_email'] ?? 'Add Email' ?></button>
                            </div>
                            <div class="mb-3 col-lg-6">
                                <label
                                    class="form-label fw-bold"><?= $traducciones['telephones'] ?? 'Teléfonos' ?></label>
                                <div id="telephone-list" class="vstack gap-2"></div>
                                <button type="button" class="btn btn-sm btn-add mt-2"
                                    id="btn-add-telephone"><?= $traducciones['add_telephone'] ?? 'Add Telephone' ?></button>
                            </div>
                        </div>
                        <hr class="my-3">

                        <div class="mb-3">
                            <label for="password" class="form-label"><?= $traducciones['password_label'] ?></label>
                            <div class="input-group">
                                <input type="password" id="password" data-error-container=".input-group" name="password"
                                    class="form-control"
                                    placeholder="<?= htmlspecialchars($traducciones['password_label']) ?>"
                                    data-rules="longitudMinima:8"
                                    data-message-longitud-minima="<?= htmlspecialchars($traducciones['validation_min_length_8'] ?? 'Mínimo 8 caracteres') ?>"
                                    data-revalidate-targets="#confirm_password">
                                <button class="btn btn-toogle-password btn-sm me-1 toggle-password"
                                    data-target="password" type="button" tabindex="-1">
                                    <i class="mdi mdi-eye-outline"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password"
                                class="form-label"><?= htmlspecialchars($traducciones['signup_confirm_password']) ?></label>
                            <div class="input-group">
                                <input class="form-control" type="password" id="confirm_password"
                                    name="confirm_password"
                                    placeholder="<?= htmlspecialchars($traducciones['signup_confirm_password']) ?>"
                                    data-error-container=".input-group" data-rules="coincideCon:#password"
                                    data-message-coincide-con="<?= htmlspecialchars($traducciones['validation_password_match'] ?? 'Las contraseñas no coinciden') ?>">
                                <button class="btn btn-toogle-password btn-sm me-1 toggle-password"
                                    data-target="confirm_password" type="button" tabindex="-1">
                                    <i class="mdi mdi-eye-outline"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-save"><i class="mdi mdi-content-save-outline"></i>
                            <?= $traducciones['save'] ?></button>
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal"><i
                                class="mdi mdi-cancel"></i> <?= $traducciones['cancel'] ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<body>

    <script src="public/assets/js/logout.js"></script>
    <script>
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
        // ============================
        //  IMPORTACIÓN DE MÓDULOS
        // ============================
        import {
            countrySelect
        } from "./public/assets/js/components/countrySelect.js";
        import { initTimezoneSelect } from './public/assets/js/components/timezoneSelect.js';
        // Al cargar el DOM


        $(document).ready(function () {
            // ============================
            //  VARIABLES GLOBALES Y ESTADO
            // ============================

            initTimezoneSelect('timezoneSelect', '#editUserModal');

            function addEmailRow(data = {}) {
                const emailList = document.getElementById('email-list');
                const newIndex = `email_${emailList.children.length}_${Date.now()}`;
                const isPrimary = data.is_primary == 1 || (emailList.children.length === 0 && data.is_primary === undefined);
                const isActive = data.is_active == 1 || data.is_active === undefined;

                const rowWrapper = document.createElement('div');
                rowWrapper.className = 'email-row-wrapper border rounded p-2 mb-2';

                rowWrapper.innerHTML = `
      <div class="mb-2">
          <input type="hidden" class="contact-email-id" value="${data.contact_email_id || ''}">
          <input
              type="email"
              name="email_contact"
              class="form-control form-control-sm email-input"
              placeholder="${traducciones.email_placeholder || 'example@email.com'}"
              id="email-input-${newIndex}"
              value="${data.email || ''}"
              data-rules="noVacio|email"
              data-message-no-vacio="${traducciones.validation_required || 'Required'}"
              data-message-email="${traducciones.validation_email_format || 'Invalid email'}"
              data-validate-duplicate-url="contact-emails/check-email?entity_type=administrator"
              data-message-duplicado="${traducciones.validation_email_exists || 'Email already exists'}"
              data-record-id-selector="#user_id"
              data-initial-value="${data.email || ''}">
      </div>
      <div class="d-flex justify-content-between align-items-center">
          <div class="d-flex gap-3 align-items-center">
              <div class="form-check">
                  <input class="form-check-input" type="radio" name="email_is_primary" id="email_primary_${newIndex}" ${isPrimary ? 'checked' : ''}>
                  <label class="form-check-label small" for="email_primary_${newIndex}">${traducciones.primary || 'Primary'}</label>
              </div>
              <div class="d-flex align-items-center">
                  <input type="checkbox" class="js-switch-small email-active-switch" ${isActive ? 'checked' : ''}>
                  <label class="small ms-1">${traducciones.active || 'Active'}</label>
              </div>
          </div>
          <button class="btn action-icon btn-sm btn-remove-row" type="button" title="${traducciones.remove_item || 'Remove'}">
              <i class="mdi mdi-trash-can-outline"></i>
          </button>
      </div>
    `;
                emailList.appendChild(rowWrapper);
                new Switchery(rowWrapper.querySelector('.js-switch-small'), {
                    size: 'small',
                    color: systemColors.switchColorOn,
                    secondaryColor: systemColors.switchColorOff,
                });
            }
            function addTelephoneRow(data = {}) {
                const telephoneList = document.getElementById('telephone-list');
                const newIndex = `tel_${telephoneList.children.length}_${Date.now()}`;
                const isPrimary = data.is_primary == 1 || (telephoneList.children.length === 0 && data.is_primary === undefined);
                const isActive = data.is_active == 1 || data.is_active === undefined;
                const telephoneInputId = `telephone-input-${newIndex}`;

                const rowWrapper = document.createElement('div');
                rowWrapper.className = 'telephone-row-wrapper border rounded p-2 mb-2';
                rowWrapper.id = `tel-row-${newIndex}`;

                rowWrapper.innerHTML = `
      <div class="row gx-2 mb-2">
          <div class="col-6" id="country-select-container-${newIndex}"></div>
          <div class="col-6">
              <input type="hidden" class="contact-phone-id" value="${data.contact_phone_id || ''}">
              <input type="tel" name="telephone_contact" class="form-control form-control-sm telephone-input" id="${telephoneInputId}" placeholder="${traducciones.phone_placeholder || '555 123-4567'}">
          </div>
      </div>
      <div class="d-flex justify-content-between align-items-center">
          <div class="d-flex gap-3 align-items-center">
              <div class="form-check">
                  <input class="form-check-input" type="radio" name="telephone_is_primary" id="tel_primary_${newIndex}" ${isPrimary ? 'checked' : ''}>
                  <label class="form-check-label small" for="tel_primary_${newIndex}">${traducciones.primary || 'Primary'}</label>
              </div>
              <div class="d-flex align-items-center">
                  <input type="checkbox" class="js-switch-small telephone-active-switch" ${isActive ? 'checked' : ''}>
                  <label class="small ms-1">${traducciones.active || 'Active'}</label>
              </div>
          </div>
          <button class="btn action-icon btn-sm btn-remove-row" type="button" title="${traducciones.remove_item || 'Remove'}">
              <i class="mdi mdi-trash-can-outline"></i>
          </button>
      </div>
    `;
                telephoneList.appendChild(rowWrapper);

                const telephoneInput = document.getElementById(telephoneInputId);
                telephoneInput.dataset.rules = 'noVacio|longitudMinima:8';
                telephoneInput.dataset.messageNoVacio = traducciones.validation_required || 'Required';
                telephoneInput.dataset.messageLongitudMinima = traducciones.validation_phone_min_length || 'Phone too short';
                telephoneInput.dataset.errorContainer = `#${rowWrapper.id} .col-6:last-child`;
                telephoneInput.dataset.validateDuplicateUrl = 'contact-phones/check-telephone?entity_type=administrator';
                telephoneInput.dataset.messageDuplicado = traducciones.validation_phone_exists || 'Phone already exists';
                telephoneInput.dataset.recordIdSelector = '#user_id';
                telephoneInput.dataset.initialValue = data.phone_number || '';
                telephoneInput.dataset.validateMasked = 'true';

                new Switchery(rowWrapper.querySelector('.js-switch-small'), {
                    size: 'small',
                    color: systemColors.switchColorOn,
                    secondaryColor: systemColors.switchColorOff,
                });

                countrySelect(
                    telephoneInputId,
                    `#country-select-container-${newIndex}`,
                    data.phone_number || null,
                    '#editUserModal .modal-body'
                );
            }
            // Al abrir el modal, si necesitas reinicializar:
            $('#editUserModal').on('shown.bs.modal', function () {
                initTimezoneSelect('timezoneSelect', this);
            });
            let user = {}; // Contendrá los datos del administrador
            let cropper;
            let scaleX = 1;
            let currentRotation = 0;

            const lang = "<?= strtoupper($_SESSION['idioma'] ?? 'EN') ?>";
            const userId = '<?= $_SESSION['user_id'] ?>';
            const traducciones = <?= json_encode($traducciones) ?>;

            const imagePreviewElement = document.getElementById('preview_cropper');
            const imageInputElement = document.getElementById('profile_image');

            // ============================
            //  FUNCIONES AUXILIARES
            // ============================
            function formatFullDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                const utcDate = new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate());
                const options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                return utcDate.toLocaleDateString(lang === 'ES' ? 'es-ES' : 'en-US', options);
            };

            // ============================
            //  CARGA DE DATOS DEL PERFIL
            // ============================
            function loadUserProfile() {
                $.ajax({
                    url: `<?= BASE_URL ?>administrator_get/${userId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.value === true) {
                            user = response.data;
                            const baseUrl = "<?= BASE_URL ?>";
                            let user_image_path = user.profile_image_url || "<?php echo $_SESSION['user_image']; ?>";
                            if (!user_image_path.startsWith('http') && !user_image_path.startsWith('/')) {
                                user_image_path = baseUrl + user_image_path;
                            } else if (user_image_path.startsWith('/') && baseUrl.endsWith('/')) {
                                user_image_path = baseUrl + user_image_path.substring(1);
                            }

                            $('#profile-image').attr('src', user_image_path);
                            $('#topbar-profile-image').attr('src', user_image_path);
                            $('#profile-name').text(user.first_name + ' ' + user.last_name);

                            // --- INICIO DE MODIFICACIONES ---
                            if (user.email) {
                                $('#profile-email').html(`<a href="mailto:${user.email}" class="text-body">${user.email}</a>`);
                            }

                            // Nota: la API devuelve 'phone', lo usamos aquí
                            if (user.phone) {
                                const cleanPhoneNumber = String(user.phone).replace(/[\s()-]/g, '');
                                $('#profile-telephone').html(`<a href="tel:${cleanPhoneNumber}" class="text-body">${user.phone}</a>`);
                            }

                            const additionalEmailsContainer = $('#additional-emails-list');
                            const additionalPhonesContainer = $('#additional-phones-list');
                            const additionalContactsWrapper = $('#additional-contacts-container');

                            additionalEmailsContainer.empty();
                            additionalPhonesContainer.empty();
                            let hasAdditionalContacts = false;

                            // Llenar emails adicionales (asumiendo que vienen en `user.emails`)
                            if (Array.isArray(user.emails) && user.emails.length > 0) {
                                hasAdditionalContacts = true;
                                user.emails.forEach(email => {
                                    const emailHtml = `
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <a href="mailto:${email.email}" class="text-body text-break">${email.email}</a>
                                <div class="d-flex flex-column gap-1">
                                    ${email.is_primary == 1 ? `<span class="badge blue-item ms-1">${traducciones.primary || 'Primary'}</span>` : ''}
                                    ${email.is_active == 1 ? `<span class="badge green-item ms-1">${traducciones.active || 'Active'}</span>` : `<span class="badge red ms-1">${traducciones.inactive || 'Inactive'}</span>`}
                                </div>
                            </div>`;
                                    additionalEmailsContainer.append(emailHtml);
                                });
                            }

                            // Llenar teléfonos adicionales (asumiendo que vienen en `user.phones`)
                            if (Array.isArray(user.phones) && user.phones.length > 0) {
                                hasAdditionalContacts = true;
                                user.phones.forEach(phone => {
                                    const cleanPhoneNumber = phone.phone_number.replace(/[\s()-]/g, '');
                                    const phoneHtml = `
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <a href="tel:${cleanPhoneNumber}" class="text-body">${phone.phone_number}</a>
                                <div class="d-flex flex-column gap-1">
                                     ${phone.is_primary == 1 ? `<span class="badge blue-item ms-1">${traducciones.primary || 'Primary'}</span>` : ''}
                                     ${phone.is_active == 1 ? `<span class="badge green-item ms-1">${traducciones.active || 'Active'}</span>` : `<span class="badge red ms-1">${traducciones.inactive || 'Inactive'}</span>`}
                                </div>
                            </div>`;
                                    additionalPhonesContainer.append(phoneHtml);
                                });
                            }

                            additionalContactsWrapper.toggle(hasAdditionalContacts);
                            // --- FIN DE MODIFICACIONES ---

                            $(`input[name="card_height_system"][value="${user.system_type.toLowerCase()}"]`).prop('checked', true);
                            $('#profile-updated-date').text(formatFullDate(user.updated_at));
                            $('#profile-timezone').text(user.timezone || 'No establecida');

                            countrySelect('telephone', '[data-phone-select]', user.phone, '.modal-body');
                        } else {
                            Swal.fire(traducciones['titleError_profile_user'], traducciones['loadError_profile_user'], 'error');
                        }
                    },
                    error: function () {
                        Swal.fire(traducciones['titleError_profile_user'], traducciones['loadError_profile_user'], 'error');
                    }
                });
            }
            // ============================
            //  ACTUALIZACIÓN DEL SISTEMA DE MEDIDA
            // ============================
            function updateMeasurementSystem(newSystem) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: `administrator/system_type/update/${userId}`, // Endpoint para admin
                        type: 'POST',
                        data: {
                            system_type: newSystem,
                            _method: 'PUT'
                        },
                        dataType: 'json',
                        success: response => response.value ? resolve(response) : reject(new Error(response.message)),
                        error: xhr => reject(new Error(xhr.responseJSON?.message || 'AJAX request failed.'))
                    });
                });
            }

            $('.system-update-radio').on('change', function () {
                const newSystem = this.value;
                const originalSystem = user.system_type.toLowerCase();

                Swal.fire({
                    title: traducciones['confirm_update_title'] || 'Confirmar',
                    text: traducciones['confirm_update_measurent_system'] || 'Cambiar sistema de medida?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: traducciones['confirm'] || 'Confirmar',
                    cancelButtonText: traducciones['cancel'] || 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateMeasurementSystem(newSystem)
                            .then(response => {
                                Swal.fire({
                                    icon: 'success',
                                    title: traducciones['titleSuccess_profile_user'] || 'Actualizado',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(loadUserProfile); // Recargar datos
                            })
                            .catch(error => {
                                $(`input[name="card_height_system"][value="${originalSystem}"]`).prop('checked', true);
                                Swal.fire({
                                    icon: 'error',
                                    title: traducciones['error'] || 'Error',
                                    text: error.message
                                });
                            });
                    } else {
                        $(`input[name="card_height_system"][value="${originalSystem}"]`).prop('checked', true);
                    }
                });
            });

            // ============================
            //  EVENTOS DE MODALES Y BOTONES
            // ============================
            $('#profile-image').on('click', function () {
                $('#imagePreviewModal img').attr('src', $(this).attr('src'));
                bootstrap.Modal.getOrCreateInstance(document.getElementById('imagePreviewModal')).show();
            });

            $('.editUserBtn').on('click', function (e) {
                $('#user_id').val(user.id);
                $('#first_name').val(user.first_name);
                $('#last_name').val(user.last_name);
                $('#timezoneSelect').val(user.timezone).trigger('change'); // Asegurar trigger para Select2
                $(`input[name="height_system"][value="${(user.system_type || 'us').toLowerCase()}"]`).prop('checked', true);
                $('#email').val(user.email);
                $('#telephone').val(user.phone); // Usar 'phone'
                $('#password').val('');
                $('#confirm_password').val('');

                $('#email-list').empty();
                $('#telephone-list').empty();

                if (window.countrySelectMasks) { /* ... limpiar máscaras ... */ }
                if (Array.isArray(user.emails)) { user.emails.forEach(email => addEmailRow(email)); }
                if (Array.isArray(user.phones)) { user.phones.forEach(tel => addTelephoneRow(tel)); }

                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                const imageIn = document.getElementById('profile_image');
                if (imageIn) imageIn.value = "";
                $('#preview_cropper').attr('src', '').hide();
                $('#cropper-buttons').hide();
                
                if ($('#profile-image').attr('src')) {
                    $('#current_profile_image').attr('src', $('#profile-image').attr('src')).show();
                } else {
                    $('#current_profile_image').hide();
                }

                // === INICIO: NUEVA LÓGICA DE VALIDACIÓN ===
                $('#email').attr('data-initial-value', user.email || '');
                $('#telephone').attr('data-initial-value', user.phone || ''); // Usar 'phone'
                // Las reglas de contraseña ya son opcionales en el HTML
                // === FIN: NUEVA LÓGICA DE VALIDACIÓN ===

                // 3. Eliminar clearValidationMessages
                // clearValidationMessages(document.getElementById('editUserForm'));

                bootstrap.Modal.getOrCreateInstance(document.getElementById('editUserModal')).show();
            });

            // ============================
            //  LÓGICA DEL FORMULARIO DE EDICIÓN
            // ============================
            $('#first_name').mask('L'.repeat(30), {
                translation: {
                    'L': {
                        pattern: /[A-Za-zÀ-ÿ\s]/,
                        recursive: true
                    }
                }
            });
            $('#last_name').mask('L'.repeat(30), {
                translation: {
                    'L': {
                        pattern: /[A-Za-zÀ-ÿ\s]/,
                        recursive: true
                    }
                }
            });



            document.getElementById('editUserForm').addEventListener('validation:success', function (e) {

                // 6. Eliminada la validación manual
                // if (!validateFormFields(e.target, requiredFields, ...)) return;

                const formData = new FormData(this);
                formData.append('_method', 'PUT');
                formData.append('system_type', formData.get('height_system'));

                // Si no se ingresó contraseña, no la enviamos
                if (!$('#password').val()) {
                    formData.delete('password');
                    formData.delete('confirm_password');
                }

                if (cropper && imageInputElement.files[0]) {
                    cropper.getCroppedCanvas({ width: 500, height: 500 }).toBlob((blob) => {
                        formData.set('profile_image', blob, 'cropped_image.png');
                        enviarFormulario(formData);
                    }, 'image/png');
                } else {
                    if (!imageInputElement.files[0]) {
                        formData.delete('profile_image');
                    }
                    enviarFormulario(formData);
                }
            });
            function enviarFormulario(formData) {

                // ===== INICIO: CÓDIGO A AÑADIR =====
                const emails = [];
                $('#email-list .email-row-wrapper').each(function () {
                    const emailInput = $(this).find('.email-input').val();
                    if (emailInput) {
                        emails.push({
                            contact_email_id: $(this).find('.contact-email-id').val() || null,
                            email: emailInput,
                            is_primary: $(this).find('input[type="radio"]').is(':checked') ? 1 : 0,
                            is_active: $(this).find('.email-active-switch').is(':checked') ? 1 : 0,
                        });
                    }
                });

                const phones = [];
                $('#telephone-list .telephone-row-wrapper').each(function () {
                    const telInputId = $(this).find('.telephone-input').attr('id');
                    const mask = window.countrySelectMasks?.[telInputId];
                    let maskedValue = mask?.value || document.getElementById(telInputId)?.value || '';

                    if (maskedValue.replace(/\D/g, '').length > 0) {
                        phones.push({
                            contact_phone_id: $(this).find('.contact-phone-id').val() || null,
                            phone_number: maskedValue,
                            is_primary: $(this).find('input[type="radio"]').is(':checked') ? 1 : 0,
                            is_active: $(this).find('.telephone-active-switch').is(':checked') ? 1 : 0,
                        });
                    }
                });

                formData.append('emails', JSON.stringify(emails));
                formData.append('phones', JSON.stringify(phones));
                // ===== FIN: CÓDIGO A AÑADIR =====

                $.ajax({
                    url: `administrator/update-profile/${userId}`,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.value) {
                            Swal.fire({
                                icon: 'success',
                                title: traducciones['titleSuccess_profile_user'],
                                text: traducciones['saveSuccess_profile_user'],
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire(traducciones['titleError_profile_user'], response.message || traducciones['saveError_profile_user'], 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire(traducciones['titleError_profile_user'], xhr.responseJSON?.message || traducciones['saveError_profile_user'], 'error');
                    }
                });
            }

            // ============================
            //  CROP Y FLIP DE IMAGEN
            // ============================
            function initializeCropper() {
                if (cropper) cropper.destroy();
                cropper = new Cropper(imagePreviewElement, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCrop: true,
                    autoCropArea: 1,
                    responsive: true,
                    background: false,
                    rotatable: true,
                    scalable: true
                });
                scaleX = 1;
                currentRotation = 0;
            }

            imageInputElement.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        imagePreviewElement.src = event.target.result;
                        imagePreviewElement.style.display = 'block';
                        $('#flipHorizontal, #flipVertical').show(); // Mostrar botones
                        initializeCropper();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('#flipHorizontal').on('click', () => {
                if (cropper) {
                    scaleX = -scaleX;
                    cropper.scaleX(scaleX);
                }
            });

            $('#flipVertical').on('click', () => {
                if (cropper) {
                    currentRotation = (currentRotation + 90) % 360;
                    cropper.rotateTo(currentRotation);
                }
            });

            // INICIO DE LÓGICA DE PREGUNTAS DE SEGURIDAD

            const securitySetupView = $('#setup-view');
            const securityDisplayView = $('#display-view');
            const securityFormView = $('#form-view-questions');
            const securityForm = $('#security-question-form');
            const securityIdField = $('#security_id');

            const t_security = {
                success: '<?= $traducciones['tituloExito_security'] ?? "Success" ?>',
                error: '<?= $traducciones['tituloError_security'] ?? "Error" ?>',
                updateSuccess: '<?= $traducciones['updateSuccess_security'] ?? "Questions saved successfully!" ?>',
                updateError: '<?= $traducciones['updateError_security'] ?? "Could not save questions." ?>',
                updateAjaxError: '<?= $traducciones['updateAjaxError_security'] ?? "A server error occurred." ?>',
                input_generic_error: '<?= $traducciones['input_generic_error'] ?? "This field is required." ?>'
            };

            function switchSecurityView(viewToShow) {
                securitySetupView.addClass('d-none');
                securityDisplayView.addClass('d-none');
                securityFormView.addClass('d-none');
                viewToShow.removeClass('d-none');
            }

            function loadSecurityQuestions() {
                $.ajax({
                    url: 'security-questions',
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.value && response.data.data) {
                            const data = response.data.data;
                            $('#display_question1').text(data.question1);
                            $('#display_answer1').text(data.answer1);
                            $('#display_question2').text(data.question2);
                            $('#display_answer2').text(data.answer2);

                            securityIdField.val(data.security_question_id);
                            $('#question1').val(data.question1);
                            $('#answer1').val(data.answer1);
                            $('#question2').val(data.question2);
                            $('#answer2').val(data.answer2);

                            switchSecurityView(securityDisplayView);
                        } else {
                            switchSecurityView(securitySetupView);
                        }
                    },
                    error: function () {
                        Swal.fire(t_security.error, t_security.updateAjaxError, 'error');
                        switchSecurityView(securitySetupView);
                    }
                });
            }

            // Cargar preguntas solo cuando se muestra la pestaña de seguridad
            $('#security-tab').on('shown.bs.tab', function () {
                loadSecurityQuestions();
            });


            $('#btn-setup, #btn-edit-questions').on('click', function () {
                if ($(this).is('#btn-setup')) {
                    securityForm[0].reset();
                    securityIdField.val('');
                }
                switchSecurityView(securityFormView);
            });

            $('#btn-cancel-edit-questions').on('click', function () {
                if (securityIdField.val()) {
                    switchSecurityView(securityDisplayView);
                } else {
                    switchSecurityView(securitySetupView);
                }
            });

            $('#btn-add-email').on('click', () => addEmailRow());
            $('#btn-add-telephone').on('click', () => addTelephoneRow());

            $(document).on('click', '.btn-remove-row', function () {
                const rowWrapper = $(this).closest('.telephone-row-wrapper, .email-row-wrapper');
                const container = rowWrapper.parent();

                if (rowWrapper.hasClass('telephone-row-wrapper')) {
                    const telInputId = rowWrapper.find('.telephone-input').attr('id');
                    if (window.countrySelectMasks && window.countrySelectMasks[telInputId]) {
                        window.countrySelectMasks[telInputId].destroy();
                        delete window.countrySelectMasks[telInputId];
                    }
                    const $select = rowWrapper.find('select.country-select');
                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }
                }

                const wasPrimary = rowWrapper.find('input[type="radio"]:checked').length > 0;
                rowWrapper.remove();

                if (wasPrimary && container.children().length > 0) {
                    container.find('input[type="radio"]').first().prop('checked', true);
                }
            });

            document.getElementById('security-question-form').addEventListener('validation:success', function (e) {

                // 9. Eliminada la validación manual
                // if (!validateFormFields(e.target, ...)) return;

                const id = securityIdField.val();
                const method = id ? 'PUT' : 'POST';
                const url = id ? `security-questions/${encodeURIComponent(id)}` : 'security-questions';
                const data = {
                    question1: $('#question1').val(),
                    answer1: $('#answer1').val(),
                    question2: $('#question2').val(),
                    answer2: $('#answer2').val()
                };

                $.ajax({
                    url: url, type: method, contentType: 'application/json',
                    data: JSON.stringify(data), dataType: 'json',
                    success: function (response) { /* ... */ },
                    error: function () { /* ... */ }
                });
            });


            // ============================
            //  INICIALIZACIÓN
            // ============================
            loadUserProfile(); // Carga inicial de los datos del administrador
        });
    </script>