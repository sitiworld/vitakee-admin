<!DOCTYPE html>
<html lang="en">

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

    /* Tabs internos: icono + negrita cuando activa */
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

<body>

  <?php
  // --- LÓGICA PHP PERMANECE IGUAL ---
  $idioma = strtoupper($_SESSION['idioma'] ?? 'ES');
  if (!in_array($idioma, ['EN', 'ES'])) {
    $idioma = 'EN';
  }
  $archivo_idioma = PROJECT_ROOT . '/lang/' . $idioma . '.php';
  if (file_exists($archivo_idioma)) {
    $traducciones = include $archivo_idioma;
  } else {
    die("Archivo de idioma no encontrado: $archivo_idioma");
  }
  ?>

  <div class="container-fluid">
    <a href="body_composition" class="" role="button"><button class="btn btn-back mb-3"><i
          class="mdi mdi-arrow-left"></i> <?= $traducciones['back'] ?></button></a>

    <div class="row">
      <div class="col-xl-6 p-2">
        <div class="accordion custom-accordion" id="customAccordionBody">
        </div>

        <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">
                  <?= $traducciones['history'] ?>
                </h5>
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
                  <tbody id="history-table-body">
                  </tbody>
                </table>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal"> <i class=" mdi mdi-cancel"></i>
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
      const lang = "<?php echo $idioma; ?>";
      const recordId = <?php echo json_encode($_GET['id'] ?? 0); ?>;
      const sex_biological = <?php echo json_encode($_SESSION['sex_biological'] ?? 'm'); ?>;
      const $accordion = $('#customAccordionBody');

      // --- MODIFICADO: Añadida URL base para imágenes ---
      const baseUrl = "<?= BASE_URL ?>"; // <-- Inyecta la variable PHP

      // Traducciones para JS
      const msg = {
        errorTitle: "<?= $traducciones['errorTitle_body_componente'] ?>",
        loadDataFail: "<?= $traducciones['loadDataFail_body_componente'] ?>",
        missingHistoryData: "<?= $traducciones['missingHistoryData_body_componente'] ?>",
        noHistoryTitle: "<?= $traducciones['noHistoryTitle_body_componente'] ?>",
        noHistoryText: "<?= $traducciones['noHistoryText_body_componente'] ?>",
        referenceRangeLabel: "<?= $traducciones['referenceRangeLabel_body_componente'] ?>",
        deficiencyLabel: "<?= $traducciones['deficiencyLabel_body_componente'] ?>",
        excessLabel: "<?= $traducciones['excessLabel_body_componente'] ?>",
        descriptionLabel: "<?= $traducciones['descriptionLabel_body_componente'] ?>",
        viewHistoryText: "<?= $traducciones['viewHistoryText_body_componente'] ?>",
        tab_range: "<?= $traducciones['tab_range'] ?>",
        tab_current: "<?= $traducciones['tab_current'] ?>",
        tab_over_time: "<?= $traducciones['tab_over_time'] ?>",
        // --- MODIFICADO: Nuevas claves ---
        tab_comments: "<?= $traducciones['tab_comments'] ?? 'Specialist Comments' ?>",
        commentLabel: lang === 'ES' ? 'Comentario' : 'Comment',
        noComments: lang === 'ES' ? 'No hay comentarios de especialistas.' : 'No specialist comments.'
      };

      // --- Helpers ---
      const slug = s => String(s).toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');

      // Mapeo de campos (sin cambios)
      let fieldMap = {
        bmi: 'Body Mass Index (BMI)',
        water_pct: 'Total Body Water Percentage',
        resting_metabolism: 'Resting Metabolic Rate (RMR)',
        visceral_fat: 'Visceral Fat Level',
        body_age: 'Body Age'
      };
      if (sex_biological === 'm') {
        fieldMap.body_fat_pct = 'Body Fat Percentage - Male';
        fieldMap.muscle_pct = 'Muscle Mass Percentage - Male';
      } else if (sex_biological === 'f') {
        fieldMap.body_fat_pct = 'Body Fat Percentage - Female';
        fieldMap.muscle_pct = 'Muscle Mass Percentage - Female';
      } else if (sex_biological === 'M') {
        fieldMap.body_fat_pct = ['Body Fat Percentage - Male', 'Body Fat Percentage - Female'];
        fieldMap.muscle_pct = ['Muscle Mass Percentage - Male', 'Muscle Mass Percentage - Female'];
      }

      const biomarkerNames = [];
      Object.values(fieldMap).forEach(v => Array.isArray(v) ? biomarkerNames.push(...v) : biomarkerNames.push(v));

      function calculatePercentage(value, maxReference) {
        if (maxReference <= 0) return "0.00";
        const scale = maxReference * 1.5;
        const percentage = (value / scale) * 100;
        return Math.max(0, Math.min(100, percentage)).toFixed(2);
      }

      // --- 2. LÓGICA PRINCIPAL (async/await) ---
      async function initializePage() {
        try {
          // Peticiones en paralelo (sin cambios)
          const [biomarkerInfoRes, commentsRes] = await Promise.all([
            $.ajax({
              url: 'biomarkers/info',
              method: 'POST',
              dataType: 'json',
              data: { names: biomarkerNames }
            }),
            $.ajax({
              url: `biomarker-comments/with-specialist/81054d57-92c9-4df8-a6dc-51334c1d82c4/${recordId}`,
              method: 'GET',
              dataType: 'json'
            })
          ]);

          if (!biomarkerInfoRes.data || biomarkerInfoRes.data.length === 0) {
            throw new Error("No se pudo cargar la información de los biomarcadores.");
          }

          const biomarkerData = {};
          biomarkerInfoRes.data.forEach(bm => biomarkerData[bm.name] = bm);

          // --- MODIFICADO: Mapa de comentarios ---
          // Ahora agrupa los comentarios en un array por id_biomarker
          const commentsMap = {};
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
          // --- FIN MODIFICACIÓN ---


          const valuesResponse = await $.ajax({
            url: `body-compositions/${recordId}`,
            method: 'GET',
            dataType: 'json'
          });
          if (valuesResponse.error) throw new Error(valuesResponse.error);

          renderAccordion(valuesResponse.data, biomarkerData, commentsMap);

        } catch (error) {
          console.error("Error al inicializar la página:", error);
          Swal.fire(msg.errorTitle, (error.message || msg.loadDataFail), 'error');
        }
      }

      // --- 3. RENDER DEL ACORDEÓN ---
      function renderAccordion(recordData, biomarkerDefinitions, commentsMap) {
        $accordion.empty();

        const urlParams = new URLSearchParams(window.location.search);
        const selectedField = urlParams.get('select');
        let isFirstItem = true;

        for (const [fieldKey, nameOrList] of Object.entries(fieldMap)) {
          const value = parseFloat(recordData[fieldKey]);
          if (!value || isNaN(value)) continue;

          const names = Array.isArray(nameOrList) ? nameOrList : [nameOrList];
          for (const biomarkerName of names) {
            const biomarker = biomarkerDefinitions[biomarkerName];
            if (!biomarker) continue;

            const suffix = slug(biomarkerName);
            const id = `${fieldKey}-${suffix}`;

            const label = (lang === 'ES' ? (biomarker.name_es || biomarker.name) : biomarker.name);
            const unit = biomarker.unit === '%' ? '%' : ` ${biomarker.unit}`;

            let min = parseFloat(biomarker.reference_min);
            let max = parseFloat(biomarker.reference_max);
            let refLabel = `${biomarker.reference_min} - ${biomarker.reference_max}${unit}`;
            if (fieldKey === 'body_age') {
              min = 0;
              max = parseInt(recordData.age, 10);
              refLabel = `≤ ${max} ${lang === 'ES' ? 'años' : 'years'}`;
            }

            const pct = calculatePercentage(value, max);
            const minPct = calculatePercentage(min, max);
            const maxPct = calculatePercentage(max, max);

            const inRange = (value >= min && value <= max);
            const valueColorClass = inRange ? 'green-item' : 'red-item';
            const labelColorClass = inRange ? 'green-label' : 'red-label';

            const deficiencyText = lang === 'ES' ? (biomarker.deficiency_es || biomarker.deficiency_label) : biomarker.deficiency_label;
            const excessText = lang === 'ES' ? (biomarker.excess_es || biomarker.excess_label) : biomarker.excess_label;
            const descriptionText = lang === 'ES' ? (biomarker.description_es || biomarker.description) : biomarker.description;

            // --- MODIFICADO: Lógica de Comentarios para el nuevo tab ---
            const bmId = biomarker.biomarker_id || biomarker.id || biomarker.biomarkerId;
            const commentsList = (bmId && commentsMap[bmId]) ? commentsMap[bmId] : [];
            let commentsHtml = '';

            if (commentsList.length > 0) {
              commentsList.forEach(c => {
                const specName = `${c.specialist_first_name || ''} ${c.specialist_last_name || ''}`;
                const specTitle = c.specialist_title || '';

                let imageUrl = '';
                if (c.specialist_image) {
                  // Genera una URL única para evitar caché si la imagen cambia
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

            const commentsTabPane = `
              <div class="tab-pane fade" id="${id}-comments" role="tabpanel">
                ${commentsHtml}
              </div>
            `;
            // --- FIN MODIFICACIÓN ---

            const isSelected = selectedField ? (fieldKey === selectedField) : isFirstItem;

            // --- MODIFICADO: Eliminados ${commentLine} y añadido nuevo tab/pane ---
            const accordionItemHTML = `
            <div class="card mb-0">
              <div class="card-header" id="heading-${id}" style="background-color: #e8fcfd;">
                <h5 class="m-0 position-relative">
                  <a class="custom-accordion-title d-block ${isSelected ? '' : 'collapsed'}" style="color: #223976;"
                     data-bs-toggle="collapse" href="#collapse-${id}"
                     aria-expanded="${isSelected ? 'true' : 'false'}" aria-controls="collapse-${id}">
                    ${label}
                    <i class="mdi mdi-chevron-down accordion-arrow"></i>
                  </a>
                </h5>
              </div>
              <div id="collapse-${id}" class="collapse ${isSelected ? 'show' : ''}" aria-labelledby="heading-${id}" data-bs-parent="#customAccordionBody">
                <div class="card-body">
                  <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" data-bs-toggle="tab" href="#${id}-home" role="tab" aria-selected="true">
                        <i class="mdi mdi-ruler"></i>
                        ${msg.tab_range}
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" data-bs-toggle="tab" href="#${id}-profile" role="tab" aria-selected="false" tabindex="-1">
                        <i class="mdi mdi-gauge"></i>
                        ${msg.tab_current}
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" data-bs-toggle="tab" href="#${id}-messages" role="tab" aria-selected="false" tabindex="-1">
                        <i class="mdi mdi-chart-line"></i>
                        ${msg.tab_over_time}
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" data-bs-toggle="tab" href="#${id}-comments" role="tab" aria-selected="false" tabindex="-1">
                        <i class="mdi mdi-comment-text-multiple-outline"></i>
                        ${msg.tab_comments}
                      </a>
                    </li>
                  </ul>

                  <div class="tab-content pt-3">
                    <div class="tab-pane fade show active" id="${id}-home" role="tabpanel">
                      <h5 class="mb-4">${label}</h5>
                      <div class="barra-container" style="--minPorcentaje:${minPct};--maxPorcentaje:${maxPct};">
                        <div class="burbuja ${valueColorClass}" style="left:${pct}%;">${value}${unit}</div>
                      </div>
                      <div class="barra-valor"><b>${msg.referenceRangeLabel}</b><br>${refLabel}</div>
                      <div class="barra-valor"><b>${msg.deficiencyLabel}</b><br>${deficiencyText}</div>
                      <div class="barra-valor"><b>${msg.excessLabel}</b><br>${excessText}</div>
                      <div class="barra-valor"><b>${msg.descriptionLabel}</b><br>${descriptionText}</div>
                    </div>

                    <div class="tab-pane fade" id="${id}-profile" role="tabpanel">
                      <div class="seccion-biomarcador">
                        <h5>${label} <div class="rectangulo-valor ${valueColorClass}">${value}${unit}</div></h5>
                        <div class="descripcion-biomarcador">
                          <h6>${msg.referenceRangeLabel}</h6><p>${refLabel}</p>
                          <h6>${msg.deficiencyLabel}</h6><p>${deficiencyText}</p>
                          <h6>${msg.excessLabel}</h6><p>${excessText}</p>
                          <h6>${msg.descriptionLabel}</h6><p>${descriptionText}</p>
                        </div>
                      </div>
                    </div>

                    <div class="tab-pane fade" id="${id}-messages" role="tabpanel">
                      <div class="seccion-biomarcador">
                        <h5>${label}</h5>
                        <div class="valor-reference">
                          <p class="py-1 px-2 rounded">
                            <span class="${labelColorClass}">${value}${unit}</span> – ${msg.referenceRangeLabel} ${refLabel}
                          </p>
                        </div>
                        <div class="descripcion-biomarcador">
                          <h6>${msg.deficiencyLabel}</h6><p>${deficiencyText}</p>
                          <h6>${msg.excessLabel}</h6><p>${excessText}</p>
                          <h6>${msg.descriptionLabel}</h6><p>${descriptionText}</p>
                        </div>
                        <div class="history-button" data-rec-id="${recordId}" data-type="${fieldKey}" data-label="${label}">
                          <span class="btn btn-view action-icon"><i class="mdi mdi-eye-outline"></i></span>
                          <span>${msg.viewHistoryText}</span>
                        </div>
                      </div>
                    </div>
                    
                    ${commentsTabPane}

                  </div> </div>
              </div>
            </div>`;
            $accordion.append(accordionItemHTML);
            isFirstItem = false;
          }
        }
      }

      // --- 4. HISTORIAL (Sin cambios) ---
      $(document).on('click', '.history-button', async function () {
        const recId = $(this).data('rec-id');
        const field = $(this).data('type');
        const label = $(this).data('label');

        if (!recId || !field) {
          return Swal.fire(msg.errorTitle, msg.missingHistoryData, 'error');
        }
        try {
          const response = await $.ajax({
            url: `body-compositions/history/${recId}/${field}`,
            method: 'GET',
            dataType: 'json'
          });

          if (response.value === true && Array.isArray(response.data) && response.data.length > 0) {
            const rows = response.data.map(row => `<tr><td>${row.date}</td><td>${row.value}</td></tr>`).join('');
            const historyTitle = lang === 'ES' ? `Historial de ${label}` : `${label} History`;

            $('#historyModalLabel').text(historyTitle);
            $('#history-table-body').html(rows);
            new bootstrap.Modal($('#historyModal')[0]).show();
          } else {
            Swal.fire(msg.noHistoryTitle, msg.noHistoryText, 'info');
          }
        } catch (error) {
          console.error('Error al cargar historial:', error);
          Swal.fire(msg.errorTitle, msg.loadDataFail, 'error');
        }
      });

      // --- 5. INICIALIZACIÓN ---
      initializePage();
    });
  </script>

</body>

</html>