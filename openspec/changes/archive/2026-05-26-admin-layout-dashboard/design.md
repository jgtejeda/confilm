## Context

AdminFilter registrado (P07). Rutas /admin/* con filter 'admin' configuradas. GSAP disponible (CDN o vendor). Variables CSS de auth.css como referencia para consistencia visual.

## Goals / Non-Goals

**Goals:** Layout admin funcional, DashboardController con 4 queries COUNT(), stats animadas con GSAP counter.
**Non-Goals:** Tablas de datos detalladas (son P11-P16), notificaciones en sidebar (P21), reportes o exports.

## Decisions

### D1 — Stats del dashboard (4 queries COUNT simples)
```php
$stats = [
  'total_users'          => $db->table('users')->where('role','user')->countAllResults(),
  'pending_review'       => $db->table('users')->where('status','pending')->countAllResults(),
  'docs_pending'         => $db->table('documents')->where('status','pending')->countAllResults(),
  'inscriptions_approved'=> $db->table('inscriptions')->where('status','approved')->countAllResults(),
];
```

### D2 — GSAP counter en admin.js
```javascript
document.querySelectorAll('.stat-value').forEach(el => {
    const target = parseInt(el.dataset.value);
    gsap.to({val:0}, {val:target, duration:1.5, ease:'power2.out',
        onUpdate: function() { el.textContent = Math.round(this.targets()[0].val); }
    });
});
```
Cada `<span class="stat-value" data-value="<?= $stats['total_users'] ?>">0</span>`.

### D3 — Sidebar links
Con site_url(): admin/, admin/usuarios, admin/tipos-documento, admin/periodos, admin/notificaciones. Link de logout: site_url('logout').

### D4 — Layout admin.php incluye admin.css y GSAP
GSAP desde CDN cdnjs, admin.js después. Las páginas internas extienden el layout via `$this->renderSection('content')` o via include simple con variable `$content`.
