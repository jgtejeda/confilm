# auth-register-view Specification

## Purpose
TBD - created by archiving change auth-layout-card. Update Purpose after archive.
## Requirements
### Requirement: Vista de registro con campos personales básicos
El sistema SHALL proveer `views/auth/register.php` como card de registro dentro del layout `auth.php`. En esta propuesta (P04) la card DEBE contener ÚNICAMENTE los campos personales: `nombres`, `apellido_pat`, `apellido_mat` (opcional), `phone`, `email`. Los slots de documentos se agregan en P05. El formulario DEBE tener `method="post" action="<?= site_url('registro') ?>"` con el token CSRF de CI4 (`<?= csrf_field() ?>`).

#### Scenario: Vista de registro renderiza sin lógica de backend
- **WHEN** el usuario accede a `http://localhost/comisionfilm/registro`
- **THEN** se muestra la card con 5 campos de formulario (nombres, apellido_pat, apellido_mat, phone, email), un botón de submit, y un link "Ya tengo cuenta" que dispara `showLogin()`

#### Scenario: Campos tienen atributos correctos
- **WHEN** se inspecciona el HTML de la card--register
- **THEN** cada input tiene: `name` exacto (nombres, apellido_pat, apellido_mat, phone, email), `type` apropiado (text/email/tel), `id` para label, y el campo `apellido_mat` tiene `required` omitido o ausente

#### Scenario: CSRF token presente en el formulario
- **WHEN** se inspecciona el HTML del formulario de registro
- **THEN** existe un input hidden con el token CSRF generado por CI4 (`csrf_token` o equivalente)

#### Scenario: Link de navegación usa showLogin()
- **WHEN** el usuario hace click en "Ya tengo cuenta" en la card--register
- **THEN** se ejecuta la función `showLogin()` de auth-card.js sin navegar a una nueva URL

---

### Requirement: RegisterController stub
El sistema SHALL tener `app/app/Controllers/Auth/RegisterController.php` en el namespace `App\Controllers\Auth`, extendiendo `BaseController`. El método `index()` SHALL retornar `view('layouts/auth', ['card' => 'register'])`. La lógica de procesamiento POST se implementa en P05.

#### Scenario: GET /registro retorna la vista correcta
- **WHEN** el router de CI4 recibe GET /registro
- **THEN** `RegisterController::index()` es invocado y retorna el layout auth.php con `$card = 'register'`

#### Scenario: Controller no tiene lógica de procesamiento aún
- **WHEN** se inspecciona RegisterController.php
- **THEN** solo existe el método `index()` — no hay método `process()`, `store()`, ni lógica de validación de formulario

