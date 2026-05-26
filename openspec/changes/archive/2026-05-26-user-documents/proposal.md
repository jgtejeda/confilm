## Why

Después del registro inicial, el usuario debe subir los documentos complementarios del periodo. Puede re-subir documentos (el anterior se archiva en S3) y al tenerlos todos enviarlos para revisión del admin.

## What Changes

- **NUEVO** `views/user/documents.php` — grid de slots complementarios dinámicos + botón "Enviar"
- **MODIFICAR** `Controllers/User/DocumentController.php` — agregar index(), upload(), submit() (ya tiene view() de P17)
- **NUEVO** `public/assets/js/document-upload.js` — drag & drop, validación ext client-side

## Capabilities

### New Capabilities

- `complementary-documents`: Vista de documentos complementarios con upload individual por slot, re-subida con S3 archive del anterior, botón submit que verifica todos los slots server-side

### Modified Capabilities

(ninguna)

## Impact

- Modificado: `User/DocumentController.php` (agregar 3 métodos)
- Nuevos: `views/user/documents.php`, `public/assets/js/document-upload.js`
- DB: INSERT `documents`, UPDATE `inscriptions.status='under_review'`
- Usa: S3Service (upload, archive), FileValidator, notification_helper (P21)
- S3 key: `rcf/{period_id}/{user_id}/complementario/{uuid}.{ext}`
