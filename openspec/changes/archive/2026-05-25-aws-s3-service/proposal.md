## Why

Todo archivo subido por usuarios debe ir a AWS S3 — no al servidor local (no existe `writable/uploads/`). Esta propuesta crea los servicios centrales de almacenamiento y validación de archivos que serán consumidos por el registro, el dashboard de usuario y el panel admin a partir de la Propuesta 05 en adelante.

## What Changes

- **NUEVO** `app/app/Config/AWS.php` — Configuración del cliente S3 (region, bucket, key, secret leídos del `.env`)
- **NUEVO** `app/app/Libraries/S3Service.php` — Library con métodos `upload`, `presignedUrl`, `archive` y `delete`; nunca lanza excepción al caller
- **NUEVO** `app/app/Libraries/FileValidator.php` — Validación de tipo, tamaño y magic bytes para los 6 formatos soportados: `pdf`, `docx`, `xlsx`, `pptx`, `jpg`, `png`

No se modifican migraciones, modelos, rutas ni vistas existentes. La tabla `documents` ya tiene las columnas `s3_key`, `s3_bucket`, `file_extension`, `period_id`, `original_name` y `stored_name` listas para ser usadas.

## Capabilities

### New Capabilities

- `s3-file-storage`: Subida, recuperación (presigned URL), archivado y eliminación de archivos en AWS S3 con el key `rcf/{period_id}/{user_id}/{categoria}/{uuid}.{ext}`
- `file-type-validation`: Validación de extensión, tamaño y magic bytes para los 6 tipos de archivo admitidos por el sistema; rechaza archivos maliciosos renombrados

### Modified Capabilities

_(ninguna — no hay cambios en specs existentes)_

## Impact

- **Archivos nuevos**: `app/app/Config/AWS.php`, `app/app/Libraries/S3Service.php`, `app/app/Libraries/FileValidator.php`
- **Dependencias PHP**: `aws/aws-sdk-php ^3.0` — ya instalado en `vendor/` vía Composer; no se toca `composer.json`
- **Variables de entorno**: `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_REGION`, `AWS_S3_BUCKET` — ya presentes en `.env`
- **Consumidores futuros**: `RegisterController::process()` (P05), `User\DocumentController::upload()` (P20), `Admin\DocumentController::validate()` (P14), `Admin\DocumentController::view()` (P14), `document-viewer.js` vía endpoint de presigned URL (P17)
- **Sin cambios en DB**: la tabla `documents` ya tiene todas las columnas necesarias
- **Sin cambios en Docker**: el contenedor `rcf_app` ya tiene las extensiones PHP necesarias (zip, pdo_mysql, gd, intl, mbstring)
