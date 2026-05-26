# create-documents-table Specification

## Purpose
TBD - created by archiving change migration-documents. Update Purpose after archive.
## Requirements
### Requirement: Migración CreateDocumentsTable crea la tabla correctamente
El sistema SHALL disponer del archivo `2025-01-01-000005_CreateDocumentsTable.php` que crea la tabla `documents`.

#### Scenario: Migración ejecuta sin errores
- **WHEN** se ejecuta `php spark migrate` con users, document_types y periods ya creados
- **THEN** la tabla `documents` existe sin errores

#### Scenario: Rollback limpio
- **WHEN** se ejecuta rollback de documents
- **THEN** la tabla se elimina sin errores

### Requirement: Columnas de nombre son original_name y stored_name
La tabla SHALL tener columnas `original_name VARCHAR(255) NOT NULL` y `stored_name VARCHAR(255) NOT NULL`. NO SHALL existir columnas llamadas `filename_orig` ni `filename_stored`.

#### Scenario: Columnas con nombre correcto presentes
- **WHEN** se inspecciona `DESCRIBE documents`
- **THEN** aparecen `original_name` y `stored_name` como varchar(255) NOT NULL

#### Scenario: Columnas con nombre incorrecto ausentes
- **WHEN** se inspecciona `DESCRIBE documents`
- **THEN** NO aparecen columnas llamadas `filename_orig` ni `filename_stored`

### Requirement: period_id es NOT NULL con FK a periods
La tabla SHALL tener `period_id INT UNSIGNED NOT NULL` con FK a `periods.id`. Un documento siempre pertenece a un periodo.

#### Scenario: period_id no acepta NULL
- **WHEN** se intenta insertar un documento sin period_id
- **THEN** MySQL lanza error NOT NULL constraint

#### Scenario: FK period_id es válida
- **WHEN** se inspecciona con SHOW CREATE TABLE documents
- **THEN** aparece FOREIGN KEY (period_id) REFERENCES periods(id)

### Requirement: Campos S3 presentes y con tipos correctos
La tabla SHALL tener: `s3_key VARCHAR(500) NOT NULL`, `s3_bucket VARCHAR(150) NOT NULL`, `file_extension VARCHAR(10) NULL`.

#### Scenario: Campos S3 con tipos correctos
- **WHEN** se inspecciona `DESCRIBE documents`
- **THEN** s3_key es varchar(500) NOT NULL, s3_bucket es varchar(150) NOT NULL, file_extension es varchar(10) NULL

### Requirement: Índices idx_user_period e idx_status presentes
La tabla SHALL tener INDEX en (user_id, period_id) y en status para optimizar queries de consulta de documentos.

#### Scenario: Índices presentes
- **WHEN** se ejecuta `SHOW INDEX FROM documents`
- **THEN** aparecen idx_user_period y idx_status

