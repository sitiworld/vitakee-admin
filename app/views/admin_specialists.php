<div id="wrapper">
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <h4 class="page-title"><?= $traducciones['page_title_specialists'] ?? 'Especialistas' ?></h4>
                <div id="toolbar">
                    <button class="btn btn-add-user" id="btnOpenSpecialistModal">
                        + <?= $traducciones['add_new_specialist'] ?? 'Agregar Nuevo Especialista' ?>
                    </button>
                </div>

                <div class="card">
                    <div class="card-body">



                        <table id="specialistsTable" data-toggle="table" data-search="true" data-show-refresh="true"
                            data-page-list="[5, 10, 20]" data-page-size="5" data-pagination="true"
                            data-show-columns="true" data-show-pagination-switch="true" data-locale="<?= $locale ?>"
                            class="table table-borderless" data-toolbar="#toolbar" data-unique-id="specialist_id">
                            <thead>
                                <tr>
                                    <th data-field="first_name" data-sortable="true">
                                        <?= $traducciones['first_name'] ?>
                                    </th>
                                    <th data-field="last_name" data-sortable="true">
                                        <?= $traducciones['last_name'] ?>
                                    </th>
                                    <th data-field="email" data-sortable="true"><?= $traducciones['email'] ?></th>
                                    <th data-field="phone" data-sortable="true"><?= $traducciones['telephone'] ?>
                                    </th>

                                    <th data-field="title_display_name" data-sortable="true"
                                        data-formatter="titleFormatter">
                                        <?= $traducciones['title'] ?? 'Título' ?>
                                    </th>
                                    <th data-field="specialty_display_name" data-sortable="true"
                                        data-formatter="specialtyFormatter">
                                        <?= $traducciones['specialty'] ?? 'Especialidad' ?>
                                    </th>
                                    <th data-field="id" data-align="center" data-formatter="specialistActionFormatter">
                                        <?= $traducciones['actions'] ?>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div class="modal fade" id="specialistModal" tabindex="-1" aria-labelledby="specialistModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form id="specialistForm" autocomplete="off" data-validation="reactive" novalidate>
                                <div class="modal-header">
                                    <h5 class="modal-title" id="specialistModalLabel"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="specialist_id" name="id">

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

                                    <div class="mb-3">
                                        <label for="email"
                                            class="form-label"><?= $traducciones['email_label'] ?></label>
                                        <input type="email" id="email" name="email" class="form-control"
                                            data-rules="noVacio|email"
                                            data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>"
                                            data-message-email="<?= htmlspecialchars($traducciones['validation_email'] ?? 'Email inválido') ?>"
                                            data-validate-duplicate-url="check-email-specialist"
                                            data-message-duplicado="<?= htmlspecialchars($traducciones['validation_email_duplicate'] ?? 'Email ya en uso') ?>"
                                            data-record-id-selector="#specialist_id">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"
                                                for="country-select"><?= $traducciones['signup_country'] ?></label>
                                            <div data-phone-select=""></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="phone"
                                                class="form-label"><?= $traducciones['telephone_label'] ?></label>
                                            <input type="text" id="phone" name="phone" class="form-control"
                                                data-rules="noVacio"
                                                data-message-no-vacio="<?= htmlspecialchars($traducciones['validation_required'] ?? 'Campo obligatorio') ?>"
                                                data-validate-duplicate-url="check-telephone-specialist"
                                                data-message-duplicado="<?= htmlspecialchars($traducciones['validation_duplicate_phone'] ?? 'Teléfono ya en uso') ?>"
                                                data-record-id-selector="#specialist_id" data-validate-masked="true">
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

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="title_id"
                                                class="form-label"><?= $traducciones['title'] ?? 'Título' ?></label>
                                            <select id="title_id" name="title_id" class="form-select"
                                                style="width: 100%;">
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="specialty_id"
                                                class="form-label"><?= $traducciones['specialty'] ?? 'Especialidad' ?></label>
                                            <select id="specialty_id" name="specialty_id" class="form-select"
                                                style="width: 100%;">
                                            </select>
                                        </div>
                                    </div>

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

                <div class="modal fade" id="viewSpecialistModal" tabindex="-1"
                    aria-labelledby="viewSpecialistModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewSpecialistModalLabel">
                                    <?= $traducciones['view_specialist_title'] ?? 'Specialist Details' ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="viewSpecialistContainer">
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                                    <i class="mdi mdi-close-circle-outline"></i>
                                    <?= $traducciones['close'] ?? 'Close' ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </div>
    <div class="rightbar-overlay"></div>
</div>

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
    function resetSwitchery(elem, checked = false) {
        if (!elem) return;

        // Eliminar el switchery si ya existe
        if (elem.switchery) {
            elem.switchery.element.checked = false;
            elem.switchery.destroy();
            delete elem.switchery;
        }

        // Quitar cualquier wrapper switchery anterior
        const parent = elem.parentNode;
        const existing = parent.querySelectorAll('.switchery');
        existing.forEach(el => el.remove());

        // Asegura el estado base
        elem.checked = checked;

        // Crear uno nuevo
        const switchery = new Switchery(elem, { color: '#0072b8', size: 'small' });
        elem.switchery = switchery;
    }
</script>

<script src="public/assets/js/logout.js"></script>

<script type="module">
    import { countrySelect } from "./public/assets/js/components/countrySelect.js";

    // 1. Importar el nuevo validador
    import "./public/assets/js/helpers/validarFormulario.js";

    // 2. Quitar 'validateFormFields' y 'validateFieldAsync' de la importación
    import { clearValidationMessages } from "./public/assets/js/helpers/helpers.js";

    import { selectSpecialisties, selectTitles } from "./public/assets/js/apiConfig.js";

    const currentLang = '<?= $idioma ?? 'en' ?>'.toLowerCase();
    const traducciones = <?= json_encode($traducciones) ?>;
    const language = traducciones;
    let currentSpecialistData = {};
    const specialistModal = new bootstrap.Modal(document.getElementById('specialistModal'));

    let statusSwitcheryInstance = null;

    // ... (Promise.all para cargar selects sin cambios) ...
    Promise.all([
        selectSpecialisties(),
        selectTitles()
    ]).then(([specialtiesResponse, titlesResponse]) => {
        if (specialtiesResponse?.value) {
            populateSelect('specialty_id', specialtiesResponse.data, currentLang);
        }
        if (titlesResponse?.value) {
            populateSelect('title_id', titlesResponse.data, currentLang);
        }

        $('#specialty_id, #title_id').select2({
            dropdownParent: $('#specialistModal .modal-body')
        });
    }).catch(error => {
        console.error("Error al cargar datos para los selects:", error);
    });

    // ============================================
    //  FUNCIONES PARA FILAS DINÁMICAS (MODIFICADAS)
    // ============================================

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
              class="form-control form-control-sm email-input"
              name="email_contact" placeholder="${traducciones.email_placeholder || 'example@email.com'}"
              id="email-input-${newIndex}"
              value="${data.email || ''}"
              data-rules="noVacio|email"
              data-message-no-vacio="${traducciones.validation_required || 'Required'}"
              data-message-email="${traducciones.validation_email || 'Invalid email'}"
              data-validate-duplicate-url="contact-emails/check-email?entity_type=specialist"
              e/contact-emails/check-email?
              data-message-duplicado="${traducciones.validation_email_duplicate || 'Email already exists'}"
              data-record-id-selector="#specialist_id"
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
              <input type="tel" 
                     class="form-control form-control-sm telephone-input" 
                     id="${telephoneInputId}" 
                     name="telephone_contact" placeholder="${traducciones.phone_placeholder || '555 123-4567'}">
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
        // ... (resto de la función sin cambios) ...
        telephoneList.appendChild(rowWrapper);

        const telephoneInput = document.getElementById(telephoneInputId);
        telephoneInput.dataset.rules = 'noVacio|longitudMinima:8';
        telephoneInput.dataset.messageNoVacio = traducciones.validation_required || 'Required';
        telephoneInput.dataset.messageLongitudMinima = traducciones.validation_phone_min_length || 'Phone too short';
        telephoneInput.dataset.errorContainer = `#${rowWrapper.id} .col-6:last-child`;
        telephoneInput.dataset.validateDuplicateUrl = 'contact-phones/check-telephone?entity_type=specialist';
        telephoneInput.dataset.messageDuplicado = traducciones.validation_duplicate_phone || 'Phone already exists';
        telephoneInput.dataset.recordIdSelector = '#specialist_id';
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
            '#specialistModal .modal-body'
        );
    }

    // ... (populateSelect, formatters, loadSpecialists, refresh sin cambios) ...
    function populateSelect(selectId, data, lang) {
        const selectElement = $(`#${selectId}`);
        if (!selectElement.length || !Array.isArray(data)) return;

        const nameKey = lang === 'es' ? 'name_es' : 'name_en';
        const idKey = selectId === 'specialty_id' ? 'specialty_id' :
            selectId === 'title_id' ? 'title_id' : 'id';

        selectElement.empty();
        selectElement.append(new Option(language.select_speciality_specialists, '', true, true));

        data.forEach(item => {
            const option = new Option(item[nameKey], item[idKey]);
            selectElement.append(option);
        });
    }

    window.specialtyFormatter = (value, row) => row.specialty_display_name || value;
    window.titleFormatter = (value, row) => row.title_display_name || value;

    window.specialistActionFormatter = (value, row) => {
        return `
        <div class="btn-group" role="group">
            <button class="btn btn-view action-icon viewBtn" data-id="${row.specialist_id}" title="${language['view_specialist'] || 'Ver Especialista'}">
                <i class="mdi mdi-eye-outline"></i>
            </button>
            <button class="btn btn-pencil action-icon editBtn" data-id="${row.specialist_id}" title="${language['edit_specialist'] || 'Editar Especialista'}">
                <i class="mdi mdi-pencil-outline"></i>
            </button>
            <button class="btn btn-delete action-icon deleteBtn" data-id="${row.specialist_id}" title="${language['delete_specialist'] || 'Eliminar Especialista'}">
                <i class="mdi mdi-delete-outline"></i>
            </button>
        </div>`;
    };

    function loadSpecialists() {
        $.ajax({
            url: 'specialist_get',
            type: 'GET',
            dataType: 'json',
            success: (response) => {
                $('#specialistsTable').bootstrapTable('load', response.value && Array.isArray(response.data) ? response.data : []);
            },
            error: () => Swal.fire(language.titleError_user, language.error_loading_specialists, 'error')
        });
    }

    $('#specialistsTable').on('refresh.bs.table', loadSpecialists);


    // 4. Lógica de 'Crear' (btnOpenSpecialistModal) MODIFICADA
    $('#btnOpenSpecialistModal').on('click', function () {
        currentSpecialistData = {};
        $('#specialistForm')[0].reset();
        clearValidationMessages(document.getElementById('specialistForm'));
        $('#title_id, #specialty_id').val('').trigger('change');

        $('#specialistModalLabel').text(language['add_new_specialist'] || 'Agregar Nuevo Especialista');
        $('#specialist_id').val('');

        $('#email-list').empty();
        $('#telephone-list').empty();

        const statusInput = document.getElementById('status');
        resetSwitchery(statusInput, true);

        countrySelect('phone', '[data-phone-select]', null, '#specialistModal .modal-body');

        // === INICIO: NUEVA LÓGICA DE VALIDACIÓN ===
        $('#email').attr('data-initial-value', '');
        $('#phone').attr('data-initial-value', '');
        // Hacer contraseñas obligatorias
        $('#password').attr('data-rules', 'noVacio|longitudMinima:8');
        $('#confirm_password').attr('data-rules', 'noVacio|coincideCon:#password');
        // === FIN: NUEVA LÓGICA DE VALIDACIÓN ===

        specialistModal.show();
    });

    // 5. Lógica de 'Editar' (.editBtn) MODIFICADA
    $(document).on('click', '.editBtn', function () {
        const specialistId = $(this).data('id');
        currentSpecialistData = $('#specialistsTable').bootstrapTable('getRowByUniqueId', specialistId);
        if (!currentSpecialistData) return;

        $('#specialistForm')[0].reset();
        clearValidationMessages(document.getElementById('specialistForm'));

        $('#specialistModalLabel').text(language['edit_specialist'] || 'Editar Especialista');
        $('#specialist_id').val(currentSpecialistData.specialist_id);
        $('#first_name').val(currentSpecialistData.first_name);
        $('#last_name').val(currentSpecialistData.last_name);
        $('#email').val(currentSpecialistData.email);
        $('#phone').val(currentSpecialistData.phone);
        $('#title_id').val(currentSpecialistData.title_id).trigger('change');
        $('#specialty_id').val(currentSpecialistData.specialty_id).trigger('change');
        $('#password').val('');
        $('#confirm_password').val('');

        $('#email-list').empty();
        $('#telephone-list').empty();
        if (Array.isArray(currentSpecialistData.emails)) {
            currentSpecialistData.emails.forEach(email => addEmailRow(email));
        }
        if (Array.isArray(currentSpecialistData.phones)) {
            currentSpecialistData.phones.forEach(tel => addTelephoneRow(tel));
        }

        const statusInput = document.getElementById('status');
        const checked = currentSpecialistData.status == 1 || currentSpecialistData.status === "1";
        resetSwitchery(statusInput, checked);

        countrySelect('phone', '[data-phone-select]', currentSpecialistData.phone, '#specialistModal .modal-body');

        // === INICIO: NUEVA LÓGICA DE VALIDACIÓN ===
        $('#email').attr('data-initial-value', currentSpecialistData.email || '');
        $('#phone').attr('data-initial-value', currentSpecialistData.phone || '');
        // Hacer contraseñas opcionales
        $('#password').attr('data-rules', 'longitudMinima:8');
        $('#confirm_password').attr('data-rules', 'coincideCon:#password');
        // === FIN: NUEVA LÓGICA DE VALIDACIÓN ===

        specialistModal.show();
    });

    // ... (Lógica de .deleteBtn y .viewBtn sin cambios) ...
    $(document).on('click', '.deleteBtn', function () {
        const specialistId = $(this).data('id');
        Swal.fire({
            title: language.delete_confirm_title_users,
            text: language.delete_confirm_text_users,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: language.delete_confirm_btn_users,
            cancelButtonText: language.cancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `specialist/${specialistId}`,
                    type: 'DELETE',
                    dataType: 'json',
                    success: res => {
                        if (res.value) {
                            Swal.fire(language.titleSuccess_user, language.success_delete_specialists, 'success');
                            loadSpecialists();
                        } else {
                            Swal.fire(language.titleError_user, res.message || language.error_delete_specialists, 'error');
                        }
                    },
                    error: () => Swal.fire(language.titleError_user, language.error_delete_specialists, 'error')
                });
            }
        });
    });
    $(document).on('click', '.viewBtn', function () {
        const specialistId = $(this).data('id');
        const specialistData = $('#specialistsTable').bootstrapTable('getRowByUniqueId', specialistId);
        if (!specialistData) return;

        // Emails adicionales
        let emailsHtml = '';
        if (Array.isArray(specialistData.emails) && specialistData.emails.length > 0) {
            specialistData.emails.forEach(email => {
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
        if (Array.isArray(specialistData.phones) && specialistData.phones.length > 0) {
            specialistData.phones.forEach(phone => {
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

        const modalHtml = `
        <h5 class="mb-3 text-uppercase bg-light p-2 rounded"><i class="mdi mdi-account-star me-1"></i> ${specialistData.first_name} ${specialistData.last_name}</h5>
        <p><strong>${traducciones.title || 'Título'}:</strong> ${specialistData.title_display_name}</p>
        <p><strong>${traducciones.specialty || 'Especialidad'}:</strong> ${specialistData.specialty_display_name}</p>
        <hr>
        <p><strong>${traducciones.email_label}:</strong> <a href="mailto:${specialistData.email}" class="text-body">${specialistData.email}</a></p>
        <p><strong>${traducciones.telephone_label}:</strong> <a href="tel:${String(specialistData.phone).replace(/[\s()-]/g, '')}" class="text-body">${specialistData.phone}</a></p>
        
        ${(emailsHtml || phonesHtml) ? `<hr><h5 class="mt-3 text-uppercase bg-light p-2 rounded"><i class="mdi mdi-card-account-phone-outline me-1"></i> ${traducciones.additional_contacts || 'Contactos Adicionales'}</h5>` : ''}
        
        <div class="row">
            <div class="col-md-6">${emailsHtml ? `<div class="mb-2"><h6>${traducciones.emails || 'Emails'}</h6>${emailsHtml}</div>` : ''}</div>
            <div class="col-md-6">${phonesHtml ? `<div><h6>${traducciones.telephones || 'Teléfonos'}</h6>${phonesHtml}</div>` : ''}</div>
        </div>
    `;

        $('#viewSpecialistContainer').html(modalHtml);
        new bootstrap.Modal(document.getElementById('viewSpecialistModal')).show();
    });

    // 6. REEMPLAZAR EL MANEJADOR 'submit' por 'validation:success'
    document.getElementById('specialistForm').addEventListener('validation:success', function (e) {

        // 7. Eliminada la validación manual
        const isEdit = !!$('#specialist_id').val();
        const formData = new FormData(this);
        const endpoint = isEdit ? `specialist/update/${$('#specialist_id').val()}` : 'specialist/create';

        const isChecked = document.getElementById('status').checked;
        formData.set('status', isChecked ? '1' : '0');

        if (isEdit) {
            formData.append('_method', 'PUT');
            // Mantener la lógica de no enviar contraseña si está vacía
            if (!$('#password').val()) {
                formData.delete('password');
                formData.delete('confirm_password');
            }
        }

        // ... (Lógica de recolección de emails y phones sin cambios) ...
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

        // ... (Lógica de $.ajax sin cambios) ...
        $.ajax({
            url: endpoint,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                if (res.value) {
                    specialistModal.hide();
                    Swal.fire(language.titleSuccess_user, isEdit ? language.success_update_specialists : language.success_create_specialists, 'success');
                    loadSpecialists();
                } else {
                    Swal.fire(language.titleError_user, res.message, 'error');
                }
            },
            error: function (xhr) {
                Swal.fire(language.titleError_user, xhr.responseJSON?.message || (isEdit ? language.error_update_specialists : language.error_create_specialists), 'error');
            }
        });
    });

    // 8. ELIMINAR LOS 'blur' listeners
    // document.getElementById('phone').addEventListener('blur', ...);
    // document.getElementById('email').addEventListener('blur', ...);

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

    loadSpecialists();
</script>

</body>

</html>