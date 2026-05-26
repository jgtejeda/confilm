## Context

Las notificaciones internas se muestran en la bandeja del usuario con badge contador actualizado por polling cada 30s. El índice `idx_user_read (user_id, read_at)` optimiza la query `SELECT COUNT(*) WHERE user_id=? AND read_at IS NULL`.

## Goals / Non-Goals

**Goals:**
- Tabla con `sender_id NULL` (sistema automático) o con valor (admin manual)
- Type ENUM: info, success, warning, error, document, inscription
- `read_at DATETIME NULL` — patrón estándar para "leído/no leído"
- `send_email TINYINT(1) DEFAULT 0` — indica si además se envió correo
- Índice compuesto `idx_user_read` en `(user_id, read_at)` para el polling de no leídas

**Non-Goals:**
- No la lógica de envío ni el badge (va en NotificationController y notifications.js)
- No el notification_helper (Propuesta 21)

## Decisions

**`read_at DATETIME NULL` en lugar de booleano `is_read`**
→ Registrar la fecha exacta de lectura es más informativo y permite queries temporales (ej: "notificaciones leídas en los últimos 7 días"). El patrón NULL/fecha es estándar.

**`sender_id` sin ON DELETE** (RESTRICT implícito)
→ Si un admin es eliminado mientras tiene notificaciones enviadas, MySQL bloqueará la eliminación. El admin debe gestionar sus notificaciones antes de ser eliminado. Alternativa: SET NULL — depende del negocio.

**Índice compuesto `(user_id, read_at)`**
→ La query de badge es exactamente `WHERE user_id=? AND read_at IS NULL`. El índice compuesto evita full scan por usuario.

## Risks / Trade-offs

- [Trade-off] `sender_id` sin CASCADE puede bloquear la eliminación de admins con notificaciones históricas.
  → Aceptable: los admins raramente se eliminan. Si se necesita, se puede hacer UPDATE SET sender_id=NULL antes.
