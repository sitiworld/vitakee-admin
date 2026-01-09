<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <h4 class="page-title my-2"><?= $traducciones['title_energy_metabolism'] ?></h4>
    </div>
  </div>

<style>
  /* ====== barra de tabs (mismo look que renal/perfil, mejorada con iconos) ====== */
  .nav-tabs {
    --tab-border: #e5e7eb;      /* gris claro */
    --tab-text:   #6b7280;      /* gris medio */
    --tab-text-on:#111827;      /* gris oscuro */
    border-color: var(--tab-border) !important;
    background:#fff;
  }

  .nav-tabs .nav-link {
    display: flex;
    align-items: center;
    gap: 6px; /* espacio entre icono y texto */
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

  .nav-tabs .nav-link.active {
    color: var(--tab-text-on);
    background:#fff;
    border: 1px solid var(--tab-border);
    border-bottom-color: #fff;
    font-weight: 700; /* texto en negrita para resaltar selección */
  }

  .tab-content {
    background:#fff;
    border: 1px solid var(--tab-border);
    border-top: 0;
    border-radius: 0 0 .5rem .5rem;
    padding: 1rem;
  }
</style>

<ul class="nav nav-tabs bg-white px-2 py-1" id="emTabs" role="tablist">
  <li class="nav-item" role="presentation">
    <a href="#outer-gk-pane" class="nav-link text-gray active" id="outer-gk-tab"
       data-bs-toggle="tab" data-bs-target="#outer-gk-pane" role="tab"
       aria-controls="outer-gk-pane" aria-selected="true">
      <i class="mdi mdi-water"></i>
      <?= $traducciones['tab_glucose_ketone'] ?? 'Glucose & Ketones' ?>
    </a>
  </li>

  <li class="nav-item" role="presentation">
    <a href="#outer-hba1c-pane" class="nav-link text-gray" id="outer-hba1c-tab"
       data-bs-toggle="tab" data-bs-target="#outer-hba1c-pane" role="tab"
       aria-controls="outer-hba1c-pane" aria-selected="false" tabindex="-1">
      <i class="mdi mdi-flask-outline"></i>
      <?= $traducciones['tab_hba1c'] ?? 'HbA1c' ?>
    </a>
  </li>
</ul>


  <div class="tab-content">
    <div class="tab-pane fade show active" id="outer-gk-pane" role="tabpanel" aria-labelledby="outer-gk-tab">
      <div id="toolbar-gk" class="my-2 d-none d-flex gap-2 align-items-center">
        <button id="btnAddGK" class="btn btn-action-glucose w-20" onclick="openEnergyMetabolismModal(null, 'gk')">
          + <?= $traducciones['add_new_glucose'] ?>
        </button>
        <button id="btnExportCSV_GK" class="btn btn-action-lipid" type="button">
          <span class="mdi mdi-file-export-outline"></span> <?= $traducciones['export_csv'] ?>
        </button>
      </div>

      <div class="card">
        <div class="card-body">
          <table id="energyMetabolismTableGK"
            data-toggle="table"
            data-search="true"
            data-show-refresh="true"
            data-page-list="[5, 10, 20]"
            data-page-size="5"
            data-pagination="true"
            data-show-no-records="true"
            data-show-empty="true"
            data-undefined-text=""
            data-show-columns="true"
            data-show-pagination-switch="true"
            class="table-borderless"
            data-toolbar="#toolbar-gk"
            data-locale="<?= $locale ?>">
            <thead>
              <tr>
                <th data-field="energy_date" data-sortable="true" data-formatter="dateFormatterEnergyMetabolism">
                  <?= $traducciones['date'] ?>
                </th>
                <th data-field="energy_time" data-sortable="true"><?= $traducciones['time'] ?></th>
                <th data-field="glucose" data-sortable="true"><?= $traducciones['glucose_register'] ?></th>
                <th data-field="ketone" data-sortable="true"><?= $traducciones['ketone_register'] ?></th>
                <th data-field="id" data-align="center" data-formatter="gkActionFormatter">
                  <?= $traducciones['actions'] ?>
                </th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="outer-hba1c-pane" role="tabpanel" aria-labelledby="outer-hba1c-tab">
      <div id="toolbar-hba1c" class="my-2 d-none d-flex gap-2 align-items-center">
        <button id="btnAddHbA1c" class="btn btn-action-glucose w-20" onclick="openEnergyMetabolismModal(null, 'hba1c')">
          + <?= $traducciones['add_new_hb1ac'] ?>
        </button>
        <button id="btnExportCSV_Hb" class="btn btn-action-lipid" type="button">
          <span class="mdi mdi-file-export-outline"></span> <?= $traducciones['export_csv'] ?>
        </button>
      </div>

      <div class="card">
        <div class="card-body">
          <table id="energyMetabolismTableHbA1c"
            data-toggle="table"
            data-search="true"
            data-show-refresh="true"
            data-page-list="[5, 10, 20]"
            data-page-size="5"
            data-pagination="true"
            data-show-no-records="true"
            data-show-empty="true"
            data-undefined-text=""
            data-show-columns="true"
            data-show-pagination-switch="true"
            class="table-borderless"
            data-toolbar="#toolbar-hba1c"
            data-locale="<?= $locale ?>">
            <thead>
              <tr>
                <th data-field="energy_date" data-sortable="true" data-formatter="dateFormatterEnergyMetabolism">
                  <?= $traducciones['date'] ?>
                </th>
                <th data-field="energy_time" data-sortable="true"><?= $traducciones['time'] ?></th>
                <th data-field="hba1c" data-sortable="true"><?= $traducciones['hba1c_label'] ?? 'HbA1c (%)' ?></th>
                <th data-field="derived_value" data-sortable="true"><?= $traducciones['eag_label'] ?? 'eAG (mg/dL)' ?></th>
                <th data-field="id" data-align="center" data-formatter="hba1cActionFormatter">
                  <?= $traducciones['actions'] ?>
                </th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>

  </div> <div class="modal fade" id="energyMetabolismModalGK" tabindex="-1" aria-labelledby="energyMetabolismLabelGK" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="energyMetabolismFormGK">
          <div class="modal-header">
            <h5 class="modal-title" id="energyMetabolismLabelGK">
              <?= $traducciones['add_modal_title_glucose'] ?>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="recordIdGK" name="id">
            <div class="row">
              <div class="col-md-6 mb-3" id="group_date_gk">
  <label class="form-label"><?= $traducciones['date'] ?></label>
  <input type="text" class="form-control fp-date" id="energy_date" name="energy_date"
         placeholder="<?= $traducciones['ph_date'] ?? 'Date (MM/DD/YYYY)' ?>">
</div>
<div class="col-md-6 mb-3" id="group_time_gk">
  <label class="form-label"><?= $traducciones['time'] ?></label>
  <input type="text" class="form-control fp-time" id="energy_time" name="energy_time"
         placeholder="<?= $traducciones['ph_time'] ?? 'Time (HH:mm:ss)' ?>">
</div>

<div class="col-md-6 mb-3">
  <label class="form-label"><?= $traducciones['glucose_register'] ?></label>
  <input type="text" min="50" max="500" step="0.1" class="form-control number" id="glucose" name="glucose"
         placeholder="<?= $traducciones['ph_glucose'] ?? 'e.g., 95 mg/dL' ?>">
</div>
<div class="col-md-6 mb-3">
  <label class="form-label"><?= $traducciones['ketone_register'] ?></label>
  <input type="text" min="0" max="10" step="0.1" class="form-control number" id="ketone" name="ketone"
         placeholder="<?= $traducciones['ph_ketone'] ?? 'e.g., 0.6 mmol/L' ?>">
</div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-save">
              <i class="mdi mdi-content-save-outline"></i> <?= $traducciones['save'] ?>
            </button>
            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
              <i class="mdi mdi-cancel"></i> <?= $traducciones['cancel'] ?>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="energyMetabolismModalHbA1c" tabindex="-1" aria-labelledby="energyMetabolismLabelHbA1c" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="energyMetabolismFormHbA1c">
          <div class="modal-header">
            <h5 class="modal-title" id="energyMetabolismLabelHbA1c">
              <?= $traducciones['add_modal_title_hba1c'] ?? 'Add HbA1c' ?>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="recordIdHbA1c" name="id">
            <div class="row">
              <div class="col-md-6 mb-3" id="group_date_hb">
  <label class="form-label"><?= $traducciones['date'] ?></label>
  <input type="text" class="form-control fp-date" id="hba1c_date" name="hba1c_date"
         placeholder="<?= $traducciones['ph_date'] ?? 'Date (MM/DD/YYYY)' ?>">
</div>
<div class="col-md-6 mb-3" id="group_time_hb">
  <label class="form-label"><?= $traducciones['time'] ?></label>
  <input type="text" class="form-control fp-time" id="hba1c_time" name="hba1c_time"
         placeholder="<?= $traducciones['ph_time'] ?? 'Time (HH:mm:ss)' ?>">
</div>

<div class="col-md-4 mb-3">
  <label class="form-label"><?= $traducciones['hba1c_label'] ?? 'HbA1c (%)' ?></label>
  <input type="text" min="3.5" max="15.0" step="0.1" class="form-control number" id="hba1c" name="hba1c"
         placeholder="<?= $traducciones['ph_hba1c'] ?? 'e.g., 5.6 %' ?>">
</div>
<div class="col-md-4 mb-3">
  <label class="form-label"><?= $traducciones['hba1c_target'] ?? 'Target HbA1c (%)' ?></label>
  <input type="text" min="3.5" max="15.0" step="0.1" class="form-control number" id="hba1c_target" name="hba1c_target"
         placeholder="<?= $traducciones['ph_hba1c_target'] ?? 'e.g., 6.5 %' ?>">
</div>
<div class="col-md-4 mb-3">
  <label class="form-label"><?= $traducciones['eag_label'] ?? 'eAG (mg/dL)' ?></label>
  <div class="form-control-plaintext" id="derived_value_text">—</div>
  <input type="hidden" id="derived_value" name="derived_value">
  <input type="hidden" id="derived_unit" name="derived_unit" value="mg/dL">
</div>
<div class="col-12 mb-3">
  <label class="form-label"><?= $traducciones['notes_label'] ?? 'Notes' ?></label>
  <textarea class="form-control" id="notes" name="notes" rows="2"
            placeholder="<?= $traducciones['ph_notes'] ?? 'e.g., Fasting reading' ?>"></textarea>
</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-save">
              <i class="mdi mdi-content-save-outline"></i> <?= $traducciones['save'] ?>
            </button>
            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
              <i class="mdi mdi-cancel"></i> <?= $traducciones['cancel'] ?>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
<div class="rightbar-overlay"></div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script type="module">
import { validateFormFields, clearValidationMessages } from "./public/assets/js/helpers/helpers.js";
import { reloadAlerts } from "./public/assets/js/controllers/notificationsController.js";
/* ========= i18n para Energy Metabolism ========= */
/* ========= i18n (Glucose & Ketone) ========= */
const lang = '<?= $_SESSION["idioma"] ?? "EN"; ?>';

/* ========= Títulos de modales ========= */
const modalTitles = {
  gk: {
    create: '<?= $traducciones['add_modal_title_glucose'] ?? 'Add glucose/ketone' ?>',
    edit:   '<?= $traducciones['edit_modal_title_glucose'] ?? 'Edit glucose/ketone' ?>'
  },
  hba1c: {
    create: '<?= $traducciones['add_modal_title_hba1c'] ?? 'Add HbA1c' ?>',
    edit:   '<?= $traducciones['edit_modal_title_hba1c'] ?? 'Edit HbA1c' ?>'
  }
};

function setModalTitle(scope, isEdit) {
  const el = document.getElementById(scope === 'hba1c' ? 'energyMetabolismLabelHbA1c' : 'energyMetabolismLabelGK');
  if (el) el.textContent = isEdit ? modalTitles[scope].edit : modalTitles[scope].create;
}

const messages = {
  ES: {
    savedTitle:   '<?= $traducciones['savedTitle_glucose_ketone']   ?? '¡Guardado!' ?>',
    saveText:     '<?= $traducciones['saveText_glucose_ketone']     ?? 'Registro guardado con éxito.' ?>',
    updatedTitle: '<?= $traducciones['updatedTitle_glucose_ketone'] ?? '¡Actualizado!' ?>',
    updateText:   '<?= $traducciones['updateText_glucose_ketone']   ?? 'Registro actualizado con éxito.' ?>',
    loadError:    '<?= $traducciones['loadError_glucose_ketone']    ?? 'No se pudieron cargar los registros de glucosa y cetonas.' ?>',
    editError:    '<?= $traducciones['editError_glucose_ketone']    ?? 'No se pudo cargar el registro para editar.' ?>',
    saveError:    '<?= $traducciones['saveError_glucose_ketone']    ?? 'No se pudo guardar el registro.' ?>',
    updateError:  '<?= $traducciones['updateError_glucose_ketone']  ?? 'Algo salió mal al actualizar el registro.' ?>',
    deleteError:  '<?= $traducciones['deleteError_glucose_ketone']  ?? 'No se pudo eliminar el registro.' ?>',
    deleteConfirmTitle: '<?= $traducciones['deleteConfirm_glucose_ketone']   ?? '¿Estás seguro?' ?>',
    deleteWarning:      '<?= $traducciones['deleteWarning_glucose_ketone']   ?? '¡Esta acción no se puede deshacer!' ?>',
    deleteConfirmBtn:   '<?= $traducciones['deleteConfirmBtn_glucose_ketone']?? '¡Sí, eliminar!' ?>',
    deleteSuccessText:  '<?= $traducciones['deleteSuccess_glucose_ketone']   ?? 'El registro ha sido eliminado.' ?>'
  },
  EN: {
    savedTitle:   '<?= $traducciones['savedTitle_glucose_ketone']   ?? 'Saved!' ?>',
    saveText:     '<?= $traducciones['saveText_glucose_ketone']     ?? 'Record saved successfully.' ?>',
    updatedTitle: '<?= $traducciones['updatedTitle_glucose_ketone'] ?? 'Updated!' ?>',
    updateText:   '<?= $traducciones['updateText_glucose_ketone']   ?? 'Record updated successfully.' ?>',
    loadError:    '<?= $traducciones['loadError_glucose_ketone']    ?? 'Could not load glucose & ketone records.' ?>',
    editError:    '<?= $traducciones['editError_glucose_ketone']    ?? 'Could not load record for editing.' ?>',
    saveError:    '<?= $traducciones['saveError_glucose_ketone']    ?? 'Could not save record.' ?>',
    updateError:  '<?= $traducciones['updateError_glucose_ketone']  ?? 'Something went wrong while updating record.' ?>',
    deleteError:  '<?= $traducciones['deleteError_glucose_ketone']  ?? 'Could not delete record.' ?>',
    deleteConfirmTitle: '<?= $traducciones['deleteConfirm_glucose_ketone']   ?? 'Are you sure?' ?>',
    deleteWarning:      '<?= $traducciones['deleteWarning_glucose_ketone']   ?? 'This action cannot be undone!' ?>',
    deleteConfirmBtn:   '<?= $traducciones['deleteConfirmBtn_glucose_ketone']?? 'Yes, delete!' ?>',
    deleteSuccessText:  '<?= $traducciones['deleteSuccess_glucose_ketone']   ?? 'Record deleted.' ?>'
  }
};

// Selección dinámica según idioma activo
const langMessages = messages[lang] || messages.EN;

/* ========= Estado ========= */
let RAW_DATA = []; // todos los registros

/* ========= Flatpickr config ========= */
const FP = {
  date: {
    altInput: true,         // input visible
    altFormat: 'm/d/Y',     // MM/DD/YYYY para el usuario
    dateFormat: 'Y-m-d',     // Y-m-d para backend
    allowInput: true
  },
  time: {
    enableTime: true,
    noCalendar: true,
    dateFormat: 'H:i:S',     // 24h con segundos para backend
    time_24hr: true,
    enableSeconds: true,
    allowInput: true
  },
  inst: {}
};

// Placeholders i18n para los altInput de Flatpickr
const PH = {
  date: '<?= $traducciones['ph_date'] ?? 'Date (MM/DD/YYYY)' ?>',
  time: '<?= $traducciones['ph_time'] ?? 'Time (HH:mm:ss)' ?>'
};
function setAltPlaceholder(inst, text){
  if (inst && inst.altInput) inst.altInput.setAttribute('placeholder', text);
}

function fpSet(id, valueYmdOrTime) {
  const inst = FP.inst[id];
  if (!inst) return;
  if (valueYmdOrTime) {
    const fmt = (id.includes('time')) ? 'H:i:S' : 'Y-m-d';
    inst.setDate(valueYmdOrTime, true, fmt);
  } else {
    inst.clear();
  }
}

/* ========= Formatters ========= */
function dateFormatterEnergyMetabolism(value) {
  if (!value) return '';
  const [y,m,d] = value.split('-');
  const mm = String(m).padStart(2,'0');
  const dd = String(d).padStart(2,'0');
  return `${mm}/${dd}/${y}`; // MM/DD/YYYY
}
window.dateFormatterEnergyMetabolism = dateFormatterEnergyMetabolism;

/* ========= Helpers: fecha/hora visibles solo al EDITAR ========= */
// CAMBIO: Esta función ya no oculta campos, solo limpia si es 'crear'
function toggleDateTimeFields(scope, isEditMode){
  // scope: 'gk' | 'hba1c'
  // Los campos ya no se ocultan (d-none quitado del HTML)
  
  // Esta función ahora solo limpia los campos si estamos en modo 'crear'
  // (Aunque esto es redundante, ya que openEnergyMetabolismModal ya limpia todo)
  if (!isEditMode) { // Si es 'Crear'
    const dateId = (scope === 'gk') ? 'energy_date' : 'hba1c_date';
    const timeId = (scope === 'gk') ? 'energy_time' : 'hba1c_time';
    fpSet(dateId, null);
    fpSet(timeId, null);
  }
}

// CAMBIO: Esta función ya no es necesaria
/*
function stripEmptyDateTime(payload){
  if (payload.energy_date === '') delete payload.energy_date;
  if (payload.energy_time === '') delete payload.energy_time;
  return payload;
}
*/

/* ========= Action buttons ========= */
function gkActionFormatter(value, row) {
  const type = 'gk';
  return `
    <a href="component_energy_metabolism?id=${row.energy_metabolism_id}&type=${type}">
      <button class="btn btn-view action-icon"><i class="mdi mdi-eye-outline"></i></button>
    </a>
    <a href="user_test_documents?id_test_panel=7ff39dd8-01e9-443c-b8e6-0d6b429e63a6&id_test=${row.energy_metabolism_id}&type=${type}">
      <button class="btn btn-image-test action-icon"><i class="mdi mdi-file-image-outline"></i></button>
    </a>
    <button class="btn btn-pencil action-icon" onclick="openEnergyMetabolismModal('${row.energy_metabolism_id}', '${type}')">
      <i class="mdi mdi-pencil-outline"></i>
    </button>
  `;
}
function hba1cActionFormatter(value, row) {
  const type = 'hba1c';
  return `
    <a href="component_energy_metabolism?id=${row.energy_metabolism_id}&type=${type}">
      <button class="btn btn-view action-icon"><i class="mdi mdi-eye-outline"></i></button>
    </a>
    <a href="user_test_documents?id_test_panel=7ff39dd8-01e9-443c-b8e6-0d6b429e63a6&id_test=${row.energy_metabolism_id}&type=${type}">
      <button class="btn btn-image-test action-icon"><i class="mdi mdi-file-image-outline"></i></button>
    </a>
    <button class="btn btn-pencil action-icon" onclick="openEnergyMetabolismModal('${row.energy_metabolism_id}', '${type}')">
      <i class="mdi mdi-pencil-outline"></i>
    </button>
  `;
}
window.gkActionFormatter = gkActionFormatter;
window.hba1cActionFormatter = hba1cActionFormatter;

/* ========= Carga y partición de datos en tablas ========= */
function loadEnergyData() {
  $.ajax({
    url: 'energy_metabolism',
    method: 'GET',
    dataType: 'json',
    success: function (res) {
      const data = (res && res.value && Array.isArray(res.data)) ? res.data : [];
      RAW_DATA = data;

      const gk = [], hb = [];
      data.forEach(r => (parseFloat(r.hba1c || 0) > 0 ? hb : gk).push(r));

      // Mostrar toolbars
      document.getElementById('toolbar-gk').classList.remove('d-none');
      document.getElementById('toolbar-hba1c').classList.remove('d-none');

      // Cargar tablas y limpiar búsqueda
      $('#energyMetabolismTableGK').bootstrapTable('load', gk).bootstrapTable('resetSearch', '');
      $('#energyMetabolismTableHbA1c').bootstrapTable('load', hb).bootstrapTable('resetSearch', '');
    },
    error: function () {
      $('#energyMetabolismTableGK').bootstrapTable('load', []).bootstrapTable('resetSearch', '');
      $('#energyMetabolismTableHbA1c').bootstrapTable('load', []).bootstrapTable('resetSearch', '');
      Swal.fire('Error!', langMessages.loadError, 'error');
    }
  });
}

function openEnergyMetabolismModal(recordId = null, forceType = 'gk') {
  const isHb = forceType === 'hba1c';
  const formId  = isHb ? 'energyMetabolismFormHbA1c' : 'energyMetabolismFormGK';
  const modalId = isHb ? 'energyMetabolismModalHbA1c' : 'energyMetabolismModalGK';

  if (!window.bootstrap || !bootstrap.Modal) {
    console.error('Bootstrap JS no está cargado. Incluye bootstrap.bundle.min.js');
    return;
  }

  const formEl = document.getElementById(formId);
  if (!formEl) { console.error('No se encontró el formulario del modal:', formId); return; }

  clearValidationMessages(formEl);
  formEl.reset();
  formEl.querySelector('input[name="id"]').value = '';

  // Limpia pickers
  fpSet('energy_date', null);
  fpSet('energy_time', null);
  fpSet('hba1c_date',  null);
  fpSet('hba1c_time',  null);

  // Reset eAG en modo crear HbA1c
  if (isHb) {
    const dvText = document.getElementById('derived_value_text');
    const dv = document.getElementById('derived_value');
    const du = document.getElementById('derived_unit');
    if (dvText) dvText.textContent = '—';
    if (dv) dv.value = '';
    if (du) du.value = 'mg/dL';
  }

  let modalEl = document.getElementById(modalId);
  if (!modalEl) { console.error('No se encontró el modal:', modalId); return; }

  // ⚠️ Clave: saca el modal de cualquier contenedor con transform/z-index y ponlo en <body>
  if (modalEl.parentElement !== document.body) {
    document.body.appendChild(modalEl);
  }

  const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);

  if (recordId) {
    // MODO EDITAR
    setModalTitle(forceType, true);
    toggleDateTimeFields(forceType, true); // true = isEditMode
    
    $.ajax({
      url: `energy_metabolism/${recordId}`,
      method: 'GET',
      dataType: 'json',
      success: function (res) {
        if (res.value === true) {
          const record = Array.isArray(res.data) ? res.data[0] : res.data;
          formEl.querySelector('input[name="id"]').value = record.energy_metabolism_id;

          if (isHb) {
            fpSet('hba1c_date', record.energy_date || null);
            fpSet('hba1c_time', record.energy_time || null);
            $('#hba1c').val(record.hba1c ?? '');
            $('#hba1c_target').val(record.hba1c_target ?? '');
            const dv = (record.derived_value ?? '') === '' ? '' : String(record.derived_value);
            $('#derived_value').val(dv);
            const dvText = document.getElementById('derived_value_text');
            if (dvText) dvText.textContent = dv !== '' ? dv : '—';
            $('#derived_unit').val(record.derived_unit ?? 'mg/dL');
            $('#notes').val(record.notes ?? '');
            // toggleDateTimeFields('hba1c', true); // Ya no es necesario
          } else {
            fpSet('energy_date', record.energy_date || null);
            fpSet('energy_time', record.energy_time || null);
            $('#glucose').val(record.glucose ?? '');
            $('#ketone').val(record.ketone ?? '');
            // toggleDateTimeFields('gk', true); // Ya no es necesario
          }

          // pequeño defer para asegurar reflow antes de mostrar
          setTimeout(() => modalInstance.show(), 0);
        } else {
          Swal.fire('Error!', langMessages.editError, 'error');
        }
      },
      error: function () {
        Swal.fire('Error!', langMessages.editError, 'error');
      }
    });
  } else {
    // MODO CREAR
    setModalTitle(forceType, false);
    toggleDateTimeFields(forceType, false); // false = !isEditMode (limpia campos)

    // CAMBIO: Auto-poblar con fecha y hora actual
    const now = new Date();
    const currentDate = now.toISOString().split('T')[0]; // YYYY-MM-DD
    const currentTime = now.toTimeString().split(' ')[0]; // HH:mm:ss
    
    if (isHb) { // forceType === 'hba1c'
      fpSet('hba1c_date', currentDate);
      fpSet('hba1c_time', currentTime);
    } else { // 'gk'
      fpSet('energy_date', currentDate);
      fpSet('energy_time', currentTime);
    }
    
    setTimeout(() => modalInstance.show(), 0);
  }
}
window.openEnergyMetabolismModal = openEnergyMetabolismModal;


/* ========= Autocálculo eAG (muestra texto y llena hidden) ========= */
document.addEventListener('input', (e) => {
  if (e.target && e.target.id === 'hba1c') {
    const v = parseFloat(e.target.value);
    const textEl = document.getElementById('derived_value_text');
    const hidEl  = document.getElementById('derived_value');
    const unitEl = document.getElementById('derived_unit');

    if (!isNaN(v)) {
      const eAG = 28.7 * v - 46.7;
      const val = (Math.round(eAG * 10) / 10).toFixed(1);
      if (textEl) textEl.textContent = val;
      if (hidEl)  hidEl.value = val;
      if (unitEl) unitEl.value = 'mg/dL';
    } else {
      if (textEl) textEl.textContent = '—';
      if (hidEl)  hidEl.value = '';
      if (unitEl) unitEl.value = 'mg/dL';
    }
  }
});

/* ========= Submit GK ========= */
$('#energyMetabolismFormGK').on('submit', function (e) {
  e.preventDefault();

  const recordId = $('#energyMetabolismFormGK input[name="id"]').val() || '';
  // CAMBIO: Fecha y hora siempre requeridos
  const req = ['energy_date', 'energy_time', 'glucose'];
  if (!validateFormFields(e.target, req, '<?= $traducciones['input_generic_error'] ?>')) return;

  const method = recordId ? 'PUT' : 'POST';
  const url = recordId ? `energy_metabolism/${recordId}` : 'energy_metabolism';

  const payload = {
    energy_date: $('#energy_date').val(),
    energy_time: $('#energy_time').val(),
    glucose: $('#glucose').val() ? parseFloat($('#glucose').val()) : 0,
    ketone: $('#ketone').val() ? parseFloat($('#ketone').val()) : 0,
    hba1c: 0, hba1c_target: null, derived_value: null, derived_unit: null, notes: null
  };
  if (recordId) payload.id = recordId;

  // CAMBIO: Se elimina la llamada a stripEmptyDateTime
  // stripEmptyDateTime(payload);

  $.ajax({
    url, type: method,
    data: JSON.stringify(payload),
    contentType: 'application/json',
    dataType: 'json',
    success: function (response) {
      if (response.value || (response.data && response.data.status === 'success')) {
        Swal.fire({ icon:'success', title: recordId ? langMessages.updatedTitle : langMessages.savedTitle, text: recordId ? langMessages.updateText : langMessages.saveText, timer:1500, showConfirmButton:false });
        const modalEl = document.getElementById('energyMetabolismModalGK');
        if (modalEl) bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        loadEnergyData();
        reloadAlerts();
      } else {
        Swal.fire('Error', response.message, 'error');
      }
    },
    error: function () {
      Swal.fire('Error', recordId ? langMessages.updateError : langMessages.saveError, 'error');
    }
  });
});

/* ========= Submit HbA1c ========= */
$('#energyMetabolismFormHbA1c').on('submit', function (e) {
  e.preventDefault();

  const recordId = $('#energyMetabolismFormHbA1c input[name="id"]').val() || '';
  // CAMBIO: Fecha y hora siempre requeridos
  const req = ['hba1c_date', 'hba1c_time', 'hba1c'];
  if (!validateFormFields(e.target, req, '<?= $traducciones['input_generic_error'] ?>')) return;

  const method = recordId ? 'PUT' : 'POST';
  const url = recordId ? `energy_metabolism/${recordId}` : 'energy_metabolism';

  const dv = $('#derived_value').val();
  const payload = {
    energy_date: $('#hba1c_date').val(),
    energy_time: $('#hba1c_time').val(),
    hba1c: $('#hba1c').val() ? parseFloat($('#hba1c').val()) : 0,
    hba1c_target: $('#hba1c_target').val() ? parseFloat($('#hba1c_target').val()) : null,
    derived_value: dv !== '' ? parseFloat(dv) : null,
    derived_unit: $('#derived_unit').val() || 'mg/dL',
    notes: $('#notes').val() || null,
    glucose: 0, ketone: 0
  };
  if (recordId) payload.id = recordId;

  // CAMBIO: Se elimina la llamada a stripEmptyDateTime
  // stripEmptyDateTime(payload);

  $.ajax({
    url, type: method,
    data: JSON.stringify(payload),
    contentType: 'application/json',
    dataType: 'json',
    success: function (response) {
      if (response.value || (response.data && response.data.status === 'success')) {
        Swal.fire({ icon:'success', title: recordId ? langMessages.updatedTitle : langMessages.savedTitle, text: recordId ? langMessages.updateText : langMessages.saveText, timer:1500, showConfirmButton:false });
        const modalEl = document.getElementById('energyMetabolismModalHbA1c');
        if (modalEl) bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        loadEnergyData();
        reloadAlerts();
      } else {
        Swal.fire('Error', response.message, 'error');
      }
    },
    error: function () {
      Swal.fire('Error', recordId ? langMessages.updateError : langMessages.saveError, 'error');
    }
  });
});

/* ========= Hooks & Init ========= */
$('#energyMetabolismTableGK, #energyMetabolismTableHbA1c').on('refresh.bs.table', loadEnergyData);

document.addEventListener('DOMContentLoaded', () => {
  // Verificación básica (evita “pantalla gris” si faltara bootstrap JS)
  if (!window.bootstrap) {
    console.warn('Bootstrap JS no está cargado. Asegúrate de incluir bootstrap.bundle.min.js');
  }

  // Inicializa Flatpickr instancias
  FP.inst.energy_date = flatpickr('#energy_date', FP.date);
  FP.inst.energy_time = flatpickr('#energy_time', FP.time);
  FP.inst.hba1c_date  = flatpickr('#hba1c_date', FP.date);
  FP.inst.hba1c_time  = flatpickr('#hba1c_time', FP.time);

  // Placeholders de los altInput
  setAltPlaceholder(FP.inst.energy_date, PH.date);
  setAltPlaceholder(FP.inst.hba1c_date,  PH.date);
  setAltPlaceholder(FP.inst.energy_time, PH.time);
  setAltPlaceholder(FP.inst.hba1c_time,  PH.time);

  // Ajustes de tablas: mostrar “sin registros” cuando estén vacías
  const opts = {
    showNoRecords: true,
    showEmpty: true,
    formatNoMatches: () => '<?= $traducciones['no_records_text'] ?>',
    formatEmpty: () => '<?= $traducciones['no_records_text'] ?>'
  };
  $('#energyMetabolismTableGK').bootstrapTable('refreshOptions', opts);
  $('#energyMetabolismTableHbA1c').bootstrapTable('refreshOptions', opts);

  // Cargar datos y repartir
  loadEnergyData();

  // Búsqueda por querystring
  const params = new URLSearchParams(window.location.search);
  const search = params.get('search');
  function applySearchAfterTableLoads(tableId, searchValue) {
    if (!searchValue) return;
    const $table = $(`#${tableId}`);
    $table.on('post-body.bs.table', function () {
      const $searchInput = $table.closest('.bootstrap-table').find('.search input');
      if ($searchInput.length) {
        $searchInput.val(searchValue).trigger('input');
        const rows = $table.bootstrapTable('getData') || [];
        const index = rows.findIndex(row =>
          Object.values(row || {}).some(value => String(value ?? '').includes(searchValue))
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
  if ($('#energyMetabolismTableGK').length) applySearchAfterTableLoads('energyMetabolismTableGK', search);
  if ($('#energyMetabolismTableHbA1c').length) applySearchAfterTableLoads('energyMetabolismTableHbA1c', search);
});
</script>