## Context

ARQUITECTURA.md §7: 5 secciones del dashboard. §16: timeline GSAP ScrollTrigger con stagger 150ms. AuthFilter ya verifica email_verified (P06). Las rutas /dashboard/* tienen filter 'auth' (P07).

## Decisions

### D1 — Docs iniciales del periodo activo
```php
// Periodo activo del usuario = el de su inscripción más reciente
$inscription = $inscriptionModel->where('user_id',session('user_id'))->orderBy('created_at','DESC')->first();
$period = $periodModel->find($inscription['period_id'] ?? null);
// Docs iniciales del periodo
$initialDocTypes = $db->table('period_document_types pdt')
    ->select('dt.id, dt.name, dt.description, d.status, d.id as doc_id')
    ->join('document_types dt','dt.id=pdt.doc_type_id')
    ->join('documents d','d.doc_type_id=dt.id AND d.user_id='.session('user_id'),'left')
    ->where('pdt.period_id', $period['id'])
    ->where('dt.category','inicial')
    ->orderBy('pdt.sort_order')
    ->get()->getResultArray();
```

### D2 — Timeline 5 pasos (GSAP stagger)
Pasos: 1-Registro, 2-Verificación correo, 3-Documentos enviados, 4-Revisión admin, 5-Aprobación.
Estado activo = el paso actual según inscripción.status y email_verified.
```javascript
gsap.from('.timeline-step', {opacity:0, y:30, stagger:0.15, ease:'power2.out', duration:0.5});
```

### D3 — Polling de notificaciones (JS)
```javascript
var baseUrl = '<?= site_url() ?>';
setInterval(function() {
    fetch(baseUrl + 'dashboard/notificaciones/count')
        .then(r=>r.json()).then(data => { badge.textContent = data.count; });
}, 30000);
```
La variable `baseUrl` se inyecta desde PHP — NUNCA hardcodeada.

### D4 — Sin periodo activo
Si el usuario no tiene inscripción: mostrar mensaje "No tienes inscripciones activas" + link a /registro si hay periodo activo, o "No hay convocatoria abierta" si no.
