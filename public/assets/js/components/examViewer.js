/**
 * Crea una instancia de un visualizador de detalles de exámenes en un modal.
 * El componente es autocontenido y maneja sus propios elementos del DOM y llamadas a la API.
 *
 * @param {object} options - Objeto de configuración para el componente.
 * @param {object} options.apiEndpoints - URLs para las llamadas a la API.
 * @param {string} options.apiEndpoints.biomarkerInfo - Endpoint para obtener información de biomarcadores.
 * @param {string} options.apiEndpoints.biomarkerComments - Endpoint para obtener/guardar comentarios.
 * @param {string[]} [options.categoricalBiomarkers] - Lista de 'db_column' de biomarcadores que deben tratarse como categóricos.
 * @param {object} options.translations - Objeto con las cadenas de texto para internacionalización.
 * @param {string} options.lang - Idioma actual (ej. 'es', 'en').
 * @param {object} options.Swal - La instancia de SweetAlert2 para mostrar notificaciones.
 * @param {string} [options.role] - El rol del usuario ('specialist' o 'patient'). 'specialist' por defecto.
 * @returns {object} - Un objeto con el método `show` para mostrar el modal.
 */
export function createExamViewer(options) {
  const {
    apiEndpoints,
    categoricalBiomarkers = ['albumin', 'creatinine'],
    translations,
    lang,
    Swal,
    role = 'specialist',
  } = options

  // Variables privadas (sin cambios)
  let modalInstance = null
  let modalElement = null
  let onCloseCallback = null

  // createModalElement (sin cambios)
  const createModalElement = () => {
    const modalId = `examViewerModal_${Date.now()}`

    const modalHTML = `
        <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-scrollable" style="max-width: 800px; height: 90vh;">
            <div class="modal-content" style="height: 100%;">
              <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body"></div>
            </div>
          </div>
        </div>`

    document.body.insertAdjacentHTML('beforeend', modalHTML)
    return document.getElementById(modalId)
  }

  // renderLoadingState (sin cambios)
  const renderLoadingState = (modalBody) => {
    modalBody.innerHTML = `
        <div class="text-center p-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">${
              translations.loading_text || 'Loading...'
            }</span>
          </div>
        </div>`
  }

  // renderErrorState (sin cambios)
  const renderErrorState = (modalBody) => {
    modalBody.innerHTML = `<div class="alert alert-danger">${
      translations.loadDataFail || 'Failed to load data.'
    }</div>`
  }

  // normalizeLevel (sin cambios)
  const normalizeLevel = (raw) => {
    if (raw == null) return null
    const s = String(raw).trim().toUpperCase()
    if (s === 'N' || s === 'NORMAL' || s === 'NEG' || s === 'NEGATIVE')
      return 'N'
    if (s === '1+' || s === '+1' || s === '1') return '1+'
    if (s === '2+' || s === '+2' || s === '2') return '2+'
    if (s === '3+' || s === '+3' || s === '3') return '3+'
    return null
  }

  // getCategoricalLevelClass (sin cambios)
  const getCategoricalLevelClass = (biomarkerKey, level) => {
    if (biomarkerKey === 'albumin') {
      return (
        { N: 'alb-n', '1+': 'alb-1', '2+': 'alb-2', '3+': 'alb-3' }[level] || ''
      )
    }
    return { '1+': 'cr-1', '2+': 'cr-2', '3+': 'cr-3' }[level] || ''
  }

  // renderSemaforoCategorico (sin cambios)
  function renderSemaforoCategorico(fieldKey, level, unit) {
    const isAlb = fieldKey === 'albumin'
    const active = (lbl) => (level === lbl ? 'semaforo-active' : '')

    const labelsAlb = `
<div class="semaforo-labels">
  <div class="d-flex flex-column"><span>N</span><span>(Normal)</span></div><div>1+</div><div>2+</div><div>3+</div>
</div>`
    const labelsCr = `
<div class="semaforo-labels-3">
  <div>1+</div><div>2+</div><div>3+</div>
</div>`

    const albCells = `
<div class="semaforo-cell alb-n ${active('N')}"></div>
<div class="semaforo-cell alb-1 ${active('1+')}"></div>
<div class="semaforo-cell alb-2 ${active('2+')}"></div>
<div class="semaforo-cell alb-3 ${active('3+')}"></div>`

    const crCells = `
<div class="semaforo-cell cr-1 ${active('1+')}"></div>
<div class="semaforo-cell cr-2 ${active('2+')}"></div>
<div class="semaforo-cell cr-3 ${active('3+')}"></div>`

    const gridClass = isAlb ? 'semaforo-grid' : 'semaforo-grid-3'
    const labelsRow = isAlb ? labelsAlb : labelsCr
    const cellsRow = isAlb ? albCells : crCells

    return `
${labelsRow}
<div class="${gridClass}">${cellsRow}</div>
`
  }

  /**
   * Genera el HTML para un único biomarcador.
   */
  const createBiomarkerHtml = (
    biomarker,
    recordData,
    commentsMap,
    accordionId
  ) => {
    // Lógica de valores y rangos (sin cambios)
    const biomarkerKey = biomarker.db_column.toLowerCase()
    const isCategorical = categoricalBiomarkers.includes(biomarkerKey)
    const recordKey =
      lang.toLowerCase() === 'es' ? biomarker.name_es : biomarker.name
    const rawValue = recordData[recordKey]

    let rangeContent,
      referenceRangeText,
      titleBadge = ''
    const headingId = `heading-biomarker-${biomarker.biomarker_id}`
    const collapseId = `collapse-biomarker-${biomarker.biomarker_id}`

    if (isCategorical) {
      const levelCat = normalizeLevel(rawValue)
      if (!levelCat) return ''
      rangeContent = renderSemaforoCategorico(
        biomarkerKey,
        levelCat,
        biomarker.unit
      )
      referenceRangeText =
        biomarkerKey === 'albumin' ? 'N, 1+, 2+, 3+' : '1+, 2+, 3+'
      const levelClass = getCategoricalLevelClass(biomarkerKey, levelCat)
      titleBadge = `<span class="badge ms-2 ${levelClass}" style="color: white; border: 1px solid rgba(0,0,0,0.1);">${levelCat}</span>`
    } else {
      const value = parseFloat(rawValue)
      if (isNaN(value)) return ''
      if (value === 0) return ''
      const min = parseFloat(biomarker.reference_min)
      const max = parseFloat(biomarker.reference_max)
      const scale = max * 1.5
      const valuePosPercent = Math.min(100, Math.max(0, (value / scale) * 100))
      const minPosPercent = (min / scale) * 100
      const maxPosPercent = (max / scale) * 100
      const valueColorClass =
        value >= min && value <= max ? 'green-item' : 'red-item'
      rangeContent = `<div class="barra-container" style="--minPorcentaje:${minPosPercent};--maxPorcentaje:${maxPosPercent};"><div class="burbuja ${valueColorClass}" style="left:${valuePosPercent}%;">${value} ${biomarker.unit}</div></div>`
      referenceRangeText = `${min} - ${max} ${biomarker.unit}`
      titleBadge = `<span class="badge ms-2 ${valueColorClass}" style="border: 1px solid rgba(0,0,0,0.1);">${value} ${biomarker.unit}</span>`
    }

    const deficiency =
      lang.toLowerCase() === 'es'
        ? biomarker.deficiency_es
        : biomarker.deficiency_label
    const excess =
      lang.toLowerCase() === 'es' ? biomarker.excess_es : biomarker.excess_label
    const description =
      lang.toLowerCase() === 'es'
        ? biomarker.description_es
        : biomarker.description

    // --- MODIFICACIÓN: Lógica de Comentarios basada en 'role' ---
    let commentSectionHtml = '' // 1. Inicializa vacía

    // 2. Solo si el rol es 'specialist', genera el HTML de comentarios
    if (role === 'specialist') {
      const commentData = commentsMap[biomarker.biomarker_id] || {}
      const commentText = commentData.comment || ''
      const commentId = commentData.comment_biomarker_id || null
      const hasComment = !!commentId

      // Vista "Añadir"
      const addViewHtml = `
        <div class="comment-add-view ${hasComment ? 'd-none' : ''}">
            <button class="btn btn-sm btn-add btn-add-comment">
                <i class="mdi mdi-plus me-1"></i> ${
                  translations.comment_modal_label_comment || 'Add Comment'
                }
            </button>
        </div>`

      // Vista "Mostrar" (con botones)
      const displayViewHtml = `
        <div class="comment-display-view ${hasComment ? '' : 'd-none'}">
          <p class="mb-2 fst-italic bg-light p-2 rounded comment-text-display">${commentText}</p>
          <div class="d-flex justify-content-end gap-1">
             <button class="btn btn-sm btn-save btn-edit-comment" title="${
               translations.edit || 'Edit'
             }"><i class="mdi mdi-pencil"></i></button>
            <button class="btn btn-sm btn-cancel btn-delete-comment" title="${
              translations.delete || 'Delete'
            }"><i class="mdi mdi-trash-can-outline"></i></button>
          </div>
        </div>`

      // Vista "Editar"
      const editViewHtml = `
        <div class="comment-edit-view d-none">
          <textarea class="form-control comment-textarea" rows="3" placeholder="${
            translations.comment_modal_label_comment
          }...">${commentText}</textarea>
          <div class="d-flex justify-content-end gap-1 mt-2">
            <button class="btn btn-sm btn-save btn-save-comment" title="${
              translations.save_button || 'Save'
            }"><i class="mdi mdi-content-save-outline me-1"></i> ${
        translations.save_button || 'Save'
      }</button>
       <button class="btn btn-sm btn-cancel btn-cancel-edit" title="${
         translations.cancel_button || 'Cancel'
       }">${translations.cancel_button || 'Cancel'}</button>
          </div>
        </div>`

      // 3. Llena la variable con todo el bloque de comentarios
      commentSectionHtml = `
          <hr class="my-3">
          <h6><i class="mdi mdi-comment-text-outline me-1"></i>${
            translations.comment_modal_label_comment
          }</h6>
          <div class="comment-wrapper" 
               data-biomarker-id="${biomarker.biomarker_id}" 
               data-comment-id="${commentId || ''}"
               data-comment-text="${commentText || ''}">
            
            ${addViewHtml}
            ${displayViewHtml}
            ${editViewHtml}

          </div>`
    }
    // --- FIN MODIFICACIÓN ---

    // Estructura HTML del Acordeón
    return `
      <div class="accordion-item">
        <h2 class="accordion-header" id="${headingId}">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">
            ${recordKey} ${titleBadge}
          </button>
        </h2>
        <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headingId}" data-bs-parent="#${accordionId}">
          <div class="card-body p-3">
            <h5 class="mb-4 d-flex align-items-center">${recordKey}</h5>
            ${rangeContent}
            <div class="barra-valor mt-3"><b>${
              translations.referenceRangeLabel
            }:</b> ${referenceRangeText}</div>
            <div class="barra-valor mt-2"><b>${translations.deficiency}:</b> ${
      deficiency || 'N/A'
    }</div>
            <div class="barra-valor mt-2"><b>${translations.excess}:</b> ${
      excess || 'N/A'
    }</div>
            <div class="barra-valor mt-2"><b>${
              translations.descriptionLabel
            }:</b> ${description || 'N/A'}</div>
            
            ${commentSectionHtml}
           
          </div>
        </div>
      </div>`
  }

  // fetchBiomarkerData (sin cambios)
  const fetchBiomarkerData = async (
    biomarkerKeys,
    panelId,
    recordId,
    userId
  ) => {
    const [infoResponse, commentsResponse] = await Promise.all([
      fetch(apiEndpoints.biomarkerInfo, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        // 2. Añadir user_id al body
        body: JSON.stringify({
          names: biomarkerKeys,
          user_id: userId,
        }),
      }).then((res) => res.json()),
      fetch(`${apiEndpoints.biomarkerComments}/${panelId}/${recordId}`).then(
        (res) => res.json()
      ),
    ])

    if (!infoResponse.value || !commentsResponse.value) {
      throw new Error('Failed to fetch biomarker data')
    }

    const commentsMap = {}
    if (commentsResponse.data && Array.isArray(commentsResponse.data)) {
      commentsResponse.data.forEach((comment) => {
        commentsMap[comment.id_biomarker] = comment
      })
    }

    return [infoResponse.data, commentsMap]
  }

  // handleSaveComment (sin cambios)
  const handleSaveComment = async (e, panelId, recordId) => {
    const button = e.target.closest('.btn-save-comment')
    const wrapper = button.closest('.comment-wrapper')
    const biomarkerId = wrapper.dataset.biomarkerId
    const textarea = wrapper.querySelector('.comment-textarea')
    const comment = textarea.value

    if (!comment.trim()) {
      Swal.fire(
        translations.error_title || 'Error',
        translations.comment_cannot_be_empty || 'Comment cannot be empty.',
        'error'
      )
      return
    }

    button.disabled = true
    button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`

    try {
      const response = await fetch(apiEndpoints.biomarkerComments, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
          id_test_panel: panelId,
          id_test: recordId,
          id_biomarker: biomarkerId,
          comment: comment,
        }),
      })

      const result = await response.json()
      if (!result.value) throw new Error(result.message)

      Swal.fire({
        icon: 'success',
        title: result.message, // Mensaje del backend
        timer: 1500,
        showConfirmButton: false,
      })

      const newCommentId = result.data[0].id
      wrapper.dataset.commentId = newCommentId
      wrapper.dataset.commentText = comment
      wrapper.querySelector('.comment-text-display').textContent = comment
      textarea.value = comment

      wrapper.querySelector('.comment-display-view').classList.remove('d-none')
      wrapper.querySelector('.comment-edit-view').classList.add('d-none')
      wrapper.querySelector('.comment-add-view').classList.add('d-none')
    } catch (error) {
      console.error('Error saving comment:', error)
      Swal.fire(translations.error_title || 'Error', error.message, 'error')
    } finally {
      button.disabled = false
      button.innerHTML = `<i class="mdi mdi-content-save-outline me-1"></i> ${
        translations.save_button || 'Save'
      }`
    }
  }

  // handleDeleteComment (sin cambios)
  const handleDeleteComment = async (e) => {
    const button = e.target.closest('.btn-delete-comment')
    const wrapper = button.closest('.comment-wrapper')
    const commentId = wrapper.dataset.commentId
    const biomarkerId = wrapper.dataset.biomarkerId

    if (!commentId) return

    const confirmResult = await Swal.fire({
      title: translations.delete_comment_title || 'Delete Comment?',
      text: translations.delete_comment_text || 'This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      confirmButtonText: translations.delete_button || 'Delete',
      cancelButtonText: translations.cancel_button || 'Cancel',
    })

    if (!confirmResult.isConfirmed) {
      return
    }

    button.disabled = true

    try {
      const response = await fetch(
        `${apiEndpoints.biomarkerComments}/${commentId}`,
        {
          method: 'DELETE',
        }
      )

      const result = await response.json()
      if (!result.value) throw new Error(result.message)

      Swal.fire({
        icon: 'success',
        title: result.message, // Mensaje del backend
        timer: 1500,
        showConfirmButton: false,
      })

      wrapper.dataset.commentId = ''
      wrapper.dataset.commentText = ''
      wrapper.querySelector('.comment-text-display').textContent = ''
      wrapper.querySelector('.comment-textarea').value = ''

      wrapper.querySelector('.comment-display-view').classList.add('d-none')
      wrapper.querySelector('.comment-edit-view').classList.add('d-none')
      wrapper.querySelector('.comment-add-view').classList.remove('d-none')
    } catch (error) {
      console.error('Error deleting comment:', error)
      Swal.fire(translations.error_title || 'Error', error.message, 'error')
    } finally {
      button.disabled = false
    }
  }

  // cleanup (sin cambios)
  const cleanup = () => {
    if (typeof onCloseCallback === 'function') {
      onCloseCallback()
    }
    if (modalInstance) {
      modalInstance.dispose()
      modalInstance = null
    }
    if (modalElement) {
      modalElement.remove()
      modalElement = null
    }
    onCloseCallback = null
  }

  // Objeto público
  return {
    /**
     * Muestra el modal con los detalles de un examen específico.
     * @param {object} showOptions - Opciones para mostrar el modal.
     * (El resto de JSDoc sin cambios)
     */
    show: async function (showOptions) {
      const {
        recordId,
        panelId,
        userId,
        recordData,
        panelTitle,
        selectedBiomarkerIds,
        onClose,
      } = showOptions

      if (modalElement) {
        cleanup()
      }

      onCloseCallback = onClose
      modalElement = createModalElement()
      modalInstance = new bootstrap.Modal(modalElement)
      modalElement.addEventListener('hidden.bs.modal', cleanup, { once: true })

      const modalTitle = modalElement.querySelector('.modal-title')
      const modalBody = modalElement.querySelector('.modal-body')
      modalBody.classList.add('p-4')
      modalTitle.textContent = panelTitle

      renderLoadingState(modalBody)
      modalInstance.show()

      try {
        const biomarkerKeys = Object.keys(recordData).filter(
          (key) =>
            !['record_id', 'user_id', 'state'].includes(key) &&
            !key.endsWith('_date') &&
            !key.endsWith('_time')
        )

        const [biomarkerInfoList, commentsMap] = await fetchBiomarkerData(
          biomarkerKeys,
          panelId,
          recordId,
          userId // <-- AÑADIDO
        )

        const accordionId = `examViewerAccordion_${Date.now()}`

        // El 'role' ya está disponible aquí y se pasa a createBiomarkerHtml
        const contentHTML = biomarkerInfoList
          .filter((bm) => selectedBiomarkerIds.includes(bm.biomarker_id))
          .map((bm) =>
            createBiomarkerHtml(bm, recordData, commentsMap, accordionId)
          )
          .join('')

        modalBody.innerHTML = `<div class="accordion custom-accordion" id="${accordionId}">${
          contentHTML || `<p class='p-3 text-muted'>${translations.noData}</p>`
        }</div>`

        const firstItem = modalBody.querySelector('.accordion-collapse')
        if (firstItem) {
          new bootstrap.Collapse(firstItem, { toggle: true })
        }

        // Los listeners solo se añaden si el rol es 'specialist'
        // (Sin cambios, esto ya era correcto)
        if (role === 'specialist') {
          modalBody.addEventListener('click', (e) => {
            const wrapper = e.target.closest('.comment-wrapper')
            if (!wrapper) return

            if (e.target.closest('.btn-add-comment')) {
              wrapper.querySelector('.comment-add-view').classList.add('d-none')
              wrapper
                .querySelector('.comment-edit-view')
                .classList.remove('d-none')
              wrapper.querySelector('.comment-textarea').focus()
            }

            if (e.target.closest('.btn-edit-comment')) {
              wrapper
                .querySelector('.comment-display-view')
                .classList.add('d-none')
              wrapper
                .querySelector('.comment-edit-view')
                .classList.remove('d-none')
              wrapper.querySelector('.comment-textarea').focus()
            }

            if (e.target.closest('.btn-cancel-edit')) {
              const hasExistingComment = !!wrapper.dataset.commentId
              wrapper
                .querySelector('.comment-edit-view')
                .classList.add('d-none')

              if (hasExistingComment) {
                wrapper
                  .querySelector('.comment-display-view')
                  .classList.remove('d-none')
                wrapper.querySelector('.comment-textarea').value =
                  wrapper.dataset.commentText
              } else {
                wrapper
                  .querySelector('.comment-add-view')
                  .classList.remove('d-none')
                wrapper.querySelector('.comment-textarea').value = ''
              }
            }

            if (e.target.closest('.btn-save-comment')) {
              handleSaveComment(e, panelId, recordId)
            }

            if (e.target.closest('.btn-delete-comment')) {
              handleDeleteComment(e)
            }
          })
        }
      } catch (error) {
        console.error('Failed to render exam details:', error)
        renderErrorState(modalBody)
      }
    },
  }
}
