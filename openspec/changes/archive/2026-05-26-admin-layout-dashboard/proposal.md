## Why

El panel de administración necesita su propio layout con sidebar, header y área de contenido, más un dashboard con métricas animadas. Es la base sobre la que se montan todos los módulos admin (P11-P16).

## What Changes

- **NUEVO** `app/app/Views/layouts/admin.php` — sidebar fijo con links a todas las secciones, header con nombre del admin y logout, área main
- **NUEVO** `app/app/Views/admin/dashboard.php` — 4 tarjetas de stats con GSAP counter animado
- **NUEVO** `app/app/Controllers/Admin/DashboardController.php` — queries COUNT() de métricas
- **NUEVO** `public/assets/css/admin.css` — estilos del panel admin con variables CSS heredadas
- **NUEVO** `public/assets/js/admin.js` — GSAP counter para stats

## Capabilities

### New Capabilities

- `admin-layout`: Layout de panel admin con sidebar, header, nav links, área de contenido reutilizable
- `admin-dashboard-stats`: Dashboard con 4 contadores animados (total usuarios, pendientes, docs pendientes, inscripciones aprobadas)

### Modified Capabilities

(ninguna)

## Impact

- Nuevos: `layouts/admin.php`, `views/admin/dashboard.php`, `Controllers/Admin/DashboardController.php`, `public/assets/css/admin.css`, `public/assets/js/admin.js`
- Queries: COUNT() sobre `users`, `documents`, `inscriptions` — sin cargar todos los registros en memoria
- AdminFilter ya configurado (P07) — todas las rutas /admin/* protegidas
