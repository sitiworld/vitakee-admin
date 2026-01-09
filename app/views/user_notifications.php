<div class="container-fluid">
  <div class="card-body">

    <div id="custom-toolbar" class="d-flex justify-content-between align-items-center mb-2">
      <h4 class="page-title my-0"><?= $traducciones['title_notification'] ?></h4>
    </div>

    <div class="card">
      <div class="card-body">
        <table id="panelsTable" data-toggle="table" data-toolbar="#custom-toolbar" data-search="true"
          data-show-refresh="true" data-show-columns="true" data-pagination="true" data-page-list="[5, 10, 20]"
          data-page-size="5" data-show-pagination-switch="true" data-locale="<?= $locale ?>" class="table-borderless">
          <thead>
            <tr>
              <th data-field="title" data-sortable="true" data-formatter="titleFormatter">
                <?= $traducciones['title_notification_table'] // Título ?>
              </th>
              <th data-field="description" data-sortable="true">
                <?= $traducciones['description_notification'] // Asegúrate de tener esta traducción ?>
              </th>

              <th data-field="created_at" data-sortable="true" data-formatter="dateFormatter">
                <?= $traducciones['date_notification'] ?>
              </th>

              <th data-field="route" data-align="center" data-formatter="panelActionFormatter">
                <?= $traducciones['dashboard_recent_records_actions_user'] ?>
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
  import { formatDateTime } from './public/assets/js/helpers/validacionesEspeciales.js';


  // --- Función auxiliar para reemplazar parámetros ---
  // Asume que el objeto global 'language' está cargado.
  function replaceTemplateParams(templateString, params) {
    if (!templateString) return '';
    return templateString.replace(/\{\{([a-zA-Z0-9_]+)\}\}/g, (match, key) => {
      return params.hasOwnProperty(key) ? params[key] : match;
    });
  }

  // --- Formateador de Fecha ---
  window.dateFormatter = function dateFormatter(value) {
    if (!value) return '';

    return formatDateTime(value);
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


  // --- Formateador de Acciones (sin cambios) ---
  window.panelActionFormatter = function panelActionFormatter(value, row, index) {
    const url = value || '#'; // El valor es la ruta directa

    return `
      <a href="${url}" title="View">
        <button class="btn btn-view action-icon">
          <i class="mdi mdi-eye-outline"></i>
        </button>
      </a>
    `;
  }

  // --- CAMBIADO: Lógica de carga y mapeo de datos ---
  // Cargar datos de alertas
  function loadPanelsTable(userId) {
    $.ajax({
      url: `notifications/all-by-user/${userId}`,
      method: 'GET',
      dataType: 'json',
      success: function (response) {
        if (response.value && Array.isArray(response.data)) {

          // --- Lógica de mapeo actualizada ---
          const rows = response.data.map(r => {
            const params = r.template_params || {};

            // 1. Generar Título
            const titleKey = r.template_key + '_title';
            const titleTemplate = language[titleKey] || params.biomarker_name || 'Notification';
            const title = replaceTemplateParams(titleTemplate, params);

            // 2. Generar Descripción
            const descKey = r.template_key + '_desc';
            const descTemplate = language[descKey] || ''; // Fallback a string vacío
            const description = replaceTemplateParams(descTemplate, params);

            return {
              ...r,
              title: title,                 // Campo para la columna 'title'
              description: description,     // Campo para la nueva columna 'description'
              // 'status' y 'module' ya no se necesitan aquí
            };
          });

          $('#panelsTable').bootstrapTable('load', rows);
        } else {
          console.error('Invalid data received');
        }
      },
      error: function (xhr) {
        console.error('AJAX error:', xhr);
      }
    });
  }

  // --- ELIMINADO ---
  // Las funciones getStatusColor() y statusFormatter() se han eliminado
  // ya que no se usa la columna 'status'.

  // Refrescar tabla
  $('#panelsTable').on('refresh.bs.table', function () {
    loadPanelsTable(id);
  });

  // Al cargar la página
  $(document).ready(function () {
    loadPanelsTable(id);
  });


</script>