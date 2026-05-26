## Why

El admin necesita aprobar o rechazar cada documento de cada usuario, con nota obligatoria en el rechazo. El visor de documento genera presigned URL de S3 en tiempo real para que el admin pueda ver el archivo sin acceso público al bucket.

## What Changes

- **NUEVO** `Controllers/Admin/DocumentController.php` — validate($userId, $docId), view($docId)
- El modal de validación se implementa inline en `views/admin/users/detail.php`

## Capabilities

### New Capabilities

- `document-validation`: approve/reject de documentos con nota mínimo 20 chars; verificación de ownership (doc pertenece al userId); presigned URL para visor; notificación interna + correo al usuario

### Modified Capabilities

(ninguna)

## Impact

- Nuevo: `Admin/DocumentController.php`
- DB: UPDATE `documents` (status, reviewed_by, reviewed_at, rejection_note) — columnas existen (P02)
- Usa: S3Service::presignedUrl (P03), MailService (P09), notification_helper (P21)
- Rutas ya en Routes.php (P07)
