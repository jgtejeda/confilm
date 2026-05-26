# session-filters Specification

## Purpose
TBD - created by archiving change auth-login-security. Update Purpose after archive.
## Requirements
### Requirement: AdminFilter — solo admin y superadmin
`AdminFilter` SHALL verificar que `session('user_id')` existe Y que `session('role')` está en `['admin','superadmin']`. Si no hay sesión: redirect a `site_url('login')`. Si hay sesión pero role es 'user': redirect a `site_url('dashboard')`.

#### Scenario: Admin accede al panel admin
- **WHEN** usuario con role='admin' accede a GET /admin
- **THEN** AdminFilter permite el acceso

#### Scenario: Usuario regular bloqueado del admin
- **WHEN** usuario con role='user' accede a GET /admin
- **THEN** AdminFilter redirige a site_url('dashboard')

---

### Requirement: NoAuthFilter — redirigir usuarios ya autenticados
`NoAuthFilter` SHALL verificar si hay sesión activa (`session('user_id')`). Si existe: redirigir según role (admin/superadmin → site_url('admin'), user → site_url('dashboard')). Si no existe: permitir el acceso.

#### Scenario: Usuario ya logueado no puede ver /login
- **WHEN** usuario con sesión activa role='user' accede a GET /login
- **THEN** NoAuthFilter redirige a site_url('dashboard')

#### Scenario: Admin ya logueado no puede ver /registro
- **WHEN** admin con sesión activa accede a GET /registro
- **THEN** NoAuthFilter redirige a site_url('admin')

#### Scenario: Visitante sin sesión puede ver /login
- **WHEN** acceso a GET /login sin sesión activa
- **THEN** NoAuthFilter permite el acceso y LoginController::index() retorna la vista

---

### Requirement: Logout destruye sesión completamente
`LoginController::logout()` SHALL llamar `session()->destroy()` y redirigir a `site_url('login')`.

#### Scenario: Logout destruye la sesión
- **WHEN** GET /logout con sesión activa
- **THEN** la sesión es destruida y redirige a /login; acceder a /dashboard después retorna redirect al login

