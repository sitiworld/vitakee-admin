import { validateNotifications } from './controllers/notificationsController.js'

import { hideLoader, showLoader } from './helpers/helpers.js'
dayjs.extend(window.dayjs_plugin_isSameOrBefore)
dayjs.extend(window.dayjs_plugin_isoWeek)
dayjs.extend(window.dayjs_plugin_utc)

const d = document

d.addEventListener('DOMContentLoaded', async () => {
  console.log('DOM CHARGED')

  $(document).ajaxStart(function () {
    // Esto se llamará automáticamente cuando la primera petición AJAX inicie
    showLoader()
  })

  $(document).ajaxStop(function () {
    // Esto se llamará automáticamente cuando TODAS las peticiones hayan terminado
    hideLoader()
  })

  validateNotifications()

  let viewsToCheck = [
    'dashboard',
    'dashboard_administrator',
    'dashboard_specialist',
  ]
  console.log(window.location.pathname.split('/').pop())

  if (viewsToCheck.includes(window.location.pathname.split('/').pop())) {
    checkSecurityQuestions()
  }

  document.addEventListener('input', (e) => {
    if (e.target.classList.contains('number', 'form-control')) {
      e.target.value = e.target.value.replace(/[^0-9\.,]/g, '')
    }
  })
})

export async function checkSecurityQuestions() {
  try {
    const response = await fetch(`security-questions`)
    const json = await response.json()
    let { data } = json

    console.log(json)
    const localStorageKey = `hideSecurityQuestionWarning_${data.role}_${data.user_id}`

    if (json.value) {
      localStorage.setItem(localStorageKey, true)
    }

    if (!json.value) {
      if (localStorage.getItem(localStorageKey) === 'true') return
      localStorage.setItem(localStorageKey, false)
    }

    if (localStorage.getItem(localStorageKey) !== 'true') {
      Swal.fire({
        title: language.title_security,
        text: language.text_security,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: language.confirmButton_security,
        cancelButtonText: language.cancelButton_security,
        focusConfirm: false, // <--- Esto evita el focus automático
        html: `
          <div class="">
          <p>${language.text_security}</p>
            <input class="form-check-input mx-1" type="checkbox" id="swal-security-checkbox">
            <label class="form-check-label" for="swal-security-checkbox">
              ${language.checkbox_security}
            </label>
          </div>
        `,
        allowOutsideClick: false,
      }).then((result) => {
        const checked = document.getElementById(
          'swal-security-checkbox',
        ).checked
        if (checked) {
          localStorage.setItem(localStorageKey, true)
        } else {
          localStorage.setItem(localStorageKey, false)
        }

        if (result.isConfirmed) {
          window.location.href = `my_profile`
        }
      })
    }
  } catch (err) {
    console.error('Error comprobando preguntas de seguridad:', err)
  }
}
