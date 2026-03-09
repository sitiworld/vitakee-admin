# Full Notification Ecosystem Implementation Plan for Administrator

## Goal

Integrate the complete Notification Ecosystem (Email, Browser Web Push, and In-App Polling) into the **vitakee-admin** panel, aligning it perfectly with the architecture used in `vitakee-users`.

## Key Architectural Principles

To maintain parity with `vitakee-users`:

1. **Model-Driven Dispatch:** Web Push and Emails are triggered via `register_shutdown_function` inside `NotificationModel::create()`, ensuring the HTTP response is never blocked.
2. **Preference Driven:** Both systems rely on `user_notification_preferences` (`push_enabled`, `email_enabled`) before dispatching.
3. **Smart Polling:** The frontend `notificationsController.js` will poll the server for new notifications only if `push_enabled` is false, or rely on the Push event to update the UI if `push_enabled` is true.

---

## Proposed Changes

### 1. Database Adjustments

We need to allow administrators to store their subscriptions and preferences in the existing tables by extending the `user_type` enum.

```sql
-- Allow admins in Push Subscriptions
ALTER TABLE `push_subscriptions`
MODIFY COLUMN `user_type` ENUM('user', 'specialist', 'administrator') NOT NULL DEFAULT 'user';

-- Allow admins in Preferences
ALTER TABLE `user_notification_preferences`
MODIFY COLUMN `user_type` ENUM('user', 'specialist', 'administrator') NOT NULL DEFAULT 'user';
```

---

### 2. Backend Files: Models & Services

#### [NEW] app/models/NotificationPreferenceModel.php

- Port from `vitakee-users`.
- Manages `push_enabled` and `email_enabled` preferences, auto-creating defaults for admins if they don't exist.

#### [NEW] app/models/PushSubscriptionModel.php

- Port from `vitakee-users`.
- Handles UPSERT and DELETE of browser push endpoint configurations.

#### [NEW] app/services/NotificationPushService.php

- Port from `vitakee-users`.
- Validates preferences via `NotificationPreferenceModel` and dispatches payloads via VAPID if `push_enabled = 1`.

#### [NEW] app/services/NotificationEmailService.php

- Port from `vitakee-users`.
- Validates preferences and uses `MailHelper` and `NotificationTemplateHelper` to send batched/recent notifications via email if `email_enabled = 1`.

#### [MODIFY] app/models/NotificationModel.php

- In the `create` method, append the non-blocking dispatch logic via `register_shutdown_function`:

```php
// Dispatch Push
NotificationPushService::dispatchIfEnabled($userId, 'administrator', $title, $body, $url);
// Dispatch Email
NotificationEmailService::dispatchIfEnabled($userId, 'administrator', $userEmail, null, $userName, $lang);
```

- Expand `exists` and `create` validation parameters for Admin needs if missing.

#### [NEW] app/helpers/NotificationTemplateHelper.php

- Copy the template renderer to format admin-specific email data cleanly. Ensure the translations file (`EN.php`/`ES.php`) has the keys matching the templates.

#### [NEW] app/controllers/PushSubscriptionController.php

- Expose `POST /push/subscribe` and `POST /push/unsubscribe`.

#### [NEW] app/controllers/NotificationPreferencesController.php

- `GET /notifications/preferences` -> Returns current values.
- `POST /notifications/preferences` -> Updates `push_enabled` / `email_enabled`.

#### [MODIFY] index.php

- Register routes for `/push/*` and `/notifications/preferences`.

---

### 3. Frontend / UI Parity

#### [NEW] public/sw.js

- Port from `vitakee-users`.
- Listens to `push` and `notificationclick` events, focusing an open window or opening a new tab.

#### [MODIFY] public/assets/js/controllers/notificationsController.js

- **Add Push Lifecycle:** `initPushSubscription`, `unsubscribePush`, `checkPushPermission`.
- **Add Polling Logic:** Implement `startPolling` / `stopPolling` checking the server for unread counts every `X` seconds, exactly matching the implementation in `vitakee-users`.
- **Preference Interlock:** Polling is enabled _only_ if `push_enabled = 0`. If Web Push is active, the app relies on the Service Worker `message` event to update the UI counter without aggressive polling.

#### Frontend UI Settings Toggle

Since Administrators don't have a "Preferences" panel view like Users, the toggles for **Enable Push** and **Enable Email** notifications will be appended inside the top-nav Notification dropdown header (the bell icon dropdown) or added as a small settings modal, allowing quick access to toggle them.

---

## Verification Plan

1. **SQL Alterations:** Ensure ENUM updates run cleanly.
2. **Email Test:** Trigger an admin notification -> Verify email arrives in Mailtrap / Inbox.
3. **Push Test:** Toggle Switch -> Accept Browser Prompt -> Verify `web-push` library delivers the VAPID payload natively.
4. **Polling Test:** Disable Push -> Verify the network tab fires polling requests every X seconds. Enabling Push should kill the polling loop.
