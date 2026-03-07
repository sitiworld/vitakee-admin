# Push Notifications Implementation Plan for Administrator

## Goal

Integrate the Browser Web Push Notifications system into the **vitakee-admin** panel. This will allow administrators to receive native push notifications on their browsers (even when the tab is in the background) in exactly the same way as users and specialists do.

## User Preference Note

The switch/button to "Enable Push Notifications" will be temporarily placed inside the Notification Dropdown (bell icon) but can be relocated to a main configuration panel later if needed. Email notifications are currently out of scope for administrators but can be added later following the `NotificationEmailService` pattern.

---

## Proposed Changes

### 1. Database Adjustment

Allow administrators to store their subscriptions in the existing table.

```sql
ALTER TABLE `push_subscriptions`
MODIFY COLUMN `user_type` ENUM('user', 'specialist', 'administrator') NOT NULL DEFAULT 'user';
```

### 2. Backend Files

#### [NEW] app/models/PushSubscriptionModel.php

- Port the entire file from `vitakee-users/app/models/PushSubscriptionModel.php`.
- Handles UPSERT and DELETE of push endpoint configurations.

#### [NEW] app/services/NotificationPushService.php

- Port the entire file from `vitakee-users/app/services/NotificationPushService.php`.
- Integrate `minishlink/web-push` to dispatch payloads via the VAPID Protocol.
- _Adjust:_ Since there is no `NotificationPreferenceModel` for admins, remove the check or create a unified preference model.

#### [NEW] app/controllers/PushSubscriptionController.php

- Port the entire file from `vitakee-users/app/controllers/PushSubscriptionController.php`.
- Provides API endpoints `POST /push/subscribe` and `POST /push/unsubscribe`.

#### [MODIFY] index.php

- Register routes for `/push/subscribe` and `/push/unsubscribe` inside the `AuthMiddleware` group.

#### [MODIFY] app/models/NotificationModel.php

- In the `create` method, append the non-blocking push dispatch logic `NotificationPushService::dispatchIfEnabled()` via `register_shutdown_function`.
- This guarantees that whenever `vitakee-admin` emits a notification internally, it automatically triggers a Web Push.

### 3. Frontend / UI

#### [NEW] public/sw.js

- Port the Service Worker from `vitakee-users/public/sw.js`.
- Listens to `push` and `notificationclick` events, focusing an open window or opening a new one when clicking a notification.

#### [MODIFY] public/assets/js/controllers/notificationsController.js

- Port subscription lifecycle logic (`initPushSubscription`, `unsubscribePush`, `checkPushPermission`) from `vitakee-users`.
- Mount a toggle inside the notifications dropdown UI to let the admin opt-in/opt-out of push notifications dynamically.

## Environment Note

- Ensure the `VAPID_PUBLIC_KEY` and `VAPID_PRIVATE_KEY` are present in the `.env` file of `vitakee-admin`.
