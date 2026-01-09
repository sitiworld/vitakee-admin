<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <title>Renal Panel</title>

  <style>
    /* ... (Estilos sin cambios) ... */
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

    .barra-valor {
      margin-top: 5px;
      font-size: 14px;
    }

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

    .seccion-biomarcador {
      margin-bottom: 40px;
    }

    .descripcion-biomarcador {
      margin-top: 15px;
      font-size: 14px;
    }

    .descripcion-biomarcador h6 {
      margin-bottom: 5px;
      font-weight: bold;
    }

    .descripcion-biomarcador p {
      margin-left: 10px;
    }

    .valor-reference p {
      font-size: 14px;
      font-weight: bold;
      margin: 5px 0;
      display: inline-block;
    }

    .history-button {
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      margin-top: 15px;
    }

    .semaforo-cell {
      height: 38px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 12px;
      color: #223976;
      background: #e9eef6;
      box-shadow: inset 0 0 0 1px rgba(0, 0, 0, .06);
    }

    .alb-3 {
      background: #95a38d;
    }

    .alb-2 {
      background: #8aa295;
    }

    .alb-1 {
      background: #68959b;
    }

    .alb-n {
      background: #68959b;
    }

    .cr-1 {
      background: #8f926b;
    }

    .cr-2 {
      background: #7c8b6a;
    }

    .cr-3 {
      background: #5f6d56;
    }

    .semaforo-grid {
      display: grid;
      grid-template-columns: repeat(4, 80px);
      gap: 8px;
      align-items: center;
      margin: 10px 0 6px;
    }

    .semaforo-grid-3 {
      display: grid;
      grid-template-columns: repeat(3, 80px);
      gap: 8px;
      align-items: center;
      margin: 10px 0 6px;
    }

    .semaforo-active {
      outline: 3px solid #223976;
      outline-offset: 2px;
    }

    .semaforo-label {
      font-size: 12px;
      opacity: .8;
      margin-top: 2px
    }

    .level-badge {
      color: #223976 !important;
      border: 1px solid rgba(0, 0, 0, .06);
    }

    .level-chip {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 6px;
      font-weight: 700;
      color: #223976;
      margin-right: 6px;
      border: 1px solid rgba(0, 0, 0, .06);
    }

    .nav-tabs .nav-link {
      display: flex;
      align-items: center;
      gap: 6px;
      transition: color .2s ease;
    }

    .nav-tabs .nav-link i {
      font-size: 16px;
      line-height: 1;
    }

    .nav-tabs .nav-link.active {}

    :root {
      --c-79abaf: #79abaf;
      --c-92b7b1: #92b7b1;
      --c-9fb8a9: #9fb8a9;
      --c-adbaa4: #adbaa4;
      --c-a8aa80: #a8aa80;
      --c-92a07e: #92a07e;
      --c-74846a: #74846a;

    }

    .semaforo-cell {
      height: 38px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 12px;
      color: #223976;
      background: #e9eef6;
      box-shadow: inset 0 0 0 1px rgba(0, 0, 0, .06);
    }

    .alb-n {
      background: var(--c-adbaa4);
    }

    .alb-1 {
      background: var(--c-9fb8a9);
    }

    .alb-2 {
      background: var(--c-92b7b1);
    }

    .alb-3 {
      background: var(--c-79abaf);
    }

    .cr-1 {
      background: var(--c-a8aa80);
    }

    .cr-2 {
      background: var(--c-92a07e);
    }

    .cr-3 {
      background: var(--c-74846a);
    }

    .semaforo-active {
      outline-offset: 2px;
      outline: 3px solid var(--color-gray-dark);
    }

    .level-badge {
      color: #223976 !important;
      border: 1px solid rgba(0, 0, 0, .06);
    }

    .level-chip {
      color: #223976;
      border: 1px solid rgba(0, 0, 0, .06);
    }

    .semaforo-labels,
    .semaforo-labels-3 {
      display: grid;
      gap: 8px;
      align-items: center;
      margin: 0 0 6px;
      text-align: center;
      font-weight: 700;
      font-size: 12px;
      color: #000;
    }

    .semaforo-labels {
      grid-template-columns: repeat(4, 80px);
    }

    .semaforo-labels-3 {
      grid-template-columns: repeat(3, 80px);
    }

    .semaforo-cell {
      color: transparent;
    }
  </style>
</head>

<body>
  <?php
  $idioma = strtoupper($_SESSION['idioma'] ?? 'ES');
  if (!in_array($idioma, ['EN', 'ES'])) {
    $idioma = 'EN';
  }
  $traducciones = include(PROJECT_ROOT . '/lang/' . $idioma . '.php'); // Cargamos traducciones
  $metrics = [
    'Albumin' => 'albumin',
    'Creatinine' => 'creatinine',
    'Albumin-to-Creatinine Ratio' => 'acr',
    'Serum Creatinine' => 'serum_creatinine',
    'Uric Acid Blood' => 'uric_acid_blood',
    'Bun Blood' => 'bun_blood',
    'EGFR' => 'egfr',
  ];
  ?>
  <div class="container-fluid">
    <a href="renal_function" class="" role="button">
      <button class="btn btn-back mb-3"><i class="mdi mdi-arrow-left"></i> <?= $traducciones['back'] ?></button>
    </a>

    <div class="row">
      <div class="col-xl-6">
        <div class="accordion custom-accordion" id="customAccordionRenal"></div>
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

  <script src="public/assets/js/logout.js"></script>
  <script>
    $(document).ready(function () {
      const recordId = <?php echo json_encode($_GET['id'] ?? 0); ?>;
      const lang = <?php echo json_encode($idioma); ?>;
      const $accordion = $('#customAccordionRenal');
      const metricsConfig = <?php echo json_encode($metrics); ?>;
      let biomarkerDefs = {};

      // --- AÑADIDO: Base URL ---
      const baseUrl = "<?= BASE_URL ?>";

      // --- MODIFICADO: Añadida clave 'tab_comments' ---
      const translations = {
        EN: {
          errorTitle: "Error", loadError: "Could not load biomarker data.", historyLoadError: "Could not load history.", noHistory: "Could not find historical data.",
          viewHistory: "View History", commentLabel: "Comment", referenceRange: "Reference Range", deficiency: "Deficiency", excess: "Excess", description: "Description",
          noData: "No data available", tab_range: "Range", tab_current: "Current", tab_over_time: "Over Time",
          noComments: "No specialist comments.",
          tab_comments: "<?= $traducciones['tab_comments'] ?? 'Specialist Comments' ?>" // <-- CORREGIDO
        },
        ES: {
          errorTitle: "Error", loadError: "No se pudieron cargar los datos del biomarcador.", historyLoadError: "No se pudo cargar el historial.", noHistory: "No se encontraron datos históricos.",
          viewHistory: "<?= $traducciones['viewHistory_renal_component'] ?? 'Ver Historial' ?>", commentLabel: "<?= $traducciones['commentLabel_renal_component'] ?? 'Comentario' ?>",
          referenceRange: "<?= $traducciones['referenceRange_renal_component'] ?? 'Rango de Referencia' ?>", deficiency: "<?= $traducciones['deficiency_renal_component'] ?? 'Deficiencia' ?>",
          excess: "<?= $traducciones['excess_renal_component'] ?? 'Exceso' ?>", description: "<?= $traducciones['description_renal_component'] ?? 'Descripción' ?>",
          noData: "No hay datos disponibles", tab_range: "<?= $traducciones['tab_range'] ?>", tab_current: "<?= $traducciones['tab_current'] ?>", tab_over_time: "<?= $traducciones['tab_over_time'] ?>",
          noComments: "<?= $traducciones['no_comments'] ?? 'No hay comentarios de especialistas.' ?>", // <-- CORREGIDO
          tab_comments: "<?= $traducciones['tab_comments'] ?? 'Comentarios' ?>" // <-- CORREGIDO
        }
      };
      const msg = translations[lang] || translations['EN'];

      // ... (Helpers: getHelpIcon, enableTooltips, calculatePercentage, normalizeLevel, albCrLevelClass, renderSemaforoCategorico - sin cambios) ...
      function getHelpIcon(fieldKey) {
        if (fieldKey !== 'albumin' && fieldKey !== 'creatinine') return '';
        const tip = (lang === 'ES')
          ? '¿Cómo leer? ALB a 50 s y CRE a 60 s; comparar con la etiqueta; registrar; ignorar cambios de color posteriores.'
          : 'How to read? Read ALB at 50 s and CRE at 60 s; compare with label; record; ignore later color changes.';
        return `
      <span class="ms-2 align-middle" data-bs-toggle="tooltip" data-bs-placement="top" title="${tip}">
        <i class="mdi mdi-help-circle-outline"></i>
      </span>`;
      }
      function enableTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
      }
      function calculatePercentage(value, maxReference) {
        if (maxReference <= 0) return "0.00";
        const scale = maxReference * 1.5;
        const percentage = (value / scale) * 100;
        return Math.max(0, Math.min(100, percentage)).toFixed(2);
      }
      function normalizeLevel(raw) {
        if (raw == null) return null;
        const s = String(raw).trim().toUpperCase();
        if (s === 'N' || s === 'NORMAL' || s === 'NEG' || s === 'NEGATIVE') return 'N';
        if (s === '1+' || s === '+1' || s === '1') return '1+';
        if (s === '2+' || s === '+2' || s === '2') return '2+';
        if (s === '3+' || s === '+3' || s === '3') return '3+';
        return null;
      }
      function albCrLevelClass(fieldKey, level) {
        if (fieldKey === 'albumin') {
          return level === 'N' ? 'alb-n' :
            level === '1+' ? 'alb-1' :
              level === '2+' ? 'alb-2' :
                level === '3+' ? 'alb-3' : '';
        } else {
          return level === '1+' ? 'cr-1' :
            level === '2+' ? 'cr-2' :
              level === '3+' ? 'cr-3' : '';
        }
      }
      function renderSemaforoCategorico(fieldKey, level, unit) {
        const isAlb = (fieldKey === 'albumin');
        const active = (lbl) => (level === lbl ? 'semaforo-active' : '');

        const labelsAlb = `
      <div class="semaforo-labels">
        <div class="d-flex flex-column"><span>N</span><span>(Normal)</span></div><div>1+</div><div>2+</div><div>3+</div>
      </div>`;
        const labelsCr = `
      <div class="semaforo-labels-3">
        <div>1+</div><div>2+</div><div>3+</div>
      </div>`;

        const albCells = `
      <div class="semaforo-cell alb-n ${active('N')}"></div>
      <div class="semaforo-cell alb-1 ${active('1+')}"></div>
      <div class="semaforo-cell alb-2 ${active('2+')}"></div>
      <div class="semaforo-cell alb-3 ${active('3+')}"></div>`;

        const crCells = `
      <div class="semaforo-cell cr-1 ${active('1+')}"></div>
      <div class="semaforo-cell cr-2 ${active('2+')}"></div>
      <div class="semaforo-cell cr-3 ${active('3+')}"></div>`;

        const gridClass = isAlb ? 'semaforo-grid' : 'semaforo-grid-3';
        const labelsRow = isAlb ? labelsAlb : labelsCr;
        const cellsRow = isAlb ? albCells : crCells;

        return `${labelsRow}<div class="${gridClass}">${cellsRow}</div>`;
      }

      // --- MODIFICADO: initializePage ---
      async function initializePage() {
        try {
          // --- MODIFICADO: URL de comentarios ---
          const [biomarkerInfoRes, commentsRes] = await Promise.all([
            $.ajax({ url: 'biomarkers/info', method: 'POST', dataType: 'json', data: { names: Object.keys(metricsConfig) } }),
            // Panel 4 (Renal Function)
            $.ajax({ url: `biomarker-comments/with-specialist/60819af9-0533-472c-9d5a-24a5df5a83f7/${recordId}`, method: 'GET', dataType: 'json' })
          ]);

          if (!biomarkerInfoRes.data || biomarkerInfoRes.data.length === 0) throw new Error(msg.loadError);
          biomarkerDefs = Object.fromEntries(biomarkerInfoRes.data.map(bm => [bm.name, bm]));

          // --- MODIFICADO: Mapeo de comentarios a Array ---
          const commentsMap = {};
          if (commentsRes && commentsRes.value === true && Array.isArray(commentsRes.data)) {
            for (const c of commentsRes.data) {
              const key = c.id_biomarker;
              if (key) {
                if (!commentsMap[key]) {
                  commentsMap[key] = []; // Inicializa array
                }
                commentsMap[key].push(c); // Añade objeto completo
              }
            }
          }

          const valuesResponse = await $.ajax({ url: `renal-function/${recordId}`, method: 'GET', dataType: 'json' });
          if (valuesResponse.error || !valuesResponse.data) throw new Error(msg.noData);

          renderAccordion(valuesResponse.data, biomarkerDefs, commentsMap);

        } catch (error) {
          console.error("Error al inicializar la página:", error);
          Swal.fire(msg.errorTitle, error.message || msg.loadError, 'error');
        }
      }

      // --- MODIFICADO: renderAccordion ---
      function renderAccordion(recordData, biomarkerDefs, comments) {
        $accordion.empty();
        const selectedField = new URLSearchParams(window.location.search).get('select');
        let isFirstItem = true;

        for (const [biomarkerName, fieldKey] of Object.entries(metricsConfig)) {
          const biomarker = biomarkerDefs[biomarkerName];
          if (!biomarker) continue;

          // ... (cálculos de isAlbOrCr, helpIcon, rawVal, hasValue, etc. - sin cambios) ...
          const isAlbOrCr = (fieldKey === 'albumin' || fieldKey === 'creatinine');
          const helpIcon = isAlbOrCr ? getHelpIcon(fieldKey) : '';
          const rawVal = recordData[fieldKey];
          let hasValue, valueNum, levelCat;
          if (isAlbOrCr) {
            levelCat = normalizeLevel(rawVal);
            hasValue = !!levelCat;
          } else {
            valueNum = parseFloat(rawVal);
            hasValue = rawVal !== null && rawVal !== '' && !isNaN(valueNum);
          }
          if (!hasValue) continue;

          const id = fieldKey.toLowerCase();
          const name = (lang === 'ES' ? (biomarker.name_es || biomarker.name) : biomarker.name);
          const min = parseFloat(biomarker.reference_min);
          const max = parseFloat(biomarker.reference_max);
          const unit = biomarker.unit;

          const inRange = !isAlbOrCr ? (valueNum >= min && valueNum <= max) : null;
          const deficiency = lang === 'ES' ? (biomarker.deficiency_es ?? biomarker.deficiency_label) : biomarker.deficiency_label;
          const excess = lang === 'ES' ? (biomarker.excess_es ?? biomarker.excess_label) : biomarker.excess_label;
          const description = lang === 'ES' ? (biomarker.description_es ?? biomarker.description) : biomarker.description;

          // --- ELIMINADO: commentLine ---
          // const bmId = biomarker.biomarker_id || biomarker.id || biomarker.biomarkerId;
          // const commentText = comments[bmId] || msg.noComments;
          // const commentLine = `<div class="barra-valor"><b>${msg.commentLabel}</b><br>${commentText}</div>`;

          const isSelected = selectedField ? (fieldKey === selectedField) : isFirstItem;

          // ... (lógica de rangeContent, currentBadge, overtimeChip - sin cambios) ...
          let rangeContent = '';
          if (isAlbOrCr) {
            rangeContent = `
          ${renderSemaforoCategorico(fieldKey, levelCat, unit)}
          <div class="barra-valor"><b>${msg.referenceRange}</b><br>${fieldKey === 'albumin' ? 'N, 1+, 2+, 3+' : '1+, 2+, 3+'}</div>
        `;
          } else {
            const pct = calculatePercentage(valueNum, max);
            const minPct = calculatePercentage(min, max);
            const maxPct = calculatePercentage(max, max);
            rangeContent = `
          <div class="barra-container text-left" style="--minPorcentaje:${minPct};--maxPorcentaje:${maxPct};">
            <div class="burbuja ${inRange ? 'green-item' : 'red-item'}" style="left:${pct}%;">${valueNum} ${unit}</div>
          </div>
          <div class="barra-valor"><b>${msg.referenceRange}</b><br>${min}–${max} ${unit}</div>
        `;
          }
          const currentBadge = isAlbOrCr
            ? `<div class="rectangulo-valor level-badge ${albCrLevelClass(fieldKey, levelCat)}">${levelCat}</div>`
            : `<div class="rectangulo-valor ${inRange ? 'green-item' : 'red-item'}">${valueNum} ${unit}</div>`;
          const overtimeChip = isAlbOrCr
            ? `<span class="level-chip ${albCrLevelClass(fieldKey, levelCat)}">${levelCat}</span>`
            : `<span class="${inRange ? 'green-label' : 'red-label'}">${valueNum} ${unit}</span>`;


          // --- NUEVO: Lógica de Comentarios para el tab ---
          const bmId = biomarker.biomarker_id || biomarker.id || biomarker.biomarkerId;
          const commentsList = (bmId && comments[bmId]) ? comments[bmId] : [];
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
            commentsHtml = `<p class="text-muted">${msg.noComments}</p>`;
          }
          // --- FIN NUEVO ---


          // --- MODIFICADO: Añadido tab/panel de comentarios, eliminado commentLine, corregidos IDs y añadido text-gray ---
          const accordionItemHTML = `
        <div class="card mb-0">
          <div class="card-header" style="background-color:#e8fcfd;">
            <h5 class="m-0 position-relative">
              <a class="custom-accordion-title d-block ${isSelected ? '' : 'collapsed'}" style="color:#223976;"
                 data-bs-toggle="collapse" href="#collapse-${id}" aria-expanded="${isSelected}" aria-controls="collapse-${id}">
                 ${name} <i class="mdi mdi-chevron-down accordion-arrow"></i>
              </a>
            </h5>
          </div>
          <div id="collapse-${id}" class="collapse ${isSelected ? 'show' : ''}" data-bs-parent="#customAccordionRenal">
            <div class="card-body">
              <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item"><a class="nav-link text-gray active" data-bs-toggle="tab" href="#${id}-home"><i class="mdi mdi-ruler"></i>${msg.tab_range}</a></li>
                <li class="nav-item"><a class="nav-link text-gray" data-bs-toggle="tab" href="#${id}-profile"><i class="mdi mdi-gauge"></i>${msg.tab_current}</a></li>
                <li class="nav-item"><a class="nav-link text-gray" data-bs-toggle="tab" href="#${id}-messages"><i class="mdi mdi-chart-line"></i>${msg.tab_over_time}</a></li>
                <li class="nav-item"><a class="nav-link text-gray" data-bs-toggle="tab" href="#${id}-comments"><i class="mdi mdi-comment-text-multiple-outline"></i>${msg.tab_comments}</a></li>
              </ul>
              <div class="tab-content pt-3">
                <div class="tab-pane fade show active" id="${id}-home">
                  ${rangeContent}
                  <div class="barra-valor"><b>${msg.deficiency}</b><br>${deficiency}</div>
                  <div class="barra-valor"><b>${msg.excess}</b><br>${excess}</div>
                  <div class="barra-valor"><b>${msg.description}</b><br>${description}</div>
                </div>
                <div class="tab-pane fade" id="${id}-profile">
                  <h5>${name}${helpIcon} ${currentBadge}</h5>
                  <div class="descripcion-biomarcador">
                    <h6>${msg.referenceRange}</h6><p>${isAlbOrCr ? (fieldKey === 'albumin' ? 'N, 1+, 2+, 3+' : '1+, 2+, 3+') : `${min}–${max} ${unit}`}</p>
                    <h6>${msg.deficiency}</h6><p>${deficiency}</p>
                    <h6>${msg.excess}</h6><p>${excess}</p>
                    <h6>${msg.description}</h6><p>${description}</p>
                  </div>
                </div>
                <div class="tab-pane fade" id="${id}-messages">
                  <h5>${name}${helpIcon}</h5>
                  <p>${overtimeChip} – ${msg.referenceRange} ${isAlbOrCr ? (fieldKey === 'albumin' ? 'N, 1+, 2+, 3+' : '1+, 2+, 3+') : `${min}–${max} ${unit}`}</p>
                  <h6>${msg.deficiency}</h6><p>${deficiency}</p>
                  <h6>${msg.excess}</h6><p>${excess}</p>
                  <h6>${msg.description}</h6><p>${description}</p>
                  <div class="history-button" data-type="${fieldKey}">
                    <span class="btn btn-view action-icon"><i class="mdi mdi-eye-outline"></i></span>
                    <span>${msg.viewHistory}</span>
                  </div>
                </div>
                <div class="tab-pane fade" id="${id}-comments">
                  ${commentsHtml}
                </div>
              </div>
            </div>
          </div>
        </div>`;
          $accordion.append(accordionItemHTML);
          isFirstItem = false;
        }

        enableTooltips();
      }

      // ... (Listener de .history-button no estaba, así que no lo añado) ...

      initializePage();
    });
  </script>

</body>

</html>