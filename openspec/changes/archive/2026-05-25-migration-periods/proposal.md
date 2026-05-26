## Why

El sistema maneja periodos de inscripción configurables por el admin. La tabla `periods` es la segunda en el orden de migraciones porque otras tablas (period_document_types, documents, inscriptions) tienen FK a ella. Depende de `users` (created_by).

## What Changes

- Crear migración CI4 `2025-01-01-000002_CreatePeriodsTable.php` en `app/app/Database/Migrations/`
- La tabla almacena nombre, descripción, fechas de inicio/fin, estado activo y el admin que la creó
- FK: `created_by → users.id ON DELETE SET NULL`
- Índice compuesto `idx_active_dates (active, start_date, end_date)` para la query de "periodo activo"

## Capabilities

### New Capabilities
- `create-periods-table`: Migración que crea la tabla `periods` con FK a `users` y el índice compuesto requerido por la query de periodo activo

### Modified Capabilities

## Impact

- Archivo nuevo: `app/app/Database/Migrations/2025-01-01-000002_CreatePeriodsTable.php`
- Depende de: `migration-users` (FK created_by → users.id)
- Habilita: `migration-period-document-types`, `migration-documents`, `migration-inscriptions`
- El `down()` debe ejecutarse ANTES que el rollback de users para no violar FK
