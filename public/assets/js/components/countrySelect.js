import { getCountries } from '../apiConfig.js'

// Asegúrate de que IMask y jQuery ($) estén disponibles globalmente.

window.countrySelectMasks = window.countrySelectMasks || {}

export async function countrySelect(
  telInputId,
  containerSelector,
  defaultPhone,
  modalSelector,
  defaultSuffix = 'CA'
) {
  console.log(defaultSuffix)

  const telInput = document.getElementById(telInputId)
  if (!telInput) {
    return console.error(`No se encontró input con id "${telInputId}"`)
  }

  const oldContainerId = telInput.dataset.countrySelectContainerId
  if (oldContainerId) {
    const oldContainer = document.getElementById(oldContainerId)
    if (oldContainer) {
      const oldSelect = oldContainer.querySelector('select.country-select')
      if (oldSelect) {
        const $oldSelect = $(oldSelect)
        if ($oldSelect.hasClass('select2-hidden-accessible')) {
          $oldSelect.select2('destroy')
        }
      }
      if (window.countrySelectMasks[telInputId]) {
        window.countrySelectMasks[telInputId].destroy()
        delete window.countrySelectMasks[telInputId]
      }
      oldContainer.innerHTML = ''
    }
  }

  const container = document.querySelector(containerSelector)
  if (!container) {
    return console.warn(`No se encontró el contenedor "${containerSelector}"`)
  }

  container.classList.add('mb-2')

  const containerId = `country-select-container-${telInputId}`
  container.id = containerId
  telInput.dataset.countrySelectContainerId = containerId

  const countrySelect = document.createElement('select')
  countrySelect.id = `country-select-${telInputId}`
  countrySelect.className = 'country-select form-select'
  container.appendChild(countrySelect)

  try {
    let countries = await getCountries()
    countries = countries.data

    function formatOptionWithFlag(option) {
      if (!option.id) return option.text
      const suffix = $(option.element).data('sufijo')
      const prefijoNormal = $(option.element).data('prefijo')
      const country_name = $(option.element).data('pais')
      return $(`
        <div class="country-option-container">
          <span class="country-sufijo">${
            suffix ? suffix.toUpperCase() : ''
          }</span>
          <div class="country-details">
            <span class="country-prefijo">${prefijoNormal || ''}</span>
            <span class="country-name">${
              country_name ? country_name.toUpperCase() : ''
            }</span>
          </div>
        </div>`)
    }

    function applyMask(id, value = '') {
      let country = countries.find((c) => String(c.country_id) === String(id))
      if (!country) {
        console.warn('No se encontró el país para ID:', id)
        return
      }

      let maskDb = country.phone_mask ? country.phone_mask.toLowerCase() : ''
      let prefijo = country.normalized_prefix
      let mask = getCleanMask(maskDb, prefijo)

      // Destruye cualquier máscara anterior
      if (window.countrySelectMasks[telInputId]) {
        window.countrySelectMasks[telInputId].destroy()
      }

      let cleanValue = ''
      if (value) {
        // LA FORMA MÁS SIMPLE Y DIRECTA:
        // Tomamos el string original y eliminamos TODO lo que no sea un dígito.
        cleanValue = value.replace(/\D/g, '')
      }

      // Logs para depuración: Revisa la consola de tu navegador (F12)
      console.log(`[countrySelect] ID: ${id}`)
      console.log(`[countrySelect] Valor Inicial: "${value}"`)
      console.log(`[countrySelect] Valor Limpio: "${cleanValue}"`)
      console.log(`[countrySelect] Máscara a aplicar: "${mask}"`)

      // 2. Prepara el input HTML: límpialo y ponle el placeholder.
      // Es crucial que el input esté vacío ANTES de inicializar IMask.
      telInput.value = ''
      telInput.placeholder = mask ? `e.g. ${mask}` : 'e.g. 0000000000'

      // 3. Ahora sí, inicializa IMask en el input limpio.
      const phoneMask = IMask(telInput, {
        mask: mask,
        lazy: false,
        placeholderChar: '_',
      })
      window.countrySelectMasks[telInputId] = phoneMask

      // 4. Finalmente, asigna los dígitos limpios a la instancia de la máscara.
      // IMask se encargará de formatear estos dígitos y mostrarlos correctamente en el input.
      if (cleanValue) {
        phoneMask.value = cleanValue
      }

      // Esto asegura que la visualización del input se actualice
      phoneMask.updateValue()

      // --- FIN DE LA LÓGICA CORREGIDA ---

      telInput.addEventListener(
        'focus',
        () => {
          const pos = telInput.value.indexOf(')') + 2
          setTimeout(() => {
            telInput.setSelectionRange(pos, pos)
          }, 10)
        },
        { once: true }
      )
    }

    function getCleanMask(maskStr, prefijo) {
      let cleanPrefix = prefijo.replace('+', '')
      let maskBody = maskStr.replace(/^\+\d+\s*/, '')
      maskBody = maskBody.replace(/#/g, '0').replace(/\s+/g, '')
      return `(+${cleanPrefix}) ${maskBody}`
    }

    let options = []
    countries
      .sort((a, b) => a.prefijo - b.prefijo)
      .forEach((country) => {
        options.push(`
          <option 
            data-sufijo="${country.suffix}" 
            data-prefijo="${country.normalized_prefix}"
            data-pais="${country.country_name}" 
            data-mask="${country.phone_mask.toLowerCase()}" 
            value="${country.country_id}">
            ${country.suffix} (${country.normalized_prefix}) (${
          country.country_name
        })
          </option>`)
      })
    countrySelect.innerHTML = options.join('')

    const $selectElem = $(countrySelect)

    let select2Options = {
      width: '100%',
      placeholder: 'Select a country',
      templateResult: formatOptionWithFlag,
      templateSelection: formatOptionWithFlag,
    }

    let $modalBody = $selectElem.closest('.modal-body')
    let $modalParent = $selectElem.closest('.modal')
    if ($modalBody.length) {
      select2Options.dropdownParent = $modalBody
    } else if (modalSelector && $(modalSelector).length) {
      select2Options.dropdownParent = $(modalSelector)
    } else if ($modalParent.length) {
      select2Options.dropdownParent = $modalParent
    }

    // console.log('Inicializando Select2 en el elemento:', $selectElem)
    $selectElem.select2(select2Options).on('change', function () {
      const selectedValue = $(this).val()
      applyMask(selectedValue, '')
    }).on('select2:open', function () {
      setTimeout(() => {
        const openDropdown = document.querySelector('.select2-container--open .select2-dropdown');
        if (openDropdown) {
          openDropdown.classList.add('country-select-dropdown');
        }
      }, 0);
    })

    let selectedCountryId = countrySelect.options[0].value // Valor por defecto si no se encuentra nada
    const cleanDefaultPhone = defaultPhone ? defaultPhone.trim() : ''

    if (cleanDefaultPhone) {
      // La lógica existente para defaultPhone tiene prioridad
      const prefijoMatch = cleanDefaultPhone.match(/\(\+(\d+)\)/)
      console.log(cleanDefaultPhone)

      if (prefijoMatch) {
        const prefijo = `+${prefijoMatch[1]}`
        const country = countries.find((c) => c.normalized_prefix === prefijo)
        if (country) {
          selectedCountryId = country.country_id
        }
      }
    } else if (defaultSuffix) {
      // NUEVO: Si no hay defaultPhone, se busca por el sufijo
      const country = countries.find(
        (c) =>
          c.suffix && c.suffix.toLowerCase() === defaultSuffix.toLowerCase()
      )
      if (country) {
        selectedCountryId = country.country_id
      }
    }

    const initialValue = cleanDefaultPhone || ''
    $selectElem.val(selectedCountryId) // 1. Establece el país seleccionado en el <select>

    // 2. Llama a applyMask UNA SOLA VEZ con el ID y el valor inicial (que puede ser vacío o el defaultPhone)
    applyMask(selectedCountryId, initialValue)

    // 3. Notifica a Select2 para que actualice su visualización
    $selectElem.trigger('change.select2')
  } catch (error) {
    console.error('Falló la inicialización del selector de país:', error)
    container.innerHTML =
      '<div class="alert alert-danger">Error al cargar los países.</div>'
  }
}
