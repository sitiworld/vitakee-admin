// --- CALCULATION ENGINE ---
const calculationEngine = {
  /**
   * Calculates the base cost of a single unit.
   * @param {number} materialCost - The cost of materials for one unit.
   * @param {number} laborCost - The cost of labor for one unit.
   * @returns {number} The total unit cost.
   */
  getUnitCost: function (materialCost, laborCost) {
    return (materialCost || 0) + (laborCost || 0)
  },

  /**
   * Calculates the profit amount for a given cost.
   * @param {number} cost - The base cost to apply the profit to.
   * @param {number} profitValue - The value of the profit (e.g., 15 for 15% or 100 for $100).
   * @param {string} profitType - The type of profit ('porcentaje' or 'monto').
   * @returns {number} The calculated profit amount.
   */
  getProfitAmount: function (cost, profitValue, profitType) {
    cost = cost || 0
    profitValue = profitValue || 0
    if (profitType === 'porcentaje') {
      return cost * (profitValue / 100)
    }
    return profitValue // Assumes 'monto' if not percentage
  },

  /**
   * Calculates the final sale price of a single unit.
   * @param {number} unitCost - The base cost of the unit.
   * @param {number} materialProfit - The profit amount from materials.
   * @param {number} laborProfit - The profit amount from labor.
   * @returns {number} The total unit price.
   */
  getUnitPrice: function (unitCost, materialProfit, laborProfit) {
    return (unitCost || 0) + (materialProfit || 0) + (laborProfit || 0)
  },

  /**
   * Calculates the total value based on a unit value and quantity.
   * @param {number} unitValue - The value of a single unit (can be cost or price).
   * @param {number} quantity - The number of units.
   * @returns {number} The total calculated value.
   */
  getTotal: function (unitValue, quantity) {
    return (unitValue || 0) * (quantity || 0) // Default quantity to 1 to avoid multiplying by zero
  },
}

export function showLoader() {
  let overlay = document.getElementById('custom-loader-overlay')
  if (!overlay) {
    overlay = document.createElement('div')
    overlay.id = 'custom-loader-overlay'
    overlay.style.display = 'none'
    overlay.innerHTML = `
      <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
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

// FUNCIÓN PARA FORMATEAR CUALQUIER FECHA A FORMATO MM/DD/YYYY HH:MM AM/PM SI SE REQUIERE
export function formatDateTime(dateString, withTime = false, locale = 'en-US') {
  if (!dateString) return '-'

  // Reemplazar guiones por "/" para que se interprete como fecha local
  const date = new Date(dateString.replace(/-/g, '/'))
  if (isNaN(date)) return ''

  const options = {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
  }

  if (withTime) {
    options.hour = '2-digit'
    options.minute = '2-digit'
    options.hour12 = true
  }

  return date.toLocaleString(locale, options).replace(',', '')
}

export const systemColors = {
  switchColorOn: '#1b188f', // Verde para "on"
  switchColorOff: '#4a4b61', // Rojo para "off"
}

export const iconsMap = {
  facebook: 'mdi mdi-facebook text-icons',
  twitter:
    '<svg height="12" width="12" class="text-icons" version="1.1" id="Layer_1" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve"><path d="M14.095479,10.316482L22.286354,1h-1.940718l-7.115352,8.087682L7.551414,1H1l8.589488,12.231093L1,23h1.940717  l7.509372-8.542861L16.448587,23H23L14.095479,10.316482z M11.436522,13.338465l-0.871624-1.218704l-6.924311-9.68815h2.981339  l5.58978,7.82155l0.867949,1.218704l7.26506,10.166271h-2.981339L11.436522,13.338465z"/></svg>',
  instagram: 'mdi mdi-instagram text-icons',
  linkedin: 'mdi mdi-linkedin text-icons',
  skype: 'mdi mdi-skype text-icons',
  threads: 'mdi mdi-at text-icons',
  telegram: 'mdi mdi-telegram text-icons',
  whatsapp: 'mdi mdi-whatsapp text-icons',
  whatsapp_business:
    '<svg class="text-icons" width="16" height="16" viewBox="0 0 192 192" xmlns="http://www.w3.org/2000/svg"><path d="m60.359 160.867 2.894-5.256a6.003 6.003 0 0 0-4.284-.581l1.39 5.837ZM22 170l-5.837-1.39a6.002 6.002 0 0 0 7.227 7.227L22 170Zm9.133-38.359 5.837 1.39a6.001 6.001 0 0 0-.581-4.284l-5.256 2.894ZM96 176c44.183 0 80-35.817 80-80h-12c0 37.555-30.445 68-68 68v12Zm-38.535-9.877C68.9 172.42 82.04 176 96 176v-12c-11.884 0-23.04-3.043-32.747-8.389l-5.788 10.512Zm-34.075 9.714 38.358-9.133-2.78-11.674-38.358 9.133 2.78 11.674Zm1.906-45.585-9.133 38.358 11.674 2.78 9.133-38.359-11.674-2.779ZM16 96c0 13.959 3.58 27.1 9.877 38.535l10.512-5.788C31.043 119.039 28 107.884 28 96H16Zm80-80c-44.183 0-80 35.817-80 80h12c0-37.555 30.445-68 68-68V16Zm80 80c0-44.183-35.817-80-80-80v12c37.555 0 68 30.445 68 68h12Z"/><path d="M103 130H76V96h27c9.389 0 17 7.611 17 17s-7.611 17-17 17Zm-2-34H76V62h25c9.389 0 17 7.611 17 17s-7.611 17-17 17Z"/></svg>',
  tiktok: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="16" height="16" class="text-icons"><path fill="currentColor" d="M448,209.9a210.1,210.1,0,0,1-122.2-39.1v127 c0,78.5-63.5,142-142,142S42,376.3,42,297.8s63.5-142,142-142 a141,141,0,0,1,21.1,1.6v72.7a70.8,70.8,0,0,0-21.1-3.1 c-39.1,0-70.8,31.7-70.8,70.8s31.7,70.8,70.8,70.8 70.8-31.7,70.8-70.8V0h71.1a140.9,140.9,0,0,0,122.2,209.9Z"/></svg>`,
}

export const getPlatformIcon = (platform) => {
  if (!platform) return '<i class="mdi mdi-link"></i>'

  return iconsMap[platform]
    ? iconsMap[platform].startsWith('<svg')
      ? iconsMap[platform]
      : `<i class="${iconsMap[platform]}"></i>`
    : '<i class="mdi mdi-link"></i>'
}

document.addEventListener('DOMContentLoaded', () => {
  // Selecciona todos los botones que tienen la clase para alternar la visibilidad

  // $(document).ajaxStart(function () {
  //   // Esto se llamará automáticamente cuando la primera petición AJAX inicie
  //   showLoader()
  // })

  // $(document).ajaxStop(function () {
  //   // Esto se llamará automáticamente cuando TODAS las peticiones hayan terminado
  //   hideLoader()
  // })

  const toggleButtons = document.querySelectorAll('.toggle-password-button')

  toggleButtons.forEach((button) => {
    button.addEventListener('click', function () {
      // Obtiene el selector del input desde el data-attribute 'data-target-input'
      const targetInputSelector = this.dataset.targetInput
      if (!targetInputSelector) return // Si no hay data-attribute, no hace nada

      const targetInput = document.querySelector(targetInputSelector)
      const icon = this.querySelector('span.bi') // Busca el ícono dentro del botón

      console.log(targetInput)

      if (targetInput && icon) {
        // Alterna el tipo de input entre 'password' y 'text'
        if (targetInput.type === 'password') {
          targetInput.type = 'text'
          // Cambia el ícono a "ojo tachado" (asumiendo que usas Bootstrap Icons)
          icon.classList.remove('bi-eye')
          icon.classList.add('bi-eye-slash')
        } else {
          targetInput.type = 'password'
          // Cambia el ícono de vuelta a "ojo normal"
          icon.classList.remove('bi-eye-slash')
          icon.classList.add('bi-eye')
        }
      }
    })
  })

  document.addEventListener('input', (e) => {
    if (e.target.classList.contains('number', 'form-control')) {
      e.target.value = e.target.value.replace(/[^0-9\.,]/g, '')
    }
  })
})
