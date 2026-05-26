## MODIFIED Requirements

### Requirement: AuthFilter verifica sesión activa y email verificado
El `AuthFilter` SHALL verificar que `session('user_id')` existe Y que `session('email_verified') == 1`. Si no hay `user_id`: redirigir a `site_url('login')`. Si hay `user_id` pero `email_verified == 0`: redirigir a `site_url('verificar-pendiente')`.

#### Scenario: Usuario no logueado es redirigido al login
- **WHEN** se accede a una ruta protegida por 'auth' sin sesión activa
- **THEN** AuthFilter redirige a `http://localhost/comisionfilm/login`

#### Scenario: Usuario logueado pero no verificado va a verificar-pendiente
- **WHEN** se accede a GET /dashboard con `session('user_id')` presente pero `session('email_verified') == 0`
- **THEN** AuthFilter redirige a `http://localhost/comisionfilm/verificar-pendiente`

#### Scenario: Usuario verificado pasa el filtro
- **WHEN** se accede a una ruta protegida con `session('user_id')` presente y `session('email_verified') == 1`
- **THEN** AuthFilter permite el acceso y el controller procesa la request
