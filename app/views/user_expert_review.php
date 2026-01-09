<head>
    <link href="<?= BASE_URL ?>public/assets/libs/fullcalendar/main.min.css" rel="stylesheet">
</head>
<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .specialist-card-animation {
        /* Inicia oculto para que la animación funcione al aparecer */
        opacity: 0;
        animation: fadeIn 0.5s ease-out forwards;
    }

    #certificateViewerModal .modal-body {
        height: 80vh;
    }

    /* Estilo para el scroll en el wizard */
    #booking-wizard-modal .tab-content {
        overflow-y: auto;
        padding: 1rem;
    }

    #evaluation-wizard-modal .tab-content {
        overflow-y: auto;
        padding: 1rem;
    }
</style>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    <?= htmlspecialchars($traducciones['specialists_search_page_title']) ?>
                </h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-md-8">
                            <form class="d-flex flex-wrap align-items-center" id="search-specialists-form"
                                onsubmit="return false;">
                                <label for="search-input" class="visually-hidden">Search</label>
                                <div class="me-3">
                                    <input type="search" class="form-control my-1 my-md-0" id="search-input"
                                        placeholder="<?= htmlspecialchars($traducciones['search_placeholder']) ?>">
                                </div>
                                <label for="sort-select"
                                    class="me-2"><?= htmlspecialchars($traducciones['sort_by_label']) ?></label>
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-md-0" id="sort-select">
                                        <option value="default" selected>
                                            <?= htmlspecialchars($traducciones['sort_by_recent']) ?>
                                        </option>
                                        <option value="rating_cost">
                                            <?= htmlspecialchars($traducciones['sort_by_rating']) ?>
                                        </option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div class="text-md-end mt-3 mt-md-0">
                                <button type="button" class="btn btn-secondary-color waves-effect waves-light me-1"
                                    data-bs-toggle="offcanvas" data-bs-target="#filtersOffcanvas">
                                    <i class="mdi mdi-filter-variant"></i>
                                    <?= htmlspecialchars($traducciones['filter_button']) ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="specialist-cards-container"></div>

    <div class="row text-center" id="loading-indicator" style="display: none;">
        <div class="col-12 my-3">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
            <p class="mt-2"><?= htmlspecialchars($traducciones['loading_specialists']) ?></p>
        </div>
    </div>
    <div class="row" id="end-message" style="display: none;">
        <div class="col-12 text-center py-4">
            <p class="text-muted"><?= htmlspecialchars($traducciones['no_more_specialists']) ?></p>
        </div>
    </div>

</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="filtersOffcanvas" aria-labelledby="filtersOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="filtersOffcanvasLabel">
            <i class="mdi mdi-filter-variant me-2"></i><?= htmlspecialchars($traducciones['filters_offcanvas_title']) ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0">
        <div class="d-flex h-100">
            <div class="col-sm-8">
                <div class="card tab-content rounded-0 p-4 h-100" style="overflow-y: auto">

                    <div class="tab-pane fade active show" id="v-pills-general" role="tabpanel"
                        aria-labelledby="v-pills-general-tab">
                        <div class="filter-group mb-3">
                            <h5><?= htmlspecialchars($traducciones['specialty_label']) ?></h5>
                            <select class="form-control" multiple="multiple" id="filter-specialties"
                                style="width: 100%;"></select>
                        </div>
                        <hr />
                        <div class="filter-group my-3">
                            <h5><?= htmlspecialchars($traducciones['verification_label']) ?></h5>
                            <div class="form-check form-switch"><input class="form-check-input" type="checkbox"
                                    role="switch" id="verifiedSwitch"><label class="form-check-label"
                                    for="verifiedSwitch"><?= htmlspecialchars($traducciones['verification_show_verified']) ?></label>
                            </div>
                        </div>
                        <hr />
                        <div class="filter-group my-3">
                            <h5><?= htmlspecialchars($traducciones['languages_label']) ?></h5>
                            <div class="filter-options-list">
                                <div class="form-check"><input class="form-check-input" type="checkbox"
                                        id="lang-english" value="EN"><label class="form-check-label"
                                        for="lang-english"><?= htmlspecialchars($traducciones['language_english']) ?></label>
                                </div>
                                <div class="form-check"><input class="form-check-input" type="checkbox"
                                        id="lang-spanish" value="ES"><label class="form-check-label"
                                        for="lang-spanish"><?= htmlspecialchars($traducciones['language_spanish']) ?></label>
                                </div>

                            </div>
                        </div>
                        <div class="filter-group mt-4">
                            <label for="costRange"
                                class="form-label h5"><?= htmlspecialchars($traducciones['min_cost_label']) ?></label>
                            <input type="range" class="form-range" min="0" max="999" step="10" value="0" id="costRange"
                                oninput="this.nextElementSibling.value = '$' + this.value">
                            <output class="fw-bold d-block text-center mt-2">$0</output>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="v-pills-experience" role="tabpanel"
                        aria-labelledby="v-pills-experience-tab">
                        <div class="filter-group mb-4">
                            <label for="evaluationsRange"
                                class="form-label h5"><?= htmlspecialchars($traducciones['min_evaluations_label']) ?></label>
                            <input type="range" class="form-range" min="0" max="200" step="5" value="0"
                                id="evaluationsRange" oninput="this.nextElementSibling.value = this.value">
                            <output class="fw-bold d-block text-center mt-2">0</output>
                        </div>
                        <div class="filter-group">
                            <label for="consultationsRange"
                                class="form-label h5"><?= htmlspecialchars($traducciones['min_consultations_label']) ?></label>
                            <input type="range" class="form-range" min="0" max="500" step="10" value="0"
                                id="consultationsRange" oninput="this.nextElementSibling.value = this.value">
                            <output class="fw-bold d-block text-center mt-2">0</output>
                        </div>
                    </div>
<style>
/* === Estrellas casi pegadas === */
.form-check-label i.mdi-star,
.form-check-label i.mdi-star-outline {
  margin-right: 0.5px;        /* separación mínima posible */
  font-size: 18px;
  vertical-align: middle;
}

/* Texto muy cerca de las estrellas */
.form-check-label {
  display: inline-flex;
  align-items: center;
  gap: 2px;                   /* casi pegado al texto */
}

/* Espaciado uniforme entre filas */
.filter-group .form-check {
  margin-bottom: 3px;
}
</style>



                    <div class="tab-pane fade" id="v-pills-rating" role="tabpanel" aria-labelledby="v-pills-rating-tab">
                        <div class="filter-group">
                            <h5><?= htmlspecialchars($traducciones['min_rating_label']) ?></h5>
                            <div class="form-check"><input class="form-check-input" type="radio" name="ratingFilter"
                                    id="rating5" value="5"><label class="form-check-label" for="rating5"><i
                                        class="mdi mdi-star text-accent-alt"></i><i
                                        class="mdi mdi-star text-accent-alt"></i><i
                                        class="mdi mdi-star text-accent-alt"></i><i
                                        class="mdi mdi-star text-accent-alt"></i><i
                                        class="mdi mdi-star text-accent-alt"></i>
                                    <?= htmlspecialchars($traducciones['rating_5_only']) ?></label></div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="ratingFilter"
                                    id="rating4" value="4"><label class="form-check-label" for="rating4"><i
                                        class="mdi mdi-star text-accent-alt"></i><i
                                        class="mdi mdi-star text-accent-alt"></i><i
                                        class="mdi mdi-star text-accent-alt"></i><i
                                        class="mdi mdi-star text-accent-alt"></i><i
                                        class="mdi mdi-star-outline text-accent-alt"></i>
                                    <?= htmlspecialchars($traducciones['rating_4_up']) ?></label></div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="ratingFilter"
                                    id="rating3" value="3"><label class="form-check-label" for="rating3"><i
                                        class="mdi mdi-star text-accent-alt"></i><i
                                        class="mdi mdi-star text-accent-alt"></i><i
                                        class="mdi mdi-star text-accent-alt"></i><i
                                        class="mdi mdi-star-outline text-accent-alt"></i><i
                                        class="mdi mdi-star-outline text-accent-alt"></i>
                                    <?= htmlspecialchars($traducciones['rating_3_up']) ?></label></div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="ratingFilter" id="rating3"
                                    value="2"><label class="form-check-label" for="rating2">
                                    <i class="mdi mdi-star text-accent-alt"></i>
                                    <i class="mdi mdi-star text-accent-alt"></i>
                                    <i class="mdi mdi-star-outline text-accent-alt"></i>
                                    <i class="mdi mdi-star-outline text-accent-alt"></i>
                                    <i class="mdi mdi-star-outline text-accent-alt"></i>
                                    <?= htmlspecialchars($traducciones['rating_2_up']) ?></label>
                            </div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="ratingFilter"
                                    id="rating1" value="1"><label class="form-check-label" for="rating1">
                                    <i class="mdi mdi-star text-accent-alt"></i>
                                    <i class="mdi mdi-star-outline text-accent-alt"></i>
                                    <i class="mdi mdi-star-outline text-accent-alt"></i>
                                    <i class="mdi mdi-star-outline text-accent-alt"></i>
                                    <i class="mdi mdi-star-outline text-accent-alt"></i>
                                    <?= htmlspecialchars($traducciones['rating_1_up']) ?></label></div>

                                        <small class="mt-2">
                                *
                                <?= htmlspecialchars($traducciones['rating_help_text'] ?? 'Select an exact rating to view specialists with that average score..') ?>
                            </small>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="v-pills-availability" role="tabpanel"
                        aria-labelledby="v-pills-availability-tab">
                        <div class="filter-group">
                            <label for="date-picker"
                                class="form-label h5"><?= htmlspecialchars($traducciones['availability_date_label']) ?></label>
                            <input type="text" class="form-control mb-2" id="date-picker"
                                placeholder="<?= htmlspecialchars($traducciones['select_date_placeholder'] ?? 'Selecciona una fecha...') ?>">
                            <small>
                                *
                                <?= htmlspecialchars($traducciones['availability_date_help_text'] ?? 'Only specialists available on the selected date will appear.') ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="nav flex-column nav-pills nav-pills-light h-100" id="v-pills-tab" role="tablist"
                    aria-orientation="vertical">
                    <a class="nav-link text-start active" id="v-pills-general-tab" data-bs-toggle="pill"
                        href="#v-pills-general" role="tab" aria-controls="v-pills-general" aria-selected="true"><i
                            class="mdi mdi-tune me-2"></i><?= htmlspecialchars($traducciones['filters_tab_general']) ?></a>
                    <a class="nav-link text-start" id="v-pills-experience-tab" data-bs-toggle="pill"
                        href="#v-pills-experience" role="tab" aria-controls="v-pills-experience"
                        aria-selected="false"><i
                            class="mdi mdi-briefcase-check-outline me-2"></i><?= htmlspecialchars($traducciones['filters_tab_experience']) ?></a>
                    <a class="nav-link text-start" id="v-pills-rating-tab" data-bs-toggle="pill" href="#v-pills-rating"
                        role="tab" aria-controls="v-pills-rating" aria-selected="false"><i
                            class="mdi mdi-star-circle-outline me-2"></i><?= htmlspecialchars($traducciones['filters_tab_rating']) ?></a>
                    <a class="nav-link text-start" id="v-pills-availability-tab" data-bs-toggle="pill"
                        href="#v-pills-availability" role="tab" aria-controls="v-pills-availability"
                        aria-selected="false"><i
                            class="mdi mdi-calendar-clock me-2"></i><?= htmlspecialchars($traducciones['filters_tab_availability']) ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="offcanvas-footer text-end p-3 border-top">
        <button type="button" class="btn btn-cancel me-2"
            id="reset-filters-btn"><?= htmlspecialchars($traducciones['clear_all_button']) ?></button>
        <button type="button" class="btn btn-save"
            id="apply-filters-btn"><?= htmlspecialchars($traducciones['apply_filters_button']) ?></button>
    </div>
</div>

<div id="specialistProfileModal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="specialistProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="specialistProfileModalLabel">
                    <?= htmlspecialchars($traducciones['profile_modal_title']) ?>
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-loader" class="text-center">
                    <div class="spinner-border" role="status"></div>
                    <p><?= htmlspecialchars($traducciones['loading_profile_text']) ?></p>
                </div>
                <div id="modal-content-container" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light"
                    data-bs-dismiss="modal"><?= htmlspecialchars($traducciones['modal_close_button']) ?></button>
            </div>
        </div>
    </div>
</div>

<div id="certificateViewerModal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="certificateViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="certificateViewerModalLabel">
                    <?= htmlspecialchars($traducciones['certificate_viewer_title']) ?>
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="certificate-iframe"
                    style="width: 100%; height: 100%; border: none; display: none;"></iframe>
                <img id="certificate-image" style="width: 100%; height: auto; display: none;" src=""
                    alt="Certificate Image" />
            </div>
        </div>
    </div>
</div>



<!-- =================================================================== -->
<!-- =================== MODAL PARA BOOK APPOINTMENT =================== -->
<!-- =================================================================== -->
<div id="booking-wizard-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="bookingWizardModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="bookingWizardModalLabel">
                    <?= htmlspecialchars($traducciones['book_appointment_title']) ?>
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="wizard-loader text-center p-5">
                    <div class="spinner-border" role="status"></div>
                    <p><?= htmlspecialchars($traducciones['loading_text'] ?? 'Loading...') ?></p>
                </div>
                <div class="wizard-container" style="display: none;">
                    <ul class="nav nav-pills bg-light nav-justified form-wizard-header m-0" role="tablist">
                        <li class="nav-item" role="presentation"><a href="#booking-step-intro" data-bs-toggle="tab"
                                class="nav-link rounded-0 pt-2 pb-2 active" role="tab"><i
                                    class="mdi mdi-information-outline me-1"></i><span
                                    class="d-none d-sm-inline"><?= htmlspecialchars($traducciones['wizard_tab_intro']) ?></span></a>
                        </li>
                        <li class="nav-item" role="presentation"><a href="#booking-step-1" data-bs-toggle="tab"
                                class="nav-link rounded-0 pt-2 pb-2" role="tab"><i
                                    class="mdi mdi-clipboard-text-search-outline me-1"></i><span
                                    class="d-none d-sm-inline"><?= htmlspecialchars($traducciones['wizard_tab_type']) ?></span></a>
                        </li>
                        <li class="nav-item" role="presentation"><a href="#booking-step-2" data-bs-toggle="tab"
                                class="nav-link rounded-0 pt-2 pb-2" role="tab"><i
                                    class="mdi mdi-folder-heart-outline me-1"></i><span
                                    class="d-none d-sm-inline"><?= htmlspecialchars($traducciones['wizard_tab_sharing']) ?></span></a>
                        </li>
                        <li class="nav-item" role="presentation"><a href="#booking-step-3" data-bs-toggle="tab"
                                class="nav-link rounded-0 pt-2 pb-2" role="tab"><i
                                    class="mdi mdi-calendar-clock me-1"></i><span
                                    class="d-none d-sm-inline"><?= htmlspecialchars($traducciones['wizard_tab_schedule']) ?></span></a>
                        </li>
                        <li class="nav-item" role="presentation"><a href="#booking-step-4" data-bs-toggle="tab"
                                class="nav-link rounded-0 pt-2 pb-2" role="tab"><i
                                    class="mdi mdi-check-circle-outline me-1"></i><span
                                    class="d-none d-sm-inline"><?= htmlspecialchars($traducciones['wizard_tab_confirm']) ?></span></a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div id="booking-step-intro" class="tab-pane active show" role="tabpanel">
                            <h5 class="mb-3"><?= htmlspecialchars($traducciones['wizard_intro_title']) ?></h5>
                            <div class="text-start">
                                <p class="fs-5"><?= htmlspecialchars($traducciones['wizard_intro_welcome']) ?></p>
                                <p><?= htmlspecialchars($traducciones['wizard_intro_p1_booking']) ?></p>
                                <p><?= htmlspecialchars($traducciones['wizard_intro_p2_booking']) ?></p>
                                <ul>
                                    <li><?= $traducciones['wizard_intro_li_type'] ?></li>
                                    <li><?= $traducciones['wizard_intro_li_sharing'] ?></li>
                                    <li><?= $traducciones['wizard_intro_li_schedule'] ?></li>
                                    <li><?= $traducciones['wizard_intro_li_confirm'] ?></li>
                                </ul>
                                <div class="alert bg-white-light text-primary-darl border-0" role="alert"><i
                                        class="mdi mdi-shield-lock-outline"></i>
                                    <strong><?= htmlspecialchars($traducciones['wizard_intro_privacy_strong']) ?></strong>
                                    <?= htmlspecialchars($traducciones['wizard_intro_privacy_text']) ?>
                                </div>
                            </div>
                        </div>
                        <div id="booking-step-1" class="tab-pane" role="tabpanel">
                            <h5 class="mb-3"><?= htmlspecialchars($traducciones['wizard_step1_title']) ?></h5>
                            <div class="row appointment-type-container"></div>
                        </div>
                        <div id="booking-step-2" class="tab-pane" role="tabpanel">
                            <h5 class="mb-3"><?= htmlspecialchars($traducciones['wizard_step2_title']) ?></h5>
                            <p class="text-muted"><?= htmlspecialchars($traducciones['wizard_sharing_notice']) ?></p>
                            <div class="mb-3">
                                <div class="form-check form-check-inline"><input class="form-check-input" type="radio"
                                        name="sharingOptionsBooking" id="share-none-booking" value="share_none"
                                        checked /><label class="form-check-label"
                                        for="share-none-booking"><?= htmlspecialchars($traducciones['sharing_option_none']) ?></label>
                                </div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="radio"
                                        name="sharingOptionsBooking" id="share-all-booking" value="share_all" /><label
                                        class="form-check-label"
                                        for="share-all-booking"><?= htmlspecialchars($traducciones['sharing_option_all']) ?></label>
                                </div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="radio"
                                        name="sharingOptionsBooking" id="share-custom-booking"
                                        value="share_custom" /><label class="form-check-label"
                                        for="share-custom-booking"><?= htmlspecialchars($traducciones['sharing_option_custom']) ?></label>
                                </div>
                            </div>
                            <div class="custom-sharing-interface fade" style="display: none;">
                                <hr />
                                <p class="text-muted small">
                                    <?= htmlspecialchars($traducciones['sharing_custom_intro']) ?>
                                </p>

                                <div class="row mb-3 align-items-center bg-light p-2 rounded mx-1">
                                    <div class="col-md-4">
                                        <label for="global-date-filter-booking" class="form-label mb-0 fw-bold">
                                            <i class="mdi mdi-filter-variant me-1"></i>
                                            <?= htmlspecialchars($traducciones['filter_all_exams_by_date'] ?? 'Filter all exams by date') ?>
                                        </label>
                                    </div>
                                    <div class="col-md-8 d-flex align-items-center">
                                        <input type="text" id="global-date-filter-booking" class="form-control"
                                            placeholder="<?= htmlspecialchars($traducciones['select_date_range_placeholder']) ?>" />
                                        <button id="clear-date-filter-booking" class="btn btn-sm btn-cancel ms-2"
                                            style="display: none;">
                                            <i class="mdi mdi-close"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="accordion panels-accordion"></div>
                            </div>
                        </div>
                        <div id="booking-step-3" class="tab-pane" role="tabpanel">
                            <h5 class="mb-3"><?= htmlspecialchars($traducciones['wizard_step3_title']) ?></h5>

                            <div class="row">

                                <div class="col-12 col-lg-8 order-lg-2 mb-3 mb-lg-0">
                                    <div id="calendar"></div>
                                </div>

                                <div class="col-12 col-lg-4 order-lg-1">

                                    <div class="mb-3"> <label for="booking-reason" class="form-label">
                                            <?= htmlspecialchars($traducciones['wizard_reason_label']) ?>
                                        </label>
                                        <textarea class="form-control reason-textarea" id="booking-reason" rows="3"
                                            placeholder="<?= htmlspecialchars($traducciones['wizard_reason_placeholder']) ?>"></textarea>
                                    </div>

                                    <div class="alert bg-white-light mb-3">
                                        <h5 class="mb-3">
                                            <?= htmlspecialchars($traducciones['selected_appointment_time_label']) ?>
                                        </h5>
                                        <h4 id="selected-time-text" class="">
                                            <?= htmlspecialchars($traducciones['no_time_selected_text']) ?>
                                        </h4>
                                    </div>

                                    <div class="available-times-container"></div>

                                </div>
                            </div>
                        </div>
                        <div id="booking-step-4" class="tab-pane" role="tabpanel">
                            <h5 class="mb-3"><?= htmlspecialchars($traducciones['wizard_step4_title']) ?></h5>
                            <div class="appointment-summary"></div>
                        </div>
                    </div>
                    <ul class="list-inline mb-0 wizard p-3 border-top">
                        <li class="list-inline-item"><button type="button"
                                class="btn btn-cancel wizard-prev-btn"><?= htmlspecialchars($traducciones['wizard_btn_prev']) ?></button>
                        </li>
                        <li class="list-inline-item float-end"><button type="button"
                                class="btn btn-save wizard-next-btn"><?= htmlspecialchars($traducciones['wizard_btn_next']) ?></button><button
                                type="button" class="btn btn-save wizard-finish-btn"
                                style="display: none;"><?= htmlspecialchars($traducciones['wizard_btn_finish']) ?></button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =================================================================== -->
<!-- =================== MODAL PARA REQUEST EVALUATION ================= -->
<!-- =================================================================== -->
<div id="evaluation-wizard-modal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="evaluationWizardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="evaluationWizardModalLabel">
                    <?= htmlspecialchars($traducciones['request_evaluation_modal_title']) ?>
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="wizard-loader text-center p-5">
                    <div class="spinner-border" role="status"></div>
                    <p><?= htmlspecialchars($traducciones['loading_text'] ?? 'Loading...') ?></p>
                </div>
                <div class="wizard-container" style="display: none;">
                    <ul class="nav nav-pills bg-light nav-justified form-wizard-header m-0" role="tablist">
                        <li class="nav-item" role="presentation"><a href="#eval-step-intro" data-bs-toggle="tab"
                                class="nav-link rounded-0 pt-2 pb-2 active" role="tab"><i
                                    class="mdi mdi-information-outline me-1"></i><span
                                    class="d-none d-sm-inline"><?= htmlspecialchars($traducciones['wizard_tab_intro']) ?></span></a>
                        </li>
                        <li class="nav-item" role="presentation"><a href="#eval-step-1" data-bs-toggle="tab"
                                class="nav-link rounded-0 pt-2 pb-2" role="tab"><i
                                    class="mdi mdi-clipboard-text-search-outline me-1"></i><span
                                    class="d-none d-sm-inline"><?= htmlspecialchars($traducciones['wizard_tab_type']) ?></span></a>
                        </li>
                        <li class="nav-item" role="presentation"><a href="#eval-step-2" data-bs-toggle="tab"
                                class="nav-link rounded-0 pt-2 pb-2" role="tab"><i
                                    class="mdi mdi-folder-heart-outline me-1"></i><span
                                    class="d-none d-sm-inline"><?= htmlspecialchars($traducciones['wizard_tab_sharing']) ?></span></a>
                        </li>
                        <li class="nav-item" role="presentation"><a href="#eval-step-3" data-bs-toggle="tab"
                                class="nav-link rounded-0 pt-2 pb-2" role="tab"><i
                                    class="mdi mdi-check-circle-outline me-1"></i><span
                                    class="d-none d-sm-inline"><?= htmlspecialchars($traducciones['wizard_tab_confirm']) ?></span></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div id="eval-step-intro" class="tab-pane active show" role="tabpanel">
                            <h5 class="mb-3"><?= htmlspecialchars($traducciones['wizard_intro_title']) ?></h5>
                            <div class="text-start">
                                <p class="fs-5"><?= htmlspecialchars($traducciones['wizard_intro_welcome']) ?></p>
                                <p><?= htmlspecialchars($traducciones['wizard_intro_p1_review']) ?></p>
                                <p><?= htmlspecialchars($traducciones['wizard_intro_p2_review']) ?></p>
                                <ul>
                                    <li><?= $traducciones['wizard_intro_li_type'] ?></li>
                                    <li><?= $traducciones['wizard_intro_li_sharing'] ?></li>
                                    <li><?= $traducciones['wizard_intro_li_confirm'] ?></li>
                                </ul>
                                <div class="alert alert-info bg-light-info text-info border-0" role="alert"><i
                                        class="mdi mdi-shield-lock-outline"></i>
                                    <strong><?= htmlspecialchars($traducciones['wizard_intro_privacy_strong']) ?></strong>
                                    <?= htmlspecialchars($traducciones['wizard_intro_privacy_text']) ?>
                                </div>
                            </div>
                        </div>
                        <div id="eval-step-1" class="tab-pane" role="tabpanel">
                            <h5 class="mb-3"><?= htmlspecialchars($traducciones['wizard_step1_title']) ?></h5>
                            <div class="row appointment-type-container"></div>
                        </div>
                        <div id="eval-step-2" class="tab-pane" role="tabpanel">
                            <h5 class="mb-3"><?= htmlspecialchars($traducciones['wizard_step2_title']) ?></h5>
                            <p class="text-muted"><?= htmlspecialchars($traducciones['wizard_sharing_notice']) ?></p>
                            <div class="mb-3">
                                <div class="form-check form-check-inline"><input class="form-check-input" type="radio"
                                        name="sharingOptionsEval" id="share-none-eval" value="share_none"
                                        checked /><label class="form-check-label"
                                        for="share-none-eval"><?= htmlspecialchars($traducciones['sharing_option_none']) ?></label>
                                </div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="radio"
                                        name="sharingOptionsEval" id="share-all-eval" value="share_all" /><label
                                        class="form-check-label"
                                        for="share-all-eval"><?= htmlspecialchars($traducciones['sharing_option_all']) ?></label>
                                </div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="radio"
                                        name="sharingOptionsEval" id="share-custom-eval" value="share_custom" /><label
                                        class="form-check-label"
                                        for="share-custom-eval"><?= htmlspecialchars($traducciones['sharing_option_custom']) ?></label>
                                </div>
                            </div>
                            <div class="custom-sharing-interface fade" style="display: none;">
                                <hr />
                                <p class="text-muted small">
                                    <?= htmlspecialchars($traducciones['sharing_custom_intro']) ?>
                                </p>

                                <div class="row mb-3 align-items-center bg-light p-2 rounded mx-1">
                                    <div class="col-md-4">
                                        <label for="global-date-filter-eval" class="form-label mb-0 fw-bold">
                                            <i class="mdi mdi-filter-variant me-1"></i>
                                            <?= htmlspecialchars($traducciones['filter_all_exams_by_date'] ?? 'Filter all exams by date') ?>:
                                        </label>
                                    </div>
                                    <div class="col-md-8 d-flex align-items-center">
                                        <input type="text" id="global-date-filter-eval" class="form-control"
                                            placeholder=" <?= htmlspecialchars($traducciones['select_date_range_placeholder']) ?>" />
                                        <button id="clear-date-filter-eval" class="btn btn-sm btn-cancel ms-2"
                                            style="display: none;"></button>
                                    </div>
                                </div>
                                <div class="accordion panels-accordion"></div>
                            </div>
                        </div>
                        <div id="eval-step-3" class="tab-pane" role="tabpanel">
                            <h5 class="mb-3"><?= htmlspecialchars($traducciones['wizard_step4_title_2']) ?></h5>
                            <div class="mb-3">
                                <label for="eval-reason"
                                    class="form-label"><?= htmlspecialchars($traducciones['wizard_reason_label']) ?></label>
                                <textarea class="form-control reason-textarea" id="eval-reason" rows="3"
                                    placeholder="<?= htmlspecialchars($traducciones['wizard_reason_placeholder']) ?>"></textarea>
                            </div>
                            <div class="appointment-summary"></div>
                        </div>
                    </div>
                    <ul class="list-inline mb-0 wizard p-3 border-top">
                        <li class="list-inline-item"><button type="button"
                                class="btn btn-cancel wizard-prev-btn"><?= htmlspecialchars($traducciones['wizard_btn_prev']) ?></button>
                        </li>
                        <li class="list-inline-item float-end"><button type="button"
                                class="btn btn-save wizard-next-btn"><?= htmlspecialchars($traducciones['wizard_btn_next']) ?></button><button
                                type="button" class="btn btn-save wizard-finish-btn"
                                style="display: none;"><?= htmlspecialchars($traducciones['wizard_btn_finish']) ?></button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>public/assets/libs/fullcalendar/main.min.js"></script>
<script>
    const APP_CONFIG = {
        baseUrl: '<?= BASE_URL ?>',
        currentLang: '<?= $_SESSION['lang'] ?? 'en' ?>',
        userTimezone: '<?= $_SESSION["timezone"] ?? "UTC" ?>',
        translations: <?= json_encode($traducciones) ?>
        // <-- AÑADIR ESTA LÍNEA
    };
</script>
<script src="public/assets/js/logout.js"></script>
<script type="module" src="<?= BASE_URL ?>public/assets/js/modules/user_expert_review.js"></script>