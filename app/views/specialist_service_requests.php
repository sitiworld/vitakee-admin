<?php
// Suponiendo que $traducciones se carga aquí desde un archivo o base de datos.
// include 'translations.php';
// $lang = $_SESSION['lang'] ?? 'es';
// $page_translations = $traducciones[$lang];
?>

<div class="container-fluid">
    <!-- Título de la Página -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title"><?= $traducciones['service_requests_title'] ?? 'Service Requests' ?></h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Columna de Filtros (Izquierda) -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4">
                        <input type="search" id="searchInput" class="form-control"
                            placeholder="<?= $traducciones['search_by_patient_placeholder'] ?? 'Search by patient name...' ?>">
                    </div>

                    <h5 class="mb-2"><?= $traducciones['filter_by_status'] ?? 'Filter by Status' ?></h5>
                    <div id="statusFilters" class="nav flex-column nav-pills nav-pills-custom" role="tablist"
                        aria-orientation="vertical">
                        <a class="nav-link mb-1 active" data-status="all"
                            href="#"><?= $traducciones['all_status'] ?? 'All Status' ?></a>
                        <a class="nav-link mb-1" data-status="upcoming" href="#"><i
                                class="bi-calendar-check me-2"></i><?= $traducciones['upcoming'] ?? 'Upcoming' ?></a>
                        <a class="nav-link mb-1" data-status="awaiting_payment" href="#"><i
                                class="bi bi-hourglass-bottom me-2"></i><?= $traducciones['awaiting_payment'] ?? 'Awaiting Payment' ?></a>
                        <a class="nav-link mb-1" data-status="pending" href="#"><i
                                class="bi-clock-history me-2"></i><?= $traducciones['pending'] ?? 'Pending' ?></a>
                        <a class="nav-link mb-1" data-status="rejected" href="#"><i
                                class="bi bi-x-circle me-2"></i><?= $traducciones['rejected'] ?? 'Rejected' ?></a>
                        <a class="nav-link mb-1" data-status="completed" href="#"><i
                                class="bi-check-circle-fill me-2"></i><?= $traducciones['completed'] ?? 'Completed' ?></a>
                        <a class="nav-link mb-1" data-status="cancelled" href="#"><i
                                class="bi-x-circle me-2"></i><?= $traducciones['cancelled'] ?? 'Canceled' ?></a>
                    </div>

                    <hr class="my-2">

                    <h5 class="mb-2"><?= $traducciones['filter_by_type'] ?? 'Filter by Type' ?></h5>
                    <div id="typeFilters" class="nav flex-column nav-pills nav-pills-custom" role="tablist"
                        aria-orientation="vertical">
                        <a class="nav-link mb-1 active" data-type="all"
                            href="#"><?= $traducciones['all_types'] ?? 'All Types' ?></a>
                        <a class="nav-link mb-1" data-type="appointment_request"
                            href="#"><?= $traducciones['type_appointments'] ?? 'Appointments' ?></a>
                        <a class="nav-link mb-1" data-type="document_review"
                            href="#"><?= $traducciones['type_document_reviews'] ?? 'Document Reviews' ?></a>
                    </div>

                    <hr class="my-2">
                    <h5 class="mb-3"><?= $traducciones['sort_by'] ?? 'Sort by' ?></h5>
                    <select id="sortSelect" class="form-select form-select-sm">
                        <option value="newest"><?= $traducciones['sort_newest'] ?? 'Newest' ?></option>
                        <option value="oldest"><?= $traducciones['sort_oldest'] ?? 'Oldest' ?></option>
                    </select>

                </div>
            </div>
        </div>

        <!-- Columna de Lista de Solicitudes (Centro) -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <div id="requestList" class="list-group">
                        <!-- Las solicitudes se cargarán aquí dinámicamente -->
                        <div class="text-center p-5" id="loader">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div id="pagination" class="mt-3">
                        <!-- La paginación se generará aquí si es necesario -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna de Detalles de Solicitud (Derecha) -->
        <div class="col-lg-4">
            <div id="requestDetailsContainer">
                <!-- El detalle de la solicitud se cargará aquí -->
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bi bi-info-circle fs-1 text-primary"></i>
                        <p class="mt-3">
                            <?= $traducciones['select_request_prompt'] ?? 'Select a request from the list to see its details.' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="sharedRecordsModal" tabindex="-1" aria-labelledby="sharedRecordsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sharedRecordsModalLabel">
                    <?= $traducciones['shared_records_title'] ?? 'Shared Medical Records' ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center" id="shared-records-loader" style="display: none;">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2"><?= $traducciones['loading_records'] ?? 'Loading records...' ?></p>
                </div>
                <div id="shared-records-content">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <?= $traducciones['modal_close_button'] ?? 'Close' ?>
                </button>
            </div>
        </div>
    </div>
</div>c

<!-- Script de configuración para pasar variables de PHP a JS -->
<script>
    const APP_CONFIG = {
        baseUrl: '<?= BASE_URL ?? '/api/' ?>', // Asegúrate de que BASE_URL esté definida
        currentLang: '<?= $_SESSION['lang'] ?? 'en' ?>',
        translations: <?= !empty($traducciones) ? json_encode($traducciones) : '{}' ?>
    };
</script>
<script src="public/assets/js/logout.js"></script>
<!-- Script personalizado para esta página. No busca 'filtersOffcanvas'. -->
<script type="module" src="<?= BASE_URL ?>public/assets/js/modules/specialist_service_requests.js"></script>