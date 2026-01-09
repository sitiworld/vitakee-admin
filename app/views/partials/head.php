<head>
    <!-- Bootstrap Table locale -->

    <meta charset="utf-8" />
    <title><?= htmlspecialchars($titulo) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= BASE_URL ?>public/assets/images/favicon.svg">


    <link href="<?= BASE_URL ?>public/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- PRINCIPAL -->
    <link href="<?= BASE_URL ?>public/assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Bootstrap -->
    <link href="<?= BASE_URL ?>public/assets/libs/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"
        type="text/css" />

    <!-- App css -->
    <link href="<?= BASE_URL ?>public/assets/libs/switchery/switchery.min.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>public/assets/css/app-colors.css" rel="stylesheet" />

    <!-- Sweetalert2 -->
    <link href="<?= BASE_URL ?>public/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <script src="<?= BASE_URL ?>public/assets/libs/sweetalert2/sweetalert2.all.min.js"></script>

    <!-- icons -->
    <link href="<?= BASE_URL ?>public/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= BASE_URL ?>public/assets/css/leaflet.css" rel="stylesheet" type="text/css" />


    <!-- Head js -->
    <script src="<?= BASE_URL ?>public/assets/js/head.js"></script>


    <!-- FLATPICKR -->
    <link href="<?= BASE_URL ?>public/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css">



    <!-- select2 -->
    <link href="<?= BASE_URL ?>public/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css">

    <!-- Dayjs -->
    <script src="<?= BASE_URL ?>public/assets/libs/dayjs/dayjs.min.js"></script>
    <script src="<?= BASE_URL ?>public/assets/libs/dayjs/plugin/isSameOrBefore.js"></script>
    <script src="<?= BASE_URL ?>public/assets/libs/dayjs/plugin/isoWeek.js"></script>
    <script src="<?= BASE_URL ?>public/assets/libs/dayjs/plugin/relativeTime.js"></script>
    <script src="<?= BASE_URL ?>public/assets/libs/dayjs/plugin/utc.js"></script>






    <script src="<?= BASE_URL ?>public/assets/libs/flatpickr/l10n/es.js"></script>

    <link href="<?= BASE_URL ?>public/assets/css/cropper.min.css" rel="stylesheet" />

    <script src="<?= BASE_URL ?>public/assets/js/vendor.min.js"></script>
    <script src="<?= BASE_URL ?>public/assets/js/poppers.js"></script>
    <script src="<?= BASE_URL ?>public/assets/js/tippy.js"></script>




    <!-- Init js-->


    <script>
        const language = <?= json_encode($traducciones) ?>;
        var idioma = "<?= strtoupper($_SESSION["idioma"] ?? "ES") ?>";
        var timezone = "<?= strtoupper($_SESSION["timezone"] ?? "ES") ?>";
        console.log(timezone);


        const systemColors = {
            switchColorOn: '#a5dfb4',
            switchColorOff: '#ccc',
            switchColorDisabled: '#e6e6e6',
        }

    </script>
</head>

<!-- <head>
    <meta charset="UTF-8">

    <link rel="shortcut icon" href="<?= BASE_URL ?>public/assets/images/favicon.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php ?>
    <title><?php echo htmlspecialchars($data['titulo'], ENT_QUOTES, 'UTF-8'); ?> - VITAKEE</title>
    <?php ; ?>
    <meta name="description" content="<?php echo htmlspecialchars($descripcionPagina); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($palabrasClave); ?>">
    <meta name="robots" content="<?php echo htmlspecialchars($robots); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />



    <script>
        window.sessionId = "<?= $_SESSION['session_id'] ?? '' ?>";
    </script>





    <script src="<?= BASE_URL ?>public/assets/libs/jquery/jquery.min.js"></script>


    <link href="<?= BASE_URL ?>public/assets/libs/bootstrap-table/bootstrap-table.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= BASE_URL ?>public/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />




    <link href="<?= BASE_URL ?>public/assets/libs/switchery/switchery.min.css" rel="stylesheet" />
    <script src="<?= BASE_URL ?>public/assets/libs/switchery/switchery.min.js"></script>
    <link href="<?= BASE_URL ?>public/assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="<?= BASE_URL ?>public/assets/css/app-colors.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="<?= BASE_URL ?>public/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <link href="<?= BASE_URL ?>public/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= BASE_URL ?>public/assets/css/leaflet.css" rel="stylesheet" type="text/css" />

    <script src="<?= BASE_URL ?>public/assets/js/head.js"></script>


    <link href="<?= BASE_URL ?>public/assets/libs/c3/c3.min.css" rel="stylesheet" type="text/css" />




    <script src="<?= BASE_URL ?>public/assets/libs/jquery-mask-plugin/jquery.mask.min.js"></script>
    <script src="<?= BASE_URL ?>public/assets/libs/autonumeric/autoNumeric.min.js"></script>

    <script src="<?= BASE_URL ?>public/assets/libs/sweetalert2/sweetalert2.all.min.js"></script>

 



    <script src="<?= BASE_URL ?>public/assets/libs/select2/js/select2.min.js"></script>
    <link href="<?= BASE_URL ?>public/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css">


    <script src="<?= BASE_URL ?>public/assets/libs/dayjs/dayjs.min.js"></script>
    <script src="<?= BASE_URL ?>public/assets/libs/dayjs/plugin/isSameOrBefore.js"></script>
    <script src="<?= BASE_URL ?>public/assets/libs/dayjs/plugin/isoWeek.js"></script>
    <script src="<?= BASE_URL ?>public/assets/libs/dayjs/plugin/relativeTime.js"></script>
    <script src="<?= BASE_URL ?>public/assets/libs/dayjs/plugin/utc.js"></script>



    <script src="<?= BASE_URL ?>public/assets/libs/apexcharts/apexcharts.min.js"></script>


    <link href="<?= BASE_URL ?>public/assets/css/cropper.min.css" rel="stylesheet" />



    <script src="<?= BASE_URL ?>public/assets/js/pages/form-masks.init.js"></script>



</head> -->