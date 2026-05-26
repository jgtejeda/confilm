## Context

`notifications` columnas: id, user_id, sender_id INT NULL (NULL=sistema, admin=session user_id), type ENUM('info','success','warning','error','document','inscription'), title, body, read_at, send_email, email_sent_at. NotificationModel ya existe.

## Decisions

### D1 — Envío masivo: iterar solo IDs
```php
// NO cargar objetos completos
$userIds = $db->table('users')->select('id,email,nombres')->where('status',$targetStatus)->get()->getResultArray();
foreach ($userIds as $u) {
    INSERT notifications (user_id, sender_id=session('user_id'), type='info', title, body, send_email);
    if ($sendEmail) MailService::sendAdminMessage($u, $subject, $body);
}
```

### D2 — sender_id del admin
`'sender_id' => session('user_id')` — NO NULL (NULL es solo para notificaciones automáticas del sistema).

### D3 — target_type: 'user' o 'group'
Si 'user': target_id = un user específico. Si 'group': target_status = 'pending'|'active'|'rejected'|'suspended'.
