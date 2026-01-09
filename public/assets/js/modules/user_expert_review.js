import { createExamViewer } from '../components/examViewer.js'
import { formatDateTime } from '../helpers/validacionesEspeciales.js'

document.addEventListener('DOMContentLoaded', function () {
  // --- Global Config from PHP ---
  const { baseUrl, currentLang, translations, userTimezone } = APP_CONFIG

  // --- DOM References ---
  const searchInput = document.getElementById('search-input')
  const sortSelect = document.getElementById('sort-select')
  const container = document.getElementById('specialist-cards-container')
  const loadingIndicator = document.getElementById('loading-indicator')
  const endMessage = document.getElementById('end-message')

  // --- Filter DOM References ---
  const applyFiltersBtn = document.getElementById('apply-filters-btn')
  const resetFiltersBtn = document.getElementById('reset-filters-btn')
  const filtersOffcanvasElement = document.getElementById('filtersOffcanvas')
  const filtersOffcanvas = new bootstrap.Offcanvas(filtersOffcanvasElement)

  // --- Modal References ---
  const specialistModal = new bootstrap.Modal(
    document.getElementById('specialistProfileModal')
  )
  const modalLoader = document.getElementById('modal-loader')
  const modalContentContainer = document.getElementById(
    'modal-content-container'
  )
  const certificateModal = new bootstrap.Modal(
    document.getElementById('certificateViewerModal')
  )
  const certificateIframe = document.getElementById('certificate-iframe')
  const certificateImage = document.getElementById('certificate-image')
  const certificateModalLabel = document.getElementById(
    'certificateViewerModalLabel'
  )

  // --- Wizard Modals ---
  const bookingWizardModalEl = document.getElementById('booking-wizard-modal')
  const bookingWizardModal = new bootstrap.Modal(bookingWizardModalEl)
  const evaluationWizardModalEl = document.getElementById(
    'evaluation-wizard-modal'
  )
  const evaluationWizardModal = new bootstrap.Modal(evaluationWizardModalEl)
  let tableLocale = currentLang.toLowerCase() === 'es' ? 'es-ES' : 'en-US'

  // --- State ---
  let currentOffset = 0,
    limit = 12,
    isLoading = false,
    hasMoreData = true
  let currentQuery = '',
    currentOrder = 'default',
    currentFilters = {}
  let currentWizardData = {
    appointment: {},
    specialist: null,
    panels: [],
  }
  let dateFilterInstance = null
  let calendar = null
  let wizardInstance = null
  let evaluationWizardInstance = null
  let lastActiveWizardModal = null
  let lastRenderedPricingId = null
  let currentDateRange = []

  const EVENT_COLORS = {
    available: {
      backgroundColor: '#0c204c',
      borderColor: '#0c204c',
      textColor: '#ffffff',
    },
    blocked: {
      backgroundColor: '#82dded',
      borderColor: '#82dded',
      textColor: '#122657',
    },
    selected: {
      backgroundColor: '#2852af',
      borderColor: '#2852af',
      textColor: '#ffffff',
    },
  }

  const API_URL = baseUrl + 'specialists/search'
  const SPECIALIST_DETAIL_API_URL = baseUrl + 'specialist-second-opinion/' // MODIFICADO
  const CALENDAR_DATA_API_URL = baseUrl + 'second-opinion-slots' // NUEVO
  const SPECIALTIES_API_URL = baseUrl + 'specialties'
  const PANELS_API_URL = baseUrl + 'second-opinion-test-panels'
  const BIOMARKER_INFO_API_URL = baseUrl + 'biomarkers/info'
  const BIOMARKER_COMMENTS_API_URL = baseUrl + 'biomarker-comments'
  const CREATE_REQUEST_API_URL = baseUrl + 'second-opinion-requests'

  // ===================================================================
  // INSTANCIA DEL VISUALIZADOR DE EXÁMENES
  // ===================================================================
  const examViewer = createExamViewer({
    apiEndpoints: {
      biomarkerInfo: BIOMARKER_INFO_API_URL,
      biomarkerComments: BIOMARKER_COMMENTS_API_URL,
    },
    categoricalBiomarkers: ['albumin', 'creatinine'],
    translations: translations,
    lang: currentLang,
    Swal: Swal, // Pasa la instancia global de SweetAlert
    role: 'patient',
  })

  //======================================================================
  // HELPERS
  //======================================================================

  /**
   * Utiliza la API Intl para obtener el offset correcto (ej. -04:00) y construir un string ISO 8601 válido.
   * @param {string} dateString - La fecha en formato 'YYYY-MM-DD'.
   * @param {string} timeStr - La hora en formato 'HH:mm:ss'.
   * @param {string} timezone - El nombre de la zona horaria IANA (ej. 'America/Caracas').
   * @returns {Date} - El objeto Date final con el timestamp UTC correcto.
   */
  const getDateFromTimezone = (dateString, timeStr, timezone) => {
    try {
      const tempDateForOffset = new Date(`${dateString}T12:00:00Z`)
      const formatter = new Intl.DateTimeFormat('en-US', {
        timeZone: timezone,
        timeZoneName: 'longOffset',
        hour: 'numeric',
      })
      const parts = formatter.formatToParts(tempDateForOffset)
      const offsetPart = parts.find((part) => part.type === 'timeZoneName')

      if (!offsetPart || !offsetPart.value.startsWith('GMT')) {
        console.error(
          `Could not determine offset for timezone: ${timezone}. Falling back to UTC.`
        )
        return new Date(`${dateString}T${timeStr}Z`)
      }

      let offsetStr = offsetPart.value.replace('GMT', '')
      const sign = offsetStr.includes('-') ? '-' : '+'
      offsetStr = offsetStr.replace(/[-+]/, '')
      let hours, minutes
      if (offsetStr.includes(':')) {
        ;[hours, minutes] = offsetStr.split(':')
      } else {
        hours = offsetStr
        minutes = '0'
      }
      const finalOffset = `${sign}${hours.padStart(2, '0')}:${minutes.padStart(
        2,
        '0'
      )}`
      const fullISOString = `${dateString}T${timeStr}${finalOffset}`
      return new Date(fullISOString)
    } catch (e) {
      console.error(
        `Error creating Date for ${dateString} ${timeStr} in ${timezone}`,
        e
      )
      return new Date(`${dateString}T${timeStr}Z`)
    }
  }

  const convertTimeForUserDisplay = (
    dateString,
    timeStr,
    specialistTz,
    userTz
  ) => {
    // const todayString = new Date().toISOString().split('T')[0]
    const correctDate = getDateFromTimezone(dateString, timeStr, specialistTz)
    const userTime = correctDate.toLocaleTimeString(currentLang, {
      hour: '2-digit',
      minute: '2-digit',
      timeZone: userTz,
      hour12: false,
    })
    const originalTzAbbr =
      new Intl.DateTimeFormat(currentLang, {
        timeZoneName: 'short',
        timeZone: specialistTz,
      })
        .formatToParts(correctDate)
        .find((part) => part.type === 'timeZoneName')?.value || specialistTz
    return { time: userTime, originalTzAbbr }
  }

  const debounce = (func, delay = 400) => {
    let timeout
    return (...args) => {
      clearTimeout(timeout)
      timeout = setTimeout(() => func.apply(this, args), delay)
    }
  }

  const formatDuration = (minutes) => {
    const mins = parseInt(minutes, 10)
    if (isNaN(mins) || mins <= 0) {
      return ''
    }
    const h = Math.floor(mins / 60)
    const m = mins % 60
    let parts = []
    if (h > 0) {
      parts.push(`${h} H`)
    }
    if (m > 0) {
      parts.push(`${m} Min`)
    }
    return parts.join(' ')
  }

  //======================================================================
  // INITIALIZATION & SEARCH
  //======================================================================

  const loadSpecialties = async () => {
    try {
      const response = await fetch(SPECIALTIES_API_URL)
      if (!response.ok)
        throw new Error('Network response was not ok for specialties.')
      const result = await response.json()
      if (result.data && result.data.length > 0) {
        const specialtiesSelect = document.getElementById('filter-specialties')
        const nameKey = currentLang === 'ES' ? 'name_es' : 'name_en'
        result.data.forEach((specialty) => {
          const option = new Option(
            specialty[nameKey],
            specialty.specialty_id,
            false,
            false
          )
          specialtiesSelect.appendChild(option)
        })
        $('#filter-specialties').select2({
          placeholder: translations.select_specialties_placeholder,
          width: '100%',
          dropdownParent: $('#filtersOffcanvas'),
        })
      }
    } catch (error) {
      console.error('Could not load specialties:', error)
    }
  }

  const renderStars = (rating) => {
    if (rating === null || rating === undefined)
      return `<span class="text-muted fst-italic">${translations.no_reviews_text}</span>`
    let starsHtml = ''
    const fullStars = Math.floor(rating)
    const halfStar = rating % 1 >= 0.5 ? 1 : 0
    const emptyStars = 5 - fullStars - halfStar
    for (let i = 0; i < fullStars; i++)
      starsHtml += '<i class="mdi mdi-star text-accent-alt"></i>'
    if (halfStar)
      starsHtml += '<i class="mdi mdi-star-half-full text-accent-alt"></i>'
    for (let i = 0; i < emptyStars; i++)
      starsHtml += '<i class="mdi mdi-star-outline text-accent-alt"></i>'
    return starsHtml
  }

  const createSpecialistCard = (specialist, index) => {
    let avatar = specialist.specialist_image
      ? `${baseUrl}uploads/specialist/user_${specialist.specialist_id}.jpg`
      : specialist.avatar_url ||
        'https://placehold.co/128x128/EFEFEF/AAAAAA&text=NA'
    const website = specialist.website_url
      ? `<a href="${
          specialist.website_url
        }" target="_blank" class="text-info">${specialist.website_url.replace(
          /^(https?:\/\/)?(www\.)?/,
          ''
        )}</a>`
      : ''
    const ratingText = specialist.rating_text || 'N/A'
    const animationDelay = (index % limit) * 100

    return `
            <div class="col-lg-4 col-md-6 mb-4 specialist-card-animation" style="animation-delay: ${animationDelay}ms;">
                <div class="text-center card h-100">
                    <div class="card-body d-flex flex-column">
                        <img src="${avatar}" class="rounded-circle img-thumbnail avatar-xl mx-auto" alt="profile-image" onerror="this.onerror=null;this.src='https://placehold.co/128x128/EFEFEF/AAAAAA&text=NA';">
                        <h4 class="mt-3 mb-0"><a data-action="view-profile" data-specialist-id="${
                          specialist.specialist_id
                        }" href="#" class="text-accent-alt">${
      specialist.full_name
    }</a></h4>
                        <p class="text-muted">${
                          specialist.specialty_display || ''
                        }</p>
                        <p class="text-muted text-truncate px-2" style="min-height: 24px;">${
                          specialist.handle || ''
                        } ${
      specialist.handle && website ? ' | ' : ''
    } ${website}</p>
                        
                        <div class="d-flex flex-wrap justify-content-center gap-1 my-2">
                            <button type="button" class="btn btn-sm btn-accent waves-effect waves-light" data-action="view-profile" data-specialist-id="${
                              specialist.specialist_id
                            }">${translations.view_profile_button}</button>
                            <button type="button" class="btn btn-sm btn-success-dark waves-effect waves-light" data-action="request-evaluation" data-specialist-id="${
                              specialist.specialist_id
                            }">${
      translations.request_evaluation_button
    }</button>
                            <button type="button" class="btn btn-sm btn-secondary-color waves-effect waves-light" data-action="book-appointment" data-specialist-id="${
                              specialist.specialist_id
                            }">${translations.book_appointment_button}</button>
                        </div>

                        <div class="row mt-auto pt-3 border-top">
                            <div class="col-4"><h5 class="mb-1 fw-normal text-secondary-color">${
                              specialist.lab_reports_evaluated || 0
                            }</h4><p class="mb-0 text-muted small">${
      translations.reports_evaluated_label
    }</p></div>
                            <div class="col-4"><h5 class="mb-1 fw-normal text-secondary-color">${
                              specialist.consultations_completed || 0
                            }</h4><p class="mb-0 text-muted small">${
      translations.consultations_completed_label
    }</p></div>
                            <div class="col-4"><h5 class="mb-1 fw-normal text-secondary-color">${ratingText}</h4><p class="mb-1 text-muted small">${
      translations.rating_label
    }</p><div class="text-blue">${renderStars(
      specialist.avg_rating
    )}</div></div>
                        </div>
                    </div>
                </div>
            </div>`
  }

  const fetchSpecialists = async (append = false) => {
    if (isLoading || (!hasMoreData && append)) return
    isLoading = true
    loadingIndicator.style.display = 'block'
    if (!append) {
      container.innerHTML = ''
      endMessage.style.display = 'none'
      currentOffset = 0
      hasMoreData = true
    }
    const payload = {
      q: currentQuery,
      order: currentOrder,
      limit: limit,
      offset: currentOffset,
      ...currentFilters,
    }
    try {
      const response = await fetch(API_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify(payload),
      })
      if (!response.ok) throw new Error(`Server error: ${response.statusText}`)
      const result = await response.json()
      if (result.data && result.data.length > 0) {
        container.insertAdjacentHTML(
          'beforeend',
          result.data.map(createSpecialistCard).join('')
        )
        currentOffset += result.data.length
        if (result.data.length < limit) {
          hasMoreData = false
          endMessage.style.display = 'block'
        }
      } else {
        hasMoreData = false
        if (currentOffset === 0)
          container.innerHTML = `<div class="col-12"><div class="alert bg-white-light text-center">${translations.no_specialists_found}</div></div>`
        else endMessage.style.display = 'block'
      }
    } catch (error) {
      console.error('Error fetching specialists:', error)
      container.innerHTML = `<div class="col-12"><div class="alert alert-danger text-center">${translations.error_loading_specialists}</div></div>`
    } finally {
      isLoading = false
      loadingIndicator.style.display = 'none'
    }
  }

  const handleApplyFilters = () => {
    const filters = {
      availability: {},
    }
    const specialtyIds = $('#filter-specialties').val()
    if (specialtyIds && specialtyIds.length > 0)
      filters.specialty_ids = specialtyIds
    if (document.getElementById('verifiedSwitch').checked)
      filters.verified = true
    const languages = Array.from(
      document.querySelectorAll(
        '#v-pills-general .form-check-input[type="checkbox"]:checked'
      )
    )
      .filter((cb) => cb.id.startsWith('lang-'))
      .map((cb) => cb.value)
    if (languages.length > 0) filters.languages = languages
    const minCost = parseInt(document.getElementById('costRange').value, 10)
    if (minCost > 0) filters.min_cost = minCost
    const minConsultations = parseInt(
      document.getElementById('consultationsRange').value,
      10
    )
    if (minConsultations > 0) filters.min_consultations = minConsultations
    const minEvaluations = parseInt(
      document.getElementById('evaluationsRange').value,
      10
    )
    if (minEvaluations > 0) filters.min_evaluations = minEvaluations
    const ratingFilter = document.querySelector(
      'input[name="ratingFilter"]:checked'
    )
    if (ratingFilter) filters.min_rating = parseFloat(ratingFilter.value)
    const availableDate = document.getElementById('date-picker').value
    if (availableDate) filters.availability.date = availableDate
    if (Object.keys(filters.availability).length === 0)
      delete filters.availability
    currentFilters = filters
    filtersOffcanvas.hide()
    fetchSpecialists(false)
  }

  const handleResetFilters = () => {
    $('#filter-specialties').val(null).trigger('change')
    document
      .querySelectorAll('#v-pills-general input[type="checkbox"]')
      .forEach((el) => (el.checked = false))
    const costRange = document.getElementById('costRange')
    costRange.value = 0
    costRange.nextElementSibling.value = '$0'
    const consultRange = document.getElementById('consultationsRange')
    consultRange.value = 0
    consultRange.nextElementSibling.value = '0'
    const evalRange = document.getElementById('evaluationsRange')
    evalRange.value = 0
    evalRange.nextElementSibling.value = '0'
    document
      .querySelectorAll('input[name="ratingFilter"]')
      .forEach((radio) => (radio.checked = false))
    document.getElementById('date-picker').value = ''
    currentFilters = {}
    filtersOffcanvas.hide()
    fetchSpecialists(false)
  }

  //======================================================================
  // PROFILE MODAL & WIZARD LOGIC
  //======================================================================

  const fetchSpecialistData = async (specialistId) => {
    if (
      currentWizardData.specialist &&
      currentWizardData.specialist.specialist_id == specialistId
    ) {
      return true
    }
    try {
      const response = await $.ajax({
        url: `${SPECIALIST_DETAIL_API_URL}${specialistId}`,
        type: 'GET',
        dataType: 'json',
      })
      if (!response.value) throw new Error(response.message)
      currentWizardData.specialist = response.data
      return true
    } catch (e) {
      console.error('Could not load specialist data:', e)
      Swal.fire(
        translations.error_title,
        translations.load_specialist_data_error,
        'error'
      )
      return false
    }
  }

  const fetchAndShowSpecialistDetails = async (specialistId) => {
    modalLoader.style.display = 'block'
    modalContentContainer.style.display = 'none'
    specialistModal.show()

    if (await fetchSpecialistData(specialistId)) {
      populateModal(currentWizardData.specialist)
      modalLoader.style.display = 'none'
      modalContentContainer.style.display = 'block'
    } else {
      modalContentContainer.innerHTML = `<p class="text-danger">An error occurred while fetching the specialist profile.</p>`
      modalLoader.style.display = 'none'
      modalContentContainer.style.display = 'block'
    }
  }

  const populateModal = (data) => {
    let avatar = data.specialist_image
      ? `${baseUrl}uploads/specialist/user_${data.specialist_id}.jpg`
      : data.avatar_url || 'https://placehold.co/128x128/EFEFEF/AAAAAA&text=NA'
    let primaryLocation =
      data.locations.find((loc) => loc.is_primary == 1) || data.locations[0]
    let locationString = primaryLocation
      ? `${primaryLocation.city_name}, ${primaryLocation.country_name}`
      : 'N/A'

    let primaryPhone =
      data.phones.find((p) => p.is_primary == 1) || data.phones[0]
    let phoneHTML = primaryPhone
      ? `<a href="tel:${primaryPhone.phone_number}" class="text-body">${primaryPhone.phone_number}</a>`
      : 'N/A'
    let primaryEmail = data.emails.find((p) => p.is_primary == 1) || {
      email: data.email,
    }
    let emailHTML = primaryEmail
      ? `<a href="mailto:${primaryEmail.email}" class="text-body">${primaryEmail.email}</a>`
      : 'N/A'

    let socialLinksHTML =
      data.social_links && data.social_links.length > 0
        ? data.social_links
            .map(
              (link) =>
                `<a href="${link.url}" target="_blank" class="text-icons me-1" title="${link.platform}"><i class="mdi mdi-${link.platform}"></i></a>`
            )
            .join('')
        : 'N/A'

    // --- TABS CONTENT ---
    let availabilityHTML = ''
    if (data.availability && data.availability.length > 0) {
      availabilityHTML = data.availability
        .map((slot) => {
          const specialistTz = slot.timezone || data.timezone
          const todayString = new Date().toISOString().split('T')[0] // <-- AÑADE ESTO
          const start = convertTimeForUserDisplay(
            todayString, // <-- PASA LA FECHA
            slot.start_time,
            specialistTz,
            userTimezone
          )
          const end = convertTimeForUserDisplay(
            todayString, // <-- PASA LA FECHA
            slot.end_time,
            specialistTz,
            userTimezone
          )
          return `<div class="d-flex justify-content-between align-items-center border-bottom py-1">
                      <strong>${
                        translations[`weekday_${slot.weekday.toUpperCase()}`] ||
                        slot.weekday
                      }:</strong> 
                      <span class="badge bg-secondary-lighten text-secondary fw-normal fs-6">
                        ${start.time} - ${end.time} (${end.originalTzAbbr})
                      </span>
                  </div>`
        })
        .join('')
    } else {
      availabilityHTML = `<p class="text-muted">${translations.no_availability_text}</p>`
    }

    let availabilitySection = `
    <h5 class="mb-2">${translations.weekly_availability_title}</h5>
    <div class="list-group list-group-flush mb-3">${availabilityHTML}</div>
    <div class="alert bg-white-light text-info border-0 mt-2" role="alert">
        <i class="mdi mdi-information-outline me-1"></i>
        ${translations.timezone_clarification_specialist} <strong>${
      data.timezone || 'N/A'
    }</strong>
    </div>`

    let pricingHTML =
      data.pricing && data.pricing.length > 0
        ? data.pricing
            .map((p) => {
              const durationText = formatDuration(p.duration_services)

              return `<p class="mb-1">
                          <strong>${translations[p.service_type]}:</strong>
                          ${
                            durationText
                              ? `<span class="text-muted">(${durationText})</span>`
                              : ''
                          }
                          $${p.price_usd} - <em class="text-muted">${
                p.description
              }</em>
                        </p>`
            })
            .join('')
        : `<p class="text-muted">${translations.no_credentials_text}</p>`

    let credentialsTabHTML = `<div class="row"><div class="col-md-6">${availabilitySection}</div><div class="col-md-6"><h5 class="mb-2">${translations.pricing_title}</h5>${pricingHTML}</div></div>`

    let certificationsHTML =
      data.certifications && data.certifications.length > 0
        ? data.certifications
            .map(
              (c) => `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong>${c.title}</strong>
                        <p class="mb-0 text-muted">${c.description}</p>
                    </div>
                    <button class="btn btn-sm action-icon" data-cert-url="${baseUrl}${c.file_url.replace(
                /\\/g,
                '/'
              )}" data-cert-title="${c.title}">
                        <i class="mdi mdi-eye"></i>
                    </button>
                </div>`
            )
            .join('')
        : `<p class="text-muted">${translations.no_certifications_text}</p>`

    let reviewsHTML =
      data.reviews && data.reviews.length > 0
        ? data.reviews
            .map((review, index) => {
              const userImage = review.profile_image
                ? `${baseUrl}${review.profile_image}`
                : `${baseUrl}public/assets/images/users/user_boy.png`
              const reviewDate = new Date(review.created_at).toLocaleDateString(
                currentLang === 'ES' ? 'es-ES' : 'en-US'
              )
              return `
                <div class="carousel-item ${index === 0 ? 'active' : ''}">
                    <div class="text-body p-2 w-75 mx-auto">
                        <div class="d-flex align-items-center p-0">
                            <img src="${userImage}" class="me-2 rounded-circle" height="42" alt="${
                review.full_name
              }" onerror="this.onerror=null;this.src='${baseUrl}public/assets/images/users/user_boy.png';">
                            <div class="w-100 px-4 text-start">
                                <h5 class="mt-0 mb-0 font-14">${
                                  review.full_name
                                }</h5>
                                <div class="text-blue">${renderStars(
                                  review.rating
                                )}</div>
                            </div>
                            <span class="float-end text-muted fw-normal font-12 ms-auto">${reviewDate}</span>
                        </div>
                        <p class="mt-1 mb-0 text-muted font-14 text-start">
                           "${review.comment}"
                        </p>
                    </div>
                </div>`
            })
            .join('')
        : `<div class="carousel-item active"><p class="text-muted text-center">${translations.no_reviews_text_modal}</p></div>`

    const modalHTML = `
            <div class="card text-center">
                <div class="card-body">
                    <img alt="profile-image" class="rounded-circle avatar-lg img-thumbnail" src="${avatar}" onerror="this.onerror=null;this.src='https://placehold.co/128x128/EFEFEF/AAAAAA&text=NA';" />
                    <h4 class="mb-0 mt-2">${data.first_name} ${
      data.last_name
    }</h4>
                    <p class="text-muted">${data.title_display_name} | ${
      data.specialty_display_name
    }</p>
                    <div class="d-flex flex-wrap justify-content-center gap-2 my-2">
                      
                    </div>
                    <div class="text-start mt-3">
                        <h4 class="font-13 text-uppercase">${
                          translations.about_me_title
                        }</h4>
                        <p class="text-muted font-13 mb-3">${
                          data.bio || 'N/A'
                        }</p>
                        <p class="text-muted mb-2 font-13"><strong>${
                          translations.full_name_label
                        }</strong> <span class="ms-2">${data.first_name} ${
      data.last_name
    }</span></p>
                        <p class="text-muted mb-2 font-13"><strong>${
                          translations.mobile_label
                        }</strong><span class="ms-2">${phoneHTML}</span></p>
                        <p class="text-muted mb-2 font-13"><strong>${
                          translations.email_label
                        }</strong> <span class="ms-2">${emailHTML}</span></p>
                        <p class="text-muted mb-1 font-13"><strong>${
                          translations.location_label
                        }</strong> <span class="ms-2">${locationString}</span></p>
                        <p class="text-muted mb-1 font-13"><strong>${
                          translations.social_media_label
                        }</strong> <span class="ms-2">${socialLinksHTML}</span></p>
                    </div>
                     <div class="row mt-4">
                        <div class="col-4"><div class="mt-3"><h4>${
                          data.lab_reports_evaluated || 0
                        }</h4><p class="mb-0 text-muted">${
      translations.reports_evaluated_label
    }</p></div></div>
                        <div class="col-4"><div class="mt-3"><h4>${
                          data.consultations_completed || 0
                        }</h4><p class="mb-0 text-muted">${
      translations.consultations_completed_label
    }</p></div></div>
                        <div class="col-4"><div class="mt-3"><h4>${
                          data.average_rating
                            ? data.average_rating.toFixed(1) + '/5'
                            : 'N/A'
                        }</h4><p class="mb-0 text-muted">${
      translations.rating_label
    }</p><div class="text-blue">${renderStars(
      data.average_rating
    )}</div></div></div>
                    </div>
                </div>
                <hr class="my-2" />
                <div class="card-body p-2">
                    <h4 class="header-title mb-3">${
                      translations.more_info_title
                    }</h4>
                    <ul class="nav nav-pills navtab-bg nav-justified" role="tablist">
                        <li class="nav-item" role="presentation"><a href="#modal-availability" data-bs-toggle="tab" class="nav-link active" role="tab">${
                          translations.availability_pricing_tab
                        }</a></li>
                        <li class="nav-item" role="presentation"><a href="#modal-certifications" data-bs-toggle="tab" class="nav-link" role="tab">${
                          translations.certifications_tab
                        }</a></li>
                        <li class="nav-item" role="presentation"><a href="#modal-reviews" data-bs-toggle="tab" class="nav-link" role="tab">${
                          translations.reviews_tab
                        }</a></li>
                    </ul>
                    <div class="tab-content mt-3 text-start p-2">
                        <div class="tab-pane active" id="modal-availability" role="tabpanel">${credentialsTabHTML}</div>
                        <div class="tab-pane" id="modal-certifications" role="tabpanel">${certificationsHTML}</div>
                        <div class="tab-pane" id="modal-reviews" role="tabpanel">
                            <div id="reviewsCarousel" class="carousel slide pointer-event" data-bs-ride="carousel">
                                <div class="carousel-inner" role="listbox">${reviewsHTML}</div>
                                <a class="carousel-control-prev" href="#reviewsCarousel" role="button" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1);"></span></a>
                                <a class="carousel-control-next" href="#reviewsCarousel" role="button" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(1);"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`

    modalContentContainer.innerHTML = modalHTML
  }

  const initGenericWizard = (modalEl, totalSteps, isBooking) => {
    const navLinks = modalEl.querySelectorAll('.nav-pills .nav-link')
    const prevBtn = modalEl.querySelector('.wizard-prev-btn')
    const nextBtn = modalEl.querySelector('.wizard-next-btn')
    const finishBtn = modalEl.querySelector('.wizard-finish-btn')
    let currentStep = 0

    // Habilita o deshabilita los tabs del wizard (después del paso 1)
    const updateTabStates = (enabled) => {
      navLinks.forEach((link, index) => {
        // Afecta a todos los tabs DESPUÉS del de selección de servicio (index 1)
        if (index > 1) {
          if (enabled) {
            link.classList.remove('disabled')
          } else {
            link.classList.add('disabled')
          }
        }
      })
    }
    updateTabStates(false)

    const showStep = (stepIndex) => {
      if (stepIndex >= 0 && stepIndex < totalSteps) {
        currentStep = stepIndex
        const tabTrigger = new bootstrap.Tab(navLinks[stepIndex])
        tabTrigger.show()
      }
    }

    navLinks.forEach((link, index) => {
      link.addEventListener('show.bs.tab', (event) => {
        const stepToGo = index
        const appointmentDuration = currentWizardData.appointment.duration

        if (stepToGo > 1 && !currentWizardData.appointment.pricing_id) {
          event.preventDefault() // Detiene el cambio de tab
          return // No continuar
        }

        if (
          isBooking &&
          stepToGo === 3 &&
          (appointmentDuration === undefined || appointmentDuration === null)
        ) {
          event.preventDefault()
          Swal.fire({
            title: translations.select_service_title || 'Selección Requerida',
            text:
              translations.select_service_text ||
              'Por favor, selecciona un tipo de servicio antes de continuar al agendamiento.',
            icon: 'warning',
            confirmButtonText: translations.confirm_button_text || 'Entendido',
          })
        }

        // declarar traducciones para  select_service_title select_service_text
      })

      link.addEventListener('shown.bs.tab', () => {
        currentStep = index

        if (isBooking && index === 3) {
          const currentPricingId = currentWizardData.appointment.pricing_id

          if (!currentPricingId) {
            return
          }
          if (currentPricingId !== lastRenderedPricingId) {
            currentWizardData.appointment.datetime = null
            document.getElementById('selected-time-text').innerText =
              translations.no_time_selected_text
            if (calendar) calendar.destroy()
            initFullCalendar()

            lastRenderedPricingId = currentPricingId
          }
        }
        if (index === totalSteps - 1) {
          populateConfirmationStep(modalEl, isBooking)
        }
        prevBtn.style.display = index === 0 ? 'none' : 'inline-block'
        nextBtn.style.display =
          index === totalSteps - 1 ? 'none' : 'inline-block'
        finishBtn.style.display =
          index === totalSteps - 1 ? 'inline-block' : 'none'
      })
    })

    prevBtn.addEventListener('click', () => {
      if (currentStep > 0) showStep(currentStep - 1)
    })
    nextBtn.addEventListener('click', () => {
      // Si estamos en el paso 1 (Selección de Servicio) y no hay pricing_id...
      if (currentStep === 1 && !currentWizardData.appointment.pricing_id) {
        Swal.fire({
          title: translations.select_service_title || 'Selección Requerida',
          text:
            translations.select_service_text ||
            'Por favor, selecciona un tipo de servicio para continuar.',
          icon: 'warning',
          confirmButtonText: translations.confirm_button_text || 'Entendido',
        })
        return // No avanzar
      }

      // Lógica original
      if (currentStep < totalSteps - 1) showStep(currentStep + 1)
    })

    $(modalEl)
      .find('.appointment-type-container')
      .off('click')
      .on('click', '.select-service-btn', function (e) {
        e.preventDefault()
        const card = $(this).closest('.service-type-card')
        currentWizardData.appointment.type = card.data('service-type')
        currentWizardData.appointment.cost = card.data('cost')
        currentWizardData.appointment.pricing_id = card.data('pricing-id')
        currentWizardData.appointment.duration = parseInt(
          card.data('duration-services'),
          10
        )

        $(modalEl)
          .find('.service-type-card')
          .removeClass('border-sapphire-blue')
        card.addClass('border-sapphire-blue')

        // --- LÍNEA MODIFICADA ---
        // Ya no avanzamos automáticamente. Solo habilitamos los siguientes pasos.
        updateTabStates(true)
        // showStep(2) // <-- LÍNEA ORIGINAL ELIMINADA
      })
    showStep(0)
    return {
      showStep,
      updateTabStates, // <-- Exportamos la nueva función
    }
  }

  const openBookingWizard = async (specialistId) => {
    const loader = bookingWizardModalEl.querySelector('.wizard-loader')
    const container = bookingWizardModalEl.querySelector('.wizard-container')
    loader.style.display = 'block'
    container.style.display = 'none'

    wizardInstance.showStep(0) // <--- AÑADE ESTA LÍNEA PARA RESETEAR
    wizardInstance.updateTabStates(false)

    currentWizardData = {
      appointment: {},
    }
    currentDateRange = []

    if (!(await fetchSpecialistData(specialistId))) return

    const appointmentServiceTypes = ['consultation', 'follow_up']
    const consultationPricings = currentWizardData.specialist.pricing.filter(
      (p) => appointmentServiceTypes.includes(p.service_type.toLowerCase())
    )
    if (consultationPricings.length === 0) {
      Swal.fire(
        translations.no_consultation_services_title,
        translations.no_consultation_services_text,
        'warning'
      )
      return
    }

    bookingWizardModal.show()
    if (calendar) {
      calendar.destroy()
      calendar = null
    }

    bookingWizardModalEl.querySelector('#share-none-booking').checked = true
    const customUIBooking = bookingWizardModalEl.querySelector(
      '.custom-sharing-interface'
    )
    customUIBooking.classList.remove('show')
    customUIBooking.style.display = 'none'

    const pricingContainer = bookingWizardModalEl.querySelector(
      '.appointment-type-container'
    )
    pricingContainer.innerHTML = consultationPricings
      .map((p) => {
        const priceHTML =
          parseFloat(p.price_usd) === 0
            ? `<span class="badge bg-bright-turquoise fs-6">${
                translations.free_badge || 'Free'
              }</span>`
            : `<strong>${translations.cost_service_fee}:</strong> $${p.price_usd}`

        const durationText = formatDuration(p.duration_services)

        return `
        <div class="col-md-6">
            <div class="card ribbon-box border service-type-card" data-service-type="${
              translations[p.service_type]
            }" data-cost="${p.price_usd}" data-pricing-id="${
          p.pricing_id
        }" data-duration-services="${p.duration_services || 0}"> 
                <div class="card-body">
                    <h5 class="text-secondary float-start mt-0">
                      ${translations[p.service_type]}
                      ${
                        durationText
                          ? `<small class="text-muted fw-normal ms-2">(${durationText})</small>`
                          : ''
                      }
                    </h5>
                    <div class="ribbon-content">
                        <p class="card-text small">${p.description}</p>
                        <p class="card-text">${priceHTML}</p>
                        <button class="btn btn-accent-alt-outline btn-sm select-service-btn">${
                          translations.select_button_second
                        }</button>
                    </div>
                </div>
            </div>
        </div>`
      })
      .join('')

    await fetchAndPrepareSharingPanels(bookingWizardModalEl)
    setupGlobalDateFilter(bookingWizardModalEl)

    // wizardInstance = initGenericWizard(bookingWizardModalEl, 5, true)
    loader.style.display = 'none'
    container.style.display = 'block'
  }

  const openEvaluationWizard = async (specialistId) => {
    const loader = evaluationWizardModalEl.querySelector('.wizard-loader')
    const container = evaluationWizardModalEl.querySelector('.wizard-container')
    loader.style.display = 'block'
    container.style.display = 'none'
    currentWizardData = {
      appointment: {},
    }

    evaluationWizardInstance.showStep(0)
    evaluationWizardInstance.updateTabStates(false)

    currentDateRange = []

    if (!(await fetchSpecialistData(specialistId))) return

    const reviewPricings = currentWizardData.specialist.pricing.filter(
      (p) => p.service_type.toLowerCase() === 'review'
    )
    if (reviewPricings.length === 0) {
      Swal.fire(
        translations.no_review_services_title,
        translations.no_review_services_text,
        'warning'
      )
      return
    }

    evaluationWizardModal.show()

    evaluationWizardModalEl.querySelector('#share-none-eval').checked = true
    const customUIEval = evaluationWizardModalEl.querySelector(
      '.custom-sharing-interface'
    )
    customUIEval.classList.remove('show')
    customUIEval.style.display = 'none'

    const pricingContainer = evaluationWizardModalEl.querySelector(
      '.appointment-type-container'
    )
    pricingContainer.innerHTML = reviewPricings
      .map((p) => {
        // --- FIX: AÑADIR ESTA LÓGICA QUE FALTABA ---
        const priceHTML =
          parseFloat(p.price_usd) === 0
            ? `<span class="badge bg-bright-turquoise fs-6">${
                translations.free_badge || 'Free'
              }</span>`
            : `<strong>${translations.cost_service_fee}</strong> $${p.price_usd}`
        // --- FIN DEL FIX ---

        return `
        <div class="col-md-6">
            <div class="card ribbon-box border service-type-card" data-service-type="${
              translations[p.service_type]
            }" data-cost="${p.price_usd}" data-pricing-id="${p.pricing_id}"> 
                <div class="card-body">
                    <h5 class="text-secondary float-start mt-0">${
                      translations[p.service_type]
                    }</h5>
                    <div class="ribbon-content">
                        <p class="card-text small">${p.description}</p>
                        <p class="card-text">${priceHTML}</p>
                        <button class="btn btn-accent-alt-outline btn-sm select-service-btn">${
                          translations.select_button_second
                        }</button>
                    </div>
                </div>
            </div>
        </div>`
      })
      .join('')

    await fetchAndPrepareSharingPanels(evaluationWizardModalEl)
    setupGlobalDateFilter(evaluationWizardModalEl)

    // evaluationWizardInstance = initGenericWizard(
    //   evaluationWizardModalEl,
    //   4,
    //   false
    // )

    loader.style.display = 'none'
    container.style.display = 'block'
  }

  const fetchAndPrepareSharingPanels = async (modalEl) => {
    try {
      const response = await $.ajax({
        url: PANELS_API_URL,
        type: 'GET',
        dataType: 'json',
      })
      if (response.value) {
        currentWizardData.panels = response.data
        generateSharingAccordionShell(response.data, modalEl)
      } else {
        throw new Error(response.message)
      }
    } catch (e) {
      modalEl.querySelector(
        '.panels-accordion'
      ).innerHTML = `<p class="text-danger">${
        translations.error_loading_records || 'Could not load medical records.'
      }</p>`
    }
  }

  const generateSharingAccordionShell = (panelsData, modalEl) => {
    const accordionContainer = modalEl.querySelector('.panels-accordion')
    if (!panelsData || panelsData.length === 0) {
      accordionContainer.innerHTML = `<p class="text-muted">${
        translations.no_panels_available ||
        'No medical records available to share.'
      }</p>`
      return
    }
    const panelNameKey =
      currentLang === 'ES' ? 'display_name_es' : 'display_name'
    accordionContainer.innerHTML = panelsData
      .map((panelInfo) => {
        const panel = panelInfo.panel
        return `<div class="accordion-item" data-panel-id="${panel.panel_id}">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${panel.panel_id}-${modalEl.id}">
                               ${panel[panelNameKey]}
                            </button>
                        </h2>
                        <div id="collapse-${panel.panel_id}-${modalEl.id}" class="accordion-collapse collapse" data-bs-parent="#${modalEl.id} .panels-accordion">
                            <div class="accordion-body">
                                </div>
                        </div>
                    </div>`
      })
      .join('')

    accordionContainer
      .querySelectorAll('.accordion-collapse')
      .forEach((item) => {
        item.addEventListener('show.bs.collapse', (event) => {
          const panelId =
            event.target.closest('.accordion-item').dataset.panelId
          renderSharingAccordionContent(panelId, modalEl, currentDateRange)
        })
      })
  }

  const renderSharingAccordionContent = (panelId, modalEl, dateRange = []) => {
    const panelInfo = currentWizardData.panels.find(
      (p) => p.panel.panel_id === panelId
    )
    const body = modalEl.querySelector(
      `#collapse-${panelId}-${modalEl.id} .accordion-body`
    )

    const dateKey =
      panelInfo.user_records.length > 0
        ? Object.keys(panelInfo.user_records[0]).find((k) =>
            k.endsWith('_date')
          )
        : null
    const timeKey =
      panelInfo.user_records.length > 0
        ? Object.keys(panelInfo.user_records[0]).find((k) =>
            k.endsWith('_time')
          )
        : null

    const filteredExams = panelInfo.user_records.filter((exam) => {
      if (dateRange.length < 2 || !dateKey || !exam[dateKey]) return true
      const [start, end] = dateRange

      const dateParts = exam[dateKey].split('-')
      const examDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2])

      const inclusiveEnd = new Date(end)
      inclusiveEnd.setHours(23, 59, 59, 999)

      return examDate >= start && examDate <= inclusiveEnd
    })

    const formatDate = (isoDate) => {
      if (!isoDate || isoDate.split('-').length !== 3) return isoDate
      const [year, month, day] = isoDate.split('-')
      return `${month}/${day}/${year}`
    }
    const tableData = filteredExams.map((record) => ({
      ...record,
      date: dateKey ? formatDate(record[dateKey]) : 'N/A',
      time: timeKey ? record[timeKey].substring(0, 5) : 'N/A',
    }))

    const table = $(`#table-${panelId}`)
    if (table.length > 0) {
      table.bootstrapTable('load', tableData)
      return
    }

    const biomarkersHTML =
      panelInfo.biomarkers.length > 0
        ? '<div class="row">' +
          panelInfo.biomarkers
            .map(
              (b) =>
                // Usamos col-6 (2 columnas en móvil) y col-md-4 (3 columnas en desktop)
                `<div class="col-md-4 col-6 mb-2">
                 <div class="form-check">
                   <input 
                       class="form-check-input biomarker-checkbox" 
                       type="checkbox" 
                       id="bio-${b.biomarker_id}" 
                       value="${b.biomarker_id}" 
                       data-panel-id="${panelId}" 
                       checked>
                   <label class="form-check-label" for="bio-${b.biomarker_id}">${b.name}</label>
                 </div>
               </div>`
            )
            .join('') +
          '</div>'
        : `<p class="text-muted small">${
            translations.no_biomarkers_to_share ||
            'No hay biomarcadores disponibles para este panel.'
          }</p>`

    const tableId = `table-${panelId}`
    const toolbarId = `toolbar-${panelId}`
    const examsHTML =
      panelInfo.user_records.length > 0
        ? `
        <div id="${toolbarId}">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="select-all-${panelId}">
            <label class="form-check-label" for="select-all-${panelId}">${translations.sharing_select_all}</label>
          </div>
        </div>
        <table id="${tableId}" 
               data-toolbar="#${toolbarId}" 
               data-pagination="true" 
               data-page-size="5" 
               data-search="true" 
               data-maintain-meta-data="true" 
               data-click-to-select="false"></table>`
        : `<p class="text-muted small">${translations.no_exams_found}</p>`

    body.innerHTML = `
  <h6>${translations.sharing_biomarkers_title}</h6>
  <p class="text-muted small mb-2">${translations.sharing_biomarkers_desc}</p>
  ${biomarkersHTML}
  <hr>
  <h6>${translations.sharing_exams_title}</h6>
  <p class="text-muted small mb-2">${translations.sharing_exams_desc}</p>
  ${examsHTML}
  <small class="mt-2 d-block text-muted">
    * ${
      translations.share_help_text ||
      'You can review all shared data before submitting your request.'
    }
  </small>
`

    if (panelInfo.user_records.length > 0) {
      const tableEl = $(`#${tableId}`)
      const columns = [
        {
          field: 'state',
          checkbox: true,
          align: 'center',
        },
        {
          field: 'date',
          title: translations.exam_date_column,
          sortable: true,
          sorter: (a, b) => {
            const dateA = new Date(a)
            const dateB = new Date(b)
            if (dateA < dateB) return -1
            if (dateA > dateB) return 1
            return 0
          },
        },
        {
          field: 'time',
          title: translations.exam_time_column,
          sortable: true,
        },
        {
          field: 'action',
          title: translations.exam_action_column,
          align: 'center',
          formatter: (value, row) =>
            `<button class="btn btn-sm action-icon view-exam-btn" data-record-id="${row.record_id}" data-panel-id="${panelId}">
                        <i class="mdi mdi-eye"></i>
                    </button>`,
        },
      ]

      tableEl.bootstrapTable({
        locale: tableLocale,
        columns: columns,
        data: tableData,
        search: true,
        formatSearch: function () {
          return (
            translations.search_exams_placeholder ||
            'Search exams by date or type…'
          )
        },
      })
      $(`#select-all-${panelId}`).on('change', function () {
        tableEl.bootstrapTable(
          $(this).is(':checked') ? 'checkAll' : 'uncheckAll'
        )
      })
    }
  }

  const renderScheduleLegends = (referenceDate) => {
    const container = document.querySelector(
      '#booking-wizard-modal .available-times-container'
    )
    if (!container) return

    // Obtenemos el string YYYY-MM-DD de la fecha de referencia
    const dateString = referenceDate.toISOString().split('T')[0]

    const specialist = currentWizardData.specialist

    const timezoneAlert = `
      <div class="alert bg-white-light text-primary-dark" role="alert">
          <i class="mdi mdi-clock-outline me-1"></i>
          ${translations.timezone_clarification_patient.replace(
            '{userTimezone}',
            `<strong>${userTimezone}</strong>`
          )}
      </div>`

    let availabilityHtml = `
      <h5 class="mb-2">${
        translations.weekly_availability_title || 'Horario Semanal'
      }</h5>
    `
    if (specialist.availability && specialist.availability.length > 0) {
      const dayOrder = [
        translations.weekday_MONDAY,
        translations.weekday_TUESDAY,
        translations.weekday_WEDNESDAY,
        translations.weekday_THURSDAY,
        translations.weekday_FRIDAY,
        translations.weekday_SATURDAY,
        translations.weekday_SUNDAY,
      ]

      specialist.availability.sort(
        (a, b) => dayOrder.indexOf(a.weekday) - dayOrder.indexOf(b.weekday)
      )

      specialist.availability.forEach((slot) => {
        const dayKey = `weekday_${slot.weekday.toUpperCase()}`
        const dayName = translations[dayKey] || slot.weekday

        const originalStartTime = slot.start_time.substring(0, 5)
        const originalEndTime = slot.end_time.substring(0, 5)

        const specialistTz = slot.timezone || specialist.timezone

        // ¡Aquí pasamos el dateString!
        const convertedStart = convertTimeForUserDisplay(
          dateString, // <-- Parámetro nuevo
          slot.start_time,
          specialistTz,
          userTimezone
        )
        const convertedEnd = convertTimeForUserDisplay(
          dateString, // <-- Parámetro nuevo
          slot.end_time,
          specialistTz,
          userTimezone
        )
        const originalTzAbbr = convertedStart.originalTzAbbr

        availabilityHtml += `
        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
          <span>${dayName}</span>
          <div class="text-end">
              <div class="mb-1">
                  <span class="badge bg-secondary-lighten text-secondary fw-normal">${originalStartTime} - ${originalEndTime} (${translations.specialist_time_label})</span>
              </div>
              <div>
                  <span class="badge bg-info-lighten text-info">${convertedStart.time} - ${convertedEnd.time} (${translations.your_time_label})</span>
              </div>
          </div>
        </li>
      `
      })
      availabilityHtml += '</ul>'
    } else {
      availabilityHtml += `<p class="text-muted">${
        translations.no_availability_text || 'No hay horario disponible.'
      }</p>`
    }

    const legendLabels = {
      available: translations.available || 'Disponible',
      selected: translations.your_appointment || 'Tu Cita',
      blocked: translations.not_available || 'No Disponible',
    }

    let legendHtml = `<h5 class="mb-2 mt-3">${
      translations.legend_title || 'Leyenda'
    }</h5>`

    for (const key in EVENT_COLORS) {
      legendHtml += `
        <div class="d-flex align-items-center mb-1">
          <span class="badge me-2" style="background-color: ${EVENT_COLORS[key].backgroundColor}; color: ${EVENT_COLORS[key].textColor};">&nbsp;&nbsp;</span>
          <span>${legendLabels[key]}</span>
        </div>
      `
    }

    container.innerHTML = timezoneAlert + availabilityHtml + legendHtml
  }

  const initFullCalendar = () => {
    const calendarEl = document.getElementById('calendar')
    if (!calendarEl) return
    if (calendar) calendar.destroy()

    const specialist = currentWizardData.specialist
    const duration = '00:20:00'
    const pricingId = currentWizardData.appointment.pricing_id

    let buttonText = {
      today: translations.calendar_button_today || 'Today',
      month: translations.calendar_button_month || 'Month',
      week: translations.calendar_button_week || 'Week',
      day: translations.calendar_button_day || 'Day',
    }

    calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'timeGridWeek',
      locale: currentLang.toLowerCase(),
      slotDuration: duration,
      height: 650,
      slotLabelFormat: {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
      },
      // botones de calendario traducciones
      buttonText,
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay',
      },
      timeZone: userTimezone,
      datesSet: function (dateInfo) {
        console.log(
          'Date range changed:',
          dateInfo.startStr,
          'to',
          dateInfo.endStr
        )
        console.log(dateInfo.start)

        renderScheduleLegends(dateInfo.start)
      },
      events: function (fetchInfo, successCallback, failureCallback) {
        const start = fetchInfo.startStr.split('T')[0]
        const end = fetchInfo.endStr.split('T')[0]

        const apiUrl = `${CALENDAR_DATA_API_URL}?specialist_id=${specialist.specialist_id}&pricing_id=${pricingId}&start=${start}&end=${end}`

        fetch(apiUrl)
          .then((response) => response.json())
          .then((result) => {
            if (!result.value) throw new Error(result.message)

            const data = result.data
            console.log(data)

            const clickableSlots = (data.clickableSlots || []).map((slot) => ({
              ...slot,
              title: translations.available || 'Available',
              ...EVENT_COLORS.available,
              extendedProps: { type: 'available_slot' },
            }))

            const busySlots = (data.busySlots || []).map((slot) => ({
              ...slot,
              title: translations.not_available || 'Not Available',

              ...EVENT_COLORS.blocked,
            }))
            successCallback([...clickableSlots, ...busySlots])

            // =================== FIN DE LA CORRECCIÓN ====================
          })
          .catch((error) => {
            Swal.fire({
              title: translations.error_loading_calendar_title,
              text: error.message || translations.error_loading_calendar_text,
              icon: 'error',
              confirmButtonText: translations.confirm_button_text,
            })

            console.error('Error fetching calendar data:', error)
            failureCallback(error)
          })
      },
      // =================== FIN DE LA CORRECCIÓN DE VISUALIZACIÓN =====================
      allDaySlot: false,
      eventClick: function (info) {
        if (info.event.extendedProps.type === 'selected_appointment') {
          return
        }
        if (info.event.extendedProps.type !== 'available_slot') {
          return
        }

        const selectedDate = info.event.start

        console.log(selectedDate)
        console.log(selectedDate.toISOString())
        console.log(selectedDate.toUTCString())

        // formatear a la zona horaria del usuario

        const formattedDateTime = new Intl.DateTimeFormat('en-US', {
          // Usando el locale de Venezuela
          year: 'numeric',
          month: '2-digit',
          day: '2-digit',
          hour: 'numeric',
          minute: '2-digit',
          hour12: true, // Usamos formato de 24h para mayor claridad
          timeZone: 'UTC', // <-- ¡Esta es la clave!
        }).format(selectedDate)

        console.log('Formatted for user timezone:', formattedDateTime)

        Swal.fire({
          title: translations.appointment_confirmation_title,
          html: `¿${translations.want_to_book_appointment} <br><b>${formattedDateTime}</b>?`,
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: translations.confirm_button_text,
          cancelButtonText: translations.cancel_button_text,
        }).then((result) => {
          if (result.isConfirmed) {
            const allEvents = calendar.getEvents()
            const previouslySelected = allEvents.find(
              (event) => event.extendedProps.type === 'selected_appointment'
            )

            if (previouslySelected) {
              previouslySelected.setProp('title', translations.available)
              previouslySelected.setProp(
                'backgroundColor',
                EVENT_COLORS.available.backgroundColor
              )
              previouslySelected.setProp(
                'borderColor',
                EVENT_COLORS.available.borderColor
              )
              previouslySelected.setProp(
                'textColor',
                EVENT_COLORS.available.textColor
              )
              previouslySelected.setExtendedProp('type', 'available_slot')
            }

            info.event.setProp('title', translations.your_appointment)
            info.event.setProp(
              'backgroundColor',
              EVENT_COLORS.selected.backgroundColor
            )
            info.event.setProp('borderColor', EVENT_COLORS.selected.borderColor)
            info.event.setProp('textColor', EVENT_COLORS.selected.textColor)
            info.event.setExtendedProp('type', 'selected_appointment')

            currentWizardData.appointment.datetime = info.event.start

            document.getElementById('selected-time-text').innerText =
              formattedDateTime

            Swal.fire(
              translations.appointment_confirmed_title,
              translations.appointment_confirmed_text,
              'success'
            )
          }
        })
      },
      eventDidMount: function (info) {
        if (info.event.extendedProps.type === 'available_slot') {
          info.el.style.cursor = 'pointer'
        }
      },
    })
    calendar.render()
  }

  const populateConfirmationStep = (modalEl, isBooking) => {
    const summaryContainer = modalEl.querySelector('.appointment-summary')
    const specialist = currentWizardData.specialist
    const appointment = currentWizardData.appointment

    let sharingSummary = translations.sharing_summary_none // MODIFICADO: Texto estático
    const sharingOptionRadioName = isBooking
      ? 'sharingOptionsBooking'
      : 'sharingOptionsEval'
    const sharingOption = (
      modalEl.querySelector(
        `input[name="${sharingOptionRadioName}"]:checked`
      ) || {}
    ).value

    if (sharingOption === 'share_all')
      sharingSummary = translations.sharing_summary_all // MODIFICADO: Texto estático
    if (sharingOption === 'share_custom')
      sharingSummary = translations.sharing_summary_custom // MODIFICADO: Texto estático

    currentWizardData.appointment.reason =
      (modalEl.querySelector('.reason-textarea') || {}).value || ''

    console.log(currentWizardData)

    const typeHTML = appointment.type
      ? `<strong>${appointment.type}</strong>`
      : `<strong>${translations.not_selected_text}</strong> <a href="#" class="wizard-edit-link text-accent-alt" data-step-to="1">${translations.edit_link}</a>` // MODIFICADO: Texto estático

    const formattedDateTime = new Intl.DateTimeFormat('en-US', {
      // Usando el locale de Venezuela
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: 'numeric',
      minute: '2-digit',
      hour12: true, // Usamos formato de 24h para mayor claridad
      timeZone: 'UTC', // <-- ¡Esta es la clave!
    }).format(appointment.datetime)

    let dateHTML = ''
    if (isBooking) {
      dateHTML = appointment.datetime
        ? `<strong>${formattedDateTime}</strong>`
        : `<strong>${translations.not_selected_text}</strong> <a href="#" class="wizard-edit-link text-accent-alt" data-step-to="3">${translations.edit_link}</a>` // MODIFICADO: Texto estático
    }

    summaryContainer.innerHTML = `
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center"> ${
                  translations.summary_specialist
                } <strong>${specialist.first_name} ${
      specialist.last_name
    }</strong></li>
                <li class="list-group-item d-flex justify-content-between align-items-center"> ${
                  translations.summary_type
                } ${typeHTML}</li>
                ${
                  isBooking
                    ? `<li class="list-group-item d-flex justify-content-between align-items-center"> ${translations.summary_datetime} ${dateHTML}</li>`
                    : ''
                }
                <li class="list-group-item d-flex justify-content-between align-items-center"> ${
                  translations.summary_sharing
                } <strong>${sharingSummary}</strong></li>
                <li class="list-group-item d-flex justify-content-between align-items-center"> ${
                  translations.summary_cost
                } <strong>$${appointment.cost || '0.00'}</strong></li>
            </ul>` // MODIFICADO: Se usan las nuevas claves para las etiquetas del resumen
  }

  const setupGlobalDateFilter = (modalEl) => {
    const isBooking = modalEl.id.includes('booking')
    const filterId = isBooking
      ? 'global-date-filter-booking'
      : 'global-date-filter-eval'
    const clearBtnId = isBooking
      ? 'clear-date-filter-booking'
      : 'clear-date-filter-eval'

    const datePickerInput = modalEl.querySelector(`#${filterId}`)
    const clearButton = modalEl.querySelector(`#${clearBtnId}`)

    if (!datePickerInput) return

    const applyFilterToAllOpenTables = (dateRange) => {
      const openAccordions = modalEl.querySelectorAll(
        '.panels-accordion .accordion-collapse.show'
      )

      openAccordions.forEach((accordionBody) => {
        const panelId = accordionBody.closest('.accordion-item').dataset.panelId
        const tableEl = $(`#table-${panelId}`)
        if (tableEl.length === 0) return

        const panelInfo = currentWizardData.panels.find(
          (p) => p.panel.panel_id === panelId
        )
        if (!panelInfo) return

        const dateKey = Object.keys(panelInfo.user_records[0] || {}).find((k) =>
          k.endsWith('_date')
        )

        const timeKey = Object.keys(panelInfo.user_records[0] || {}).find((k) =>
          k.endsWith('_time')
        )

        const filteredRecords = panelInfo.user_records.filter((record) => {
          if (dateRange.length < 2 || !dateKey || !record[dateKey]) return true
          const [start, end] = dateRange

          const dateParts = record[dateKey].split('-')
          const examDate = new Date(
            dateParts[0],
            dateParts[1] - 1,
            dateParts[2]
          )

          const inclusiveEnd = new Date(end)
          inclusiveEnd.setHours(23, 59, 59, 999)

          return examDate >= start && examDate <= inclusiveEnd
        })

        const formatDate = (isoDate) => {
          if (!isoDate || isoDate.split('-').length !== 3) return isoDate
          const [year, month, day] = isoDate.split('-')
          return `${month}/${day}/${year}`
        }

        const tableData = filteredRecords.map((record) => ({
          ...record,
          date: dateKey ? formatDate(record[dateKey]) : 'N/A',
          time: timeKey ? record[timeKey].substring(0, 5) : 'N/A',
        }))

        tableEl.bootstrapTable('load', tableData)
      })
    }

    const fp = flatpickr(datePickerInput, {
      locale: currentLang.toLowerCase(),
      mode: 'range',
      dateFormat: 'm/d/Y',
      onClose: (selectedDates) => {
        currentDateRange = selectedDates
        if (selectedDates.length === 2) {
          applyFilterToAllOpenTables(selectedDates)
          clearButton.style.display = 'inline-block'
        }
      },
    })

    clearButton.addEventListener('click', () => {
      fp.clear()
      currentDateRange = []
      applyFilterToAllOpenTables([])
      clearButton.style.display = 'none'
    })
  }

  const handleFinishRequest = async (isBooking) => {
    const modalEl = isBooking ? bookingWizardModalEl : evaluationWizardModalEl
    const sharingOptionRadioName = isBooking
      ? 'sharingOptionsBooking'
      : 'sharingOptionsEval'
    const scopeRequest = modalEl.querySelector(
      `input[name="${sharingOptionRadioName}"]:checked`
    ).value

    const formatDateTime = (date) => {
      if (!date || !(date instanceof Date)) return null
      const pad = (num) => num.toString().padStart(2, '0')
      return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(
        date.getDate()
      )} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(
        date.getSeconds()
      )}`
    }

    const appointmentDate = isBooking
      ? currentWizardData.appointment.datetime
      : null

    // si no ha elegido un servicio y tampoco elegido una fecha (en caso de ser booking), entonces mostrar una alerta de lo que falta
    if (
      !currentWizardData.appointment.pricing_id ||
      (isBooking && !appointmentDate)
    ) {
      let missingItems = []
      if (!currentWizardData.appointment.pricing_id)
        missingItems.push(translations.missing_service_type || 'service type')
      if (isBooking && !appointmentDate)
        missingItems.push(translations.missing_appointment_date || 'date')

      Swal.fire({
        title: translations.incomplete_request_title || 'Incomplete Request',
        text: translations.incomplete_request_text
          .replace('{items}', missingItems.join(' and '))
          .replace(
            '{itemsCount}',
            missingItems.length > 1 ? 'these items' : 'this item'
          ),
        icon: 'warning',
        confirmButtonText: translations.confirm_button_text,
      })
      return
    }

    const payload = {
      specialist_id: currentWizardData.specialist.specialist_id,
      type_request: isBooking ? 'appointment_request' : 'document_review',
      pricing_id: currentWizardData.appointment.pricing_id,
      status: 'PENDING',
      scope_request: scopeRequest,
      cost_request: currentWizardData.appointment.cost,
      notes: (modalEl.querySelector('.reason-textarea') || {}).value || '',
      request_date_to: appointmentDate ? appointmentDate : null,
    }

    if (scopeRequest === 'share_custom') {
      const dataPayload = []
      const panelNodes = modalEl.querySelectorAll(
        '.panels-accordion .accordion-item'
      )

      panelNodes.forEach((panelNode) => {
        const panelId = panelNode.dataset.panelId
        const biomarkers_selected = Array.from(
          panelNode.querySelectorAll(`.biomarker-checkbox:checked`)
        ).map((cb) => cb.value)
        const tableEl = $(`#table-${panelId}`)
        const selectAllCheckbox = modalEl.querySelector(
          `#select-all-${panelId}`
        )
        let exams = []

        if (tableEl.length > 0) {
          if (selectAllCheckbox && selectAllCheckbox.checked) {
            exams = null
          } else {
            exams = tableEl
              .bootstrapTable('getSelections')
              .map((row) => row.record_id)
          }
        }

        if (
          biomarkers_selected.length > 0 ||
          exams === null ||
          (Array.isArray(exams) && exams.length > 0)
        ) {
          dataPayload.push({
            panel_id: panelId,
            biomarkers_selected: biomarkers_selected,
            exams: exams,
          })
        }
      })
      payload.data = dataPayload
    }

    try {
      const response = await fetch(CREATE_REQUEST_API_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify(payload),
      })
      const result = await response.json()
      if (result.value) {
        Swal.fire({
          title: translations.request_success_title || 'Request Sent!',
          text:
            translations.request_success_text ||
            'Your request has been successfully sent.',
          icon: 'success',
        })
        if (isBooking) bookingWizardModal.hide()
        else evaluationWizardModal.hide()
      } else {
        throw new Error(result.message || 'Failed to create the request.')
      }
    } catch (error) {
      console.error('Error creating request:', error)
      Swal.fire({
        title: translations.request_error_title || 'Error',
        text: error.message,
        icon: 'error',
      })
    }
  }
  //======================================================================
  // EVENT LISTENERS
  //======================================================================

  document.body.addEventListener('change', function (e) {
    if (
      e.target.matches(
        'input[name="sharingOptionsBooking"], input[name="sharingOptionsEval"]'
      )
    ) {
      const modal = e.target.closest('.modal')
      if (!modal) return

      const customInterface = modal.querySelector('.custom-sharing-interface')
      if (!customInterface) return

      if (e.target.value === 'share_custom') {
        customInterface.style.display = 'block'
        setTimeout(() => customInterface.classList.add('show'), 10)
      } else {
        customInterface.classList.remove('show')
        setTimeout(() => {
          if (!customInterface.classList.contains('show')) {
            customInterface.style.display = 'none'
          }
        }, 150)
      }
    }
  })

  document.body.addEventListener('click', function (e) {
    const actionButton = e.target.closest('[data-action]')
    if (actionButton) {
      e.preventDefault()
      const action = actionButton.dataset.action
      const specialistId = actionButton.dataset.specialistId
      switch (action) {
        case 'view-profile':
          fetchAndShowSpecialistDetails(specialistId)
          break
        case 'book-appointment':
          openBookingWizard(specialistId)
          break
        case 'request-evaluation':
          openEvaluationWizard(specialistId)
          break
      }
    }

    const certBtn = e.target.closest('[data-cert-url]')

    if (certBtn) {
      const url = certBtn.dataset.certUrl
      const title = certBtn.dataset.certTitle
      const isPdf = url.toLowerCase().endsWith('.pdf')
      certificateModalLabel.textContent = title
      certificateIframe.style.display = isPdf ? 'block' : 'none'
      certificateImage.style.display = isPdf ? 'none' : 'block'
      if (isPdf) certificateIframe.src = url
      else certificateImage.src = url
      certificateModal.show()
    }

    let viewExamBtn = e.target.closest('.view-exam-btn')
    if (viewExamBtn) {
      e.preventDefault()

      const recordId = viewExamBtn.dataset.recordId
      const panelId = viewExamBtn.dataset.panelId
      const activeModalEl = e.target.closest('.modal')

      // Obtiene los IDs de los biomarcadores seleccionados en la UI
      const selectedBiomarkerNodes = activeModalEl.querySelectorAll(
        `.biomarker-checkbox[data-panel-id="${panelId}"]:checked`
      )
      const selectedBiomarkerIds = Array.from(selectedBiomarkerNodes).map(
        (node) => node.value
      )

      // Valida que al menos un biomarcador esté seleccionado
      if (selectedBiomarkerIds.length === 0) {
        Swal.fire({
          title: translations.no_biomarkers_selected_title,
          text: translations.no_biomarkers_selected_text,
          icon: 'warning',
          confirmButtonText: translations.confirm_button_text,
        })
        return
      }

      // Oculta el modal actual (el wizard) antes de mostrar el de detalles
      if (activeModalEl) {
        lastActiveWizardModal = bootstrap.Modal.getInstance(activeModalEl)
        if (lastActiveWizardModal) {
          lastActiveWizardModal.hide()
        }
      }

      // Encuentra los datos del examen y el título del panel
      const panelInfo = currentWizardData.panels.find(
        (p) => p.panel.panel_id === panelId
      )
      const recordData = panelInfo.user_records.find(
        (r) => r.record_id === recordId
      )
      const panelNameKey =
        currentLang.toLowerCase() === 'es' ? 'display_name_es' : 'display_name'
      const panelTitle = panelInfo.panel[panelNameKey]
      // ¡Llama al componente con el nuevo callback 'onClose'!

      console.log({
        recordId,
        panelId,
        recordData,
        panelTitle,
        selectedBiomarkerIds,
      })
      examViewer.show({
        recordId,
        panelId,
        recordData,
        panelTitle,
        selectedBiomarkerIds,
        onClose: () => {
          if (lastActiveWizardModal) {
            lastActiveWizardModal.show()

            // Opcional: Resetea la variable si ya no se necesita.
            lastActiveWizardModal = null
          }
        },
      })
    }

    const editLink = e.target.closest('.wizard-edit-link')
    if (editLink) {
      e.preventDefault()
      const step = parseInt(editLink.dataset.stepTo, 10)
      const wizardModal = e.target.closest('.modal')
      if (wizardModal.id === 'booking-wizard-modal' && wizardInstance)
        wizardInstance.showStep(step)
      if (
        wizardModal.id === 'evaluation-wizard-modal' &&
        evaluationWizardInstance
      )
        evaluationWizardInstance.showStep(step)
    }
  })

  searchInput.addEventListener(
    'input',
    debounce(() => {
      currentQuery = searchInput.value.trim()
      fetchSpecialists(false)
    })
  )
  sortSelect.addEventListener('change', () => {
    currentOrder = sortSelect.value
    fetchSpecialists(false)
  })
  applyFiltersBtn.addEventListener('click', handleApplyFilters)
  resetFiltersBtn.addEventListener('click', handleResetFilters)

  window.addEventListener('scroll', () => {
    if (
      window.innerHeight + window.scrollY >= document.body.offsetHeight - 300 &&
      !isLoading &&
      hasMoreData
    ) {
      fetchSpecialists(true)
    }
  })

  bookingWizardModalEl
    .querySelector('.wizard-finish-btn')
    .addEventListener('click', () => handleFinishRequest(true))
  evaluationWizardModalEl
    .querySelector('.wizard-finish-btn')
    .addEventListener('click', () => handleFinishRequest(false))

  bookingWizardModalEl.addEventListener('hidden.bs.modal', function () {
    // Si la variable `lastActiveWizardModal` tiene un valor, significa que
    // estamos ocultando este modal para mostrar otro (el de detalle del examen).
    // En este caso, NO queremos resetear el formulario.
    if (lastActiveWizardModal) {
      return // Detiene la ejecución y mantiene el estado del wizard intacto.
    }

    // Si `lastActiveWizardModal` es null, significa que el usuario cerró el modal
    // de forma definitiva (con la 'X', el botón 'Cerrar' o la tecla Esc).
    // En este caso, SÍ queremos limpiar y resetear todo.

    // 1. Destruye la instancia de FullCalendar para liberar memoria y eliminar sus eventos internos.
    if (calendar) {
      calendar.destroy()
      calendar = null // Importante: Asigna null para que la validación `if (calendar)` funcione la próxima vez.
    }

    // 2. Resetea la variable "memoria" del pricing_id.
    lastRenderedPricingId = null

    // 3. Limpia los datos de la cita que se estaba creando.
    currentWizardData.appointment = {}

    // 4. Opcional pero recomendado: Asegúrate de que el wizard vuelva a la primera pestaña.
    const firstTabEl = bookingWizardModalEl.querySelector(
      '.nav-pills a[href="#booking-step-intro"]'
    )
    if (firstTabEl) {
      const firstTab = new bootstrap.Tab(firstTabEl)
      firstTab.show()
    }
  })

  const handleUrlSearchParameter = () => {
    // 1. Se obtienen los parámetros de la URL actual.
    const urlParams = new URLSearchParams(window.location.search)

    // 2. Se busca un parámetro llamado 'search'. urlParams.get() decodifica
    //    automáticamente el valor (ej. %20 se convierte en espacio).
    const searchQuery = urlParams.get('search')

    // 3. Si el parámetro 'search' existe y tiene contenido...
    if (searchQuery) {
      // 4. Se coloca el valor en el input de búsqueda.
      searchInput.value = searchQuery

      // 5. Se actualiza la variable de estado 'currentQuery' que usa fetchSpecialists.
      currentQuery = searchQuery
    }
  }

  // Initial Load
  handleUrlSearchParameter()
  loadSpecialties()
  fetchSpecialists()

  if (document.getElementById('date-picker')) {
    dateFilterInstance = flatpickr('#date-picker', {
      locale: currentLang.toLowerCase(), // Usa el idioma actual
      altInput: true, // Activa un campo visible alterno
      altFormat: 'm/d/Y', // Muestra MM/DD/YYYY al usuario
      dateFormat: 'Y-m-d', // Envia YYYY-MM-DD al backend
    })
  }

  // === AÑADE ESTAS LÍNEAS AQUÍ ===
  // Se inicializan los wizards UNA SOLA VEZ cuando la página carga.
  if (bookingWizardModalEl) {
    wizardInstance = initGenericWizard(bookingWizardModalEl, 5, true)
  }
  if (evaluationWizardModalEl) {
    evaluationWizardInstance = initGenericWizard(
      evaluationWizardModalEl,
      4,
      false
    )
  }
})
