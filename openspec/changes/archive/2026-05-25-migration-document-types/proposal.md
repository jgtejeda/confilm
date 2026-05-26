## Why

Los tipos de documento son configurables por el admin — no están hardcodeados. Esta tabla almacena el catálogo maestro que el admin define desde el panel. Es la tercera migración porque `period_document_types` y `documents` tienen FK a ella.

## What Changes

- Crear migración CI4 `2025-01-01-000003_CreateDocumentTypesTable.php`
- Campo crítico: `allowed_types VARCHAR(500) NOT NULL` — almacena JSON array (NO es tabla separada)
- Campo `category ENUM('inicial','complementario')` — categoriza el tipo de documento
- FK: `created_by → users.id ON DELETE SET NULL`

## Capabilities

### New Capabilities
- `create-document-types-table`: Migración que crea `document_types` con `allowed_types` como VARCHAR(500) JSON y los campos de configuración del admin

### Modified Capabilities

## Impact

- Archivo nuevo: `app/app/Database/Migrations/2025-01-01-000003_CreateDocumentTypesTable.php`
- Depende de: `migration-users` (FK created_by)
- Habilita: `migration-period-document-types` (FK doc_type_id), `migration-documents` (FK doc_type_id)
- CRÍTICO: `allowed_types` es VARCHAR(500), NO una tabla relacional separada
