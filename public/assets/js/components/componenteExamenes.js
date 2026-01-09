/**
 * @file Contiene la función para renderizar el detalle de un examen en un modal con acordeones.
 * Este componente es reutilizable y se encarga de generar dinámicamente el HTML
 * a partir de los datos de un registro de examen.
 */

// -----------------------------------------------------------------------------
// DEPENDENCIAS NECESARIAS
// -----------------------------------------------------------------------------
// Para que esta función opere correctamente, tu proyecto debe incluir:
//
// 1. jQuery: Para manipulación del DOM.
//    - Se utiliza para seleccionar y modificar elementos ($).
//
// 2. Bootstrap 5 (JS & CSS): Para el funcionamiento de modales y acordeones.
//    - La función necesita que el CSS de Bootstrap esté cargado para los estilos.
//    - El JavaScript de Bootstrap es necesario para que los modales y colapsables funcionen.
//
// 3. SweetAlert2: Para mostrar notificaciones elegantes.
//    - Se usa para todos los mensajes de error y éxito (Swal.fire).
//
// ESTRUCTURA HTML REQUERIDA:
// Debes tener un modal vacío como este en tu archivo HTML principal. La función
// inyectará todo el contenido dinámico dentro de 'panelDetailContent'.
/*
    <div class="modal fade" id="panelDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" style="max-width: 800px; height: 90vh;">
            <div class="modal-content" id="panelDetailContent" style="height: 100%;">
                <!-- El contenido del acordeón se generará aquí -->
            </div>
        </div>
    </div>
*/
// -----------------------------------------------------------------------------

/**
 * Renderiza y muestra el detalle de un registro de panel en un modal con un acordeón.
 * Obtiene información de biomarcadores y comentarios, construye el HTML dinámicamente
 * y lo presenta en el modal '#panelDetailModal'.
 *
 * @param {number} recordId - El ID del registro específico del examen.
 * @param {number} panelId - El ID del panel al que pertenece el registro.
 * @param {object} record - El objeto completo con los datos del registro (valores de biomarcadores).
 * @param {string} panelTitle - El título del panel para mostrar en la cabecera del modal.
 * @param {object} language - El objeto que contiene todas las cadenas de texto traducidas para el idioma actual.
 * @param {string} currentLang - El código del idioma actual (ej: 'ES' o 'EN').
 * @param {object} api - Un objeto o módulo para realizar las llamadas al backend (debe tener métodos como `getBiomarkerInfo`, `getBiomarkerComments`).
 * @returns {Promise<void>} Una promesa que se resuelve una vez que el modal se muestra o si ocurre un error.
 */
async function renderPanelRecord(
  recordId,
  panelId,
  record,
  panelTitle,
  language,
  currentLang,
  api
) {
  if (!record) {
    Swal.fire(language.errorTitle, 'No record data provided.', 'error')
    console.error('No se proporcionó un registro válido para renderizar.')
    return
  }

  try {
    // 1. Obtener datos de biomarcadores y comentarios desde la API.
    const keys = Object.keys(record).filter(
      (k) =>
        !['record_id', 'user_id'].includes(k) &&
        !k.endsWith('_date') &&
        !k.endsWith('_time')
    )

    const [infoResponse, commentsResponse] = await Promise.all([
      api.getBiomarkerInfo(keys),
      api.getBiomarkerComments(panelId, recordId),
    ])

    const biomarkerInfoList = infoResponse.data
    const commentsMap = commentsResponse.data

    // 2. Construir el HTML del modal y el acordeón.
    let html = `
            <div class="modal-header">
                <h5 class="modal-title">${panelTitle}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="accordion" id="panelDetailAccordion">`

    for (const bm of biomarkerInfoList) {
      const recordKey = currentLang === 'ES' ? bm.name_es : bm.name
      if (record[recordKey] === undefined) continue // Salta si el biomarcador no está en este examen

      const value = parseFloat(record[recordKey])
      if (isNaN(value)) continue

      // --- Lógica de cálculo y construcción de cada item del acordeón ---
      const id = bm.db_column
      const label = recordKey
      const unit = bm.unit === '%' ? '%' : ` ${bm.unit}`
      let min = parseFloat(bm.reference_min)
      let max = parseFloat(bm.reference_max)
      let refLabel = `${min} - ${max}${unit}`
      const pct = Math.min(
        100,
        Math.max(0, (value / (max * 1.5)) * 100)
      ).toFixed(2)
      const minPct = ((min / (max * 1.5)) * 100).toFixed(2)
      const maxPct = ((max / (max * 1.5)) * 100).toFixed(2)
      const valueColorClass =
        value >= min && value <= max ? 'green-item' : 'red-item'
      const commentData = commentsMap[bm.biomarker_id] || {
        comment: '',
        id: '',
      }
      const commentText = commentData.comment

      // --- Plantilla HTML para un biomarcador (un item del acordeón) ---
      html += `
            <div class="card mb-1">
                <div class="card-header p-0" id="heading-${id}">
                    <h5 class="m-0">
                        <button class="btn btn-link d-block w-100 text-start p-3 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${id}" style="text-decoration: none; color: #223976;">
                            ${label}
                            <i class="mdi mdi-chevron-down float-end"></i>
                        </button>
                    </h5>
                </div>
                <div id="collapse-${id}" class="collapse" data-bs-parent="#panelDetailAccordion">
                    <div class="card-body">
                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#${id}-range">${
        language.tab_range
      }</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#${id}-current">${
        language.tab_current
      }</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#${id}-time">${
        language.tab_over_time
      }</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#${id}-comment">${
        language.comment_modal_label_comment
      }</a></li>
                        </ul>
                        <div class="tab-content pt-3">
                            <div class="tab-pane fade show active" id="${id}-range">
                                <div class="barra-container" style="--minPorcentaje:${minPct};--maxPorcentaje:${maxPct};">
                                    <div class="burbuja ${valueColorClass}" style="left:${pct}%;">${value}${unit}</div>
                                </div>
                                <div class="barra-valor"><b>${
                                  language.referenceRangeLabel
                                }:</b> ${refLabel}</div>
                                <div class="barra-valor"><b>${
                                  language.deficiencyLabel
                                }:</b> ${
        currentLang === 'ES' ? bm.deficiency_es : bm.deficiency_label
      }</div>
                                <div class="barra-valor"><b>${
                                  language.excessLabel
                                }:</b> ${
        currentLang === 'ES' ? bm.excess_es : bm.excess_label
      }</div>
                                <div class="barra-valor"><b>${
                                  language.descriptionLabel
                                }:</b> ${
        currentLang === 'ES' ? bm.description_es : bm.description
      }</div>
                            </div>
                            <div class="tab-pane fade" id="${id}-current">
                                <h5>${label} <div class="rectangulo-valor ${valueColorClass}">${value}${unit}</div></h5>
                            </div>
                            <div class="tab-pane fade" id="${id}-time">
                                <div class="history-button" data-rec-id="${recordId}" data-type="${
        bm.db_column
      }" data-label="${label}" style="cursor: pointer;">
                                    <span class="btn btn-sm action-icon"><i class="mdi mdi-chart-timeline-variant"></i></span>
                                    <span>${language.viewHistoryText}</span>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="${id}-comment">
                                <textarea class="form-control comment-textarea" rows="3" placeholder="${
                                  language.comment_modal_label_comment
                                }...">${commentText}</textarea>
                                <div class="d-flex justify-content-end mt-2">
                                    <button class="btn btn-sm action-icon save-comment-btn" title="${
                                      language.save_button
                                    }" data-biomarker-id="${
        bm.biomarker_id
      }"><i class="mdi mdi-content-save-outline"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`
    }

    html += `</div></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${language.close}</button>
            </div>`

    // 3. Renderizar el HTML en el modal y mostrarlo
    $('#panelDetailContent').html(html)
    // Oculta cualquier otro modal que pueda estar abierto para evitar solapamientos
    $('.modal').not('#panelDetailModal').modal('hide')
    $('#panelDetailModal').modal('show')
  } catch (err) {
    console.error('Error al renderizar el registro del panel:', err)
    Swal.fire(language.errorTitle, language.loadDataFail, 'error')
  }
}

/**
 * =============================================================================
 * EJEMPLO DE USO
 * =============================================================================
 * Así es como podrías llamar a la función desde el evento click de un botón.
 */
/*
// Asume que tienes un objeto `translations`, `currentPanelContext` y un `api` definidos en tu script.
$(document).on('click', '.viewGenericRecordBtn', async function () {
    // 1. Recolectar parámetros
    const recordId = $(this).data('id');
    const { panelId, records, title } = currentPanelContext; 
    const record = records.find(r => r.record_id == recordId);
    
    // 2. Definir idioma y objeto API
    const currentLang = 'ES'; // o 'EN'
    const language = translations[currentLang];
    const myApi = { // Este es un ejemplo, usa tu propio objeto API
        getBiomarkerInfo: (keys) => $.ajax({...}),
        getBiomarkerComments: (pId, rId) => $.ajax({...}),
    };

    // 3. Llamar a la función principal
    if (record) {
        await renderPanelRecord(recordId, panelId, record, title, language, currentLang, myApi);
    } else {
        console.error("Registro no encontrado con ID:", recordId);
    }
});
*/
