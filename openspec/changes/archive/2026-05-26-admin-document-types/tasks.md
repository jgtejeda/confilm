## 1. Verificación previa

- [x] 1.1 Verificar `DocumentTypeModel.$allowedFields`: name, description, category, required, allowed_types, max_size_mb, max_months, sort_order, active, created_by (P02)
- [x] 1.2 Verificar que las rutas `/admin/tipos-documento*` están en Routes.php (P07)
- [x] 1.3 Valores válidos de allowed_types: EXACTAMENTE "pdf","docx","xlsx","pptx","jpg","png"

## 2. DocumentTypeController — 7 métodos

- [x] 2.1 `index()`: `$docTypeModel->orderBy('sort_order','ASC')->paginate(20)` — pasar `$pager` a vista
- [x] 2.2 `create()`: retornar `view('admin/document_types/form')` con `$docType=null`
- [x] 2.3 `store()`: validar (name required, allowed_types min 1), `json_encode($post['allowed_types'])`, INSERT con `created_by=session('user_id')`, redirect a index
- [x] 2.4 `edit($id)`: cargar docType, `json_decode($docType['allowed_types'],true)` para checkboxes pre-marcados, retornar form
- [x] 2.5 `update($id)`: validar, `json_encode`, UPDATE — NO modificar created_by
- [x] 2.6 `toggle($id)`: flip `active` (0→1, 1→0), `$docTypeModel->update($id,['active'=>$newVal])`, retornar `$this->response->setJSON(['success'=>true,'active'=>$newVal])`
- [x] 2.7 `reorder()`: `$items = json_decode($request->getBody(),true)` — foreach item: `$docTypeModel->update($item['id'],['sort_order'=>$item['sort_order']])`, retornar JSON success

## 3. Vistas

- [x] 3.1 `views/admin/document_types/index.php`: tabla con columnas nombre, categoría, tipos (array legible), activo (badge), acciones (editar, toggle)
- [x] 3.2 `views/admin/document_types/form.php`: input name, textarea description, radio category (inicial/complementario), checkboxes para los 6 tipos de archivo, input max_size_mb (default 5), input max_months, input sort_order
- [x] 3.3 Checkboxes: `<input type="checkbox" name="allowed_types[]" value="pdf">PDF` — etc. para los 6 tipos
- [x] 3.4 Toggle button en index: `onclick="toggleDocType(<?= $dt['id'] ?>)"` con fetch AJAX

## 4. Verificación final

- [x] 4.1 Crear tipo "RFC" con PDF → `document_types.allowed_types = '["pdf"]'` en DB
- [x] 4.2 Editar tipo → checkboxes pre-marcados correctamente desde json_decode
- [x] 4.3 Toggle → respuesta JSON, UI actualizada sin recargar
- [x] 4.4 allowed_types vacío → error de validación

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. `allowed_types` es VARCHAR(500) que almacena JSON — NO es tabla separada, NO es columna con tipo JSON de MySQL
2. Valores válidos EXACTOS del array: `"pdf"`, `"docx"`, `"xlsx"`, `"pptx"`, `"jpg"`, `"png"` — sin variaciones
3. `json_encode($post['allowed_types'])` antes de guardar; `json_decode($val, true)` al leer
4. `created_by = session('user_id')` del admin logueado — NO NULL al crear
5. `category` es ENUM: EXACTAMENTE `'inicial'` o `'complementario'` — sin mayúsculas ni variaciones
6. Las rutas ya existen en Routes.php (P07) — NO recrear en esta propuesta
7. Toggle retorna JSON — NO redirect ni recarga de página
