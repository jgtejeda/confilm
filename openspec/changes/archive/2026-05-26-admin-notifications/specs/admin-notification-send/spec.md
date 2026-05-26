## ADDED Requirements

### Requirement: Envío de notificación a usuario o grupo
`Admin\NotificationController::send()` SHALL recibir: target_type ('user'|'group'), target_id (si user) o target_status (si group), title, body, send_email (bool). Para cada destinatario: INSERT en notifications con `sender_id=session('user_id')`. Si send_email=true: llamar MailService::sendAdminMessage — si falla: loggear y continuar.

#### Scenario: Envío a usuario individual crea 1 notificación
- **WHEN** admin envía notificación con target_type='user' y target_id=5
- **THEN** se inserta 1 fila en notifications con user_id=5 y sender_id=session('user_id')

#### Scenario: Envío a grupo 'pending' crea N notificaciones
- **WHEN** admin envía con target_type='group' y target_status='pending' con 10 usuarios pending
- **THEN** se insertan 10 filas en notifications, una por usuario

#### Scenario: sender_id nunca es NULL en notificaciones del admin
- **WHEN** admin envía cualquier notificación desde el panel
- **THEN** `notifications.sender_id` = session('user_id') del admin — nunca NULL
