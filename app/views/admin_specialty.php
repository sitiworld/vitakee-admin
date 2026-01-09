<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="card-body">
                <h4 class="page-title"><?= $traducciones['specialty2'] ?? 'Specialty' ?></h4>
                <div id="toolbar" class="d-none">
                    <button id="addSpecialtyBtn" class="btn btn-add">
                        <i class="bi bi-plus"></i>
                        <?= $traducciones['add_specialty_button'] ?? 'Add Specialty' ?>
                    </button>
                    <button id="btnExportCSV" class="btn btn-action-lipid">
                        <span class="mdi mdi-file-export-outline"></span>
                        <?= $traducciones['export_csv_button'] ?? 'Export CSV' ?>
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">


                        <table id="specialtyTable" class="table-borderless" data-toggle="table"
                            data-page-list="[5, 10, 20]" data-show-pagination-switch="true" data-pagination="true"
                            data-search="true" data-show-refresh="true" data-show-columns="true" data-page-size="5"
                            data-url="specialties" data-toolbar="#toolbar" data-locale="<?= $locale ?>">
                            <thead>
                                <tr>
                                    <th data-field="name_en" data-sortable="true">
                                        <?= $traducciones['name_en'] ?? 'Name (EN)' ?>
                                    </th>
                                    <th data-field="name_es" data-sortable="true">
                                        <?= $traducciones['name_es'] ?? 'Name (ES)' ?>
                                    </th>
                                    <th data-field="id" data-align="center" data-formatter="specialtyActionFormatter">
                                        <?= $traducciones['actions'] ?? 'Action' ?>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="specialtyModal" tabindex="-1" aria-labelledby="specialtyModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="specialtyForm">
                            <div class="modal-header border-bottom-0">
                                <h5 class="modal-title" id="modal-label">
                                    <?= $traducciones['specialty_modal_title'] ?? 'Specialty' ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label"><?= $traducciones['name_en'] ?? 'Name (EN)' ?></label>
                                    <input type="text" id="name_en" name="name_en" class="form-control panels-input">
                                </div>
                                <div class="mb-3">
                                    <label for="name_es"
                                        class="form-label"><?= $traducciones['name_es'] ?? 'Name (ES)' ?></label>
                                    <input type="text" id="name_es" name="name_es" class="form-control panels-input">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-save text-white" id="specialty-save">
                                    <i class="mdi mdi-content-save-outline"></i>
                                    <?= $traducciones['save'] ?? 'Save' ?>
                                </button>
                                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                                    <i class="mdi mdi-cancel"></i>
                                    <?= $traducciones['cancelButtonText_helper'] ?? 'Cancel' ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- View Specialty Modal -->
            <div class="modal fade" id="viewSpecialtyModal" tabindex="-1" aria-labelledby="viewSpecialtyModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header border-bottom-0">
                            <h5 class="modal-title" id="viewSpecialtyModalLabel">
                                <?= $traducciones['view_specialty_modal_title'] ?? 'Specialty Details' ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <strong><?= $traducciones['name_en'] ?? 'Name (EN)' ?>:</strong>
                                <div id="view_name_en"></div>
                            </div>
                            <div class="mb-3">
                                <strong><?= $traducciones['name_es'] ?? 'Name (ES)' ?>:</strong>
                                <div id="view_name_es"></div>
                            </div>
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


<script>
    document.getElementById('btnExportCSV').addEventListener('click', function () {
        Swal.fire({
            title: language.exportLoadingTitle,
            text: language.exportLoadingText,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        fetch('specialties/export/<?php echo $_SESSION['user_id'] ?>')
            .then(async response => {
                const contentType = response.headers.get("Content-Type");
                if (contentType && contentType.includes("text/csv")) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `${language.csvFilenamePrefix_speciality}.csv`;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                    Swal.close();
                } else {
                    const res = await response.json();
                    Swal.fire({ icon: 'info', title: language.noRecordsTitle, text: language.noRecordsText });
                }
            }).catch(error => {
                Swal.fire({ icon: 'error', title: language.exportErrorTitle, text: language.exportErrorText });
            });
    });
</script>

<script type="module">
    import {
        createSpecialty,
        getAllSpecialties,
        getSpecialtyById,
        updateSpecialty,
        deleteSpecialty
    } from './public/assets/js/apiConfig.js'
    import {
        clearValidationMessages,
        validateFormFields
    } from './public/assets/js/helpers/helpers.js'

    const d = document
    const fieldList = { specialty_id: '', name_en: '', name_es: '' }
    const modal = new bootstrap.Modal(d.getElementById('specialtyModal'))
    const form = d.getElementById('specialtyForm')
    const modalLabel = d.getElementById('modal-label')

    const traducciones = <?= json_encode($traducciones) ?>;
    const language = traducciones;

    loadSpecialtyList()
    d.getElementById('toolbar').classList.remove('d-none')
    form.addEventListener('submit', e => e.preventDefault())
    d.addEventListener('click', handleClick)

    $('#specialtyTable').on('refresh.bs.table', loadSpecialtyList)

    window.specialtyActionFormatter = (value, row) => {
        const editTitle = language['edit_specialty'] || 'Editar Especialidad';
        const deleteTitle = language['delete_specialty'] || 'Eliminar Especialidad';
        return `
        <div class="btn-group d-inline-flex" role="group">
            <button class="btn btn-view action-icon viewBtn p-1" data-id="${row.specialty_id}" title="View Specialty">
                <i class="mdi mdi-eye-outline"></i>
            </button>
            <button class="btn btn-pencil action-icon editBtn p-1" data-id="${row.specialty_id}" title="${editTitle}">
                <i class="mdi mdi-pencil-outline"></i>
            </button>
            <button class="btn btn-delete action-icon deleteBtn p-1" data-id="${row.specialty_id}" title="${deleteTitle}">
                <i class="mdi mdi-delete-outline"></i>
            </button>
        </div>`;
    };

    async function loadSpecialtyList() {
        try {
            const data = await getAllSpecialties()
            $('#specialtyTable').bootstrapTable('load', data.data || [])
        } catch (e) {
            console.error(e)
        }
    }

    async function handleClick(e) {
        if (e.target.closest('#addSpecialtyBtn')) {
            clearValidationMessages(form)
            form.reset()
            fieldList.specialty_id = ''
            modalLabel.textContent = language.add_new_specialty || 'Agregar Especialidad'
            modal.show()
        }

        if (e.target.closest('.viewBtn')) {
            const id = e.target.closest('.viewBtn').dataset.id
            const res = await getSpecialtyById(id)
            if (!res.value) return

            d.getElementById('view_name_en').textContent = res.data.name_en
            d.getElementById('view_name_es').textContent = res.data.name_es

            const viewModal = new bootstrap.Modal(d.getElementById('viewSpecialtyModal'))
            viewModal.show()
        }

        if (e.target.closest('.editBtn')) {
            const id = e.target.closest('.editBtn').dataset.id
            const res = await getSpecialtyById(id)
            if (!res.value) return

            fieldList.specialty_id = res.data.specialty_id
            d.getElementById('name_en').value = res.data.name_en
            d.getElementById('name_es').value = res.data.name_es
            modalLabel.textContent = language.edit_specialty || 'Editar Especialidad'
            modal.show()
        }

        if (e.target.closest('.deleteBtn')) {
            const id = e.target.closest('.deleteBtn').dataset.id

            Swal.fire({
                title: language.delete_confirm_title_specialty || '¿Confirmar eliminación?',
                text: language.delete_confirm_text_specialty || 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: language.delete_confirm_btn_specialty || 'Eliminar',
                cancelButtonText: language.cancel || 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const res = await deleteSpecialty(id);
                        if (res.value) {
                            Swal.fire({
                                icon: 'success',
                                title: language.titleSuccess_specialty || 'Especialidad eliminada',
                                text: language.success_delete_specialty || 'La especialidad fue eliminada correctamente.'
                            });
                            loadSpecialtyList();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: language.titleError_specialty || 'Error al eliminar',
                                text: res.message || language.error_delete_specialty || 'No se pudo eliminar la especialidad.'
                            });
                        }
                    } catch (err) {
                        let errorMessage = language.error_delete_specialty || 'No se pudo eliminar la especialidad.';
                        if (err?.response?.json) {
                            const errorData = await err.response.json();
                            if (errorData?.message) {
                                errorMessage = errorData.message;
                            }
                        }
                        Swal.fire({
                            icon: 'error',
                            title: language.titleError_specialty || 'Error al eliminar',
                            text: errorMessage
                        });
                    }
                }
            });
        }

        if (e.target.closest('#specialty-save')) {
            if (!validateFormFields(form, ['name_en', 'name_es'], language.input_generic_error)) return

            const payload = {
                name_en: d.getElementById('name_en').value,
                name_es: d.getElementById('name_es').value
            }

            if (fieldList.specialty_id) {
                const res = await updateSpecialty(fieldList.specialty_id, payload)
                if (res.value) {
                    modal.hide()
                    Swal.fire(language.titleSuccess_specialty, language.success_update_specialty, 'success');
                    loadSpecialtyList()
                }
            } else {
                const res = await createSpecialty(payload)
                if (res.value) {
                    modal.hide()
                    Swal.fire(language.titleSuccess_specialty, language.success_create_specialty, 'success');
                    loadSpecialtyList()
                }
            }
        }
    }
</script>


</body>

</html>