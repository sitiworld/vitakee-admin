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

                                    <!-- Mostrar Todos button -->
                                    <div class="text-center mt-2">
                                        <button id="btn-show-all-countries" class="btn btn-sm btn-bright-turquoise-outline" onclick="openAllCountriesModal()">
                                            <i class="mdi mdi-earth me-1"></i><?= $traducciones['dashboard_show_all_countries'] ?? 'Mostrar Todos' ?>
                                        </button>
                                    </div>

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
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <div class="form-check form-check-inline mb-0">
                                                    <input class="form-check-input country-filter-radio" type="radio" name="country_filter" id="countryFilterUsers" value="users" checked>
                                                    <label class="form-check-label text-muted" for="countryFilterUsers"><?= $traducciones['kpi_total_users'] ?? 'Usuarios' ?></label>
                                                </div>
                                                <div class="form-check form-check-inline mb-0">
                                                    <input class="form-check-input country-filter-radio" type="radio" name="country_filter" id="countryFilterSpecs" value="specialists">
                                                    <label class="form-check-label text-muted" for="countryFilterSpecs"><?= $traducciones['kpi_total_specialists'] ?? 'Especialistas' ?></label>
                                                </div>
                                                <button class="btn btn-sm btn-bright-turquoise-outline" onclick="openAllCountriesModal()">
                                                    <i class="mdi mdi-view-list me-1"></i><?= $traducciones['dashboard_show_all_countries'] ?? 'Mostrar Todos' ?>
                                                </button>
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

                        <!-- ============================================================ -->
                        <!-- Modal: Detalle completo de todos los países                 -->
                        <!-- ============================================================ -->
                        <div class="modal fade" id="all-countries-modal" tabindex="-1" aria-labelledby="allCountriesModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="allCountriesModalLabel">
                                            <i class="mdi mdi-earth me-2 text-accent"></i>
                                            <?= $traducciones['dashboard_all_countries_modal_title'] ?? 'Distribución completa por Nacionalidad' ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Filter radios -->
                                        <div class="d-flex gap-3 mb-3 align-items-center flex-wrap">
                                            <strong><?= $traducciones['dashboard_filter_label'] ?? 'Filtrar por' ?>:</strong>
                                            <div class="form-check form-check-inline mb-0">
                                                <input class="form-check-input" type="radio" name="modal_country_filter" id="modalFilterUsers" value="users" checked>
                                                <label class="form-check-label" for="modalFilterUsers"><?= $traducciones['kpi_total_users'] ?? 'Usuarios' ?></label>
                                            </div>
                                            <div class="form-check form-check-inline mb-0">
                                                <input class="form-check-input" type="radio" name="modal_country_filter" id="modalFilterSpecs" value="specialists">
                                                <label class="form-check-label" for="modalFilterSpecs"><?= $traducciones['kpi_total_specialists'] ?? 'Especialistas' ?></label>
                                            </div>
                                            <div class="form-check form-check-inline mb-0">
                                                <input class="form-check-input" type="radio" name="modal_country_filter" id="modalFilterAll" value="all">
                                                <label class="form-check-label" for="modalFilterAll"><?= $traducciones['dashboard_filter_all'] ?? 'Ambos' ?></label>
                                            </div>
                                        </div>

                                        <!-- Loading state -->
                                        <div id="all-countries-loading" class="text-center py-4">
                                            <i class="mdi mdi-loading mdi-spin fs-3 text-muted"></i>
                                            <p class="text-muted mt-2"><?= $traducciones['dashboard_loading'] ?? 'Cargando...' ?></p>
                                        </div>

                                        <!-- Content -->
                                        <div id="all-countries-content" style="display:none;">
                                            <!-- Bar chart overview -->
                                            <div id="all-countries-bar-chart" class="mb-4"></div>

                                            <!-- Table -->
                                            <div class="table-responsive">
                                                <table class="table table-hover table-borderless align-middle" id="all-countries-table">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width:40px">#</th>
                                                            <th><?= $traducciones['dashboard_country_label'] ?? 'País / Nacionalidad' ?></th>
                                                            <th class="text-center"><?= $traducciones['kpi_total_users'] ?? 'Usuarios' ?></th>
                                                            <th class="text-center"><?= $traducciones['kpi_total_specialists'] ?? 'Especialistas' ?></th>
                                                            <th class="text-center"><?= $traducciones['dashboard_table_total_label'] ?? 'Total' ?></th>
                                                            <th class="text-center">%</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="all-countries-tbody">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sapphire-blue" data-bs-dismiss="modal"><?= $traducciones['close_btn'] ?? 'Cerrar' ?></button>
                                    </div>
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

<script type="module">
    import { initDashboardAdmin } from './public/assets/js/modules/dashboard_administrator.js';

    <?php
    $uid   = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $uRole = 0;
    ?>

    document.addEventListener('DOMContentLoaded', () => {
        initDashboardAdmin({
            userId:   '<?= $uid ?>',
            userRole: <?= $uRole ?>,
            language: <?= isset($_SESSION['idioma']) ? json_encode($_SESSION['idioma']) : json_encode('EN') ?>,
            translations: {
                // Country charts
                otherNationalities: '<?= $traducciones['dashboard_other_nationalities'] ?? 'Otras Nacionalidades' ?>',
                usersLabel:         '<?= $traducciones['kpi_total_users'] ?? 'Usuarios' ?>',
                specialistsLabel:   '<?= $traducciones['kpi_total_specialists'] ?? 'Especialistas' ?>',
                quantityLabel:      '<?= $traducciones['kpi_total_users'] ?? 'Cantidad' ?>',
                // Alert details
                noAlertDetails:  '<?= $traducciones['noAlertDetails_dashboard'] ?? '' ?>',
                biomarker:       '<?= $traducciones['biomarker_dashboard'] ?? 'Biomarcador' ?>',
                value:           '<?= $traducciones['value_dashboard'] ?? 'Valor' ?>',
                referenceRange:  '<?= $traducciones['referenceRange_dashboard'] ?? 'Rango de Referencia' ?>',
                date:            '<?= $traducciones['date_dashboard'] ?? 'Fecha' ?>',
                name:            '<?= $traducciones['name_dashboard'] ?? 'Nombre' ?>',
                status:          '<?= $traducciones['status_dashboard'] ?? 'Estado' ?>',
                detailsTitle:    '<?= $traducciones['detailsTitle_dashboard'] ?? 'Detalles' ?>',
                inRange:         '<?= $traducciones['inRange'] ?? 'In Range' ?>',
                outOfRange:      '<?= $traducciones['outOfRange'] ?? 'Out of Range' ?>',
            }
        });
    });
</script>
