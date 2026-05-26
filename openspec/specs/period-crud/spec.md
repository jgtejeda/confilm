# period-crud Specification

## Purpose
TBD - created by archiving change admin-periods. Update Purpose after archive.
## Requirements
### Requirement: CRUD de periodos con document_types
El admin SHALL poder crear y editar periodos (name, description, start_date, end_date) y asignar document_types activos al periodo via checkboxes. Al editar, `period_document_types` SHALL ser DELETE WHERE period_id + INSERT (no UPDATE).

#### Scenario: Crear periodo asigna document_types
- **WHEN** admin crea periodo con 3 doc_types iniciales y 5 complementarios seleccionados
- **THEN** se inserta en `periods` y se insertan 8 filas en `period_document_types`

#### Scenario: Editar periodo reemplaza doc_types
- **WHEN** admin edita periodo quitando 2 doc_types y agregando 1
- **THEN** DELETE WHERE period_id + INSERT de los nuevos seleccionados — no UPDATE

---

### Requirement: Toggle activo — solo un periodo activo
Al activar un periodo SHALL ejecutar en transacción: `UPDATE periods SET active=0 WHERE id != $id` + `UPDATE periods SET active=1 WHERE id = $id`.

#### Scenario: Activar periodo desactiva los demás
- **WHEN** admin activa el periodo 3 (había 2 periodos con active=1)
- **THEN** solo el periodo 3 queda con active=1, los demás con active=0

---

### Requirement: Badge de estado correcto
La vista de lista SHALL mostrar el estado correcto: Activo (active=1 y fechas vigentes), Futuro (active=1 y start>NOW()), Expirado (end<NOW()), Inactivo (active=0).

#### Scenario: Periodo con fechas pasadas muestra badge Expirado
- **WHEN** un periodo tiene end_date < NOW()
- **THEN** la vista muestra badge "Expirado" independientemente de active

