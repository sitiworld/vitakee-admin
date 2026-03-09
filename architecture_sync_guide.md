# Guía de Sincronización Arquitectónica: Vitakee Projects

Este documento detalla los componentes y patrones que se mantienen simétricos entre `vitakee-users` y `vitakee-admin`. Cualquier modificación en uno de estos componentes **debe** replicarse en el otro para mantener la integridad del ecosistema.

## 1. Enrutador (`app/Router.php`)

Ambos proyectos comparten la misma lógica de enrutamiento basada en expresiones regulares.

- **Simetría de Código**: El archivo `Router.php` debe ser idéntico en ambos proyectos.
- **Lógica de Especificidad**: Se utiliza `uksort` con `preg_match_all` para priorizar rutas estáticas sobre rutas con parámetros (`{id}`).
  - _Si modificas el Router en un proyecto, copia el archivo íntegro al otro._

## 2. Ecosistema de Notificaciones

Las notificaciones están diseñadas para funcionar de forma idéntica en el cliente y el panel de administración.

### Backend (PHP)

- **Controladores**: Ambos tienen un `NotificationController` con métodos como `countNewBySession` (para el badge) y `updateNoAlertAdmin/User`.
- **Modelos**: `NotificationModel` y `NotificationPreferenceModel` comparten la misma estructura de base de datos y lógica de filtrado.
- **Servicios**: Los servicios de Push (`NotificationPushService.php`) y Email utilizan las mismas dependencias y lógica de despacho.

### Frontend (JS)

- **Controlador JS**: `public/assets/js/controllers/notificationsController.js` es el motor de la UI. Gestiona el polling del badge, el renderizado de la lista y la suscripción a Push.
- **Service Worker**: `public/sw.js` debe ser el mismo, ya que maneja los eventos de `push` y `notificationclick` de forma genérica.

## 3. Estructura de Inicialización (`index.php`)

Aunque los archivos `index.php` tienen rutas diferentes, su estructura de arranque es simétrica:

- **Constantes**: `BASE_URL`, `APP_ROOT` y `PROJECT_ROOT` se definen de la misma forma dinámica.
- **Carga de Idiomas**: Ambos usan `Language::loadLanguage($lang)` y dependen de la carpeta `app/languages`.
- **Renderizado**: Utilizan el mismo `ViewRenderer.php` para la inyección de traducciones en las vistas.

## 4. Estilos y UI (Partials)

Para mantener una experiencia de usuario coherente, ciertos elementos visuales son espejos:

- **Topbar (`app/views/partials/topbar.php`)**: El dropdown de notificaciones tiene la misma estructura HTML y clases CSS (`.notification-list`, `.notification-item`).
- **Profile Tabs**: La pestaña de "Notificaciones" en el perfil de administrador es una réplica funcional de la del usuario, permitiendo gestionar preferencias de Push y Email.

## 5. Configuración de API (`public/assets/js/apiConfig.js`)

Este archivo es crucial. Contiene las definiciones de `BASE_URL_API` y los endpoints.

- _Asegúrate de que los nombres de los endpoints (ej: `/notifications/count-new`) coincidan en ambos archivos de configuración._

---

> [!IMPORTANT]
> **Regla de Oro**: Antes de dar por finalizada una tarea en el Enrutador o en el Sistema de Notificaciones, verifica que el cambio se haya portado al proyecto hermano.
