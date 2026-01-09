<head>

  <style>
    /* ESTILOS CSS PERMANECEN IGUALES */
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

    /* Tabs internos: icono a la izquierda y negrita en activa */
    .nav-tabs .nav-link {
      display: flex;
      align-items: center;
      gap: 6px;
      /* espacio entre icono y texto */
      font-weight: 500;
      /* normal */
      transition: color .2s ease;
    }

    .nav-tabs .nav-link i {
      font-size: 16px;
      line-height: 1;
    }

    .nav-tabs .nav-link.active {
      font-weight: 700;
      /* negrita cuando está activa */
    }
  </style>
</head>

<div class="container-fluid">
  <a href="lipid_profile" class="" role="button"><button class="btn btn-back mb-3"><i class="mdi mdi-arrow-left"></i>
      <?= $traducciones['back'] ?></button></a>
  <div class="row">
    <div class="col-xl-6">
      <div class="accordion custom-accordion" id="customAccordionLipid">
        <?php
        // ANÁLISIS CLAVE: Este bloque PHP ya crea la estructura del acordeón.
        // El JavaScript no debe borrarla, sino rellenarla.
        $metrics = [
          'LDL Cholesterol' => 'ldl',
          'HDL Cholesterol' => 'hdl',
          'Total Cholesterol' => 'total_cholesterol',
          'Triglycerides' => 'triglycerides',
          'Non-HDL Cholesterol' => 'non_hdl',
        ];
        $first = true;
        foreach ($metrics as $label => $field):
          $id = str_replace('_', '-', $field);
          ?>
          <div class="card mb-0">
            <div class="card-header" id="heading-<?= $id ?>" style="background-color: #e8fcfd;">
              <h5 class="m-0 position-relative">
                <a class="custom-accordion-title <?= $first ? '' : 'collapsed' ?> d-block" data-bs-toggle="collapse"
                  href="#collapse-<?= $id ?>" aria-expanded="<?= $first ? 'true' : 'false' ?>"
                  aria-controls="collapse-<?= $id ?>" style="color: #223976;">
                  <?= $traducciones[$label] ?? $label ?> <i class="mdi mdi-chevron-down accordion-arrow"></i>
                </a>
              </h5>
            </div>
            <div id="collapse-<?= $id ?>" class="collapse <?= $first ? 'show' : '' ?>"
              aria-labelledby="heading-<?= $id ?>" data-bs-parent="#customAccordionLipid">
              <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active text-gray" data-bs-toggle="tab" href="#<?= $id ?>-home" role="tab"
                      aria-selected="true">
                      <i class="mdi mdi-ruler"></i>
                      <?= $traducciones['tab_range'] ?>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link text-gray" data-bs-toggle="tab" href="#<?= $id ?>-profile" role="tab"
                      aria-selected="false" tabindex="-1">
                      <i class="mdi mdi-gauge"></i>
                      <?= $traducciones['tab_current'] ?>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link text-gray" data-bs-toggle="tab" href="#<?= $id ?>-messages" role="tab"
                      aria-selected="false" tabindex="-1">
                      <i class="mdi mdi-chart-line"></i>
                      <?= $traducciones['tab_over_time'] ?>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link text-gray" data-bs-toggle="tab" href="#<?= $id ?>-comments" role="tab"
                      aria-selected="false" tabindex="-1">
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

      <div class="modal fade" id="history-modal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="historyModalLabel"><?= $traducciones['lipid_records_history'] ?></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col"><?= $traducciones['date'] ?></th>
                    <th scope="col"><?= $traducciones['value'] ?></th>
                  </tr>
                </thead>
                <tbody id="history-table-body"></tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-cancel" data-bs-dismiss="modal"> <i class="mdi mdi-cancel"></i>
                <?= $traducciones['close'] ?></button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script src="public/assets/js/logout.js"></script>
<script>
  $(document).ready(function () {

    // --- 1. CONFIGURACIÓN Y CONSTANTES ---
    const recordId = <?php echo json_encode($_GET['id'] ?? 0); ?>;
    const sex_biological = <?php echo json_encode($_SESSION['sex_biological'] ?? 'm'); ?>;
    const lang = "<?php echo $_SESSION['idioma']; ?>";

    // --- AÑADIDO: Base URL ---
    const baseUrl = "<?= BASE_URL ?>";

    const biomarkerDefinitions = {};

    // --- MODIFICADO: Añadida clave 'tab_comments' ---
    const translations = {
      EN: {
        errorTitle: "Error",
        loadError: "Could not load biomarker data.",
        historyLoadError: "Could not load history.",
        noHistory: "No historical data found for this biomarker.",
        viewHistory: "View History",
        commentLabel: "Comment",
        refRangeLabel: "Reference Range",
        deficiencyLabel: "Deficiency",
        excessLabel: "Excess",
        descriptionLabel: "Description",
        noData: "No data available",
        noComments: "No specialist comments.",
        tab_comments: "Specialist Comments"
      },
      ES: {
        errorTitle: "Error",
        loadError: "No se pudieron cargar los datos del biomarcador.",
        historyLoadError: "No se pudo cargar el historial.",
        noHistory: "No se encontraron datos históricos para este biomarcador.",
        viewHistory: "<?= $traducciones['viewHistory_lipid_component'] ?>",
        commentLabel: "<?= $traducciones['commentLabel_lipid_component'] ?>",
        refRangeLabel: "<?= $traducciones['refRangeLabel_lipid_component'] ?>",
        deficiencyLabel: "<?= $traducciones['deficiencyLabel_lipid_component'] ?>",
        excessLabel: "<?= $traducciones['excessLabel_lipid_component'] ?>",
        descriptionLabel: "<?= $traducciones['descriptionLabel_lipid_component'] ?>",
        noData: "No hay datos disponibles",
        noComments: "No hay comentarios de especialistas.",
        tab_comments: "<?= $traducciones['tab_comments'] ?? 'Comentarios' ?>"
      }
    };
    const msg = translations[lang];

    const fieldMap = {
      ldl: 'LDL Cholesterol',
      hdl: sex_biological === 'm' ? 'HDL Cholesterol - Male' : 'HDL Cholesterol - Female',
      total_cholesterol: 'Total Cholesterol',
      triglycerides: 'Triglycerides',
      non_hdl: 'Non-HDL Cholesterol'
    };

    function calculatePercentage(value, maxReference) {
      if (maxReference <= 0) return "0.00";
      const scale = maxReference * 1.5;
      const percentage = (value / scale) * 100;
      return Math.max(0, Math.min(100, percentage)).toFixed(2);
    }

    // --- 2. LÓGICA PRINCIPAL ASÍNCRONA ---
    async function initializePage() {
      try {
        // --- MODIFICADO: URL de comentarios ---
        const [biomarkerInfoRes, commentsRes] = await Promise.all([
          $.ajax({
            url: 'biomarkers/info',
            method: 'POST',
            dataType: 'json',
            data: { names: Object.values(fieldMap) }
          }),
          $.ajax({
            // <- Usa el panel id (Lipid Profile) y el endpoint 'with-specialist'
            url: `biomarker-comments/with-specialist/e6861593-7327-4f63-9511-11d56f5398dc/${recordId}`,
            method: 'GET',
            dataType: 'json'
          })
        ]);

        if (!biomarkerInfoRes.data || biomarkerInfoRes.data.length === 0) {
          throw new Error(msg.loadError);
        }

        biomarkerInfoRes.data.forEach(bm => {
          biomarkerDefinitions[bm.name] = bm;
        });

        // --- MODIFICADO: Mapeo de comentarios a Array ---
        const commentsMap = {};
        if (commentsRes && commentsRes.value === true && Array.isArray(commentsRes.data)) {
          for (const c of commentsRes.data) {
            const k = c.id_biomarker;
            if (k) {
              if (!commentsMap[k]) {
                commentsMap[k] = []; // Inicializa array
              }
              commentsMap[k].push(c); // Añade objeto completo
            }
          }
        }

        // Cargar valores (sin cambios)
        const valuesResponse = await $.ajax({
          url: `lipid-profile/${recordId}`,
          method: 'GET',
          dataType: 'json'
        });

        if (valuesResponse.error || !valuesResponse.data) {
          throw new Error(msg.noData);
        }

        populateAccordionPanels(valuesResponse.data, biomarkerDefinitions, commentsMap);
        handleAccordionFromUrl();

      } catch (error) {
        console.error("Error al inicializar la página:", error);
        Swal.fire(msg.errorTitle, error.message || msg.loadError, 'error');
      }
    }

    // --- 3. FUNCIÓN DE RENDERIZADO ---
    // --- MODIFICADO: Eliminado commentLine, añadido panel de comentarios ---
    function populateAccordionPanels(recordData, biomarkerDefs, comments) {
      for (const [fieldKey, biomarkerName] of Object.entries(fieldMap)) {
        const value = parseFloat(recordData[fieldKey]);
        const biomarker = biomarkerDefs[biomarkerName];

        const id = fieldKey.replace(/_/g, '-');

        // Si el valor es 0, significa que no se practicó.
        // Ocultar el 'card' padre y saltar al siguiente biomarcador.
        if (!value || value === 0) {
          // El PHP crea el card con un header 'heading-...'
          // Apuntamos a ese header y ocultamos su 'card' padre.
          $(`#heading-${id}`).closest('.card.mb-0').hide();
          continue; // No procesar este biomarcador
        }

        if (!biomarker || isNaN(value)) continue;

        // ... (cálculos de id, label, unit, min, max, pct, colors, etc. - sin cambios) ...

        const label = lang === 'ES' ? (biomarker.name_es || biomarker.name) : biomarker.name;
        const unit = biomarker.unit;

        const min = parseFloat(biomarker.reference_min);
        const max = parseFloat(biomarker.reference_max);

        const pct = calculatePercentage(value, max);
        const minPct = calculatePercentage(min, max);
        const maxPct = calculatePercentage(max, max);

        const inRange = (value >= min && value <= max);
        const valueColorClass = inRange ? 'green-item' : 'red-item';
        const labelColorClass = inRange ? 'green-label' : 'red-label';

        const deficiencyText = lang === 'ES' ? (biomarker.deficiency_es ?? biomarker.deficiency_label) : biomarker.deficiency_label;
        const excessText = lang === 'ES' ? (biomarker.excess_es ?? biomarker.excess_label) : biomarker.excess_label;
        const descriptionText = lang === 'ES' ? (biomarker.description_es ?? biomarker.description) : biomarker.description;
        const refRange = `${min}–${max} ${unit}`;

        // --- ELIMINADO: commentLine ---
        // const bmId = ...
        // const commentText = ...
        // const commentLine = ...

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


        // --- MODIFICADO: Eliminado ${commentLine} de las pestañas ---
        // Pestaña 1: Range
        $(`#${id}-home`).html(`
          <h5 class="mb-4">${label}</h5>
          <div class="barra-container" style="--minPorcentaje:${minPct};--maxPorcentaje:${maxPct};">
            <div class="burbuja ${valueColorClass}" style="left:${pct}%;">${value} ${unit}</div>
          </div>
          <div class="barra-valor"><b>${msg.refRangeLabel}</b><br>${refRange}</div>
          <div class="barra-valor"><b>${msg.deficiencyLabel}</b><br>${deficiencyText}</div>
          <div class="barra-valor"><b>${msg.excessLabel}</b><br>${excessText}</div>
          <div class="barra-valor"><b>${msg.descriptionLabel}</b><br>${descriptionText}</div>
        `);

        // Pestaña 2: Current
        $(`#${id}-profile`).html(`
          <div class="seccion-biomarcador">
            <h5>${label}<div class="rectangulo-valor ${valueColorClass}">${value} ${unit}</div></h5>
            <div class="descripcion-biomarcador">
              <h6>${msg.refRangeLabel}</h6><p>${refRange}</p>
              <h6>${msg.deficiencyLabel}</h6><p>${deficiencyText}</p>
              <h6>${msg.excessLabel}</h6><p>${excessText}</p>
              <h6>${msg.descriptionLabel}</h6><p>${descriptionText}</p>
            </div>
          </div>
        `);

        // Pestaña 3: Over Time (Historial)
        $(`#${id}-messages`).html(`
          <div class="seccion-biomarcador">
            <h5>${label}</h5>
            <div class="valor-reference">
              <p class="px-2 py-1 rounded">
                <span class="${labelColorClass}">${value} ${unit}</span> – ${msg.refRangeLabel} ${refRange}
              </p>
            </div>
            <div class="descripcion-biomarcador">
              <h6>${msg.deficiencyLabel}</h6><p>${deficiencyText}</p>
              <h6>${msg.excessLabel}</h6><p>${excessText}</p>
              <h6>${msg.descriptionLabel}</h6><p>${descriptionText}</p>
            </div>
            <div class="history-button" data-rec-id="${recordId}" data-type="${fieldKey}">
              <span class="btn btn-view action-icon"><i class="mdi mdi-eye-outline"></i></span>
              <span>${msg.viewHistory}</span>
            </div>
          </div>
        `);

        // --- NUEVO: Inyectar HTML en el tab de comentarios ---
        $(`#${id}-comments`).html(commentsHtml);
      }
    }

    // ... (handleAccordionFromUrl y listener de .history-button - sin cambios) ...
    function handleAccordionFromUrl() {
      const urlParams = new URLSearchParams(window.location.search);
      const selectedField = urlParams.get('select');

      if (selectedField && fieldMap.hasOwnProperty(selectedField)) {
        $('#customAccordionLipid .collapse.show').removeClass('show');
        $('#customAccordionLipid .custom-accordion-title[aria-expanded="true"]')
          .addClass('collapsed')
          .attr('aria-expanded', 'false');

        const targetId = selectedField.replace(/_/g, '-');
        $(`#collapse-${targetId}`).addClass('show');
        $(`a[href="#collapse-${targetId}"]`)
          .removeClass('collapsed')
          .attr('aria-expanded', 'true');
      }
    }

    $(document).on('click', '.history-button', async function () {
      const recId = $(this).data('rec-id');
      const field = $(this).data('type');

      if (!recId || !field) return;

      try {
        const response = await $.ajax({
          url: `lipid-profile/history/${recId}/${field}`,
          method: 'GET',
          dataType: 'json'
        });

        if (response.value === true && Array.isArray(response.data) && response.data.length > 0) {
          const rows = response.data.map(row => `<tr><td>${row.date}</td><td>${row.value}</td></tr>`).join('');
          const biomarkerName = fieldMap[field];
          const biomarker = biomarkerDefinitions[biomarkerName];

          const historyTitle = lang === 'ES'
            ? `Historial de ${biomarker?.name_es || biomarkerName}`
            : `History of ${biomarkerName}`;

          $('#historyModalLabel').text(historyTitle);
          $('#history-table-body').html(rows);
          new bootstrap.Modal($('#history-modal')[0]).show();
        } else {
          Swal.fire(msg.noData, msg.noHistory, 'info');
        }
      } catch (error) {
        console.error('Error al cargar historial:', error);
        Swal.fire(msg.errorTitle, msg.historyLoadError, 'error');
      }
    });

    // --- 5. INICIALIZACIÓN ---
    initializePage();
  });
</script>