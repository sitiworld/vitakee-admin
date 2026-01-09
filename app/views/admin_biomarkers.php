
    <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="card-body">
            <!-- id="panelsTable" data-toggle="table" data-search="true" data-show-refresh="true"
                            data-page-list="[5, 10, 20]" data-page-size="5" data-show-columns="true"
                            data-show-pagination-switch="true" data-pagination="false" data-detail-view="false"
                            class="table-borderless -->

            <h4 class="page-title"><?= $traducciones['table_column_biomarker2'] ?? 'Biomarkers' ?></h4>
            <div id="toolbar" class="d-none">
              <button id="addBiomarkerBtn" class="btn btn-add">
                + <?= $traducciones['add_biomarker_button'] ?>
              </button>
              <button id="exportCsvBtn" class="btn btn-action-lipid">
                <span class="mdi mdi-file-export-outline"></i> <?= $traducciones['export_csv_button'] ?>
              </button>
            </div>
            <div class="card">
              <div class="card-body">


                <table id="biomarkersTable" data-toggle="table" data-search="true" data-show-refresh="true"
                  data-page-list="[5, 10, 20]" data-page-size="5" data-show-columns="true" data-pagination="true"
                  data-show-pagination-switch="true" class="table-borderless" data-url="biomarkers/all"
                  data-toolbar="#toolbar" data-locale="<?= $locale ?>">
                  <thead>

                    <tr>
                      <th data-field="name" data-sortable="true"><?= $traducciones['biomarkers_table_column_name'] ?>
                      </th>
                      <th data-field="unit" data-sortable="true"><?= $traducciones['biomarkers_table_column_unit'] ?>
                      </th>
                      <th data-field="reference_min" data-sortable="true">
                        <?= $traducciones['biomarkers_table_column_reference_min'] ?>
                      </th>
                      <th data-field="reference_max" data-sortable="true">
                        <?= $traducciones['biomarkers_table_column_reference_max'] ?>
                      </th>
                      <th data-field="deficiency_label" data-sortable="true">
                        <?= $traducciones['biomarkers_table_column_deficiency_label'] ?>
                      </th>
                      <th data-field="excess_label" data-sortable="true">
                        <?= $traducciones['biomarkers_table_column_excess_label'] ?>
                      </th>
                      <th data-field="description" data-sortable="false">
                        <?= $traducciones['biomarkers_table_column_description'] ?>
                      </th>
                      <th data-field="id" data-width="100" data-align="center"
                        data-formatter="biomarkerActionFormatter">
                        <?= $traducciones['actions'] ?>
                      </th>
                    </tr>
                  </thead>
                </table>

              </div>
            </div>


            <!-- Add / Edit Biomarker Modal -->
            <div class="modal fade" id="biomarkerModal" tabindex="-1" aria-labelledby="biomarkerModalLabel"
              aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form id="biomarkerForm">
                    <div class="modal-header">
                      <h5 class="modal-title" id="biomarkerModalLabel"><?= $traducciones['biomarker_modal_title'] ?>
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" id="biomarker_id" name="id">
                      <div class="mb-3">
                        <label for="panel_id"
                          class="form-label"><?= $traducciones['biomarker_modal_label_panel'] ?></label>
                        <select id="panel_id" name="panel_id" class="form-control">
                          <option value="">Cargando panels…</option>
                        </select>
                      </div>

                      <div class="mb-3">
                        <label for="name" class="form-label"><?= $traducciones['biomarker_modal_label_name'] ?></label>
                        <input type="text" id="name" name="name" class="form-control">
                      </div>

                      <div class="mb-3">
                        <label for="unit" class="form-label"><?= $traducciones['biomarker_modal_label_unit'] ?></label>
                        <input type="text" id="unit" name="unit" class="form-control">
                      </div>
                      <div class="mb-3 row">
                        <div class="col">
                          <label for="reference_min"
                            class="form-label"><?= $traducciones['biomarker_modal_label_reference_min'] ?></label>
                          <input type="text" step="any" id="reference_min" name="reference_min"
                            class="form-control number">
                        </div>
                        <div class="col">
                          <label for="reference_max"
                            class="form-label"><?= $traducciones['biomarker_modal_label_reference_max'] ?></label>
                          <input type="text" step="any" id="reference_max" name="reference_max"
                            class="form-control number">
                        </div>
                      </div>
                      <div class="mb-3">
                        <label for="deficiency_label"
                          class="form-label"><?= $traducciones['biomarker_modal_label_deficiency'] ?></label>
                        <input type="text" id="deficiency_label" name="deficiency_label" class="form-control">
                      </div>
                      <div class="mb-3">
                        <label for="excess_label"
                          class="form-label"><?= $traducciones['biomarker_modal_label_excess'] ?></label>
                        <input type="text" id="excess_label" name="excess_label" class="form-control">
                      </div>
                      <div class="mb-3">
                        <label for="max_exam"
                          class="form-label"><?= $traducciones['biomarker_modal_label_max_exam'] ?? 'Max. Exams per Day' ?></label>
                        <input type="text" min="0" id="max_exam" name="max_exam" class="form-control number">
                      </div>
                      <div class="mb-3">
                        <label for="description"
                          class="form-label"><?= $traducciones['biomarker_modal_label_description'] ?></label>
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                      </div>

                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-save"><i class="mdi mdi-content-save-outline"></i>
                        <?= $traducciones['save'] ?></button>
                      <button type="button" class="btn btn-cancel" data-bs-dismiss="modal"> <i class=" mdi mdi-cancel">
                        </i> <?= $traducciones['cancel'] ?></button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          </div>
        </div>


      </div>
      <!-- View Biomarker Modal -->
      <div class="modal fade" id="viewBiomarkerModal" tabindex="-1" aria-labelledby="viewBiomarkerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg"> <!-- Hacemos el modal más ancho -->
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="viewBiomarkerModalLabel">
                <?= $traducciones['biomarker_view_modal_title'] ?? 'Biomarker Details' ?>
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

              <div class="row">
                <div class="col-md-6 mb-3">
                  <strong><?= $traducciones['biomarker_modal_label_name'] ?>:</strong> <span id="view_name"></span>
                </div>
                <div class="col-md-6 mb-3">
                  <strong><?= $traducciones['biomarker_modal_label_unit'] ?>:</strong> <span id="view_unit"></span>
                </div>
                <div class="col-md-6 mb-3">
                  <strong><?= $traducciones['biomarker_modal_label_reference_min'] ?>:</strong> <span
                    id="view_reference_min"></span>
                </div>
                <div class="col-md-6 mb-3">
                  <strong><?= $traducciones['biomarker_modal_label_reference_max'] ?>:</strong> <span
                    id="view_reference_max"></span>
                </div>
                <div class="col-md-6 mb-3">
                  <strong><?= $traducciones['biomarker_modal_label_deficiency'] ?>:</strong> <span
                    id="view_deficiency_label"></span>
                </div>
                <div class="col-md-6 mb-3">
                  <strong><?= $traducciones['biomarker_modal_label_excess'] ?>:</strong> <span
                    id="view_excess_label"></span>
                </div>
                <div class="col-md-6 mb-3">
                  <strong><?= $traducciones['biomarker_modal_label_max_exam'] ?? 'Max Exams per Day' ?>:</strong> <span
                    id="view_max_exam"></span>
                </div>
                <div class="col-md-12 mb-3">
                  <strong><?= $traducciones['biomarker_modal_label_description'] ?>:</strong>
                  <div id="view_description"></div>
                </div>
              </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                <i class="mdi mdi-close-circle-outline"></i> <?= $traducciones['close'] ?? 'Close' ?>
              </button>
            </div>
          </div>
        </div>
      </div>


      <!-- /Right-bar -->

      <!-- Right bar overlay-->
      <!-- Right bar overlay-->
      <div class="rightbar-overlay"></div>
      <!-- JQuery primero -->
      <!-- Bootstrap Table CSS -->


      <!-- Bootstrap JS -->


      <!-- Bootstrap Table JS -->
      <script src="public/assets/js/logout.js"></script>
      <script>
        document.getElementById('exportCsvBtn').addEventListener('click', function () {
          Swal.fire({
            title: messages[lang].exportLoadingTitle,
            text: messages[lang].exportLoadingText,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          fetch('biomarker/export/<?php echo $_SESSION["user_id"] ?>')
            .then(async response => {
              const contentType = response.headers.get("Content-Type");

              if (contentType && contentType.includes("text/csv")) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const filename = `${messages[lang].csvFilenamePrefix}.csv`;

                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);

                Swal.close();
              } else {
                const res = await response.json();
                Swal.fire({
                  icon: 'info',
                  title: messages[lang].noRecordsTitle,
                  text: messages[lang].noRecordsText
                });
              }
            })
            .catch(error => {
              Swal.fire({
                icon: 'error',
                title: messages[lang].exportErrorTitle,
                text: messages[lang].exportErrorText
              });
            });
        });
      </script>

      <script type="module">
        import { validateFormFields, clearValidationMessages } from "./public/assets/js/helpers/helpers.js";

        $(document).ready(function () {
          const idioma = '<?= $_SESSION["idioma"] ?? "ES" ?>';

          // Reemplaza tu inicialización actual de Select2 con esta
          $('#panel_id').select2({
            // theme: 'bootstrap-5', // Correcto para la compatibilidad visual
            width: '100%',        // Correcto para que ocupe todo el ancho del contenedor
            placeholder: '<?= $traducciones['select_placeholder'] ?? 'Seleccione una opción' ?>',
            dropdownParent: $('#biomarkerModal') // <-- AÑADE ESTA LÍNEA ESENCIAL
          });

          const mensajes = {
            ES: {
              tituloError: '<?= $traducciones['tituloError_biomarker'] ?>',
              tituloExito: '<?= $traducciones['tituloExito_biomarker'] ?>',
              biomarker_modal_title_add: '<?= $traducciones['biomarker_modal_title_add'] ?>',
              biomarker_modal_title_edit: '<?= $traducciones['biomarker_modal_title_edit'] ?>',
              errorCargarBiomarcadores: '<?= $traducciones['errorCargarBiomarcadores_biomarker'] ?>',
              errorGenerico: '<?= $traducciones['errorGenerico_biomarker'] ?>',
              sinPanels: '<?= $traducciones['sinPanels_biomarker'] ?>',
              errorCargarPanels: '<?= $traducciones['errorCargarPanels_biomarker'] ?>',
              errorObtener: '<?= $traducciones['errorObtener_biomarker'] ?>',
              errorConexion: '<?= $traducciones['errorConexion_biomarker'] ?>',
              guardadoExito: '<?= $traducciones['guardadoExito_biomarker'] ?>',
              guardadoError: '<?= $traducciones['guardadoError_biomarker'] ?>',
              noRecordsTitle: '<?= $traducciones['no_records_title'] ?>',
              noRecordsText: '<?= $traducciones['no_records_text'] ?>',
              exportErrorTitle: '<?= $traducciones['export_error_title'] ?>',
              exportErrorText: '<?= $traducciones['export_error_text'] ?>',
              exportLoadingTitle: '<?= $traducciones['export_loading_title'] ?>',
              exportLoadingText: '<?= $traducciones['export_loading_text'] ?>',
              csvFilenamePrefix: '<?= $traducciones['csv_filename_prefix_biomarker'] ?>',
              inputErrorGeneric: '<?= $traducciones['input_generic_error'] ?>'
            },
            EN: {
              tituloError: '<?= $traducciones['tituloError_biomarker'] ?>',
              tituloExito: '<?= $traducciones['tituloExito_biomarker'] ?>',
              biomarker_modal_title_add: '<?= $traducciones['biomarker_modal_title_add'] ?>',
              biomarker_modal_title_edit: '<?= $traducciones['biomarker_modal_title_edit'] ?>',
              errorCargarBiomarcadores: '<?= $traducciones['errorCargarBiomarcadores_biomarker'] ?>',
              errorGenerico: '<?= $traducciones['errorGenerico_biomarker'] ?>',
              sinPanels: '<?= $traducciones['sinPanels_biomarker'] ?>',
              errorCargarPanels: '<?= $traducciones['errorCargarPanels_biomarker'] ?>',
              errorObtener: '<?= $traducciones['errorObtener_biomarker'] ?>',
              errorConexion: '<?= $traducciones['errorConexion_biomarker'] ?>',
              guardadoExito: '<?= $traducciones['guardadoExito_biomarker'] ?>',
              guardadoError: '<?= $traducciones['guardadoError_biomarker'] ?>',
              noRecordsTitle: '<?= $traducciones['no_records_title'] ?>',
              noRecordsText: '<?= $traducciones['no_records_text'] ?>',
              exportErrorTitle: '<?= $traducciones['export_error_title'] ?>',
              exportErrorText: '<?= $traducciones['export_error_text'] ?>',
              exportLoadingTitle: '<?= $traducciones['export_loading_title'] ?>',
              exportLoadingText: '<?= $traducciones['export_loading_text'] ?>',
              csvFilenamePrefix: '<?= $traducciones['csv_filename_prefix_biomarker'] ?>',
              inputErrorGeneric: '<?= $traducciones['input_generic_error'] ?>'
            }
          };

          const t = mensajes[idioma];

          window.biomarkerActionFormatter = function (value, row) {
            return `<button class="btn btn-view action-icon btn-sm me-1 viewBiomarkerBtn" data-id="${row.biomarker_id}">
      <i class="mdi mdi-eye-outline"></i>
    </button>
    <button class="btn btn-pencil action-icon btn-sm editBiomarkerBtn" data-id="${row.biomarker_id}">
      <i class="mdi mdi-pencil-outline"></i>
    </button>`;
          };

          $('#biomarkersTable').bootstrapTable();

          function loadBiomarkers() {
            $.ajax({
              url: 'biomarkers/all',
              method: 'GET',
              dataType: 'json',
              success: function (res) {
                let data = res.data || [];

                if (idioma === 'ES') {
                  data = data.map(bm => ({
                    ...bm,
                    name: bm.name_es || bm.name,
                    deficiency_label: bm.deficiency_es || bm.deficiency_label,
                    excess_label: bm.excess_es || bm.excess_label,
                    description: bm.description_es || bm.description
                  }));
                }

                $('#biomarkersTable').bootstrapTable('load', data);
              },
              error: function (xhr, status, error) {
                console.error('Error cargando biomarcadores:', status, error);
                Swal.fire(t.tituloError, t.errorCargarBiomarcadores, 'error');
              }
            });
          }

          $('#biomarkersTable').on('refresh.bs.table', loadBiomarkers);

          function loadPanels(selectedId = '') {
            $.ajax({
              url: 'test-panels',
              method: 'GET',
              dataType: 'json',
              success: function (res) {
                const $sel = $('#panel_id');
                $sel.empty();
                if (res.value === true && Array.isArray(res.data)) {
                  res.data.forEach(function (p) {
                    const sel = (p.panel_id == selectedId ? ' selected' : '');
                    $sel.append(`<option value="${p.panel_id}"${sel}>${p.translated_name}</option>`);
                  });
                } else {
                  $sel.append(`<option value="">${t.sinPanels}</option>`);
                }
              },
              error: function () {
                $('#panel_id').html(`<option value="">${t.errorCargarPanels}</option>`);
              }
            });
          }

          document.getElementById('toolbar').classList.remove('d-none');
          loadBiomarkers();

          $('#addBiomarkerBtn').click(function (e) {
            clearValidationMessages(e.target);
            $('#biomarkerForm')[0].reset();
            $('#biomarker_id').val('');
            $('#biomarkerModalLabel').text(t.biomarker_modal_title_add);
            loadPanels();
            $('#biomarkerModal').modal('show');
          });

          $(document).on('click', '.editBiomarkerBtn', function () {
            clearValidationMessages(document.getElementById('biomarkerForm'));
            const id = $(this).data('id');
            $.ajax({
              url: `biomarkers/${id}`,
              method: 'GET',
              dataType: 'json',
              success: function (res) {
                if (res.value === true) {
                  const b = res.data;
                  $('#biomarker_id').val(b.biomarker_id);
                  loadPanels(b.panel_id);
                  $('#name').val(idioma === 'ES' ? b.name_es : b.name);
                  $('#unit').val(b.unit);
                  $('#panel_id').val(b.panel_id);
                  $('#reference_min').val(b.reference_min);
                  $('#reference_max').val(b.reference_max);
                  $('#deficiency_label').val(idioma === 'ES' ? b.deficiency_es : b.deficiency_label);
                  $('#excess_label').val(idioma === 'ES' ? b.excess_es : b.excess_label);
                  $('#description').val(idioma === 'ES' ? b.description_es : b.description);
                  $('#max_exam').val(b.max_exam);
                  $('#biomarkerModalLabel').text(t.biomarker_modal_title_edit);
                  $('#biomarkerModal').modal('show');
                } else {
                  Swal.fire(t.tituloError, t.errorObtener, 'error');
                }
              },
              error: function (xhr, status, error) {
                console.error('Error status:', status, 'Error details:', error);
                Swal.fire(t.tituloError, t.errorConexion, 'error');
              }
            });
          });

          $(document).on('click', '.viewBiomarkerBtn', function () {
            const id = $(this).data('id');
            $.ajax({
              url: `biomarkers/${id}`,
              method: 'GET',
              dataType: 'json',
              success: function (res) {
                if (res.value === true) {
                  const b = res.data;
                  $('#view_name').text(idioma === 'ES' ? b.name_es : b.name);
                  $('#view_unit').text(b.unit);
                  $('#view_reference_min').text(b.reference_min);
                  $('#view_reference_max').text(b.reference_max);
                  $('#view_deficiency_label').text(idioma === 'ES' ? b.deficiency_es : b.deficiency_label);
                  $('#view_excess_label').text(idioma === 'ES' ? b.excess_es : b.excess_label);
                  $('#view_description').text(idioma === 'ES' ? b.description_es : b.description);
                  $('#view_max_exam').text(b.max_exam);
                  $('#viewBiomarkerModal').modal('show');
                } else {
                  Swal.fire(t.tituloError, t.errorObtener, 'error');
                }
              },
              error: function (xhr, status, error) {
                console.error('Error status:', status, 'Error details:', error);
                Swal.fire(t.tituloError, t.errorConexion, 'error');
              }
            });
          });

          $('#biomarkerForm').submit(function (e) {
            e.preventDefault();

            const valid = validateFormFields(e.target,
              ['name', 'unit', 'reference_min', 'reference_max', 'deficiency_label', 'excess_label', 'description', 'max_exam'],
              '<?= $traducciones['input_generic_error']; ?>'
            );
            if (!valid) return;

            const maxExam = parseInt($('#max_exam').val(), 10);
            const maxExamInput = $('#max_exam');
            maxExamInput.removeClass('is-invalid is-valid border-danger border-success');
            maxExamInput.next('.invalid-feedback').remove();

            if (isNaN(maxExam) || maxExam <= 0) {
              maxExamInput.addClass('border-danger');
              maxExamInput.after(`<div class="invalid-feedback d-block"><?= $traducciones['max_exam_invalid'] ?? 'Please enter a number greater than 0.' ?></div>`);
              return;
            } else {
              maxExamInput.addClass('border-success');
            }

            const id = $('#biomarker_id').val().trim();
            let formData = {};
            $('#biomarkerForm').serializeArray().map(x => formData[x.name] = x.value);

            if (idioma === 'ES') {
              formData['name_es'] = formData['name'];
              formData['deficiency_es'] = formData['deficiency_label'];
              formData['excess_es'] = formData['excess_label'];
              formData['description_es'] = formData['description'];
            }

            const ajaxOptions = {
              url: id ? (idioma === 'ES' ? `biomarkers/es/${id}` : `biomarkers/${id}`) : 'biomarkers',
              method: id ? 'PUT' : 'POST',
              dataType: 'json',
              contentType: 'application/json',
              data: JSON.stringify(formData),
              success: function (response) {
                if (response.value === true) {
                  $('#biomarkerModal').modal('hide');
                  Swal.fire(t.tituloExito, t.guardadoExito, 'success').then(loadBiomarkers);
                } else {
                  Swal.fire(t.tituloError, t.guardadoError, 'error');
                }
              },
              error: function (xhr, status, error) {
                console.error('Error status:', status, 'Error:', error);
                Swal.fire(t.tituloError, t.guardadoError, 'error');
              }
            };

            $.ajax(ajaxOptions);
          });

          // 9) Exportar CSV (fallback por si otro botón lo necesita)
          $('#exportCsvBtn').click(function () {
            window.location.href = 'biomarker/export/<?php echo $_SESSION["user_id"] ?>';
          });
        });

        // Máscara para inputs numéricos
        document.addEventListener('DOMContentLoaded', () => {
          document.querySelectorAll('.form-control.number').forEach(input => {
            input.addEventListener('input', () => {
              input.value = input.value.replace(/[^0-9\.,]/g, '');
            });
            input.addEventListener('paste', (e) => {
              const paste = (e.clipboardData || window.clipboardData).getData('text');
              if (/[^0-9\.,]/.test(paste)) {
                e.preventDefault();
              }
            });
          });
        });
      </script>



      <!-- Bootstrap JS -->


      <!-- Bootstrap Table JS -->


</body>

</html>