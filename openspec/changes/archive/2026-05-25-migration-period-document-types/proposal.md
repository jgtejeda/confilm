## Why

Un periodo puede requerir distintos tipos de documento dependiendo de la convocatoria. Esta tabla pivote establece qué `document_types` aplican a cada `period`, con su orden de aparición. Es la cuarta migración.

## What Changes

- Crear migración CI4 `2025-01-01-000004_CreatePeriodDocumentTypesTable.php`
- Tabla pivote N:M entre `periods` y `document_types`
- UNIQUE KEY `uq_period_doctype (period_id, doc_type_id)` — evita duplicados
- Ambas FK con `ON DELETE CASCADE` — si se elimina el periodo o el tipo, se elimina la asignación

## Capabilities

### New Capabilities
- `create-period-document-types-table`: Tabla pivote con UNIQUE compuesta y CASCADE en ambas FK

### Modified Capabilities

## Impact

- Archivo nuevo: `app/app/Database/Migrations/2025-01-01-000004_CreatePeriodDocumentTypesTable.php`
- Depende de: `migration-periods` (FK period_id), `migration-document-types` (FK doc_type_id)
- El `down()` no requiere eliminación previa de otras FK (nada depende de esta tabla directamente)
