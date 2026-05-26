## Why

El dashboard del usuario es su vista principal: sus datos, el estado de sus documentos iniciales, el timeline del proceso y el acceso a notificaciones. Sin esto, el usuario verificado no tiene a dónde ir después del login.

## What Changes

- **NUEVO** `app/app/Views/layouts/user.php` — layout del usuario con navbar, campana de notificaciones + polling 30s
- **NUEVO** `Controllers/User/DashboardController.php` — queries usuario + docs iniciales + inscripción
- **NUEVO** `views/user/dashboard.php` — Mis Datos, chips de docs, timeline GSAP 5 pasos
- **NUEVO** `public/assets/css/user.css` — estilos del dashboard de usuario

## Capabilities

### New Capabilities

- `user-dashboard-view`: Dashboard con datos del usuario, chips de estado de docs iniciales dinámicos (del periodo activo), timeline GSAP animado de 5 pasos, polling de notificaciones cada 30s

### Modified Capabilities

(ninguna)

## Impact

- Nuevos: `layouts/user.php`, `Controllers/User/DashboardController.php`, `views/user/dashboard.php`, `public/assets/css/user.css`
- Queries: usuario + docs del periodo activo (JOIN period_document_types) + inscripción
- Rutas ya en Routes.php (P07): GET /dashboard (filter: auth)
