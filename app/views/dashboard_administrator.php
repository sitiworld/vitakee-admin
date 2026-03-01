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



                    <div class="row" id="admin-kpi-cards">
                        <!-- KPI 1: Total Usuarios -->
                        <div class="col-md-6 col-xl-3">
                            <div class="widget-rounded-circle card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-lg justify-content-center align-items-center d-flex rounded-circle border-kpi-person bg-white-light">
                                                <span class="mdi mdi-account-group-outline text-kpi-person" style="font-size: 24px;"></span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center flex-column col-6">
                                            <div class="text-end">
                                                <h3 class="mt-1 mb-0"><span id="kpi-total-users"><i class="mdi mdi-loading mdi-spin"></i></span></h3>
                                                <p class="text-muted mb-0 text-truncate"><?= $traducciones['kpi_total_users'] ?? 'Total Usuarios' ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KPI 2: Total Especialistas -->
                        <div class="col-md-6 col-xl-3">
                            <div class="widget-rounded-circle card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-lg justify-content-center align-items-center d-flex rounded-circle bg-white-light border-kpi-calendar">
                                                <span class="mdi mdi-doctor text-kpi-calendar" style="font-size: 24px;"></span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center flex-column col-6">
                                            <div class="text-end">
                                                <h3 class="mt-1 mb-0"><span id="kpi-total-specialists"><i class="mdi mdi-loading mdi-spin"></i></span></h3>
                                                <p class="text-muted mb-0 text-truncate"><?= $traducciones['kpi_total_specialists'] ?? 'Total Especialistas' ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KPI 3: Verificaciones Standard -->
                        <div class="col-md-6 col-xl-3">
                            <div class="widget-rounded-circle card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-lg justify-content-center align-items-center d-flex rounded-circle bg-white-light border-kpi-view">
                                                <span class="mdi mdi-shield-check-outline" style="font-size: 24px; color: #3EBBD0;"></span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center flex-column col-6">
                                            <div class="text-end">
                                                <h3 class="mt-1 mb-0"><span id="kpi-standard-verif"><i class="mdi mdi-loading mdi-spin"></i></span></h3>
                                                <p class="text-muted mb-0 text-truncate"><?= $traducciones['kpi_standard_verifications'] ?? 'Verif. Standard' ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KPI 4: Verificaciones Plus -->
                        <div class="col-md-6 col-xl-3">
                            <div class="widget-rounded-circle card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-lg justify-content-center align-items-center d-flex rounded-circle bg-white-light border-kpi-calendar">
                                                <span class="mdi mdi-shield-star-outline text-kpi-calendar" style="font-size: 24px;"></span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center flex-column col-6">
                                            <div class="text-end">
                                                <h3 class="mt-1 mb-0"><span id="kpi-plus-verif"><i class="mdi mdi-loading mdi-spin"></i></span></h3>
                                                <p class="text-muted mb-0 text-truncate"><?= $traducciones['kpi_plus_verifications'] ?? 'Verif. Plus' ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- end KPI row -->



                    <div class="row">

                        <!-- === Donut: % por país === -->
                        <div class="col-lg-4">
                            <div class="card" id="printable-section">
                                <div class="card-body">

                                    <div class="dropdown float-end" id="country-donut-dropdown">
                                        <a href="#" class="dropdown-toggle arrow-none card-drop"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="javascript:void(0);" class="dropdown-item"
                                                onclick="printBiomarkerReport()"><?= $traducciones['dashboard_health_overview_print_pdf'] ?></a>
                                        </div>
                                    </div>

                                    <h4 class="header-title mb-0">
                                        <i class="mdi mdi-earth me-1 text-accent"></i>
                                        <?= $traducciones['dashboard_country_donut_title'] ?? 'Distribución por País' ?>
                                    </h4>

                                    <div class="widget-chart text-center" dir="ltr">
                                        <div id="donut-chart-admin" class="mt-2"></div>
                                    </div>

                                    <!-- Legend list -->
                                    <ul class="list-unstyled mb-0 mt-1" id="country-donut-legend"
                                        style="max-height:160px;overflow-y:auto;font-size:.82rem;"></ul>

                                </div>
                            </div>
                        </div>

                        <!-- === Bar: usuarios vs especialistas por país === -->
                        <div class="col-lg-8">
                            <div class="card pb-2">
                                <div class="row d-flex justify-content-start align-items-center">
                                    <div class="card-body">
                                        <div class="px-2 d-flex flex-wrap w-100 align-items-center justify-content-between gap-2">
                                            <h4 class="header-title m-0">
                                                <i class="mdi mdi-account-group me-1 text-primary"></i>
                                                <?= $traducciones['dashboard_country_bar_title'] ?? 'Usuarios y Especialistas por País' ?>
                                            </h4>
                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input country-filter-radio" type="radio" name="country_filter" id="countryFilterUsers" value="users" checked>
                                                    <label class="form-check-label text-muted" for="countryFilterUsers"><?= $traducciones['kpi_total_users'] ?? 'Usuarios' ?></label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input country-filter-radio" type="radio" name="country_filter" id="countryFilterSpecs" value="specialists">
                                                    <label class="form-check-label text-muted" for="countryFilterSpecs"><?= $traducciones['kpi_total_specialists'] ?? 'Especialistas' ?></label>
                                                </div>
                                            </div>
                                        </div>

                                        <div dir="ltr">
                                            <div id="barlines-chart-admin" class="mt-4"></div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- end row charts -->



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
                                                onclick="exportTopUsersPDF()"><?= $traducciones['dashboard_top_alerts_export'] ?? 'Export PDF' ?></a>
                                        </div>
                                    </div>

                                    <!-- Title -->
                                    <h4 class="header-title mb-3">
                                        <i class="mdi mdi-trophy-outline me-1 text-yellow-text"></i>
                                        <?= $traducciones['dashboard_top_users_exams_title'] ?? 'Top Usuarios con más Exámenes' ?>
                                    </h4>

                                    <!-- Table: Top Users by Exams -->
                                    <div class="table-responsive">
                                        <table id="allUsersTable" data-toggle="table" data-search="true"
                                            data-show-refresh="true" data-page-list="[5, 10, 20]" data-page-size="5"
                                            data-show-columns="true" data-pagination="true" data-url=""
                                            data-show-pagination-switch="true" class="table-borderless"
                                            data-locale="<?= $locale ?>">
                                            <thead>
                                                <tr>
                                                    <th data-field="rank" data-sortable="false" data-escape="false">#</th>
                                                    <th data-field="full_name" data-sortable="true">
                                                        <?= $traducciones['dashboard_table_user'] ?? 'Usuario' ?>
                                                    </th>
                                                    <th data-field="email" data-sortable="true">Email</th>
                                                    <th data-field="total_exams" data-align="center" data-sortable="true">
                                                        <?= $traducciones['dashboard_table_total_exams'] ?? 'Exámenes' ?>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Loaded via JS -->
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
                                                onclick="exportTopSpecialistsPDF()"><?= $traducciones['dashboard_export_report'] ?? 'Export PDF' ?></a>
                                        </div>
                                    </div>

                                    <!-- Title -->
                                    <h4 class="header-title mb-3">
                                        <i class="mdi mdi-star-circle-outline me-1 text-accent"></i>
                                        <?= $traducciones['dashboard_top_specialists_consult_title'] ?? 'Top Especialistas con más Consultas' ?>
                                    </h4>

                                    <!-- Table: Top Specialists by Consultations -->
                                    <div class="table-responsive">
                                        <table id="topUsersTable" data-toggle="table" data-search="true"
                                            data-show-refresh="true" data-page-list="[5, 10, 20]" data-page-size="5"
                                            data-show-columns="true" data-pagination="true"
                                            data-show-pagination-switch="true" class="table-borderless"
                                            data-locale="<?= $locale ?>">
                                            <thead>
                                                <tr>
                                                    <th data-field="rank" data-sortable="false" data-escape="false">#</th>
                                                    <th data-field="full_name" data-sortable="true">
                                                        <?= $traducciones['dashboard_table_specialist'] ?? 'Especialista' ?>
                                                    </th>
                                                    <th data-field="title_display" data-sortable="true">
                                                        <?= $traducciones['dashboard_table_title'] ?? 'Título' ?>
                                                    </th>
                                                    <th data-field="total_consultations" data-align="center" data-sortable="true">
                                                        <?= $traducciones['dashboard_table_total_consultations'] ?? 'Consultas' ?>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Loaded via JS -->
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

    // ============================================================
    // === DASHBOARD ADMIN: KPIs + Ranking tables ================
    // ============================================================
    $(document).ready(function () {

        // --- Helper: render a number with animation ---
        function animateCount(spanId, target) {
            const el = document.getElementById(spanId);
            if (!el) return;
            let current = 0;
            const step = Math.ceil(target / 30);
            const timer = setInterval(() => {
                current = Math.min(current + step, target);
                el.textContent = current.toLocaleString();
                if (current >= target) clearInterval(timer);
            }, 30);
        }

        // --- 1. KPIs ---
        fetch('admin-dashboard/kpis', {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.value && res.data) {
                const d = Array.isArray(res.data) ? res.data[0] : res.data;
                animateCount('kpi-total-users',        parseInt(d.total_users)            || 0);
                animateCount('kpi-total-specialists',  parseInt(d.total_specialists)      || 0);
                animateCount('kpi-standard-verif',     parseInt(d.standard_verifications) || 0);
                animateCount('kpi-plus-verif',         parseInt(d.plus_verifications)     || 0);
            }
        })
        .catch(err => {
            console.error('[AdminDashboard] KPI fetch error:', err);
            ['kpi-total-users','kpi-total-specialists','kpi-standard-verif','kpi-plus-verif'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = '—';
            });
        });

        // --- 2. Top Users by Exams → allUsersTable ---
        fetch('admin-dashboard/top-users?limit=20', {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => {
            if (!res.value || !Array.isArray(res.data)) return;
            const rows = res.data.map((row, i) => ({
                rank:        `<span class="badge ${i === 0 ? 'bg-primary-app' : i === 1 ? 'bg-accent' : i === 2 ? 'bg-electric-blue' : 'bg-sapphire-blue'} text-white">${i + 1}</span>`,
                full_name:   row.full_name   || '—',
                email:       row.email       || '—',
                total_exams: row.total_exams || 0,
            }));
            $('#allUsersTable').bootstrapTable('load', rows);
        })
        .catch(err => console.error('[AdminDashboard] Top users fetch error:', err));

        // --- 3. Top Specialists by Consultations → topUsersTable ---
        fetch('admin-dashboard/top-specialists?limit=20', {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => {
            if (!res.value || !Array.isArray(res.data)) return;
            const rows = res.data.map((row, i) => ({
                rank:                `<span class="badge ${i === 0 ? 'bg-primary-app' : i === 1 ? 'bg-accent' : i === 2 ? 'bg-electric-blue' : 'bg-sapphire-blue'} text-white">${i + 1}</span>`,
                full_name:           row.full_name            || '—',
                title_display:       row.title_display        || '—',
                total_consultations: row.total_consultations  || 0,
            }));
            $('#topUsersTable').bootstrapTable('load', rows);
        })
        .catch(err => console.error('[AdminDashboard] Top specialists fetch error:', err));

        // --- 4. Country Distribution → donut + bar charts ---
        (function initCountryCharts() {
            let countryDonutChart = null;
            let countryBarChart   = null;

            const PALETTE = [
                '#3EBBD0','#2fbde0','#1a8ea3','#0d6e80',
                '#2d9cdb','#56ccf2','#1abc9c','#16a085',
                '#f39c12','#e67e22','#8e44ad','#c0392b',
                '#27ae60','#2980b9','#7f8c8d'
            ];

            function loadCountryData(type = 'users') {
                fetch(`admin-dashboard/country-distribution?limit=15&type=${type}`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(res => {
                    const donutEl = document.getElementById('donut-chart-admin');
                    if (!res.value || !Array.isArray(res.data) || res.data.length === 0) {
                        if (donutEl) {
                            donutEl.innerHTML = '<p class="text-muted text-center py-4"><i class="mdi mdi-information-outline"></i> Sin datos</p>';
                        }
                        return;
                    }

                    // Clear previous 'no data' messages if any exist
                    if (donutEl) donutEl.innerHTML = '';

                    const data     = res.data;
                    const labels   = data.map(d => `${d.flag} ${d.country_name}`);
                    const totals   = data.map(d => d.total);
                    const users    = data.map(d => d.users_count);
                    const specs    = data.map(d => d.specialists_count);
                    const colors   = PALETTE.slice(0, data.length);
                    const grand    = totals.reduce((a, b) => a + b, 0);

                    // ── DONUT (C3) ──────────────────────────────────────────────
                    const colorsMap = {};
                    const columns   = data.map((d, i) => {
                        const lbl = `${d.flag} ${d.country_name}`;
                        colorsMap[lbl] = PALETTE[i % PALETTE.length];
                        return [lbl, d.total];
                    });

                    // Destroy old donut if exists
                    if (countryDonutChart) {
                        donutEl.innerHTML = ''; // Force complete clear
                    }
                    countryDonutChart = c3.generate({
                        bindto: '#donut-chart-admin',
                        data: {
                            columns: columns,
                            type:    'donut',
                            colors:  colorsMap,
                        },
                        donut: {
                            title: `${grand} total`,
                            width: 22,
                            label: { show: false },
                        },
                        tooltip: {
                            format: {
                                value: (value, ratio) =>
                                    `${value} (${(ratio * 100).toFixed(1)}%)`
                            }
                        },
                        size: { height: 220 },
                    });

                    // ── LEGEND LIST ─────────────────────────────────────────────
                    const legendEl = document.getElementById('country-donut-legend');
                    if (legendEl) {
                        legendEl.innerHTML = data.map((d, i) => `
                            <li class="d-flex align-items-center justify-content-between py-1 border-bottom">
                                <span>
                                    <span style="display:inline-block;width:10px;height:10px;border-radius:50%;
                                        background:${PALETTE[i % PALETTE.length]};margin-right:5px;"></span>
                                    ${d.flag} ${d.country_name}
                                </span>
                                <span class="fw-bold">${d.percentage}%
                                    <small class="text-muted">(${d.total})</small>
                                </span>
                            </li>`).join('');
                    }

                    // ── BAR (ApexCharts) ────────────────────────────────────────
                    const barEl = document.getElementById('barlines-chart-admin');
                    if (!barEl) return;

                    if (countryBarChart) { countryBarChart.destroy(); }

                    // Only show series based on selection
                    const seriesData = [];
                    const barColors = [];
                    if (type === 'all' || type === 'users') {
                        seriesData.push({ name: '<?= $traducciones['kpi_total_users'] ?? "Usuarios" ?>', data: users });
                        barColors.push('#3EBBD0');
                    }
                    if (type === 'all' || type === 'specialists') {
                        seriesData.push({ name: '<?= $traducciones['kpi_total_specialists'] ?? "Especialistas" ?>', data: specs });
                        barColors.push(type === 'specialists' ? '#3EBBD0' : '#2d9cdb');
                    }

                    countryBarChart = new ApexCharts(barEl, {
                        series: seriesData,
                        chart: {
                            type:    'bar',
                            height:  340,
                            stacked: false,
                            toolbar: { show: false },
                        },
                        plotOptions: {
                            bar: { horizontal: false, columnWidth: '55%', borderRadius: 3, grouped: true }
                        },
                        colors: barColors,
                        dataLabels: { enabled: false },
                        stroke: { show: true, width: 2, colors: ['transparent'] },
                        xaxis: {
                            categories: labels,
                            labels: { rotate: -20, style: { fontSize: '11px' } }
                        },
                        yaxis: {
                            title: { text: '<?= $traducciones['kpi_total_users'] ?? "Cantidad" ?>' },
                            min: 0,
                            labels: { formatter: val => Math.round(val) }
                        },
                        legend: { position: 'top', horizontalAlign: 'right' },
                        tooltip: {
                            shared: true,
                            intersect: false,
                            y: { formatter: val => `${val} personas` }
                        },
                        grid: { padding: { bottom: 10 } },
                    });
                    countryBarChart.render();
                })
                .catch(err => {
                    console.error('[AdminDashboard] Country distribution fetch error:', err);
                    const el = document.getElementById('donut-chart-admin');
                    if (el) el.innerHTML = '<p class="text-danger text-center py-3"><i class="mdi mdi-alert-circle-outline"></i> Error al cargar datos</p>';
                });
            }

            // Init fetch defaults to 'users'
            loadCountryData('users');

            // Event listener for radio buttons
            const filterRadios = document.querySelectorAll('.country-filter-radio');
            filterRadios.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    if (e.target.checked) loadCountryData(e.target.value);
                });
            });
        })();

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






        // allUsersTable now shows top users by exams — no longer uses allUsersData
        function renderAllUsersTable() {
            // Table replaced: data loaded via admin-dashboard/top-users
        }
        function statusFormatter(value, row, index) {
            return `<span class="badge ${getStatusColor(value)}">${value}</span>`;
        }


        //tabla alerts user adminisstrador
        // Función que carga la tabla y activa los botones
        function renderTopUsersTable() {
            // Table replaced: data loaded via admin-dashboard/top-specialists
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

        // allUsersTable now shows top users by exams — old biomarker AJAX removed
        function loadAllUsersRecords(minDate = '', maxDate = '') {
            // Table replaced: now shows top users by exams (loaded on page init)
        }

        $('#allUsersTable').on('refresh.bs.table', function () {
            fetch('admin-dashboard/top-users?limit=20', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json()).then(res => {
                    if (!res.value || !Array.isArray(res.data)) return;
                    const rows = res.data.map((row, i) => ({
                        rank:        `<span class="badge ${i === 0 ? 'bg-primary-app' : i === 1 ? 'bg-accent' : i === 2 ? 'bg-electric-blue' : 'bg-sapphire-blue'} text-white">${i + 1}</span>`,
                        full_name:   row.full_name   || '—',
                        email:       row.email       || '—',
                        total_exams: row.total_exams || 0,
                    }));
                    $('#allUsersTable').bootstrapTable('load', rows);
                });
        });


        // topUsersTable now shows top specialists by consultations — no AJAX reload needed
        function loadTopUsersWithAlerts(minDate = '', maxDate = '') {
            // Table replaced: now shows top specialists by consultations (loaded on page init)
        }
        function renderTopUsersTable() {
            // Table replaced: data loaded via admin-dashboard/top-specialists
        }

        $('#topUsersTable').on('refresh.bs.table', function () {
            // Re-fetch top specialists on manual refresh
            fetch('admin-dashboard/top-specialists?limit=20', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json()).then(res => {
                    if (!res.value || !Array.isArray(res.data)) return;
                    const rows = res.data.map((row, i) => ({
                        rank:                `<span class="badge ${i === 0 ? 'bg-primary-app' : i === 1 ? 'bg-accent' : i === 2 ? 'bg-electric-blue' : 'bg-sapphire-blue'} text-white">${i + 1}</span>`,
                        full_name:           row.full_name            || '—',
                        title_display:       row.title_display        || '—',
                        total_consultations: row.total_consultations  || 0,
                    }));
                    $('#topUsersTable').bootstrapTable('load', rows);
                });
        });






        function loadBiomarkerDashboard(minDate = '', maxDate = '') {
            // CARD 1: Active Markers


            $.ajax({
                url: 'users/count/${userId}',
                method: 'GET',
                dataType: 'json',
                success(resp) {
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
                    if (!resp.value || !resp.data || typeof resp.data.total === 'undefined') {
                        console.error('Respuesta inválida:', resp);
                        $('#bm2-finish').text('0');
                    } else {
                        $('#bm2-finish').text(resp.data.total);

                        if (myTippy1 && myTippy1[0]) myTippy1[0].setContent(`${formatearFecha(minDate).replaceAll('-', '/')} - ${formatearFecha(maxDate).replaceAll('-', '/')}`); // Cambia el contenido del tooltip

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