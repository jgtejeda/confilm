## Why

Los periodos de inscripción controlan cuándo los usuarios pueden registrarse y qué documentos deben subir. El admin necesita crear periodos con fechas, asignar tipos de documento (iniciales y complementarios) y activarlos/desactivarlos.

## What Changes

- **NUEVO** `app/app/Controllers/Admin/PeriodController.php` — CRUD + toggle
- **NUEVO** `app/app/Views/admin/periods/index.php` — lista con badges de estado
- **NUEVO** `app/app/Views/admin/periods/form.php` — campos + checkboxes de doc types

## Capabilities

### New Capabilities

- `period-crud`: CRUD de periodos con asignación de document_types; toggle activa un periodo y desactiva los demás en transacción; badges de estado (Activo/Expirado/Futuro/Inactivo)

### Modified Capabilities

(ninguna)

## Impact

- Nuevos: `PeriodController.php`, 2 vistas en `admin/periods/`
- DB: INSERT/UPDATE en `periods`, DELETE+INSERT en `period_document_types` — tablas existen (P02)
- PeriodModel ya existe (P02)
- Rutas ya en Routes.php (P07)
