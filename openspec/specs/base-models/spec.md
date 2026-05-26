# base-models Specification

## Purpose
TBD - created by archiving change base-models. Update Purpose after archive.
## Requirements
### Requirement: Los 6 modelos base existen con la estructura correcta de CI4
El sistema SHALL disponer de 6 archivos en `app/app/Models/`: UserModel.php, PeriodModel.php, DocumentTypeModel.php, DocumentModel.php, InscriptionModel.php, NotificationModel.php. Cada modelo SHALL extender `CodeIgniter\Model`.

#### Scenario: Todos los modelos existen
- **WHEN** se listan los archivos en app/app/Models/
- **THEN** existen los 6 archivos de modelo con los nombres exactos indicados

#### Scenario: Cada modelo referencia la tabla correcta
- **WHEN** se inspecciona la propiedad $table de cada modelo
- **THEN** UserModel→'users', PeriodModel→'periods', DocumentTypeModel→'document_types', DocumentModel→'documents', InscriptionModel→'inscriptions', NotificationModel→'notifications'

### Requirement: DocumentModel.$allowedFields incluye todos los campos S3 y nombres correctos
DocumentModel SHALL tener en `$allowedFields`: period_id, s3_key, s3_bucket, file_extension, original_name, stored_name. NO SHALL incluir fields llamados filename_orig ni filename_stored.

#### Scenario: Campos S3 en allowedFields
- **WHEN** se inspecciona $allowedFields de DocumentModel
- **THEN** contiene: 's3_key', 's3_bucket', 'file_extension', 'period_id'

#### Scenario: Nombres de columna correctos en allowedFields
- **WHEN** se inspecciona $allowedFields de DocumentModel
- **THEN** contiene 'original_name' y 'stored_name' — NO 'filename_orig' ni 'filename_stored'

#### Scenario: INSERT de documento incluye todos los campos
- **WHEN** se inserta un documento via DocumentModel con period_id, s3_key, s3_bucket, original_name, stored_name, file_extension
- **THEN** todos los campos se guardan correctamente en la BD

### Requirement: $useTimestamps correcto en cada modelo
Los modelos SHALL configurar $useTimestamps según si la tabla tiene created_at+updated_at o no.

#### Scenario: DocumentModel no usa timestamps automáticos
- **WHEN** se inspecciona DocumentModel
- **THEN** $useTimestamps = false (la tabla usa uploaded_at, no created_at/updated_at)

#### Scenario: Modelos con timestamps estándar los usan
- **WHEN** se inspeccionan UserModel, PeriodModel, InscriptionModel
- **THEN** $useTimestamps = true en todos ellos

