import {
  clearNewAlerts,
  clearUserAlerts,
  getAllNotificationsByUser,
  getNewNotificationsByUser,
  updateNoAlertUser,
} from '../apiConfig.js'
import { getBaseUrl } from '../apiConfig.js'

// Intervalo de polling en milisegundos (30 segundos)
const POLLING_INTERVAL_MS = 30_000
let pollingTimer = null
let isPushEnabled = true // Estado local del toggle push

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

  initializeDropdownBehavior()
  setupTabSwitching()
  setupPreferencesPanel() // ← nuevo

  // Cargar preferencias para estado de los switches
  await fetchPreferencesOnce()

  await getNotificationsUser()

  notificationContainer.parentElement.addEventListener(
    'scroll',
    handleInfiniteScroll,
  )
  document.addEventListener('click', handleNotificationClick)

  // Polling del badge SIEMPRE arranca (es funcionalidad in-app, independiente de push)
  startPolling()
}

/**
 * Consulta las preferencias una vez al inicializar.
 * Retorna { push_enabled: 0|1, email_enabled: 0|1 }
 */
async function fetchPreferencesOnce() {
  try {
    const baseUrl = getBaseUrl()
    const res = await fetch(
      `${baseUrl}/notifications/preferences`.replace(/([^:]\/)\/+/g, '$1'),
      { method: 'GET' },
    )
    if (!res.ok) return { push_enabled: 1, email_enabled: 1 }
    const json = await res.json()
    if (json?.value && json.data) return json.data
    return { push_enabled: 1, email_enabled: 1 }
  } catch (_) {
    return { push_enabled: 1, email_enabled: 1 }
  }
}

function setupTabSwitching() {
  const tabsContainer = document.getElementById('notification-tabs')
  if (!tabsContainer) return

  tabsContainer.addEventListener('click', async (event) => {
    const clickedButton = event.target.closest('.notification-button')

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

// ─── Preferencias de notificación ────────────────────────────────────────────

/**
 * Configura el botón de engranaje y los switches de preferencias
 * de notificación (push / email).
 */
function setupPreferencesPanel() {
  const toggleBtn = document.getElementById('notification-pref-toggle')
  const panel = document.getElementById('notification-pref-panel')
  const isProfilePage =
    document.getElementById('v-pills-notifications-tab') ||
    document.getElementById('notifications-tab')

  if (!toggleBtn && !panel && !isProfilePage) return

  // Abrir / cerrar panel
  if (toggleBtn && panel) {
    toggleBtn.addEventListener('click', async (e) => {
      e.stopPropagation()
      const isOpen = panel.style.display !== 'none'
      panel.style.display = isOpen ? 'none' : 'block'
      toggleBtn.setAttribute('aria-expanded', String(!isOpen))

      // Cargar preferencias la primera vez que se abre
      if (!isOpen && !panel.dataset.loaded) {
        await loadPreferences()
        panel.dataset.loaded = '1'
      }
    })
  }

  // Si estamos en una página de perfil (v-pills o myProfileTabs), cargar preferencias inmediatamente
  if (
    document.getElementById('v-pills-notifications-tab') ||
    document.getElementById('notifications-tab')
  ) {
    loadPreferences().then(() => checkPushPermission())
  } else {
    // En el topbar, verificar permiso push al cargar
    checkPushPermission()
  }

  // Guardar al cambiar cada switch (del topbar o del perfil)
  document.addEventListener('change', async (e) => {
    if (e.target.classList.contains('notification-pref-switch')) {
      await savePreferences(e.target)
    }
  })
}

/**
 * Carga las preferencias via GET /notifications/preferences y
 * establece el estado de los checkboxes.
 */
async function loadPreferences() {
  try {
    const baseUrl = getBaseUrl()
    const res = await fetch(
      `${baseUrl}/notifications/preferences`.replace(/([^:]\/)\/+/g, '$1'),
      { method: 'GET' },
    )
    if (!res.ok) return
    const json = await res.json()
    if (!json?.value || !json.data) return

    // Actualizar todos los switches que tengan data-pref correspondiente
    document.querySelectorAll('.notification-pref-switch').forEach((sw) => {
      const pref = sw.getAttribute('data-pref') // 'push_enabled' o 'email_enabled'
      if (pref && json.data.hasOwnProperty(pref)) {
        sw.checked = json.data[pref] === 1
      }
    })

    // Si push_enabled=1 pero no hay suscripción activa en el navegador,
    // suscribir silenciosamente si ya tenemos permiso (sin mostrar pre-prompt).
    if (json.data.push_enabled === 1) {
      await _autoSubscribeIfMissing()
    }
  } catch (e) {
    // silencioso
  }
}

/**
 * Si el permiso del browser ya es 'granted' pero no hay ninguna suscripción
 * push activa, registra el SW y suscribe sin mostrar dialogs.
 * Solo se llama desde loadPreferences().
 */
async function _autoSubscribeIfMissing() {
  if (!('serviceWorker' in navigator) || !('PushManager' in window)) return
  if (Notification.permission !== 'granted') return

  try {
    const swUrl = getBaseUrl().replace(/\/+$/, '') + '/public/sw.js'
    const registration = await navigator.serviceWorker.register(swUrl)
    await navigator.serviceWorker.ready

    const existing = await registration.pushManager.getSubscription()
    if (existing) return // Ya hay suscripción activa → nada que hacer

    // No hay suscripción → crearla silenciosamente (reutilizando subscribePush)
    await subscribePush()
  } catch (_) {
    // silencioso: no interrumpir al usuario
  }
}

/**
 * Guarda las preferencias via POST /notifications/preferences.
 */
async function savePreferences(triggeringSwitch) {
  try {
    const baseUrl = getBaseUrl()

    // 1. Si tenemos un switch que disparó el evento, sincronizamos su estado con los duplicados
    if (triggeringSwitch) {
      const pref = triggeringSwitch.getAttribute('data-pref')
      const isChecked = triggeringSwitch.checked
      document
        .querySelectorAll(`.notification-pref-switch[data-pref="${pref}"]`)
        .forEach((sw) => {
          sw.checked = isChecked
        })
    }

    // 2. Ahora leemos los valores finales (todos sincronizados)
    const pushSwitch = document.querySelector(
      '.notification-pref-switch[data-pref="push_enabled"]',
    )
    const emailSwitch = document.querySelector(
      '.notification-pref-switch[data-pref="email_enabled"]',
    )

    const body = {
      push_enabled: pushSwitch?.checked ? 1 : 0,
      email_enabled: emailSwitch?.checked ? 1 : 0,
    }

    await fetch(
      `${baseUrl}/notifications/preferences`.replace(/([^:]\/)\/+/g, '$1'),
      {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
      },
    )

    // 3. Reaccionar al cambio del toggle push en tiempo real
    onPushToggleChanged(body.push_enabled === 1)
  } catch (e) {
    // silencioso
  }
}

/**
 * Gestiona la suscripción push del navegador cuando el usuario cambia el toggle.
 * Ya NO toca el polling ni el badge (siempre activos como funcionalidad in-app).
 */
async function onPushToggleChanged(enabled) {
  if (enabled) {
    await initPushSubscription()
  } else {
    await unsubscribePush()
  }
}

// ─── Web Push Subscription ───────────────────────────────────────────────────

// VAPID public key — debe coincidir con VAPID_PUBLIC_KEY del .env
const VAPID_PUBLIC_KEY =
  'BKruuLU57ybFlHe77kGmMEmJpftCv23u9e5K4Bl2ciFP4Zw5ExBwWVDScGX_0KMma-FdUapdv_Xwoh4x5lqCdIA'

/**
 * Inicia la suscripción push del navegador.
 * Flujo: pre-prompt SweetAlert → permiso navegador → registrar SW → suscribir → enviar al backend
 */
async function initPushSubscription() {
  // Verificar soporte del navegador
  if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
    showPushToast(
      language?.push_not_supported || 'Browser does not support push.',
      'warning',
    )
    revertPushToggle(false)
    return
  }

  // Si ya está denied, no mostrar pre-prompt
  if (Notification.permission === 'denied') {
    showPushToast(
      language?.push_permission_denied || 'Notifications blocked by browser.',
      'warning',
    )
    revertPushToggle(false)
    return
  }

  // Si ya está granted, saltar pre-prompt
  if (Notification.permission === 'granted') {
    await subscribePush()
    return
  }

  // Pre-prompt SweetAlert
  const result = await Swal.fire({
    title: language?.push_pre_prompt_title || 'Enable push notifications?',
    text:
      language?.push_pre_prompt_text ||
      'Receive alerts even when Vitakee is closed.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: language?.push_pre_prompt_confirm || 'Enable',
    cancelButtonText: language?.push_pre_prompt_cancel || 'Not now',
    customClass: {
      confirmButton: 'btn btn-primary',
      cancelButton: 'btn btn-secondary',
    },
  })

  if (!result.isConfirmed) {
    revertPushToggle(false)
    return
  }

  // Solicitar permiso real del navegador
  const permission = await Notification.requestPermission()

  if (permission === 'granted') {
    await subscribePush()
  } else {
    showPushToast(
      permission === 'denied'
        ? language?.push_permission_denied || 'Notifications blocked.'
        : language?.push_pre_prompt_cancel || 'Not now',
      'warning',
    )
    revertPushToggle(false)
  }
}

/**
 * Registra el Service Worker, suscribe al push manager, y envía la suscripción al backend.
 */
async function subscribePush() {
  try {
    const baseUrl = getBaseUrl()
    const swUrl = baseUrl.replace(/\/+$/, '') + '/public/sw.js'

    const registration = await navigator.serviceWorker.register(swUrl)
    await navigator.serviceWorker.ready

    const subscription = await registration.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY),
    })

    const subJson = subscription.toJSON()

    await fetch(`${baseUrl}/push/subscribe`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        endpoint: subJson.endpoint,
        keys: {
          p256dh: subJson.keys.p256dh,
          auth: subJson.keys.auth,
        },
      }),
    })

    showPushToast(
      language?.push_enabled_toast || 'Push notifications enabled',
      'success',
    )
  } catch (err) {
    console.error('[Push] Subscribe error:', err)
    showPushToast(
      language?.push_not_supported || 'Could not enable push.',
      'error',
    )
    revertPushToggle(false)
  }
}

/**
 * Desuscribe del push manager y notifica al backend.
 */
async function unsubscribePush() {
  try {
    if (!('serviceWorker' in navigator)) return

    const swUrl = getBaseUrl().replace(/\/+$/, '') + '/public/sw.js'
    const registration = await navigator.serviceWorker.getRegistration(swUrl) // buscar por URL absoluta
    if (!registration) return

    const subscription = await registration.pushManager.getSubscription()
    if (!subscription) return

    const endpoint = subscription.endpoint
    await subscription.unsubscribe()

    const baseUrl = getBaseUrl()
    await fetch(`${baseUrl}/push/unsubscribe`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ endpoint }),
    })

    showPushToast(
      language?.push_disabled_toast || 'Push notifications disabled',
      'info',
    )
  } catch (err) {
    console.error('[Push] Unsubscribe error:', err)
  }
}

/**
 * Verifica el permiso del navegador y actualiza el toggle push si está denied.
 */
function checkPushPermission() {
  if (!('Notification' in window)) return

  if (Notification.permission === 'denied') {
    document
      .querySelectorAll('.notification-pref-switch[data-pref="push_enabled"]')
      .forEach((sw) => {
        sw.checked = false
        sw.disabled = true

        // Buscar el contenedor padre inmediato (.d-flex o similar) que agrupa el renglón del switch
        const row = sw.closest('.d-flex')
        if (!row) return
        // El hint va DESPUÉS del renglón, no dentro
        const container = row.parentElement
        if (container && !container.querySelector('.push-denied-hint')) {
          const hint = document.createElement('small')
          hint.className = 'text-danger push-denied-hint d-block mt-1 px-1'
          hint.textContent =
            language?.push_permission_denied || 'Blocked by browser.'
          // Insertar justo después del renglón .d-flex
          row.insertAdjacentElement('afterend', hint)
        }
      })
  }
}

// ─── Push Helpers ────────────────────────────────────────────────────────────

/** Convierte una base64url string a Uint8Array (requerido por pushManager.subscribe) */
function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - (base64String.length % 4)) % 4)
  const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/')
  const rawData = atob(base64)
  const outputArray = new Uint8Array(rawData.length)
  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i)
  }
  return outputArray
}

/** Muestra un toast rápido usando SweetAlert */
function showPushToast(message, icon = 'info') {
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: icon,
      title: message,
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
    })
  }
}

/** Revierte el toggle push a un estado dado sin disparar savePreferences */
function revertPushToggle(checked) {
  document
    .querySelectorAll('.notification-pref-switch[data-pref="push_enabled"]')
    .forEach((sw) => {
      sw.checked = checked
    })
}

/** Helper para marcar/desmarcar un checkbox por id. */
function setSwitch(id, checked) {
  const el = document.getElementById(id)
  if (el) el.checked = checked
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
    refreshBadgeCount()
    reloadAlerts() // Refresca la lista al abrir el dropdown
  })
}

// --- Polling de badge (Page Visibility API) ---

function startPolling() {
  // Llamada inmediata al cargar, sin esperar el primer tick
  refreshBadgeCount()

  pollingTimer = setInterval(() => {
    // Solo hace la llamada si la pestaña está visible
    if (document.visibilityState === 'visible') {
      refreshBadgeCount()
    }
  }, POLLING_INTERVAL_MS)

  // Pausa/reanuda según visibilidad de la pestaña
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
      // Refresca inmediatamente al volver a la pestaña
      refreshBadgeCount()
    }
  })

  // Escuchar mensajes del Service Worker para sincronización instantánea
  listenForPushMessages()
}

/**
 * Escucha mensajes del Service Worker.
 * Cuando llega un PUSH_RECEIVED, refresca badge y lista al instante
 * en lugar de esperar el próximo tick del polling (30s).
 */
function listenForPushMessages() {
  if (!('serviceWorker' in navigator)) return

  navigator.serviceWorker.addEventListener('message', (event) => {
    if (event.data?.type === 'PUSH_RECEIVED') {
      // Actualizar badge inmediatamente
      refreshBadgeCount()

      // Si el dropdown está abierto, recargar la lista también
      const dropdown = document.getElementById('notificationDropdown')
      if (dropdown?.classList.contains('show')) {
        reloadAlerts()
      }
    }
  })
}

/**
 * Consulta solo el conteo de notificaciones "new" y actualiza el badge.
 * Usa un endpoint liviano para no recargar toda la lista.
 */
async function refreshBadgeCount() {
  try {
    const baseUrl = getBaseUrl()
    const res = await fetch(
      `${baseUrl}/notifications/count-new`.replace(/([^:]\/)\/+/g, '$1'),
      { method: 'GET' },
    )
    if (!res.ok) return
    const json = await res.json()
    if (json?.value && json.data !== undefined) {
      const alertCount = document.getElementById('alert-count')
      if (alertCount) alertCount.textContent = json.data
    }
  } catch (_) {
    // Silencioso — no interrumpir la UX si falla el polling
  }
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
      paginationState.notificationsPerPage,
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
          response.unread_count,
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
  unreadCount = 0,
) {
  const container = document.getElementById('alerts-container')
  if (!append) {
    container.innerHTML = ''
  }

  const newNotificationsHTML = notifications
    .map((n) => createUserAlertItem(n))
    .join('')
  container.insertAdjacentHTML('beforeend', newNotificationsHTML)

  // El badge de la campana NO se toca aquí para no fluctuar entre tabs.
  // Solo actualizamos el contador interno del tab "Unread".
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
              alert.template_key,
            )}'>
              <i class='mdi ${validateNotificationIcon(
                alert.template_key,
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
