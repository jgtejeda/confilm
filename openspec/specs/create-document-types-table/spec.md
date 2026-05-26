# create-document-types-table Specification

## Purpose
TBD - created by archiving change migration-document-types. Update Purpose after archive.
## Requirements
### Requirement: Migración CreateDocumentTypesTable crea la tabla correctamente
El sistema SHALL disponer del archivo `app/app/Database/Migrations/2025-01-01-000003_CreateDocumentTypesTable.php` que crea la tabla `document_types`.

#### Scenario: Migración ejecuta sin errores
- **WHEN** se ejecuta `php spark migrate` con users ya creado
- **THEN** la tabla `document_types` existe en la base de datos

#### Scenario: Rollback limpio
- **WHEN** se ejecuta rollback de document_types (antes de period_document_types y documents)
- **THEN** la tabla se elimina sin errores de FK

### Requirement: allowed_types es VARCHAR(500) que almacena JSON
El campo `allowed_types` SHALL ser de tipo VARCHAR(500) NOT NULL y almacenar un JSON array de strings. NO SHALL existir una tabla separada para los tipos permitidos.

#### Scenario: Campo allowed_types tiene el tipo correcto
- **WHEN** se inspecciona el esquema con `DESCRIBE document_types`
- **THEN** `allowed_types` aparece como varchar(500) NOT NULL

#### Scenario: Almacena JSON array válido
- **WHEN** se inserta un registro con allowed_types = '["pdf","jpg","png"]'
- **THEN** el registro se guarda correctamente y json_decode retorna un array de 3 elementos

### Requirement: Tabla document_types tiene todos los campos requeridos
La tabla SHALL contener: id, name, description, category, required, allowed_types, max_size_mb, max_months, sort_order, active, created_by, created_at, updated_at.

#### Scenario: Campo category con ENUM correcto
- **WHEN** se inspecciona el esquema de `document_types`
- **THEN** `category` es ENUM('inicial','complementario') NOT NULL

#### Scenario: Valores default correctos
- **WHEN** se inserta un document_type con solo name, category y allowed_types
- **THEN** required=1, max_size_mb=5, sort_order=0, active=1

#### Scenario: FK created_by con SET NULL
- **WHEN** se elimina el usuario admin que creó el tipo de documento
- **THEN** created_by se convierte en NULL sin eliminar el tipo de documento

