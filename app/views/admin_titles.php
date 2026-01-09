<div class="container-fluid">
    <div class="card-body">
        <h4 class="page-title"><?= $traducciones['specialty_title_modal_title'] ?? 'Specialty Title' ?>
        </h4>
        <div id="toolbar" class="d-none">
            <button id="addTitleBtn" class="btn btn-add">
                <i class="bi bi-plus"></i>
                <?= $traducciones['add_specialty_title_button'] ?? 'Add Title' ?>
            </button>
            <button id="btnExportCSV" class="btn btn-action-lipid">
                <span class="mdi mdi-file-export-outline"></span>
                <?= $traducciones['export_csv_button'] ?? 'Export CSV' ?>
            </button>
        </div>

        <div class="card">
            <div class="card-body">


                <table id="titleTable" class="table-borderless" data-toggle="table" data-page-list="[5, 10, 20]"
                    data-show-pagination-switch="true" data-pagination="true" data-search="true"
                    data-show-refresh="true" data-show-columns="true" data-page-size="5" data-url="titles"
                    data-toolbar="#toolbar" data-locale="<?= $locale ?>">
                    <thead>
                        <tr>
                            <th data-field="name_en" data-sortable="true">
                                <?= $traducciones['name_en'] ?? 'Name (EN)' ?>
                            </th>
                            <th data-field="name_es" data-sortable="true">
                                <?= $traducciones['name_es'] ?? 'Name (ES)' ?>
                            </th>
                            <th data-field="id" data-align="center" data-formatter="titleActionFormatter">
                                <?= $traducciones['actions'] ?? 'Action' ?>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="titleModal" tabindex="-1" aria-labelledby="titleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="titleForm">
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title" id="modal-label">
                            <?= $traducciones['title_modal_title'] ?? 'Title' ?>
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
                        <button type="submit" class="btn btn-save text-white" id="title-save">
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
    <!-- View Title Modal -->
    <div class="modal fade" id="viewTitleModal" tabindex="-1" aria-labelledby="viewTitleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="viewTitleModalLabel">
                        <?= $traducciones['view_title_modal_title'] ?? 'Title Details' ?>
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


<script src="public/assets/js/logout.js"></script>


<script>
    document.getElementById('btnExportCSV').addEventListener('click', function () {
        Swal.fire({
            title: language.exportLoadingTitle,
            text: language.exportLoadingText,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        fetch('titles/export/<?php echo $_SESSION['user_id'] ?>')
            .then(async response => {
                const contentType = response.headers.get("Content-Type");
                if (contentType && contentType.includes("text/csv")) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `${language.csvFilenamePrefix_speciality_title}.csv`;
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
        createTitle,
        getAllTitles,
        getTitleById,
        updateTitle,
        deleteTitle
    } from './public/assets/js/apiConfig.js'
    import {
        clearValidationMessages,
        validateFormFields
    } from './public/assets/js/helpers/helpers.js'

    const d = document
    const fieldList = { title_id: '', name_en: '', name_es: '' }
    const modal = new bootstrap.Modal(d.getElementById('titleModal'))
    const form = d.getElementById('titleForm')
    const modalLabel = d.getElementById('modal-label')

    const traducciones = <?= json_encode($traducciones) ?>;
    const language = traducciones;

    loadTitleList()
    d.getElementById('toolbar').classList.remove('d-none')
    form.addEventListener('submit', e => e.preventDefault())
    d.addEventListener('click', handleClick)

    $('#titleTable').on('refresh.bs.table', loadTitleList)

    window.titleActionFormatter = (value, row) => {
        const editTitle = language['edit_title'] || 'Edit Title';
        const deleteTitle = language['delete_title'] || 'Delete Title';
        return `
        <div class="btn-group d-inline-flex" role="group">
            <button class="btn btn-view action-icon viewBtn p-1" data-id="${row.title_id}" title="View Title">
                <i class="mdi mdi-eye-outline"></i>
            </button>
            <button class="btn btn-pencil action-icon editBtn p-1" data-id="${row.title_id}" title="${editTitle}">
                <i class="mdi mdi-pencil-outline"></i>
            </button>
            <button class="btn btn-delete action-icon deleteBtn p-1" data-id="${row.title_id}" title="${deleteTitle}">
                <i class="mdi mdi-delete-outline"></i>
            </button>
        </div>`;
    };

    async function loadTitleList() {
        try {
            const data = await getAllTitles()
            $('#titleTable').bootstrapTable('load', data.data || [])
        } catch (e) {
            console.error(e)
        }
    }

    async function handleClick(e) {
        if (e.target.closest('#addTitleBtn')) {
            clearValidationMessages(form)
            form.reset()
            fieldList.title_id = ''
            modalLabel.textContent = language.add_new_title || 'Add New Title'
            modal.show()
        }

        if (e.target.closest('.viewBtn')) {
            const id = e.target.closest('.viewBtn').dataset.id
            const res = await getTitleById(id)
            if (!res.value) return

            d.getElementById('view_name_en').textContent = res.data.name_en
            d.getElementById('view_name_es').textContent = res.data.name_es

            const viewModal = new bootstrap.Modal(d.getElementById('viewTitleModal'))
            viewModal.show()
        }

        if (e.target.closest('.editBtn')) {
            const id = e.target.closest('.editBtn').dataset.id
            const res = await getTitleById(id)
            if (!res.value) return

            fieldList.title_id = res.data.title_id
            d.getElementById('name_en').value = res.data.name_en
            d.getElementById('name_es').value = res.data.name_es
            modalLabel.textContent = language.edit_title || 'Edit Title'
            modal.show()
        }

        if (e.target.closest('.deleteBtn')) {
            const id = e.target.closest('.deleteBtn').dataset.id

            Swal.fire({
                title: language.delete_confirm_title_title || '¿Confirmar eliminación?',
                text: language.delete_confirm_text_title || 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: language.delete_confirm_btn_title || 'Eliminar',
                cancelButtonText: language.cancel || 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const res = await deleteTitle(id);
                        if (res.value) {
                            Swal.fire({
                                icon: 'success',
                                title: language.titleSuccess_title || 'Título eliminado',
                                text: language.success_delete_title || 'El título fue eliminado correctamente.'
                            });
                            loadTitleList();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: language.titleError_title || 'Error al eliminar',
                                text: res.message || language.error_delete_title || 'No se pudo eliminar el título.'
                            });
                        }
                    } catch (err) {
                        let errorMessage = language.error_delete_title || 'No se pudo eliminar el título.';
                        if (err?.response?.json) {
                            const errorData = await err.response.json();
                            if (errorData?.message) {
                                errorMessage = errorData.message;
                            }
                        }
                        Swal.fire({
                            icon: 'error',
                            title: language.titleError_title || 'Error al eliminar',
                            text: errorMessage
                        });
                    }
                }
            });
        }

        if (e.target.closest('#title-save')) {
            if (!validateFormFields(form, ['name_en', 'name_es'], language.input_generic_error)) return

            const payload = {
                name_en: d.getElementById('name_en').value,
                name_es: d.getElementById('name_es').value
            }

            if (fieldList.title_id) {
                const res = await updateTitle(fieldList.title_id, payload)
                if (res.value) {
                    modal.hide()
                    Swal.fire(language.titleSuccess_title, language.success_update_title, 'success');
                    loadTitleList()
                }
            } else {
                const res = await createTitle(payload)
                if (res.value) {
                    modal.hide()
                    Swal.fire(language.titleSuccess_title, language.success_create_title, 'success');
                    loadTitleList()
                }
            }
        }
    }
</script>