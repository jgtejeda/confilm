## ADDED Requirements

### Requirement: Layout admin con sidebar y header
`views/layouts/admin.php` SHALL incluir: sidebar fijo con links de navegación usando `site_url()`, header con nombre del admin (`session('nombres')`) y link logout, área `<main>` para el contenido de cada página. SHALL cargar admin.css y GSAP CDN antes de admin.js.

#### Scenario: Panel admin accesible con rol admin
- **WHEN** admin logueado accede a GET /admin
- **THEN** retorna HTTP 200 con el layout admin, sidebar visible y dashboard con 4 tarjetas de stats

#### Scenario: Sidebar links usan site_url()
- **WHEN** se inspecciona el HTML del layout admin
- **THEN** todos los `href` del sidebar contienen `/comisionfilm/admin/...`, ninguno hardcodeado

---

### Requirement: Dashboard con 4 métricas COUNT()
`DashboardController::index()` SHALL hacer 4 queries COUNT() (total usuarios rol=user, usuarios status=pending, documentos status=pending, inscripciones status=approved) y pasarlas a la vista. Las queries NO cargan registros completos en memoria — solo COUNT().

#### Scenario: Dashboard muestra números correctos
- **WHEN** admin accede a GET /admin con datos en DB
- **THEN** las 4 tarjetas muestran los conteos reales con animación counter GSAP desde 0

#### Scenario: GSAP counter anima desde 0 al valor real
- **WHEN** la página /admin carga completamente
- **THEN** cada `.stat-value` anima su número desde 0 hasta el valor del `data-value` en ~1.5s con ease power2.out
