import { formatDateTime } from '../helpers/validacionesEspeciales.js'

$(document).ready(function () {
  // Configuración inicial desde el objeto global
  const translations = APP_CONFIG?.translations || {}
  const baseUrl = APP_CONFIG?.baseUrl || '/api/'

  let allRequests = []
  let currentFilters = { status: 'all', sort: 'newest' }
  let activeRequestId = null

  // --- LLAMADAS A LA API ---
  const detailLoaderHtml = `
  <div class="card card-pricing">
    <div class="card-body text-center p-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">${translations.loading_helper}...</span>
      </div>
    </div>
  </div>`

  // --- LLAMADAS A LA API ---

  // --- MODIFICADO: fetchRequests ahora usa fetch() y envía filtros ---
  function fetchRequests() {
    $('#loader').show()
    $('#requestList').empty()

    const queryParams = new URLSearchParams({
      status: currentFilters.status,
    }).toString()

    fetch(`${baseUrl}user-second-opinion-requests?${queryParams}`)
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        return response.json()
      })
      .then((response) => {
        if (response.value) {
          allRequests = response.data
          applyFiltersAndRender()

          const activeExists = allRequests.some(
            (r) => r.second_opinion_id === activeRequestId
          )

          if (activeExists) {
            handleItemClick(activeRequestId, true)
          } else if (allRequests.length > 0) {
            handleItemClick(allRequests[0].second_opinion_id)
          } else {
            $('#requestDetailsContainer').empty()
            activeRequestId = null
          }
        } else {
          allRequests = []
          applyFiltersAndRender()
          $('#requestDetailsContainer').empty()
          activeRequestId = null
        }
      })
      .catch((error) => {
        console.error('Fetch error:', error)
        $('#requestList').html(
          `<p class="text-center text-danger p-4">${
            translations.error_loading_requests || 'Could not load requests.'
          }</p>`
        )
      })
      .finally(() => {
        $('#loader').hide()
      })
  }

  // --- MODIFICADO: fetchRequestDetails usa fetch() y muestra loader ---
  function fetchRequestDetails(requestId) {
    // --- NUEVO: Mostrar loader de detalles ---
    $('#requestDetailsContainer').html(detailLoaderHtml)

    fetch(`${baseUrl}user-second-opinion-requests/${requestId}`)
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        return response.json()
      })
      .then((response) => {
        if (response.value && response.data) {
          renderRequestDetails(response.data)
        } else {
          $('#requestDetailsContainer').html(
            `<p class="text-center text-danger p-4">${
              translations.error_loading_details || 'Could not load details.'
            }</p>`
          )
        }
      })
      .catch((error) => {
        console.error('Fetch details error:', error)
        $('#requestDetailsContainer').html(
          `<p class="text-center text-danger p-4">${
            translations.error_loading_details || 'Could not load details.'
          }</p>`
        )
      })
  }

  // --- RENDERIZADO DE LA VISTA ---

  function applyFiltersAndRender() {
    // 'allRequests' ya viene filtrado por 'status' desde la API
    let sorted = [...allRequests]

    // Solo aplicamos el ordenamiento local
    sorted.sort((a, b) => {
      const dateA = new Date(a.created_at)
      const dateB = new Date(b.created_at)
      return currentFilters.sort === 'newest' ? dateB - dateA : dateA - dateB
    })
    renderRequestList(sorted)
  }

  function renderRequestList(requests) {
    const listContainer = $('#requestList')
    listContainer.empty()
    if (requests.length === 0) {
      listContainer.html(
        `<p class="text-center text-muted p-4">${
          translations.no_results_filters || 'No results for current filters.'
        }</p>`
      )
      $('#requestDetailsContainer').empty()
      return
    }
    requests.forEach((req) => {
      const statusClass =
        {
          completed: 'bg-bright-turquoise',
          upcoming: 'bg-sapphire-blue',
          cancelled: 'bg-midnight-blue',
          rejected: 'bg-royal-blue',
          awaiting_payment: 'bg-sky-blue',
          pending: 'bg-electric-blue',
        }[req.status] || 'bg-light text-dark'
      const requestType = translations[req.type_request]

      const specialistAvatar = `https://placehold.co/40x40/EFEFEF/AAAAAA&text=${req.first_name.charAt(
        0
      )}${req.last_name.charAt(0)}`
      const itemHtml = `
        <a href="#" class="list-group-item list-group-item-action ${
          activeRequestId === req.second_opinion_id
            ? 'border border-sapphire-blue'
            : ''
        }" data-id="${req.second_opinion_id}">
          <div class="d-flex w-100 justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <img src="${specialistAvatar}" alt="specialist" class="me-3 rounded-circle" height="40" width="40">
              <div>
                <h5 class="mb-0">Dr. ${req.first_name} ${req.last_name}</h5>
                <p class="mb-0 text-muted small">${requestType} • ${formatDateTime(
        req.created_at,
        true
      )}</p>
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

  function renderRequestDetails(request) {
    let cardHtml = ''
    switch (request.status) {
      case 'pending':
        cardHtml = getPendingApprovalCard(request)
        break
      case 'upcoming':
        cardHtml = getUpcomingCard(request)
        break
      case 'awaiting_payment':
        cardHtml = getAwaitingPaymentCard(request)
        break
      case 'completed':
        cardHtml = getCompletedCard(request)
        break
      case 'rejected':
        cardHtml = getRejectedCard(request)
        break
      case 'cancelled':
        cardHtml = getCancelledCard(request)
        break
    }
    $('#requestDetailsContainer').html(cardHtml)
  }

  // --- PLANTILLAS DE TARJETAS ---

  function getPendingApprovalCard(req) {
    const costDisplay =
      req.cost_request == 0
        ? `<span class="badge bg-bright-turquoise">${
            translations.free || 'Free'
          }</span>`
        : `$${req.cost_request}`

    let costLine = ''
    if (parseFloat(req.cost_request) > 0) {
      costLine = `<li><strong>${translations.cost || 'Cost'}:</strong> $${
        req.cost_request
      }</li>`
    }

    return `<div class="card card-pricing"><div class="card-body text-center">
      <p class="card-pricing-plan-name fw-bold text-uppercase">${
        translations[req.type_request]
      }</p>
      <span class="text-electric-blue"><i class="bi-clock-history fs-1"></i></span>
      <h2 class="card-pricing-price text-electric-blue">${
        translations.pending_approval || 'Pending Approval'
      }</h2>
      <ul class="card-pricing-features list-unstyled">
        <li><strong>${
          translations.for || 'For'
        }:</strong>${req.first_name} ${req.last_name}</li>
        <li><strong>${
          translations.requested || 'Requested'
        }:</strong> ${formatDateTime(req.created_at, true)}</li>
        <li><strong>${translations.cost || 'Cost'}:</strong> ${costDisplay}</li>
      </ul>
      <button class="btn btn-electrue-blue-outline waves-effect waves-light mt-4 mb-2 width-sm btn-cancel-request" data-id="${
        req.second_opinion_id
      }">${translations.cancel_request || 'Cancel Request'}</button>
    </div></div>`
  }

  function getUpcomingCard(req) {
    // --- INICIO DE MODIFICACIÓN ---
    // Unificamos la lógica del costo.
    // Ya no se usan costText y costLine por separado.
    const costElement =
      parseFloat(req.cost_request) > 0
        ? `$${req.cost_request} (${translations.paid || 'Paid'})`
        : `<span class="badge bg-bright-turquoise">${
            translations.free || 'Free'
          }</span>`
    // --- FIN DE MODIFICACIÓN ---

    return `<div class="card card-pricing"><div class="card-body text-center">
      <p class="card-pricing-plan-name fw-bold text-uppercase">${
        translations.appointment || 'Appointment'
      }</p>
      <span class="text-sapphire-blue"><i class="bi-calendar-check fs-1"></i></span>
      <h2 class="card-pricing-price text-sapphire-blue">${
        translations.upcoming || 'Upcoming'
      }</h2>
      <ul class="card-pricing-features list-unstyled">
        <li><strong>${
          translations.with || 'With'
        }:</strong> Dr. ${req.first_name} ${req.last_name}</li>
        <li><strong>${
          translations.on || 'On'
        }:</strong> ${formatDateTime(req.request_date_to, true)}</li>
        
        <li><strong>${translations.cost || 'Cost'}:</strong> ${costElement}</li>

      </ul>
      <button class="btn btn-sapphire-blue waves-effect waves-light mt-4 mb-2 width-sm">${
        translations.view_details_join || 'View Details & Join'
      }</button>
    </div></div>`
  }
  function getAwaitingPaymentCard(req) {
    const costDisplay =
      req.cost_request == 0
        ? `<span class="badge bg-bright-turquoise">${
            translations.free || 'Free'
          }</span>`
        : `$${req.cost_request}`

    return `<div class="card card-pricing"><div class="card-body text-center">
      <p class="card-pricing-plan-name fw-bold text-uppercase">${
        translations[req.type_request]
      }</p>
      <span class="text-sky-blue"><i class="bi-wallet2 fs-1"></i></span>
      <h2 class="card-pricing-price text-sky-blue">${
        translations.awaiting_payment || 'Awaiting Payment'
      }</h2>
      <ul class="card-pricing-features list-unstyled">
        <li><strong>${
          translations.with || 'With'
        }:</strong> Dr. ${req.first_name} ${req.last_name}</li>
        <li><strong>${
          translations.accepted || 'Accepted'
        }:</strong> ${formatDateTime(req.updated_at, true)}</li>
        <li><strong>${
          translations.amount_due || 'Amount Due'
        }:</strong> ${costDisplay}</li>
      </ul>
      <button class="btn btn-sky-blue waves-effect waves-light mt-4 mb-2 width-sm">${
        translations.awaiting_payment_note ||
        'Waiting for payment confirmation from the specialist'
      }</button>
    </div></div>`
  }

  function getCompletedCard(req) {
    let actionElement = ''

    if (req.has_review) {
      actionElement = `
        <div class="text-start mt-4">
            <h6 class="text-muted">${
              translations.your_review_label || 'Your Review:'
            }</h6>
            <blockquote class="blockquote blockquote-light bg-light p-2 rounded">
                <p class="mb-0 small fst-italic">"${req.review}"</p>
            </blockquote>
        </div>`
    } else {
      actionElement = `
        <button class="btn btn-bright-turquoise-outline waves-effect waves-light mt-4 mb-2 width-sm" 
                data-bs-toggle="modal" 
                data-bs-target="#reviewModal"
                data-request-id="${req.second_opinion_id}"
                data-user-id="${req.user_id}"
                data-specialist-id="${req.specialist_id}"
                data-specialist-name="Dr. ${req.first_name} ${req.last_name}"
                data-request-date="${formatDateTime(
                  req.request_date_to,
                  true
                )}">
          ${translations.leave_review || 'Leave a Review'}
        </button>`
    }

    // --- INICIO DE MODIFICACIÓN ---
    // Se corrige la lógica de costo que faltaba para 'Free'
    // y se arregla el HTML (no más <li> dentro de <li>)
    let costElement = ''
    if (parseFloat(req.cost_request) > 0) {
      costElement = `<li><strong>${translations.cost || 'Cost'}:</strong> $${
        req.cost_request
      } (${translations.paid || 'Paid'})</li>`
    } else {
      costElement = `<li><strong>${
        translations.cost || 'Cost'
      }:</strong> <span class="badge bg-bright-turquoise">${
        translations.free || 'Free'
      }</span></li>`
    }
    // --- FIN DE MODIFICACIÓN ---

    return `<div class="card card-pricing"><div class="card-body text-center">
      <p class="card-pricing-plan-name fw-bold text-uppercase">${
        translations[req.type_request]
      }</p>
      <span class="text-bright-turquoise"><i class="bi-check-circle-fill fs-1"></i></span>
      <h2 class="card-pricing-price text-bright-turquoise">${
        translations.completed || 'Completed'
      }</h2>
      <ul class="card-pricing-features list-unstyled">
        <li><strong>${
          translations.with || 'With'
        }:</strong> Dr. ${req.first_name} ${req.last_name}</li>
        <li><strong>${
          translations.on || 'On'
        }:</strong> ${formatDateTime(req.request_date_to, true)}</li>
        
        ${costElement}

      </ul>
      ${actionElement}
    </div></div>`
  }

  function getRejectedCard(req) {
    const searchUrl = `${baseUrl}second_opinion_view`

    return `<div class="card card-pricing"><div class="card-body text-center">
      <p class="card-pricing-plan-name fw-bold text-uppercase">${
        translations[req.type_request]
      }</p>
      <span class="card-pricing-icon text-royal-blue"><i class="bi-x-circle-fill fs-1"></i></span>
      <h2 class="card-pricing-price text-royal-blue">${
        translations.rejected_by_specialist || 'Rejected by Specialist'
      }</h2>
      <div class="card-pricing-features">
        <p class="mb-1"><strong>${
          translations.reason_from || 'Reason from'
        } Dr. ${req.last_name}:</strong></p>
        <blockquote class="blockquote blockquote-light bg-light p-2 rounded text-start"><p class="mb-0 small fst-italic">"${
          req.reject_message || ''
        }"</p></blockquote>
      </div>
      <a href="${searchUrl}" class="btn btn-royal-blue waves-effect waves-light mt-4 mb-2 width-sm">${translations.find_another_specialist || 'Find Another Specialist'}</a>
    </div></div>`
  }

  function getCancelledCard(req) {
    const searchUrl = `${baseUrl}second_opinion_view?search=${encodeURIComponent(
      `${req.first_name} ${req.last_name}`
    )}`

    const costDisplay =
      req.cost_request == 0
        ? `<span class="badge bg-bright-turquoise">${
            translations.free || 'Free'
          }</span>`
        : `$${req.cost_request}`

    return `<div class="card card-pricing"><div class="card-body text-center">
      <p class="card-pricing-plan-name fw-bold text-uppercase">${
        translations[req.type_request]
      }</p>
      <span class="text-midnight-blue"><i class="bi-slash-circle fs-1"></i></span>
      <h2 class="card-pricing-price text-midnight-blue">${
        translations.cancelled_by_you || 'Canceled by You'
      }</h2>
      <ul class="card-pricing-features list-unstyled">
        <li><strong>${
          translations.with || 'With'
        }:</strong> Dr. ${req.first_name} ${req.last_name}</li>
        <li><strong>${
          translations.cancelled_on || 'Canceled On'
        }:</strong> ${formatDateTime(req.updated_at, true)}</li>
        <li><strong>${translations.cost || 'Cost'}:</strong> ${costDisplay}</li>
      </ul>
      <a href="${searchUrl}" class="btn btn-midnight-blue waves-effect waves-light mt-4 mb-2 width-sm">${translations.book_again || 'Book Again'}</a>
    </div></div>`
  }
  function handleItemClick(id, skipAnimation = false) {
    activeRequestId = id
    $('#requestList .list-group-item').removeClass(
      'border border-sapphire-blue'
    )
    $(`#requestList .list-group-item[data-id="${id}"]`).addClass(
      'border border-sapphire-blue'
    )

    if (skipAnimation) {
      fetchRequestDetails(id)
    } else {
      // Animación suave al hacer clic
      $('#requestDetailsContainer').animate({ opacity: 0 }, 150, function () {
        fetchRequestDetails(id) // Carga detalles (y loader)
        $(this).animate({ opacity: 1 }, 150)
      })
    }
  }

  // --- MANEJADORES DE EVENTOS ---

  $('#requestList').on('click', '.list-group-item', function (e) {
    e.preventDefault()
    handleItemClick($(this).data('id'))
  })

  $('#statusFilters').on('click', 'a', function (e) {
    e.preventDefault()
    $('#statusFilters a').removeClass('active')
    $(this).addClass('active')
    currentFilters.status = $(this).data('status')
    fetchRequests() // Llama a la API en lugar de filtrar localmente
  })

  $('#sortSelect').on('change', function () {
    currentFilters.sort = $(this).val()
    applyFiltersAndRender()
  })

  $('#requestDetailsContainer').on('click', '.btn-cancel-request', function () {
    const requestId = $(this).data('id')

    Swal.fire({
      title: translations.cancel_confirm_title || 'Are you sure?',
      text:
        translations.cancel_confirm_text || "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText:
        translations.cancel_confirm_button || 'Yes, cancel it!',
      cancelButtonText: translations.cancel_cancel_button || 'No, keep it',
    }).then((result) => {
      if (result.isConfirmed) {
        // --- INICIO DE MODIFICACIÓN ---
        fetch(`${baseUrl}second-opinion-requests-cancel/${requestId}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
        })
          .then((response) => response.json())
          .then((response) => {
            if (response.value) {
              Swal.fire(
                translations.cancelled_success_title || 'Cancelled!',
                translations.cancelled_success_text ||
                  'Your request has been cancelled.',
                'success'
              )
              fetchRequests() // Recarga la lista
            } else {
              Swal.fire(
                translations.cancelled_error_title || 'Error!',
                response.message ||
                  translations.cancelled_error_text ||
                  'Could not cancel the request.',
                'error'
              )
            }
          })
          .catch((error) => {
            console.error('Cancel request error:', error)
            Swal.fire(
              translations.cancelled_error_title || 'Error!',
              translations.cancelled_error_text ||
                'Could not cancel the request.',
              'error'
            )
          })
        // --- FIN DE MODIFICACIÓN ---
      }
    })
  })
  $('#reviewModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget)
    const specialistName = button.data('specialist-name')
    const requestDate = button.data('request-date')
    const requestId = button.data('request-id')
    const specialistId = button.data('specialist-id')
    const userId = button.data('user-id')

    const modal = $(this)

    // Poblar textos y datos del modal usando el objeto de traducciones
    modal
      .find('.modal-title')
      .text(translations.review_modal_title || 'Leave a Review')

    let contextText =
      translations.review_modal_context ||
      'You are reviewing your consultation with <strong>{specialistName}</strong> that took place on {requestDate}.'
    contextText = contextText
      .replace('{specialistName}', specialistName)
      .replace('{requestDate}', requestDate)
    modal.find('#reviewContext').html(contextText)

    modal
      .find('label[for="reviewRating"]')
      .text(translations.review_modal_rating_label || 'Your Overall Rating')
    modal
      .find('label[for="reviewComment"]')
      .text(translations.review_modal_comments_label || 'Your Comments')
    modal
      .find('#reviewComment')
      .attr(
        'placeholder',
        translations.review_modal_comment_placeholder || 'Share details...'
      )
    modal
      .find('.modal-footer .btn-secondary')
      .text(translations.review_modal_close_btn || 'Close')
    modal
      .find('#submitReviewBtn')
      .text(translations.review_modal_submit_btn || 'Submit Review')
    document
      .getElementById('submitReviewBtn')
      .setAttribute('data-user-id', userId)

    // Poblar datos ocultos
    modal.find('#reviewSecondOpinionId').val(requestId)
    modal.find('#reviewSpecialistId').val(specialistId)
    modal.find('#reviewComment').val('')

    modal.find('#reviewRating').rateYo({
      rating: 0,
      fullStar: true,
      starWidth: '24px',
      ratedFill: '#06add9',
    })
  })

  $('#submitReviewBtn').on('click', function () {
    const rating = $('#reviewRating').rateYo('rating')
    const currentUserId = $(this).data('user-id')
    const comment = $('#reviewComment').val()
    const secondOpinionId = $('#reviewSecondOpinionId').val()
    const specialistId = $('#reviewSpecialistId').val()

    if (rating === 0) {
      Swal.fire(
        translations.review_validation_error_title || 'Validation Error',
        translations.review_validation_rating_text ||
          'Please provide a rating.',
        'error'
      )
      return
    }

    const formData = new FormData()
    formData.append('rating', rating)
    formData.append('comment', comment)
    formData.append('second_opinion_id', secondOpinionId)
    formData.append('specialist_id', specialistId)
    formData.append('user_id', currentUserId)

    // --- INICIO DE MODIFICACIÓN ---
    fetch(`${baseUrl}specialist-reviews`, {
      method: 'POST',
      body: formData, // fetch maneja FormData automáticamente
    })
      .then((response) => response.json())
      .then((response) => {
        if (response.value) {
          $('#reviewModal').modal('hide')
          Swal.fire(
            translations.review_success_title || 'Success!',
            translations.review_success_text ||
              'Your review has been submitted successfully.',
            'success'
          )
          fetchRequests() // Recarga la lista
        } else {
          Swal.fire(
            translations.review_user_error_title || 'Error',
            response.message ||
              translations.review_submit_error_text ||
              'Could not submit your review.',
            'error'
          )
        }
      })
      .catch((error) => {
        console.error('Submit review error:', error)
        Swal.fire(
          translations.review_user_error_title || 'Error',
          translations.review_unexpected_error_text ||
            'An unexpected error occurred. Please try again.',
          'error'
        )
      })
    // --- FIN DE MODIFICACIÓN ---
  })

  // Carga inicial
  fetchRequests()
})
