## Why

La aprobación de inscripción es la decisión final del proceso. Solo puede aprobarse si todos los documentos del periodo están aprobados. El rechazo requiere un motivo mínimo de 30 chars.

## What Changes

- **NUEVO** método `validateInscription($userId)` en `Admin\UserController.php` (completar el stub de P13)

## Capabilities

### New Capabilities

- `inscription-validation`: Aprobar (verificando todos los docs aprobados) o rechazar inscripción con motivo; UPDATE users.status y inscriptions.status; notificación + correo obligatorios

### Modified Capabilities

(ninguna)

## Impact

- Modificado: `Admin/UserController.php` (agregar validateInscription completo)
- DB: UPDATE `inscriptions` (status, rejection_note, reviewed_by, reviewed_at), UPDATE `users.status` solo en aprobación
- Usa: MailService (P09), notification_helper (P21)
