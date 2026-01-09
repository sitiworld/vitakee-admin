<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <h4 class="page-title my-2"><?= $traducciones['page_title_body'] ?></h4>
      <div class="page-title-box"></div>
    </div>
  </div>

  <div id="toolbar" class="d-none">
    <button class="btn btn-action-glucose w-20" onclick="openAddEditBodyModal()">
      + <?= $traducciones['add_new_body'] ?>
    </button>
    <button id="btnExportCSV" class="btn btn-action-lipid" type="button">
      <span class="mdi mdi-file-export-outline"></span> <?= $traducciones['export_csv'] ?>
    </button>
  </div>

  <div class="card">
    <div class="card-body">
      <table id="bodyCompositionTable"
             data-toggle="table" data-search="true" data-show-refresh="true"
             data-page-list="[5, 10, 20]" data-page-size="5" data-show-columns="true"
             data-pagination="true" data-show-pagination-switch="true"
             class="table-borderless" data-url="body-compositions"
             data-toolbar="#toolbar" data-locale="<?= $locale ?>">
        <thead>
          <tr>
            <th data-field="composition_date" data-sortable="true" data-formatter="dateFormatterBodyComposition">
              <?= $traducciones['date'] ?>
            </th>
            <th data-field="composition_time" data-sortable="true">
              <?= $traducciones['time'] ?>
            </th>
            <th data-field="id" data-align="center" data-formatter="actionFormatter">
              <?= $traducciones['actions'] ?>
            </th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

<div class="modal fade" id="modalAddEditBody" tabindex="-1" aria-labelledby="modalAddEditBodyLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAddEditBodyLabel">
          <?= $traducciones['modal_title_add'] ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="formAddEditBody">
          <input type="hidden" id="addEditBodyId" name="id">

          <div class="mb-3" id="group_date_body">
            <label for="addEditCompositionDate" class="form-label"><?= $traducciones['date'] ?></label>
            <input type="text" id="addEditCompositionDate" name="composition_date" class="form-control"
                   placeholder="<?= $traducciones['ph_date'] ?? 'Date (MM/DD/YYYY)' ?>">
          </div>
          <div class="mb-3" id="group_time_body">
            <label for="addEditCompositionTime" class="form-label"><?= $traducciones['time'] ?></label>
            <input type="text" id="addEditCompositionTime" name="composition_time" class="form-control"
                   placeholder="<?= $traducciones['ph_time'] ?? 'Time (HH:mm:ss)' ?>">
          </div>

          <?php
            $system_type = $_SESSION['system_type'] ?? 'US';
            $peso_key = $system_type === 'EU' ? 'weight_kg' : 'weight_lb';
          ?>
          <div class="mb-3">
            <label for="addEditWeight" class="form-label">
              <?= $traducciones[$peso_key] ?>
            </label>
            <i class="mdi mdi-help-circle-outline" data-bs-toggle="tooltip" data-bs-placement="top"
               title="<?= $_SESSION['idioma'] === 'ES'
                         ? 'Los cálculos se harán en base a la edad registrada.'
                         : 'Calculations will be based on the registered age.' ?>"></i>
            <input type="text" step="0.1" id="addEditWeight" name="weight_lb" class="form-control number"
                   placeholder="<?= $traducciones['ph_weight_number'] ?? 'e.g., 165' ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label>
              <?= $traducciones['bmi'] ?>
              <i class="mdi mdi-help-circle-outline" data-bs-toggle="tooltip" data-bs-placement="top"
                 title="<?= $traducciones['bmi_description_tooltip'] ?>"></i>
              <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top"
                 title="<?= $traducciones['bmi_accuracy_tooltip'] ?>"></i>
            </label>
            <div class="form-control-plaintext" id="addEditBmiResultText">--</div>
            <input type="hidden" name="bmi" id="addEditBmiResult">
          </div>

          <div class="mb-3">
            <label for="addEditBodyFat" class="form-label"><?= $traducciones['body_fat_pct'] ?></label>
            <input type="text" step="0.1" id="addEditBodyFat" name="body_fat_pct" class="form-control number"
                   placeholder="<?= $traducciones['ph_bodyfat_number'] ?? 'e.g., 18.5' ?>">
          </div>

          <div class="mb-3">
            <label for="addEditWater" class="form-label">
              <?= $traducciones['water_pct'] ?>
              <i class="mdi mdi-help-circle-outline" data-bs-toggle="tooltip" data-bs-placement="top"
                 title="<?= $traducciones['water_pct_description_tooltip'] ?>"></i>
              <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top"
                 title="<?= $traducciones['water_pct_accuracy_tooltip'] ?>"></i>
            </label>
            <div class="form-control-plaintext" id="addEditWaterResultText">--</div>
            <input type="hidden" step="0.1" id="addEditWater" name="water_pct" class="form-control number">
          </div>

          <div class="mb-3">
            <label for="addEditMuscle" class="form-label"><?= $traducciones['muscle_pct'] ?></label>
            <input type="text" step="0.1" id="addEditMuscle" name="muscle_pct" class="form-control number"
                   placeholder="<?= $traducciones['ph_muscle_number'] ?? 'e.g., 41.0' ?>">
          </div>

          <div class="mb-3">
            <label for="addEditMetabolism" class="form-label"><?= $traducciones['resting_metabolism'] ?></label>
            <input type="text" step="1" id="addEditMetabolism" name="resting_metabolism" class="form-control number"
                   placeholder="<?= $traducciones['ph_rmr_number'] ?? 'e.g., 1500' ?>">
          </div>

          <div class="mb-3">
            <label for="addEditVisceral" class="form-label"><?= $traducciones['visceral_fat'] ?></label>
            <input type="text" step="0.1" id="addEditVisceral" name="visceral_fat" class="form-control number"
                   placeholder="<?= $traducciones['ph_visceral_number'] ?? 'e.g., 10' ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label>
              <?= $traducciones['body_age'] ?>
              <i class="mdi mdi-help-circle-outline" data-bs-toggle="tooltip" data-bs-placement="top"
                 title="<?= $traducciones['body_age_description_tooltip'] ?>"></i>
              <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top"
                 title="<?= $traducciones['body_age_accuracy_tooltip'] ?>"></i>
            </label>
            <div class="form-control-plaintext" id="addEditBodyAgeText">--</div>
            <input type="hidden" name="body_age" id="addEditBodyAge">
          </div>

        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" form="formAddEditBody" class="btn btn-save">
          <i class="mdi mdi-content-save-outline"></i> <?= $traducciones['save'] ?>
        </button>
        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
          <i class="mdi mdi-cancel"></i> <?= $traducciones['cancel'] ?>
        </button>
      </div>
    </div>
  </div>
</div>

</div>

<script src="public/assets/js/logout.js"></script>
<script>
  const userFullName = "<?= $_SESSION['user_name'] ?>";
</script>

<script type="module">
import { validateFormFields, clearValidationMessages } from "./public/assets/js/helpers/helpers.js";
import { reloadAlerts } from "./public/assets/js/controllers/notificationsController.js";

/* ===== Flatpickr config ===== */
const FP = {
  date: {
    altInput: true,
    altFormat: 'm/d/Y',
    dateFormat: 'Y-m-d',
    allowInput: true
  },
  time: {
    enableTime: true,
    noCalendar: true,
    dateFormat: 'H:i:S',
    time_24hr: true,
    enableSeconds: true,
    allowInput: true
  },
  inst: {}
};
function fpSet(id, valueYmdOrTime){
  const inst = FP.inst[id];
  if (!inst) return;
  if (valueYmdOrTime) {
    const fmt = id.includes('Time') ? 'H:i:S' : 'Y-m-d';
    inst.setDate(valueYmdOrTime, true, fmt);
  } else {
    inst.clear();
  }
}
// Placeholders i18n para los altInput de Flatpickr (después de inicializar)
const PH = {
  date: '<?= $traducciones['ph_date'] ?? 'Date (MM/DD/YYYY)' ?>',
  time: '<?= $traducciones['ph_time'] ?? 'Time (HH:mm:ss)' ?>'
};
const setAltPlaceholder = (inst, text) => {
  if (inst && inst.altInput) inst.altInput.setAttribute('placeholder', text);
};
setAltPlaceholder(FP.inst.addEditCompositionDate, PH.date);
setAltPlaceholder(FP.inst.addEditCompositionTime, PH.time);

/* ===== Helpers fecha/hora ===== */
// CAMBIO: Modificada para manejar solo 'required' y limpieza
function toggleDateTimeFieldsBody(isEditMode){
  const iDate   = document.getElementById('addEditCompositionDate');
  const iTime   = document.getElementById('addEditCompositionTime');

  // Campos siempre visibles, siempre requeridos
  iDate?.setAttribute('data-required','true');
  iTime?.setAttribute('data-required','true');

  if (!isEditMode) { // Si es modo CREAR
    // Limpiar campos antes de autocompletar
    if (iDate) iDate.value = '';
    if (iTime) iTime.value = '';
    FP.inst.addEditCompositionDate?.clear?.();
    FP.inst.addEditCompositionTime?.clear?.();
  }
  // Si es modo EDITAR, no hacer nada, la AJAX poblará los campos.
}

// CAMBIO: Esta función ya no es necesaria
/*
function stripEmptyDateTime(payload){
  if (payload.composition_date === '') delete payload.composition_date;
  if (payload.composition_time === '') delete payload.composition_time;
  return payload;
}
*/

/* ===== Tooltips/Popovers ===== */
document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => new bootstrap.Popover(el));

/* ===== Export CSV ===== */
document.getElementById('btnExportCSV').addEventListener('click', function () {
  Swal.fire({
    title: messages[lang].exportLoadingTitle,
    text: messages[lang].exportLoadingText,
    allowOutsideClick: false,
    allowEscapeKey: false,
    didOpen: () => Swal.showLoading()
  });

  fetch('body-compositions/export/<?php echo $_SESSION['user_id'] ?>')
    .then(async response => {
      const contentType = response.headers.get("Content-Type");
      if (contentType && contentType.includes("text/csv")) {
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const dateStr = new Date().toISOString().slice(0, 10);
        const filename = `${messages[lang].csvFilenamePrefix}_${userFullName}_${dateStr}.csv`;
        const a = document.createElement('a');
        a.href = url; a.download = filename;
        document.body.appendChild(a); a.click(); a.remove();
        window.URL.revokeObjectURL(url);
        Swal.close();
      } else {
        await response.json().catch(()=>{});
        Swal.fire({ icon:'info', title: messages[lang].noRecordsTitle, text: messages[lang].noRecordsText });
      }
    })
    .catch(() => Swal.fire({ icon:'error', title: messages[lang].exportErrorTitle, text: messages[lang].exportErrorText }));
});

/* ===== Textos ===== */
const lang = "<?php echo isset($_SESSION['idioma']) ? addslashes($_SESSION['idioma']) : 'EN'; ?>";
const messages = {
  EN: {
    titleError: '<?= $traducciones['titleError_body_composition'] ?>',
    titleSuccess: '<?= $traducciones['titleSuccess_body_composition'] ?>!',
    genericLoadError: '<?= $traducciones['genericLoadError_body_composition'] ?>',
    genericSaveSuccess: '<?= $traducciones['genericSaveSuccess_body_composition'] ?>',
    genericSaveError: '<?= $traducciones['genericSaveError_body_composition'] ?>',
    genericFetchError: '<?= $traducciones['genericFetchError_body_composition'] ?>',
    noRecordsTitle: '<?= $traducciones['no_records_title'] ?>',
    noRecordsText: '<?= $traducciones['no_records_text'] ?>',
    exportErrorTitle: '<?= $traducciones['export_error_title'] ?>',
    exportErrorText: '<?= $traducciones['export_error_text'] ?>',
    exportLoadingTitle: '<?= $traducciones['export_loading_title'] ?>',
    exportLoadingText: '<?= $traducciones['export_loading_text'] ?>',
    csvFilenamePrefix: '<?= $traducciones['csv_filename_prefix_body'] ?>'
  },
  ES: {
    titleError: '<?= $traducciones['titleError_body_composition'] ?>',
    titleSuccess: '<?= $traducciones['titleSuccess_body_composition'] ?>',
    genericLoadError: '<?= $traducciones['genericLoadError_body_composition'] ?>',
    genericSaveSuccess: '<?= $traducciones['genericSaveSuccess_body_composition'] ?>',
    genericSaveError: '<?= $traducciones['genericSaveError_body_composition'] ?>',
    genericFetchError: '<?= $traducciones['genericFetchError_body_composition'] ?>',
    noRecordsTitle: '<?= $traducciones['no_records_title'] ?>',
    noRecordsText: '<?= $traducciones['no_records_text'] ?>',
    exportErrorTitle: '<?= $traducciones['export_error_title'] ?>',
    exportErrorText: '<?= $traducciones['export_error_text'] ?>',
    exportLoadingTitle: '<?= $traducciones['export_loading_title'] ?>',
    exportLoadingText: '<?= $traducciones['export_loading_text'] ?>',
    csvFilenamePrefix: '<?= $traducciones['csv_filename_prefix_body'] ?>'
  }
};

function actionFormatter(value, row) {
  return `
    <a href="component_body_composition?id=${row.body_composition_id}">
      <button class="btn btn-view action-icon"><i class="mdi mdi-eye-outline"></i></button>
    </a>
    <a href="user_test_documents?id_test_panel=81054d57-92c9-4df8-a6dc-51334c1d82c4&id_test=${row.body_composition_id}">
      <button class="btn btn-image-test action-icon"><i class="mdi mdi-file-image-outline"></i></button>
    </a>
    <button class="btn btn-pencil action-icon" onclick="openAddEditBodyModal('${row.body_composition_id}')">
      <i class="mdi mdi-pencil-outline"></i>
    </button>`;
}
window.actionFormatter = actionFormatter;

/* ===== Variables de sesión ===== */
let height       = "<?php echo addslashes($_SESSION['height']); ?>";   // ej: 5'7"
let system_type  = "<?php echo addslashes($_SESSION['system_type']); ?>";
let birthday     = "<?php echo $_SESSION['birthday']; ?>";
let chronologicalAge = calculateChronologicalAge(birthday);

/* ===== Conversión & BMI (Omron-strict) ===== */
const LB_TO_KG = 0.45359237; // exacto
const IN_TO_CM = 2.54;       // exacto

function roundTo(x, step, mode='nearest'){
  if (x == null || isNaN(x)) return NaN;
  const n = x / step;
  if (mode === 'down') return Math.floor(n) * step;
  if (mode === 'up')   return Math.ceil(n) * step;
  return Math.round(n) * step; // nearest
}

function heightStringToCmOmron(heightStr){
  if (!heightStr) return NaN;
  if (heightStr.includes("'")) {
    let [ft, rest] = heightStr.split("'");
    ft = parseInt(ft, 10) || 0;
    const inches = parseInt(String(rest).replace(/["]/g,'').trim(), 10) || 0;
    const totalIn = (ft * 12) + inches;
    const cm = totalIn * IN_TO_CM;
    return roundTo(cm, 0.5, 'nearest'); // redondeo 0.5 cm
  }
  const cm = parseFloat(heightStr);
  return roundTo(cm, 0.5, 'nearest');
}

/* ===== Helpers comunes num/sexo ===== */
function normalizeSex(s) {
  if (!s) return 'm';
  s = String(s).toLowerCase();
  if (s.startsWith('m')) return 'm'; // 'm' / 'male'
  if (s.startsWith('f')) return 'f'; // 'f' / 'female'
  return 'm';
}
function toNum(x) {
  if (x == null) return 0;
  if (typeof x === 'string') x = x.replace(',', '.');
  const n = parseFloat(x);
  return Number.isFinite(n) ? n : 0;
}

/**
 * BMI Omron-strict:
 * - peso a kg (0.1 kg)
 * - altura a cm (0.5 cm)
 * - BMI = kg/m² (0.1)
 */
function calculateBMI_OmronStrict(weight_input, heightStr, sys='US'){
  let kg = (sys === 'EU') ? parseFloat(weight_input) : (parseFloat(weight_input) * LB_TO_KG);
  kg = roundTo(kg, 0.1, 'nearest');
  const cm = heightStringToCmOmron(heightStr);
  const m  = cm / 100;
  if (!isFinite(kg) || !isFinite(m) || m <= 0) return NaN;
  const bmi = kg / (m * m);
  return Number(roundTo(bmi, 0.1, 'nearest').toFixed(1));
}

/* ===== Otras utilidades ===== */
function kgToLb(kg){ return kg*2.20462; }
function lbToKg(lb){ return lb/2.20462; }
function heightToCm(heightStr){
  let feet=0, inches=0;
  if (heightStr.includes("'")) {
    let parts = heightStr.split("'");
    feet = parseInt(parts[0],10) || 0;
    inches = parseInt(parts[1].replace(/["]/g,'').trim(),10) || 0;
    if (inches>=12){ feet += Math.floor(inches/12); inches = inches%12; }
  }
  return ((feet*12)+inches)*2.54;
}
function calculateChronologicalAge(birthday){
  let birthDate = new Date(birthday), today = new Date();
  let age = today.getFullYear()-birthDate.getFullYear();
  let m = today.getMonth()-birthDate.getMonth();
  if (m<0 || (m===0 && today.getDate()<birthDate.getDate())) age--;
  return age;
}
function calculateWaterPct(sex_biological, age, heightStr, weight_lb){
  if (system_type==='EU') weight_lb = kgToLb(weight_lb);
  const heightCm = heightToCm(heightStr);
  const weightKg = lbToKg(weight_lb);
  let tbw=0;
  sex_biological = normalizeSex(sex_biological);
  if (sex_biological==='m'){
    tbw = 2.447 - (0.09516*age) + (0.1074*heightCm) + (0.3362*weightKg);
  } else if (sex_biological==='f'){
    tbw = -2.097 + (0.1069*heightCm) + (0.2466*weightKg);
  } else { return null; }
  return (tbw/weightKg)*100;
}

/* ===== Body Age dinámico “Omron-inspirado” ===== */
const BODY_AGE_DEBUG = true; // pon true para ver contribuciones en consola

function msjExpectedBMR(sex_biological, weight_lb, heightStr, age, sys='US'){
  const kg = (sys === 'EU') ? toNum(weight_lb) : (toNum(weight_lb) * LB_TO_KG);
  const cm = heightStringToCmOmron(heightStr);
  if (!Number.isFinite(kg) || !Number.isFinite(cm)) return NaN;
  const base = (10*kg) + (6.25*cm) - (5*toNum(age));
  sex_biological = normalizeSex(sex_biological);
  return (sex_biological === 'm') ? (base + 5) : (base - 161);
}

/**
 * Ajustada para que con: 35 años, 'm', BMI 21.1, BF 14.6, MUS 41.1, WATER 61.3, VF 4, RMR 1488
 * dé ~24 (entero).
 */
function calculateBodyAge_OmronInspired({
  sex_biological, chronologicalAge, bmi, bodyFatPct, musclePct, visceralFat, waterPct, restingMetabolism,
  heightStr, weight_lb, system_type='US'
}){
  sex_biological = normalizeSex(sex_biological);

  const BMI_TARGET = 22;
  const VF_TARGET  = 10;
  const norms = (sex_biological === 'm')
    ? { bf: 15, muscle: 40, water: 60 }
    : { bf: 25, muscle: 30, water: 55 };

  // Entradas seguras
  const _age  = toNum(chronologicalAge);
  const _bmi  = toNum(bmi);
  const _bf   = toNum(bodyFatPct);
  const _mus  = toNum(musclePct);
  const _vf   = toNum(visceralFat);
  const _wat  = toNum(waterPct);
  const _rmr  = toNum(restingMetabolism);
  const _wtlb = toNum(weight_lb);

  // Desviaciones
  const bfDiff    = _bf  - norms.bf;          // + sube edad
  const muscDiff  = _mus - norms.muscle;       // + baja edad (coef negativo)
  const waterDiff = norms.water - _wat;         // + sube edad si poca agua
  const bmiDiff   = _bmi - BMI_TARGET;         // ± por punto de IMC
  const vfDiff    = _vf  - VF_TARGET;          // ± por nivel VF

  // RMR esperado (MSJ)
  const kg = (system_type === 'EU') ? _wtlb : (_wtlb * LB_TO_KG);
  const cm = heightStringToCmOmron(heightStr);
  let bmrExp = NaN;
  if (Number.isFinite(kg) && Number.isFinite(cm)) {
    const base = (10*kg) + (6.25*cm) - (5*_age);
    bmrExp = (sex_biological === 'm') ? (base + 5) : (base - 161);
  }
  const bmrDiff = Number.isFinite(bmrExp) ? (_rmr - bmrExp) : 0; // + baja si RMR > esperado

  // Ponderaciones ajustadas (OMRON-like)
  const C_BMI = 1.20;
  const C_BF  = 0.60;
  const C_MUS = -1.25;
  const C_VF  = 1.35;
  const C_WAT = 0.30;
  const C_RMR = -0.015;

  const a = C_BMI * bmiDiff;
  const b = C_BF  * bfDiff;
  const c = C_MUS * muscDiff;
  const d = C_VF  * vfDiff;
  const e = C_WAT * waterDiff;
  const f = C_RMR * bmrDiff;

  let delta = a + b + c + d + e + f;
  let bodyAge = _age + delta;

  if (BODY_AGE_DEBUG) {
    console.log('[BodyAge DEBUG]', {
      inputs: { sex_biological, chronologicalAge:_age, bmi:_bmi, bodyFatPct:_bf, musclePct:_mus, visceralFat:_vf, waterPct:_wat, restingMetabolism:_rmr, heightStr, weight_lb:_wtlb, system_type },
      diffs: { bmiDiff, bfDiff, muscDiff, vfDiff, waterDiff, bmrDiff },
      contribs: { a_BMI:a, b_BF:b, c_MUS:c, d_VF:d, e_WAT:e, f_RMR:f, sum: delta },
      bodyAge_raw: bodyAge
    });
  }

  if (!Number.isFinite(bodyAge)) bodyAge = _age;
  bodyAge = Math.max(18, bodyAge);
  return Math.round(bodyAge); // estilo Omron
}

/* ===== Abrir modal (crear/editar) ===== */
function openAddEditBodyModal(recordId) {
  const modal = new bootstrap.Modal(document.getElementById('modalAddEditBody'));
  const form = document.getElementById('formAddEditBody');

  clearValidationMessages(form);
  form.reset();
  document.getElementById('addEditBodyId').value = '';
  document.getElementById('addEditBmiResultText').innerText = '--';
  document.getElementById('addEditBmiResult').value = '';
  document.getElementById('addEditBodyAgeText').innerText = chronologicalAge;
  document.getElementById('addEditBodyAge').value = chronologicalAge;

  // limpiar pickers (se hará en toggleDateTimeFieldsBody si es 'crear')
  // fpSet('addEditCompositionDate', null); // Se mueve a toggle
  // fpSet('addEditCompositionTime', null); // Se mueve a toggle

  if (recordId) {
    // EDITAR
    toggleDateTimeFieldsBody(true); // true = isEditMode

    $.ajax({
      url: `body-compositions/${recordId}`,
      type: 'get',
      success: function (res) {
        if (!res.value) {
          return Swal.fire(messages[lang].titleError, messages[lang].genericLoadError, 'error');
        }
        let { data } = res;

        $('#addEditBodyId').val(data.body_composition_id);

        fpSet('addEditCompositionDate', data.composition_date || null);
        fpSet('addEditCompositionTime', data.composition_time || null);

        $('#addEditWeight').val(data.weight_lb);

        // BMI + Agua + BodyAge
        let weight = toNum(data.weight_lb);
        if (Number.isFinite(weight)) {
          // BMI Omron-strict
          let bmi = calculateBMI_OmronStrict(weight, height, system_type);
          $('#addEditBmiResultText').text(isFinite(bmi) ? bmi.toFixed(1) : '--');
          $('#addEditBmiResult').val(isFinite(bmi) ? bmi.toFixed(1) : '');

          // Agua: usa el valor visible o calcula si falta
          const sexo = normalizeSex("<?php echo $_SESSION['sex_biological']; ?>");
          let waterVal = toNum($('#addEditWater').val());
          if (!Number.isFinite(waterVal) || waterVal === 0) {
            const wCalc = calculateWaterPct(sexo, chronologicalAge, height, weight);
            if (wCalc != null && isFinite(wCalc)) {
              waterVal = Number(wCalc.toFixed(1));
              $('#addEditWater').val(waterVal);
              $('#addEditWaterResultText').text(waterVal.toFixed(1));
            }
          }

          // Body Age dinámico
          const bodyAge = calculateBodyAge_OmronInspired({
            sex_biological: sexo,
            chronologicalAge,
            bmi: toNum($('#addEditBmiResult').val()),
            bodyFatPct: toNum(data.body_fat_pct),
            musclePct: toNum(data.muscle_pct),
            visceralFat: toNum(data.visceral_fat),
            waterPct: waterVal,
            restingMetabolism: toNum(data.resting_metabolism),
            heightStr: height,
            weight_lb: weight,
            system_type
          });
          $('#addEditBodyAgeText').text(bodyAge);
          $('#addEditBodyAge').val(bodyAge);
        }

        $('#addEditBodyFat').val(data.body_fat_pct);
        $('#addEditMuscle').val(data.muscle_pct);
        $('#addEditMetabolism').val(data.resting_metabolism);
        $('#addEditVisceral').val(data.visceral_fat);

        modal.show();
      },
      error: function () {
        Swal.fire(messages[lang].titleError, messages[lang].genericLoadError, 'error');
      }
    });
  } else {
    // CREAR
    toggleDateTimeFieldsBody(false); // false = !isEditMode (limpia y pone required)
    
    // CAMBIO: Auto-poblar con fecha y hora actual
    const now = new Date();
    const currentDate = now.toISOString().split('T')[0]; // YYYY-MM-DD
    const currentTime = now.toTimeString().split(' ')[0]; // HH:mm:ss
    
    fpSet('addEditCompositionDate', currentDate);
    fpSet('addEditCompositionTime', currentTime);
    
    modal.show();
  }
}
window.openAddEditBodyModal = openAddEditBodyModal;

/* ===== Recalcular dinámicos en el modal ===== */
function updateBmiAndWater(context) {
  const weightInput = context.querySelector('input[name="weight_lb"]');
  const bmiText = context.querySelector('#addEditBmiResultText');
  const bmiField = context.querySelector('#addEditBmiResult');
  const waterText = context.querySelector('#addEditWaterResultText');
  const waterField = context.querySelector('#addEditWater');

  let weight = toNum(weightInput.value);
  if (Number.isFinite(weight)) {
    // BMI Omron-strict
    let bmi = calculateBMI_OmronStrict(weight, height, system_type);
    if (isFinite(bmi)) {
      bmiText.innerText = bmi.toFixed(1);
      bmiField.value = bmi.toFixed(1);
    } else {
      bmiText.innerText = '--';
      bmiField.value = '';
    }

    let sexo = normalizeSex("<?php echo $_SESSION['sex_biological']; ?>");
    let waterPct = calculateWaterPct(sexo, chronologicalAge, height, weight);
    if (waterPct != null && isFinite(waterPct) && waterField) {
      const waterValue = Number(waterPct.toFixed(1));
      waterField.value = waterValue;
      waterText.innerText = waterValue.toFixed(1);
    }
  }
}

function updateBodyAge(context) {
  const bmiField = context.querySelector('#addEditBmiResult');
  const ageText = context.querySelector('#addEditBodyAgeText');
  const ageField = context.querySelector('#addEditBodyAge');

  const bodyFatInput = context.querySelector('input[name="body_fat_pct"]');
  const muscleInput = context.querySelector('input[name="muscle_pct"]');
  const visceralInput = context.querySelector('input[name="visceral_fat"]');
  const waterInput = context.querySelector('input[name="water_pct"]');
  const metabolismInput = context.querySelector('input[name="resting_metabolism"]');
  const weightInput = context.querySelector('input[name="weight_lb"]');

  const sexo = normalizeSex("<?php echo $_SESSION['sex_biological']; ?>");
  const weightCurrent = toNum(weightInput?.value);

  let bodyAge = calculateBodyAge_OmronInspired({
    sex_biological: sexo,
    chronologicalAge,
    bmi: toNum(bmiField.value),
    bodyFatPct: toNum(bodyFatInput.value),
    musclePct: toNum(muscleInput.value),
    visceralFat: toNum(visceralInput.value),
    waterPct: toNum(waterInput.value),
    restingMetabolism: toNum(metabolismInput.value),
    heightStr: height,
    weight_lb: weightCurrent,
    system_type
  });

  ageText.innerText = String(bodyAge);
  ageField.value = String(bodyAge);
}

document.getElementById('modalAddEditBody').addEventListener('input', function (event) {
  const inputName = event.target.name;
  if (inputName === 'weight_lb') updateBmiAndWater(this);
  if (['weight_lb','body_fat_pct','muscle_pct','visceral_fat','water_pct','resting_metabolism'].includes(inputName)) {
    updateBodyAge(this);
  }
});

/* ===== Submit (crear/editar) ===== */
$('#formAddEditBody').on('submit', function (e) {
  e.preventDefault();

  const recordId = document.getElementById('addEditBodyId').value;
  
  // CAMBIO: Fecha y hora ahora son requeridos siempre
  const required = [
    'composition_date','composition_time',
    'weight_lb','body_fat_pct','water_pct','muscle_pct','resting_metabolism','visceral_fat'
  ];

  const ok = validateFormFields(e.target, required, '<?= $traducciones['input_generic_error'] ?>');
  if (!ok) return;

  const payload = {};
  $(this).serializeArray().forEach(i => payload[i.name] = i.value);
  // CAMBIO: Se elimina la llamada a stripEmptyDateTime
  // if (!recordId) stripEmptyDateTime(payload);

  const url = recordId ? `body-compositions/${recordId}` : 'body-compositions';
  const method = recordId ? 'PUT' : 'POST';

  $.ajax({
    url, type: method,
    data: JSON.stringify(payload),
    contentType: 'application/json',
    dataType: 'json',
    success: function (resp) {
      if (resp.value) {
        Swal.fire({ icon:'success', title: messages[lang].titleSuccess, text: messages[lang].genericSaveSuccess, timer:1500, showConfirmButton:false });
        $('#modalAddEditBody').modal('hide');
        loadBodyCompositionData();
        reloadAlerts();
      } else {
        Swal.fire(messages[lang].titleError, resp.message || messages[lang].genericSaveError, 'error');
      }
    },
    error: function () {
      Swal.fire(messages[lang].titleError, messages[lang].genericSaveError, 'error');
    }
  });
});

/* ===== Carga tabla ===== */
function loadBodyCompositionData() {
  $.ajax({
    url: 'body-compositions',
    method: 'GET',
    dataType: 'json',
    success: function (res) {
      if (res.value) {
        document.getElementById('toolbar').classList.remove('d-none');
        $('#bodyCompositionTable').bootstrapTable('load', res.data).bootstrapTable('resetSearch','');
      } else {
        $('#bodyCompositionTable').bootstrapTable('load', []);
        Swal.fire(messages[lang].titleError, messages[lang].genericFetchError, 'error');
      }
    },
    error: function () {
      Swal.fire(messages[lang].titleError, messages[lang].genericFetchError, 'error');
    }
  });
}
$('#bodyCompositionTable').on('refresh.bs.table', loadBodyCompositionData);

/* ===== Formatter fecha ===== */
function dateFormatterBodyComposition(value) {
  if (!value) return '';
  const parts = value.split('-');
  if (parts.length !== 3) return value;
  const [year, month, day] = parts;
  const mm = String(month).padStart(2,'0');
  const dd = String(day).padStart(2,'0');
  return `${mm}/${dd}/${year}`;
}
window.dateFormatterBodyComposition = dateFormatterBodyComposition;

/* ===== Init ===== */
document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('addEditCompositionDate')) {
    FP.inst.addEditCompositionDate = flatpickr('#addEditCompositionDate', FP.date);
  }
  if (document.getElementById('addEditCompositionTime')) {
    FP.inst.addEditCompositionTime = flatpickr('#addEditCompositionTime', FP.time);
  }

  if (!height || height === '0') {
    Swal.fire({
      title: '<?= $traducciones['height_alert_title'] ?>',
      text: '<?= $traducciones['height_alert_text'] ?>',
      icon: 'warning',
      confirmButtonText: '<?= $traducciones['height_alert_ok'] ?>'
    }).then(() => { window.location.href = 'my_profile'; });
    return;
  }

  document.querySelectorAll('.form-control.number').forEach(input => {
    input.addEventListener('input', () => { input.value = input.value.replace(/[^0-9\.,]/g, ''); });
    input.addEventListener('paste', (e) => {
      const paste = (e.clipboardData || window.clipboardData).getData('text');
      if (/[^0-9\.,]/.test(paste)) e.preventDefault();
    });
  });

  loadBodyCompositionData();
});
</script>

<script>
/* ===== Helpers de búsqueda al cargar tablas ===== */
function getSearchParam() {
  const params = new URLSearchParams(window.location.search);
  return params.get('search');
}
function applySearchAfterTableLoads(tableId, searchValue) {
  if (!searchValue) return;
  const $table = $(`#${tableId}`);
  const $searchInput = $('.search-input');
  $table.on('post-body.bs.table', function () {
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
$(document).ready(function () {
  const search = getSearchParam();
  if ($('#bodyCompositionTable').length) {
    applySearchAfterTableLoads('bodyCompositionTable', search);
  } else if ($('#lipidProfileTable').length) {
    applySearchAfterTableLoads('lipidProfileTable', search);
  } else if ($('#renalFunctionTable').length) {
    applySearchAfterTableLoads('renalFunctionTable', search);
  } else if ($('#glucoseKetoneTable').length) {
    applySearchAfterTableLoads('glucoseKetoneTable', search);
  }
});
</script>