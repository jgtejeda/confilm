## Context

`periods`: id, name, description, start_date, end_date, active, created_by. `period_document_types`: id, period_id, doc_type_id, sort_order. UNIQUE(period_id, doc_type_id). PeriodModel.$allowedFields: name, description, start_date, end_date, active, created_by.

## Decisions

### D1 — Toggle activo en transacción
```php
$db->transStart();
$db->table('periods')->where('id !=', $id)->update(['active' => 0]);
$db->table('periods')->where('id', $id)->update(['active' => 1]);
$db->transComplete();
```
Solo un periodo activo a la vez.

### D2 — Asignar docs al periodo (edit: DELETE + INSERT)
```php
// Al editar: eliminar todos y reinsertar
$db->table('period_document_types')->where('period_id', $id)->delete();
foreach($selectedDocTypeIds as $dtId) {
    $db->table('period_document_types')->insert(['period_id'=>$id,'doc_type_id'=>$dtId,'sort_order'=>$idx]);
}
```

### D3 — Estado del periodo (lógica de badge)
```php
// Activo: active=1 AND start<=NOW() AND end>=NOW()
// Futuro: active=1 AND start>NOW()
// Expirado: end<NOW()
// Inactivo: active=0
```

### D4 — Form carga solo document_types activos
`WHERE active=1 ORDER BY category, sort_order` para poblar los checkboxes del formulario.
