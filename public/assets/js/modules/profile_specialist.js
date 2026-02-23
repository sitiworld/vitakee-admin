import { countrySelect } from '../components/countrySelect.js'
import { initTimezoneSelect } from '../components/timezoneSelect.js'
import {
  formatDateTime,
  getPlatformIcon,
} from '../helpers/validacionesEspeciales.js'

// --- Avatar Picker Logic ---
;(function () {
  const fileInput = document.getElementById('profile_image_input')
  const previewCropper = document.getElementById('preview_cropper')
  const cropBtns = document.getElementById('cropper-buttons')

  const avatarUrlInput = document.getElementById('avatar_url')
  const avatarPreview = document.getElementById('avatarPreview')
  const avatarGrid = document.getElementById('avatarGrid')
  const confirmAvatarBtn = document.getElementById('confirmAvatarBtn')

  const tabAvatarBtn = document.getElementById('tab-avatar')
  const tabUploadBtn = document.getElementById('tab-upload')

  tabUploadBtn?.addEventListener('shown.bs.tab', () => {
    avatarUrlInput.value = ''
    if (avatarPreview) avatarPreview.style.display = 'none'
  })

  tabAvatarBtn?.addEventListener('shown.bs.tab', () => {
    if (fileInput) fileInput.value = ''
    if (previewCropper) {
      previewCropper.src = ''
      previewCropper.style.display = 'none'
    }
    if (cropBtns) cropBtns.style.display = 'none'
  })

  let tempSelectedSrc = null

  if (avatarGrid) {
    avatarGrid.addEventListener('click', (e) => {
      const item = e.target.closest('.avatar-item')
      if (!item) return
      avatarGrid
        .querySelectorAll('.avatar-item')
        .forEach((el) => el.classList.remove('selected'))
      item.classList.add('selected')
      tempSelectedSrc = item.getAttribute('data-src')
      confirmAvatarBtn.disabled = !tempSelectedSrc
    })
    avatarGrid.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        const item = e.target.closest('.avatar-item')
        if (item) item.click()
      }
    })
  }

  confirmAvatarBtn?.addEventListener('click', () => {
    if (!tempSelectedSrc) return
    avatarUrlInput.value = tempSelectedSrc
    avatarPreview.src = tempSelectedSrc
    avatarPreview.style.display = 'inline-block'
    if (fileInput) fileInput.value = ''
    if (previewCropper) {
      previewCropper.src = ''
      previewCropper.style.display = 'none'
    }
    if (cropBtns) cropBtns.style.display = 'none'

    const pickerEl = document.getElementById('avatarPickerModal')
    const picker =
      bootstrap.Modal.getInstance(pickerEl) || new bootstrap.Modal(pickerEl)
    picker.hide()

    const parentEl = document.getElementById('editUserModal')
    const parent =
      bootstrap.Modal.getInstance(parentEl) ||
      new bootstrap.Modal(parentEl, { backdrop: 'static' })
    parent.show()

    if (tabAvatarBtn) {
      new bootstrap.Tab(tabAvatarBtn).show()
      setTimeout(() => {
        const chooseBtn = document.querySelector(
          '[data-bs-target="#avatarPickerModal"]',
        )
        if (chooseBtn) chooseBtn.focus()
      }, 150)
    }
  })
})()

// --- Main Profile Logic ---
let currentSpecialistData = null
let cropper
let birthdayPicker

// Declarations for form elements
let socialView,
  socialEdit,
  addSocialBtn,
  cancelSocialBtn,
  socialForm,
  socialFormTitle,
  socialLinksList
let certView,
  certEdit,
  addCertBtn,
  cancelCertBtn,
  certForm,
  certFormTitle,
  certList
let availView,
  availEdit,
  addAvailBtn,
  cancelAvailBtn,
  availForm,
  availFormTitle,
  availList
let pricingView,
  pricingEdit,
  addPricingBtn,
  cancelPricingBtn,
  pricingForm,
  pricingFormTitle,
  pricingList
let locationView,
  locationEdit,
  addLocationBtn,
  cancelLocationBtn,
  locationForm,
  locationFormTitle,
  locationList
let blockView,
  blockEdit,
  addBlockBtn,
  cancelBlockBtn,
  blockForm,
  blockFormTitle,
  blockList
let startTimePicker, endTimePicker
let blockStartTimePicker, blockEndTimePicker

function addEmailRow(data = {}) {
  const emailList = document.getElementById('email-list')
  const newIndex = `email_${emailList.children.length}_${Date.now()}`
  const isPrimary =
    data.is_primary == 1 ||
    (emailList.children.length === 0 && data.is_primary === undefined)
  const isActive = data.is_active == 1 || data.is_active === undefined

  const rowWrapper = document.createElement('div')
  rowWrapper.className = 'email-row-wrapper border rounded p-2 mb-2'

  rowWrapper.innerHTML = `
    <div class="mb-2">
        <input type="hidden" class="contact-email-id" value="${
          data.contact_email_id || ''
        }">
        <input
            type="email"
            class="form-control form-control-sm email-input"
            placeholder="example@email.com"
            id="email-input-${newIndex}"
            name="email_contact"
            value="${data.email || ''}"
            data-rules="noVacio|email"
            data-validate-duplicate-url="contact-emails/check-email?entity_type=specialist"
            data-message-no-vacio="${translations['validation_required']}"
            data-message-email="${translations['validation_email']}"
            data-message-duplicado="${
              translations['validation_email_duplicate']
            }"
            data-initial-value="${data.email || ''}"
            data-record-id-selector="#user_id">
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex gap-3 align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="email_is_primary" id="email_primary_${newIndex}" ${
                  isPrimary ? 'checked' : ''
                }>
                <label class="form-check-label small" for="email_primary_${newIndex}">${
                  translations['profile_specialist_primary']
                }</label>
            </div>
            <div class="d-flex align-items-center">
                <input type="checkbox" class="js-switch-small email-active-switch" ${
                  isActive ? 'checked' : ''
                }>
                <label class="small ms-1">${
                  translations['status_active']
                }</label>
            </div>
        </div>
        <button class="btn action-icon btn-sm btn-remove-row" type="button" title="${
          translations['profile_specialist_remove']
        }">
            <i class="mdi mdi-trash-can-outline"></i>
        </button>
    </div>
  `
  emailList.appendChild(rowWrapper)

  new Switchery(rowWrapper.querySelector('.js-switch-small'), {
    size: 'small',
    color: systemColors.switchColorOn,
    secondaryColor: systemColors.switchColorOff,
  })
}

function addTelephoneRow(data = {}) {
  const telephoneList = document.getElementById('telephone-list')
  const newIndex = `tel_${telephoneList.children.length}_${Date.now()}`
  const isPrimary =
    data.is_primary == 1 ||
    (telephoneList.children.length === 0 && data.is_primary === undefined)
  const isActive = data.is_active == 1 || data.is_active === undefined
  const telephoneInputId = `telephone-input-${newIndex}`

  const rowWrapper = document.createElement('div')
  rowWrapper.className = 'telephone-row-wrapper border rounded p-2 mb-2'
  rowWrapper.id = `tel-row-${newIndex}`

  rowWrapper.innerHTML = `
    <div class="row gx-2 mb-2">
        <div class="col-6" id="country-select-container-${newIndex}"></div>
        <div class="col-6">
            <input type="hidden" class="contact-phone-id" value="${
              data.contact_phone_id || ''
            }">
            <input type="tel" class="form-control form-control-sm telephone-input" id="${telephoneInputId}" name="telephone_contact" placeholder="555 123-4567">
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex gap-3 align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="telephone_is_primary" id="tel_primary_${newIndex}" ${
                  isPrimary ? 'checked' : ''
                }>
                <label class="form-check-label small" for="tel_primary_${newIndex}">${
                  translations['profile_specialist_primary']
                }</label>
            </div>
            <div class="d-flex align-items-center">
                <input type="checkbox" class="js-switch-small telephone-active-switch" ${
                  isActive ? 'checked' : ''
                }>
                <label class="small ms-1">${
                  translations['status_active']
                }</label>
            </div>
        </div>
        <button class="btn action-icon btn-sm btn-remove-row" type="button" title="${
          translations['profile_specialist_remove']
        }">
            <i class="mdi mdi-trash-can-outline"></i>
        </button>
    </div>
  `
  telephoneList.appendChild(rowWrapper)

  const telephoneInput = document.getElementById(telephoneInputId)
  telephoneInput.dataset.rules = 'noVacio|longitudMinima:8'
  telephoneInput.dataset.messageNoVacio = translations.validation_required
  telephoneInput.dataset.messageLongitudMinima =
    translations.validation_phone_min_length
  telephoneInput.dataset.errorContainer = `#${rowWrapper.id} .col-6:last-child`
  telephoneInput.dataset.validateDuplicateUrl =
    'contact-phones/check-telephone?entity_type=specialist'
  telephoneInput.dataset.messageDuplicado =
    translations.validation_duplicate_phone
  telephoneInput.dataset.recordIdSelector = '#customerId'
  telephoneInput.dataset.initialValue = data.phone_number || ''
  telephoneInput.dataset.validateMasked = 'true'

  new Switchery(rowWrapper.querySelector('.js-switch-small'), {
    size: 'small',
    color: systemColors.switchColorOn,
    secondaryColor: systemColors.switchColorOff,
  })

  countrySelect(
    telephoneInputId,
    `#country-select-container-${newIndex}`,
    data.phone_number || null,
    '#editUserModal .modal-body',
  )
}

const showBlockForm = (isEditing = false) => {
  blockView.style.display = 'none'
  blockEdit.style.display = 'block'
  blockFormTitle.textContent = isEditing
    ? translations['profile_specialist_edit_block']
    : translations['profile_specialist_add_block']

  const flatpickrConfig = {
    enableTime: true,
    dateFormat: 'Y-m-d H:i',
    altInput: true,
    altFormat: 'F j, Y h:i K',
    time_24hr: false,
    minDate: 'today',
  }
  blockStartTimePicker = flatpickr('#request_date_to', flatpickrConfig)
  blockEndTimePicker = flatpickr('#request_date_end', flatpickrConfig)
}

const hideBlockForm = () => {
  blockView.style.display = 'block'
  blockEdit.style.display = 'none'
  blockForm.reset()
  if (window.limpiarErroresDelFormulario) {
    limpiarErroresDelFormulario(blockForm)
  }
  document.getElementById('second_opinion_id').value = ''
  if (blockStartTimePicker) blockStartTimePicker.destroy()
  if (blockEndTimePicker) blockEndTimePicker.destroy()
}

const showSocialForm = (isEditing = false) => {
  socialView.style.display = 'none'
  socialEdit.style.display = 'block'
  socialFormTitle.textContent = isEditing
    ? translations['profile_specialist_edit_social_link']
    : translations['profile_specialist_add_social_link']
  $('#platform').select2({
    templateResult: formatPlatformOption,
    templateSelection: formatPlatformOption,
    minimumResultsForSearch: -1,
    dropdownParent: $('#social-links-edit'),
  })
}

const hideSocialForm = () => {
  socialView.style.display = 'block'
  socialEdit.style.display = 'none'
  socialForm.reset()
  if (window.limpiarErroresDelFormulario) {
    limpiarErroresDelFormulario(socialForm)
  }
  document.getElementById('social_link_id').value = ''
  $('#platform').select2('destroy')
}

const showCertForm = (isEditing = false) => {
  certView.style.display = 'none'
  certEdit.style.display = 'block'
  certFormTitle.textContent = isEditing
    ? translations['profile_specialist_edit_certification']
    : translations['profile_specialist_add_certification']
}

const hideCertForm = () => {
  certView.style.display = 'block'
  certEdit.style.display = 'none'
  certForm.reset()
  if (window.limpiarErroresDelFormulario) {
    limpiarErroresDelFormulario(certForm)
  }
  document.getElementById('certification_id').value = ''
  const previewContainer = document.getElementById('cert-preview-container')
  previewContainer.style.display = 'none'
  document.getElementById('cert-preview-image').src = ''
  document.getElementById('cert-preview-pdf').src = ''
}

const showAvailabilityForm = (isEditing = false) => {
  availView.style.display = 'none'
  availEdit.style.display = 'block'
  availFormTitle.textContent = isEditing
    ? translations['profile_specialist_edit_availability']
    : translations['profile_specialist_add_availability']

  // ✅ Nueva línea: asignar timezone automáticamente
  const tzInput = document.getElementById('availabilityTimezone')
  if (tzInput) {
    tzInput.value =
      (window.currentSpecialistData && window.currentSpecialistData.timezone) ||
      'UTC'
  }

  const flatpickrConfig = {
    enableTime: true,
    noCalendar: true,
    dateFormat: 'H:i',
    time_24hr: false,
  }
  startTimePicker = flatpickr('#start_time', flatpickrConfig)
  endTimePicker = flatpickr('#end_time', flatpickrConfig)
}

const hideAvailabilityForm = () => {
  availView.style.display = 'block'
  availEdit.style.display = 'none'
  availForm.reset()
  if (window.limpiarErroresDelFormulario) {
    limpiarErroresDelFormulario(availForm)
  }
  document.getElementById('availability_id').value = ''
  $('#availabilityTimezone').select2('destroy')
  if (startTimePicker) startTimePicker.destroy()
  if (endTimePicker) endTimePicker.destroy()
}

function handleBlockListClick(e) {
  const editBtn = e.target.closest('.edit-block-btn')
  const deleteBtn = e.target.closest('.delete-block-btn')

  if (editBtn) {
    const { id, start, end, notes, timezone: blockTimezone } = editBtn.dataset

    if (new Date(end) < new Date()) {
      Swal.fire({
        icon: 'info',
        title: translations['alert_cant_edit_title'] ?? "Can't Edit Event",
        text:
          translations['alert_cant_edit_past_event'] ??
          'This event has already passed and cannot be edited.',
      })
      return
    }

    const specialistTimezone = currentSpecialistData.timezone

    const proceedWithEdit = () => {
      showBlockForm(true)
      blockForm.second_opinion_id.value = id
      blockForm.notes.value = notes
      blockStartTimePicker.setDate(start, true)
      blockEndTimePicker.setDate(end, true)
    }

    // Comprobar si las zonas horarias son diferentes
    if (
      specialistTimezone &&
      blockTimezone &&
      specialistTimezone !== blockTimezone
    ) {
      const warningText = (translations['timezone_change_warning_text'] || '')
        .replace('{block_timezone}', blockTimezone)
        .replace('{specialist_timezone}', specialistTimezone)

      Swal.fire({
        title:
          translations['timezone_change_warning_title'] || 'Timezone Mismatch',
        html: warningText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: translations['proceed_anyway'] || 'Proceed',
        cancelButtonText: translations['cancel'] || 'Cancel',
      }).then((result) => {
        if (result.isConfirmed) {
          proceedWithEdit()
        }
      })
    } else {
      // Si las zonas horarias son iguales, proceder directamente
      proceedWithEdit()
    }
  }

  if (deleteBtn) {
    const { id } = deleteBtn.dataset
    Swal.fire({
      title: translations['delete_confirm_title_users'],
      text: translations['delete_confirm_text_users'],
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: translations['delete_confirm_btn_users'],
    }).then((result) => {
      if (result.isConfirmed) deleteBlock(id)
    })
  }
}
function handleBlockFormSubmit(e) {
  const { formData } = e.detail
  const blockId = formData.get('second_opinion_id')

  // 1. Obtener las fechas locales directamente del formulario.
  const startTimeStr = formData.get('request_date_to')
  const endTimeStr = formData.get('request_date_end')

  // 2. Crear objetos Date solo para la validación en el navegador.
  const localStartTime = new Date(startTimeStr)
  const localEndTime = new Date(endTimeStr)

  // 3. Mantener la validación básica para una mejor experiencia de usuario.
  if (localStartTime >= localEndTime) {
    Swal.fire({
      icon: 'error',
      title: translations['alert_time_validation_error'],
      text: translations['alert_end_time_after_start'],
    })
    return // Detiene el envío del formulario
  }

  // 4. NO convertir a UTC. Enviar la fecha/hora local tal cual.
  // El backend se encargará de la conversión usando el timezone del especialista.
  formData.set('type_request', 'block')

  // 5. Añadir la zona horaria del perfil del especialista al formulario.
  if (currentSpecialistData && currentSpecialistData.timezone) {
    formData.set('timezone', currentSpecialistData.timezone)
  } else {
    // Fallback por si acaso, aunque no debería ocurrir.
    formData.set('timezone', 'UTC')
  }

  // 6. Enviar los datos al backend.
  if (blockId) {
    formData.append('_method', 'PUT')
    sendFormData(formData, `second-opinion/blocks/${blockId}`, 'POST')
  } else {
    formData.append('specialist_id', pageData.userId)
    sendFormData(formData, 'second-opinion/blocks', 'POST')
  }

  hideBlockForm()
}
async function deleteBlock(id) {
  await sendFormData(null, `second-opinion/blocks/${id}`, 'DELETE')
}

const showPricingForm = (isEditing = false) => {
  pricingView.style.display = 'none'
  pricingEdit.style.display = 'block'
  pricingFormTitle.textContent = isEditing
    ? translations['profile_specialist_edit_service']
    : translations['profile_specialist_add_service']
}

const hidePricingForm = () => {
  pricingView.style.display = 'block'
  pricingEdit.style.display = 'none'
  pricingForm.reset()

  // Ocultar y limpiar el campo de duración
  const durationContainer = document.getElementById('duration-container')
  const durationInput = document.getElementById('duration_services')
  durationContainer.style.display = 'none'
  durationInput.removeAttribute('data-rules')
  durationInput.value = ''

  // Restore price input to its default state
  const priceInput = document.getElementById('price_usd')
  priceInput.disabled = false
  if (priceInput.dataset.originalRules) {
    priceInput.setAttribute('data-rules', priceInput.dataset.originalRules)
    delete priceInput.dataset.originalRules
  }

  if (window.limpiarErroresDelFormulario) {
    limpiarErroresDelFormulario(pricingForm)
  }
  document.getElementById('pricing_id').value = ''
}

function handleServiceTypeChange() {
  const serviceType = this.value
  const durationContainer = document.getElementById('duration-container')
  const durationInput = document.getElementById('duration_services')

  if (serviceType === 'CONSULTATION' || serviceType === 'FOLLOW_UP') {
    durationContainer.style.display = 'block'
    durationInput.setAttribute('data-rules', 'noVacio|esEnteroPositivo')
  } else {
    durationContainer.style.display = 'none'
    durationInput.removeAttribute('data-rules')
    durationInput.value = '' // Limpiar valor si se oculta
  }
}

const renderStars = (rating) => {
  if (rating === null || rating === undefined)
    return `<span class="text-muted text-icons fst-italic">${translations.no_reviews_text}</span>`
  let starsHtml = ''
  const fullStars = Math.floor(rating)
  const halfStar = rating % 1 >= 0.5 ? 1 : 0
  const emptyStars = 5 - fullStars - halfStar
  for (let i = 0; i < fullStars; i++)
    starsHtml += '<i class="mdi mdi-star"></i>'
  if (halfStar) starsHtml += '<i class="mdi mdi-star-half-full"></i>'
  for (let i = 0; i < emptyStars; i++)
    starsHtml += '<i class="mdi mdi-star-outline"></i>'
  return starsHtml
}

;(async function loadSessionSpecialistCard() {
  try {
    const resp = await fetch('specialist/cards/session', {
      headers: { Accept: 'application/json' },
    })
    if (!resp.ok) throw new Error(`HTTP ${resp.status}`)
    const json = await resp.json()
    if (!json?.value || !json?.data) return
    const d = json.data
    const nf = new Intl.NumberFormat()
    const labsEl = document.getElementById('profile-labs-count')
    const consEl = document.getElementById('profile-consultations-count')
    const ratingCountEl = document.getElementById('profile-rating-count')
    const ratingStarsEl = document.getElementById('profile-rating-stars')
    if (labsEl) labsEl.textContent = nf.format(d.lab_reports_evaluated ?? 0)
    if (consEl) consEl.textContent = nf.format(d.consultations_completed ?? 0)
    const avg = typeof d.avg_rating === 'number' ? d.avg_rating : null
    const ratingText = d.rating_text || (avg !== null ? `${avg}/5` : '0/5')
    if (ratingCountEl) ratingCountEl.textContent = ratingText
    if (ratingStarsEl) ratingStarsEl.innerHTML = renderStars(avg)
  } catch (err) {
    console.error('Error loading specialist card from session:', err)
    const ratingStarsEl = document.getElementById('profile-rating-stars')
    if (ratingStarsEl) ratingStarsEl.innerHTML = renderStars(null)
  }
})()

const showLocationForm = (isEditing = false) => {
  locationView.style.display = 'none'
  locationEdit.style.display = 'block'
  locationFormTitle.textContent = isEditing
    ? translations['profile_specialist_edit_location']
    : translations['profile_specialist_add_location']
  const config = {
    placeholder: translations['select_option'],
    width: '100%',
    dropdownParent: $('#locations-edit'),
  }
  $('#country_id').select2(config)
  $('#state_id').select2(config)
  $('#city_id').select2(config)
}

const hideLocationForm = () => {
  locationView.style.display = 'block'
  locationEdit.style.display = 'none'
  locationForm.reset()
  if (window.limpiarErroresDelFormulario) {
    limpiarErroresDelFormulario(locationForm)
  }
  document.getElementById('location_id').value = ''
  $('#country_id').select2('destroy')
  $('#state_id').select2('destroy')
  $('#city_id').select2('destroy')
}

document.addEventListener('DOMContentLoaded', function () {
  socialView = document.getElementById('social-links-view')
  socialEdit = document.getElementById('social-links-edit')
  addSocialBtn = document.getElementById('add-social-link-btn')
  cancelSocialBtn = document.getElementById('cancel-social-edit-btn')
  socialForm = document.getElementById('socialLinksForm')
  socialFormTitle = document.getElementById('social-form-title')
  socialLinksList = document.getElementById('social-links-list')

  certView = document.getElementById('certifications-view')
  certEdit = document.getElementById('certifications-edit')
  addCertBtn = document.getElementById('add-certification-btn')
  cancelCertBtn = document.getElementById('cancel-cert-edit-btn')
  certForm = document.getElementById('certificationForm')
  certFormTitle = document.getElementById('certification-form-title')
  certList = document.getElementById('certifications-list')

  availView = document.getElementById('availability-view')
  availEdit = document.getElementById('availability-edit')
  addAvailBtn = document.getElementById('add-availability-btn')
  cancelAvailBtn = document.getElementById('cancel-availability-edit-btn')
  availForm = document.getElementById('availabilityForm')
  availFormTitle = document.getElementById('availability-form-title')
  availList = document.getElementById('availability-list')

  pricingView = document.getElementById('pricing-view')
  pricingEdit = document.getElementById('pricing-edit')
  addPricingBtn = document.getElementById('add-pricing-btn')
  cancelPricingBtn = document.getElementById('cancel-pricing-edit-btn')
  pricingForm = document.getElementById('pricingForm')
  pricingFormTitle = document.getElementById('pricing-form-title')
  pricingList = document.getElementById('pricing-list')

  locationView = document.getElementById('locations-view')
  locationEdit = document.getElementById('locations-edit')
  addLocationBtn = document.getElementById('add-location-btn')
  cancelLocationBtn = document.getElementById('cancel-location-edit-btn')
  locationForm = document.getElementById('locationForm')
  locationFormTitle = document.getElementById('location-form-title')
  locationList = document.getElementById('locations-list')

  blockView = document.getElementById('blocks-view')
  blockEdit = document.getElementById('blocks-edit')
  addBlockBtn = document.getElementById('add-block-btn')
  cancelBlockBtn = document.getElementById('cancel-block-edit-btn')
  blockForm = document.getElementById('blockForm')
  blockFormTitle = document.getElementById('block-form-title')
  blockList = document.getElementById('blocks-list')

  if (pageData.userId) {
    loadProfile(pageData.userId)
  } else {
    console.error('User ID not found.')
  }

  document
    .getElementById('is_free_service')
    .addEventListener('change', function () {
      const priceInput = document.getElementById('price_usd')
      if (this.checked) {
        // Store original validation rules and remove them
        if (priceInput.hasAttribute('data-rules')) {
          priceInput.dataset.originalRules =
            priceInput.getAttribute('data-rules')
          priceInput.removeAttribute('data-rules')
        }
        priceInput.value = '0.00'
        priceInput.disabled = true
        // Clear any existing validation error on the input
        if (window.limpiarErroresDelInput) {
          limpiarErroresDelInput(priceInput)
        }
      } else {
        // Restore validation rules
        if (priceInput.dataset.originalRules) {
          priceInput.setAttribute(
            'data-rules',
            priceInput.dataset.originalRules,
          )
          delete priceInput.dataset.originalRules
        }
        priceInput.value = ''
        priceInput.disabled = false
      }
    })

  document.querySelector('.editUserBtn').addEventListener('click', async () => {
    if (currentSpecialistData) {
      populateEditForm(currentSpecialistData)
      const select2Config = {
        placeholder: translations['select_option'],
        width: '100%',
        dropdownParent: $('#editUserModal .modal-body'),
      }
      $('#title_id').select2(select2Config)
      $('#specialty_id').select2(select2Config)
      initTimezoneSelect('timezoneSelect', '#editUserModal .modal-body')
      document.getElementById('telephone').dataset.initialValue =
        currentSpecialistData.phone || ''
      await countrySelect(
        'telephone',
        '#editUserModal [data-phone-select]',
        currentSpecialistData.phone,
        '#editUserModal .modal-body',
      )
      birthdayPicker = flatpickr('#birthday', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'm/d/Y',
        allowInput: true,
        enableTime: false,
        monthSelectorType: 'dropdown',
      })
      $('#languages').select2({
        placeholder: translations['select_option'],
        width: '100%',
        dropdownParent: $('#editUserModal .modal-body'),
      })
      $('#timezoneSelect').val(currentSpecialistData.timezone).trigger('change')
      const modal = new bootstrap.Modal(
        document.getElementById('editUserModal'),
      )
      modal.show()
    } else {
      Swal.fire({
        icon: 'warning',
        title: translations['alert_please_wait'],
        text: translations['alert_profile_data_loading'],
      })
    }
  })

  addSocialBtn.addEventListener('click', () => showSocialForm(false))
  cancelSocialBtn.addEventListener('click', hideSocialForm)
  socialLinksList.addEventListener('click', handleSocialListClick)

  addCertBtn.addEventListener('click', () => showCertForm(false))
  cancelCertBtn.addEventListener('click', hideCertForm)
  certList.addEventListener('click', handleCertListClick)
  document
    .getElementById('cert_file')
    .addEventListener('change', handleCertFilePreview)

  addAvailBtn.addEventListener('click', () => showAvailabilityForm(false))
  cancelAvailBtn.addEventListener('click', hideAvailabilityForm)
  availList.addEventListener('click', handleAvailabilityListClick)

  addPricingBtn.addEventListener('click', () => showPricingForm(false))
  cancelPricingBtn.addEventListener('click', hidePricingForm)
  pricingList.addEventListener('click', handlePricingListClick)
  document
    .getElementById('service_type')
    .addEventListener('change', handleServiceTypeChange)

  addLocationBtn.addEventListener('click', () => {
    showLocationForm(false)
    loadCountries()
  })
  cancelLocationBtn.addEventListener('click', hideLocationForm)
  locationList.addEventListener('click', handleLocationListClick)

  addBlockBtn.addEventListener('click', () => showBlockForm(false))
  cancelBlockBtn.addEventListener('click', hideBlockForm)
  blockList.addEventListener('click', handleBlockListClick)

  const imageInput = document.getElementById('profile_image_input')
  const previewImage = document.getElementById('preview_cropper')
  const cropperButtons = document.getElementById('cropper-buttons')

  imageInput.addEventListener('change', (e) => {
    const files = e.target.files
    if (files && files.length > 0) {
      const reader = new FileReader()
      reader.onload = () => {
        previewImage.src = reader.result
        previewImage.style.display = 'block'
        cropperButtons.style.display = 'block'
        if (cropper) cropper.destroy()
        cropper = new Cropper(previewImage, {
          aspectRatio: 1,
          viewMode: 1,
          movable: false,
          zoomable: false,
        })
      }
      reader.readAsDataURL(files[0])
    }
  })

  document
    .getElementById('editUserModal')
    .addEventListener('hidden.bs.modal', function () {
      $('#title_id').select2('destroy')
      $('#specialty_id').select2('destroy')
      $('#languages').select2('destroy')
      if (cropper) {
        cropper.destroy()
        cropper = null
        previewImage.style.display = 'none'
        cropperButtons.style.display = 'none'
        imageInput.value = ''
      }
      if (birthdayPicker) {
        birthdayPicker.destroy()
        birthdayPicker = null
      }
    })

  document
    .getElementById('certificationViewerModal')
    .addEventListener('hidden.bs.modal', function () {
      document.getElementById('certViewerIframe').src = 'about:blank'
      document.getElementById('certViewerImage').src = ''
    })

  document
    .getElementById('flipHorizontal')
    .addEventListener('click', () =>
      cropper.scaleX(-cropper.getData().scaleX || -1),
    )
  document
    .getElementById('flipVertical')
    .addEventListener('click', () =>
      cropper.scaleY(-cropper.getData().scaleY || -1),
    )

  document
    .getElementById('editUserForm')
    .addEventListener('validation:success', handleFormSubmit)
  document
    .getElementById('socialLinksForm')
    .addEventListener('validation:success', handleSocialFormSubmit)
  document
    .getElementById('certificationForm')
    .addEventListener('validation:success', handleCertificationFormSubmit)
  document
    .getElementById('availabilityForm')
    .addEventListener('validation:success', handleAvailabilityFormSubmit)
  document
    .getElementById('pricingForm')
    .addEventListener('validation:success', handlePricingFormSubmit)
  document
    .getElementById('locationForm')
    .addEventListener('validation:success', handleLocationFormSubmit)

  document
    .getElementById('blockForm')
    .addEventListener('validation:success', handleBlockFormSubmit)

  $('#btn-add-email').on('click', () => addEmailRow())
  $('#btn-add-telephone').on('click', () => addTelephoneRow())

  $(document).on('click', '.btn-remove-row', function () {
    const rowWrapper = $(this).closest(
      '.telephone-row-wrapper, .email-row-wrapper',
    )
    const container = rowWrapper.parent()
    if (rowWrapper.hasClass('telephone-row-wrapper')) {
      const telInputId = rowWrapper.find('.telephone-input').attr('id')
      if (window.countrySelectMasks && window.countrySelectMasks[telInputId]) {
        window.countrySelectMasks[telInputId].destroy()
        delete window.countrySelectMasks[telInputId]
      }
    }
    const wasPrimary = rowWrapper.find('input[type="radio"]:checked').length > 0
    rowWrapper.remove()
    if (wasPrimary && container.children().length > 0) {
      container.find('input[type="radio"]').first().prop('checked', true)
    }
  })
})

function formatPlatformOption(state) {
  if (!state.id) return state.text
  return $(`<span>${getPlatformIcon(state.id)}  ${state.text}</span>`)
}

async function loadProfile(specialistId) {
  try {
    const response = await fetch(`specialist_get/${specialistId}`)
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`)
    const result = await response.json()

    if (result.value && result.data) {
      currentSpecialistData = result.data
      const specialist = result.data
      const user_image_path = pageData.userImage

      document.getElementById('profile-name').innerText =
        `${specialist.first_name} ${specialist.last_name}`
      document.getElementById('profile-role').innerText =
        specialist.title_display_name || translations['profile_specialist_role']

      let profileImage = 'public/assets/images/users/user_boy.svg'
      if (specialist.avatar_url !== null) {
        profileImage = specialist.avatar_url
      } else if (user_image_path) {
        profileImage = pageData.baseUrl + user_image_path
      }
      document.getElementById('profile-image').src = profileImage

      document.getElementById('profile-bio').innerText = specialist.bio || '-'

      if (specialist.email) {
        document.getElementById('profile-email').innerHTML =
          `<a href="mailto:${specialist.email}" class="text-body">${specialist.email}</a>`
      }
      if (specialist.phone) {
        const cleanPhone = String(specialist.phone).replace(/[\s()-]/g, '')
        document.getElementById('profile-telephone').innerHTML =
          `<a href="tel:${cleanPhone}" class="text-body">${specialist.phone}</a>`
      }

      const ageText = translations['profile_years']
      document.getElementById('profile-birthday').innerText =
        specialist.birthday
          ? `${formatDateTime(specialist.birthday)} (${
              specialist.age_years
            } ${ageText})`
          : '-'

      if (specialist.locations && specialist.locations.length > 0) {
        const primaryLocation =
          specialist.locations.find((loc) => loc.is_primary == 1) ||
          specialist.locations[0]
        document.getElementById('profile-location').innerText =
          `${primaryLocation.city_name}, ${primaryLocation.country_name}`
      } else {
        document.getElementById('profile-location').innerText = '-'
      }

      if (specialist.system_type) {
        const sys = String(specialist.system_type).toLowerCase()
        const el = document.querySelector(
          `input[name="card_height_system"][value="${sys}"]`,
        )
        if (el) {
          el.checked = true
        }
      }

      const websiteLinkContainer = document.getElementById(
        'profile-website-link',
      )
      const websiteText = translations['website_url']
      websiteLinkContainer.innerHTML = specialist.website_url
        ? `<a href="${specialist.website_url}" class="text-info" target="_blank"><i class="mdi mdi-web"></i> ${websiteText}</a>`
        : ''

      populateTabs(specialist)
      populateSocialLinks(specialist)
    } else {
      console.error('API Error:', result.message)
    }
  } catch (error) {
    console.error('Fetch Error:', error)
  }
}

function updateMeasurementSystem(specialistId, newSystem) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `specialist/system_type/update/${specialistId}`,
      type: 'POST',
      data: { system_type: newSystem, _method: 'PUT' },
      dataType: 'json',
      success: (response) =>
        response.value
          ? resolve(response)
          : reject(new Error(response.message)),
      error: (xhr) =>
        reject(new Error(xhr.responseJSON?.message || 'AJAX request failed.')),
    })
  })
}

$('.system-update-radio').on('change', function () {
  const newSystem = this.value
  const originalSystem = String(
    currentSpecialistData?.system_type || '',
  ).toLowerCase()
  const specialistId = currentSpecialistData?.specialist_id

  if (!specialistId) {
    $(this).prop('checked', false)
    Swal.fire({
      icon: 'error',
      title: translations['error'],
      text: translations['profile_not_loaded'],
    })
    return
  }

  Swal.fire({
    title: translations['confirm_update_title'],
    text: translations['confirm_update_measurent_system'],
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: translations['confirmYes'],
    cancelButtonText: translations['cancel'],
  }).then((result) => {
    if (result.isConfirmed) {
      updateMeasurementSystem(specialistId, newSystem)
        .then((response) => {
          if (currentSpecialistData)
            currentSpecialistData.system_type = newSystem.toUpperCase()
          Swal.fire({
            icon: 'success',
            title: translations['titleSuccess_profile_user'],
            text: response.message,
            timer: 2000,
            showConfirmButton: false,
          }).then(() => {
            loadProfile(specialistId)
          })
        })
        .catch((error) => {
          if (originalSystem) {
            $(
              `input[name="card_height_system"][value="${originalSystem}"]`,
            ).prop('checked', true)
          }
          Swal.fire({
            icon: 'error',
            title: translations['error'],
            text: error.message,
          })
        })
    } else {
      if (originalSystem) {
        $(`input[name="card_height_system"][value="${originalSystem}"]`).prop(
          'checked',
          true,
        )
      }
    }
  })
})

function normalizeServiceType(raw) {
  const norm = String(raw ?? '')
    .trim()
    .toUpperCase()
    .replace(/[\s\-]+/g, '_')
  const aliases = { FOLLOWUP: 'FOLLOW_UP' }
  return aliases[norm] || norm
}

function translateServiceType(type) {
  const key = 'service_type_' + normalizeServiceType(type)
  return translations[type]
}

function startCase(s) {
  return s.toLowerCase().replace(/\b\w/g, (c) => c.toUpperCase())
}

function translateWeekday(day) {
  const key =
    'weekday_' +
    String(day || '')
      .trim()
      .toUpperCase()
  return translations[key] || day || ''
}

function populateTabs(specialist) {
  const primaryLocation =
    specialist.locations?.find((l) => l.is_primary == 1) ||
    specialist.locations?.[0] ||
    null
  const primaryLocationText = primaryLocation
    ? `${primaryLocation.city_name}, ${primaryLocation.state_name}, ${primaryLocation.country_name}`
    : '-'

  let emailsHtml =
    specialist.emails
      ?.map(
        (email) => `
    <div class="d-flex justify-content-between align-items-center mb-1">
        <a href="mailto:${email.email}" class="text-body text-break">${
          email.email
        }</a>
        <div class="d-flex flex-nowrap">
            ${
              email.is_primary == 1
                ? `<span class="badge blue-item ms-1">${translations['profile_specialist_primary']}</span>`
                : ''
            }
            ${
              email.is_active == 1
                ? `<span class="badge green-item ms-1">${translations['status_active']}</span>`
                : `<span class="badge red ms-1">${translations['status_inactive']}</span>`
            }
        </div>
    </div>`,
      )
      .join('') || ''

  let phonesHtml =
    specialist.phones
      ?.map((phone) => {
        const cleanPhoneNumber = phone.phone_number.replace(/[\s()-]/g, '')
        return `
      <div class="d-flex justify-content-between align-items-center mb-1">
          <a href="tel:${cleanPhoneNumber}" class="text-body">${
            phone.phone_number
          }</a>
          <div class="d-flex flex-nowrap">
               ${
                 phone.is_primary == 1
                   ? `<span class="badge blue-item ms-1">${translations['profile_specialist_primary']}</span>`
                   : ''
               }
               ${
                 phone.is_active == 1
                   ? `<span class="badge green-item ms-1">${translations['status_active']}</span>`
                   : `<span class="badge red ms-1">${translations['status_inactive']}</span>`
               }
          </div>
      </div>`
      })
      .join('') || ''

  document.getElementById('v-pills-general').innerHTML = `
    <h4>${translations['profile_specialist_general_info_title']}</h4>
    <p><strong>${translations['full_name']}:</strong> ${
      specialist.title_display_name || ''
    } ${specialist.first_name} ${specialist.last_name}</p>
    <p><strong>${translations['specialty']}:</strong> ${
      specialist.specialty_display_name || '-'
    }</p>
    <p><strong>${
      translations['profile_specialist_primary_location_title']
    }:</strong> ${primaryLocationText}</p>
    <hr>
    <h6>${translations['profile_specialist_contact_info_title']}</h6>
    <p><strong>${translations['email']}:</strong> <a href="mailto:${
      specialist.email
    }" class="text-body">${specialist.email || '-'}</a></p>
    <p><strong>${
      translations['profile_specialist_phone_label']
    }:</strong> <a href="tel:${String(specialist.phone).replace(
      /[\s()-]/g,
      '',
    )}" class="text-body">${specialist.phone || '-'}</a></p>
    ${
      emailsHtml || phonesHtml
        ? `<hr><h6 class="mt-3">${translations['additional_contacts']}</h6>`
        : ''
    }
    <div class="row">
        <div class="col-md-6">${
          emailsHtml
            ? `<div class="mb-2"><strong>${translations['emails']}</strong>${emailsHtml}</div>`
            : ''
        }</div>
        <div class="col-md-6">${
          phonesHtml
            ? `<div><strong>${translations['telephones']}</strong>${phonesHtml}</div>`
            : ''
        }</div>
    </div>
  `

  let certificationsArray = Array.isArray(specialist.certifications)
    ? specialist.certifications
    : []
  if (certificationsArray.length > 0) {
    certList.innerHTML = certificationsArray
      .map((cert) => {
        const fileUrl = cert.file_url.startsWith('http')
          ? cert.file_url
          : pageData.baseUrl + cert.file_url
        const isImage = /\.(jpg|jpeg|png)$/i.test(fileUrl)
        const isPdf = /\.pdf$/i.test(fileUrl)
        let viewLink =
          isImage || isPdf
            ? `<a href="javascript:void(0);" class="view-cert-btn" data-file-url="${fileUrl}" data-is-image="${isImage}">${translations['profile_specialist_view_document']}</a>`
            : `<a href="${fileUrl}" target="_blank">${translations['profile_specialist_download_document']}</a>`
        return `<div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
              <div>
                  <strong>${cert.title}</strong><p class="mb-0 text-muted">${
                    cert.description || ''
                  }</p>${viewLink}
                  <span class="badge ${
                    (cert.visibility || 'PUBLIC') === 'PUBLIC'
                      ? 'green-item'
                      : 'yellow-item'
                  } ms-2">${
                    translations[cert.visibility.toLowerCase() || 'public']
                  }</span>
              </div>
              <div>
                  <button class="btn btn-sm action-icon edit-cert-btn" data-id="${
                    cert.certification_id
                  }" data-title="${cert.title}" data-description="${
                    cert.description || ''
                  }" data-visibility="${
                    cert.visibility || 'PUBLIC'
                  }"><i class="mdi mdi-pencil "></i></button>
                  <button class="btn btn-sm  action-icon delete-cert-btn" data-id="${
                    cert.certification_id
                  }"><i class="mdi mdi-delete"></i></button>
              </div>
          </div>`
      })
      .join('')
  } else {
    certList.innerHTML = `<p>${translations['profile_specialist_no_certifications']}</p>`
  }

  let locationsArray = Array.isArray(specialist.locations)
    ? specialist.locations
    : []
  if (locationsArray.length > 0) {
    locationList.innerHTML = locationsArray
      .map(
        (loc) => `
       <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
            <div>
                <strong>${loc.city_name}, ${loc.state_name}, ${
                  loc.country_name
                }</strong>
                ${
                  loc.is_primary == 1
                    ? `<span class="badge blue-item ms-2">${translations['profile_specialist_primary']}</span>`
                    : ''
                }
            </div>
            <div>
                <button class="btn btn-sm action-icon edit-location-btn" data-id="${
                  loc.location_id
                }" data-country_id="${loc.country_id}" data-state_id="${
                  loc.state_id
                }" data-city_id="${loc.city_id}" data-is_primary="${
                  loc.is_primary
                }"><i class="mdi mdi-pencil"></i></button>
                <button class="btn btn-sm action-icon delete-location-btn" data-id="${
                  loc.location_id
                }"><i class="mdi mdi-delete"></i></button>
            </div>
        </div>
      `,
      )
      .join('')
  } else {
    locationList.innerHTML = `<p>${translations['profile_specialist_no_locations']}</p>`
  }

  let availabilityArray = Array.isArray(specialist.availability)
    ? specialist.availability
    : []
  if (availabilityArray.length > 0) {
    availList.innerHTML = availabilityArray
      .map((avail) => {
        const formatTime12h = (time24) => {
          if (!time24) return ''
          const [hours, minutes] = time24.split(':')
          const d = new Date(1970, 0, 1, hours, minutes)
          return d.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true,
          })
        }
        return `
          <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
            <div>
              <strong>${translateWeekday(avail.weekday)}</strong>
              <p class="mb-0 text-muted">${formatTime12h(
                avail.start_time,
              )} - ${formatTime12h(avail.end_time)} (${avail.timezone})</p>
            </div>
            <div>
              <button class="btn btn-sm action-icon edit-avail-btn" data-id="${
                avail.availability_id
              }" data-weekday="${avail.weekday}" data-start="${
                avail.start_time
              }" data-end="${avail.end_time}" data-timezone="${
                avail.timezone
              }"><i class="mdi mdi-pencil"></i></button>
              <button class="btn btn-sm action-icon delete-avail-btn" data-id="${
                avail.availability_id
              }"><i class="mdi mdi-delete"></i></button>
            </div>
          </div>`
      })
      .join('')
  } else {
    availList.innerHTML = `<p>${translations['profile_specialist_no_availability']}</p>`
  }

  // --- AÑADE ESTE BLOQUE DE CÓDIGO AQUÍ ---
  const syncTimezoneSwitch = document.getElementById('sync-timezone-switch')
  if (syncTimezoneSwitch) {
    const specialistTimezone = specialist.timezone
    let areAllSynced = false // Por defecto, el switch estará apagado y habilitado

    // La verificación solo tiene sentido si hay una zona horaria principal Y al menos un horario de disponibilidad
    if (specialistTimezone && availabilityArray.length > 0) {
      // Usamos .every() para verificar si TODOS los horarios coinciden
      areAllSynced = availabilityArray.every(
        (avail) => avail.timezone === specialistTimezone,
      )
    }
    // Establece el estado del switch (marcado/desmarcado)
    syncTimezoneSwitch.checked = areAllSynced
    // Establece el estado del switch (habilitado/deshabilitado)
    syncTimezoneSwitch.disabled = areAllSynced
  }
  let blocksArray = Array.isArray(specialist.blocks) ? specialist.blocks : []
  if (blocksArray.length > 0) {
    blockList.innerHTML = blocksArray
      .map((block) => {
        const endDate = new Date(block.request_date_end)
        const isPast = endDate < new Date()
        const formattedStart = formatDateTime(block.request_date_to, {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
          hour: 'numeric',
          minute: '2-digit',
        })
        const formattedEnd = formatDateTime(block.request_date_end, {
          hour: 'numeric',
          minute: '2-digit',
        })
        const notesHtml = block.notes
          ? `<p class="mb-0 text-muted fst-italic">"${block.notes}"</p>`
          : ''

        // --- INICIO: Lógica para la advertencia de zona horaria ---
        let timezoneWarningHtml = ''
        const specialistTimezone = specialist.timezone
        const blockTimezone = block.timezone

        if (
          specialistTimezone &&
          blockTimezone &&
          specialistTimezone !== blockTimezone
        ) {
          const translationKey = 'block_event_timezone_warning'
          // Texto por defecto en inglés si la traducción no existe
          let warningText =
            translations.block_event_timezone_warning ||
            'Note: This event was created in a different timezone ({timezone})'
          warningText = warningText.replace(
            '{timezone}',
            `<strong>${blockTimezone}</strong>`,
          )

          timezoneWarningHtml = `
                <div class="d-flex align-items-center text-info small mt-1">
                    <i class="mdi mdi-alert-outline me-1"></i>
                    <span>${warningText}</span>
                </div>
            `
        }
        // --- FIN: Lógica para la advertencia de zona horaria ---

        return `
        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded ${
          isPast ? 'bg-light text-muted' : ''
        }">
          <div>
            <strong>${formattedStart} - ${formattedEnd}</strong>
            ${notesHtml}
            ${timezoneWarningHtml} 
            ${
              isPast
                ? `<span class="badge yellow-item mt-1">${
                    translations['past_event'] ?? 'Past'
                  }</span>`
                : ''
            }
          </div>
          <div>
            <button class="btn btn-sm action-icon edit-block-btn" 
              data-id="${block.second_opinion_id}" 
              data-start="${block.request_date_to}" 
              data-end="${block.request_date_end}"
              data-notes="${block.notes || ''}"
              data-timezone="${block.timezone || ''}" 
              ${isPast ? 'disabled' : ''}>
              <i class="mdi mdi-pencil"></i>
            </button>
            <button class="btn btn-sm action-icon delete-block-btn" data-id="${
              block.second_opinion_id
            }">
              <i class="mdi mdi-delete"></i>
            </button>
          </div>
        </div>`
      })
      .join('')
  } else {
    blockList.innerHTML = `<p>${
      translations['profile_specialist_no_blocks'] ??
      'No blocked events have been added.'
    }</p>`
  }
  let pricingArray = Array.isArray(specialist.pricing) ? specialist.pricing : []

  if (pricingArray.length > 0) {
    pricingList.innerHTML = pricingArray
      .map((price) => {
        const priceValue = Number(price.price_usd ?? 0)
        const isFree = priceValue === 0
        const durationText = price.duration_services
          ? `(${price.duration_services} ${
              translations['minutes_abbr'] || 'min'
            })`
          : ''

        return `
        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
          <div>
            <strong>${translateServiceType(
              price.service_type,
            )} ${durationText} - ${
              isFree ? '$0.00' : `$${priceValue.toFixed(2)}`
            }</strong>
            ${
              isFree
                ? `<span class="badge bg-success ms-2">${
                    translations['free_badge'] || 'Free'
                  }</span>`
                : ''
            }
            <p class="mb-0 text-muted">${price.description || ''}</p>
            <span class="badge ${
              price.is_active == 1 ? 'green-item' : 'red-item'
            }">${
              price.is_active == 1
                ? translations['status_active']
                : translations['status_inactive']
            }</span>
          </div>
          <div>
            <button class="btn btn-sm action-icon edit-pricing-btn" 
                data-id="${price.pricing_id}" 
                data-service_type="${price.service_type}" 
                data-price_usd="${price.price_usd}" 
                data-description="${(price.description || '').replace(
                  /"/g,
                  '&quot;',
                )}" 
                data-is_active="${price.is_active}"
                data-duration_services="${price.duration_services || ''}"
            ><i class="mdi mdi-pencil"></i></button>
            <button class="btn btn-sm action-icon delete-pricing-btn" data-id="${
              price.pricing_id
            }"><i class="mdi mdi-delete"></i></button>
          </div>
        </div>`
      })
      .join('')
  } else {
    pricingList.innerHTML = `<p>${translations['profile_specialist_no_services']}</p>`
  }

  document.getElementById('v-pills-about').innerHTML = `<h4>${
    translations['profile_specialist_tab_about']
  }</h4><p>${specialist.bio || translations['profile_specialist_no_bio']}</p>`
}

function populateSocialLinks(specialist) {
  let linksArray = Array.isArray(specialist.social_links)
    ? specialist.social_links
    : []

  document.getElementById('profile-social-links').innerHTML = linksArray
    .map(
      (link) =>
        `<a href="${link.url}" target="_blank" class="text-icons me-1" title="${
          link.platform
        }">
        ${getPlatformIcon(link.platform)}</a>`,
    )
    .join('')

  let socialTabListHtml = ''
  if (linksArray.length > 0) {
    socialTabListHtml = linksArray
      .map(
        (link) => `
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                <div>
                    ${getPlatformIcon(link.platform)}
                    <a href="${
                      link.url
                    }" target="_blank" style="text-transform: capitalize;">${
                      link.platform
                    }</a>
                </div>
                <div>
                    <button class="btn btn-sm action-icon edit-link-btn" data-id="${
                      link.social_link_id
                    }" data-platform="${link.platform}" data-url="${
                      link.url
                    }"><i class="mdi mdi-pencil"></i></button>
                    <button class="btn btn-sm action-icon delete-link-btn" data-id="${
                      link.social_link_id
                    }"><i class="mdi mdi-delete"></i></button>
                </div>
            </div>`,
      )
      .join('')
  } else {
    socialTabListHtml = `<p>${translations['profile_specialist_no_social_links']}</p>`
  }
  document.getElementById('social-links-list').innerHTML = socialTabListHtml
}

function populateEditForm(specialist) {
  const form = document.getElementById('editUserForm')
  for (const key in specialist) {
    if (form.elements[key]) {
      if (form.elements[key].type === 'checkbox') {
        form.elements[key].checked = specialist[key] == 1
      } else if (form.elements[key].type === 'radio') {
        const radio = form.querySelector(
          `[name="${key}"][value="${specialist[key].toLowerCase()}"]`,
        )
        if (radio) radio.checked = true
      } else {
        form.elements[key].value = specialist[key] || ''
      }
    }
  }
  form.user_id.value = specialist.specialist_id
  form.telephone.value = specialist.phone || ''

  $('#title_id').val(specialist.title_id).trigger('change')
  $('#specialty_id').val(specialist.specialty_id).trigger('change')

  $('#email-list').empty()
  $('#telephone-list').empty()
  if (Array.isArray(specialist.emails)) {
    specialist.emails.forEach((email) => addEmailRow(email))
  }
  if (Array.isArray(specialist.phones)) {
    specialist.phones.forEach((tel) => addTelephoneRow(tel))
  }

  if (specialist.system_type) {
    const sys = String(specialist.system_type).toLowerCase()
    const el = form.querySelector(`input[name="system_type"][value="${sys}"]`)
    if (el) {
      el.checked = true
    }
  }

  document.getElementById('email').dataset.initialValue = specialist.email || ''

  if (specialist.languages && Array.isArray(specialist.languages)) {
    $('#languages').val(specialist.languages).trigger('change')
  }
  form.password.value = ''
}

function handleSocialListClick(e) {
  const editBtn = e.target.closest('.edit-link-btn')
  const deleteBtn = e.target.closest('.delete-link-btn')
  if (editBtn) {
    showSocialForm(true)
    const { id, platform, url } = editBtn.dataset
    socialForm.social_link_id.value = id
    socialForm.url.value = url
    $(socialForm.platform).val(platform).trigger('change')
  }
  if (deleteBtn) {
    const { id } = deleteBtn.dataset
    Swal.fire({
      title: translations['delete_confirm_title_users'],
      text: translations['delete_confirm_text_users'],
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: translations['delete_confirm_btn_users'],
    }).then((result) => {
      if (result.isConfirmed) deleteSocialLink(id)
    })
  }
}

function handleCertListClick(e) {
  const editBtn = e.target.closest('.edit-cert-btn')
  const deleteBtn = e.target.closest('.delete-cert-btn')
  const viewBtn = e.target.closest('.view-cert-btn')
  if (editBtn) {
    showCertForm(true)
    const { id, title, description, visibility } = editBtn.dataset
    certForm.certification_id.value = id
    certForm.title.value = title
    certForm.description.value = description
    certForm.querySelector(
      `input[name="visibility"][value="${visibility}"]`,
    ).checked = true
    const cert = currentSpecialistData.certifications.find(
      (c) => c.certification_id == id,
    )
    if (cert && cert.file_url) {
      const fileUrl = cert.file_url.startsWith('http')
        ? cert.file_url
        : pageData.baseUrl + cert.file_url
      updateCertPreview(
        fileUrl,
        translations['profile_specialist_current_document_preview'],
      )
    }
  }
  if (deleteBtn) {
    const { id } = deleteBtn.dataset
    Swal.fire({
      title: translations['delete_confirm_title_users'],
      text: translations['delete_confirm_text_users'],
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: translations['delete_confirm_btn_users'],
    }).then((result) => {
      if (result.isConfirmed) deleteCertification(id)
    })
  }
  if (viewBtn) {
    const fileUrl = viewBtn.dataset.fileUrl
    const isImage = viewBtn.dataset.isImage === 'true'
    const modal = new bootstrap.Modal(
      document.getElementById('certificationViewerModal'),
    )
    const iframe = document.getElementById('certViewerIframe')
    const image = document.getElementById('certViewerImage')
    const modalTitle = document.getElementById('certificationViewerModalLabel')
    modalTitle.textContent =
      viewBtn.closest('.d-flex').querySelector('strong').textContent ||
      translations['profile_specialist_certification_document_title']
    if (isImage) {
      image.src = fileUrl
      image.style.display = 'block'
      iframe.style.display = 'none'
    } else {
      iframe.src = fileUrl
      iframe.style.display = 'block'
      image.style.display = 'none'
    }
    modal.show()
  }
}

function handleCertFilePreview(e) {
  const file = e.target.files[0]
  if (!file) return
  if (window.validarInput) {
    window.validarInput(e.target)
  }
  const objectUrl = URL.createObjectURL(file)
  updateCertPreview(
    objectUrl,
    translations['profile_specialist_new_document_preview'],
    file.type,
  )
}

function updateCertPreview(url, label, fileType = '') {
  const previewContainer = document.getElementById('cert-preview-container')
  const previewImage = document.getElementById('cert-preview-image')
  const previewPdf = document.getElementById('cert-preview-pdf')
  const previewLabel = document.getElementById('cert-preview-label')
  previewLabel.textContent = label
  const isImage =
    fileType.startsWith('image/') || /\.(jpg|jpeg|png)$/i.test(url)
  const isPdf = fileType === 'application/pdf' || /\.pdf$/i.test(url)
  if (isImage) {
    previewImage.src = url
    previewImage.style.display = 'block'
    previewPdf.style.display = 'none'
    previewContainer.style.display = 'block'
  } else if (isPdf) {
    previewPdf.src = url
    previewPdf.style.display = 'block'
    previewImage.style.display = 'none'
    previewContainer.style.display = 'block'
  } else {
    previewContainer.style.display = 'none'
  }
}

function handleAvailabilityListClick(e) {
  const editBtn = e.target.closest('.edit-avail-btn')
  const deleteBtn = e.target.closest('.delete-avail-btn')

  if (editBtn) {
    showAvailabilityForm(true)
    const { id, weekday, start, end, timezone, buffer } = editBtn.dataset
    availForm.availability_id.value = id
    availForm.weekday.value = weekday
    startTimePicker.setDate(start, true)
    endTimePicker.setDate(end, true)
    $(availForm.timezone).val(timezone).trigger('change')
    // Asignar el valor del tiempo entre citas
    availForm.buffer_time_minutes.value = buffer || '0'
  }

  if (deleteBtn) {
    const { id } = deleteBtn.dataset
    Swal.fire({
      title: translations['delete_confirm_title_users'],
      text: translations['delete_confirm_text_users'],
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: translations['delete_confirm_btn_users'],
    }).then((result) => {
      if (result.isConfirmed) deleteAvailability(id)
    })
  }
}

function handlePricingListClick(e) {
  const editBtn = e.target.closest('.edit-pricing-btn')
  const deleteBtn = e.target.closest('.delete-pricing-btn')
  if (editBtn) {
    showPricingForm(true)
    const {
      id,
      service_type,
      price_usd,
      description,
      is_active,
      duration_services,
    } = editBtn.dataset

    pricingForm.pricing_id.value = id
    // Disparar el evento change para mostrar/ocultar el campo de duración
    $(pricingForm.service_type).val(service_type).trigger('change')

    pricingForm.price_usd.value = Number(price_usd).toFixed(2)
    pricingForm.description.value = description
    pricingForm.is_active.checked = is_active == 1
    pricingForm.duration_services.value = duration_services || ''

    // --- CORRECCIÓN APLICADA AQUÍ ---
    // Se controla manualmente la visibilidad del contenedor de duración
    const durationContainer = document.getElementById('duration-container')
    const durationInput = document.getElementById('duration_services')
    if (service_type === 'CONSULTATION' || service_type === 'FOLLOW_UP') {
      durationContainer.style.display = 'block'
      durationInput.setAttribute('data-rules', 'noVacio|esEnteroPositivo')
    } else {
      durationContainer.style.display = 'none'
      durationInput.removeAttribute('data-rules')
    }

    // Handle free service checkbox logic
    const isFreeCheckbox = document.getElementById('is_free_service')
    const priceInput = document.getElementById('price_usd')
    const isFree = parseFloat(price_usd) === 0

    isFreeCheckbox.checked = isFree
    priceInput.disabled = isFree

    if (isFree) {
      if (priceInput.hasAttribute('data-rules')) {
        priceInput.dataset.originalRules = priceInput.getAttribute('data-rules')
        priceInput.removeAttribute('data-rules')
      }
    } else {
      if (priceInput.dataset.originalRules) {
        priceInput.setAttribute('data-rules', priceInput.dataset.originalRules)
        delete priceInput.dataset.originalRules
      }
    }
  }
  if (deleteBtn) {
    const { id } = deleteBtn.dataset
    Swal.fire({
      title: translations['delete_confirm_title_users'],
      text: translations['delete_confirm_text_users'],
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: translations['delete_confirm_btn_users'],
    }).then((result) => {
      if (result.isConfirmed) deletePricing(id)
    })
  }
}

function handleLocationListClick(e) {
  const editBtn = e.target.closest('.edit-location-btn')
  const deleteBtn = e.target.closest('.delete-location-btn')
  if (editBtn) {
    const { id, country_id, state_id, city_id, is_primary } = editBtn.dataset
    showLocationForm(true)
    locationForm.location_id.value = id
    locationForm.is_primary.checked = is_primary == 1
    loadCountries(country_id, state_id, city_id)
  }
  if (deleteBtn) {
    const { id } = deleteBtn.dataset
    Swal.fire({
      title: translations['delete_confirm_title_users'],
      text: translations['delete_confirm_text_users'],
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: translations['delete_confirm_btn_users'],
    }).then((result) => {
      if (result.isConfirmed) deleteLocation(id)
    })
  }
}

function handleFormSubmit(e) {
  const { formData } = e.detail
  const selectedLanguages = formData.getAll('languages[]')
  formData.delete('languages[]')
  formData.append('languages', JSON.stringify(selectedLanguages))
  const emails = []
  $('#email-list .email-row-wrapper').each(function () {
    const emailInput = $(this).find('.email-input').val()
    if (emailInput) {
      emails.push({
        contact_email_id: $(this).find('.contact-email-id').val() || null,
        email: emailInput,
        is_primary: $(this).find('input[type="radio"]').is(':checked') ? 1 : 0,
        is_active: $(this).find('.email-active-switch').is(':checked') ? 1 : 0,
      })
    }
  })
  const phones = []
  $('#telephone-list .telephone-row-wrapper').each(function () {
    const telInputId = $(this).find('.telephone-input').attr('id')
    const mask = window.countrySelectMasks?.[telInputId]
    let maskedValue =
      mask?.value || document.getElementById(telInputId)?.value || ''
    if (maskedValue.replace(/\D/g, '').length > 0) {
      phones.push({
        contact_phone_id: $(this).find('.contact-phone-id').val() || null,
        phone_number: maskedValue,
        is_primary: $(this).find('input[type="radio"]').is(':checked') ? 1 : 0,
        is_active: $(this).find('.telephone-active-switch').is(':checked')
          ? 1
          : 0,
      })
    }
  })
  formData.append('emails', JSON.stringify(emails))
  formData.append('phones', JSON.stringify(phones))
  if (cropper) {
    cropper
      .getCroppedCanvas({ width: 512, height: 512 })
      .toBlob(async (blob) => {
        formData.delete('profile_image')
        formData.append('profile_image', blob, 'profile.jpg')
        await sendFormData(
          formData,
          `specialist/update-profile/${pageData.userId}`,
          'POST',
        )
      }, 'image/jpeg')
  } else {
    formData.delete('profile_image')
    sendFormData(
      formData,
      `specialist/update-profile/${pageData.userId}`,
      'POST',
    )
  }
}

function handleSocialFormSubmit(e) {
  const { formData } = e.detail
  const socialLinkId = formData.get('social_link_id')
  if (socialLinkId) {
    formData.append('_method', 'PUT')
    sendFormData(formData, `specialist-social-links/${socialLinkId}`, 'POST')
  } else {
    formData.append('specialist_id', pageData.userId)
    sendFormData(formData, 'specialist-social-links', 'POST')
  }
  hideSocialForm()
}

function handleCertificationFormSubmit(e) {
  const { formData } = e.detail
  const certId = formData.get('certification_id')
  if (!formData.get('file').name && !certId) {
    Swal.fire({
      icon: 'error',
      title: translations['alert_file_required_title'],
      text: translations['alert_file_required_text'],
    })
    return
  }
  if (certId) {
    formData.append('_method', 'PUT')
    sendFormData(formData, `specialist-certifications/${certId}`, 'POST')
  } else {
    formData.append('specialist_id', pageData.userId)
    sendFormData(formData, 'specialist-certifications', 'POST')
  }
  hideCertForm()
}

function handleAvailabilityFormSubmit(e) {
  const { formData } = e.detail
  const availabilityId = formData.get('availability_id')
  const startTime = new Date(`1970-01-01T${formData.get('start_time')}:00`)
  const endTime = new Date(`1970-01-01T${formData.get('end_time')}:00`)
  if (startTime >= endTime) {
    Swal.fire({
      icon: 'error',
      title: translations['alert_time_validation_error'],
      text: translations['alert_end_time_after_start'],
    })
    return
  }
  if ((endTime.getTime() - startTime.getTime()) / 60000 < 60) {
    Swal.fire({
      icon: 'error',
      title: translations['alert_time_validation_error'],
      text: translations['alert_min_1_hour'],
    })
    return
  }
  if (availabilityId) {
    formData.append('_method', 'PUT')
    sendFormData(formData, `specialist-availability/${availabilityId}`, 'POST')
  } else {
    sendFormData(formData, 'specialist-availability', 'POST')
  }
  hideAvailabilityForm()
}

function handlePricingFormSubmit(e) {
  const { formData } = e.detail
  const pricingId = formData.get('pricing_id')
  const priceInput = document.getElementById('price_usd')
  const serviceType = formData.get('service_type')

  // Asegurarse de que el precio se envíe incluso si el input está deshabilitado
  formData.set('price_usd', priceInput.value || '0.00')

  if (!formData.has('is_active')) {
    formData.set('is_active', '0')
  }

  // Si el tipo de servicio es REVIEW, asegurarse de que duration_services esté vacío
  if (serviceType === 'REVIEW') {
    formData.set('duration_services', '')
  }

  if (pricingId) {
    formData.append('_method', 'PUT')
    sendFormData(formData, `specialist-pricing/${pricingId}`, 'POST')
  } else {
    formData.append('specialist_id', pageData.userId)
    sendFormData(formData, 'specialist-pricing', 'POST')
  }
  hidePricingForm()
}

async function handleSyncTimezone(e) {
  const switchEl = e.target

  // Solo actuar cuando el switch se activa
  if (!switchEl.checked) {
    return
  }

  // Desactivar visualmente el switch de inmediato.
  // El estado final se reflejará solo después de una operación exitosa.
  switchEl.checked = false

  if (!currentSpecialistData?.timezone) {
    Swal.fire({
      icon: 'error',
      title: translations['alert_error_title'] || 'Error',
      text:
        translations['profile_not_loaded'] || 'Profile data is not loaded yet.',
    })
    return
  }

  const specialistTimezone = currentSpecialistData.timezone
  let confirmText = (translations['sync_timezone_confirm_text'] || '').replace(
    '{timezone}',
    `<strong>${specialistTimezone}</strong>`,
  )

  const result = await Swal.fire({
    title: translations['sync_timezone_confirm_title'] || 'Sync Timezones?',
    html: confirmText,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: translations['confirm'] || 'Confirm',
    cancelButtonText: translations['cancel'] || 'Cancel',
  })

  if (result.isConfirmed) {
    // cambiar a ajax
    $.ajax({
      url: 'specialist-sync-timezone',
      type: 'POST',
      dataType: 'json',
      success: function (result) {
        if (result.value) {
          Swal.fire({
            icon: 'success',
            title: translations['alert_success_title'] || 'Success',
            text:
              translations['sync_timezone_success_message'] ||
              'Timezones synchronized successfully.',
            timer: 2000,
            showConfirmButton: false,
          }).then(() => {
            // Recargar todo el perfil para reflejar los cambios en la lista
            loadProfile(pageData.userId)
          })
        } else {
          Swal.fire({
            icon: 'error',
            title: translations['alert_error_title'] || 'Error',
            text: result.message || translations['unexpected_error'],
          })
        }
      },
      error: function (xhr) {
        Swal.fire({
          icon: 'error',
          title: translations['alert_error_title'] || 'Error',
          text: xhr.responseJSON?.message || translations['unexpected_error'],
        })
      },
    })
  }
}

function handleLocationFormSubmit(e) {
  const { formData } = e.detail
  const locationId = formData.get('location_id')
  if (!formData.has('is_primary')) {
    formData.set('is_primary', '0')
  }
  if (locationId) {
    formData.append('_method', 'PUT')
    sendFormData(formData, `specialist-locations/${locationId}`, 'POST')
  } else {
    sendFormData(formData, 'specialist-locations', 'POST')
  }
  hideLocationForm()
}

async function deleteSocialLink(id) {
  await sendFormData(null, `specialist-social-links/${id}`, 'DELETE')
}
async function deleteCertification(id) {
  await sendFormData(null, `specialist-certifications/${id}`, 'DELETE')
}
async function deleteAvailability(id) {
  await sendFormData(null, `specialist-availability/${id}`, 'DELETE')
}
async function deletePricing(id) {
  await sendFormData(null, `specialist-pricing/${id}`, 'DELETE')
}
async function deleteLocation(id) {
  await sendFormData(null, `specialist-locations/${id}`, 'DELETE')
}

async function sendFormData(formData, endpoint, method) {
  try {
    const options = { method, headers: { Accept: 'application/json' } }
    if (method !== 'DELETE') options.body = formData
    const response = await fetch(endpoint, options)
    const result = await response.json()
    if (result.value) {
      Swal.fire({
        icon: 'success',
        title: translations['alert_success_title'],
        text: result.message || translations['saved_successfully'],
        timer: 1500,
        showConfirmButton: false,
      }).then(() => {
        if (endpoint.includes('update-profile')) {
          location.reload()
        } else {
          loadProfile(pageData.userId)
        }
      })
      if (endpoint.includes('update-profile')) {
        bootstrap.Modal.getInstance(
          document.getElementById('editUserModal'),
        ).hide()
      }
    } else {
      Swal.fire({
        icon: 'error',
        title: translations['alert_error_title'],
        text: result.data.error || translations['unexpected_error'],
      })
    }
  } catch (error) {
    console.log(error)

    Swal.fire({
      icon: 'error',
      title: translations['alert_error_title'],
      text: error.JSON?.message || translations['unexpected_error'],
    })
  }
}

async function loadCountries(selectedCountry, selectedState, selectedCity) {
  try {
    const response = await fetch('countries')
    const result = await response.json()
    if (result.value && result.data) {
      const countrySelect = $('#country_id')
      countrySelect.html('<option></option>')
      result.data.forEach((country) => {
        countrySelect.append(
          new Option(country.country_name, country.country_id),
        )
      })
      if (selectedCountry) {
        countrySelect.val(selectedCountry).trigger('change')
        await loadStates(selectedCountry, selectedState, selectedCity)
      }
    }
  } catch (error) {
    console.error('Error loading countries:', error)
  }
}

async function loadStates(countryId, selectedState, selectedCity) {
  try {
    const response = await fetch(`states?country_id=${countryId}`)
    const result = await response.json()
    const stateSelect = $('#state_id')
    stateSelect.html('<option></option>').prop('disabled', false)
    if (result.value && result.data) {
      result.data.forEach((state) => {
        stateSelect.append(new Option(state.state_name, state.state_id))
      })
      if (selectedState) {
        stateSelect.val(selectedState).trigger('change')
        await loadCities(selectedState, selectedCity)
      }
    }
  } catch (error) {
    console.error('Error loading states:', error)
  }
}

async function loadCities(stateId, selectedCity) {
  try {
    const response = await fetch(`cities?state_id=${stateId}`)
    const result = await response.json()
    const citySelect = $('#city_id')
    citySelect.html('<option></option>').prop('disabled', false)
    if (result.value && result.data) {
      result.data.forEach((city) => {
        citySelect.append(new Option(city.city_name, city.city_id))
      })
      if (selectedCity) {
        citySelect.val(selectedCity).trigger('change')
      }
    }
  } catch (error) {
    console.error('Error loading cities:', error)
  }
}

$('#country_id').on('change', function () {
  const countryId = $(this).val()
  $('#state_id').val(null).trigger('change').prop('disabled', true)
  $('#city_id').val(null).trigger('change').prop('disabled', true)
  if (countryId) {
    loadStates(countryId)
  }
})

$('#state_id').on('change', function () {
  const stateId = $(this).val()
  $('#city_id').val(null).trigger('change').prop('disabled', true)
  if (stateId) {
    loadCities(stateId)
  }
})
