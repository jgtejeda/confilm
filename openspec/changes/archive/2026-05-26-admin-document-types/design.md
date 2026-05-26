## Context

`document_types` columnas: `id, name, description, category ENUM('inicial','complementario'), required, allowed_types VARCHAR(500), max_size_mb INT, max_months INT NULL, sort_order INT, active TINYINT(1), created_by INT`. DocumentTypeModel.$allowedFields incluye todos estos campos. Los valores válidos de allowed_types son: "pdf","docx","xlsx","pptx","jpg","png".

## Goals / Non-Goals

**Goals:** CRUD completo, toggle AJAX (fetch POST), reorder AJAX (array de {id, sort_order}).
**Non-Goals:** Historial de cambios, soft delete, exportar lista.

## Decisions

### D1 — allowed_types: JSON en VARCHAR(500)
```php
// Guardar: json_encode($request->getPost('allowed_types'))
// Leer:    json_decode($docType['allowed_types'], true)
// En el form: foreach(['pdf','docx','xlsx','pptx','jpg','png'] as $type)
//   checked si in_array($type, $currentAllowed)
```

### D2 — Toggle AJAX
```javascript
fetch(site_url + 'admin/tipos-documento/' + id + '/toggle', {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest',...csrf}})
  .then(r=>r.json()).then(data => { /* actualizar UI */ Notify.success(...) })
```
Controller retorna `$this->response->setJSON(['success'=>true, 'active'=>$newState])`.

### D3 — Reorder AJAX
POST con body `[{id:1,sort_order:0},{id:3,sort_order:1},...]`. Controller hace UPDATE individual por ID.

### D4 — created_by
`'created_by' => session('user_id')` — el ID del admin logueado al crear.

## Risks / Trade-offs
- **[Riesgo] allowed_types vacío** → Validar server-side: mínimo 1 elemento seleccionado antes de json_encode.
