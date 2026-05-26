## Why

CI4 necesita modelos para interactuar con las tablas via Query Builder. Los 6 modelos base definen `$table`, `$primaryKey`, `$allowedFields` y timestamps. El modelo más crítico es `DocumentModel` — su `$allowedFields` debe incluir los nombres exactos de columnas.

## What Changes

- Crear 6 modelos base en `app/app/Models/`:
  1. `UserModel.php`
  2. `PeriodModel.php`
  3. `DocumentTypeModel.php`
  4. `DocumentModel.php` — $allowedFields CRÍTICO: period_id, s3_key, s3_bucket, file_extension, original_name, stored_name
  5. `InscriptionModel.php`
  6. `NotificationModel.php`
- Solo estructura base: $table, $primaryKey, $allowedFields, $useTimestamps, $createdField, $updatedField
- Sin lógica de negocio (va en controllers y services)

## Capabilities

### New Capabilities
- `base-models`: 6 modelos CI4 con $allowedFields correctos, especialmente DocumentModel con los campos S3 y nombres de columnas exactos

### Modified Capabilities

## Impact

- 6 archivos nuevos en `app/app/Models/`
- CRÍTICO: DocumentModel.$allowedFields debe incluir: period_id, s3_key, s3_bucket, file_extension, original_name, stored_name
- CRÍTICO: DocumentModel NO debe incluir: filename_orig, filename_stored (nombres incorrectos)
- Habilita que todos los controllers puedan usar los modelos vía CI4 Query Builder
