<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layouts/head.php'; ?>
</head>

<body>
    <?php include 'layouts/header.php'; ?>
    <?php include 'layouts/sidebar.php';

    $idioma = strtoupper($_SESSION['idioma'] ?? 'ES');
    if (!in_array($idioma, ['EN', 'ES']))
        $idioma = 'EN';
    $archivo_idioma = PROJECT_ROOT . '/lang/' . $idioma . '.php';
    $traducciones = file_exists($archivo_idioma) ? include $archivo_idioma : [];

    $user_id = $_GET['user_id'] ?? null;
    $panel_id = $_GET['panel_id'] ?? null;

    ?>

    <div id="wrapper">
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <div class="card-body">
                        <div class="card">
                            <div class="card-body">


                                <table id="panelRecordsTable" class="table-borderless" data-toggle="table"
                                    data-page-list="[5, 10, 20]" data-pagination="true" data-search="true"
                                    data-show-refresh="true" data-show-columns="true" data-page-size="10"
                                    data-locale="<?= $idioma === 'ES' ? 'es-ES' : 'en-US' ?>">
                                    <thead>
                                        <tr>
                                            <th data-field="date_column" data-sortable="true">
                                                <?= $traducciones['record_date'] ?? 'Record Date' ?>
                                            </th>
                                            <th data-field="id" data-align="center"
                                                data-formatter="recordActionFormatter">
                                                <?= $traducciones['actions'] ?? 'Action' ?>
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de Visualización -->
                    <div class="modal fade" id="viewRecordModal" tabindex="-1" aria-labelledby="viewRecordModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header border-bottom-0">
                                    <h5 class="modal-title" id="viewRecordModalLabel">
                                        <?= $traducciones['view_details'] ?? 'Record Details' ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body" id="record-details-body">
                                    <!-- Campos se llenan dinámicamente -->
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

    <div class="rightbar-overlay"></div>
    <?php include 'layouts/footer.php'; ?>
    <script src="public/assets/js/logout.js"></script>
    <script>

        const traducciones = <?= json_encode($traducciones, JSON_UNESCAPED_UNICODE) ?>;
        const userId = <?= $user_id ?>;
        const panelId = <?= $panel_id ?>;

    </script>

    <script>
        const viewModal = new bootstrap.Modal(document.getElementById('viewRecordModal'));

        window.recordActionFormatter = (value, row) => {
            return `
                <button class="btn btn-view viewBtn p-1" data-id="${row.id}">
                    <i class="mdi mdi-eye-outline"></i>
                </button>`;
        };

        $(document).on('click', '.viewBtn', function () {
            const id = $(this).data('id');
            const url = `test-panels/${userId}/${panelId}`;
            fetch(url)
                .then(res => res.json())
                .then(json => {
                    if (!json.value) return;

                    const record = json.data.find(item => item.id == id);
                    const body = document.getElementById('record-details-body');
                    body.innerHTML = '';

                    for (const key in record) {
                        const val = record[key];
                        body.innerHTML += `
                            <div class="mb-2">
                                <strong>${key.replace(/_/g, ' ')}:</strong>
                                <div>${val ?? '-'}</div>
                            </div>`;
                    }

                    viewModal.show();
                });
        });

        async function loadRecords() {
            const url = `test-panels/${userId}/${panelId}`;
            try {
                const res = await fetch(url);
                const json = await res.json();
                if (!json.value || !Array.isArray(json.data)) return;

                const tableData = json.data.map(row => {
                    const dateKey = Object.keys(row).find(k => k.includes('_date')) || 'created_at';
                    return { ...row, date_column: row[dateKey] ?? '-' };
                });

                $('#panelRecordsTable').bootstrapTable('load', tableData);
            } catch (e) {
                console.error('Error loading records:', e);
            }
        }

        loadRecords();
    </script>
</body>

</html>