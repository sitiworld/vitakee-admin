<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/libs/rateyo/rateyo.css">


<script src="<?= BASE_URL ?>public/assets/libs/rateyo/rateyo.js"></script>


<!-- Contenedor principal de la página -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <!-- Título de la página -->
                <h4 class="page-title"><?= $traducciones['my_requests_title'] ?? 'My Requests' ?></h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Columna de filtros -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <!-- Filtros y ordenación -->
                    <h5 class="mb-2"><?= $traducciones['filter_by_status'] ?? 'Filter by Status' ?></h5>
                    <div id="statusFilters" class="nav flex-column nav-pills nav-pills-custom" role="tablist"
                        aria-orientation="vertical">
                        <a class="nav-link mb-1 active" data-status="all"
                            href="#"><?= $traducciones['all_status'] ?? 'All Status' ?></a>
                        <a class="nav-link mb-1" data-status="upcoming" href="#"><i
                                class="bi-calendar-check me-2"></i><?= $traducciones['upcoming'] ?? 'Upcoming' ?></a>
                        <a class="nav-link mb-1" data-status="awaiting_payment" href="#"><i
                                class="bi bi-wallet2 me-2"></i><?= $traducciones['awaiting_payment'] ?? 'Awaiting Payment' ?></a>
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

                    <h5 class="mb-3"><?= $traducciones['sort_by'] ?? 'Sort by' ?></h5>
                    <select id="sortSelect" class="form-select form-select-sm">
                        <option value="newest"><?= $traducciones['sort_newest'] ?? 'Newest' ?></option>
                        <option value="oldest"><?= $traducciones['sort_oldest'] ?? 'Oldest' ?></option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Columna para la lista de solicitudes -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <!-- Indicador de carga -->
                    <div id="loader" class="text-center p-5" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <!-- Contenedor para la lista de solicitudes -->
                    <div id="requestList" class="list-group">
                        <!-- Los items de la lista se insertarán aquí dinámicamente -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna para los detalles de la solicitud -->
        <div class="col-lg-3">
            <div id="requestDetailsContainer">
                <!-- La tarjeta de detalle se insertará aquí dinámicamente -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Leave a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light" role="alert" id="reviewContext"></div>

                <form id="reviewForm">
                    <input type="hidden" id="reviewSecondOpinionId">
                    <input type="hidden" id="reviewSpecialistId">

                    <div class="mb-3">
                        <label for="reviewRating" class="form-label fw-bold">Your Overall Rating</label>
                        <div id="reviewRating"></div>
                    </div>

                    <div class="mb-3">
                        <label for="reviewComment" class="form-label fw-bold">Your Comments</label>
                        <textarea class="form-control" id="reviewComment" rows="4"
                            placeholder="Share details of your experience with the specialist..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-save" id="submitReviewBtn">Submit Review</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts necesarios para la página -->
<script>
    // Configuración global para el script JS
    const APP_CONFIG = {
        baseUrl: '<?= BASE_URL ?>',
        translations: <?= json_encode($traducciones) ?>
    };
</script>
<script src="public/assets/js/logout.js"></script>
<script type="module" src="<?= BASE_URL ?>public/assets/js/modules/user_request_panel.js"></script>