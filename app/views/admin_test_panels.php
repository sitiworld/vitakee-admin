
    <div id="wrapper">

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <div class="card-body">
                        <!-- id="panelsTable" data-toggle="table" data-search="true" data-show-refresh="true"
                            data-page-list="[5, 10, 20]" data-page-size="5" data-show-columns="true"
                            data-show-pagination-switch="true" data-pagination="false" data-detail-view="false"
                            class="table-borderless -->
                        <h4 class="page-title"><?= $traducciones['test_panels_title'] ?? 'Panels' ?></h4>

                        <div id="toolbar" class="d-none">
                            <button id="addPanelBtn" class="btn btn-add">
                                <i class="bi bi-plus"></i> <?= $traducciones['add_panel_button'] ?>
                            </button>
                            <button id="btnExportCSV" class="btn btn-action-lipid">
                                <span class="mdi mdi-file-export-outline"></i> <?= $traducciones['export_csv_button'] ?>
                            </button>
                        </div>

                        <div class="card">
                            <div class="card-body">


                                <table id="panelsTable" class="table-borderless" data-page-list="[5, 10, 20]"
                                    data-toggle="table" data-show-pagination-switch="true" data-pagination="true"
                                    data-search="true" data-show-refresh="true" data-show-columns="true"
                                    data-pagination-switch="false" data-page-size="5" data-url="test-panels"
                                    data-toolbar="#toolbar" data-locale="<?= $locale ?>">
                                    <thead class="">

                                        <tr>
                                            <th data-field="panel_name" data-sortable="true">
                                                <?= $traducciones['panels_table_column_panel_name'] ?>
                                            </th>
                                            <th data-field="display_name" data-sortable="true">
                                                <?= $traducciones['panels_table_column_display_name'] ?>
                                            </th>
                                            <th data-field="id" data-align="center"
                                                data-formatter="panelActionFormatter">
                                                <?= $traducciones['actions'] ?>
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>


                        </div>
                    </div>

                    <!-- Add / Edit Panel Modal -->
                    <div class="modal fade" id="panelModal" tabindex="-1" aria-labelledby="panelModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="panelForm">
                                    <div class="modal-header border-bottom-0">
                                        <h5 class="modal-title" id="modal-label">
                                            <?= $traducciones['panel_modal_title'] ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="panel_name"
                                                class="form-label"><?= $traducciones['panel_modal_label_panel_name'] ?></label>
                                            <input type="text" id="panel_name" name="panel_name"
                                                class="form-control panels-input">
                                        </div>
                                        <div class="mb-3">
                                            <label for="display_name"
                                                class="form-label"><?= $traducciones['panel_modal_label_display_name'] ?></label>
                                            <input type="text" id="display_name" name="display_name"
                                                class="form-control panels-input">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-save text-white" id="panel-save"><i
                                                class="mdi mdi-content-save-outline"></i>
                                            <?= $traducciones['save'] ?></button>
                                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal"><i
                                                class=" mdi mdi-cancel"></i> <?= $traducciones['cancel'] ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- View Panel Modal -->
                    <div class="modal fade" id="viewPanelModal" tabindex="-1" aria-labelledby="viewPanelModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header border-bottom-0">
                                    <h5 class="modal-title" id="viewPanelModalLabel">
                                        <?= $traducciones['panel_view_modal_title'] ?? 'Panel Details' ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <strong><?= $traducciones['panel_modal_label_panel_name'] ?>:</strong>
                                        <div id="view_panel_name"></div>
                                    </div>
                                    <div class="mb-3">
                                        <strong><?= $traducciones['panel_modal_label_display_name'] ?>:</strong>
                                        <div id="view_display_name"></div>
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


    </div>

    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    <!-- JQuery primero -->
    <script src="public/assets/js/logout.js"></script>

    <script>
        document.getElementById('btnExportCSV').addEventListener('click', function () {
            Swal.fire({
                title: mensajes[idioma].exportLoadingTitle,
                text: mensajes[idioma].exportLoadingText,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('test-panels/export/<?php echo $_SESSION['user_id'] ?>')
                .then(async response => {
                    const contentType = response.headers.get("Content-Type");

                    if (contentType && contentType.includes("text/csv")) {
                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);
                        const filename = `${mensajes[idioma].csvFilenamePrefix}.csv`;

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
                            title: mensajes[idioma].noRecordsTitle,
                            text: mensajes[idioma].noRecordsText
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: mensajes[idioma].exportErrorTitle,
                        text: mensajes[idioma].exportErrorText
                    });
                });
        });
    </script>

    <script type="module">
        import {
            createTestPanel,
            getAllTestPanels,
            getTestPanelById,
            updateTestPanel,
        } from './public/assets/js/apiConfig.js'
        import {
            clearValidationMessages,
            showConfirmation,
            validateFormFields,
        } from './public/assets/js/helpers/helpers.js'

        const d = document

        export function validateTestPanel() {
            const fieldList = { display_name: '', panel_name: '', panel_id: '' }
            const table = d.getElementById('panelsTable')
            const modal = new bootstrap.Modal(d.getElementById('panelModal'))
            const panelForm = d.getElementById('panelForm')
            const modalLabel = d.getElementById('modal-label')

            loadPanelsList()
            $('#panelsTable').on('refresh.bs.table', loadPanelsList)

            window.panelActionFormatter = function (value, row) {
                return `
        <button class="btn btn-view action-icon btn-sm me-1 viewPanelBtn" data-id="${row.panel_id}">
            <i class="mdi mdi-eye-outline"></i>
        </button>
        <button class="btn btn-pencil action-icon btn-sm editPanelBtn" data-id="${row.panel_id}">
            <i class="mdi mdi-pencil-outline"></i>
        </button>`
            }

            d.addEventListener('click', handleButtonClicks)
            panelForm.addEventListener('submit', (e) => e.preventDefault())

            async function loadPanelsList() {
                try {
                    const data = await getAllTestPanels()
                    $('#panelsTable').bootstrapTable('load', data.data || [])
                } catch (err) {
                    console.error('Error loading panels:', err)
                    alert('Could not load panels.')
                }
            }

            d.getElementById('toolbar').classList.remove('d-none')

            async function handleButtonClicks(e) {
                if (e.target.id === 'addPanelBtn') {
                    clearValidationMessages(panelForm)
                    panelForm.reset()
                    fieldList.panel_id = ''
                    modalLabel.textContent = mensajes[idioma].addPanel;
                    modal.show()
                }

                if (e.target.id === 'exportPanelsCsvBtn') {
                    window.location.href = 'test-panels/export/<?php echo $_SESSION['user_id'] ?>'
                }

                if (e.target.closest('.viewPanelBtn')) {
                    const panelId = e.target.closest('.viewPanelBtn').dataset.id
                    await openPanelModalForView(panelId)
                }

                if (e.target.closest('.editPanelBtn')) {
                    const panelId = e.target.closest('.editPanelBtn').dataset.id
                    await openPanelModalForEdit(panelId)
                }

                if (e.target.id === 'panel-save') {
                    await savePanelData()
                }
            }

            async function openPanelModalForEdit(panelId) {
                clearValidationMessages(panelForm)
                const res = await getTestPanelById(panelId)
                if (!res.value) return

                const data = res.data
                fieldList.panel_id = data.panel_id
                fieldList.display_name = data.display_name
                fieldList.panel_name = data.panel_name

                d.getElementById('panel_name').value = data.panel_name
                d.getElementById('display_name').value = data.display_name
                modalLabel.textContent = mensajes[idioma].editPanel;
                modal.show()
            }

            async function openPanelModalForView(panelId) {
                const res = await getTestPanelById(panelId);
                if (!res.value) return;

                const data = res.data;
                d.getElementById('view_panel_name').textContent = data.panel_name;
                d.getElementById('view_display_name').textContent = data.display_name;

                const viewModal = new bootstrap.Modal(d.getElementById('viewPanelModal'));
                viewModal.show();
            }

            async function savePanelData() {
                let validate = validateFormFields(panelForm, ['panel_name', 'display_name'], '<?= $traducciones['input_generic_error'] ?>')
                if (!validate) return

                fieldList.panel_name = d.getElementById('panel_name').value
                fieldList.display_name = d.getElementById('display_name').value

                if (fieldList.panel_id) {
                    const res = await updateTestPanel(fieldList.panel_id, fieldList)
                    if (res.value) {
                        loadPanelsList()
                        modal.hide()
                    }
                } else {
                    showConfirmation({
                        type: 'create',
                        actionCallback: async () => {
                            const res = await createTestPanel(fieldList)
                            if (res.value) {
                                loadPanelsList()
                                modal.hide()
                            }
                        }
                    })
                }
            }
        }

        validateTestPanel()
    </script>


</body>

</html>