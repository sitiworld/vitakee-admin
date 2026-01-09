export function showAlert(value, message) {
  Swal.fire({
    icon: value ? 'success' : 'error',
    title: value ? language.successTitle_helper : language.errorTitle_helper,
    text: value ? language.successText_helper : language.errorText_helper,
    confirmButtonText: language.confirmButtonText_helper,
  })
}

export function showConfirmation({ type, actionCallback, message = {} }) {
  let title = ''
  let text = ''
  let icon = 'question'
  let confirmButtonText = ''
  let cancelButton = true
  const cancelButtonText = language.cancelButtonText_helper

  switch (type) {
    case 'success':
      title = message.hasOwnProperty('title')
        ? message.title
        : language.successTitle_helper
      text = message.hasOwnProperty('text')
        ? message.text
        : language.successText_helper
      confirmButtonText = language.confirmButtonText_helper
      icon = 'success'
      cancelButton = false
      break

    case 'error':
      title = message.hasOwnProperty('title')
        ? message.title
        : language.errorTitle_helper
      text = message.hasOwnProperty('text')
        ? message.text
        : language.errorText_helper
      confirmButtonText = language.confirmButtonText_helper
      icon = 'error'
      cancelButton = false
      break

    case 'create':
      title = message.hasOwnProperty('title')
        ? message.title
        : language.createConfirmTitle_helper
      text = message.hasOwnProperty('text')
        ? message.text
        : language.createConfirmText_helper
      confirmButtonText = language.createConfirmButton_helper
      icon = 'question'
      break

    case 'save':
      title = message.hasOwnProperty('title')
        ? message.title
        : language.saveConfirmTitle_helper
      text = message.hasOwnProperty('text')
        ? message.text
        : language.saveConfirmText_helper
      confirmButtonText = language.saveConfirmButton_helper
      icon = 'question'
      break

    case 'delete':
      title = message.hasOwnProperty('title')
        ? message.title
        : language.deleteConfirmTitle_helper
      text = message.hasOwnProperty('text')
        ? message.text
        : language.deleteConfirmText_helper
      confirmButtonText = language.deleteConfirmButton_helper
      icon = 'warning'
      break

    // (se deja tal cual tu segundo 'delete' para no alterar el flujo de tu app)
    case 'delete':
      title = message.hasOwnProperty('title')
        ? message.title
        : language.deleteConfirmTitle_helper
      text = message.hasOwnProperty('text')
        ? message.text
        : language.deleteConfirmText_helper
      confirmButtonText = language.deleteConfirmButton_helper
      icon = 'warning'
      break

    default:
      title = message.hasOwnProperty('title')
        ? message.title
        : language.defaultConfirmTitle_helper
      text = message.hasOwnProperty('text')
        ? message.text
        : language.defaultConfirmText_helper
      confirmButtonText = language.defaultConfirmButton_helper
      icon = 'question'
      break
  }

  Swal.fire({
    title: title,
    text: text,
    icon: icon,
    showCancelButton: cancelButton,
    confirmButtonText: language.confirmButtonText_helper,
    cancelButtonText: language.cancelButtonText_helper,
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      actionCallback()
    }
  })
}

function createItem() {
  console.log(language.createdTitle)
  Swal.fire(language.createdTitle, language.createdText, 'success')
}

function saveChanges() {
  console.log(language.savedTitle)
  Swal.fire(language.savedTitle, language.savedText, 'success')
}

function deleteItem() {
  console.log(language.deletedTitle)
  Swal.fire(language.deletedTitle, language.deletedText, 'success')
}

export function validateFormFields(form, fieldsToValidate = [], genericError) {
  // Limpiar todos los errores previos antes de validar
  clearAllValidationMessages(form)

  const fields = form.querySelectorAll('input, select, textarea')
  let firstInvalid = null
  let isValid = true

  // Agregar event listener a cada campo para limpiar validación al escribir
  fields.forEach((field) => {
    if (!field.hasAttribute('data-has-validation-listener')) {
      field.addEventListener('input', () => {
        field.classList.remove('is-invalid')
        removeMessage(field)
      })
      field.setAttribute('data-has-validation-listener', 'true')
    }
  })

  // ----------- Identificar los 3 posibles campos de contraseña -------------
  const passwordRegisterField = Array.from(fields).find((f) =>
    f.name?.toLowerCase().includes('password_register')
  )
  const passwordChangeField = Array.from(fields).find(
    (f) => f.name?.toLowerCase() === 'password'
  )
  const confirmPasswordField = Array.from(fields).find((f) =>
    f.name?.toLowerCase().includes('confirm_password')
  )

  // ----------- 1. Validación de campos individuales (requeridos, email, etc.) -------------
  fields.forEach((field) => {
    const fieldIdName = field.name || field.id || ''
    let isFieldRequired = fieldsToValidate.includes(fieldIdName)
    const isVisible = field.offsetParent !== null

    // En cambio de contraseña, no validar required de estos 2 aquí.
    if (passwordChangeField) {
      if (field === passwordChangeField || field === confirmPasswordField) {
        isFieldRequired = false
      }
    }

    // Validación de vacío (requerido)
    if (isFieldRequired && isVisible && !field.value.trim()) {
      field.classList.add('is-invalid')
      addMessage(field, genericError ? genericError : language.requiredField_helper)
      if (!firstInvalid) firstInvalid = field
      isValid = false
      return
    }

    // Validación de email
    if (
      isVisible &&
      field.value &&
      (field.type === 'email' ||
        field.getAttribute('data-validate') === 'email')
    ) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!emailRegex.test(field.value.trim())) {
        field.classList.add('is-invalid')
        addMessage(field, language.invalidEmail_helper)
        if (!firstInvalid) firstInvalid = field
        isValid = false
        return
      }
    }
  })

  // ----------- 2. Lógica de validación de Contraseñas (Multi-campo) -------------
  if (passwordRegisterField && confirmPasswordField) {
    const passValue = passwordRegisterField.value
    const confirmValue = confirmPasswordField.value
    if (passValue !== confirmValue) {
      isValid = false
      passwordRegisterField.classList.add('is-invalid')
      confirmPasswordField.classList.add('is-invalid')
      addMessage(confirmPasswordField, language.passwordMismatch_helper)
      if (!firstInvalid) firstInvalid = confirmPasswordField
    }
  }
  // Escenario B: Formulario de Cambio de Contraseña (si existe 'password')
  else if (passwordChangeField && confirmPasswordField) {
    const passValue = passwordChangeField.value
    const confirmValue = confirmPasswordField.value
    if (passValue !== confirmValue) {
      isValid = false
      passwordChangeField.classList.add('is-invalid')
      confirmPasswordField.classList.add('is-invalid')
      addMessage(confirmPasswordField, language.passwordMismatch_helper)
      if (!firstInvalid) firstInvalid = confirmPasswordField
    }
  }

  // ----------- Foco y scroll al primer error -------------
  if (!isValid && firstInvalid) {
    firstInvalid.focus()
    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' })
  }

  return isValid

  // ----------- Helpers para mensajes -------------
  function addMessage(input, text) {
    let msgElem
    const parentSelector = input.getAttribute('data-error-parent')
    const msgKey = input.name || input.id || ''

    if (parentSelector) {
      const parent = input.closest(parentSelector)
      if (
        parent &&
        parent.nextElementSibling &&
        parent.nextElementSibling.classList.contains('validation-message') &&
        parent.nextElementSibling.getAttribute('data-for') === msgKey
      ) {
        msgElem = parent.nextElementSibling
      }
    } else {
      if (
        input.nextElementSibling &&
        input.nextElementSibling.classList.contains('validation-message')
      ) {
        msgElem = input.nextElementSibling
      }
    }

    if (!msgElem) {
      msgElem = document.createElement('div')
      msgElem.className = 'validation-message'
      msgElem.setAttribute('data-for', msgKey)
    }

    msgElem.textContent = text
    msgElem.style.color = 'red'
    msgElem.style.fontSize = '0.95em'
    msgElem.style.marginTop = '4px'

    if (parentSelector) {
      const parent = input.closest(parentSelector)
      if (parent && parent.nextElementSibling !== msgElem) {
        parent.parentNode.insertBefore(msgElem, parent.nextElementSibling)
      }
    } else {
      if (input.nextElementSibling !== msgElem) {
        input.parentNode.insertBefore(msgElem, input.nextSibling)
      }
    }
  }

  function removeMessage(input) {
    const parentSelector = input.getAttribute('data-error-parent')
    const msgKey = input.name || input.id || ''
    let msgElem = null

    if (parentSelector) {
      const parent = input.closest(parentSelector)
      if (
        parent &&
        parent.nextElementSibling &&
        parent.nextElementSibling.classList.contains('validation-message') &&
        parent.nextElementSibling.getAttribute('data-for') === msgKey
      ) {
        msgElem = parent.nextElementSibling
      }
    } else {
      if (
        input.nextElementSibling &&
        input.nextElementSibling.classList.contains('validation-message')
      ) {
        msgElem = input.nextElementSibling
      }
    }
    if (msgElem) msgElem.remove()
  }

  function clearAllValidationMessages(form) {
    form.querySelectorAll('.validation-message').forEach((msg) => msg.remove())
    form
      .querySelectorAll('.is-invalid')
      .forEach((input) => input.classList.remove('is-invalid'))
  }
}

export function clearValidationMessages(form) {
  // Quita todos los mensajes de texto (.validation-message)
  form.querySelectorAll('.validation-message').forEach((msg) => msg.remove())

  // Quita las clases de validación (.is-invalid y .is-valid) de todos los campos.
  form.querySelectorAll('.is-invalid, .is-valid').forEach((input) => {
    input.classList.remove('is-invalid', 'is-valid')
  })
}

export function maskMedida(input, type) {
  // Permite pasar id o input
  if (typeof input === 'string') input = document.getElementById(input)
  if (!input) return null

  // Destruir máscara previa si existe
  if (input._imaskInstance) {
    input._imaskInstance.destroy()
    input._imaskInstance = null
  }

  let maskOptions = null
  let placeholder = ''

  function autoDecimalHandler(e) {
    let input = e.target
    let v = input.value.replace(/[^\d]/g, '')
    if (
      v.length === 3 &&
      !input.value.includes('.') &&
      !input.value.includes(',')
    ) {
      input.value = v[0] + '.' + v.slice(1)
    }
  }
  input.removeEventListener('input', autoDecimalHandler)

  switch (type) {
    case 'peso-europeo':
      maskOptions = {
        mask: Number,
        scale: 2,
        signed: false,
        thousandsSeparator: '',
        padFractionalZeros: true,
        normalizeZeros: true,
        radix: ',',
        mapToRadix: ['.'],
        min: 0,
        max: 500,
      }
      placeholder = '70,00'
      break

    case 'altura-europea':
      maskOptions = {
        mask: '0.00',
        lazy: false,
        placeholderChar: '_',
      }
      placeholder = '1.72'
      setTimeout(() => {
        input.addEventListener('input', autoDecimalHandler)
      }, 10)
      break

    case 'altura-americana':
      // Solo fuerza el formato (no el rango)
      maskOptions = {
        mask: '0\'00"',
        lazy: false,
        placeholderChar: '_',
      }
      placeholder = `5'11"`

      // Corrección al perder el foco (no mientras escribe)
      setTimeout(() => {
        input.addEventListener('blur', function corregirAlturaAmericana() {
          // Extraer pies y pulgadas del valor ingresado
          let val = input.value.match(/^(\d{1,2})'(\d{2})"?$/)
          if (!val) return

          let feet = parseInt(val[1], 10)
          let inches = parseInt(val[2], 10)

          // Correcciones de rango
          if (feet < 2) feet = 2
          if (feet > 8) feet = 8
          if (inches < 0) inches = 0
          if (inches > 11) inches = 11

          // Rellenar pulgadas con 2 dígitos
          input.value = `${feet}'${inches.toString().padStart(2, '0')}"`
        })
      }, 10)
      break

    default:
      console.warn(language.errorGeneric_helper)
      input.removeAttribute('placeholder')
      return null
  }

  input.setAttribute('placeholder', placeholder)
  input._imaskInstance = IMask(input, maskOptions)
  return input._imaskInstance
}

export function americanToMetersString(american) {
  // Extraer pies y pulgadas del string
  let match = american.match(/(\d{1,2})'(\d{1,2})"/)
  if (!match) return null
  let feet = parseInt(match[1], 10)
  let inches = parseInt(match[2], 10)
  let totalInches = feet * 12 + inches
  let meters = totalInches * 0.0254
  // Redondear a 2 decimales y devolver como string
  return meters.toFixed(2)
}

export function metersToAmerican(meters) {
  let totalInches = meters / 0.0254
  let feet = Math.floor(totalInches / 12)
  let inches = Math.round(totalInches % 12)
  // Ajusta si las pulgadas suman 12
  if (inches === 12) {
    feet++
    inches = 0
  }
  return { feet, inches }
}

export function validateInputOnBlur(
  inputId,
  url,
  paramName,
  initialValue = '',
  translations,
  errorKey,
  extraDataCallback = null
) {
  const inputElement = document.getElementById(inputId)
  if (!inputElement) {
    console.error(`${language.errorGeneric_helper} (${inputId})`)
    return
  }

  inputElement.addEventListener('blur', (e) => {
    const input = e.target
    const currentValue = input.value.trim()

    // Función interna para mostrar mensajes debajo del input
    function showValidationMessage(message, isError) {
      let msgElem = input.nextElementSibling
      if (!msgElem || !msgElem.classList.contains('validation-message')) {
        msgElem = document.createElement('div')
        msgElem.classList.add('validation-message')
        input.parentNode.insertBefore(msgElem, input.nextSibling)
      }
      msgElem.textContent = message
      msgElem.style.color = isError ? 'red' : 'green'
      msgElem.style.fontSize = '0.9em'
    }

    // Limpiar estados si el campo está vacío
    if (!currentValue) {
      input.classList.remove('is-invalid', 'is-valid')
      let msgElem = input.nextElementSibling
      if (msgElem && msgElem.classList.contains('validation-message')) {
        msgElem.textContent = ''
      }
      return
    }

    // No validar si el valor no ha cambiado del original
    if (currentValue === initialValue) {
      return
    }

    // Preparar los datos para la petición AJAX
    let requestData = {
      [paramName]: currentValue,
    }

    // Si hay una función para datos extra, la ejecutamos y fusionamos los resultados
    if (typeof extraDataCallback === 'function') {
      const extraData = extraDataCallback()
      requestData = { ...requestData, ...extraData }
    }

    $.ajax({
      url: url,
      type: 'POST',
      data: requestData,
      dataType: 'json',
      success: function (res) {
        // Mantengo tu lógica original:
        // res.value === true  => (según tu comentario) YA EXISTE (error)
        // Aunque aquí se usa: const isAlreadyTaken = res.value !== true
        const isAlreadyTaken = res.value !== true

        input.classList.toggle('is-invalid', isAlreadyTaken)
        input.classList.toggle('is-valid', !isAlreadyTaken)

        if (isAlreadyTaken) {
          showValidationMessage(
            translations[errorKey] || language.valueAlreadyInUse_helper,
            true
          )
        } else {
          showValidationMessage('', false) // p.ej. Disponible
        }
      },
      error: function () {
        const errorMessage =
          translations[errorKey] || language.errorGeneric_helper
        Swal.fire({
          title: language.errorTitle_helper,
          text: errorMessage,
          icon: 'error',
          confirmButtonText: language.confirmButtonText_helper,
        })
      },
    })
  })
}

export function validateFieldAsync(
  inputElement,
  currentValue,
  initialValue,
  options
) {
  const {
    url,
    paramName,
    translations,
    errorKey,
    extraDataCallback = null,
  } = options

  // Función interna para mostrar mensajes
  function showValidationMessage(message, isError) {
    let msgElem = inputElement.nextElementSibling
    if (!msgElem || !msgElem.classList.contains('validation-message')) {
      msgElem = document.createElement('div')
      msgElem.classList.add('validation-message')
      inputElement.parentNode.insertBefore(msgElem, inputElement.nextSibling)
    }
    msgElem.textContent = message
    msgElem.style.color = isError ? 'red' : 'green'
    msgElem.style.fontSize = '0.9em'
  }

  // Limpiar si el valor actual (ya limpio) está vacío
  if (!currentValue) {
    inputElement.classList.remove('is-invalid', 'is-valid')
    showValidationMessage('', false)
    return
  }

  // Comparar el valor actual limpio con el inicial limpio
  if (currentValue === initialValue) {
    inputElement.classList.remove('is-invalid', 'is-valid')
    showValidationMessage('', false)
    return
  }

  // Preparar los datos para la petición AJAX
  let requestData = {
    [paramName]: currentValue, // Enviar el valor limpio al backend
  }

  if (typeof extraDataCallback === 'function') {
    requestData = { ...requestData, ...extraDataCallback() }
  }

  $.ajax({
    url: url,
    type: 'POST',
    data: requestData,
    dataType: 'json',
    success: function (res) {
      const isAvailable = res.value === true
      inputElement.classList.toggle('is-valid', isAvailable)
      inputElement.classList.toggle('is-invalid', !isAvailable)

      showValidationMessage(
        isAvailable ? '' : translations[errorKey] || language.valueUnavailable_helper,
        !isAvailable
      )
    },
    error: function () {
      // Mantengo tu manejo simple aquí.
      console.log(language.errorGeneric_helper)
    },
  })
}

// LOADERS

// Función para mostrar el loader con overlay
export function showLoader() {
  let overlay = document.getElementById('custom-loader-overlay')
  if (!overlay) {
    overlay = document.createElement('div')
    overlay.id = 'custom-loader-overlay'
    overlay.style.display = 'none'
    overlay.innerHTML = `
      <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">${language.loading_helper}</span>
      </div>
    `
    document.body.insertBefore(overlay, document.body.firstChild)
  }

  // Quitar foco de cualquier elemento activo para evitar interacción
  if (document.activeElement) {
    document.activeElement.blur()
  }

  overlay.style.display = 'flex'
  setTimeout(() => {
    overlay.classList.add('show')
  }, 10)
}

// Función para ocultar el loader con animación
export function hideLoader() {
  const overlay = document.getElementById('custom-loader-overlay')
  if (!overlay) return

  // Quitar clase show para iniciar transición de opacidad a 0
  overlay.classList.remove('show')

  // Al terminar la transición (300ms) ocultar el overlay
  setTimeout(() => {
    overlay.style.display = 'none'
  }, 300)
}
