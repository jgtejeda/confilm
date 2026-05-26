## Why

Los tipos de documento son el corazón configurable del sistema. El admin debe poder crear, editar, activar/desactivar y reordenar los tipos sin tocar código. Sin este CRUD el sistema no puede configurar qué documentos piden a los usuarios.

## What Changes

- **NUEVO** `app/app/Controllers/Admin/DocumentTypeController.php` — 7 métodos: index, create, store, edit, update, toggle, reorder
- **NUEVO** `app/app/Views/admin/document_types/index.php` — tabla con filtros
- **NUEVO** `app/app/Views/admin/document_types/form.php` — checkboxes para tipos de archivo

## Capabilities

### New Capabilities

- `document-type-crud`: CRUD completo de tipos de documento; allowed_types como JSON en VARCHAR(500); toggle AJAX; reorder via AJAX

### Modified Capabilities

(ninguna)

## Impact

- Nuevos: `DocumentTypeController.php`, 2 vistas en `admin/document_types/`
- DB: INSERT/UPDATE en `document_types` — tabla ya existe (P02)
- DocumentTypeModel ya existe con `$allowedFields` correctos (P02)
- Rutas ya configuradas en P07 (grupo admin): `/admin/tipos-documento*`
