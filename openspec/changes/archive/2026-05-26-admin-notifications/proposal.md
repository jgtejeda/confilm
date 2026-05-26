## Why

El admin necesita enviar mensajes internos (y opcionalmente correo) a usuarios individuales o grupos completos (por status). Esto permite comunicación masiva sin acceder a correo externo.

## What Changes

- **NUEVO** `Controllers/Admin/NotificationController.php` — send(), index()
- **NUEVO** `views/admin/notifications.php` — formulario de envío + historial

## Capabilities

### New Capabilities

- `admin-notification-send`: Envío a usuario individual o grupo por status; INSERT en notifications por cada destinatario; correo opcional; sender_id del admin logueado

### Modified Capabilities

(ninguna)

## Impact

- Nuevos: `Admin/NotificationController.php`, `views/admin/notifications.php`
- DB: INSERT en `notifications` — tabla existe (P02), NotificationModel existe (P02)
- Rutas ya en Routes.php (P07)
