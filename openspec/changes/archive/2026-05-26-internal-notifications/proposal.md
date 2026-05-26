## Why

El sistema necesita notificaciones internas para comunicar al usuario cada evento importante (doc aprobado, inscripción rechazada, mensajes del admin). El helper centraliza el INSERT y el Controller expone los endpoints que el polling del dashboard consume.

## What Changes

- **NUEVO** `app/app/Helpers/notification_helper.php` — función `create_notification()`
- **NUEVO** `Controllers/User/NotificationController.php` — index(), markRead(), unreadCount()
- **NUEVO** `views/user/notifications.php` — bandeja de notificaciones con fecha relativa

## Capabilities

### New Capabilities

- `notification-system`: Helper create_notification para INSERT; Controller con lista paginada, marcar leída (ownership check), count para polling; vista con fecha relativa JS

### Modified Capabilities

(ninguna)

## Impact

- Nuevos: `Helpers/notification_helper.php`, `User/NotificationController.php`, `views/user/notifications.php`
- DB: INSERT/UPDATE en `notifications` — tabla y model existen (P02)
- Cargar helper en BaseController: `$this->helpers = ['notification']`
- Rutas ya en Routes.php (P07): /dashboard/notificaciones*
