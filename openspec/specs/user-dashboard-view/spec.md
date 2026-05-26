# user-dashboard-view Specification

## Purpose
TBD - created by archiving change user-dashboard. Update Purpose after archive.
## Requirements
### Requirement: Dashboard con documentos iniciales dinámicos
`DashboardController::index()` SHALL cargar los doc_types iniciales del periodo de la inscripción del usuario y sus estados actuales. Si un tipo de documento no fue cargado por el usuario: mostrar chip "No cargado" sin status de colores.

#### Scenario: Dashboard muestra chips de docs con estado correcto
- **WHEN** usuario con inscripción en periodo activo accede a GET /dashboard
- **THEN** se muestran los chips de documentos iniciales con sus estados (pending/approved/rejected/no cargado) desde la DB

#### Scenario: Docs vienen del periodo del usuario, no hardcodeados
- **WHEN** el admin cambia los doc_types del periodo
- **THEN** el dashboard refleja los nuevos tipos en el siguiente request

---

### Requirement: Polling de notificaciones cada 30s
El layout user.php SHALL inyectar `var baseUrl = '<?= site_url() ?>'` y hacer `setInterval(fetch(baseUrl+'dashboard/notificaciones/count'), 30000)`. El badge de la campana SHALL actualizarse con el count retornado.

#### Scenario: Badge de notificaciones se actualiza
- **WHEN** transcurren 30 segundos en cualquier página del dashboard
- **THEN** el badge actualiza su número con el count de notificaciones no leídas del servidor

#### Scenario: baseUrl usa site_url() de PHP
- **WHEN** se inspecciona el JS del layout
- **THEN** `baseUrl` contiene la URL con `/comisionfilm/` — nunca `/dashboard/` hardcodeado

---

### Requirement: Timeline de 5 pasos animado con GSAP
La vista SHALL mostrar un timeline de 5 pasos del proceso. El paso activo SHALL tener estilo destacado. Los pasos anteriores completados SHALL mostrarse como completados. GSAP stagger 150ms al cargar.

#### Scenario: Timeline refleja estado de la inscripción
- **WHEN** inscripción.status='under_review'
- **THEN** los pasos 1-3 se muestran completados, el paso 4 como activo

