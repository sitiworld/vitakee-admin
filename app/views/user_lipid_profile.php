<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="page-title my-2"><?= $traducciones['lipid_records'] ?></h4>
        </div>
    </div>
    <div id="toolbar" class="d-none">
        <button class="btn btn-action-glucose w-20" onclick="openLipidModal()">
            + <?= $traducciones['add_new_lipid'] ?>
        </button>
        <button id="btnExportCSV" class="btn btn-action-lipid" type="button">
            <span class="mdi mdi-file-export-outline"></span> <?= $traducciones['export_csv'] ?>
        </button>
    </div>
    <div class="card">
        <div class="card-body">

            <table id="lipidProfileTable" data-toggle="table" data-search="true" data-show-refresh="true"
                   data-page-list="[5, 10, 20]" data-page-size="5" data-pagination="true" data-show-columns="true"
                   data-show-pagination-switch="true" data-url="lipid-profile" class="table-borderless"
                   data-toolbar="#toolbar" data-locale="<?= $locale ?>">
                <thead>
                    <tr>
                        <th data-field="lipid_profile_date" data-sortable="true"
                            data-formatter="dateFormatterLipidProfile"><?= $traducciones['date'] ?></th>
                        <th data-field="lipid_profile_time" data-sortable="true">
                            <?= $traducciones['time'] ?>
                        </th>
                        <th data-field="id" data-align="center" data-formatter="actionFormatter">
                            <?= $traducciones['actions'] ?>
                        </th>
                    </tr>
                </thead>
            </table>

        </div>
    </div>
    <div class="modal fade" id="addEditLipidModal" tabindex="-1" aria-labelledby="addEditLipidModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addEditLipidModalLabel">
          <?= $traducciones['add_edit_title'] ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="lipidProfileForm" class="mt-4">
          <input type="hidden" name="editLipidId" id="editLipidId">

          <div class="row">
            <div class="col-md-6 mb-3" id="group_date_lipid">
              <label for="lipid_profile_date" class="form-label"><?= $traducciones['date'] ?></label>
              <input type="text" class="form-control fp-date" id="lipid_profile_date" name="lipid_profile_date"
                     placeholder="<?= $traducciones['ph_date'] ?? 'Date (MM/DD/YYYY)' ?>">
            </div>

            <div class="col-md-6 mb-3" id="group_time_lipid">
              <label for="lipid_profile_time" class="form-label"><?= $traducciones['time'] ?></label>
              <input type="text" class="form-control fp-time" id="lipid_profile_time" name="lipid_profile_time"
                     placeholder="<?= $traducciones['ph_time'] ?? 'Time (HH:mm:ss)' ?>">
            </div>

            <div class="col-md-6 mb-3">
              <label for="ldl"><?= $traducciones['ldl_cholesterol'] ?></label>
              <input type="text" min="0" max="300" step="0.1" class="form-control number" name="ldl" id="ldl"
                     placeholder="<?= $traducciones['ph_ldl_number'] ?? 'e.g., 110' ?>">
            </div>

            <div class="col-md-6 mb-3">
              <label for="hdl"><?= $traducciones['hdl_cholesterol'] ?></label>
              <input type="text" min="0" max="100" step="0.1" class="form-control number" name="hdl" id="hdl"
                     placeholder="<?= $traducciones['ph_hdl_number'] ?? 'e.g., 55' ?>">
            </div>

            <div class="col-md-6 mb-3">
              <label for="triglycerides"><?= $traducciones['triglycerides'] ?></label>
              <input type="text" min="0" max="500" step="0.1" class="form-control number" name="triglycerides" id="triglycerides"
                     placeholder="<?= $traducciones['ph_triglycerides_number'] ?? 'e.g., 130' ?>">
            </div>

            <div class="col-md-6 mb-3">
              <label><?= $traducciones['total_cholesterol'] ?></label>
              <i class="mdi mdi-help-circle-outline" data-bs-toggle="tooltip" data-bs-placement="top"
                 title="<?= $traducciones['total_cholesterol_description_tooltip'] ?>"></i>
              <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top"
                 title="<?= $traducciones['total_cholesterol_accuracy_tooltip'] ?>"></i>
              <div class="form-control-plaintext" id="total_cholesterol_text">--</div>
              <input type="hidden" name="total_cholesterol" id="total_cholesterol">
            </div>

            <div class="col-md-6 mb-3">
              <label><?= $traducciones['non_hdl_cholesterol'] ?></label>
              <i class="mdi mdi-help-circle-outline" data-bs-toggle="tooltip" data-bs-placement="top"
                 title="<?= $traducciones['non_hdl_description_tooltip'] ?>"></i>
              <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top"
                 title="<?= $traducciones['non_hdl_accuracy_tooltip'] ?>"></i>
              <div class="form-control-plaintext" id="non_hdl_text">--</div>
              <input type="hidden" name="non_hdl" id="non_hdl">
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="submit" form="lipidProfileForm" class="btn btn-save">
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


<script>
  const userFullName = "<?= $_SESSION['user_name'] ?>";
  // Diccionario de mensajes, traducido del backend
  const lang = '<?php echo $_SESSION["idioma"] ?? "EN"; ?>';
  const messages = {
      ES: {
          add: '<?= $traducciones['lipid_records'] ?>',
          edit: '<?= $traducciones['edit_title_lipid'] ?>',
          successTitle: '<?= $traducciones['successTitle_lipid_profile'] ?>',
          saveSuccess: '<?= $traducciones['saveSuccess_lipid_profile'] ?>',
          errorTitle: '<?= $traducciones['errorTitle_lipid_profile'] ?>',
          loadError: '<?= $traducciones['loadError_lipid_profile'] ?>',
          deleteError: '<?= $traducciones['deleteError_lipid_profile'] ?>',
          saveError: '<?= $traducciones['saveError_lipid_profile'] ?>',
          confirmDeleteTitle: '<?= $traducciones['confirmDeleteTitle_lipid_profile'] ?>',
          confirmDeleteText: '<?= $traducciones['confirmDeleteText_lipid_profile'] ?>',
          confirmDeleteBtn: '<?= $traducciones['confirmDeleteBtn_lipid_profile'] ?>',
          deletedTitle: '<?= $traducciones['deletedTitle_lipid_profile'] ?>',
          deletedText: '<?= $traducciones['deletedText_lipid_profile'] ?>',
          noRecordsTitle: '<?= $traducciones['no_records_title'] ?>',
          noRecordsText: '<?= $traducciones['no_records_text'] ?>',
          exportErrorTitle: '<?= $traducciones['export_error_title'] ?>',
          exportErrorText: '<?= $traducciones['export_error_text'] ?>',
          exportLoadingTitle: '<?= $traducciones['export_loading_title'] ?>',
          exportLoadingText: '<?= $traducciones['export_loading_text'] ?>',
          csvFilenamePrefix: '<?= $traducciones['csv_filename_prefix_lipid'] ?>',
          viewComponent: '<?= $traducciones['viewComponent_lipid_profile'] ?>',
          viewImage: '<?= $traducciones['viewImage_lipid_profile'] ?>',
          input_generic_error: '<?= $traducciones['input_generic_error'] ?>'
      },
      EN: {
          add: '<?= $traducciones['lipid_records'] ?>',
          edit: '<?= $traducciones['edit_title_lipid'] ?>',
          successTitle: '<?= $traducciones['successTitle_lipid_profile'] ?>',
          saveSuccess: '<?= $traducciones['saveSuccess_lipid_profile'] ?>',
          errorTitle: '<?= $traducciones['errorTitle_lipid_profile'] ?>',
          loadError: '<?= $traducciones['loadError_lipid_profile'] ?>',
          deleteError: '<?= $traducciones['deleteError_lipid_profile'] ?>',
          saveError: '<?= $traducciones['saveError_lipid_profile'] ?>',
          confirmDeleteTitle: '<?= $traducciones['confirmDeleteTitle_lipid_profile'] ?>',
          confirmDeleteText: '<?= $traducciones['confirmDeleteText_lipid_profile'] ?>',
          confirmDeleteBtn: '<?= $traducciones['confirmDeleteBtn_lipid_profile'] ?>',
          deletedTitle: '<?= $traducciones['deletedTitle_lipid_profile'] ?>',
          deletedText: '<?= $traducciones['deletedText_lipid_profile'] ?>',
          noRecordsTitle: '<?= $traducciones['no_records_title'] ?>',
          noRecordsText: '<?= $traducciones['no_records_text'] ?>',
          exportErrorTitle: '<?= $traducciones['export_error_title'] ?>',
          exportErrorText: '<?= $traducciones['export_error_text'] ?>',
          exportLoadingTitle: '<?= $traducciones['export_loading_title'] ?>',
          exportLoadingText: '<?= $traducciones['export_loading_text'] ?>',
          csvFilenamePrefix: '<?= $traducciones['csv_filename_prefix_lipid'] ?>',
          viewComponent: '<?= $traducciones['viewComponent_lipid_profile'] ?>',
          viewImage: '<?= $traducciones['viewImage_lipid_profile'] ?>',
          input_generic_error: '<?= $traducciones['input_generic_error'] ?>'
      }
  };
</script>

<script src="public/assets/js/logout.js"></script>

<script type="module">
  import { validateFormFields, clearValidationMessages } from "./public/assets/js/helpers/helpers.js";
  import { reloadAlerts } from "./public/assets/js/controllers/notificationsController.js";

  /* ========= Flatpickr config ========= */
  const FP = {
    date: {
      altInput: true,       // lo que ve el usuario
      altFormat: 'm/d/Y',   // MM/DD/YYYY visible
      dateFormat: 'Y-m-d',  // Y-m-d para backend
      allowInput: true
    },
    time: {
      enableTime: true,
      noCalendar: true,
      dateFormat: 'H:i:S',  // 24h con segundos para backend
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
      const fmt = id.includes('_time') ? 'H:i:S' : 'Y-m-d';
      inst.setDate(valueYmdOrTime, true, fmt);
    } else {
      inst.clear();
    }
  }

  // ==== Helpers para mostrar/ocultar fecha/hora y limpiar payload ====
  // CAMBIO: Modificada para manejar 'required' y limpieza, no visibilidad
  function toggleDateTimeFieldsLipid(isEditMode){
      const iDate = document.getElementById('lipid_profile_date');
      const iTime = document.getElementById('lipid_profile_time');

      // Poner 'required' en el input para la validación
      iDate?.setAttribute('data-required', 'true');
      iTime?.setAttribute('data-required', 'true');

      if (!isEditMode) { // Si es modo CREAR
        // Limpiar campos antes de autocompletar
        if (iDate) iDate.value = '';
        if (iTime) iTime.value = '';
        FP.inst.lipid_profile_date?.clear?.();
        FP.inst.lipid_profile_time?.clear?.();
      }
      // Si es modo EDITAR, no hacer nada, la AJAX poblará los campos.
  }
  
  // CAMBIO: Ya no es necesaria, la fecha/hora siempre se envían
  /*
  function stripEmptyDateTime(payload){
      if (payload.lipid_profile_date === '') delete payload.lipid_profile_date;
      if (payload.lipid_profile_time === '') delete payload.lipid_profile_time;
      return payload;
  }
  */

  // Utilidades globales para formateo
  function dateFormatterLipidProfile(value) {
      if (!value) return '';
      let dateStr = typeof value === 'string' ? value : (new Date(value)).toISOString().split('T')[0];
      const [year, month, day] = dateStr.split('-');
      const mm = String(month).padStart(2,'0');
      const dd = String(day).padStart(2,'0');
      return `${mm}/${dd}/${year}`; // MM/DD/YYYY
  }
  window.dateFormatterLipidProfile = dateFormatterLipidProfile;

  function actionFormatter(value, row) {
      return `
                  <a href="component_lipid?id=${row.lipid_profile_record_id}">
                      <button class="btn btn-view action-icon" title="${messages[lang].viewComponent}">
                          <i class="mdi mdi-eye-outline"></i>
                      </button>
                  </a>
                  <a href="user_test_documents?id_test_panel=e6861593-7327-4f63-9511-11d56f5398dc&id_test=${row.lipid_profile_record_id}">
                      <button class="btn btn-image-test action-icon" title="${messages[lang].viewImage}">
                          <i class="mdi mdi-file-image-outline"></i>
                      </button>
                  </a>
                  <button class="btn btn-pencil action-icon" onclick="openLipidModal('${row.lipid_profile_record_id}')" title="${messages[lang].edit}">
                      <i class="mdi mdi-pencil-outline"></i>
                  </button>
              `;
  }
  window.actionFormatter = actionFormatter;

  // Cálculo de perfil lipídico y actualización de UI
function updateLipidCalculatedFields() {
  const ldl = parseFloat(document.getElementById('ldl').value) || 0;
  const hdl = parseFloat(document.getElementById('hdl').value) || 0;
  const triglycerides = parseFloat(document.getElementById('triglycerides').value) || 0;

  // Curo L7: Friedewald con VLDL redondeado
  const vldl = Math.round(triglycerides / 5);
  const total_cholesterol = ldl + hdl + vldl;

  document.getElementById('total_cholesterol').value = total_cholesterol.toFixed(2);
  document.getElementById('total_cholesterol_text').innerText = total_cholesterol.toFixed(2);

  const non_hdl = total_cholesterol - hdl;
  document.getElementById('non_hdl').value = non_hdl.toFixed(2);
  document.getElementById('non_hdl_text').innerText = non_hdl.toFixed(2);
}


  // Modal Add/Edit unificado (con Flatpickr)
  function openLipidModal(recordId = null) {
      clearValidationMessages(document.getElementById('lipidProfileForm'));

      document.getElementById('lipidProfileForm').reset();
      $('#editLipidId').val('');
      const modalTitle = document.getElementById('addEditLipidModalLabel');

      // limpiar pickers al abrir (se hará en toggleDateTimeFieldsLipid si es 'crear')
      // fpSet('lipid_profile_date', null);
      // fpSet('lipid_profile_time', null);

      if (recordId) {
          modalTitle.textContent = messages[lang].edit;
          // EDITAR => preparar campos fecha/hora
          toggleDateTimeFieldsLipid(true); // true = isEditMode

          // Obtener datos para edición
          $.ajax({
              url: `lipid-profile/${recordId}`,
              type: 'GET',
              dataType: 'json',
              success: function (res) {
                  if (res.data) {
                      const data = res.data;
                      $('#editLipidId').val(data.lipid_profile_record_id);

                      // Set con Flatpickr (backend: Y-m-d y H:i:S)
                      fpSet('lipid_profile_date', data.lipid_profile_date || null);
                      fpSet('lipid_profile_time', data.lipid_profile_time || null);

                      $('#ldl').val(data.ldl);
                      $('#hdl').val(data.hdl);
                      $('#triglycerides').val(data.triglycerides);
                      // Calculados
                      updateLipidCalculatedFields();
                  } else {
                      Swal.fire(messages[lang].errorTitle, messages[lang].loadError, 'error');
                      return;
                  }
                  $('#addEditLipidModal').modal('show');
              },
              error: function () {
                  Swal.fire(messages[lang].errorTitle, messages[lang].loadError, 'error');
              }
          });
      } else {
          // CREAR
          modalTitle.textContent = messages[lang].add;
          toggleDateTimeFieldsLipid(false); // false = !isEditMode (limpia y pone required)
          
          // CAMBIO: Auto-poblar con fecha y hora actual
          const now = new Date();
          const currentDate = now.toISOString().split('T')[0]; // YYYY-MM-DD
          const currentTime = now.toTimeString().split(' ')[0]; // HH:mm:ss
          
          fpSet('lipid_profile_date', currentDate);
          fpSet('lipid_profile_time', currentTime);

          updateLipidCalculatedFields();
          $('#addEditLipidModal').modal('show');
      }
  }
  window.openLipidModal = openLipidModal;

  // Eliminar registro
  function deleteRecord(recordId) {
      Swal.fire({
          title: messages[lang].confirmDeleteTitle,
          text: messages[lang].confirmDeleteText,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: messages[lang].confirmDeleteBtn
      }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: `lipid-profile/${recordId}`,
                  type: 'delete',
                  dataType: 'json',
                  success: function (response) {
                      if (response.value) {
                          Swal.fire(messages[lang].deletedTitle, messages[lang].deletedText, 'success');
                          loadLipidData();
                      } else {
                          Swal.fire(messages[lang].errorTitle, response.message, 'error');
                      }
                  },
                  error: function () {
                      Swal.fire(messages[lang].errorTitle, messages[lang].deleteError, 'error');
                  }
              });
          }
      });
  }
  window.deleteRecord = deleteRecord;

  // Cargar datos de la tabla
  function loadLipidData() {
      $.ajax({
          url: 'lipid-profile',
          method: 'GET',
          dataType: 'json',
          success: function (res) {
              $('#lipidProfileTable').bootstrapTable('load', res.data || []).bootstrapTable('resetSearch', '');
          },
          error: function (xhr) {
              Swal.fire(messages[lang].errorTitle, xhr.responseJSON?.message || messages[lang].loadError, 'error');
          }
      });
  }
  window.loadLipidData = loadLipidData;

  // Exportar CSV
  document.getElementById('btnExportCSV').addEventListener('click', function () {
      Swal.fire({
          title: messages[lang].exportLoadingTitle,
          text: messages[lang].exportLoadingText,
          allowOutsideClick: false,
          allowEscapeKey: false,
          didOpen: () => {
              Swal.showLoading();
          }
      });

      fetch('lipid-profile/export/<?php echo $_SESSION['user_id'] ?>')
          .then(async response => {
              const contentType = response.headers.get("Content-Type");
              if (contentType && contentType.includes("text/csv")) {
                  const blob = await response.blob();
                  const url = window.URL.createObjectURL(blob);
                  const dateStr = new Date().toISOString().slice(0, 10);
                  const filename = `${messages[lang].csvFilenamePrefix}_${userFullName}_${dateStr}.csv`;
                  const a = document.createElement('a');
                  a.href = url;
                  a.download = filename;
                  document.body.appendChild(a);
                  a.click();
                  a.remove();
                  window.URL.revokeObjectURL(url);
                  Swal.close();
              } else {
                  await response.json().catch(()=>{});
                  Swal.fire({
                      icon: 'info',
                      title: messages[lang].noRecordsTitle,
                      text: messages[lang].noRecordsText
                  });
              }
          })
          .catch(() => {
              Swal.fire({
                  icon: 'error',
                  title: messages[lang].exportErrorTitle,
                  text: messages[lang].exportErrorText
              });
          });
  });

  // Validaciones de input + init
  document.addEventListener('DOMContentLoaded', () => {
      document.getElementById('toolbar')?.classList.remove('d-none');
      loadLipidData();

      // Instancias Flatpickr (si existen los inputs)
      if (document.getElementById('lipid_profile_date')) {
        FP.inst.lipid_profile_date = flatpickr('#lipid_profile_date', FP.date);
      }
      if (document.getElementById('lipid_profile_time')) {
        FP.inst.lipid_profile_time = flatpickr('#lipid_profile_time', FP.time);
      }

      // Enmascaramiento de inputs numéricos
      document.querySelectorAll('.form-control.number').forEach(input => {
          input.addEventListener('input', () => {
              input.value = input.value.replace(/[^0-9\.,]/g, '');
              updateLipidCalculatedFields();
          });
          input.addEventListener('paste', (e) => {
              const paste = (e.clipboardData || window.clipboardData).getData('text');
              if (/[^0-9\.,]/.test(paste)) e.preventDefault();
          });
      });
  });

  // Envío del formulario
  $('#lipidProfileForm').submit(function (e) {
      e.preventDefault();

      const recordId = $('#editLipidId').val();
      
      // CAMBIO: Fecha y hora siempre requeridos
      const required = ['lipid_profile_date', 'lipid_profile_time', 'ldl', 'hdl', 'triglycerides'];

      let validate = validateFormFields(e.target, required, messages[lang].input_generic_error);
      if (!validate) return;

      // Serialización simple
      let formData = {};
      $(this).serializeArray().forEach(item => {
          formData[item.name] = item.value;
      });

      // CAMBIO: Se elimina la llamada a stripEmptyDateTime
      // stripEmptyDateTime(formData);

      const url = recordId ? `lipid-profile/${recordId}` : 'lipid-profile';
      $.ajax({
          url: url,
          type: recordId ? 'PUT' : 'POST',
          data: JSON.stringify(formData),
          dataType: 'json',
          contentType: 'application/json',
          success: function (response) {
              if (response.value) {
                  Swal.fire(messages[lang].successTitle, messages[lang].saveSuccess, 'success');
                  $('#addEditLipidModal').modal('hide');
                  loadLipidData();
                  reloadAlerts();
              } else {
                  Swal.fire(messages[lang].errorTitle, response.message, 'error');
              }
          },
          error: function () {
              Swal.fire(messages[lang].errorTitle, messages[lang].saveError, 'error');
          }
      });
  });
</script>

<script>
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
                  Object.values(row).some(value =>
                      String(value).includes(searchValue)
                  )
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