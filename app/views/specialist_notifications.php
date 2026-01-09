<?php
// Asegurar que solo aceptamos 'EN' o 'ES'
$idioma = strtoupper($_SESSION['idioma'] ?? 'ES');
$locale = $idioma === 'ES' ? 'es-ES' : 'en-US';
if (!in_array($idioma, ['EN', 'ES'])) {
    $idioma = 'EN'; // valor por defecto
}

$archivo_idioma = PROJECT_ROOT . '/lang/' . $idioma . '.php';

if (file_exists($archivo_idioma)) {
    $traducciones = include $archivo_idioma;
} else {
    die("Archivo de idioma no encontrado: $archivo_idioma");
}

?>


<div class="container-fluid">
    <div class="card-body">

        <div id="custom-toolbar" class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="page-title my-0"><?= $traducciones['title_notification'] ?></h4>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="panelsTable" data-toggle="table" data-toolbar="#custom-toolbar" data-search="true"
                    data-show-refresh="true" data-show-columns="true" data-pagination="true"
                    data-page-list="[5, 10, 20]" data-page-size="5" data-show-pagination-switch="true"
                    data-locale="<?= $locale ?>" class="table-borderless">
                    <thead>
                        <tr>
                            <th data-field="title" data-sortable="true" data-formatter="titleFormatter">
                                <?= $traducciones['title_notification_table'] // Título ?>
                            </th>
                            <th data-field="description" data-sortable="true">
                                <?= $traducciones['description_notification'] // Descripción ?>
                            </th>

                            <th data-field="created_at" data-sortable="true" data-formatter="dateFormatter">
                                <?= $traducciones['date_notification'] // Fecha ?>
                            </th>

                            <th data-field="route" data-align="center" data-formatter="panelActionFormatter">
                                <?= $traducciones['dashboard_recent_records_actions_user'] // Acciones ?>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>
</div>

<script src="public/assets/js/logout.js"></script>


<script type="module">
    let id = '<?php echo $_SESSION['user_id']; ?>';
    // Importamos el helper para formatear fechas
    import { formatDateTime } from './public/assets/js/helpers/validacionesEspeciales.js';

    /**
     * Reemplaza los placeholders (ej: {{name}}) con valores de un objeto.
     * Asume que el objeto global 'language' (JS) está cargado.
     */
    function replaceTemplateParams(templateString, params) {
        if (!templateString) return '';
        return templateString.replace(/\{\{([a-zA-Z0-9_]+)\}\}/g, (match, key) => {
            return params.hasOwnProperty(key) ? params[key] : match;
        });
    }

    // --- Formateador de Fecha ---
    window.dateFormatter = function dateFormatter(value) {
        if (!value) return '';
        // Usa la función importada
        return formatDateTime(value);
    }

    // --- Formateador de Acciones ---
    window.panelActionFormatter = function panelActionFormatter(value, row, index) {
        const url = value || '#'; // El valor 'route' es la URL directa

        return `
          <a href="${url}" title="View">
            <button class="btn btn-view action-icon">
              <i class="mdi mdi-eye-outline"></i>
            </button>
          </a>
        `;
    }

    window.titleFormatter = function titleFormatter(value, row) {
        // 'value' es el texto del título (ej: "Nuevo Biomarcador")
        // 'row' es el objeto completo de la notificación

        const unreadDotHTML =
            row.read_unread === 0
                ? '<span class="badge badge-unread rounded-pill bg-primary-app ms-2"></span>'
                : '';


        // Retornamos el título + el badge (si aplica)
        return `<div class="d-flex">
        <div class="flex-grow-1">${value}</div>
        <div>${unreadDotHTML}</div>
        </div>`;
    }

    /**
     * Carga y mapea los datos de notificaciones.
     * Esta función AHORA es idéntica a la de user_notifications.php
     */
    function loadPanelsTable(userId) {
        $.ajax({
            // URL de API correcta
            url: `notifications/all-by-user/${userId}`,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.value && Array.isArray(response.data)) {

                    // Lógica de mapeo para generar título y descripción
                    const rows = response.data.map(r => {
                        const params = r.template_params || {};

                        // 1. Generar Título
                        const titleKey = r.template_key + '_title';
                        // Asumimos que 'language' es un objeto JS global de traducciones
                        const titleTemplate = language[titleKey] || params.biomarker_name || 'Notification';
                        const title = replaceTemplateParams(titleTemplate, params);

                        // 2. Generar Descripción
                        const descKey = r.template_key + '_desc';
                        const descTemplate = language[descKey] || '';
                        const description = replaceTemplateParams(descTemplate, params);

                        return {
                            ...r,
                            title: title,
                            description: description,
                        };
                    });

                    $('#panelsTable').bootstrapTable('load', rows);
                } else {
                    console.error('Invalid data received');
                    $('#panelsTable').bootstrapTable('load', []); // Muestra tabla vacía
                }
            },
            error: function (xhr) {
                console.error('AJAX error:', xhr);
                $('#panelsTable').bootstrapTable('load', []); // Muestra tabla vacía en error
            }
        });
    }

    // --- Listener para el botón de refrescar de la tabla ---
    $('#panelsTable').on('refresh.bs.table', function () {
        loadPanelsTable(id);
    });

    // --- Carga inicial al documento ---
    $(document).ready(function () {
        loadPanelsTable(id);
    });

</script>