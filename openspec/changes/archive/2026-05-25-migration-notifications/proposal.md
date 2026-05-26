## Why

El sistema tiene bandeja de notificaciones internas con badge contador. Las notificaciones pueden ser automáticas (sistema) o manuales (admin). El campo `sender_id NULL` distingue las automáticas.

## What Changes

- Crear migración CI4 `2025-01-01-000007_CreateNotificationsTable.php`
- `sender_id INT UNSIGNED NULL` — NULL = sistema automático, valor = admin que la envió
- Type ENUM con 6 tipos: info, success, warning, error, document, inscription
- `read_at DATETIME NULL` — NULL = no leída; con valor = leída en esa fecha
- Índice `idx_user_read (user_id, read_at)` para el contador de no leídas

## Capabilities

### New Capabilities
- `create-notifications-table`: Migración de la tabla de notificaciones internas con el ENUM de tipos y el índice para polling de no leídas

### Modified Capabilities

## Impact

- Archivo nuevo: `app/app/Database/Migrations/2025-01-01-000007_CreateNotificationsTable.php`
- Depende de: users (FK user_id y sender_id)
- No tiene otras tablas que dependan de ella — el `down()` es limpio
