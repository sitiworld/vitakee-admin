import {
  clearNewAlerts,
  clearUserAlerts,
  getAllNotificationsByUser,
  getNewNotificationsByUser,
  updateNoAlertUser,
} from '../apiConfig.js'

// --- ESTADO CENTRALIZADO DE PAGINACIÓN ---
const paginationState = {
  currentPage: 1,
  notificationsPerPage: 10,
  isLoading: false,
  hasMore: true,
  activeApiFunction: getNewNotificationsByUser,
}

dayjs.extend(window.dayjs_plugin_relativeTime)

/**
 * Inicializa los listeners y carga las primeras notificaciones de usuario.
 */
export const validateNotifications = async () => {
  const notificationContainer = document.getElementById('alerts-container')
  if (!notificationContainer) return

  const rol = notificationContainer.getAttribute('data-rol')

  console.log(rol)

  initializeDropdownBehavior()

  // La lógica solo se aplica a usuarios con rol 1

  setupTabSwitching()
  await getNotificationsUser()

  notificationContainer.parentElement.addEventListener(
    'scroll',
    handleInfiniteScroll
  )
  document.addEventListener('click', handleNotificationClick)
}

function setupTabSwitching() {
  const tabsContainer = document.getElementById('notification-tabs')
  if (!tabsContainer) return

  tabsContainer.addEventListener('click', async (event) => {
    const clickedButton = event.target.closest('.notification-button')
    console.log('click tab', clickedButton.id)

    if (clickedButton || !clickedButton.classList.contains('active')) {
    } else {
      return
    }

    // Actualizar la función de API activa en el estado
    if (clickedButton.id === 'all-tab') {
      paginationState.activeApiFunction = getAllNotificationsByUser
    } else if (clickedButton.id === 'unread-tab') {
      paginationState.activeApiFunction = getNewNotificationsByUser
    }

    // Actualizar el estado visual de los botones
    tabsContainer.querySelectorAll('.notification-button').forEach((btn) => {
      btn.classList.remove('active')
    })
    clickedButton.classList.add('active')

    // Recargar las notificaciones para el nuevo tab
    await reloadAlerts()
  })
}

function initializeDropdownBehavior() {
  const notificationDropdown = document.querySelector('li.notification-list')
  const notificationDropdownContainer =
    document.getElementById('dropdown-container')
  if (!notificationDropdown) return

  notificationDropdown.addEventListener('hide.bs.dropdown', function (event) {
    const focusedElement = document.activeElement

    if (
      focusedElement &&
      notificationDropdownContainer.contains(focusedElement)
    ) {
      // PERO, si es un enlace de navegación, deja que se cierre.
      if (focusedElement.closest('.notification-item[data-url]')) {
        return
      }
      // Para cualquier otro clic interno, previene el cierre.
      event.preventDefault()
      return
    }
  })

  notificationDropdown.addEventListener('shown.bs.dropdown', function (event) {
    clearNewAlerts()
    const alertCount = document.getElementById('alert-count')
    alertCount.textContent = 0
  })
}

/**
 * Recarga las notificaciones desde el principio.
 */
export const reloadAlerts = async () => {
  const notificationContainer = document.getElementById('alerts-container')
  resetPaginationState()
  showLoader(notificationContainer) // Muestra el loader inmediatamente
  await getNotificationsUser()
}

/**
 * Función que se dispara con el evento de scroll para cargar más notificaciones.
 */
async function handleInfiniteScroll(e) {
  const container = e.target
  const scrollThreshold = 100 // Píxeles antes del final para empezar a cargar

  // Condición para cargar más
  if (
    container.scrollTop + container.clientHeight >=
      container.scrollHeight - scrollThreshold &&
    !paginationState.isLoading &&
    paginationState.hasMore
  ) {
    paginationState.currentPage++
    await getNotificationsUser(true) // `true` para añadir al contenido existente
  }
}

async function handleNotificationClick(e) {
  const alertCount = document.getElementById('alert-count')
  const unreadCount = document.getElementById('unread-count')

  // --- Caso 1: Limpiar todas las notificaciones ---
  const clearAllButton = e.target.closest('#clear-all-alerts')
  if (clearAllButton) {
    const res = await clearUserAlerts()
    if (res.value) {
      showEmptyMessage()
      alertCount.textContent = '0'
      unreadCount.textContent = '0'
      reloadAlerts()
      resetPaginationState()
    }
    return
  }

  // --- Caso 2: Click en un item para navegar ---
  const notificationItem = e.target.closest('.dropdown-item[data-url]')
  if (notificationItem) {
    e.preventDefault()
    const url = notificationItem.getAttribute('data-url')
    const id = notificationItem.getAttribute('data-notification-id')
    if (url) {
      await updateNoAlertUser(id)
      window.location.href = url
    }
  }
}

async function getNotificationsUser(append = false) {
  if (paginationState.isLoading) return

  paginationState.isLoading = true
  try {
    const response = await paginationState.activeApiFunction(
      paginationState.currentPage,
      paginationState.notificationsPerPage
    )

    if (response?.value && Array.isArray(response.data)) {
      if (response.data.length === 0) {
        paginationState.hasMore = false
      }
      if (paginationState.currentPage === 1 && response.data.length === 0) {
        showEmptyMessage()
        document.getElementById('alert-count').textContent = '0'
        const unreadCountSpan = document.getElementById('unread-count')
        if (unreadCountSpan) unreadCountSpan.textContent = '0'
      } else {
        // --- MODIFICADO ---: Ya no pasamos response.labels
        renderAlerts(
          response.data,
          append,
          response.count,
          response.unread_count
        )
      }
    } else {
      paginationState.hasMore = false
      if (paginationState.currentPage === 1) showErrorMessage()
    }
  } catch (error) {
    console.error('Error fetching notifications:', error)
    paginationState.hasMore = false
    if (paginationState.currentPage === 1) showErrorMessage()
  } finally {
    paginationState.isLoading = false
  }
}

/**
 * --- MODIFICADO ---: Firma de la función actualizada, 'labels' eliminado.
 */
function renderAlerts(
  notifications,
  append = false,
  totalCount = 0,
  unreadCount = 0
) {
  const container = document.getElementById('alerts-container')
  if (!append) {
    container.innerHTML = ''
  }

  // --- MODIFICADO ---: Ya no pasamos 'labels' a createUserAlertItem
  const newNotificationsHTML = notifications
    .map((n) => createUserAlertItem(n))
    .join('')
  container.insertAdjacentHTML('beforeend', newNotificationsHTML)

  // Actualiza el contador global de la campana
  document.getElementById('alert-count').textContent = totalCount

  // Actualiza el contador específico del tab "Unread"
  const unreadCountSpan = document.getElementById('unread-count')
  if (unreadCountSpan) {
    unreadCountSpan.textContent = unreadCount || 0
  }
}

/**
 * Muestra el mensaje de "No hay notificaciones" en el contenedor.
 */
function showEmptyMessage() {
  const message =
    language?.no_notifications_avaliable || 'No hay notificaciones disponibles'

  document.getElementById('alerts-container').innerHTML = `
    <div class="text-center p-4 d-flex flex-column align-items-center justify-content-center h-100">
      <i class="mdi mdi-bell-ring-outline empty-notification-icon font-36"></i>
      <p class="text-muted mt-3">${message}</p>
    </div>`
}

/**
 * Muestra un mensaje de error en el contenedor.
 */
function showErrorMessage() {
  document.getElementById('alerts-container').innerHTML = `
      <div class="text-center p-3">
          <p class="text-danger">${language.notification_error_load}</p>
      </div>`
  document.getElementById('alert-count').textContent = '0'
}

/**
 * Resetea el estado de paginación a sus valores iniciales.
 */
function resetPaginationState() {
  paginationState.currentPage = 1
  paginationState.isLoading = false
  paginationState.hasMore = true
}

// --- NUEVA FUNCIÓN AUXILIAR ---
/**
 * Reemplaza los placeholders de una plantilla (ej: {{name}}) con valores de un objeto.
 */
function replaceTemplateParams(templateString, params) {
  if (!templateString) return ''
  return templateString.replace(/\{\{([a-zA-Z0-9_]+)\}\}/g, (match, key) => {
    return params.hasOwnProperty(key) ? params[key] : match
  })
}

// --- FUNCIONES AUXILIARES (Modificada) ---

/**
 * --- MODIFICADO ---: Firma de la función actualizada, 'labels' eliminado.
 */
function createUserAlertItem(alert) {
  // --- Lógica de URL simplificada ---
  const url = alert.route || '#'

  // --- Parámetros y estado ---
  const params = alert.template_params || {}
  const status = (params.status || '').toLowerCase() // ej: 'low'

  // --- Estado de lectura ---
  const readStatusClass = alert.read_unread === 0 ? 'notification-unread' : ''
  const unreadDotHTML =
    alert.read_unread === 0
      ? '<span class="badge badge-unread rounded-pill bg-primary-app ms-2"></span>'
      : ''

  // --- Obtener plantillas de idioma ---
  const titleKey = alert.template_key + '_title'
  const descKey = alert.template_key + '_desc'

  const titleTemplate =
    language[titleKey] || params.biomarker_name || 'Notification'
  const descTemplate = language[descKey] || ''

  // --- Reemplazar parámetros en las plantillas ---
  const title = replaceTemplateParams(titleTemplate, params)
  const description = replaceTemplateParams(descTemplate, params)

  // --- Formatear fecha ---
  const date = dayjs(alert.created_at).format('MM/DD/YYYY')

  // --- Nombre del módulo ---
  const moduleName = alert.module || ''

  // --- Plantilla HTML actualizada ---
  return `
    <a href='#' class='dropdown-item p-0 notify-item card shadow-none mb-1 ${readStatusClass} '
      data-url='${url}' data-notification-id='${
    alert.notifications_id
  }' title="${description.replace(/"/g, '&quot;')}">
      <div class='card-body'>
        
        <div class='d-flex align-items-center'>
          <div class='flex-shrink-0'>
            <div class='notify-icon ${validateNotificationColor(
              alert.template_key
            )}'>
              <i class='mdi ${validateNotificationIcon(
                alert.template_key
              )}'></i>
            </div>
          </div>
          <div class='flex-grow-1 text-wrap ms-2'>
            <h6 class='noti-item-title my-0 text-wrap fw-semibold'>
              ${title} 
            </h6>
            <div class="d-flex align-items-center mb-1">
            <small class='d-block noti-item-subtitle text-muted text-wrap flex-grow-1'>
              ${description}
            </small>

            ${unreadDotHTML}
            </div>
            
            <small class='d-block fw-normal text-muted mt-1'>
              ${language.date || 'Date'}: ${date} 
            </small>
           
           
          </div>
        </div>
      </div>
    </a>`
}

const validateNotificationIcon = (template_key) => {
  console.log(template_key)

  template_key = template_key ? template_key.toLowerCase() : ''
  if (
    template_key === 'biomarker_out_of_range' ||
    template_key === 'renal_urine_result_abnormal'
  )
    return 'mdi-alert'
  if (template_key === 'second_opinion_status_changed')
    return 'mdi-message-reply-text-outline'
  if (template_key === 'new_specialist_review') return 'mdi-star-outline'
  if (template_key === 'second_opinion_request_received') return 'mdi-bell-ring'
  if (template_key === 'second_opinion_cancelled_by_user')
    return 'bi-slash-circle'
  return 'mdi-bell-outline'
}

const validateNotificationColor = (template_key) => {
  template_key = template_key ? template_key.toLowerCase() : ''
  if (template_key === 'second_opinion_status_changed')
    return 'bg-primary-app text-white'
  if (
    template_key === 'biomarker_out_of_range' ||
    template_key === 'renal_urine_result_abnormal'
  )
    return 'red-item'
  if (template_key === 'new_specialist_review') return 'yellow-item font-14'
  if (template_key === 'second_opinion_request_received')
    return 'bg-primary-app text-white'
  if (template_key === 'second_opinion_cancelled_by_user')
    return 'bg-bright-turquoise text-white'
  return 'bg-secondary'
}

function showLoader(container) {
  if (!container) return
  container.innerHTML = `
    <div class="text-center p-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">${language.loading_helper}...</span>
      </div>
    </div>`
}
