## 1. Verificación previa

- [x] 1.1 Verificar `period_document_types` tiene UNIQUE(period_id, doc_type_id) (P02)
- [x] 1.2 Verificar `PeriodModel.$allowedFields`: name, description, start_date, end_date, active, created_by (P02)
- [x] 1.3 Verificar rutas `/admin/periodos*` en Routes.php (P07)

## 2. PeriodController — 6 métodos

- [x] 2.1 `index()`: query todos los periodos + lógica de badge en PHP antes de pasar a vista
- [x] 2.2 `create()`: cargar doc_types activos (`WHERE active=1`), retornar form
- [x] 2.3 `store()`: validar (name, start_date < end_date), INSERT periods (created_by=session('user_id')), INSERT period_document_types por cada doc seleccionado, redirect index
- [x] 2.4 `edit($id)`: cargar periodo + IDs de docs asignados, cargar todos los doc_types activos, retornar form con pre-selección
- [x] 2.5 `update($id)`: validar, UPDATE periods, DELETE WHERE period_id + INSERT nuevos docs
- [x] 2.6 `toggle($id)`: transacción — UPDATE active=0 WHERE id!=$id, UPDATE active=1 WHERE id=$id, retornar JSON o redirect

## 3. Vistas

- [x] 3.1 `views/admin/periods/index.php`: tabla con nombre, fechas, badge de estado, acciones (editar, toggle)
- [x] 3.2 `views/admin/periods/form.php`: input name, textarea description, datetime-local inputs para start/end, checkboxes de doc_types agrupados por categoría (inicial/complementario)

## 4. Verificación final

- [x] 4.1 Crear periodo → filas en periods y period_document_types
- [x] 4.2 Editar periodo quitando docs → DELETE + INSERT correcto (no duplicados)
- [x] 4.3 Toggle → solo 1 periodo activo en DB
- [x] 4.4 GET /registro con periodo activo → muestra formulario con sus docs
- [x] 4.5 GET /registro sin periodo activo → vista no_period

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. `period_document_types` al editar: DELETE WHERE period_id=? LUEGO INSERT — NO UPDATE
2. Toggle usa transacción: los dos UPDATE son atómicos
3. "Periodo activo" para registro: `active=1 AND start_date<=NOW() AND end_date>=NOW()` — AMBAS condiciones
4. `created_by = session('user_id')` del admin al crear el periodo
5. El form solo muestra document_types con `active=1` — los inactivos no aparecen
6. `start_date` y `end_date` son DATETIME — usar formato `Y-m-d H:i:s` al guardar
