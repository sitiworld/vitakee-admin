<script>

    const mensajes = {
        ES: {
            successTitle: '<?= $traducciones["successTitle_helper"] ?>',
            errorTitle: '<?= $traducciones["errorTitle_helper"] ?>',
            successText: '<?= $traducciones["successText_helper"] ?>',
            errorText: '<?= $traducciones["errorText_helper"] ?>',
            confirmButtonText: '<?= $traducciones["confirmButtonText_helper"] ?>',

            createConfirmTitle: '<?= $traducciones["createConfirmTitle_helper"] ?>',
            createConfirmText: '<?= $traducciones["createConfirmText_helper"] ?>',
            createConfirmButton: '<?= $traducciones["createConfirmButton_helper"] ?>',

            saveConfirmTitle: '<?= $traducciones["saveConfirmTitle_helper"] ?>',
            saveConfirmText: '<?= $traducciones["saveConfirmText_helper"] ?>',
            saveConfirmButton: '<?= $traducciones["saveConfirmButton_helper"] ?>',
            confirmButton: '<?= $traducciones["confirmButtonText_helper"] ?>',

            deleteConfirmTitle: '<?= $traducciones["deleteConfirmTitle_helper"] ?>',
            deleteConfirmText: '<?= $traducciones["deleteConfirmText_helper"] ?>',
            deleteConfirmButton: '<?= $traducciones["deleteConfirmButton_helper"] ?>',

            defaultConfirmTitle: '<?= $traducciones["defaultConfirmTitle_helper"] ?>',
            defaultConfirmText: '<?= $traducciones["defaultConfirmText_helper"] ?>',
            defaultConfirmButton: '<?= $traducciones["defaultConfirmButton_helper"] ?>',

            cancelButtonText: '<?= $traducciones["cancelButtonText_helper"] ?>',
            addPanel: '<?= $traducciones["addPanel"] ?>',
            editPanel: '<?= $traducciones["editPanel"] ?>',

            createdTitle: '<?= $traducciones["createdTitle_helper"] ?>',
            createdText: '<?= $traducciones["createdText_helper"] ?>',
            savedTitle: '<?= $traducciones["savedTitle_helper"] ?>',
            savedText: '<?= $traducciones["savedText_helper"] ?>',
            deletedTitle: '<?= $traducciones["deletedTitle_helper"] ?>',
            deletedText: '<?= $traducciones["deletedText_helper"] ?>',
            noRecordsTitle: '<?= $traducciones['no_records_title'] ?>',
            noRecordsText: '<?= $traducciones['no_records_text'] ?>',
            exportErrorTitle: '<?= $traducciones['export_error_title'] ?>',
            exportErrorText: '<?= $traducciones['export_error_text'] ?>',
            exportLoadingTitle: '<?= $traducciones['export_loading_title'] ?>',
            exportLoadingText: '<?= $traducciones['export_loading_text'] ?>',
            csvFilenamePrefix: '<?= $traducciones['csv_filename_prefix_panel'] ?>',
        },
        EN: {
            successTitle: '<?= $traducciones["successTitle_helper"] ?>',
            errorTitle: '<?= $traducciones["errorTitle_helper"] ?>',
            successText: '<?= $traducciones["successText_helper"] ?>',
            errorText: '<?= $traducciones["errorText_helper"] ?>',
            confirmButtonText: '<?= $traducciones["confirmButtonText_helper"] ?>',
            confirmButton: '<?= $traducciones["confirmButtonText_helper"] ?>',
            createConfirmTitle: '<?= $traducciones["createConfirmTitle_helper"] ?>',
            createConfirmText: '<?= $traducciones["createConfirmText_helper"] ?>',
            createConfirmButton: '<?= $traducciones["createConfirmButton_helper"] ?>',

            saveConfirmTitle: '<?= $traducciones["saveConfirmTitle_helper"] ?>',
            saveConfirmText: '<?= $traducciones["saveConfirmText_helper"] ?>',
            saveConfirmButton: '<?= $traducciones["saveConfirmButton_helper"] ?>',
            addPanel: '<?= $traducciones["addPanel"] ?>',
            editPanel: '<?= $traducciones["editPanel"] ?>',

            deleteConfirmTitle: '<?= $traducciones["deleteConfirmTitle_helper"] ?>',
            deleteConfirmText: '<?= $traducciones["deleteConfirmText_helper"] ?>',
            deleteConfirmButton: '<?= $traducciones["deleteConfirmButton_helper"] ?>',

            defaultConfirmTitle: '<?= $traducciones["defaultConfirmTitle_helper"] ?>',
            defaultConfirmText: '<?= $traducciones["defaultConfirmText_helper"] ?>',
            defaultConfirmButton: '<?= $traducciones["defaultConfirmButton_helper"] ?>',

            cancelButtonText: '<?= $traducciones["cancelButtonText_helper"] ?>',

            createdTitle: '<?= $traducciones["createdTitle_helper"] ?>',
            createdText: '<?= $traducciones["createdText_helper"] ?>',
            savedTitle: '<?= $traducciones["savedTitle_helper"] ?>',
            savedText: '<?= $traducciones["savedText_helper"] ?>',
            deletedTitle: '<?= $traducciones["deletedTitle_helper"] ?>',
            deletedText: '<?= $traducciones["deletedText_helper"] ?>',
            noRecordsTitle: '<?= $traducciones['no_records_title'] ?>',
            noRecordsText: '<?= $traducciones['no_records_text'] ?>',
            exportErrorTitle: '<?= $traducciones['export_error_title'] ?>',
            exportErrorText: '<?= $traducciones['export_error_text'] ?>',
            exportLoadingTitle: '<?= $traducciones['export_loading_title'] ?>',
            exportLoadingText: '<?= $traducciones['export_loading_text'] ?>',
            csvFilenamePrefix: '<?= $traducciones['csv_filename_prefix_panel'] ?>',
        }
    };

    const t = mensajes[idioma];


</script>


<!-- Vendor js -->


<script src="<?= BASE_URL ?>public/assets/js/imask.js"></script>
<script type="module" src="<?= BASE_URL ?>public/assets/js/helpers/validarFormulario.js"></script>


<script src="<?= BASE_URL ?>public/assets/js/app.min.js"></script>




<script src="<?= BASE_URL ?>public/assets/libs/apexcharts/apexcharts.min.js"></script>

<!-- TOOLTIPS -->

<!-- Plugins js-->
<script src="<?= BASE_URL ?>public/assets/libs/c3/c3.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/libs/d3/d3.min.js"></script>

<script src="<?= BASE_URL ?>public/assets/libs/selectize/js/standalone/selectize.min.js"></script>

<!-- Dashboar 1 init js-->
<script src="<?= BASE_URL ?>public/assets/libs/select2/js/select2.min.js"></script>

<!-- FOOTER IMPORTS -->
<script src="<?= BASE_URL ?>public/assets/libs/raphael/raphael.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/libs/jquery-mask-plugin/jquery.mask.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/libs/autonumeric/autoNumeric.min.js"></script>

<script src="<?= BASE_URL ?>public/assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/libs/flatpickr/l10n/es.js"></script>

<!-- maps -->
<script src="<?= BASE_URL ?>public/assets/js/leaflet.js"></script>


<!-- Init js -->
<script src="<?= BASE_URL ?>public/assets/js/pages/form-masks.init.js"></script>

<!-- Bootstrap Tables js -->
<script src="<?= BASE_URL ?>public/assets/libs/bootstrap-table/bootstrap-table.min.js"></script>

<script src="<?= BASE_URL ?>public/assets/js/pages/bootstrap-tables.init.js"></script>

<!-- App js -->
<script type="module" src="<?= BASE_URL ?>public/assets/js/app2.js"></script>
<script type="module" src="<?= BASE_URL ?>public/assets/libs/switchery/switchery.min.js"></script>

<script src="<?= BASE_URL ?>public/assets/js/pages/bootstrap-tables.init.js"></script>
<script src="<?= BASE_URL ?>public/assets/js/cropper.min.js"></script>
<!-- Carga de jsPDF -->
<script src="<?= BASE_URL ?>public/assets/js/jspdf.umd.min.js"></script>
<script type="module" src="<?= BASE_URL ?>public/assets/js/helpers/validarFormulario.js"></script>
<script src="<?= BASE_URL ?>public/assets/libs/bootstrap-table/locale/bootstrap-table-en-US.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/libs/bootstrap-table/locale/bootstrap-table-es-ES.min.js"></script>


<!-- App js -->