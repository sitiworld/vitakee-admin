<?php
// Asegurar que solo aceptamos 'EN' o 'ES'
$idioma = strtoupper($_SESSION['idioma'] ?? 'ES');
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
<style>/* Tabs internos: icono a la izquierda y negrita en activa */
.nav-tabs .nav-link {
  display: flex;
  align-items: center;
  gap: 6px;            /* espacio entre icono y texto */
  font-weight: 500;    /* normal */
  transition: color .2s ease;
}
.nav-tabs .nav-link i {
  font-size: 16px;
  line-height: 1;
}
.nav-tabs .nav-link.active {
  font-weight: 700;    /* negrita cuando está activa */
}
</style>
<div class="container-fluid">
  <a href="energy_metabolism_view" class="" role="button">
    <button class="btn btn-back mb-3">
      <i class="mdi mdi-arrow-left"></i> <?= $traducciones['back'] ?>
    </button>
  </a>

  <div class="row">
    <div class="col-xl-6">
      <div class="accordion custom-accordion" id="custom-accordion-one">
        <?php
        // Métricas a mostrar
        $metrics = [
          'Ketone'  => 'ketone',
          'Glucose' => 'glucose',
          'HbA1c'   => 'hba1c',
        ];

        $first = true;
        foreach ($metrics as $label => $field):
          $id = strtolower($field);
          ?>
          <div class="card mb-0" id="card-<?= $id ?>">
            <div class="card-header" id="heading-<?= $id ?>" style="background-color: #e8fcfd;">
              <h5 class="m-0 position-relative">
                <a class="custom-accordion-title <?= $first ? '' : 'collapsed' ?> d-block"
                   data-bs-toggle="collapse"
                   href="#collapse-<?= $id ?>"
                   aria-expanded="<?= $first ? 'true' : 'false' ?>"
                   aria-controls="collapse-<?= $id ?>"
                   style="color: #223976;">
                  <?= $traducciones[$label] ?? $label ?>
                  <i class="mdi mdi-chevron-down accordion-arrow"></i>
                </a>
              </h5>
            </div>
            <div id="collapse-<?= $id ?>" class="collapse <?= $first ? 'show' : '' ?>"
                 aria-labelledby="heading-<?= $id ?>" data-bs-parent="#custom-accordion-one">
              <div class="card-body">

    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link text-gray active" data-bs-toggle="tab" href="#<?= $id ?>-home"
           role="tab" aria-selected="true">
          <i class="mdi mdi-ruler"></i>
          <?= $traducciones['tab_range'] ?>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-gray" data-bs-toggle="tab" href="#<?= $id ?>-profile"
           role="tab" aria-selected="false" tabindex="-1">
          <i class="mdi mdi-gauge"></i>
          <?= $traducciones['tab_current'] ?>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-gray" data-bs-toggle="tab" href="#<?= $id ?>-messages"
           role="tab" aria-selected="false" tabindex="-1">
          <i class="mdi mdi-chart-line"></i>
          <?= $traducciones['tab_over_time'] ?>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-gray" data-bs-toggle="tab" href="#<?= $id ?>-comments"
           role="tab" aria-selected="false" tabindex="-1">
          <i class="mdi mdi-comment-text-multiple-outline"></i>
          <?= $traducciones['tab_comments'] ?? 'Specialist Comments' ?>
        </a>
      </li>
    </ul>

                <div class="tab-content pt-3">
                  <div class="tab-pane fade show active" id="<?= $id ?>-home" role="tabpanel"></div>
                  <div class="tab-pane fade" id="<?= $id ?>-profile" role="tabpanel"></div>
                  <div class="tab-pane fade" id="<?= $id ?>-messages" role="tabpanel"></div>
                  <div class="tab-pane fade" id="<?= $id ?>-comments" role="tabpanel"></div>
                </div>

              </div>
            </div>
          </div>
          <?php
          $first = false;
        endforeach;
        ?>
      </div>
    </div>
  </div>

  <div class="modal fade" id="history-modal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="historyModalLabel"><?= $traducciones['history'] ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th><?= $traducciones['date'] ?></th>
                <th><?= $traducciones['value'] ?></th>
              </tr>
            </thead>
            <tbody id="history-table-body"></tbody>
          </table>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
            <i class="mdi mdi-cancel"></i> <?= $traducciones['close'] ?>
          </button>
        </div>

      </div>
    </div>
  </div>
</div>

<style>
  /* ... (ESTILOS SIN CAMBIOS) ... */
  .barra-container {
    position: relative;
    width: 100%;
    height: 12px;
    background: linear-gradient(to right,
      #ebbcbe 0%,
      #ebbcbe calc(var(--minPorcentaje) * 1%),
      #a5dfb4 calc(var(--minPorcentaje) * 1%),
      #a5dfb4 calc(var(--maxPorcentaje) * 1%),
      #ebbcbe calc(var(--maxPorcentaje) * 1%),
      #ebbcbe 100%);
    border-radius: 6px;
    margin: 20px 0;
  }

  .burbuja {
    position: absolute;
    top: -30px;
    transform: translateX(-50%);
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    color: white;
    white-space: nowrap;
    cursor: pointer;
  }

  .barra-valor { margin-top: 5px; font-size: 14px; }

  .rectangulo-valor {
    display: inline-block;
    min-width: 100px;
    padding: 5px 10px;
    margin-left: 15px;
    border-radius: 6px;
    font-weight: bold;
    color: #fff;
    font-size: 14px;
    vertical-align: middle;
    text-align: center;
    cursor: pointer;
  }

  .seccion-biomarcador { margin-bottom: 40px; }

  .descripcion-biomarcador { margin-top: 15px; font-size: 14px; }
  .descripcion-biomarcador h6 { margin-bottom: 5px; font-weight: bold; }
  .descripcion-biomarcador p { margin-left: 10px; }

  .valor-reference p { font-size: 14px; font-weight: bold; margin: 5px 0; display: inline-block; }
</style>

<script src="public/assets/js/logout.js"></script>
<script>
  $(document).ready(function () {
    const recordId = '<?php echo htmlspecialchars($_GET['id'] ?? '', ENT_QUOTES); ?>';
    const sex_biological = '<?php echo $_SESSION['sex_biological'] ?? ''; ?>';
    const idioma = '<?php echo $_SESSION['idioma'] ?? 'EN'; ?>';
    
    // --- AÑADIDO: Base URL ---
    const baseUrl = "<?= BASE_URL ?>";

    const lang = {
      ES: {
        errorLoadingBiomarkers: '<?= $traducciones['errorLoadingBiomarkers_glucose_componente'] ?>',
        failLoading: '<?= $traducciones['failLoading_glucose_componente'] ?>',
        missingData: '<?= $traducciones['missingData_glucose_componente'] ?>',
        historyError: '<?= $traducciones['historyError_glucose_componente'] ?>',
        noData: '<?= $traducciones['noData_glucose_componente'] ?>',
        noDataTitle: '<?= $traducciones['noDataTitle_glucose_componente'] ?>',
        commentLabel: '<?= $traducciones['commentLabel_glucose_componente'] ?>',
        referenceRange: '<?= $traducciones['referenceRange_glucose_componente'] ?>',
        deficiency: '<?= $traducciones['deficiency_glucose_componente'] ?>',
        excess: '<?= $traducciones['excess_glucose_componente'] ?>',
        description: '<?= $traducciones['description_glucose_componente'] ?>',
        viewHistory: '<?= $traducciones['viewHistory_glucose_componente'] ?>',
        errorTitle: '<?= $traducciones['errorTitle_glucose_componente'] ?>',
        eAG: 'Glucemia estimada (eAG)',
        units_mgdl: 'mg/dL',
        noComments: '<?= $traducciones['no_comments'] ?? 'No hay comentarios de especialistas.' ?>',
        // --- AÑADIDO ---
        tab_comments: '<?= $traducciones['tab_comments'] ?? 'Comentarios' ?>'
      },
      EN: {
        errorLoadingBiomarkers: '<?= $traducciones['errorLoadingBiomarkers_glucose_componente'] ?>',
        failLoading: '<?= $traducciones['failLoading_glucose_componente'] ?>',
        missingData: '<?= $traducciones['missingData_glucose_componente'] ?>',
        historyError: '<?= $traducciones['historyError_glucose_componente'] ?>',
        noData: '<?= $traducciones['noData_glucose_componente'] ?>',
        noDataTitle: '<?= $traducciones['noDataTitle_glucose_componente'] ?>',
        commentLabel: '<?= $traducciones['commentLabel_glucose_componente'] ?>',
        referenceRange: '<?= $traducciones['referenceRange_glucose_componente'] ?>',
        deficiency: '<?= $traducciones['deficiency_glucose_componente'] ?>',
        excess: '<?= $traducciones['excess_glucose_componente'] ?>',
        description: '<?= $traducciones['description_glucose_componente'] ?>',
        viewHistory: '<?= $traducciones['viewHistory_glucose_componente'] ?>',
        errorTitle: '<?= $traducciones['errorTitle_glucose_componente'] ?>',
        eAG: 'Estimated Average Glucose (eAG)',
        units_mgdl: 'mg/dL',
        noComments: '<?= $traducciones['no_comments'] ?? 'No specialist comments.' ?>',
        // --- AÑADIDO ---
        tab_comments: '<?= $traducciones['tab_comments'] ?? 'Specialist Comments' ?>'
      }
    }[idioma] || {};

    // Datos en memoria
    const biomarkerById = {};
    const biomarkerBySlug = {};
    // --- MODIFICADO: Pasa de string a array ---
    const commentsMap = {}; // id_biomarker -> [array de comentarios]

    // Nombres pedidos al endpoint
    const biomarkerRequestNames = ['Glucose', 'Ketones', 'HbA1c'];

    // Variantes por slug
    const slugs = {
      glucose: ['Glucose'],
      ketone:  ['Ketones'],
      hba1c:   ['HbA1c', 'Hemoglobin A1c', 'A1c']
    };
    
    // ... (Helpers: calcularPorcentaje, colorBurbuja, colorBurbuja2, calcEAG, openCard, setMetricVisibility, ensureActiveFirstVisible - sin cambios) ...
    const calcularPorcentaje = (valor, min, max) => {
      const total = max * 1.5;
      return Math.max(0, Math.min(100, (valor / total) * 100)).toFixed(2);
    };

    const colorBurbuja = (valor, min, max) => (valor >= min && valor <= max) ? 'green-item' : 'red-item';
    const colorBurbuja2 = (valor, min, max) => (valor >= min && valor <= max) ? 'green-label' : 'red-label';

    const calcEAG = (hba1c) => (28.7 * hba1c - 46.7).toFixed(0);

    const openCard = (id) => {
      $('#custom-accordion-one .collapse').removeClass('show').attr('aria-expanded', 'false');
      $('#custom-accordion-one .custom-accordion-title').addClass('collapsed').attr('aria-expanded', 'false');

      $('#collapse-' + id).addClass('show').attr('aria-expanded', 'true');
      $('a[href="#collapse-' + id + '"]').removeClass('collapsed').attr('aria-expanded', 'true');

      const $target = $('#card-' + id);
      if ($target.length) $('html, body').animate({ scrollTop: $target.offset().top - 100 }, 300);
    };

    const setMetricVisibility = (id, visible) => {
      const $card = $('#card-' + id);
      if (!$card.length) return;
      if (visible) {
        $card.removeClass('d-none');
      } else {
        $card.addClass('d-none');
        $('#collapse-' + id).removeClass('show').attr('aria-expanded', 'false');
        $('a[href="#collapse-' + id + '"]').addClass('collapsed').attr('aria-expanded', 'false');
      }
    };

    const ensureActiveFirstVisible = (preferredId) => {
      if (preferredId && $('#card-' + preferredId + ':not(.d-none)').length) {
        openCard(preferredId);
        return;
      }
      const $firstVisible = $('#custom-accordion-one .card:not(.d-none)').first();
      if ($firstVisible.length) {
        const firstId = $firstVisible.attr('id').replace('card-', '');
        openCard(firstId);
      }
    };

    // --- MODIFICADO: renderBiomarcador ---
    const renderBiomarcador = (id, value, biomarker) => {
      if (!biomarker || value === 0 || value === null || value === undefined || isNaN(value)) {
        setMetricVisibility(id, false);
        return;
      }
      setMetricVisibility(id, true);

      // ... (cálculos de min, max, pct, clr, etc. - sin cambios) ...
      const min = parseFloat(biomarker.reference_min);
      const max = parseFloat(biomarker.reference_max);
      const pct = calcularPorcentaje(value, min, max);
      const minPct = calcularPorcentaje(min, min, max);
      const maxPct = calcularPorcentaje(max, min, max);
      const clr  = colorBurbuja(value, min, max);
      const clr2 = colorBurbuja2(value, min, max);

      const name        = (idioma === 'ES' ? (biomarker.name_es || biomarker.name) : (biomarker.name || biomarker.name_es));
      const deficiency  = (idioma === 'ES' ? (biomarker.deficiency_es  || biomarker.deficiency_label) : (biomarker.deficiency_label || biomarker.deficiency_es));
      const excess      = (idioma === 'ES' ? (biomarker.excess_es      || biomarker.excess_label)     : (biomarker.excess_label     || biomarker.excess_es));
      const description = (idioma === 'ES' ? (biomarker.description_es || biomarker.description)      : (biomarker.description      || biomarker.description_es));

      // --- ELIMINADO: commentLine ---
      // const commentText = ...
      // const commentLine = ...

      const extraForHba1c = (id === 'hba1c')
        ? `<div class="barra-valor"><b>${lang.eAG}</b><br>${calcEAG(parseFloat(value))} ${lang.units_mgdl}</div>`
        : '';
        
      // --- NUEVO: Lógica de Comentarios para el tab ---
      const bmId = biomarker.biomarker_id || biomarker.id || biomarker.biomarkerId;
      const commentsList = (bmId && commentsMap[bmId]) ? commentsMap[bmId] : [];
      let commentsHtml = '';

      if (commentsList.length > 0) {
        commentsList.forEach(c => {
          const specName = `${c.specialist_first_name || ''} ${c.specialist_last_name || ''}`;
          const specTitle = c.specialist_title || '';
          
          let imageUrl = '';
          if (c.specialist_image) {
              imageUrl = `${baseUrl}uploads/specialist/user_${c.id_specialist}.jpg?t=${new Date().getTime()}`;
          } else if (c.specialist_avatar_url) {
              imageUrl = c.specialist_avatar_url;
          } else {
              const initials = `${(c.specialist_first_name || 'S').charAt(0)}${(c.specialist_last_name || 'P').charAt(0)}`;
              imageUrl = `https://placehold.co/40x40/EFEFEF/AAAAAA&text=${initials}`;
          }

          commentsHtml += `
            <div class="d-flex mb-3">
              <img src="${imageUrl}" alt="${specName}" class="me-3 rounded-circle" height="40" width="40" style="object-fit: cover;">
              <div class="w-100">
                <h5 class="mt-0 mb-0 fs-6">${specName}</h5>
                <small class="text-muted">${specTitle}</small>
                <p class="mb-0 mt-2 fst-italic bg-light p-2 rounded">
                  "${c.comment}"
                </p>
              </div>
            </div>
            <hr class="my-2">
          `;
        });
        commentsHtml = commentsHtml.replace(/<hr class="my-2">$/, ''); // Quita la última línea
      } else {
        commentsHtml = `<p class="text-muted">${lang.noComments}</p>`;
      }
      // --- FIN NUEVO ---

      // --- MODIFICADO: Eliminado ${commentLine} ---
      // Range
      $(`#${id}-home`).html(`
        <h5 class="mb-4">${name}</h5>
        <div class="barra-container text-left" style="--minPorcentaje:${minPct}; --maxPorcentaje:${maxPct}; text-align:left;">
          <div class="barra-range"></div>
          <div class="burbuja ${clr}" style="left:${pct}%;">${value} ${biomarker.unit}</div>
        </div>
        <div class="barra-valor"><b>${lang.referenceRange}</b><br>${biomarker.reference_min}–${biomarker.reference_max} ${biomarker.unit}</div>
        <div class="barra-valor"><b>${lang.deficiency}</b><br>${deficiency}</div>
        <div class="barra-valor"><b>${lang.excess}</b><br>${excess}</div>
        <div class="barra-valor"><b>${lang.description}</b><br>${description}</div>
        ${extraForHba1c}
      `);

      // Current
      $(`#${id}-profile`).html(`
        <div class="seccion-biomarcador">
          <h5>${name}
            <div class="rectangulo-valor ${clr}">${value} ${biomarker.unit}</div>
          </h5>
          <div class="descripcion-biomarcador">
            <h6>${lang.referenceRange}</h6><p>${biomarker.reference_min}–${biomarker.reference_max} ${biomarker.unit}</p>
            <h6>${lang.deficiency}</h6><p>${deficiency}</p>
            <h6>${lang.excess}</h6><p>${excess}</p>
            <h6>${lang.description}</h6><p>${description}</p>
            ${id === 'hba1c' ? `<h6>${lang.eAG}</h6><p>${calcEAG(parseFloat(value))} ${lang.units_mgdl}</p>` : ''}
          </div>
        </div>
      `);

      // Over time
      $(`#${id}-messages`).html(`
        <div class="seccion-biomarcador">
          <h5>${name}</h5>
          <div class="valor-reference">
            <p class="px-2 py-2 rounded">
              <p class="${clr2}">${value} ${biomarker.unit}</p> – ${lang.referenceRange} ${biomarker.reference_min}–${biomarker.reference_max}
            </p>
          </div>
          <div class="descripcion-biomarcador">
            <h6>${lang.deficiency}</h6><p>${deficiency}</p>
            <h6>${lang.excess}</h6><p>${excess}</p>
            <h6>${lang.description}</h6><p>${description}</p>
            ${id === 'hba1c' ? `<h6>${lang.eAG}</h6><p>${calcEAG(parseFloat(value))} ${lang.units_mgdl}</p>` : ''}
          </div>
          <div class="history-button cursor-pointer flex items-center gap-2" data-type="${id}" data-recid="${recordId}">
            <span class="btn btn-view action-icon"><i class="mdi mdi-eye-outline"></i></span>
            <span>${lang.viewHistory}</span>
          </div>
        </div>
      `);
      
      // --- NUEVO: Inyectar HTML en el tab de comentarios ---
      $(`#${id}-comments`).html(commentsHtml);
    };

    // ... (Historial - sin cambios) ...
    $(document).on('click', '.history-button', async function () {
      const type = $(this).data('type');
      const recId = $(this).data('recid');

      if (!recId || !type) {
        Swal.fire(lang.errorTitle, lang.missingData, 'error');
        return;
      }

      try {
        const response = await $.ajax({
          url: `energy_metabolism/history/${recId}/${type}`,
          method: 'GET',
          dataType: 'json'
        });

        if (response.value === true && Array.isArray(response.data) && response.data.length > 0) {
          const rows = response.data.map(row => `<tr><td>${row.date}</td><td>${row.value}</td></tr>`).join('');
          const bm = biomarkerBySlug[type];
          const label = idioma === 'ES' ? (bm?.name_es || bm?.name || type) : (bm?.name || bm?.name_es || type);

          $('#historyModalLabel').text(((idioma === 'ES') ? 'Historial de ' : 'History of ') + label);
          $('#history-table-body').html(rows);
          new bootstrap.Modal($('#history-modal')[0]).show();
        } else {
          Swal.fire(lang.noDataTitle, lang.noData, 'info');
        }
      } catch (error) {
        console.error('AJAX Error:', error);
        Swal.fire(lang.errorTitle, lang.historyError, 'error');
      }
    });

    // --- MODIFICADO: initializePage ---
    async function initializePage() {
      try {
        const [biomarkerRes, commentsRes] = await Promise.all([
          $.ajax({
            url: 'biomarkers/info',
            method: 'POST',
            dataType: 'json',
            data: { names: biomarkerRequestNames }
          }),
          // --- MODIFICADO: URL del endpoint de comentarios ---
          $.ajax({
            url: `biomarker-comments/with-specialist/7ff39dd8-01e9-443c-b8e6-0d6b429e63a6/${recordId}`, // panel 1 (Energy Metabolism)
            method: 'GET',
            dataType: 'json'
          })
        ]);

        if (!biomarkerRes.data || biomarkerRes.data.length === 0) {
          throw new Error(lang.errorLoadingBiomarkers);
        }

        // ... (Index por ID y slug - sin cambios) ...
        biomarkerRes.data.forEach(bm => { biomarkerById[bm.biomarker_id] = bm; });

        const matchByNames = (targetNames, bm) => {
          const nEN = (bm.name || '').trim().toLowerCase();
          const nES = (bm.name_es || '').trim().toLowerCase();
          return targetNames.some(t => {
            const tt = t.trim().toLowerCase();
            return (nEN === tt || nES === tt);
          });
        };

        ['glucose', 'ketone', 'hba1c'].forEach(slug => {
          const list = slugs[slug];
          const found = biomarkerRes.data.find(bm => matchByNames(list, bm));
          if (found) biomarkerBySlug[slug] = found;
        });

        // --- MODIFICADO: Mapeo de comentarios a array ---
        if (commentsRes && commentsRes.value === true && Array.isArray(commentsRes.data)) {
          commentsRes.data.forEach(c => {
            const key = c.id_biomarker;
            if (key) {
                if (!commentsMap[key]) {
                  commentsMap[key] = []; // Inicializa el array
                }
                commentsMap[key].push(c); // Añade el objeto de comentario completo
              }
          });
        }

        // ... (Traer valores y renderizar - sin cambios) ...
        const valuesResponse = await $.ajax({
          url: `energy_metabolism/${recordId}`,
          method: 'GET',
          dataType: 'json'
        });
        if (valuesResponse.error) throw new Error(valuesResponse.error);

        const glucose = parseFloat(valuesResponse.data.glucose);
        const ketone  = parseFloat(valuesResponse.data.ketone);
        const hba1c   = valuesResponse.data.hba1c !== undefined ? parseFloat(valuesResponse.data.hba1c) : NaN;

        if (biomarkerBySlug.glucose !== undefined) renderBiomarcador('glucose', glucose, biomarkerBySlug.glucose);
        if (biomarkerBySlug.ketone  !== undefined) renderBiomarcador('ketone',  ketone,  biomarkerBySlug.ketone);
        if (biomarkerBySlug.hba1c   !== undefined) renderBiomarcador('hba1c',   hba1c,   biomarkerBySlug.hba1c);

        const selected = '<?php echo isset($_GET['select']) ? strtolower($_GET['select']) : ''; ?>';
        ensureActiveFirstVisible(['glucose','ketone','hba1c'].includes(selected) ? selected : null);

      } catch (error) {
        console.error("Initialization error:", error);
        const errorMessage = typeof error === 'string' ? error : (error.message || lang.failLoading);
        Swal.fire({ icon: 'error', title: lang.errorTitle, text: errorMessage });
      }
    }

    initializePage();
  });
</script>