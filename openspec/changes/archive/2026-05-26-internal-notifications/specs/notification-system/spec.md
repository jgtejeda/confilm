## ADDED Requirements

### Requirement: Helper create_notification() para INSERT
`notification_helper.php` SHALL exponer la función `create_notification(int $userId, string $type, string $title, string $body, bool $sendEmail=false): void`. Es una función PHP, no una clase. Hace INSERT en `notifications` con `sender_id=NULL` (sistema automático). Si `$sendEmail=true`: también actualiza `email_sent_at` tras enviar con MailService.

#### Scenario: create_notification inserta en DB
- **WHEN** se llama `create_notification(5, 'document', 'Documento aprobado', 'Tu RFC fue validado')`
- **THEN** se inserta 1 fila en notifications con user_id=5, sender_id=NULL, type='document'

---

### Requirement: unreadCount() retorna JSON para polling
`NotificationController::unreadCount()` SHALL retornar JSON `{"count": N}` donde N es el conteo de notificaciones con `read_at IS NULL` para el usuario logueado.

#### Scenario: unreadCount retorna conteo correcto
- **WHEN** GET /dashboard/notificaciones/count con 3 notificaciones no leídas
- **THEN** retorna `{"count":3}`

---

### Requirement: markRead() con ownership check
`NotificationController::markRead($id)` SHALL verificar que `notifications.user_id = session('user_id')`. Si no coincide: retornar 403. Si OK: UPDATE `read_at=NOW()`.

#### Scenario: Marcar notificación de otro usuario retorna 403
- **WHEN** POST /dashboard/notificaciones/leer con id de notificación de otro usuario
- **THEN** retorna HTTP 403 sin modificar read_at
