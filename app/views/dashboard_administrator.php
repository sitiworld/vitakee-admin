<!-- Start Content-->
<div class="container-fluid" id="dashboard-view">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box mb-2">
                                <h4 class="page-title" style="line-height: 2.3;"><?= $traducciones['dashboard_title'] ?>
                                </h4>

                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <span id="user-greeting" class="fw-bold"></span>
                                    <div class="d-block">
                                        <form class="d-flex align-items-center">
                                            <div class="input-group input-group-sm">

                                                <input type="text" class="form-control border" id="dash-daterange">
                                                <span class="input-group-text btn btn-view-deprecated">
                                                    <i class="mdi mdi-calendar-range"></i>
                                                </span>
                                            </div>
                                            <button class="btn btn-view-deprecated btn-sm ms-1" data-reset="">
                                                <i class="mdi mdi-autorenew"></i>
                                            </button>

                                        </form>
                                    </div>
                                </div>



                            </div>
                        </div>
                    </div>
                    <!-- end page title -->
                    <!-- Dashboard Cards -->



                    <div class="row">
                        <!-- Card 1: Active Markers -->
                        <div class="col-md-6 col-xl-3">
                            <div class="widget-rounded-circle card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div
                                                class="avatar-lg justify-content-center align-items-center d-flex rounded-circle border-kpi-person bg-white-light">
                                                <span class="mdi mdi-account-outline text-kpi-person"
                                                    style="font-size: 24px;"></span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center flex-column col-6">
                                            <div class="text-end">
                                                <h3 class="mt-1 mb-0"><span id="bm2-out-range">0</span></h3>
                                                <p class="text-muted mb-0 text-truncate" id="bm2-label-out-range">
                                                    <?= $traducciones['dashboard_cards_total_users'] ?>
                                                </p>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: This Month -->
                        <div class="col-md-6 col-xl-3">
                            <div class="widget-rounded-circle card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div
                                                class="avatar-lg justify-content-center align-items-center d-flex rounded-circle bg-white-light border-kpi-calendar">
                                                <span class="mdi mdi-calendar-blank-outline text-kpi-calendar"
                                                    style="font-size: 24px;"></span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center flex-column col-6">
                                            <div class="text-end">
                                                <h3 class="mt-1 mb-0"><span id="bm2-finish">0</span></h3>
                                                <p class="text-muted mb-0 data-range" id="bm2-label-finish">
                                                    <?= $traducciones['dashboard_cards_entries'] ?>

                                                </p>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: In Range % -->
                        <div class="col-md-6 col-xl-3">
                            <div class="widget-rounded-circle card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div
                                                class="avatar-lg justify-content-center align-items-center d-flex rounded-circle bg-white-light border-kpi-view">
                                                <span class="dripicons-bell"
                                                    style="font-size: 24px; color: #3EBBD0;"></span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center flex-column col-6">
                                            <div class="text-end">
                                                <h3 class="mt-1 mb-0"><span id="bm2-this-month">0</span></h3>
                                                <p class="text-muted mb-0 text-truncate" id="bm2-label-this-month">
                                                    <?= $traducciones['dashboard_cards_alerts_active'] ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 4 -->
                        <div class="col-md-6 col-xl-3">
                            <div class="widget-rounded-circle card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div
                                                class="avatar-lg justify-content-center align-items-center d-flex rounded-circle bg-white-light border-kpi-calendar">
                                                <span class="mdi mdi-clipboard-check-multiple-outline text-kpi-calendar"
                                                    style="font-size: 24px;"></span>

                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center flex-column col-6">
                                            <div class="text-end">
                                                <h3 class="mt-1 mb-0"><span id="bm2-in-range">0</span>%</h3>
                                                <p class="text-muted mb-0 text-truncate" id="bm2-label-in-range">
                                                    <?= $traducciones['dashboard_cards_global_in_range'] ?>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>









                    <div class="row">

                        <div class="col-lg-4">
                            <div class="card" id="printable-section">
                                <div class="card-body">


                                    <div class="dropdown float-end" id="health-overview-dropdown">
                                        <a href="#" class="dropdown-toggle arrow-none card-drop"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="javascript:void(0);" class="dropdown-item"
                                                onclick="printBiomarkerReport()"><?= $traducciones['dashboard_health_overview_print_pdf'] ?></a>

                                        </div>
                                    </div>
                                    <div class="float-end d-none d-md-inline-block">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-xs btn-time" data-toggle-donut="day">
                                                <?= $traducciones['dashboard_health_overview_today'] ?>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-time" data-toggle-donut="week">
                                                <?= $traducciones['dashboard_health_overview_weekly'] ?>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-time" data-toggle-donut="month">
                                                <?= $traducciones['dashboard_health_overview_monthly'] ?>
                                            </button>
                                        </div>

                                    </div>

                                    <h4 class="header-title mb-0">
                                        <?= $traducciones['dashboard_health_overview_title_admin'] ?>
                                    </h4>




                                    <div class="widget-chart text-center" dir="ltr">
                                        <div id="donut-chart-admin" class="mt-0" data-colors="#f1556c"></div>
                                    </div>
                                </div>
                            </div> <!-- end card -->
                        </div> <!-- end col-->

                        <div class="col-lg-8">
                            <div class="card pb-2">
                                <div class="row d-flex justify-content-start align-items-center">
                                    <div class="card-body">
                                        <div
                                            class="px-2 d-flex flex-wrap w-100 align-items-center justify-content-between gap-2">
                                            <div class="d-flex w-md-50 flex-wrap align-items-center gap-2">
                                                <h4 class="header-title m-0 w-100 " style="max-width: 200px;">
                                                    <?= $traducciones['dashboard_biomarkers_distribution_title'] ?>
                                                </h4>
                                                <select class="form-select flex-grow-2" name="id_biomarker"
                                                    id="id_biomarker" style="width: 150px;">
                                                    <option value="">
                                                        <?= $traducciones['dashboard_biomarkers_select_biomarker'] ?>
                                                    </option>
                                                </select>
                                                <select class="form-select flex-grow-2" name="id_user" id="id_user"
                                                    style="width: 100px;">
                                                    <option value="">
                                                        <?= $traducciones['dashboard_biomarkers_select_user'] ?>
                                                    </option>
                                                </select>
                                                <select class="form-select flex-grow-1" name="status_range"
                                                    style="width: 80px;" id="status_range">
                                                    <option value="">
                                                        <?= $traducciones['dashboard_biomarkers_select_status'] ?>
                                                    </option>
                                                    <option value="all" selected>
                                                        <?= $traducciones['dashboard_biomarkers_all'] ?>
                                                    </option>
                                                    <option value="in">
                                                        <?= $traducciones['dashboard_biomarkers_in_range'] ?>
                                                    </option>
                                                    <option value="out">
                                                        <?= $traducciones['dashboard_biomarkers_out_range'] ?>
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="btn-group w-md-50 flex-wrap mr-2" style="margin-right: .5rem;
">
                                                <button type="button" class="btn btn-xs btn-time"
                                                    data-toggle-bar="day"><?= $traducciones['dashboard_health_overview_today'] ?></button>
                                                <button type="button" class="btn btn-xs btn-time"
                                                    data-toggle-bar="week"><?= $traducciones['dashboard_health_overview_weekly'] ?></button>
                                                <button type="button" class="btn btn-xs btn-time"
                                                    data-toggle-bar="month"><?= $traducciones['dashboard_health_overview_monthly'] ?></button>
                                            </div>
                                        </div>

                                        <div dir="ltr">
                                            <div id="barlines-chart-admin" class="mt-4" data-colors="#1abc9c,#4a81d4">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                        </div> <!-- end col-->
                    </div>



                    <div class="row">

                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <!-- Dropdown actions -->
                                    <div class="dropdown float-end">
                                        <a href="#" class="dropdown-toggle arrow-none card-drop"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="javascript:void(0);" class="dropdown-item"
                                                onclick="exportToPDF2();"><?= $traducciones['dashboard_top_alerts_export'] ?></a>
                                        </div>
                                    </div>

                                    <!-- Title -->
                                    <h4 class="header-title mb-3">
                                        <?= $traducciones['dashboard_recent_records_title'] ?>
                                    </h4>

                                    <!-- Table administrador registros de todos usuarios -->
                                    <div class="table-responsive">
                                        <table id="allUsersTable" data-toggle="table" data-search="true"
                                            data-show-refresh="true" data-page-list="[5, 10, 20]" data-page-size="5"
                                            data-show-columns="true" data-pagination="true" data-url=""
                                            data-show-pagination-switch="true" class="table-borderless"
                                            data-locale="<?= $locale ?>">
                                            <thead class="">
                                                <tr>
                                                    <th data-field="date" data-sortable="true">
                                                        <?= $traducciones['dashboard_recent_records_date'] ?>
                                                    </th>
                                                    <th data-field="user" data-sortable="true">
                                                        <?= $traducciones['dashboard_recent_records_user'] ?>
                                                    </th>
                                                    <th data-field="biomarker" data-sortable="true">
                                                        <?= $traducciones['dashboard_recent_records_biomarker'] ?>
                                                    </th>
                                                    <th data-field="value" data-sortable="true">
                                                        <?= $traducciones['dashboard_recent_records_value'] ?>
                                                    </th>
                                                    <th data-field="status" data-class="text-center"
                                                        data-formatter="statusFormatter">
                                                        <?= $traducciones['dashboard_recent_records_status'] ?>
                                                    </th>
                                                    <th data-field="actions" data-align="center">
                                                        <?= $traducciones['dashboard_recent_records_actions'] ?>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Aquí van los datos de la tabla -->
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewUserModalLabel">
                                            <?= $traducciones['dashboard_modal_user_details_title'] ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body" id="viewUserModalBody">
                                        <!-- Aquí se cargan los detalles dinámicamente -->
                                    </div>
                                </div>
                            </div>
                        </div>






                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-body">

                                    <!-- Dropdown actions -->
                                    <div class="dropdown float-end">
                                        <a href="#" class="dropdown-toggle arrow-none card-drop"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="javascript:void(0);" class="dropdown-item"
                                                onclick="exportToPDF();"><?= $traducciones['dashboard_export_report'] ?></a>
                                        </div>
                                    </div>

                                    <!-- Title -->
                                    <h4 class="header-title mb-3">
                                        <?= $traducciones['dashboard_top_alerts_title'] ?>
                                    </h4>

                                    <!-- Table Administrador Alertas -->
                                    <div class="table-responsive">
                                        <table id="topUsersTable" data-toggle="table" data-search="true"
                                            data-show-refresh="true" data-page-list="[5, 10, 20]" data-page-size="5"
                                            data-show-columns="true" data-pagination="true"
                                            data-show-pagination-switch="true" class="table-borderless"
                                            data-locale="<?= $locale ?>">
                                            <thead class="">
                                                <tr>
                                                    <th data-field="user" data-sortable="true">
                                                        <?= $traducciones['dashboard_top_alerts_user'] ?>
                                                    </th>
                                                    <th data-field="alerts" data-align="center" data-sortable="true">
                                                        <?= $traducciones['dashboard_top_alerts_alerts'] ?>
                                                    </th>
                                                    <th data-field="latest_marker" data-sortable="true">
                                                        <?= $traducciones['dashboard_top_alerts_latest_marker'] ?>
                                                    </th>
                                                    <th data-field="id" data-align="center"
                                                        data-formatter="actionFormatterTop">
                                                        <?= $traducciones['dashboard_top_alerts_action'] ?>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Aquí van los datos de la tabla -->
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Modal for displaying alert details -->
                        <div class="modal fade" id="alert-details-modal" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <?= $traducciones['dashboard_modal_alert_details_title'] ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body" id="alert-details-modal-body"></div>
                                </div>
                            </div>
                        </div>



                    </div>
                    <!-- end row -->

                </div> <!-- container -->

            </div> <!-- content -->


        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->


    </div>
    </div>
</body>

</html>


<script src="public/assets/js/logout.js"></script>


<script type="text/javascript">

    <?php
    $uid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null';
    $uRole = 0;

    ?>

    // NO TOCAR, USADAS EN DASHBOARD.JS, DASHBOARD-ADMIN.JS
    const userId = '<?php echo $uid; ?>';
    const language2 = <?= isset($_SESSION['idioma']) ? json_encode($_SESSION['idioma']) : json_encode('EN') ?>;

    const userRole = <?php echo $uRole; ?>;

    let myTippy1 = tippy('.data-range',
        {
            content: `mm/dd/yyy`,
            trigger: 'mouseenter',
            placement: 'top',
            theme: 'light-border',
            arrow: true,
            animation: 'shift-away',
            delay: [0, 100],
        }
    )

    let myTippy2 = tippy('.data-range2',
        {
            content: `mm/dd/yyy`,
            trigger: 'mouseenter',
            placement: 'top',
            theme: 'light-border',
            arrow: true,
            animation: 'shift-away',
            delay: [0, 100],
        }
    )






    function formatearFecha(fecha) {
        var partes = fecha.split("-"); // Divide la fecha en año, mes y día
        var mes = partes[1];
        var dia = partes[2];
        var anio = partes[0];
        return mes + "-" + dia + "-" + anio; // Retorna la fecha en el nuevo formato
    }






    document.addEventListener('DOMContentLoaded', function () {
        const greetings = {
            EN: {
                guest: 'Hello, Guest',
                hello: 'Hello',
                age: 'Age'
            },
            ES: {
                guest: 'Hola, Invitado',
                hello: 'Hola',
                age: 'Edad'
            }
        };

        const lang = (language2 === 'ES' || language2 === 'EN') ? language2 : 'EN';

        if (typeof userId === 'undefined' || userId === null) {
            console.warn('userId is not defined');
            document.getElementById('user-greeting').textContent = greetings[lang].guest;
            return;
        }

        fetch(`administrator/session/${userId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {


                if (data.value === true) {
                    const user = data.data;
                    let greeting = `${greetings[lang].hello}, ${user.first_name} ${user.last_name}`;

                    if (userRole === 1 && typeof user.age !== 'undefined') {
                        greeting += ` | ${greetings[lang].age}: ${user.age}`;
                    }

                    document.getElementById('user-greeting').textContent = greeting;
                } else {
                    document.getElementById('user-greeting').textContent = greetings[lang].guest;
                }
            })
            .catch(error => {
                console.error('Error fetching user data:', error);
                document.getElementById('user-greeting').textContent = greetings[lang].guest;
            });
    });


    $(document).ready(function () {
        // Seguridad al sacar PHP

        if (userId === null) {
            console.error('User not logged in');
            return;
        }

        const rowsPerPage = 5;

        // Estados independientes para cada tabla
        let bioData = [], bioPage = 1;
        let revData = [], revPage = 1;

        // Date formatter: mm/dd/yyyy → yyyy-mm-dd
        function formatDateToYMD(dateStr) {
            if (!dateStr) return '';
            const parts = dateStr.split('/');
            if (parts.length === 3) {
                const [month, day, year] = parts;
                return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            }
            return dateStr;
        }






        function getStatusColor3(status) {
            switch (status.toLowerCase()) {
                case 'partial':
                case 'parcial':
                    return 'yellow-item';
                case 'complete':
                case 'completado':
                    return 'green-item';
                default:
                    return 'yellow-item';
            }
        }

        function getStatusIcon(status) {
            switch (status.toLowerCase()) {
                case 'partial':
                case 'parcial':
                    return 'mdi mdi-alert';
                case 'complete':
                case 'completado':
                    return 'mdi mdi-check';
                default:
                    return 'mdi mdi-alert';
            }
        }





        function formatDateForSearch(dateStr) {
            // Convierte "2025-05-08" a "05/08/2025"
            const [year, month, day] = dateStr.split('-');
            return `${month}/${day}/${year}`;
        }






        // All Users Recent Records (simplified: no search, no pagination)
        function renderAllUsersTable() {
            $('#allUsersTable').bootstrapTable('load', allUsersData);
        }
        function statusFormatter(value, row, index) {
            return `<span class="badge ${getStatusColor(value)}">${value}</span>`;
        }


        //tabla alerts user adminisstrador
        // ✅ Función que carga la tabla y activa los botones
        function renderTopUsersTable() {
            const query = $('#top-user-search-input').val()?.toLowerCase() || '';

            const filtered = topUsersData.filter(item =>
                item.user.toLowerCase().includes(query) ||
                String(item.alerts).includes(query) ||
                item.latest_out_marker.toLowerCase().includes(query)
            );

            const mapped = filtered.map(item => ({
                user: item.user,
                alerts: item.alerts,
                latest_marker: item.latest_out_marker,
                id: item.id_user
            }));

            // ✅ Cargar en la tabla correcta
            $('#topUsersTable').bootstrapTable('load', mapped);
        }




        // ✅ Diccionario de traducciones
        const translations = {
            EN: {
                noAlertDetails: '<?= $traducciones['noAlertDetails_dashboard'] ?>',
                biomarker: '<?= $traducciones['biomarker_dashboard'] ?>',
                value: '<?= $traducciones['value_dashboard'] ?>',
                referenceRange: '<?= $traducciones['referenceRange_dashboard'] ?>',
                date: '<?= $traducciones['date_dashboard'] ?>',
                name: '<?= $traducciones['name_dashboard'] ?>',
                biomarker: '<?= $traducciones['biomarker_dashboard'] ?>',
                value: '<?= $traducciones['value_dashboard'] ?>',
                date: '<?= $traducciones['date_dashboard'] ?>',
                status: '<?= $traducciones['status_dashboard'] ?>',
                detailsTitle: '<?= $traducciones['detailsTitle_dashboard'] ?>',
                inRange: '<?= $traducciones['inRange'] ?>',
                outOfRange: '<?= $traducciones['outOfRange'] ?>',
            },
            ES: {
                noAlertDetails: '<?= $traducciones['noAlertDetails_dashboard'] ?>',
                biomarker: '<?= $traducciones['biomarker_dashboard'] ?>',
                value: '<?= $traducciones['value_dashboard'] ?>',
                referenceRange: '<?= $traducciones['referenceRange_dashboard'] ?>',
                date: '<?= $traducciones['date_dashboard'] ?>',
                name: '<?= $traducciones['name_dashboard'] ?>',
                biomarker: '<?= $traducciones['biomarker_dashboard'] ?>',
                value: '<?= $traducciones['value_dashboard'] ?>',
                date: '<?= $traducciones['date_dashboard'] ?>',
                status: '<?= $traducciones['status_dashboard'] ?>',
                detailsTitle: '<?= $traducciones['detailsTitle_dashboard'] ?>',
                inRange: '<?= $traducciones['inRange'] ?>',
                outOfRange: '<?= $traducciones['outOfRange'] ?>',
            }
        };



        const lang = (language2 === 'ES' || language2 === 'EN') ? language2 : 'EN';

        // ✅ Evento delegado para manejar clic en botón de ojo
        $(document).on('click', '.show-details-btn', function () {
            const userId = $(this).data('id');
            const user = topUsersData.find(item => item.id_user === userId);

            if (user && user.alert_details && user.alert_details.length > 0) {
                showAlertDetailsModal(user.alert_details);
            } else {
                alert(translations[lang].noAlertDetails);
            }
        });

        // ✅ Mostrar detalles de alertas en modal
        function showAlertDetailsModal(alertDetails) {
            const $modalBody = $('#alert-details-modal-body').empty();

            alertDetails.forEach(alert => {
                const formattedDate = formatDateForSearch(alert.date);

                const alertHtml = `
        <div class="alert red-item">
            <strong>${translations[lang].biomarker}:</strong> ${alert.biomarker}<br>
            <strong>${translations[lang].value}:</strong> ${alert.value}<br>
            <strong>${translations[lang].referenceRange}:</strong> ${alert.reference_min} - ${alert.reference_max}<br>
            <strong>${translations[lang].date}:</strong> ${formattedDate}
        </div>
        `;
                $modalBody.append(alertHtml);
            });

            $('#alert-details-modal').modal('show');
        }




        function getStatusColor(status) {
            switch (status.toLowerCase()) {
                case 'low':
                case 'bajo':
                case 'high':
                case 'alto':
                    return 'red-item';
                case 'ok':
                case 'normal':
                    return 'green-item';
                default:
                    return 'gray-item';
            }
        }

        //tabla recent record all user adminisstrador
        let currentMinDate = '';
        let currentMaxDate = '';

        function loadAllUsersRecords(minDate = '', maxDate = '') {
            console.log("Cargando registros por usuario...");

            const query = new URLSearchParams();

            if (minDate) query.append('minDate', minDate);
            if (maxDate) query.append('maxDate', maxDate);
            currentMinDate = minDate; // 💾 Guardar
            currentMaxDate = maxDate;

            $.ajax({
                url: `biomarkers/users-status/${userId}?` + query.toString(),
                method: 'GET',
                dataType: 'json',
                success(response) {
                    console.log(response);
                    if (!response || !response.value || !Array.isArray(response.data)) {
                        console.warn('Respuesta inválida:', response);
                        return;
                    }

                    allUsersData = response.data.map((item, index) => {
                        const viewBtn = `
                    <button class="btn btn-view action-icon" data-index="${index}" data-bs-toggle="modal" data-bs-target="#viewUserModal">
                        <i class="mdi mdi-eye-outline"></i>
                    </button>
                `;

                        return {
                            ...item,
                            status: `<span class="badge ${getStatusColor(item.status)}">${item.status}</span>`,
                            actions: viewBtn
                        };
                    });

                    allUsersPage = 1;
                    renderAllUsersTable();

                    // Delegar evento al botón de ojo
                    $('#allUsersTable').on('click', 'button[data-bs-target="#viewUserModal"]', function () {
                        const index = $(this).data('index');
                        const user = allUsersData[index];

                        $('#viewUserModalLabel').text(`${translations[lang].detailsTitle} ${user.biomarker || ''}`);
                        $('#viewUserModalBody').html(`
                    <p><strong>${translations[lang].name}:</strong> ${user.user || ''}</p>
                    <p><strong>${translations[lang].biomarker}:</strong> ${user.biomarker || ''}</p>
                    <p><strong>${translations[lang].value}:</strong> ${user.value || ''}</p>
                    <p><strong>${translations[lang].date}:</strong> ${user.date || ''}</p>
                    <p><strong>${translations[lang].status}:</strong> ${user.status || ''}</p>
                `);
                    });
                },
                error(xhr, status, err) {
                    console.error('Error al cargar registros de usuarios:', err);
                }
            });
        }

        $('#allUsersTable').on('refresh.bs.table', function () {
            loadAllUsersRecords(currentMinDate, currentMaxDate);
        });


        let currentAlertMinDate = '';
        let currentAlertMaxDate = '';
        let currentAlertUserId = userId; // Asegúrate de tener el userId disponible

        function loadTopUsersWithAlerts(minDate = '', maxDate = '') {
            currentAlertMinDate = minDate;
            currentAlertMaxDate = maxDate;

            console.log("Cargando top de usuarios con alertas...");

            const query = new URLSearchParams();
            if (minDate) query.append('minDate', minDate);
            if (maxDate) query.append('maxDate', maxDate);

            const finalUrl = `biomarkers/out-of-range/${currentAlertUserId}?${query.toString()}`;

            // Puedes elegir entre usar AJAX manual (como haces ahora) o directamente Bootstrap Table:
            $.ajax({
                url: finalUrl,
                method: 'GET',
                dataType: 'json',
                success(response) {
                    if (!response || !response.value || !Array.isArray(response.data)) {
                        console.warn('Respuesta inválida:', response);
                        return;
                    }
                    topUsersData = response.data;
                    topUsersPage = 1;
                    renderTopUsersTable();
                },
                error(xhr, status, err) {
                    console.error('Error al cargar alertas de usuarios:', err);
                }
            });

            // Alternativa automática si en algún momento decides usar `data-url` con `bootstrapTable('refresh')`
            // $('#topUsersTable').bootstrapTable('refresh', {
            //     url: finalUrl
            // });
        }

        // Refrescar la tabla al hacer clic en el botón de refresh
        $('#topUsersTable').on('refresh.bs.table', function () {
            loadTopUsersWithAlerts(currentAlertMinDate, currentAlertMaxDate);
        });






        function loadBiomarkerDashboard(minDate = '', maxDate = '') {
            // CARD 1: Active Markers


            $.ajax({
                url: 'users/count/${userId}',
                method: 'GET',
                dataType: 'json',
                success(resp) {
                    console.log('count_users response:', resp);
                    if (!resp.value || !resp.data || typeof resp.data.total === 'undefined') {
                        console.error('Respuesta inválida:', resp);
                        $('#bm2-out-range').text('0');
                    } else {
                        $('#bm2-out-range').text(resp.data.total);
                    }
                },
                error(xhr, status, err) {
                    console.error('AJAX error en count_users:', status, err);
                    $('#bm2-out-range').text('0');
                }
            });

            $.ajax({
                url: 'biomarkers/today-count/1?minDate=' + encodeURIComponent(minDate) + '&maxDate=' + encodeURIComponent(maxDate),
                method: 'GET',
                dataType: 'json',
                success(resp) {
                    console.log('count_users_biomarkers_today_count response:', resp);
                    if (!resp.value || !resp.data || typeof resp.data.total === 'undefined') {
                        console.error('Respuesta inválida:', resp);
                        $('#bm2-finish').text('0');
                    } else {
                        $('#bm2-finish').text(resp.data.total);

                        myTippy1[0].setContent(`${formatearFecha(minDate).replaceAll('-', '/')} - ${formatearFecha(maxDate).replaceAll('-', '/')}`); // Cambia el contenido del tooltip

                        // ENTRIES TODAY

                    }
                },
                error(xhr, status, err) {
                    console.error('AJAX error en count_users_biomarkers_today_count:', status, err);
                    $('#bm2-out-range').text('0');
                }
            });
            $.ajax({
                url: 'biomarkers/out-streak/${userId}?minDate=' + encodeURIComponent(minDate) + '&maxDate=' + encodeURIComponent(maxDate),
                method: 'GET',
                dataType: 'json',
                success(resp) {
                    console.log('count_users_biomarkers_out_streak response:', resp);
                    if (!resp.value || !resp.data || typeof resp.data.total === 'undefined') {
                        console.error('Respuesta inválida:', resp);
                        $('#bm2-this-month').text('0.00');
                    } else {
                        $('#bm2-this-month').text(resp.data.total);
                    }
                },
                error(xhr, status, err) {
                    console.error('AJAX error en count_users_biomarkers_out_streak:', status, err);
                    $('#bm2-this-month').text('0.00');
                }
            });





            $.ajax({
                url: 'biomarkers/in-range-percentage/${userId}?minDate=' + encodeURIComponent(minDate) + '&maxDate=' + encodeURIComponent(maxDate),
                method: 'GET',
                dataType: 'json',
                success(resp) {
                    console.log('get_users_biomarkers_in_range_percentage response:', resp);
                    if (!resp.value || !resp.data || typeof resp.data.percentage === 'undefined') {
                        console.error('Respuesta inválida:', resp);
                        $('#bm2-in-range').text('0.00');
                    } else {
                        $('#bm2-in-range').text(resp.data.percentage.toFixed(2));
                    }
                },
                error(xhr, status, err) {
                    console.error('AJAX error en get_users_biomarkers_in_range_percentage:', status, err);
                    $('#bm2-in-range').text('0.00');
                }
            });







        }


        // Handlers
        $('#search-input').on('input', () => { bioPage = 1; renderBiomarkerTable(); });
        $('#revenue-search-input').on('input', () => { revPage = 1; renderRevenueTable(); });
        $('#top-user-search-input').on('input', renderTopUsersTable);


        // Date range change
        window.onDateRangeChange = (minDate, maxDate) => {
            const formattedMin = formatDateToYMD(minDate);
            const formattedMax = formatDateToYMD(maxDate);
            loadBiomarkerDashboard(formattedMin, formattedMax);


            loadAllUsersRecords(formattedMin, formattedMax);
            loadTopUsersWithAlerts(formattedMin, formattedMax);


        };

        // Initial load
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        const fmt = d => d.toISOString().split('T')[0];
        window.onDateRangeChange(fmt(firstDay), fmt(now));
    });




    function printBiomarkerReport() {
        const printContents = document.getElementById('printable-section').innerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload(); // restaurar scripts y layout
    }




    // ✅ Formatter debe estar en el ámbito global
    function actionFormatterTop(value, row, index) {
        return `
        
        <button class="btn btn-view action-icon show-details-btn" data-id="${value}"><i class="mdi mdi-eye-outline"></i></button>
            
        
    `;
    }

    function actionFormatter(value, row, index) {
        let url = '';
        const panel = row.panel;
        const id = row.record_id;
        const biomarker_search = row.biomarker_key;

        if (panel === '7ff39dd8-01e9-443c-b8e6-0d6b429e63a6') {
            url = `component_energy_metabolism?id=${id}&select=${biomarker_search}`;
        } else if (panel === '81054d57-92c9-4df8-a6dc-51334c1d82c4') {
            url = `component_body_composition?id=${id}&select=${biomarker_search}`;
        } else if (panel === 'e6861593-7327-4f63-9511-11d56f5398dc') {
            url = `component_lipid?id=${id}&select=${biomarker_search}`;
        } else if (panel === '60819af9-0533-472c-9d5a-24a5df5a83f7') {
            url = `component_renal?id=${id}&select=${biomarker_search}`;
        }

        return `
        <a href="${url}" class="">
            <button class="btn btn-view action-icon">
                <i class="mdi mdi-eye-outline"></i>
            </button>
        </a>
    `;
    }



</script>

<script>
    // Función para exportar la tabla a PDF
    function exportToPDF2() {
        const { jsPDF } = window.jspdf;  // Inicializar jsPDF
        const doc = new jsPDF();  // Crear un nuevo documento PDF

        // Obtener la tabla y sus contenidos
        const table = document.getElementById('allUsersTable');
        const rows = table.querySelectorAll('tr');

        // Definir las variables para la posición inicial en el PDF
        let yPosition = 10;

        // Añadir título al PDF
        doc.setFontSize(16);
        doc.text("Reporte de Registros Recientes", 20, yPosition);
        yPosition += 10; // Espacio entre el título y la tabla

        // Añadir encabezado de la tabla al PDF
        const headerColumns = ['Fecha', 'Usuario', 'Biomarcador', 'Valor', 'Estado', 'Acciones']; // Encabezados de la tabla
        doc.setFontSize(12);
        doc.text(headerColumns.join(' | '), 20, yPosition);
        yPosition += 10; // Ajustar el espacio entre el encabezado y las filas

        // Iterar sobre las filas de la tabla y agregar cada una al PDF
        rows.forEach((row, index) => {
            // Se omite la fila de encabezado (índice 0)
            if (index === 0) return;

            const columns = row.querySelectorAll('td');
            let rowData = [];
            columns.forEach(column => {
                rowData.push(column.innerText);
            });

            // Establecer los valores de la tabla en el PDF
            doc.setFontSize(10);
            doc.text(rowData.join(' | '), 20, yPosition);
            yPosition += 10; // Ajustar el espacio entre filas

            // Verificar si el documento está lleno, y si es necesario, crear una nueva página
            if (yPosition > 270) {
                doc.addPage();
                yPosition = 10;  // Reseteamos la posición Y
            }
        });

        // Descargar el PDF
        doc.save('reporte_registros_recientes.pdf');
    }
</script>

<script>

    // Función para exportar la tabla a PDF
    function exportToPDF() {
        const { jsPDF } = window.jspdf;  // Inicializar jsPDF
        const doc = new jsPDF();  // Crear un nuevo documento PDF

        // Obtener la tabla y sus contenidos
        const table = document.getElementById('revenueTable');
        const rows = table.querySelectorAll('tr');

        // Definir las variables para la posición inicial en el PDF
        let yPosition = 10;

        // Añadir título al PDF
        doc.setFontSize(16);
        doc.text("Reporte de Alertas", 20, yPosition);
        yPosition += 10; // Espacio entre el título y la tabla

        // Iterar sobre las filas de la tabla y agregar cada una al PDF
        rows.forEach((row, index) => {
            // Se omite la fila de encabezado (índice 0)
            if (index === 0) return;

            const columns = row.querySelectorAll('td');
            let rowData = [];
            columns.forEach(column => {
                rowData.push(column.innerText);
            });

            // Establecer los valores de la tabla en el PDF
            doc.setFontSize(12);
            doc.text(rowData.join(' | '), 20, yPosition);
            yPosition += 10; // Ajustar el espacio entre filas
        });

        // Descargar el PDF
        doc.save('reporte_alertas.pdf');
    }
</script>