<div class="container-fluid">
    <div class="card-body">

        <h4 class="page-title"><?= $traducciones['countries'] ?? 'Countries' ?></h4>
        <div id="toolbar" class="d-none">
            <button id="addCountryBtn" class="btn btn-add">
                <i class="bi bi-plus"></i>
                <?= $traducciones['add_country_button'] ?? 'Add Country' ?>
            </button>
            <button id="btnExportCSV" class="btn btn-action-lipid">
                <span class="mdi mdi-file-export-outline"></span>
                <?= $traducciones['export_csv_button'] ?? 'Export CSV' ?>
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="countryTable" class="table-borderless" data-toggle="table" data-page-list="[5, 10, 20]"
                    data-show-pagination-switch="true" data-pagination="true" data-search="true"
                    data-show-refresh="true" data-show-columns="true" data-page-size="5" data-url="countries"
                    data-toolbar="#toolbar" data-locale="<?= $locale ?>">
                    <thead>
                        <tr>
                            <th data-field="country_name" data-sortable="true">
                                <?= $traducciones['country_name'] ?? 'Country Name' ?>
                            </th>
                            <th data-field="suffix"><?= $traducciones['suffix'] ?? 'Suffix' ?></th>
                            <th data-field="full_prefix">
                                <?= $traducciones['full_prefix'] ?? 'Full Prefix' ?>
                            </th>
                            <th data-field="normalized_prefix">
                                <?= $traducciones['normalized_prefix'] ?? 'Normalized Prefix' ?>
                            </th>
                            <th data-field="phone_mask">
                                <?= $traducciones['phone_mask'] ?? 'Phone Mask' ?>
                            </th>
                            <th data-field="id" data-align="center" data-formatter="countryActionFormatter">
                                <?= $traducciones['actions'] ?? 'Action' ?>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="countryModal" tabindex="-1" aria-labelledby="countryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="countryForm" data-validation="reactive">
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title" id="modal-label">
                            <?= $traducciones['country_modal_title'] ?? 'Country' ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="country_name"
                                class="form-label"><?= $traducciones['country_name'] ?? 'Country Name' ?></label>
                            <input type="text" id="country_name" name="country_name" class="form-control panels-input"
                                data-rules="noVacio|longitudMaxima:100"
                                data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'Country name is required.' ?>"
                                data-message-longitud-maxima="<?= $traducciones['validation_max_length_100'] ?? 'Name cannot exceed 100 characters.' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="suffix" class="form-label"><?= $traducciones['suffix'] ?? 'Suffix' ?></label>
                            <input type="text" id="suffix" name="suffix" class="form-control panels-input"
                                data-rules="noVacio|longitudMaxima:10"
                                data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'Suffix is required.' ?>"
                                data-message-longitud-maxima="<?= $traducciones['validation_max_length_10'] ?? 'Suffix cannot exceed 10 characters.' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="full_prefix"
                                class="form-label"><?= $traducciones['full_prefix'] ?? 'Full Prefix' ?></label>
                            <input type="text" id="full_prefix" name="full_prefix" class="form-control panels-input"
                                data-rules="noVacio|longitudMaxima:10"
                                data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'Full prefix is required.' ?>"
                                data-message-longitud-maxima="<?= $traducciones['validation_max_length_10'] ?? 'Prefix cannot exceed 10 characters.' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="normalized_prefix"
                                class="form-label"><?= $traducciones['normalized_prefix'] ?? 'Normalized Prefix' ?></label>
                            <input type="text" id="normalized_prefix" name="normalized_prefix"
                                class="form-control panels-input" data-rules="noVacio|longitudMaxima:10"
                                data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'Normalized prefix is required.' ?>"
                                data-message-longitud-maxima="<?= $traducciones['validation_max_length_10'] ?? 'Prefix cannot exceed 10 characters.' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="phone_mask"
                                class="form-label"><?= $traducciones['phone_mask'] ?? 'Phone Mask' ?></label>
                            <input type="text" id="phone_mask" name="phone_mask" class="form-control panels-input"
                                data-rules="noVacio|longitudMaxima:50"
                                data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'Phone mask is required.' ?>"
                                data-message-longitud-maxima="<?= $traducciones['validation_max_length_50'] ?? 'Mask cannot exceed 50 characters.' ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-save text-white" id="country-save">
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

    <div class="modal fade" id="viewCountryModal" tabindex="-1" aria-labelledby="viewCountryModalLabel"
        aria-hidden="true">
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

        fetch('countries/export/<?php echo $_SESSION['user_id'] ?>')
            .then(async response => {
                const contentType = response.headers.get("Content-Type");
                if (contentType && contentType.includes("text/csv")) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `${language.csvFilenamePrefix_countries}.csv`;
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
        createCountry,
        getAllCountries,
        getCountryById,
        updateCountry,
        deleteCountry
    } from './public/assets/js/apiConfig.js'
    import {
        clearValidationMessages,
        showConfirmation,
        // validateFormFields // ✅ CAMBIO: Ya no se usa esta función
    } from './public/assets/js/helpers/helpers.js'

    const d = document
    const fieldList = { // Objeto usado para almacenar el ID en edición
        country_id: '',
        // ... (otros campos ya no son necesarios aquí, vendrán de la validación)
    }

    const modal = new bootstrap.Modal(d.getElementById('countryModal'))
    const form = d.getElementById('countryForm')
    const modalLabel = d.getElementById('modal-label')

    loadCountryList()
    d.getElementById('toolbar').classList.remove('d-none')

    // ✅ CAMBIO: El 'submit' ya no se previene. El validador lo manejará.
    // form.addEventListener('submit', e => e.preventDefault()) // <- ELIMINADO

    d.addEventListener('click', handleClick)

    // ✅ CAMBIO: Añadir listener para el evento de validación exitosa
    form.addEventListener('validation:success', handleSaveCountry);

    $('#countryTable').on('refresh.bs.table', loadCountryList)

    // Formatter (Sin cambios)
    window.countryActionFormatter = (value, row) => `
    <button class="btn btn-view action-icon btn-sm viewCountryBtn" data-id="${row.country_id}">
        <i class="mdi mdi-eye-outline"></i>
    </button>
    <button class="btn btn-pencil action-icon btn-sm editCountryBtn" data-id="${row.country_id}">
        <i class="mdi mdi-pencil-outline"></i>
    </button>
    <button class="btn btn-delete action-icon deleteCountryBtn" data-id="${row.country_id}">
        <i class="mdi mdi-delete-outline"></i>
    </button>`;

    // loadCountryList (Sin cambios)
    async function loadCountryList() {
        try {
            const data = await getAllCountries()
            $('#countryTable').bootstrapTable('load', data.data || [])
        } catch (e) {
            console.error(e)
        }
    }

    // ✅ CAMBIO: handleClick se simplifica (se elimina el 'save')
    async function handleClick(e) {
        if (e.target.closest('#addCountryBtn')) {
            clearValidationMessages(form) // Limpia errores del validador
            form.reset()
            fieldList.country_id = '' // Resetea el ID
            modalLabel.textContent = language.add_country_modal_title
            modal.show()
        }

        if (e.target.closest('.viewCountryBtn')) {
            // (Lógica sin cambios)
            const id = e.target.closest('.viewCountryBtn').dataset.id
            const res = await getCountryById(id)
            if (!res.value) return

            d.getElementById('view_country_name').textContent = res.data.country_name
            d.getElementById('view_suffix').textContent = res.data.suffix
            d.getElementById('view_full_prefix').textContent = res.data.full_prefix
            d.getElementById('view_normalized_prefix').textContent = res.data.normalized_prefix
            d.getElementById('view_phone_mask').textContent = res.data.phone_mask

            const viewModal = new bootstrap.Modal(d.getElementById('viewCountryModal'))
            viewModal.show()
        }

        if (e.target.closest('.deleteCountryBtn')) {
            // (Lógica sin cambios)
            const id = e.target.closest('.deleteCountryBtn').dataset.id
            Swal.fire({
                title: language.delete_confirm_title_country || '¿Confirmar eliminación?',
                text: language.delete_confirm_text_country || 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: language.delete_confirm_btn_country || 'Eliminar',
                cancelButtonText: language.cancel || 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const res = await deleteCountry(id);
                        if (res.value) {
                            Swal.fire({
                                icon: 'success',
                                title: language.titleSuccess_country || 'País eliminado',
                                text: language.success_delete_country || 'El país fue eliminado correctamente.'
                            });
                            loadCountryList();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: language.titleError_country || 'Error al eliminar',
                                text: res.message || language.error_delete_country || 'No se pudo eliminar el país.'
                            });
                        }
                    } catch (err) {
                        let errorMessage = language.error_delete_country || 'No se pudo eliminar el país.';
                        if (err?.response?.json) {
                            const errorData = await err.response.json();
                            if (errorData?.message) {
                                errorMessage = errorData.message;
                            }
                        }
                        Swal.fire({
                            icon: 'error',
                            title: language.titleError_country || 'Error al eliminar',
                            text: errorMessage
                        });
                    }
                }
            });
        }

        if (e.target.closest('.editCountryBtn')) {
            clearValidationMessages(form) // ✅ CAMBIO: Limpiar errores al abrir para editar
            const id = e.target.closest('.editCountryBtn').dataset.id
            const res = await getCountryById(id)
            if (!res.value) return

            fieldList.country_id = res.data.country_id // Almacena el ID
            d.getElementById('country_name').value = res.data.country_name
            d.getElementById('suffix').value = res.data.suffix
            d.getElementById('full_prefix').value = res.data.full_prefix
            d.getElementById('normalized_prefix').value = res.data.normalized_prefix
            d.getElementById('phone_mask').value = res.data.phone_mask

            // ✅ CAMBIO: Añadir valor inicial para validaciones de duplicidad (si las hubiera)
            d.getElementById('country_name').setAttribute('data-initial-value', res.data.country_name);

            modalLabel.textContent = language.edit_country_modal_title
            modal.show()
        }

        // ✅ CAMBIO: El bloque 'country-save' se ha movido a la función 'handleSaveCountry'
    }

    // ✅ CAMBIO: Nueva función para manejar el guardado tras la validación
    async function handleSaveCountry(e) {
        const formData = e.detail.datos; // Obtener datos validados
        const countryId = fieldList.country_id; // Obtener ID (si es edición)

        try {
            if (countryId) {
                // Es una ACTUALIZACIÓN
                formData.country_id = countryId; // Añadir el ID al payload
                const res = await updateCountry(countryId, formData);
                if (res.value) {
                    modal.hide();
                    loadCountryList();
                    Swal.fire(language.update_success_title_country || 'Éxito', language.update_success_text_country || 'País actualizado.', 'success');
                }
            } else {
                // Es una CREACIÓN
                showConfirmation({
                    type: 'create',
                    actionCallback: async () => {
                        const res = await createCountry(formData);
                        if (res.value) {
                            modal.hide();
                            loadCountryList();
                            return true; // Indicar éxito a showConfirmation
                        }
                        return false; // Indicar fallo
                    }
                });
            }
        } catch (err) {
            // Manejo de errores genérico para guardado/actualización
            let errorMessage = language.error_save_country || 'No se pudo guardar el país.';
            if (err?.response?.json) {
                const errorData = await err.response.json();
                if (errorData?.message) {
                    errorMessage = errorData.message;
                }
            }
            Swal.fire({
                icon: 'error',
                title: language.titleError_country_save || 'Error al guardar',
                text: errorMessage
            });
        }
    }
</script>

</body>

</html>