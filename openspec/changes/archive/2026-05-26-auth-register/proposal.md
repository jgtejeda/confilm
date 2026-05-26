## Why

El registro de usuario es el punto de entrada al sistema. Requiere cargar dinámicamente los tipos de documento del periodo activo (configurados por el admin), validar los archivos con FileValidator, subirlos a S3 y crear usuario + documentos + inscripción en una sola transacción atómica. Sin esto el sistema no puede recibir nuevos usuarios.

## What Changes

- **MODIFICAR** `app/app/Controllers/Auth/RegisterController.php` — implementar `index()` con query de periodo activo y `process()` con validación, S3 upload y transacción DB (actualmente solo stub de index())
- **MODIFICAR** `app/app/Views/auth/register.php` — agregar slots dinámicos de documentos iniciales con `foreach($docTypes)` y validación JS en tiempo real (actualmente tiene solo campos personales de P04)
- **NUEVO** `app/app/Views/auth/no_period.php` — vista cuando no hay periodo activo
- **NUEVO** `app/app/Libraries/UsernameGenerator.php` — genera username con anti-colisión
- **NUEVO** `app/app/Libraries/PasswordGenerator.php` — genera password segura de 12 chars

## Capabilities

### New Capabilities

- `user-registration`: Flujo completo de registro: verificar periodo activo, validar datos personales, validar y subir documentos a S3, insertar usuario+documentos+inscripción en transacción atómica, enviar correos
- `dynamic-document-slots`: Formulario de registro que genera slots de documentos dinámicamente desde `period_document_types JOIN document_types` del periodo activo
- `s3-upload-transaction`: Transacción DB + S3 con rollback: si falla la DB después de subir archivos, eliminar de S3; si falla S3, no insertar en DB

### Modified Capabilities

- `auth-register-view`: Los campos de documentos dinámicos se agregan a la vista de registro (stub creado en P04)

## Impact

- Archivos nuevos: `Libraries/UsernameGenerator.php`, `Libraries/PasswordGenerator.php`, `Views/auth/no_period.php`
- Archivos modificados: `Controllers/Auth/RegisterController.php`, `Views/auth/register.php`
- Usa: `UserModel`, `DocumentModel`, `InscriptionModel`, `PeriodModel`, `DocumentTypeModel` (todos existen de P02), `S3Service`, `FileValidator` (existen de P03)
- Rutas: POST /registro (agregar a Routes.php)
- DB: INSERT en `users`, `documents` (3+ filas), `inscriptions` — dentro de transacción
- S3: upload a `rcf/{period_id}/{user_id}/inicial/{uuid}.{ext}`
- Sin cambios en DB schema ni migraciones
