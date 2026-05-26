## Context

`inscriptions`: id, user_id, period_id, status ENUM('incomplete','under_review','approved','rejected'), rejection_note, reviewed_by, reviewed_at. `documents`: status ENUM('pending','approved','rejected'). Al aprobar: verificar que NO existe ningún documento del usuario en el periodo con status != 'approved'.

## Decisions

### D1 — Verificación "todos aprobados"
```php
$pending = $db->table('documents')
    ->where('user_id', $userId)
    ->where('period_id', $inscription['period_id'])
    ->where('status !=', 'approved')
    ->countAllResults();
if ($pending > 0) { /* error: faltan documentos por aprobar */ }
```

### D2 — Aprobar: users.status='active'
Solo en aprobación: `$userModel->update($userId, ['status' => 'active'])`.
En rechazo: NO tocar `users.status`.

### D3 — Motivo de rechazo: mínimo 30 chars
Validar server-side.

### D4 — Notificaciones obligatorias
Después de UPDATE: `create_notification($userId, 'inscription', ...)` + `MailService::sendInscriptionResult($user, $inscription)`. Ambas son obligatorias (loggear si fallan, no revertir la inscripción).
