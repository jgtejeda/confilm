## MODIFIED Requirements

### Requirement: baseURL incluye /comisionfilm/ y termina con /
El valor de `$baseURL` en `app/app/Config/App.php` SHALL ser `'http://localhost/comisionfilm/'` — con el subfolder y terminando en `/`. Las rutas de autenticación (`/login`, `/registro`) generadas por `site_url()` DEBEN incluir el subfolder en todas las vistas y controllers del módulo de autenticación.

#### Scenario: site_url() genera URLs correctas
- **WHEN** se llama a `site_url('login')` dentro de CI4
- **THEN** retorna `http://localhost/comisionfilm/login`

#### Scenario: base_url() genera URLs correctas para assets
- **WHEN** se llama a `base_url('assets/css/auth.css')` dentro de CI4
- **THEN** retorna `http://localhost/comisionfilm/assets/css/auth.css`

#### Scenario: Link "Regístrate" usa site_url()
- **WHEN** se inspecciona el HTML de la card--login
- **THEN** el atributo `href` del link de navegación o el `action` del formulario contiene la URL completa con `/comisionfilm/` — no una ruta hardcodeada como `/registro`

#### Scenario: Atributo action del form usa site_url()
- **WHEN** se inspecciona el `<form>` de la card--register
- **THEN** el `action` es `<?= site_url('registro') ?>` que resuelve a `http://localhost/comisionfilm/registro`
