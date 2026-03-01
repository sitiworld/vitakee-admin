<?php
    $defaultDescription = $traducciones['meta_default_description'] ?? 'VITAKEE — Preventive health platform.';
    $pageDescription    = $metaDescription ?? $defaultDescription;
    $pageTitle          = htmlspecialchars($titulo) . ' | VITAKEE';
    $pageKeywords       = $traducciones['meta_keywords'] ?? 'preventive health, biomarkers, VITAKEE';
    $pageLocale         = $traducciones['meta_og_locale'] ?? 'en_US';
    $htmlLang           = $traducciones['lang'] ?? 'en';
    $canonicalUrl       = htmlspecialchars(
        (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
        . '://' . ($_SERVER['HTTP_HOST'] ?? 'vitakee.com')
        . ($_SERVER['REQUEST_URI'] ?? '/')
    );
?>
<head>
    <!-- ═══ Charset & Compatibility ═══ -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="content-language" content="<?= $htmlLang ?>" />

    <!-- ═══ Title ═══ -->
    <title><?= $pageTitle ?></title>

    <!-- ═══ Primary Meta Tags ═══ -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>" />
    <meta name="keywords" content="<?= htmlspecialchars($pageKeywords) ?>" />
    <meta name="author" content="SITI World" />
    <meta name="application-name" content="VITAKEE" />
    <meta name="robots" content="noindex, nofollow" />
    <link rel="canonical" href="<?= $canonicalUrl ?>" />

    <!-- ═══ Theme Color ═══ -->
    <meta name="theme-color" content="#3b82f6" />
    <meta name="msapplication-TileColor" content="#3b82f6" />

    <!-- ═══ Open Graph / Facebook ═══ -->
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="VITAKEE" />
    <meta property="og:title" content="<?= $pageTitle ?>" />
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>" />
    <meta property="og:url" content="<?= $canonicalUrl ?>" />
    <meta property="og:image" content="<?= BASE_URL ?>public/assets/images/favicon.svg" />
    <meta property="og:locale" content="<?= $pageLocale ?>" />

    <!-- ═══ Twitter Card ═══ -->
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="<?= $pageTitle ?>" />
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>" />
    <meta name="twitter:image" content="<?= BASE_URL ?>public/assets/images/favicon.svg" />

    <!-- ═══ Favicon ═══ -->
    <link rel="shortcut icon" href="<?= BASE_URL ?>public/assets/images/favicon.svg" />
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>public/assets/images/favicon.svg" />
    <link rel="apple-touch-icon" href="<?= BASE_URL ?>public/assets/images/favicon.svg" />


    <!-- ═══ Stylesheets ═══ -->
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