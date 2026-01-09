<?php

$role = $_SESSION['roles_user'] ?? 'User'; // Valor por defecto: 'User'

$folder = 'users';
$userImagePath = "/uploads/users/";

// Obtener el system_type de la sesión o usar 'us' por defecto
$system_type = strtolower($_SESSION['system_type'] ?? 'us');
?>

<div id="wrapper">
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <h4 class="page-title"><?= $traducciones['page_title_users'] ?></h4>
                <div id="toolbar">
                    <button class="btn btn-add-user" id="btnOpenUserModal">
                        + <?= $traducciones['add_new_user'] ?>
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">


                        <table id="usersTable" data-toggle="table" data-search="true" data-show-refresh="true"
                            data-page-list="[5, 10, 20]" data-page-size="5" data-pagination="true"
                            data-show-columns="true" data-show-pagination-switch="true" data-locale="<?= $locale ?>"
                            class="table table-borderless" data-toolbar="#toolbar" data-unique-id="user_id">
                            <thead>
                                <tr>
                                    <th data-field="first_name" data-sortable="true">
                                        <?= $traducciones['first_name'] ?>
                                    </th>
                                    <th data-field="last_name" data-sortable="true">
                                        <?= $traducciones['last_name'] ?>
                                    </th>
                                    <th data-field="sex_biological" data-sortable="true"
                                        data-formatter="genderFormatter">
                                        <?= $traducciones['gender'] ?>
                                    </th>
                                    <th data-field="birthday" data-sortable="true" data-formatter="birthdayFormatter">
                                        <?= $traducciones['birthday'] ?>
                                    </th>
                                    <th data-field="height" data-sortable="true"><?= $traducciones['height'] ?></th>
                                    <th data-field="email" data-sortable="true"><?= $traducciones['email'] ?></th>
                                    <th data-field="telephone" data-sortable="true">
                                        <?= $traducciones['telephone'] ?>
                                    </th>
                                    <th data-field="id" data-align="center" data-formatter="userActionFormatter">
                                        <?= $traducciones['actions'] ?>
                                    </th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>
                <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form id="userForm" autocomplete="off" data-validation="reactive" novalidate>
                                <div class="modal-header">
                                    <h5 class="modal-title" id="userModalLabel"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="user_id" name="user_id">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="first_name"
                                                class="form-label"><?= $traducciones['first_name_label'] ?></label>
                                            <input type="text" id="first_name" name="first_name" class="form-control"
                                                data-rules="noVacio"
                                                data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="last_name"
                                                class="form-label"><?= $traducciones['last_name_label'] ?></label>
                                            <input type="text" id="last_name" name="last_name" class="form-control"
                                                data-rules="noVacio"
                                                data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label d-block"><?= $traducciones['gender_label'] ?><span
                                                    class="ms-1" data-bs-toggle="tooltip" data-bs-placement="right"
                                                    title="<?= ($_SESSION['idioma'] ?? 'ES') === 'ES'
                                                        ? 'Algunos biomarcadores requieren el sexo asignado al nacer para calcular valores de referencia con precisión.'
                                                        : 'Some biomarkers may require sex assigned at birth to calculate reference values accurately.' ?>">
                                                    <i class="mdi mdi-information-outline text-muted"
                                                        style="cursor: help;"></i>
                                                </span></label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="sex_biological"
                                                    id="sex_m" value="m" data-rules="noVacio"
                                                    data-message-no-vacio="<?= htmlspecialchars($traducciones['signup_gender_required'] ?? 'Seleccione una opción') ?>"
                                                    data-error-container=".col-md-6.mb-3">
                                                <label class="form-check-label"
                                                    for="sex_m"><?= $traducciones['gender_male'] ?></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="sex_biological"
                                                    id="sex_f" value="f">
                                                <label class="form-check-label"
                                                    for="sex_f"><?= $traducciones['gender_female'] ?></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="sex_biological"
                                                    id="sex_u" value="M">
                                                <label class="form-check-label" for="sex_u">
                                                    <?= htmlspecialchars($traducciones['signup_gender_undefined']) ?>
                                                </label>
                                            </div>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function () {
                                                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                                                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                                                        return new bootstrap.Tooltip(tooltipTriggerEl)
                                                    })
                                                });
                                            </script>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="birthday"
                                                class="form-label"><?= $traducciones['birthday_label'] ?></label>
                                            <input type="text" id="birthday" name="birthday" class="form-control"
                                                placeholder="<?= htmlspecialchars($traducciones['birthday_placeholder'] ?? 'YYYY-MM-DD') ?>"
                                                data-rules="noVacio"
                                                data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>">
                                        </div>
                                    </div>

                                    <div class="row align-items-end">
                                        <div class="col-md-6 mb-3">
                                            <label
                                                class="form-label"><?= $traducciones['metrical_system'] ?? 'Sistema de medida' ?></label>
                                            <div>
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
                                        <div class="col-md-6 mb-3">
                                            <label for="height" class="form-label" id="heightLabel">
                                                <?= $traducciones['height_label'] ?>
                                                <i class="mdi mdi-help-circle-outline ms-1" id="heightExample"
                                                    data-bs-toggle="popover" data-bs-trigger="hover"
                                                    data-bs-placement="top" data-bs-content=""></i>
                                            </label>
                                            <input type="text" id="height" name="height" class="form-control"
                                                data-rules="noVacio"
                                                data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email"
                                            class="form-label"><?= $traducciones['email_label'] ?></label>
                                        <input type="email" id="email" name="email" class="form-control"
                                            data-rules="noVacio|email"
                                            data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>"
                                            data-message-email="<?= htmlspecialchars($traducciones['validation_email'] ?? 'Email inválido') ?>"
                                            data-validate-duplicate-url="check/check-email"
                                            data-message-duplicado="<?= htmlspecialchars($traducciones['validation_email_duplicate'] ?? 'Email ya en uso') ?>"
                                            data-record-id-selector="#user_id">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"
                                                for="country-select"><?= $traducciones['signup_country'] ?></label>
                                            <div data-phone-select=""></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="telephone"
                                                class="form-label"><?= $traducciones['telephone_label'] ?></label>
                                            <input type="text" id="telephone" name="telephone" class="form-control"
                                                data-rules="noVacio"
                                                data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>"
                                                data-validate-duplicate-url="check/check-telephone"
                                                data-message-duplicado="<?= htmlspecialchars($traducciones['validation_duplicate_phone'] ?? 'Teléfono ya en uso') ?>"
                                                data-record-id-selector="#user_id" data-validate-masked="true">
                                        </div>
                                    </div>

                                    <hr class="my-3">
                                    <h5 class="mb-3 text-uppercase bg-light p-2 rounded">
                                        <i class="mdi mdi-card-account-phone-outline me-1"></i>
                                        <?= $traducciones['additional_contacts'] ?? 'Contactos Adicionales' ?>
                                    </h5>
                                    <div class="row">
                                        <div class="mb-3 col-lg-6">
                                            <label
                                                class="form-label fw-bold"><?= $traducciones['emails'] ?? 'Emails' ?></label>
                                            <div id="email-list" class="vstack gap-2">
                                            </div>
                                            <button type="button" class="btn btn-sm btn-add mt-2" id="btn-add-email">
                                                <i class="mdi mdi-plus"></i>
                                                <?= $traducciones['add_email'] ?? 'Añadir Email' ?>
                                            </button>
                                        </div>

                                        <div class="mb-3 col-lg-6">
                                            <label
                                                class="form-label fw-bold"><?= $traducciones['telephones'] ?? 'Teléfonos' ?></label>
                                            <div id="telephone-list" class="vstack gap-2">
                                            </div>
                                            <button type="button" class="btn btn-sm btn-add mt-2"
                                                id="btn-add-telephone">
                                                <i class="mdi mdi-plus"></i>
                                                <?= $traducciones['add_telephone'] ?? 'Añadir Teléfono' ?>
                                            </button>
                                        </div>
                                    </div>
                                    <hr class="my-3">

                                    <div class="mb-3">
                                        <label for="status"
                                            class="form-label"><?= $traducciones['status_label'] ?? 'Estado' ?></label><br>
                                        <input type="checkbox" id="status" name="status" data-plugin="switchery"
                                            data-color="#1abc9c" value="1">
                                        <small
                                            class="form-text text-muted"><?= $traducciones['status_hint'] ?? 'Activo/Inactivo' ?></small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password"
                                            class="form-label"><?= $traducciones['password_label'] ?></label>
                                        <div class="input-group">
                                            <input type="password" id="password" data-error-container=".input-group"
                                                name="password" class="form-control"
                                                placeholder="<?= htmlspecialchars($traducciones['password_label']) ?>"
                                                data-rules="longitudMinima:8"
                                                data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>"
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
                                                data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>"
                                                data-message-coincide-con="<?= htmlspecialchars($traducciones['validation_password_match'] ?? 'Las contraseñas no coinciden') ?>">
                                            <button class="btn btn-toogle-password btn-sm me-1 toggle-password"
                                                data-target="confirm_password" type="button" tabindex="-1">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </button>
                                        </div>
                                    </div>


                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-save"><i
                                            class="mdi mdi-content-save-outline"></i>
                                        <?= $traducciones['save'] ?></button>
                                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal"><i
                                            class="mdi mdi-cancel"></i> <?= $traducciones['cancel'] ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewDetailsModalLabel">
                                    <?= $traducciones['dashboard_modal_user_details_title'] ?? 'Detalles del Usuario' ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="viewDetailsContainer"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal"><i
                                        class="mdi mdi-close"></i> <?= $traducciones['close'] ?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-body p-0 d-flex justify-content-center">
                                <img src="" alt="Profile Image Preview"
                                    style="max-width:100%; height:80vh; padding: 1rem;" class="img-fluid rounded">
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal"><i
                                        class="mdi mdi-close"></i> <?= $traducciones['close'] ?></button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </div>
    <div class="rightbar-overlay"></div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<script>
    // ... (Script de toggle-password sin cambios) ...
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

    // ... (Script de resetSwitchery sin cambios) ...
    function resetSwitchery(elem, checked = false) {
        if (!elem) return;
        const existing = elem.parentNode.querySelectorAll('.switchery');
        existing.forEach(el => el.remove());
        elem.checked = checked;
        elem.setAttribute('checked', checked ? 'checked' : '');
        elem.dispatchEvent(new Event('change'));
        if (elem.switchery) {
            elem.switchery.destroy?.();
            delete elem.switchery;
        }
        const switchery = new Switchery(elem, { color: '#0072b8', size: 'small' });
        elem.switchery = switchery;
    }
</script>

<script src="public/assets/js/logout.js"></script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="<?= BASE_URL ?>public/assets/libs/flatpickr/l10n/es.js"></script>


<script type="module">
    import { countrySelect } from "./public/assets/js/components/countrySelect.js";
    import { maskMedida, americanToMetersString, metersToAmerican } from "./public/assets/js/helpers/helpers.js";

    // 1. Importar el nuevo validador
    import "./public/assets/js/helpers/validarFormulario.js";

    // 2. Quitar 'validateFormFields' y 'validateFieldAsync' de la importación
    import { clearValidationMessages } from "./public/assets/js/helpers/helpers.js";

    $(function () {

        // ... (Lógica de Flatpickr, addEmailRow, addTelephoneRow sin cambios) ...
        // ======= Flatpickr: inicializador y utilidades (NUEVO) =======
        let birthdayFp = null;

        const traducciones = <?= json_encode($traducciones) ?>;
        const defaultSystemType = '<?= $system_type ?>';
        let currentUserData = {};

        // Mapa simple de locales -> flatpickr
        const localeCode = (traducciones['locale_code'] || 'en').toLowerCase();
        const flatpickrLocale =
            localeCode.startsWith('es') ? flatpickr.l10ns.es :
                localeCode.startsWith('pt') ? flatpickr.l10ns.pt :
                    localeCode.startsWith('fr') ? flatpickr.l10ns.fr :
                        flatpickr.l10ns.default;

        function initBirthdayPicker(defaultDate = null) {
            // Destruir instancia anterior si existe
            if (birthdayFp && typeof birthdayFp.destroy === 'function') {
                birthdayFp.destroy();
            }
            birthdayFp = flatpickr('#birthday', {
                // Valor visible para el usuario:
                altInput: true,
                altFormat: localeCode.startsWith('es') ? 'd/m/Y' : 'm/d/Y',
                // Valor que se envía al backend (YYYY-MM-DD):
                dateFormat: 'Y-m-d',
                defaultDate: defaultDate || null,
                maxDate: 'today',
                allowInput: true,
                disableMobile: true,
                locale: flatpickrLocale,
            });
        }
        // ======= FIN Flatpickr =======


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
              data-message-email="${traducciones.validation_email || 'Invalid email'}"
              data-validate-duplicate-url="contact-emails/check-email?entity_type=user"
              data-message-duplicado="${traducciones.validation_email_duplicate || 'Email already exists'}"
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
            telephoneInput.dataset.validateDuplicateUrl = 'contact-phones/check-telephone?entity_type=user';
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
                '#userModal .modal-body'
            );
        }

        const userModal = new bootstrap.Modal(document.getElementById('userModal'));
        const viewDetailsModal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
        const imagePreviewModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));

        // ... (Traducciones 't', formatters 'genderFormatter', 'birthdayFormatter', 'userActionFormatter' sin cambios) ...
        const t = {
            success_title: traducciones['titleSuccess_user'] || 'Éxito',
            error_title: traducciones['titleError_user'] || 'Error',
            success_update: traducciones['success_update_users'] || 'Usuario actualizado.',
            error_update: traducciones['error_update_users'] || 'Error al actualizar.',
            success_delete: traducciones['success_delete_users'] || 'Usuario eliminado.',
            error_delete: traducciones['error_delete_users'] || 'Error al eliminar.',
            delete_confirm_title: traducciones['delete_confirm_title_users'] || '¿Estás seguro?',
            delete_confirm_text: traducciones['delete_confirm_text_users'] || 'Esta acción no se puede revertir.',
            delete_confirm_btn: traducciones['delete_confirm_btn_users'] || 'Sí, eliminar',
            error_loading: traducciones['error_loading_users'] || 'Error al cargar los usuarios.',
            success_create: traducciones['success_create_users'] || 'Usuario creado con éxito.',
            error_create: traducciones['error_create_users'] || 'Error al crear el usuario.',
        };

        window.genderFormatter = value => {
            const v = (value || '');
            if (v === 'm') return traducciones['gender_male'] || 'Male';
            if (v === 'f') return traducciones['gender_female'] || 'Female';
            if (v === 'M') return traducciones['signup_gender_undefined'] || 'Undefined';
            return traducciones['signup_gender_undefined'] || 'Undefined';
        };


        window.birthdayFormatter = value => {
            if (!value) return '';
            const date = new Date(value + 'T00:00:00');
            return date.toLocaleDateString(traducciones['locale_code'] || 'en-US', { year: 'numeric', month: '2-digit', day: '2-digit' });
        };

        window.userActionFormatter = (value, row) => {
            const viewTitle = traducciones['view_details'] || 'View Details';
            const editTitle = traducciones['edit_profile'] || 'Edit Profile';
            const deleteTitle = traducciones['delete_user'] || 'Delete User';
            return `
        <div class="btn-group" role="group">
            <button class="btn btn-view action-icon viewBtn" data-id="${row.user_id}" title="${viewTitle}"><i class="mdi mdi-eye-outline"></i></button>
            <button class="btn btn-pencil action-icon editBtn" data-id="${row.user_id}" title="${editTitle}"><i class="mdi mdi-pencil-outline"></i></button>
            <button class="btn btn-delete action-icon deleteBtn" data-id="${row.user_id}" title="${deleteTitle}"><i class="mdi mdi-delete-outline"></i></button>
        </div>`;
        };

        // ... (loadUsers y refresh.bs.table sin cambios) ...
        function loadUsers() {
            $.ajax({
                url: 'users', type: 'GET', dataType: 'json',
                success: function (response) {
                    if (response.value === true && Array.isArray(response.data)) {
                        $('#usersTable').bootstrapTable('load', response.data);
                    } else {
                        $('#usersTable').bootstrapTable('load', []);
                        Swal.fire(t.error_title, response.message || t.error_loading, 'error');
                    }
                },
                error: function () {
                    $('#usersTable').bootstrapTable('load', []);
                    Swal.fire(t.error_title, t.error_loading, 'error');
                }
            });
        }
        $('#usersTable').on('refresh.bs.table', loadUsers);


        // 3. Lógica de 'Crear' (btnOpenUserModal) MODIFICADA
        $('#btnOpenUserModal').on('click', function () {
            currentUserData = {};
            $('#userForm')[0].reset();
            clearValidationMessages(document.getElementById('userForm'));

            $('#userModalLabel').text(traducciones['add_new_user']);
            $('#user_id').val('');
            $(`input[name="height_system"][value="${defaultSystemType}"]`).prop('checked', true).trigger('change');

            const statusInput = document.getElementById('status');
            resetSwitchery(statusInput, true);

            $('#email-list').empty();
            $('#telephone-list').empty();
            if (window.countrySelectMasks) {
                Object.keys(window.countrySelectMasks).forEach(key => {
                    if (key !== 'telephone') {
                        window.countrySelectMasks[key].destroy();
                        delete window.countrySelectMasks[key];
                    }
                });
            }

            addEmailRow();
            addTelephoneRow();

            countrySelect('telephone', '[data-phone-select]', null, '#userModal .modal-body');
            initBirthdayPicker(null);

            // === INICIO: NUEVA LÓGICA DE VALIDACIÓN ===
            // Establecer valores iniciales para validación de duplicados
            $('#email').attr('data-initial-value', '');
            $('#telephone').attr('data-initial-value', '');

            // Hacer contraseñas obligatorias
            $('#password').attr('data-rules', 'noVacio|longitudMinima:8');
            $('#confirm_password').attr('data-rules', 'noVacio|coincideCon:#password');
            // === FIN: NUEVA LÓGICA DE VALIDACIÓN ===

            userModal.show();
        });


        // ... (Lógica de .viewBtn sin cambios) ...
        $(document).on('click', '.viewBtn', async function () {
            const userId = $(this).data('id');
            const row = $('#usersTable').bootstrapTable('getRowByUniqueId', userId);
            if (!row) return;

            // --- Construcción dinámica del contenido del modal ---
            const birthdayFormatted = window.birthdayFormatter(row.birthday);
            const sexoTexto = window.genderFormatter(row.sex_biological);
            let initialImg = row.sex_biological === 'm' ? 'public/assets/images/users/user_boy.jpeg' : 'public/assets/images/users/user_girl.jpeg';

            // Emails adicionales
            let emailsHtml = '';
            if (Array.isArray(row.emails) && row.emails.length > 0) {
                row.emails.forEach(email => {
                    emailsHtml += `
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <a href="mailto:${email.email}" class="text-body text-break">${email.email}</a>
                    <div class="d-flex flex-column gap-1">
                        ${email.is_primary == 1 ? `<span class="badge blue-item ms-1">${traducciones.primary || 'Primary'}</span>` : ''}
                        ${email.is_active == 1 ? `<span class="badge green-item ms-1">${traducciones.active || 'Active'}</span>` : `<span class="badge red ms-1">${traducciones.inactive || 'Inactive'}</span>`}
                    </div>
                </div>`;
                });
            }

            // Teléfonos adicionales
            let phonesHtml = '';
            if (Array.isArray(row.phones) && row.phones.length > 0) {
                row.phones.forEach(phone => {
                    const cleanPhoneNumber = phone.phone_number.replace(/[\s()-]/g, '');
                    phonesHtml += `
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <a href="tel:${cleanPhoneNumber}" class="text-body">${phone.phone_number}</a>
                    <div class="d-flex flex-column gap-1">
                         ${phone.is_primary == 1 ? `<span class="badge blue-item ms-1">${traducciones.primary || 'Primary'}</span>` : ''}
                         ${phone.is_active == 1 ? `<span class="badge green-item ms-1">${traducciones.active || 'Active'}</span>` : `<span class="badge red ms-1">${traducciones.inactive || 'Inactive'}</span>`}
                    </div>
                </div>`;
                });
            }

            const html = `
        <div class="row">
            <div class="col-md-4 text-center">
                <img id="openImagePreview" class="img-fluid rounded-circle mb-2" style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;" alt="Profile Image" src="${initialImg}">
                <h4 class="mb-1">${row.first_name} ${row.last_name}</h4>
                <p class="text-muted">${sexoTexto}</p>
            </div>
            <div class="col-md-8">
                <h5 class="mb-3 text-uppercase bg-light p-2 rounded"><i class="mdi mdi-account-circle me-1"></i> ${traducciones.personal_information || 'Info. Personal'}</h5>
                <p><strong>${traducciones.birthday_label}:</strong> ${birthdayFormatted}</p>
                <p><strong>${traducciones.height_label}:</strong> ${row.height}</p>
                <hr>
                <p><strong>${traducciones.email_label}:</strong> <a href="mailto:${row.email}" class="text-body">${row.email}</a></p>
                <p><strong>${traducciones.telephone_label}:</strong> <a href="tel:${String(row.telephone).replace(/[\s()-]/g, '')}" class="text-body">${row.telephone}</a></p>
                
                ${(emailsHtml || phonesHtml) ? `<hr><h5 class="mt-3 text-uppercase bg-light p-2 rounded"><i class="mdi mdi-card-account-phone-outline me-1"></i> ${traducciones.additional_contacts || 'Contactos Adicionales'}</h5>` : ''}
                ${emailsHtml ? `<div class="mb-2"><h6>${traducciones.emails || 'Emails'}</h6>${emailsHtml}</div>` : ''}
                ${phonesHtml ? `<div><h6>${traducciones.telephones || 'Teléfonos'}</h6>${phonesHtml}</div>` : ''}
            </div>
        </div>`;

            $('#viewDetailsContainer').html(html);
            viewDetailsModal.show();

            // Verificar imagen real
            try {
                const checkRes = await fetch('<?= BASE_URL ?>auth/check-user-image', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ user_id: userId })
                });
                const json = await checkRes.json();
                if (json?.value === true && json.data?.exists) {
                    document.getElementById('openImagePreview').src = `/uploads/users/user_${userId}.jpg`;
                }
            } catch (e) { console.warn('Error verificando imagen:', e); }
        });

        // 4. Lógica de 'Editar' (.editBtn) MODIFICADA
        $(document).on('click', '.editBtn', function () {
            const userId = $(this).data('id');
            currentUserData = $('#usersTable').bootstrapTable('getRowByUniqueId', userId);
            if (!currentUserData) return;

            $('#userForm')[0].reset();
            clearValidationMessages(document.getElementById('userForm'));

            const statusInput = document.getElementById('status');
            const isActive = currentUserData.status == 1 || currentUserData.status === "1";
            resetSwitchery(statusInput, isActive);

            $('#userModalLabel').text(traducciones['edit_user_profile'] || 'Editar Usuario');
            $('#user_id').val(currentUserData.user_id);
            $('#first_name').val(currentUserData.first_name);
            $('#last_name').val(currentUserData.last_name);
            $(`input[name="sex_biological"][value="${currentUserData.sex_biological}"]`).prop('checked', true);
            initBirthdayPicker(currentUserData.birthday || null);
            $('#email').val(currentUserData.email);
            $('#telephone').val(currentUserData.telephone);

            $('#email-list').empty();
            $('#telephone-list').empty();

            if (window.countrySelectMasks) {
                Object.keys(window.countrySelectMasks).forEach(key => {
                    if (key !== 'telephone') {
                        window.countrySelectMasks[key].destroy();
                        delete window.countrySelectMasks[key];
                    }
                });
            }

            if (Array.isArray(currentUserData.emails)) {
                currentUserData.emails.forEach(email => addEmailRow(email));
            }
            if (Array.isArray(currentUserData.phones)) {
                currentUserData.phones.forEach(tel => addTelephoneRow(tel));
            }

            const system = (currentUserData.system_type || defaultSystemType).toLowerCase();
            $(`input[name="height_system"][value="${system}"]`).prop('checked', true).trigger('change');

            if (system === 'eu') {
                $('#height').val(americanToMetersString(String(currentUserData.height)));
            } else {
                $('#height').val(currentUserData.height);
            }

            countrySelect('telephone', '[data-phone-select]', currentUserData.telephone, '#userModal .modal-body');

            $('#password').val('');

            // === INICIO: NUEVA LÓGICA DE VALIDACIÓN ===
            // Establecer valores iniciales para validación de duplicados
            $('#email').attr('data-initial-value', currentUserData.email || '');
            $('#telephone').attr('data-initial-value', currentUserData.telephone || '');

            // Hacer contraseñas opcionales (solo validan longitud si se escriben)
            $('#password').attr('data-rules', 'longitudMinima:8');
            $('#confirm_password').attr('data-rules', 'coincideCon:#password');
            // === FIN: NUEVA LÓGICA DE VALIDACIÓN ===

            userModal.show();
        });

        // ... (Lógica de .deleteBtn sin cambios) ...
        $(document).on('click', '.deleteBtn', function () {
            const userId = $(this).data('id');
            Swal.fire({
                title: t.delete_confirm_title, text: t.delete_confirm_text, icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
                confirmButtonText: t.delete_confirm_btn, cancelButtonText: traducciones['cancel'] || 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `users/${userId}`,
                        type: 'DELETE',
                        dataType: 'text', // <--- TEMPORAL
                        success: res => {
                            try {
                                const json = JSON.parse(res);
                                if (json.value) {
                                    Swal.fire(t.success_title, t.success_delete, 'success');
                                    loadUsers();
                                } else {
                                    Swal.fire(t.error_title, json.message || t.error_delete, 'error');
                                }
                            } catch (e) {
                                Swal.fire(t.error_title, 'Respuesta inválida del servidor', 'error');
                            }
                        },
                        error: (xhr) => {
                            console.log('XHR ERROR:', xhr.responseText);
                            Swal.fire(t.error_title, 'Error conectando con el servidor', 'error');
                        }
                    });
                }
            });
        });

        // ... (Lógica de máscaras y height_system sin cambios) ...
        $('#first_name, #last_name').mask('L'.repeat(30), { translation: { 'L': { pattern: /[A-Za-zÀ-ÿ\s]/, recursive: true } } });
        $('input[name="height_system"]').on('change', function () {
            maskMedida('height', this.value === 'us' ? 'altura-americana' : 'altura-europea');
            updateHeightExample(this.value);
        });

        // 5. Lógica de 'submit' REEMPLAZADA por 'validation:success'
        document.getElementById('userForm').addEventListener('validation:success', function (e) {

            // 6. Eliminada la validación manual y el cambio de nombre de 'password'

            const formData = new FormData(this);
            const isEdit = !!formData.get('user_id');
            const endpoint = isEdit ? `users/${formData.get('user_id')}` : 'users';
            if (isEdit) formData.append('_method', 'PUT');

            formData.append('system_type', formData.get('height_system'));
            formData.set('height', $('#height').val().replace('.', ''));

            const isChecked = document.getElementById('status').checked;
            formData.set('status', isChecked ? '1' : '0');

            // La lógica de 'sendFormData' (que recopila emails/teléfonos y envía AJAX) no cambia
            sendFormData(endpoint, 'POST', formData);
        });


        // ... (Función sendFormData sin cambios) ...
        function sendFormData(endpoint, method, formData) {

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

            $.ajax({
                url: endpoint, type: method, data: formData,
                contentType: false, processData: false, dataType: 'json',
                success: function (res) {
                    if (res.value) {
                        userModal.hide();
                        Swal.fire(t.success_title, !!formData.get('user_id') ? t.success_update : t.success_create, 'success');
                        loadUsers();
                    } else {
                        Swal.fire(t.error_title, res.message, 'error');
                    }
                },
                error: function (xhr) {
                    Swal.fire(t.error_title, xhr.responseJSON?.message || (!!formData.get('user_id') ? t.error_update : t.error_create), 'error');
                }
            });
        }

        // ... (Lógica de openImagePreview, heightExampleIcon, updateHeightExample sin cambios) ...
        $(document).on('click', '#openImagePreview', function () {
            $('#imagePreviewModal img').attr('src', $(this).attr('src'));
            viewDetailsModal.hide();
            $('#imagePreviewModal').one('hidden.bs.modal', () => {
                if (!viewDetailsModal._isShown) viewDetailsModal.show();
            });
            imagePreviewModal.show();
        });

        const heightExampleIcon = document.getElementById('heightExample');
        const heightTranslations = {
            us: traducciones['ex_american'] || 'Ej: 5\'7"',
            eu: traducciones['ex_european'] || 'Ej: 170 cm'
        };
        function updateHeightExample(system) {
            if (heightExampleIcon) {
                const text = heightTranslations[system];
                heightExampleIcon.setAttribute('data-bs-content', text);
                bootstrap.Popover.getInstance(heightExampleIcon)?.dispose();
                new bootstrap.Popover(heightExampleIcon);
            }
        }


        // 7. ELIMINADOS los 'blur' listeners para 'email' y 'telephone'
        // document.getElementById('email').addEventListener('blur', ...);
        // document.getElementById('telephone').addEventListener('blur', ...);

        // ... (Lógica de add/remove row y carga inicial sin cambios) ...
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

        loadUsers();
        $('#usersTable').on('refresh.bs.table', loadUsers);
        new bootstrap.Popover(heightExampleIcon);
        updateHeightExample(defaultSystemType);
    });
</script>

</body>

</html>