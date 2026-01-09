<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <h4 class="page-title my-2"><?= $traducciones['renal_function_records'] ?></h4>
    </div>
  </div>

  <style>
    /* ====== barra de tabs ====== */
    .nav-tabs {
      --tab-border: #e5e7eb;
      /* gris claro */
      --tab-text: #6b7280;
      /* gris medio */
      --tab-text-on: #111827;
      /* gris oscuro */
      border-color: var(--tab-border) !important;
      background: #fff;
    }

    /* ====== links ====== */
    .nav-tabs .nav-link {
      display: flex;
      align-items: center;
      gap: 6px;
      /* espacio entre icono y texto */
      color: var(--tab-text);
      border: 0;
      margin: .125rem .25rem;
      border-radius: .375rem;
      font-weight: 500;
      transition: all 0.2s ease-in-out;
    }

    .nav-tabs .nav-link i {
      font-size: 16px;
    }

    .nav-tabs .nav-link:hover {
      color: var(--tab-text-on);
    }

    /* ====== activo ====== */
    .nav-tabs .nav-link.active {
      color: var(--tab-text-on);
      background: #fff;
      border: 1px solid var(--tab-border);
      border-bottom-color: #fff;
      font-weight: 700;
      /* texto en negrita */
    }

    /* ====== contenido debajo de tabs ====== */
    .tab-content {
      background: #fff;
      border: 1px solid var(--tab-border);
      border-top: 0;
      border-radius: 0 0 .5rem .5rem;
      padding: 1rem;
    }

    /* ===== Estilos para selects coloreados y preview (Urine) ===== */
    .color-preview {
      width: 44px;
      height: 24px;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
      box-shadow: inset 0 0 0 2px rgba(0, 0, 0, .02);
    }

    .s2-color-pill {
      display: inline-flex;
      align-items: center;
      gap: .5rem;
    }

    .s2-dot {
      width: 12px;
      height: 12px;
      border-radius: 3px;
      display: inline-block;
      border: 1px solid rgba(0, 0, 0, .1);
    }

    .select2-container--default .select2-selection--single {
      height: 38px;
      border: 1px solid #ced4da;
      border-radius: .375rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 36px;
      padding-left: .75rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 36px;
      right: .5rem;
    }
  </style>

  <ul class="nav nav-tabs bg-white px-2 py-1" id="renalTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <a href="tab-dipstick" class="nav-link text-gray active" id="tab-dipstick-tab" data-bs-toggle="tab"
        data-bs-target="#tab-dipstick" role="tab" aria-controls="tab-dipstick" aria-selected="true">
        <i class="mdi mdi-test-tube"></i>
        <?= ($_SESSION['idioma'] ?? 'EN') === 'ES'
          ? 'Orina (tira) (semicuantitativa)'
          : 'Urine Dipstick (semi-quantitative)' ?>
      </a>
    </li>

    <li class="nav-item" role="presentation">
      <a href="tab-blood" class="nav-link text-gray" id="tab-blood-tab" data-bs-toggle="tab" data-bs-target="#tab-blood"
        role="tab" aria-controls="tab-blood" aria-selected="false" tabindex="-1">
        <i class="mdi mdi-water-outline"></i>
        <?= ($_SESSION['idioma'] ?? 'EN') === 'ES'
          ? 'Panel renal – Sangre'
          : 'Renal Panel – Blood' ?>
      </a>
    </li>
  </ul>


  <div class="tab-content">
    <div class="tab-pane fade show active" id="tab-dipstick" role="tabpanel" aria-labelledby="tab-dipstick-tab">
      <div id="toolbar-dipstick" class="my-3 d-none">
        <button class="btn btn-action-glucose w-20" onclick="openUrineModal()">
          + <?= $traducciones['add_new_urine'] ?>
        </button>
        <button id="btnExportCSV1" class="btn btn-action-lipid" type="button">
          <span class="mdi mdi-file-export-outline"></span> <?= $traducciones['export_csv'] ?>
        </button>
      </div>

      <div class="card">
        <div class="card-body">
          <table id="renalDipstickTable" data-toggle="table" data-search="true" data-show-refresh="true"
            data-page-list="[5, 10, 20]" data-page-size="5" data-pagination="true" data-show-columns="true"
            data-show-pagination-switch="true" class="table-borderless" data-toolbar="#toolbar-dipstick"
            data-locale="<?= $locale ?>">
            <thead>
              <tr>
                <th data-field="renal_date" data-sortable="true" data-formatter="dateFormatterRenalFunction">
                  <?= $traducciones['date'] ?>
                </th>
                <th data-field="renal_time" data-sortable="true"><?= $traducciones['time'] ?></th>
                <th data-field="albumin" data-sortable="true"><?= $traducciones['albumin'] ?></th>
                <th data-field="creatinine" data-sortable="true"><?= $traducciones['creatinine'] ?></th>
                <th data-field="urine_result" data-align="center" data-sortable="true"
                  data-formatter="urineResultFormatter">
                  <?= $traducciones['result'] ?? 'Result' ?>
                </th>
                <th data-field="id" data-align="center" data-formatter="dipstickActionFormatter">
                  <?= $traducciones['actions'] ?>
                </th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="tab-blood" role="tabpanel" aria-labelledby="tab-blood-tab">
      <div id="toolbar-blood" class="my-3 d-none">
        <button class="btn btn-action-glucose w-20" onclick="openBloodModal()">
          + <?= $traducciones['add_new_blood'] ?>
        </button>
        <button id="btnExportCSV2" class="btn btn-action-lipid" type="button">
          <span class="mdi mdi-file-export-outline"></span> <?= $traducciones['export_csv'] ?>
        </button>
      </div>

      <div class="card">
        <div class="card-body">
          <table id="renalBloodTable" data-toggle="table" data-search="true" data-show-refresh="true"
            data-page-list="[5, 10, 20]" data-page-size="5" data-pagination="true" data-show-columns="true"
            data-show-pagination-switch="true" class="table-borderless" data-toolbar="#toolbar-blood"
            data-locale="<?= $locale ?>">
            <thead>
              <tr>
                <th data-field="renal_date" data-sortable="true" data-formatter="dateFormatterRenalFunction">
                  <?= $traducciones['date'] ?>
                </th>
                <th data-field="renal_time" data-sortable="true"><?= $traducciones['time'] ?></th>
                <th data-field="uric_acid_blood" data-sortable="true">
                  <?= $traducciones['uric_acid_blood_table'] ?? 'Uric Acid (mg/dL)' ?>
                </th>
                <th data-field="serum_creatinine" data-sortable="true">
                  <?= $traducciones['serum_creatinine_table'] ?? 'Serum Creatinine (mg/dL)' ?>
                </th>
                <th data-field="bun_blood" data-sortable="true">
                  <?= $traducciones['bun_blood_table'] ?? 'Blood Urea Nitrogen (BUN) (mg/dL)' ?>
                </th>
                <th data-field="egfr" data-sortable="true">
                  <?= $traducciones['egfr'] ?? 'Estimated Glomerular Filtration Rate (eGFR) (mL/min/1.73m²)' ?>
                </th>
                <th data-field="bun_cr_ratio" data-sortable="true">
                  <?= $traducciones['bun_cr_ratio'] ?? 'BUN/Cr Ratio' ?>
                </th>

                <th data-field="id" data-align="center" data-formatter="bloodActionFormatter">
                  <?= $traducciones['actions'] ?>
                </th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="urineModal" tabindex="-1" aria-labelledby="urineModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="urineModalLabel">
          <?= $traducciones['add_edit_title_renal'] ?> –
          <?= ($_SESSION['idioma'] ?? 'EN') === 'ES' ? 'Orina (tira)' : 'Urine Dipstick' ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="urineForm" class="mt-2">
          <input type="hidden" name="editRenalId" id="editRenalIdUrine">

          <div class="row">
            <div class="col-md-6 mb-3" id="group_date_u">
              <label for="renal_date_u" class="form-label"><?= $traducciones['date'] ?></label>
              <input type="text" class="form-control fp-date" id="renal_date_u" name="renal_date"
                placeholder="<?= $traducciones['ph_date'] ?? 'Date (MM/DD/YYYY)' ?>">
            </div>
            <div class="col-md-6 mb-3" id="group_time_u">
              <label for="renal_time_u" class="form-label"><?= $traducciones['time'] ?></label>
              <input type="text" class="form-control fp-time" id="renal_time_u" name="renal_time"
                placeholder="<?= $traducciones['ph_time'] ?? 'Time (HH:mm:ss)' ?>">
            </div>

            <div class="col-md-6 mb-1">
              <label for="albumin_u" class="form-label"><?= $traducciones['albumin'] ?></label>
              <select class="form-select colored-select" id="albumin_u" name="albumin">
                <option value="N" data-color="#adbaa4">N (Normal)</option>
                <option value="1+" data-color="#9fb8a9">1+</option>
                <option value="2+" data-color="#92b7b1">2+</option>
                <option value="3+" data-color="#79abaf">3+</option>
              </select>
              <div id="albumin_preview" class="color-preview mt-2"></div>
            </div>

            <div class="col-md-6 mb-1">
              <label for="creatinine_u" class="form-label"><?= $traducciones['creatinine'] ?></label>
              <select class="form-select colored-select" id="creatinine_u" name="creatinine">
                <option value="1+" data-color="#a8aa80">1+</option>
                <option value="2+" data-color="#92a07e">2+</option>
                <option value="3+" data-color="#74846a">3+</option>
              </select>
              <div id="creatinine_preview" class="color-preview mt-2"></div>
            </div>

            <div class="col-md-12 mt-2">
              <label class="form-label"><?= $traducciones['result'] ?? 'Result' ?></label>
              <div class="form-control-plaintext fw-bold" id="urine_result_text">--</div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" form="urineForm" class="btn btn-save">
          <i class="mdi mdi-content-save-outline"></i> <?= $traducciones['save'] ?>
        </button>
        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
          <i class="mdi mdi-cancel"></i> <?= $traducciones['cancel'] ?>
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="bloodModal" tabindex="-1" aria-labelledby="bloodModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bloodModalLabel">
          <?= $traducciones['add_edit_title_renal'] ?> –
          <?= ($_SESSION['idioma'] ?? 'EN') === 'ES' ? 'Sangre' : 'Blood' ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="bloodForm" class="mt-2">
          <input type="hidden" name="editRenalId" id="editRenalIdBlood">
          <div class="row">
            <div class="col-md-6 mb-3" id="group_date_b">
              <label for="renal_date_b" class="form-label"><?= $traducciones['date'] ?></label>
              <input type="text" class="form-control fp-date" id="renal_date_b" name="renal_date"
                placeholder="<?= $traducciones['ph_date'] ?? 'Date (MM/DD/YYYY)' ?>">
            </div>

            <div class="col-md-6 mb-3" id="group_time_b">
              <label for="renal_time_b" class="form-label"><?= $traducciones['time'] ?></label>
              <input type="text" class="form-control fp-time" id="renal_time_b" name="renal_time"
                placeholder="<?= $traducciones['ph_time'] ?? 'Time (HH:mm:ss)' ?>">
            </div>

            <div class="col-md-6 mb-3">
              <label for="ua_mg_dl_b" class="form-label">
                <?= $traducciones['uric_acid_blood_form'] ?? 'UA (Uric Acid)' ?>
              </label>
              <input type="text" class="form-control number" id="ua_mg_dl_b" name="uric_acid_blood"
                placeholder="<?= $traducciones['ph_uric_acid_number'] ?? 'e.g., 5.6' ?>">
            </div>

            <div class="col-md-6 mb-3">
              <label for="cr_mg_dl_b" class="form-label">
                <?= $traducciones['serum_creatinine_form'] ?? 'CR (Creatinine)' ?>
              </label>
              <input type="text" class="form-control number" id="cr_mg_dl_b" name="serum_creatinine"
                placeholder="<?= $traducciones['ph_creatinine_number'] ?? 'e.g., 1.0' ?>">
            </div>

            <div class="col-md-6 mb-3">
              <label for="bun_mg_dl_b" class="form-label">
                <?= $traducciones['bun_blood_form'] ?? 'Blood Urea Nitrogen (BUN) (mg/dL)' ?>
              </label>
              <input type="text" class="form-control number" id="bun_mg_dl_b" name="bun_blood"
                placeholder="<?= $traducciones['ph_bun_number'] ?? 'e.g., 14' ?>">
            </div>

            <div class="col-md-12">
              <small class="text-muted">
                <?= ($_SESSION['idioma'] ?? 'EN') === 'ES'
                  ? 'El sistema calcula eGFR (CKD-EPI 2021) usando tu edad y sexo del perfil.'
                  : 'The system computes eGFR (CKD-EPI 2021) using your profile age and sex.' ?>
              </small>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="submit" form="bloodForm" class="btn btn-save">
          <i class="mdi mdi-content-save-outline"></i> <?= $traducciones['save'] ?>
        </button>
        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
          <i class="mdi mdi-cancel"></i> <?= $traducciones['cancel'] ?>
        </button>
      </div>
    </div>
  </div>
</div>


<script src="public/assets/js/logout.js"></script>
<script type="module">
  import { validateFormFields, clearValidationMessages } from "./public/assets/js/helpers/helpers.js";
  import { reloadAlerts } from "./public/assets/js/controllers/notificationsController.js";

  /* ========= Flatpickr config unificada ========= */
  const FP = {
    date: {
      altInput: true,     // lo visible al usuario
      altFormat: 'm/d/Y',   // MM/DD/YYYY (mostrado)
      dateFormat: 'Y-m-d',  // Y-m-d (enviado al backend)
      allowInput: true
    },
    time: {
      enableTime: true,
      noCalendar: true,
      dateFormat: 'H:i:S',  // 24h con segundos (backend)
      time_24hr: true,
      enableSeconds: true,
      allowInput: true
    },
    inst: {}
  };
  function fpSet(id, valueYmdOrTime) {
    const inst = FP.inst[id];
    if (!inst) return;
    if (valueYmdOrTime) {
      const fmt = id.includes('_time_') || id.endsWith('_time_u') || id.endsWith('_time_b') ? 'H:i:S' : 'Y-m-d';
      inst.setDate(valueYmdOrTime, true, fmt);
    } else {
      inst.clear();
    }
  }

  // ===== Idioma helper (para etiquetas N/A)
  function isES() { return (window?.APP_LANG || (<?= json_encode($_SESSION['idioma'] ?? 'EN') ?>)) === 'ES'; }
  function labelNormal() { return isES() ? 'N (Normal)' : 'N (Normal)'; }
  function labelAbnormal() { return isES() ? 'A (Anormal)' : 'A (Abnormal)'; }

  // ===== Date formatter (MM/DD/YYYY)
  function dateFormatterRenalFunction(value) {
    if (!value) return '';
    let dateStr = typeof value === 'string' ? value : (new Date(value)).toISOString().split('T')[0];
    const [y, m, d] = dateStr.split('-');
    const mm = String(m).padStart(2, '0');
    const dd = String(d).padStart(2, '0');
    return `${mm}/${dd}/${y}`;
  }
  window.dateFormatterRenalFunction = dateFormatterRenalFunction;

  // ===== Action formatters (separados)
  function dipstickActionFormatter(value, row) {
    return `
      <a href="component_renal?id=${row.renal_function_id}">
        <button class="btn btn-view action-icon" title="${language.viewComponent_renal}">
          <i class="mdi mdi-eye-outline"></i>
        </button>
      </a>
      <a href="user_test_documents?id_test_panel=60819af9-0533-472c-9d5a-24a5df5a83f7&id_test=${row.renal_function_id}">
        <button class="btn btn-image-test action-icon" title="${language.viewImage_renal}">
          <i class="mdi mdi-file-image-outline"></i>
        </button>
      </a>
      <button class="btn btn-pencil action-icon" onclick="openUrineModal('${row.renal_function_id}')" title="${language.edit_title_renal}">
        <i class="mdi mdi-pencil-outline"></i>
      </button>`;
  }
  window.dipstickActionFormatter = dipstickActionFormatter;

  function bloodActionFormatter(value, row) {
    return `
      <a href="component_renal?id=${row.renal_function_id}">
        <button class="btn btn-view action-icon" title="${language.viewComponent_renal}">
          <i class="mdi mdi-eye-outline"></i>
        </button>
      </a>
      <a href="user_test_documents?id_test_panel=60819af9-0533-472c-9d5a-24a5df5a83f7&id_test=${row.renal_function_id}">
        <button class="btn btn-image-test action-icon" title="${language.viewImage_renal}">
          <i class="mdi mdi-file-image-outline"></i>
        </button>
      </a>
      <button class="btn btn-pencil action-icon" onclick="openBloodModal('${row.renal_function_id}')" title="${language.edit_title_renal}">
        <i class="mdi mdi-pencil-outline"></i>
      </button>`;
  }
  window.bloodActionFormatter = bloodActionFormatter;

  // ===== Urine result (matriz) + etiquetas
  function computeUrineBase(albumin, creatinine) {
    if (!albumin || !creatinine) return 'N';
    if (albumin === 'N') return 'N';
    if (albumin === '1+') return (creatinine === '3+') ? 'N' : 'A';
    if (albumin === '2+' || albumin === '3+') return 'A';
    return 'N';
  }
  function computeUrineResultLabeled(albumin, creatinine) {
    const base = computeUrineBase(albumin, creatinine);
    return base === 'N' ? labelNormal() : labelAbnormal();
  }
  // Formatter para la columna en la tabla
  function urineResultFormatter(value, row) {
    const v = (value || '').toString().toUpperCase();
    const labeled = (v === 'N') ? labelNormal() : (v === 'A') ? labelAbnormal() : v;
    return `<span class="fw-semibold">${labeled}</span>`;
  }
  window.urineResultFormatter = urineResultFormatter;

  // ===== Preview de color debajo del select (Urine)
  function updatePreviewFromSelect(selectEl, previewId) {
    const preview = document.getElementById(previewId);
    if (!selectEl || !preview) return;
    const sel = selectEl instanceof HTMLElement ? selectEl : document.getElementById(selectEl);
    const opt = sel?.options?.[sel.selectedIndex];
    const color = opt?.dataset?.color || '#ffffff';
    preview.style.backgroundColor = color;
  }

  // ====== SELECT2 helpers (plantillas) ======
  function s2Template(option) {
    if (!option.id) return option.text;
    const color = option.element?.dataset?.color || '#ffffff';
    const $node = $(`
      <span class="s2-color-pill">
        <span class="s2-dot" style="background-color:${color}"></span>
        <span>${option.text}</span>
      </span>
    `);
    return $node;
  }

  function initUrineSelect2() {
    const $alb = $('#albumin_u');
    const $cre = $('#creatinine_u');
    const dropdownParent = $('#urineModal');

    // Evitar duplicados si el modal se abre varias veces
    if ($alb.hasClass('select2-hidden-accessible')) $alb.select2('destroy');
    if ($cre.hasClass('select2-hidden-accessible')) $cre.select2('destroy');

    $alb.select2({
      width: '100%',
      dropdownParent,
      templateResult: s2Template,
      templateSelection: s2Template,
      minimumResultsForSearch: Infinity
    }).on('change', function () {
      updatePreviewFromSelect(this, 'albumin_preview');
      const albVal = $(this).val();
      const creVal = $cre.val();
      document.getElementById('urine_result_text').textContent = computeUrineResultLabeled(albVal, creVal);
    });

    $cre.select2({
      width: '100%',
      dropdownParent,
      templateResult: s2Template,
      templateSelection: s2Template,
      minimumResultsForSearch: Infinity
    }).on('change', function () {
      updatePreviewFromSelect(this, 'creatinine_preview');
      const albVal = $alb.val();
      const creVal = $(this).val();
      document.getElementById('urine_result_text').textContent = computeUrineResultLabeled(albVal, creVal);
    });

    // Inicializar previews y resultado al crear Select2
    updatePreviewFromSelect($alb[0], 'albumin_preview');
    updatePreviewFromSelect($cre[0], 'creatinine_preview');
    document.getElementById('urine_result_text').textContent =
      computeUrineResultLabeled($alb.val(), $cre.val());
  }

  /* ========= CAMBIO: utilidades para mostrar/ocultar fecha y hora según crear/editar ========= */
  function toggleDateTimeFields(scope, isEditMode) {
    // scope: 'u' (urine) | 'b' (blood)
    // Los campos ya no se ocultan (d-none quitado del HTML)
    // La lógica de limpieza y población ahora está en las funciones open...Modal
  }
  
  // CAMBIO: Esta función ya no es necesaria
  /*
  // Helper: limpia renal_date / renal_time si vienen vacíos para no enviarlos
  function stripEmptyDateTime(payload) {
    if (payload.renal_date === '') delete payload.renal_date;
    if (payload.renal_time === '') delete payload.renal_time;
    return payload;
  }
  */

  // ===== Modals open
  function openUrineModal(recordId = null) {
    const form = document.getElementById('urineForm');
    clearValidationMessages(form);
    form.reset();
    $('#editRenalIdUrine').val('');
    const modal = new bootstrap.Modal(document.getElementById('urineModal'));

    const afterFill = (isEdit) => {
      // Asegurar defaults
      if (!$('#albumin_u').val()) $('#albumin_u').val('N');
      if (!$('#creatinine_u').val()) $('#creatinine_u').val('1+');

      // CAMBIO: Llamar a la función vacía (no hace nada)
      toggleDateTimeFields('u', isEdit);

      // Inicializar Select2
      initUrineSelect2();
      modal.show();
    };

    // limpiar pickers
    fpSet('renal_date_u', null);
    fpSet('renal_time_u', null);

    if (recordId) {
      // MODO EDITAR
      $.getJSON(`renal-function/${recordId}`, (res) => {
        const data = res?.data || {};
        $('#editRenalIdUrine').val(data.renal_function_id);

        // set con flatpickr
        fpSet('renal_date_u', data.renal_date || null);
        fpSet('renal_time_u', data.renal_time || null);

        $('#albumin_u').val(data.albumin ?? 'N');
        $('#creatinine_u').val(data.creatinine ?? '1+');
        afterFill(true); // editar
      }).fail(() => Swal.fire(language.errorTitle_renal, language.loadError_renal, 'error'));
    } else {
      // MODO CREAR
      $('#albumin_u').val('N');
      $('#creatinine_u').val('1+');
      
      // CAMBIO: Auto-poblar con fecha y hora actual
      const now = new Date();
      const currentDate = now.toISOString().split('T')[0]; // YYYY-MM-DD
      const currentTime = now.toTimeString().split(' ')[0]; // HH:mm:ss
      fpSet('renal_date_u', currentDate);
      fpSet('renal_time_u', currentTime);
      
      afterFill(false); // crear
    }
  }
  window.openUrineModal = openUrineModal;

  function openBloodModal(recordId = null) {
    const form = document.getElementById('bloodForm');
    clearValidationMessages(form);
    form.reset();
    $('#editRenalIdBlood').val('');
    const modal = new bootstrap.Modal(document.getElementById('bloodModal'));

    // limpiar pickers
    fpSet('renal_date_b', null);
    fpSet('renal_time_b', null);

    if (recordId) {
      // MODO EDITAR
      $.getJSON(`renal-function/${recordId}`, (dres) => {
        const d = dres?.data || {};
        $('#editRenalIdBlood').val(d.renal_function_id);

        // set con flatpickr
        fpSet('renal_date_b', d.renal_date || null);
        fpSet('renal_time_b', d.renal_time || null);

        // mg/dL
        $('#cr_mg_dl_b').val(d.serum_creatinine ?? '');
        $('#ua_mg_dl_b').val(d.uric_acid_blood ?? '');
        $('#bun_mg_dl_b').val(d.bun_blood ?? '');

        toggleDateTimeFields('b', true);  // editar
        modal.show();
      }).fail(() => Swal.fire(language.errorTitle_renal, language.loadError_renal, 'error'));
    } else {
      // MODO CREAR
      // CAMBIO: Auto-poblar con fecha y hora actual
      const now = new Date();
      const currentDate = now.toISOString().split('T')[0]; // YYYY-MM-DD
      const currentTime = now.toTimeString().split(' ')[0]; // HH:mm:ss
      fpSet('renal_date_b', currentDate);
      fpSet('renal_time_b', currentTime);

      toggleDateTimeFields('b', false);  // crear
      modal.show();
    }
  }
  window.openBloodModal = openBloodModal;

  // ===== Carga/partición de datos en tablas
  function loadRenalFunctionData() {
    $.ajax({
      url: 'renal-function',
      method: 'GET',
      dataType: 'json',
      success: function (res) {
        const all = res.data || [];
        const dipstick = [];
        const blood = [];

        all.forEach(row => {
          const hasDipstick = (row.albumin ?? null) || (row.creatinine ?? null);
          const hasBlood = (row.serum_creatinine ?? null) || (row.uric_acid_blood ?? null) || (row.bun_blood ?? null) || (row.egfr ?? null);

          if (hasDipstick) {
            const base = row.urine_result || computeUrineBase(row.albumin, row.creatinine); // 'N'/'A'
            dipstick.push({ ...row, urine_result: base });
          }
          if (hasBlood) blood.push(row);
        });

        $('#renalDipstickTable').bootstrapTable('load', dipstick).bootstrapTable('resetSearch', '');
        $('#renalBloodTable').bootstrapTable('load', blood).bootstrapTable('resetSearch', '');
      },
      error: function (xhr) {
        Swal.fire(language.errorTitle_renal, xhr.responseJSON?.message || language.loadError_renal, 'error');
      }
    });
  }
  window.loadRenalFunctionData = loadRenalFunctionData; // por si lo necesitas global

  // ===== Toolbars: export
  $('#btnExportCSV1, #btnExportCSV2').on('click', () => {
    window.location.href = 'renal-function/export';
  });

  // ===== Buscador con querystring
  function getSearchParam() {
    const params = new URLSearchParams(window.location.search);
    return params.get('search');
  }
  function applySearchAfterTableLoads(tableId, searchValue) {
    if (!searchValue) return;
    const $table = $(`#${tableId}`);
    $table.on('post-body.bs.table', function () {
      const $searchInput = $table.closest('.bootstrap-table').find('.search input');
      if ($searchInput.length) {
        $searchInput.val(searchValue).trigger('input');
        const rows = $table.bootstrapTable('getData');
        const index = rows.findIndex(row =>
          Object.values(row).some(value => String(value).includes(searchValue))
        );
        if (index >= 0) {
          $table.bootstrapTable('check', index);
          const $tr = $table.find(`tbody tr[data-index="${index}"]`);
          $tr.addClass('table-success');
          $tr[0]?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    });
  }

  // ===== Submit handlers
  $('#urineForm').on('submit', function (e) {
    e.preventDefault();

    const id = $('#editRenalIdUrine').val();
    // CAMBIO: Fecha y hora siempre requeridos (esto ya estaba bien)
    const req = ['renal_date', 'renal_time', 'albumin', 'creatinine'];

    if (!validateFormFields(this, req, language.input_generic_error)) return;

    const payload = {};
    $(this).serializeArray().forEach(i => payload[i.name] = i.value);
    // CAMBIO: Se elimina la llamada a stripEmptyDateTime
    // stripEmptyDateTime(payload);

    const url = id ? `renal-function/${id}` : 'renal-function';

    $.ajax({
      url,
      type: id ? 'PUT' : 'POST',
      data: JSON.stringify(payload),
      dataType: 'json',
      contentType: 'application/json',
      success: (r) => {
        if (r.value) {
          Swal.fire(language.successTitle_renal, language.saveSuccess_renal, 'success');
          $('#urineModal').modal('hide');
          loadRenalFunctionData();
          reloadAlerts();
        } else {
          Swal.fire(language.errorTitle_renal, r.message || language.saveError_renal, 'error');
        }
      },
      error: () => Swal.fire(language.errorTitle_renal, language.saveError_renal, 'error')
    });
  });

  $('#bloodForm').on('submit', function (e) {
    e.preventDefault();

    const id = $('#editRenalIdBlood').val();
    
    // MODIFICACIÓN: Se añaden los campos numéricos a la validación de requeridos
    const req = [
      'renal_date', 
      'renal_time',
      'uric_acid_blood',
      'serum_creatinine',
      'bun_blood'
    ];

    if (!validateFormFields(this, req, language.input_generic_error)) return;

    const payload = {};
    $(this).serializeArray().forEach(i => payload[i.name] = i.value);
    // CAMBIO: Se elimina la llamada a stripEmptyDateTime
    // stripEmptyDateTime(payload);

    const url = id ? `renal-function/${id}` : 'renal-function';

    $.ajax({
      url,
      type: id ? 'PUT' : 'POST',
      data: JSON.stringify(payload),
      dataType: 'json',
      contentType: 'application/json',
      success: (r) => {
        if (r.value) {
          Swal.fire(language.successTitle_renal, language.saveSuccess_renal, 'success');
          $('#bloodModal').modal('hide');
          loadRenalFunctionData();
          reloadAlerts();
        } else {
          Swal.fire(language.errorTitle_renal, r.message || language.saveError_renal, 'error');
        }
      },
      error: () => Swal.fire(language.errorTitle_renal, language.saveError_renal, 'error')
    });
  });

  // ===== init: flatpickr, toolbars, datos
  document.addEventListener('DOMContentLoaded', () => {
    // Instancias flatpickr con altInput MM/DD/YYYY
    FP.inst.renal_time_u = flatpickr('#renal_time_u', FP.time);
    FP.inst.renal_time_b = flatpickr('#renal_time_b', FP.time);
    FP.inst.renal_date_u = flatpickr('#renal_date_u', FP.date);
    FP.inst.renal_date_b = flatpickr('#renal_date_b', FP.date);

    // Placeholders para los inputs de fecha/hora (opcional pero bueno)
    const PH = {
        date: '<?= $traducciones['ph_date'] ?? 'Date (MM/DD/YYYY)' ?>',
        time: '<?= $traducciones['ph_time'] ?? 'Time (HH:mm:ss)' ?>'
    };
    const setAltPlaceholder = (inst, text) => {
      if (inst && inst.altInput) inst.altInput.setAttribute('placeholder', text);
    };
    setAltPlaceholder(FP.inst.renal_date_u, PH.date);
    setAltPlaceholder(FP.inst.renal_date_b, PH.date);
    setAltPlaceholder(FP.inst.renal_time_u, PH.time);
    setAltPlaceholder(FP.inst.renal_time_b, PH.time);


    // Mostrar toolbars
    document.getElementById('toolbar-dipstick')?.classList.remove('d-none');
    document.getElementById('toolbar-blood')?.classList.remove('d-none');

    // Cargar tablas
    loadRenalFunctionData();

    // tooltips
    [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].forEach(el => new bootstrap.Tooltip(el));

    // Inicializar Select2 en los selects de orina
    initUrineSelect2();

    // aplicar búsqueda si viene en querystring
    const search = getSearchParam();
    if ($('#renalDipstickTable').length) applySearchAfterTableLoads('renalDipstickTable', search);
    if ($('#renalBloodTable').length) applySearchAfterTableLoads('renalBloodTable', search);

    // MODIFICACIÓN: Añadir validación para campos numéricos (como en el ejemplo)
    document.querySelectorAll('.form-control.number').forEach(input => {
      input.addEventListener('input', () => { 
        // Solo permite números, puntos y comas (como en el ejemplo)
        input.value = input.value.replace(/[^0-9\.,]/g, ''); 
      });
      input.addEventListener('paste', (e) => {
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        if (/[^0-9\.,]/.test(paste)) { // Bloquea si el pegado contiene caracteres no válidos
          e.preventDefault();
        }
      });
    });
    
  });
</script>