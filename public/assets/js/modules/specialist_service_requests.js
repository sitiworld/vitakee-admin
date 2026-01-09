import { createExamViewer } from '../components/examViewer.js'
import { formatDateTime } from '../helpers/validacionesEspeciales.js'

$(document).ready(function () {
  // Asumiendo que tienes un objeto global APP_CONFIG como en tu .php
  const translations = APP_CONFIG?.translations || {}
  const baseUrl = APP_CONFIG?.baseUrl || '/api/' // Ajusta tu URL base de la API
  const currentLang = APP_CONFIG?.currentLang || 'en'

  let tableLocale = currentLang.toLowerCase() === 'es' ? 'es-ES' : 'en-US'

  let allRequests = []
  let currentFilters = {
    status: 'all',
    type: 'all',
    search: '',
    sort: 'newest',
  }
  let activeRequestId = null

  function debounce(func, wait) {
    let timeout
    return function (...args) {
      const context = this
      clearTimeout(timeout)
      timeout = setTimeout(() => func.apply(context, args), wait)
    }
  }

  let currentSharedData = []

  const sharedRecordsModal = new bootstrap.Modal(
    document.getElementById('sharedRecordsModal')
  )

  const detailLoaderHtml = `
    <div class="card">
      <div class="card-body text-center p-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">${translations.loading_helper}...</span>
        </div>
      </div>
    </div>`

  // --- 1. INSTANCIACIÓN DEL EXAMVIEWER ---
  const examViewer = createExamViewer({
    apiEndpoints: {
      biomarkerInfo: `${baseUrl}biomarkers/info`,
      biomarkerComments: `${baseUrl}biomarker-comments`,
    },
    categoricalBiomarkers: ['albumin', 'creatinine'],
    translations: translations,
    lang: currentLang,
    Swal: Swal,
  })

  // --- FUNCIONES DE OBTENCIÓN DE DATOS (API CALLS) ---

  // --- MODIFICADO: fetchRequests usa fetch() ---
  function fetchRequests() {
    $('#loader').fadeIn()
    $('#requestList').fadeOut()

    const queryParams = new URLSearchParams({
      status: currentFilters.status,
      type: currentFilters.type,
      search: currentFilters.search,
    }).toString()

    // --- INICIO DE MODIFICACIÓN ---
    fetch(`${baseUrl}second-opinion-requests?${queryParams}`)
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        return response.json()
      })
      .then((response) => {
        if (response.value && Array.isArray(response.data)) {
          allRequests = response.data
          applyFiltersAndRender(true)
        } else {
          allRequests = []
          renderRequestList([])
          $('#requestDetailsContainer').html(
            `<div class="card"><div class="card-body text-center"><i class="bi bi-info-circle fs-1 text-primary"></i><p class="mt-3">${
              translations.select_request_prompt ||
              'Select a request from the list to see its details.'
            }</p></div></div>`
          )
        }
      })
      .catch((error) => {
        console.error('Fetch requests error:', error)
        $('#requestList').html(
          `<p class="text-center text-danger p-4">${
            translations.error_loading_requests ||
            'Could not load requests. Please try again later.'
          }</p>`
        )
      })
      .finally(() => {
        $('#loader').fadeOut()
        $('#requestList').fadeIn()
      })
    // --- FIN DE MODIFICACIÓN ---
  }

  // --- MODIFICADO: fetchRequestDetails usa fetch() y muestra loader ---
  function fetchRequestDetails(requestId) {
    // --- NUEVO: Mostrar loader de detalles ---
    $('#requestDetailsContainer').html(detailLoaderHtml)

    // --- INICIO DE MODIFICACIÓN ---
    fetch(`${baseUrl}second-opinion-requests/${requestId}`)
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        return response.json()
      })
      .then((response) => {
        if (
          response.value &&
          typeof response.data === 'object' &&
          response.data !== null &&
          !Array.isArray(response.data)
        ) {
          const requestDetails = response.data
          renderRequestDetails(requestDetails)
        } else {
          $('#requestDetailsContainer').html(
            `<p class="text-center text-muted p-4">${
              translations.request_not_found || 'Request details not found.'
            }</p>`
          )
        }
      })
      .catch((error) => {
        console.error('Fetch details error:', error)
        $('#requestDetailsContainer').html(
          `<p class="text-center text-danger p-4">${
            translations.error_loading_details ||
            'Could not load request details.'
          }</p>`
        )
      })
    // --- FIN DE MODIFICACIÓN ---
  }
  // --- MODIFICADO: updateRequestStatus usa fetch() ---
  function updateRequestStatus(requestId, newStatus, extraData = {}) {
    // --- INICIO DE MODIFICACIÓN ---
    fetch(`${baseUrl}second-opinion-requests-${newStatus}/${requestId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ ...extraData }),
    })
      .then((response) => {
        if (!response.ok) {
          // Si la respuesta no es OK, lanza un error para el .catch
          throw new Error(
            `Status update failed with status: ${response.status}`
          )
        }
        // No necesitamos leer el JSON, solo recargar
        fetchRequests() // Vuelve a cargar los datos para reflejar el cambio
      })
      .catch((error) => {
        console.error('Update status error:', error)
        Swal.fire(
          translations.error || 'Error',
          translations.error_updating_status || 'Error updating status.',
          'error'
        )
      })
    // --- FIN DE MODIFICACIÓN ---
  }

  // --- FUNCIONES DE RENDERIZADO ---

  // --- MODIFICADO: Ya no filtra, solo ordena y renderiza. Selecciona el primer elemento. ---
  function applyFiltersAndRender(isInitialLoad = false) {
    let sorted = [...allRequests]

    // El filtrado ahora se hace en el backend. Aquí solo ordenamos.
    sorted.sort((a, b) => {
      const dateA = new Date(a.created_at)
      const dateB = new Date(b.created_at)
      return currentFilters.sort === 'newest' ? dateB - dateA : dateA - dateB
    })

    // Renderiza la lista con los datos ordenados
    renderRequestList(sorted)

    // --- ARREGLO PRINCIPAL: Si hay resultados, muestra los detalles del primero ---
    if (sorted.length > 0) {
      const firstRequestId = sorted[0].second_opinion_id
      // Establece el ID activo
      activeRequestId = firstRequestId
      // Vuelve a renderizar la lista para marcar el primer elemento como activo
      renderRequestList(sorted)
      // Carga los detalles
      fetchRequestDetails(firstRequestId)
      // Asegura que el contenedor de detalles sea visible
      $('#requestDetailsContainer').css('opacity', 1).fadeIn(200)
    } else {
      // Si no hay resultados, resetea el ID activo
      activeRequestId = null
    }
  }

  function renderRequestList(requests) {
    const listContainer = $('#requestList')
    listContainer.empty()

    if (requests.length === 0) {
      listContainer.html(
        `<p class="text-center text-muted p-4">${
          translations.no_results_filters ||
          'No results for the current filters.'
        }</p>`
      )
      // Oculta el panel de detalles si no hay resultados
      $('#requestDetailsContainer').fadeOut()
      return
    }

    requests.forEach((req) => {
      const statusClasses = {
        completed: 'bg-bright-turquoise',
        upcoming: 'bg-sapphire-blue',
        cancelled: 'bg-midnight-blue',
        rejected: 'bg-royal-blue',
        awaiting_payment: 'bg-sky-blue',
        pending: 'bg-electric-blue',
      }
      const statusClass = statusClasses[req.status] || 'bg-light text-dark'
      const requestType = translations[req.service_type]
      const avatarUrl = req.user_image
        ? `${baseUrl}uploads/users/user_${req.user_id}.jpg`
        : `https://placehold.co/40x40/EFEFEF/AAAAAA&text=${req.first_name.charAt(
            0
          )}${req.last_name.charAt(0)}`

      const itemHtml = `
            <a href="#" class="list-group-item list-group-item-action ${
              activeRequestId === req.second_opinion_id
                ? 'border border-sapphire-blue'
                : ''
            }" data-id="${req.second_opinion_id}">
                <div class="d-flex w-100 justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center">
                        <img src="${avatarUrl}" alt="patient" class="me-3 rounded-circle" height="40" width="40">
                        <div>
                            <h5 class="mb-0">${req.first_name} ${
        req.last_name
      }</h5>
                            <p class="mb-0 text-muted small">${requestType} • ${formatDateTime(
        req.created_at,
        true
      )}</p>
                            <span class="badge bg-info">${
                              translations[req.type_request]
                            }</span>
                        </div>
                    </div>
                    <span class="badge ${statusClass} rounded-pill">${
        translations[req.status] || req.status
      }</span>
                </div>
            </a>`
      listContainer.append(itemHtml)
    })
  }

  // --- EL RESTO DE TUS FUNCIONES PERMANECE IGUAL ---

  function renderMedicalHistoryButton(request) {
    const sharedType = request.scope_request
    let buttonText = ''
    if (sharedType === 'share_all') {
      buttonText =
        translations.view_full_medical_history || 'View Full Medical History'
    }
    if (sharedType === 'share_custom') {
      buttonText =
        translations.view_custom_medical_history ||
        'View Custom Medical History'
    }
    if (sharedType === 'share_none') {
      buttonText =
        translations.view_none_medical_history || 'No Medical History Shared'
    }
    if (sharedType === 'share_none') {
      return ``
    }

    return `
      <a href="#" class="btn btn-sm btn-bright-turquoise-outline view-shared-records-btn"  data-request-id="${request.second_opinion_id}">
          ${buttonText}
      </a>`
  }

  function renderRequestDetails(request) {
    let cardHtml = ''
    switch (request.status) {
      case 'pending':
        cardHtml = getPendingDetailCard(request)
        break
      case 'upcoming':
        cardHtml = getUpcomingDetailCard(request)
        break
      case 'awaiting_payment':
        cardHtml = getAwaitingPaymentDetailCard(request)
        break
      case 'completed':
        cardHtml = getCompletedDetailCard(request)
        break
      case 'rejected':
        cardHtml = getRejectedDetailCard(request)
        break
      case 'cancelled':
        cardHtml = getCancelledDetailCard(request)
        break
      default:
        cardHtml = `<div class="card"><div class="card-body"><p>${
          translations.status_not_recognized || 'Status not recognized.'
        }</p></div></div>`
        break
    }
    $('#requestDetailsContainer').html(cardHtml)
  }

  function getPendingDetailCard(req) {
    const avatarUrl = req.user_image
      ? `${baseUrl}uploads/users/user_${req.user_id}.jpg`
      : `https://placehold.co/80x80/EFEFEF/AAAAAA&text=${req.first_name.charAt(
          0
        )}${req.last_name.charAt(0)}`
    const isAppointment = req.type_request === 'appointment_request'

    // --- INICIO DE MODIFICACIÓN ---
    // Se cambia el 'paidBadge' por 'costElement' para mostrar el precio
    const costElement =
      parseFloat(req.cost_request) > 0
        ? `$${req.cost_request}` // Muestra el precio
        : `<span class="badge bg-bright-turquoise">${
            translations.free || 'Free'
          }</span>`
    // --- FIN DE MODIFICACIÓN ---

    return `
      <div class="card" data-id="${req.second_opinion_id}">
          <div class="card-body">
          <div class="alert bg-electric-blue text-white border-0 text-center" role="alert">
                  <i class="bi-hourglass-split me-1"></i> <strong>${
                    translations.pending || 'Request Pending'
                  }</strong>
              </div>
              <div class="row align-items-start mb-3">
              <div class="col-md-4">
                  <img class="d-flex me-3 rounded-circle avatar-lg" src="${avatarUrl}" alt="Patient Avatar">
              </div>
                <div class="col-md-8">
                    <h4 class="mt-0 mb-1">${
                      req.first_name
                    } ${req.last_name}</h4>
                    <p class="mb-0">${req.telephone}</p>
                    <p class="mb-2">${req.email}</p>
                    </div>
                    </div>
                    ${renderMedicalHistoryButton(req)}
              <h5 class="mb-3 mt-4 text-uppercase bg-light p-2 fs-6">${
                translations.request_details || 'Request Details'
              }</h5>
              <div>
                  <p class="mb-2"><strong>${
                    translations.request_type || 'Request Type'
                  }:</strong><br>
                      ${translations[req.service_type]}
                  </p>
                  
                  <p class="mb-2"><strong>${
                    translations.cost_service_fee || 'Service Fee'
                  }:</strong><br> 
                      ${costElement}
                  </p>

                   <p class="mb-2"><strong>${
                     translations.shared_data || 'Scope Request'
                   }:</strong><br>
                      <span class="badge bg-info">${
                        translations[req.scope_request]
                      }</span>
                  </p>
                  
                  ${
                    isAppointment && req.request_date_to
                      ? `
                  <p class="mb-2"><strong>${
                    translations.scheduled_for || 'Scheduled for'
                  }:</strong><br>
                      <span class="fs-5 text-electric-blue">${formatDateTime(
                        req.request_date_to,
                        true
                      )}</span>
                  </p>`
                      : ''
                  }
                  <p class="mb-2"><strong>${
                    translations.submitted || 'Submitted'
                  }:</strong><br>${formatDateTime(req.created_at, true)}</p>
                  <p class="mb-3"><strong>${
                    translations.reason_for_request || 'Reason for Request'
                  }:</strong><br>
                      <span class="text-muted">"${req.notes || ''}"</span>
                  </p>
              </div>
              <div class="mt-3">
                  <h5 class="mb-3 text-uppercase bg-light p-2 fs-6">${
                    translations.actions || 'Actions'
                  }</h5>
                  <div class="d-grid gap-2">
                      <button class="btn waves-effect waves-light btn-accent-dark btn-accept"><i class="bi-check-lg me-1"></i> ${
                        translations.accept_request || 'Accept Request'
                      }</button>
                      <button class="btn btn-success-dark waves-effect waves-light btn-reject"><i class="bi-x-lg me-1"></i> ${
                        translations.reject_request || 'Reject Request'
                      }</button>
                      <button class="btn waves-effect waves-light 
                  </div>
              </div>
          </div>
      </div>`
  }

  function getUpcomingDetailCard(req) {
    const avatarUrl = req.user_image
      ? `${baseUrl}uploads/users/user_${req.user_id}.jpg`
      : `https://placehold.co/80x80/EFEFEF/AAAAAA&text=${req.first_name.charAt(
          0
        )}${req.last_name.charAt(0)}`

    // --- INICIO DE MODIFICACIÓN ---
    // Se cambia el 'paidBadge' por 'costElement' para mostrar el precio
    const costElement =
      parseFloat(req.cost_request) > 0
        ? `$${req.cost_request}` // Muestra el precio
        : `<span class="badge bg-bright-turquoise">${
            translations.free || 'Free'
          }</span>`
    // --- FIN DE MODIFICACIÓN ---

    return `
      <div class="card" data-id="${req.second_opinion_id}">
          <div class="card-body">
              <div class="alert bg-sapphire-blue text-white border-0 text-center" role="alert">
                  <i class="bi-calendar-check-fill me-1"></i> <strong>${
                    translations.appointment_upcoming || 'Appointment Upcoming'
                  }</strong>
              </div>
              <div class="d-flex align-items-start mb-3">
                  <img class="d-flex me-3 rounded-circle avatar-lg" src="${avatarUrl}" alt="Patient Avatar">
                  <div class="w-100 overflow-hidden">
                      <h4 class="mt-0 mb-1">${
                        req.first_name
                      } ${req.last_name}</h4>
                      <p class="mb-0">${req.telephone}</p>
                      <p class="mb-2">${req.email}</p>
                      </div>
                      </div>
                      ${renderMedicalHistoryButton(req)}
              <h5 class="mb-3 mt-4 text-uppercase bg-light p-2 fs-6">${
                translations.appointment_details || 'Appointment Details'
              }</h5>
              
              <p class="mb-2"><strong>${
                translations.type || 'Type'
              }:</strong><br> ${translations[req.service_type]}</p>
              
              <p class="mb-2"><strong>${
                translations.cost_service_fee || 'Service Fee'
              }:</strong><br> ${costElement}</p>
              
              <p class="mb-2"><strong>${
                translations.shared_data || 'Scope Request'
              }:</strong><br>
                      <span class="badge bg-info">${
                        translations[req.scope_request]
                      }</span>
                  </p>

              <p class="mb-2"><strong>${
                translations.scheduled_for || 'Scheduled for'
              }:</strong><br>
                  <span class="fs-5 text-sapphire-blue">${formatDateTime(
                    req.request_date_to,
                    true
                  )}</span>
              </p>

              <div class="mt-3">
                  <h5 class="mb-3 text-uppercase bg-light p-2 fs-6">${
                    translations.actions || 'Actions'
                  }</h5>
                  <div class="d-grid gap-2">
                      
                      <button class="btn btn-sapphire-blue btn-join-call" data-request-date-to="${
                        req.request_date_to
                      }">
                          <i class="bi-camera-video me-1"></i> ${
                            translations.join_video_call || 'Join Video Call'
                          }
                      </button>
                      <button class="btn btn-accent-dark btn-upcoming-accept" data-request-date-to="${
                        req.request_date_to
                      }">
                          <i class="bi-check-all me-1"></i> ${
                            translations.meeting_finished_button ||
                            'Meeting Finished'
                          }
                      </button>
                      </div>
                </div>
              </div>
            </div>`
  }

  function getAwaitingPaymentDetailCard(req) {
    const avatarUrl = req.user_image
      ? `${baseUrl}uploads/users/user_${req.user_id}.jpg`
      : `https://placehold.co/80x80/EFEFEF/AAAAAA&text=${req.first_name.charAt(
          0
        )}${req.last_name.charAt(0)}`

    const costElement =
      parseFloat(req.cost_request) > 0
        ? `<span class="fs-5 text-sky-blue">$${req.cost_request}</span>`
        : `<span class="badge bg-bright-turquoise">${
            translations.free || 'Free'
          }</span>`

    return `
      <div class="card" data-id="${req.second_opinion_id}">
          <div class="card-body">
              <div class="alert bg-sky-blue text-white border-0 text-center" role="alert">
                  <i class="bi-hourglass-split me-1"></i> <strong>${
                    translations.awaiting_payment
                  }</strong>
              </div>
              <div class="d-flex align-items-start mb-3">
                  <img class="d-flex me-3 rounded-circle avatar-lg" src="${avatarUrl}" alt="Patient Avatar">
                  <div class="w-100 overflow-hidden">
                      <h4 class="mt-0 mb-1">${
                        req.first_name
                      } ${req.last_name}</h4>
                      <p class="mb-0">${req.telephone}</p>
                      <p class="mb-2">${req.email}</p>
                      </div>
                      </div>
                      ${renderMedicalHistoryButton(req)}
              <h5 class="mb-3 mt-4 text-uppercase bg-light p-2 fs-6">${
                translations.request_details || 'Request Details'
              }</h5>
              
              <p class="mb-2"><strong>${
                translations.request_type || 'Request Type'
              }:</strong><br> ${translations[req.service_type]}</p>
              
              <p class="mb-2"><strong>${
                translations.amount_due || 'Amount Due'
              }:</strong><br> ${costElement}</p>

              <p class="mb-2"><strong>${
                translations.shared_data || 'Shared Data'
              }:</strong><br>
                      <span class="badge bg-info">${
                        translations[req.scope_request]
                      }</span>
                  </p>
              
              <p class="mb-2"><strong>${
                translations.accepted_on || 'Accepted on'
              }:</strong><br> ${formatDateTime(req.updated_at, true)}</p>
              <div class="mt-3">
                  <h5 class="mb-3 text-uppercase bg-light p-2 fs-6">${
                    translations.actions || 'Actions'
                  }</h5>
                  <div class="d-grid gap-2">
                      <button class="btn btn-success-dark btn-payment-accept"><i class="bi-send me-1"></i> ${
                        translations.mark_as_paid || 'Mark as paid'
                      }</button>
                      <button class="btn btn-cancel btn-reject"><i class="bi-trash me-1"></i>  ${
                        translations.reject_request || 'Reject Request'
                      }</button>
                  </div>
              </div>
          </div>
      </div>`
  }

  function getCompletedDetailCard(req) {
    // --- INICIO DE MODIFICACIÓN ---
    // Se cambia 'paidBadge' por 'costElement' para mostrar el precio
    const costElement =
      parseFloat(req.cost_request) > 0
        ? `$${req.cost_request}` // Muestra el precio
        : `<span class="badge bg-bright-turquoise">${
            translations.free || 'Free'
          }</span>`
    // --- FIN DE MODIFICACIÓN ---

    const avatarUrl = req.user_image
      ? `${baseUrl}uploads/users/user_${req.user_id}.jpg`
      : `https://placehold.co/80x80/EFEFEF/AAAAAA&text=${req.first_name.charAt(
          0
        )}${req.last_name.charAt(0)}`
    return `
        <div class="card" data-id="${req.second_opinion_id}">
            <div class="card-body">
                <div class="alert bg-bright-turquoise text-white border-0 text-center" role="alert">
                    <i class="bi-check-circle-fill me-1"></i> <strong>${
                      req.type_request === 'appointment_request'
                        ? translations.appointment_completed ||
                          'Appointment Completed'
                        : translations.review_completed || 'Review Completed'
                    }</strong>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <img class="d-flex me-3 rounded-circle avatar-lg" src="${avatarUrl}" alt="Patient Avatar">
                    <div class="w-100 overflow-hidden">
                        <h4 class="mt-0 mb-1">${
                          req.first_name
                        } ${req.last_name}</h4>
                         <p class="mb-0">${req.telephone}</p>
                        <p class="mb-2">${req.email}</p>
                    </div>
                </div>
                
                

                <h5 class="mb-3 mt-4 text-uppercase bg-light p-2 fs-6">${
                  translations.request_details || 'Request Details'
                }</h5>
                
                <p class="mb-2"><strong>${
                  translations.request_type || 'Scope Request'
                }:</strong><br> ${translations[req.service_type]}</p>
                
                <p class="mb-2"><strong>${
                  translations.cost_service_fee || 'Service Fee'
                }:</strong><br> ${costElement}</p>
                
                <p class="mb-2"><strong>${
                  translations.shared_data || 'Scope Request'
                }:</strong><br>
                      <span class="badge bg-info">${
                        translations[req.scope_request]
                      }</span>
                  </p>
                <p class="mb-2"><strong>${
                  translations.completed_on || 'Completed on'
                }:</strong><br> ${formatDateTime(req.updated_at, true)}</p>
                
            </div>
        </div>`
  }
  function getRejectedDetailCard(req) {
    const avatarUrl = req.user_image
      ? `${baseUrl}uploads/users/user_${req.user_id}.jpg`
      : `https://placehold.co/80x80/EFEFEF/AAAAAA&text=${req.first_name.charAt(
          0
        )}${req.last_name.charAt(0)}`

    // --- INICIO DE MODIFICACIÓN ---
    // Se cambia 'paidBadge' por 'costElement' para mostrar el precio
    const costElement =
      parseFloat(req.cost_request) > 0
        ? `$${req.cost_request}` // Muestra el precio
        : `<span class="badge bg-bright-turquoise">${
            translations.free || 'Free'
          }</span>`
    // --- FIN DE MODIFICACIÓN ---

    return `
        <div class="card" data-id="${req.second_opinion_id}">
            <div class="card-body">
                <div class="alert bg-royal-blue text-white border-0 text-center" role="alert">
                    <i class="bi-x-circle-fill me-1"></i> <strong>${
                      translations.request_rejected || 'Request Rejected'
                    }</strong>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <img class="d-flex me-3 rounded-circle avatar-lg" src="${avatarUrl}" alt="Patient Avatar">
                    <div class="w-100 overflow-hidden">
                        <h4 class="mt-0 mb-1">${
                          req.first_name
                        } ${req.last_name}</h4>
                         <p class="mb-0">${req.telephone}</p>
                        <p class="mb-2">${req.email}</p>
                    </div>
                </div>

                

                <h5 class="mb-3 mt-4 text-uppercase bg-light p-2 fs-6">${
                  translations.request_details || 'Request Details'
                }</h5>
                
                <p><strong>${
                  translations.request_type || 'Request Type'
                }:</strong><br> ${translations[req.service_type]}</p>
                
                <p class="mb-2"><strong>${
                  translations.cost_service_fee || 'Service Fee'
                }:</strong><br> ${costElement}</p>
                
                <p class="mb-2"><strong>${
                  translations.shared_data || 'Scope Request'
                }:</strong><br>
                      <span class="badge bg-info">${
                        translations[req.scope_request]
                      }</span>
                  </p>
                <div class="mt-3">
                    <p class="mb-1"><strong>${
                      translations.reason_for_rejection ||
                      'Reason for rejection'
                    }:</strong></p>
                    <blockquote class="blockquote blockquote-light bg-light p-2 rounded text-start mb-0">
                        <p class="mb-0 small fst-italic">"${
                          req.reject_message || ''
                        }"</p>
                    </blockquote>
                </div>
            </div>
        </div>`
  }
  function getCancelledDetailCard(req) {
    const avatarUrl = req.user_image
      ? `${baseUrl}uploads/users/user_${req.user_id}.jpg`
      : `https://placehold.co/80x80/EFEFEF/AAAAAA&text=${req.first_name.charAt(
          0
        )}${req.last_name.charAt(0)}`

    const costElement =
      parseFloat(req.cost_request) > 0
        ? `$${req.cost_request}`
        : `<span class="badge bg-bright-turquoise">${
            translations.free || 'Free'
          }</span>`
    return `
        <div class="card" data-id="${req.second_opinion_id}">
            <div class="card-body">
                <div class="alert bg-midnight-blue text-white border-0 text-center" role="alert">
                    <i class="bi-slash-circle me-1"></i> <strong>${
                      translations.request_cancelled || 'Request Canceled'
                    }</strong>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <img class="d-flex me-3 rounded-circle avatar-lg" src="${avatarUrl}" alt="Patient Avatar">
                    <div class="w-100 overflow-hidden">
                        <h4 class="mt-0 mb-1">${
                          req.first_name
                        } ${req.last_name}</h4>
                         <p class="mb-0">${req.telephone}</p>
                        <p class="mb-2">${req.email}</p>
                    </div>
                </div>

                

                <h5 class="mb-3 mt-4 text-uppercase bg-light p-2 fs-6">${
                  translations.request_details || 'Request Details'
                }</h5>
                
                <p class="mb-2"><strong>${
                  translations.request_type || 'Request Type'
                }:</strong><br>${translations[req.service_type]}</p>
                <p class="mb-2"><strong>${
                  translations.cost_service_fee || 'Service Fee'
                }:</strong><br> ${costElement}</p>
                <p class="mb-2"><strong>${
                  translations.shared_data || 'Shared Data'
                }:</strong><br>
                      <span class="badge bg-info">${
                        translations[req.scope_request]
                      }</span>
                  </p>
                <p class="mb-2"><strong>${
                  translations.cancelled_on || 'Canceled on'
                }:</strong><br>${formatDateTime(req.updated_at, true)}</p>
                <p class="mb-2"><strong>${
                  translations.originally_submitted || 'Originally Submitted'
                }:</strong><br>${formatDateTime(req.created_at, true)}</p>
                </div>
        </div>`
  }

  function renderSharedRecordsAccordion(panelsData, lang) {
    const accordionContainer = $('#shared-records-content')
    accordionContainer.empty()

    if (!panelsData || panelsData.length === 0) {
      accordionContainer.html(
        `<p class="text-muted text-center">${
          translations.no_records_shared ||
          'No records were shared for this request.'
        }</p>`
      )
      return
    }

    const accordionId = 'sharedRecordsAccordion'
    const accordionHtml = `<div class="accordion" id="${accordionId}">${panelsData
      .map((panel, index) => {
        const panelName = panel.panel_name
        const displayKey = lang === 'es' ? 'display_name_es' : 'display_name'
        const displayName = panel[displayKey] || panel.display_name
        const collapseId = `collapse-${panel.panel_id}`
        const biomarkersHtml =
          panel.biomarkers.length > 0
            ? '<div class="row">' + // Contenedor Row
              panel.biomarkers
                .map((bio, bioIndex) => {
                  // Usamos un ID único para este contexto
                  const uniqueCheckboxId = `shared-bio-${panel.panel_id}-${bioIndex}`
                  return `
                  <div class="col-md-4 col-6 mb-2"> <div class="form-check">
                      <input class="form-check-input" 
                             type="checkbox" 
                             id="${uniqueCheckboxId}" 
                             checked 
                             disabled>
                      <label class="form-check-label" for="${uniqueCheckboxId}">
                        ${bio.name}
                      </label>
                    </div>
                  </div>`
                })
                .join('') +
              '</div>' // Cierre de Row
            : `<p class="text-muted small">${
                // Mensaje si no hay biomarcadores
                translations.no_biomarkers_in_panel ||
                'No biomarkers in this panel.'
              }</p>`
        const tableId = `table-records-${panel.panel_id}`
        const toolbarId = `toolbar-records-${panel.panel_id}` // <-- 1. Añade esto

        const recordsTableHtml =
          panel.records && panel.records.length > 0
            ? // 4. Añadimos el div del toolbar CON el título (<h5>) adentro
              `<div id="${toolbarId}">
                <h5>${translations.shared_exams || 'Shared Exams'}</h5>
             </div>
             <table id="${tableId}"></table>`
            : `<p class="text-muted small">${
                translations.no_exams_in_panel ||
                'No specific exams were shared for this panel.'
              }</p>`

        return `
              <div class="accordion-item">
                  <h2 class="accordion-header" id="heading-${collapseId}">
                      <button class="accordion-button ${
                        index > 0 ? 'collapsed' : ''
                      }" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="${
          index === 0
        }" aria-controls="${collapseId}">
                          ${displayName}
                      </button>
                  </h2>
                  <div id="${collapseId}" class="accordion-collapse collapse ${
          index === 0 ? 'show' : ''
        }" aria-labelledby="heading-${collapseId}" data-bs-parent="#${accordionId}">
                      <div class="accordion-body">
                          <h5>${
                            translations.shared_biomarkers ||
                            'Shared Biomarkers'
                          }</h5>
                          <div class="mb-1">${biomarkersHtml}</div>
                          <hr class="my-1">
                          ${recordsTableHtml}
                      </div>
                  </div>
              </div>`
      })
      .join('')}</div>`

    accordionContainer.html(accordionHtml)

    panelsData.forEach((panel) => {
      if (panel.records && panel.records.length > 0) {
        const tableId = `#table-records-${panel.panel_id}`
        const toolbarId = `toolbar-records-${panel.panel_id}` // <-- 1. Añade esto

        const tableColumns = [
          {
            field: 'created_at',
            title: translations.exam_date_column || 'Exam Date',
            sortable: true,
            formatter: (value) => formatDateTime(value, true),
          },
          {
            field: 'action',
            title: translations.exam_action_column || 'Action',
            align: 'center',
            formatter: (value, row) => {
              console.log(value, row)

              return `<button class="btn btn-sm action-icon view-exam-btn" 
                            data-user-id="${row.user_id}"
                              data-record-id="${row.record_id}" 
                              data-panel-id="${panel.panel_id}"
                              data-panel-name="${panel.panel_name}">
                                  <i class="mdi mdi-eye"></i>
                            </button>`
            },
          },
        ]
        $(tableId).bootstrapTable({
          locale: tableLocale,
          columns: tableColumns,
          data: panel.records,
          pagination: true,
          pageSize: 5,
          search: true,
          classes: 'table table-hover table-sm',
          toolbar: `#${toolbarId}`,
          toolbarAlign: 'left',
        })
      }
    })
  }

  function showSharedRecords(requestId) {
    const loader = $('#shared-records-loader')
    const content = $('#shared-records-content')
    content.empty()
    loader.show()
    sharedRecordsModal.show()

    // --- INICIO DE MODIFICACIÓN ---
    fetch(`${baseUrl}second-opinion-exams/${requestId}`)
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        return response.json()
      })
      .then((response) => {
        if (response.value && Array.isArray(response.data)) {
          currentSharedData = response.data
          renderSharedRecordsAccordion(
            currentSharedData,
            APP_CONFIG.currentLang
          )
        } else {
          content.html(
            `<p class="text-center text-muted p-4">${
              translations.no_records_found_for_request ||
              'No records found for this request.'
            }</p>`
          )
        }
      })
      .catch((error) => {
        console.error('Show shared records error:', error)
        content.html(
          `<p class="text-center text-danger p-4">${
            translations.error_loading_records || 'Could not load records.'
          }</p>`
        )
      })
      .finally(() => {
        loader.hide()
      })
    // --- FIN DE MODIFICACIÓN ---
  }

  function handleItemClick(id) {
    activeRequestId = id
    $('#requestList .list-group-item').removeClass(
      'border border-sapphire-blue'
    )
    $(`#requestList .list-group-item[data-id="${id}"]`).addClass(
      'border border-sapphire-blue'
    )
    // La animación ahora mostrará el loader
    $('#requestDetailsContainer').animate({ opacity: 0 }, 200, function () {
      fetchRequestDetails(id) // Llama a la función que pone el loader
      $(this).animate({ opacity: 1 }, 200)
    })
  }
  function handleItemClick(id) {
    activeRequestId = id
    $('#requestList .list-group-item').removeClass(
      'border border-sapphire-blue'
    )
    $(`#requestList .list-group-item[data-id="${id}"]`).addClass(
      'border border-sapphire-blue'
    )
    $('#requestDetailsContainer').animate({ opacity: 0 }, 200, function () {
      fetchRequestDetails(id)
      $(this).animate({ opacity: 1 }, 200)
    })
  }

  // --- MANEJADORES DE EVENTOS ---

  $('#requestList').on('click', '.list-group-item', function (e) {
    e.preventDefault()
    handleItemClick($(this).data('id'))
  })

  $('#requestDetailsContainer').on(
    'click',
    '.view-shared-records-btn',
    function (e) {
      e.preventDefault()
      const requestId = $(this).data('request-id')
      showSharedRecords(requestId)
    }
  )

  $('#sharedRecordsModal').on('click', '.view-exam-btn', async function (e) {
    e.preventDefault()
    const viewButton = $(this)
    viewButton.prop('disabled', true)
    try {
      const recordId = viewButton.data('record-id')
      const panelId = viewButton.data('panel-id')
      const userId = viewButton.data('user-id') // <-- AÑADIDO

      const panelInfo = currentSharedData.find((p) => p.panel_id === panelId)
      if (!panelInfo) {
        throw new Error(`No se encontró la información del panel.`)
      }
      let recordData = {}
      for (const biomarker of panelInfo.biomarkers) {
        let biomarkerEncounter = panelInfo.records.find(
          (r) => r.record_id === recordId
        )
        recordData[biomarker.name] = biomarkerEncounter[biomarker.name_db]
      }
      const selectedBiomarkerIds = panelInfo.biomarkers.map(
        (b) => b.biomarker_id
      )
      const displayKey =
        currentLang === 'es' ? 'display_name_es' : 'display_name'
      const panelTitle = panelInfo[displayKey] || panelInfo.display_name
      examViewer.show({
        recordId,
        panelId,
        userId: userId, // <-- AÑADIDO
        recordData,
        panelTitle,
        selectedBiomarkerIds,
      })
    } catch (error) {
      console.error('Error al mostrar detalles del examen:', error)
      Swal.fire('Error', error.message, 'error')
    } finally {
      viewButton.prop('disabled', false)
    }
  })

  // =================================================================
  // === MANEJADORES DE CAMBIO DE ESTADO (MODIFICADOS CON SWEETALERT) ===
  // =================================================================

  // --- RECHAZAR SOLICITUD (Desde Pending o Awaiting Payment) ---
  $('#requestDetailsContainer').on('click', '.btn-reject', function () {
    const id = $(this).closest('.card').data('id')

    Swal.fire({
      title: translations.reject_request_title || 'Reject Request?',
      text:
        translations.reject_request_text ||
        'Please provide a reason for the rejection (required):',
      input: 'textarea',
      inputPlaceholder:
        translations.reject_request_input_placeholder ||
        'Reason for rejection...',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText:
        translations.reject_request_confirm_button || 'Yes, reject it!',
      cancelButtonText: translations.cancel_button || 'Cancel',
      preConfirm: (reason) => {
        if (!reason) {
          Swal.showValidationMessage(
            translations.reject_request_validation_error ||
              'You must provide a reason.'
          )
        }
        return reason
      },
    }).then((result) => {
      if (result.isConfirmed && result.value) {
        updateRequestStatus(id, 'reject', { reject_message: result.value })
      }
    })
  })

  // --- ACEPTAR SOLICITUD (Pending -> Awaiting Payment) ---
  $('#requestDetailsContainer').on('click', '.btn-accept', function () {
    const id = $(this).closest('.card').data('id')

    Swal.fire({
      title: translations.accept_request_title || 'Accept Request?',
      text:
        translations.accept_request_text ||
        'This will move the request to "Awaiting Payment".',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#aaa',
      confirmButtonText:
        translations.accept_request_confirm_button || 'Yes, accept it!',
      cancelButtonText: translations.cancel_button || 'Cancel',
    }).then((result) => {
      if (result.isConfirmed) {
        updateRequestStatus(id, 'to-awaiting-payment')
      }
    })
  })

  // --- MARCAR COMO PAGADO (Awaiting Payment -> Upcoming) ---
  $('#requestDetailsContainer').on('click', '.btn-payment-accept', function () {
    const id = $(this).closest('.card').data('id')

    Swal.fire({
      title: translations.mark_paid_title || 'Mark as Paid?',
      text:
        translations.mark_paid_text ||
        'This will confirm payment and move the request to "Upcoming".',
      icon: 'success',
      showCancelButton: true,
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#aaa',
      confirmButtonText:
        translations.mark_paid_confirm_button || 'Yes, mark as paid!',
      cancelButtonText: translations.cancel_button || 'Cancel',
    }).then((result) => {
      if (result.isConfirmed) {
        // Corregida errata: 'to-upcomming' -> 'to-upcoming'
        updateRequestStatus(id, 'to-upcoming')
      }
    })
  })

  // --- FINALIZAR REUNIÓN (Upcoming -> Completed) ---
  // Corregida errata: '.btn-upcomming-accept' -> '.btn-upcoming-accept'
  $('#requestDetailsContainer').on(
    'click',
    '.btn-upcoming-accept',
    function () {
      const id = $(this).closest('.card').data('id')
      // --- INICIO DE MODIFICACIÓN: Lógica de validación de fecha ---
      const requestDateString = $(this).data('request-date-to')
      const now = new Date()
      const requestDate = new Date(requestDateString)

      let swalTitle = translations.complete_meeting_title || 'Finish Meeting?'
      let swalText =
        translations.complete_meeting_text ||
        'This will mark the appointment as "Completed".'
      let swalConfirm =
        translations.complete_meeting_confirm_button || 'Yes, finish it!'
      let swalIcon = 'info'

      // Comprueba si la fecha actual es ANTERIOR a la fecha de la cita
      if (now < requestDate) {
        swalTitle = translations.complete_early_warning_title || 'Finish Early?'
        swalText =
          translations.complete_early_warning_text ||
          'This appointment is scheduled for a future date. Are you sure you want to mark it as completed?'
        swalConfirm =
          translations.complete_early_confirm_button || 'Yes, finish'
        swalIcon = 'warning' // Cambia el ícono a advertencia
      }
      // --- FIN DE MODIFICACIÓN ---

      Swal.fire({
        title: swalTitle, // Variable
        text: swalText, // Variable
        icon: swalIcon, // Variable
        showCancelButton: true,

        confirmButtonText: swalConfirm, // Variable
        cancelButtonText: translations.cancel_button || 'Cancel',
      }).then((result) => {
        if (result.isConfirmed) {
          updateRequestStatus(id, 'to-completed')
        }
      })
    }
  )

  $('#requestDetailsContainer').on('click', '.btn-join-call', function () {
    const requestDateString = $(this).data('request-date-to')
    const now = new Date()
    const requestDate = new Date(requestDateString)

    // Función helper para mostrar la alerta final
    const showInDevelopmentAlert = () => {
      Swal.fire(
        translations.feature_in_development_title || 'In Development',
        translations.feature_in_development_text ||
          'This function is still in development.',
        'info'
      )
    }

    // Comprueba si la fecha actual es ANTERIOR a la fecha de la cita
    if (now < requestDate) {
      // Si es temprano, pregunta primero
      Swal.fire({
        title: translations.join_video_call_warning_title || 'Join Early?',
        text:
          translations.join_video_call_warning_text ||
          'This appointment is scheduled for a future date. Are you sure you want to join now?',
        icon: 'warning',
        showCancelButton: true,

        confirmButtonText:
          translations.join_video_call_confirm_button || 'Yes, join',
        cancelButtonText: translations.cancel_button || 'Cancel',
      }).then((result) => {
        if (result.isConfirmed) {
          // Si confirma unirse temprano, muestra la alerta de desarrollo
          showInDevelopmentAlert()
        }
      })
    } else {
      // Si es la hora o ya pasó, muestra la alerta de desarrollo directamente
      showInDevelopmentAlert()
    }
  })

  // --- MODIFICADO: Ahora llaman a fetchRequests() ---
  $('#statusFilters, #typeFilters').on('click', 'a', function (e) {
    e.preventDefault()
    const p = $(this).parent()
    p.find('a').removeClass('active')
    $(this).addClass('active')
    if (p.attr('id') === 'statusFilters') {
      currentFilters.status = $(this).data('status')
    } else {
      currentFilters.type = $(this).data('type')
    }
    fetchRequests() // Llama a la API con los nuevos filtros
  })

  // --- MODIFICADO: Búsqueda con Debounce ---
  // 1. Crea la versión "debounced" de tu función fetchRequests
  const debouncedFetchRequests = debounce(fetchRequests, 300) // 300ms de espera

  // 2. Usa 'keyup' para capturar la entrada y llama a la versión "debounced"
  $('#searchInput').on('keyup', function () {
    currentFilters.search = $(this).val()
    debouncedFetchRequests() // Llama a la API con el nuevo término de búsqueda
    // El input no debería perder el foco, ya que el input en sí no se está
    // volviendo a renderizar, solo la lista de resultados.
  })

  // --- MODIFICADO: El ordenamiento es local, no necesita llamar a la API ---
  $('#sortSelect').on('change', function () {
    currentFilters.sort = $(this).val()
    // Solo vuelve a ordenar y renderizar los datos ya cargados
    applyFiltersAndRender()
  })

  // --- Carga inicial de datos ---
  fetchRequests()
})
