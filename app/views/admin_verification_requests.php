<div id="wrapper">
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <h4 class="page-title"><?= $traducciones['verification_requests_title'] ?? 'Verification Requests' ?></h4>
                
                <div class="card">
                    <div class="card-body">
                        <table id="verificationRequestsTable" data-toggle="table" data-search="true" data-show-refresh="true"
                            data-page-list="[5, 10, 20]" data-page-size="10" data-pagination="true"
                            data-show-columns="true" data-show-pagination-switch="true" data-locale="<?= $locale ?>"
                            class="table table-borderless" data-toolbar="#toolbar" data-unique-id="verification_request_id">
                            <thead>
                                <tr>
                                    <th data-field="specialist_name" data-sortable="true" data-formatter="specialistFormatter">
                                        <?= $traducciones['specialist_name'] ?? 'Specialist' ?>
                                    </th>
                                    <th data-field="specialty_display_name" data-sortable="true" data-formatter="specialtyFormatter">
                                        <?= $traducciones['specialty'] ?? 'Specialty' ?>
                                    </th>
                                    <th data-field="title_display_name" data-sortable="true" data-formatter="titleFormatter">
                                        <?= $traducciones['title'] ?? 'Title' ?>
                                    </th>
                                    <th data-field="status" data-sortable="true" data-formatter="statusFormatter">
                                        <?= $traducciones['status'] ?? 'Status' ?>
                                    </th>
                                    <th data-field="submitted_at" data-sortable="true" data-formatter="dateFormatter">
                                        <?= $traducciones['request_date'] ?? 'Request Date' ?>
                                    </th>
                                    <th data-field="id" data-align="center" data-formatter="actionFormatter">
                                        <?= $traducciones['actions'] ?? 'Actions' ?>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <!-- Modal para ver detalles de la solicitud -->
                <div class="modal fade" id="viewRequestModal" tabindex="-1" aria-labelledby="viewRequestModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewRequestModalLabel">
                                    <?= $traducciones['verification_request_details'] ?? 'Verification Request Details' ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="viewRequestContainer">
                                <!-- Content will be loaded dynamically -->
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
    <div class="rightbar-overlay"></div>
</div>

<script src="public/assets/js/logout.js"></script>
<script type="module" src="public/assets/js/modules/admin_verification_requests.js"></script>

</body>

</html>
