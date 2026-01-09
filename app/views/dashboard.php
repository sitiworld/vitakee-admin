<!-- Start Content-->
<div class="container-fluid" id="dashboard-view">

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

    <div class="row">
        <!-- Card 1: Active Markers -->
        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div
                                class="avatar-lg justify-content-center align-items-center d-flex d-flex rounded-circle border-accent-dark bg-white-blue">
                                <span class="mdi mdi-test-tube text-accent-dark" style="font-size: 24px;"></span>
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center col-6">
                            <div class="text-end">
                                <h3 class="mt-1 mb-0"><span id="biomarker-out-range">0</span></h3>
                                <p class="text-muted mb-0" id="label-out-range">
                                    <?= $traducciones['dashboard_cards_active_markers'] ?>
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
                                class="avatar-lg justify-content-center align-items-center d-flex rounded-circle bg-kpi-2 border-primary-dark">
                                <span class="mdi mdi-calendar-blank-outline text-primary-dark"
                                    style="font-size: 24px;"></span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center flex-column col-6">
                            <div class="text-end">
                                <h3 class="mt-1 mb-0"><span id="biomarkers-finish">0</span></h3>
                                <p class="text-muted mb-0 data-range2" id="label-finish">
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
                                class="avatar-lg justify-content-center align-items-center d-flex rounded-circle bg-excess border-excess">
                                <span class="mdi mdi-check text-excess" style="font-size: 24px;"></span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center flex-column col-6">
                            <div class="text-end">
                                <h3 class="mt-1 mb-0"><span id="biomarker-this-month">0</span>%
                                </h3>
                                <p class="text-muted mb-0 text-truncate" id="label-this-month">
                                    <?= $traducciones['inRange'] ?>
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
                                class="avatar-lg justify-content-center align-items-center d-flex rounded-circle bg-deficiency border-deficiency">
                                <span class="ti ti-stats-down text-deficiency" style="font-size: 24px;"></span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center flex-column col-6">
                            <div class="text-end">
                                <h3 class="mt-1 mb-0">
                                    <span id="biomarker-in-range">0</span>%
                                </h3>
                                <p class="text-muted mb-0 text-truncate" id="label-in-range">
                                    <?= $traducciones['outOfRange'] ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- end row-->

    <div class="row">

        <div class="col-lg-4">
            <div class="card" id="printable-section">
                <div class="card-body">
                    <div class="d-flex flex-wrap w-100 align-items-center justify-content-between gap-2">
                        <div class="d-flex w-md-50 align-items-center gap-2">
                            <h4 class="header-title m-0 w-100" style="max-width: 200px;">
                                <?= $traducciones['dashboard_health_overview_title'] ?>
                            </h4>

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
                            <div class="dropdown float-end mt-1">
                                <a href="#" class="dropdown-toggle mt-2 arrow-none card-drop" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="javascript:void(0);" class="dropdown-item"
                                        onclick="printBiomarkerReport()"><?= $traducciones['dashboard_health_overview_print_pdf'] ?></a>

                                </div>
                            </div>
                        </div>


                    </div>







                    <div class="widget-chart text-center" dir="ltr">
                        <div id="donut-chart" class="mt-0" data-colors="#f1556c"></div>
                    </div>
                </div>
            </div> <!-- end card -->
        </div> <!-- end col-->

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body pb-2 row d-flex justify-content-start align-items-center">

                    <div class="row mb-2">
                        <div class="d-flex flex-wrap w-100 align-items-center justify-content-between gap-2">
                            <div class="d-flex w-md-50 align-items-center gap-2">
                                <h4 class="header-title m-0 w-100" style="max-width: 200px;">
                                    <?= $traducciones['dashboard_biomarker_progress_title'] ?>
                                </h4>
                                <select class="form-select" name="id_biomarker" id="id_biomarker" style="width: 180px;">
                                    <option value="">
                                        <?= $traducciones['dashboard_biomarker_select_biomarker'] ?>
                                    </option>
                                </select>

                            </div>

                            <div class="btn-group flex-wrap">
                                <button type="button" class="btn btn-xs btn-time"
                                    data-toggle-bar="day"><?= $traducciones['dashboard_health_overview_today'] ?></button>
                                <button type="button" class="btn btn-xs btn-time"
                                    data-toggle-bar="week"><?= $traducciones['dashboard_health_overview_weekly'] ?></button>
                                <button type="button" class="btn btn-xs btn-time"
                                    data-toggle-bar="month"><?= $traducciones['dashboard_health_overview_monthly'] ?></button>
                            </div>
                        </div>



                    </div>

                    <div class="row col-8">

                        <div class="col-md-6 mb-2 d-flex align-items-center ">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="select-by-date">
                                <label class="form-check-label"
                                    for="select-by-date"><?= $traducciones['dashboard_filter_day'] ?>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="input-group input-group-sm invisible" id="day-datarange-container">
                                <input type="text" class="form-control border" id="day-datarange"
                                    placeholder="Select day">
                            </div>
                        </div>
                    </div>


                    <div class="px-2 d-flex gap-2 d-none">
                        <div class="form-check ">
                            <input type="radio" id="radio-in-range" value="in" name="radio-in-out-range"
                                class="form-check-input" checked>
                            <label class="form-check-label"
                                for="radio-in-range"><?= $traducciones['dashboard_radio_in_range'] ?></label>
                        </div>
                        <div class="form-check">
                            <input type="radio" id="radio-out-range" name="radio-in-out-range" class="form-check-input"
                                value="out">
                            <label class="form-check-label"
                                for="radio-out-range"><?= $traducciones['dashboard_radio_out_range'] ?></label>

                        </div>
                    </div>

                    <div dir="ltr">
                        <div id="barlines-chart" class="mt-4" data-colors="#1abc9c,#4a81d4"></div>
                    </div>
                </div>
            </div> <!-- end card -->


        </div> <!-- end col-->
    </div>



    <div class="row">


        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <!-- Dropdown actions -->
                    <div class="dropdown float-end mt-1">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical "></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item"
                                onclick="exportToPDF4();"><?= $traducciones['dashboard_export_report'] ?></a>
                        </div>
                    </div>

                    <!-- Title -->
                    <h4 class="header-title mb-3">
                        <?= $traducciones['dashboard_recent_records_title_user'] ?>
                    </h4>

                    <!-- Table  usuario recent record -->
                    <div class="table-responsive">
                        <table id="recent-records-table" data-toggle="table" data-search="true" data-show-refresh="true"
                            data-page-list="[5, 10, 20]" data-page-size="5" data-show-columns="true"
                            data-pagination="true" data-show-pagination-switch="true"
                            data-url="biomarkers/resumen/${userId}?${queryParams}"
                            class="table-borderless table-hover table-nowrap table-centered m-0"
                            data-locale="<?= $locale ?>">
                            <thead class="">
                                <tr>
                                    <th data-field="date" data-sortable="true">
                                        <?= $traducciones['dashboard_recent_records_date_user'] ?>
                                    </th>
                                    <th data-field="biomarker" data-sortable="true">
                                        <?= $traducciones['dashboard_recent_records_biomarker_user'] ?>
                                    </th>
                                    <th data-field="value" data-class="text-center" data-sortable="true">
                                        <?= $traducciones['dashboard_recent_records_value_user'] ?>
                                    </th>
                                    <th data-field="status" data-visible="true" data-formatter="statusFormatter">
                                        <?= $traducciones['dashboard_recent_records_status_user'] ?>
                                    </th>
                                    <th data-field="record_id" data-visible="false">Record ID</th>
                                    <th data-field="action" data-formatter="actionFormatter" data-align="center">
                                        <?= $traducciones['dashboard_recent_records_actions_user'] ?>
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






        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <!-- Dropdown actions -->
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item"
                                onclick="exportToPDF3();"><?= $traducciones['dashboard_export_report'] ?></a>
                        </div>
                    </div>

                    <!-- Title -->
                    <h4 class="header-title mb-3">
                        <?= $traducciones['dashboard_test_submission_history_title'] ?>
                    </h4>

                    <!-- Table submission history -->
                    <div class="table-responsive">
                        <table id="revenueTable" data-toggle="table" data-search="true" data-show-refresh="true"
                            data-page-list="[5, 10, 20]" data-page-size="5" data-show-columns="true"
                            data-pagination="true" data-show-pagination-switch="true"
                            data-url="biomarkers/resumen-status/${userId}"
                            class="table-borderless table-hover table-nowrap table-centered m-0"
                            data-locale="<?= $locale ?>">
                            <thead class="">
                                <tr>
                                    <th data-field="nombre" data-sortable="true">
                                        <?= $traducciones['dashboard_test_submission_history_panel'] ?>
                                    </th>
                                    <th data-field="fecha" data-sortable="true">
                                        <?= $traducciones['dashboard_test_submission_history_date'] ?>
                                    </th>
                                    <th data-field="completados" data-align="center" data-sortable="true">
                                        <?= $traducciones['dashboard_test_submission_history_biomarkers_added'] ?>
                                    </th>
                                    <th data-field="status" data-align="center">
                                        <?= $traducciones['dashboard_test_submission_history_status'] ?>
                                    </th>
                                    <th data-field="actions" data-align="center">
                                        <?= $traducciones['dashboard_recent_records_actions_user'] ?>
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








    </div>
    <!-- end row -->

</div> <!-- container -->

<script src="public/assets/js/logout.js"></script>


<script type="text/javascript">

    <?php
    $uid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null';
    $uRole = 1;

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

        fetch(`users/session/${userId}`, {
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



        let currentBiomarkerMinDate = '';
        let currentBiomarkerMaxDate = '';


        function renderBiomarkerTable() {
            $('#recent-records-table').bootstrapTable('load', bioData);
        }

        function loadBiomarkerData(minDate = '', maxDate = '') {
            currentBiomarkerMinDate = minDate;
            currentBiomarkerMaxDate = maxDate;

            console.log("Cargando biomarcadores:", minDate, maxDate);

            const queryParams = new URLSearchParams({ minDate, maxDate }).toString();

            $.ajax({
                url: `biomarkers/resumen/${userId}?${queryParams}`,
                method: 'GET',
                dataType: 'json',
                success(response) {
                    if (!response.value || !Array.isArray(response.data)) {
                        console.warn("Respuesta inesperada:", response);
                        $('#recent-records-table').bootstrapTable('load', []);
                        return;
                    }

                    bioData = response.data.map(item => ({
                        ...item,
                        status: `<span class="badge ${getStatusColor(item.status)}"> ${item.status}</span>`
                    }));

                    bioPage = 1;
                    renderBiomarkerTable();
                },
                error(xhr, status, err) {
                    console.error("AJAX error:", err);
                    $('#recent-records-table').bootstrapTable('load', []);
                }
            });
        }

        $('#recent-records-table').on('refresh.bs.table', function () {
            loadBiomarkerData(currentBiomarkerMinDate, currentBiomarkerMaxDate);
        });





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


        //tabla complete partial usuario

        function renderRevenueTable() {
            const tableData = revData.map((item) => {
                let route = '';

                // Convertimos todo a minúsculas para comparación segura
                const name = item.nombre.toLowerCase();

                if (['body composition', 'composición corporal'].includes(name)) {
                    route = 'body_composition';
                } else if (['lipid profile', 'perfil lipídico'].includes(name)) {
                    route = 'lipid_profile';
                } else if (['renal function', 'funcion renal'].includes(name)) {
                    route = 'renal_function';
                } else if (['energy metabolism', 'energia metabolica'].includes(name)) {
                    route = 'energy_metabolism_view';
                }

                const formattedDate = formatDateForSearch(item.fecha);
                item.fecha = formattedDate;

                const targetUrl = route ? `${route}?search=${formattedDate}` : '#';

                item.actions = route ? `
            <a href="${targetUrl}">
                <button class="btn btn-view action-icon">
                    <i class="mdi mdi-eye-outline"></i>
                </button>
            </a>` : '';

                item.status = `<span><b class="badge ${getStatusColor3(item.status)}">${item.status}</b></span>`;

                return item;
            });

            $('#revenueTable').bootstrapTable('load', tableData);
        }

        $('#revenueTable').on('refresh.bs.table', function () {
            loadRevenueData(currentRevenueMinDate, currentRevenueMaxDate);
        });




        function formatDateForSearch(dateStr) {
            // Convierte "2025-05-08" a "05/08/2025"
            const [year, month, day] = dateStr.split('-');
            return `${month}/${day}/${year}`;
        }


        let currentRevenueMinDate = '';
        let currentRevenueMaxDate = '';

        function loadRevenueData(minDate = '', maxDate = '') {
            currentRevenueMinDate = minDate;
            currentRevenueMaxDate = maxDate;

            console.log("Cargando revenue:", minDate, maxDate);

            let url = `biomarkers/resumen-status/${userId}`;
            const queryParams = [];

            if (minDate) queryParams.push(`minDate=${encodeURIComponent(minDate)}`);
            if (maxDate) queryParams.push(`maxDate=${encodeURIComponent(maxDate)}`);
            if (queryParams.length > 0) {
                url += '?' + queryParams.join('&');
            }

            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                success(response) {
                    if (!response || !Array.isArray(response.data)) {
                        console.warn('Expected array in response.data but got:', response);
                        $('#revenueTable').bootstrapTable('load', []);
                        return;
                    }
                    revData = response.data;
                    revPage = 1;
                    renderRevenueTable();
                },
                error(xhr, status, err) {
                    console.error('AJAX error:', err);
                    $('#revenueTable').bootstrapTable('load', []);
                }
            });
        }

        $('#revenueTable').on('refresh.bs.table', function () {
            loadRevenueData(currentRevenueMinDate, currentRevenueMaxDate);
        });



        function statusFormatter(value, row, index) {
            return `<span class="badge ${getStatusColor(value)}">${value}</span>`;
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





        function loadBiomarkerDashboard(minDate = '', maxDate = '') {
            // CARD 1: Active Markers
            $.ajax({
                url: `biomarkers/valid-count/${userId}?minDate=${encodeURIComponent(minDate)}&maxDate=${encodeURIComponent(maxDate)}`,
                method: 'GET',
                dataType: 'json',
                success(resp) {
                    console.log('active_markers response:', resp);

                    if (resp.value && resp.data && resp.data.valid != null && resp.data.total != null) {
                        $('#biomarker-out-range').text(`${resp.data.valid}/${resp.data.total}`);
                    } else {
                        console.warn('Formato inesperado en active_markers:', resp);
                        $('#biomarker-out-range').text('0/0');
                    }
                },
                error(xhr, status, err) {
                    console.error('AJAX error en active_markers:', status, err);
                    $('#biomarker-out-range').text('0/0');
                }
            });



            // CARD 2: This Month
            // CARD 2: This Month
            $.ajax({
                url: `biomarkers/user-valid-values/${userId}?minDate=${encodeURIComponent(minDate)}&maxDate=${encodeURIComponent(maxDate)}`,
                method: 'GET',
                dataType: 'json',
                success(resp) {
                    console.log('this_month response:', resp);

                    let inR = 0;
                    if (resp.value && resp.data && resp.data.count != null) {
                        inR = resp.data.count;
                    } else if (resp.error) {
                        console.error('this_month error:', resp.error);
                    }

                    $('#biomarkers-finish').text(`${inR}`);

                    myTippy2[0].setContent(`${formatearFecha(minDate).replaceAll('-', '/')} - ${formatearFecha(maxDate).replaceAll('-', '/')}`)



                },
                error(xhr, status, err) {
                    console.error('AJAX error en this_month:', status, err);
                    $('#biomarkers-finish').text('0');
                }
            });







            // CARD 3 & 4: In/Out Range (modificado para usar la nueva ruta)
            $.ajax({
                url: `biomarkers/in-out-range/${userId}?minDate=${encodeURIComponent(minDate)}&maxDate=${encodeURIComponent(maxDate)}`,
                method: 'GET',
                dataType: 'json',
                success(resp) {
                    console.log('in_out_range response:', resp);

                    if (!resp.value || !resp.data || resp.data.in_range === undefined || resp.data.out_range === undefined) {
                        console.error('in_out_range error:', resp.message || 'Unexpected format');
                        $('#biomarker-this-month').text('0.00');
                        $('#biomarker-in-range').text('0.00');
                        return;
                    }

                    const inCount = parseInt(resp.data.in_range, 10) || 0;
                    const outCount = parseInt(resp.data.out_range, 10) || 0;
                    const total = inCount + outCount;

                    if (total > 0) {
                        const inPct = ((inCount / total) * 100).toFixed(2);
                        const outPct = ((outCount / total) * 100).toFixed(2);
                        $('#biomarker-this-month').text(inPct);
                        $('#label-this-month').text(translations[lang].inRange);
                        $('#biomarker-in-range').text(outPct);
                        $('#label-in-range').text(translations[lang].outOfRange);
                    } else {
                        $('#biomarker-this-month').text('0.00');
                        $('#biomarker-in-range').text('0.00');
                    }
                },
                error(xhr, status, err) {
                    console.error('AJAX error en in_out_range:', status, err);
                    $('#biomarker-this-month').text('0.00');
                    $('#biomarker-in-range').text('0.00');
                }
            });








        }


        // Handlers
        $('#search-input').on('input', () => { bioPage = 1; renderBiomarkerTable(); });
        $('#revenue-search-input').on('input', () => { revPage = 1; renderRevenueTable(); });


        // Date range change
        window.onDateRangeChange = (minDate, maxDate) => {
            const formattedMin = formatDateToYMD(minDate);
            const formattedMax = formatDateToYMD(maxDate);
            loadBiomarkerDashboard(formattedMin, formattedMax);


            loadBiomarkerData(formattedMin, formattedMax);
            loadRevenueData(formattedMin, formattedMax);



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
    function exportToPDF4() {
        const { jsPDF } = window.jspdf;  // Inicializar jsPDF
        const doc = new jsPDF();  // Crear un nuevo documento PDF

        // Obtener la tabla y sus contenidos
        const table = document.getElementById('recent-records-table');
        const rows = table.querySelectorAll('tr');

        // Definir las variables para la posición inicial en el PDF
        let yPosition = 10;

        // Añadir título al PDF
        doc.setFontSize(16);
        doc.text("Registros Recientes del Usuario", 20, yPosition);
        yPosition += 10; // Espacio entre el título y la tabla

        // Añadir encabezado de la tabla al PDF
        const headerColumns = ['Fecha', 'Biomarcador', 'Valor', 'Estado', 'Acciones']; // Encabezados de la tabla
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
        doc.save('registros_recientes_usuario.pdf');
    }
</script>



<script>
    // Función para exportar la tabla a PDF
    function exportToPDF3() {
        const { jsPDF } = window.jspdf;  // Inicializar jsPDF
        const doc = new jsPDF();  // Crear un nuevo documento PDF

        // Obtener la tabla y sus contenidos
        const table = document.getElementById('revenueTable');
        const rows = table.querySelectorAll('tr');

        // Definir las variables para la posición inicial en el PDF
        let yPosition = 10;

        // Añadir título al PDF
        doc.setFontSize(16);
        doc.text("Historial de Envíos de Test", 20, yPosition);
        yPosition += 10; // Espacio entre el título y la tabla

        // Añadir encabezado de la tabla al PDF
        const headerColumns = ['Panel', 'Fecha', 'Biomarcadores Añadidos', 'Estado', 'Acciones']; // Encabezados de la tabla
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
        doc.save('historial_envios_test.pdf');
    }
</script>