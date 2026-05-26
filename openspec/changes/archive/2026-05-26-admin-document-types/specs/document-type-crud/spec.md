## ADDED Requirements

### Requirement: CRUD completo de tipos de documento
`DocumentTypeController` SHALL implementar index (lista paginada), create/store (nuevo tipo), edit/update (editar), toggle (activar/desactivar vía AJAX), reorder (cambiar sort_order vía AJAX). `allowed_types` SHALL guardarse como `json_encode(array)` en VARCHAR(500) y leerse con `json_decode($val, true)`.

#### Scenario: Crear tipo con al menos un allowed_type
- **WHEN** admin crea un tipo con nombre "RFC", category='inicial', allowed_types=['pdf']
- **THEN** se inserta en `document_types` con `allowed_types='["pdf"]'` (JSON string)

#### Scenario: allowed_types vacío es rechazado
- **WHEN** admin intenta guardar un tipo sin ningún tipo de archivo seleccionado
- **THEN** la validación falla con error "Selecciona al menos un tipo de archivo"

#### Scenario: Toggle activo via AJAX retorna JSON
- **WHEN** POST /admin/tipos-documento/{id}/toggle con X-Requested-With header
- **THEN** retorna `{"success":true,"active":0}` o `{"success":true,"active":1}` y NO recarga la página

#### Scenario: Tipo inactivo no aparece en nuevos periodos
- **WHEN** un document_type tiene `active=0`
- **THEN** la vista de creación/edición de periodos NO lo muestra en la lista de documentos disponibles
