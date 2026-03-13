/**
 * Vitakee Service Worker — Web Push Notifications
 *
 * Handles incoming push events and notification click navigation.
 */

// Base del proyecto — se calcula a partir de la ubicación del SW
// sw.js está en /vitakee-admin/sw.js → base = /vitakee-admin
const SW_BASE = self.location.pathname.replace(/\/sw\.js$/, '')

const ICON_URL = SW_BASE + '/public/assets/images/logo-sm2.svg'
const BADGE_URL = SW_BASE + '/public/assets/images/logo-sm2.svg'

// --- Push event: display the notification ---
self.addEventListener('push', function (event) {
  let data = { title: 'Vitakee Admin', body: '', url: '/', icon: ICON_URL }

  if (event.data) {
    try {
      data = { ...data, ...event.data.json() }
    } catch (e) {
      data.body = event.data.text()
    }
  }

  // Construir URL absoluta para el click
  const clickUrl = data.url.startsWith('http')
    ? data.url
    : self.location.origin + SW_BASE + '/' + data.url.replace(/^\//, '')

  const options = {
    body: data.body,
    icon: data.icon || ICON_URL,
    badge: BADGE_URL,
    data: { url: clickUrl },
    vibrate: [100, 50, 100],
    requireInteraction: false,
    tag: 'vitakee-notification',
    renotify: true,
  }

  event.waitUntil(
    self.registration.showNotification(data.title, options).then(function () {
      return clients
        .matchAll({ type: 'window', includeUncontrolled: true })
        .then(function (clientList) {
          clientList.forEach(function (client) {
            client.postMessage({
              type: 'PUSH_RECEIVED',
              payload: data,
            })
          })
        })
    }),
  )
})

// --- Notification click: navigate to the URL ---
self.addEventListener('notificationclick', function (event) {
  event.notification.close()

  const targetUrl =
    event.notification.data?.url ||
    self.location.origin + SW_BASE + '/administrator'

  event.waitUntil(
    clients
      .matchAll({ type: 'window', includeUncontrolled: true })
      .then(function (clientList) {
        const baseUrl = self.location.origin + SW_BASE;
        // Buscar pestaña ya abierta de Vitakee (de esta app particular)
        for (const client of clientList) {
          if ('focus' in client && client.url.startsWith(baseUrl)) {
            client.navigate(targetUrl)
            return client.focus()
          }
        }
        // Si no hay pestaña abierta, abrir una nueva
        if (clients.openWindow) {
          return clients.openWindow(targetUrl)
        }
      }),
  )
})

// --- Activate: claim clients immediately ---
self.addEventListener('activate', function (event) {
  event.waitUntil(clients.claim())
})
