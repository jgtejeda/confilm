## 1. Verificación previa

- [x] 1.1 Verificar columnas `login_attempts`: `id, identifier, ip_address, success, attempted_at` (propuesta 02)
- [x] 1.2 Verificar `users.last_login DATETIME NULL` existe (propuesta 02)
- [x] 1.3 Verificar `users.status ENUM('pending','active','rejected','suspended')` existe (propuesta 02)
- [x] 1.4 Verificar `Filters.php` actual — qué aliases ya están registrados para no duplicar

## 2. LoginController::process() completo

- [x] 2.1 Renombrar método existente `login()` a `process()` en LoginController (o verificar si ya se llama process)
- [x] 2.2 Implementar rate limit: query a `login_attempts` con `success=0`, `identifier=$credential`, `ip=$request->getIPAddress()`, `attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)` — si `>= 5`: redirect con error genérico
- [x] 2.3 Buscar usuario: `$userModel->groupStart()->where('email',$cred)->orWhere('username',$cred)->groupEnd()->first()`
- [x] 2.4 Si no existe o `!password_verify($pass, $user['password_hash'])`: `logAttempt(false)`, redirect con error genérico "Credenciales incorrectas"
- [x] 2.5 Si `$user['status'] !== 'active'`: `logAttempt(false)`, redirect con error "Cuenta no activa"
- [x] 2.6 Login exitoso: `logAttempt(true)`, `session()->regenerate(true)`, `session()->set([...])` con: user_id, username, nombres, email, role, email_verified
- [x] 2.7 UPDATE `users.last_login = NOW()` con `$userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')])`
- [x] 2.8 Redirect: role in ['admin','superadmin'] → `site_url('admin')`, else → `site_url('dashboard')`
- [x] 2.9 Implementar método privado `logAttempt(string $identifier, string $ip, bool $success): void` con INSERT a `login_attempts`

## 3. AdminFilter

- [x] 3.1 Crear `app/app/Filters/AdminFilter.php` con namespace `App\Filters`, implementando `FilterInterface`
- [x] 3.2 En `before()`: si no `session('user_id')` → redirect `site_url('login')`; si `session('role')` no in `['admin','superadmin']` → redirect `site_url('dashboard')`

## 4. NoAuthFilter

- [x] 4.1 Crear `app/app/Filters/NoAuthFilter.php` con namespace `App\Filters`, implementando `FilterInterface`
- [x] 4.2 En `before()`: si `session('user_id')` existe → si role in ['admin','superadmin'] redirect `site_url('admin')`, else redirect `site_url('dashboard')`

## 5. Config/Filters.php

- [x] 5.1 En el array `$aliases`: verificar 'auth' → `\App\Filters\AuthFilter::class` (ya debe estar)
- [x] 5.2 Agregar: `'admin' => \App\Filters\AdminFilter::class`
- [x] 5.3 Agregar: `'noauth' => \App\Filters\NoAuthFilter::class`

## 6. Config/Routes.php — aplicar filters

- [x] 6.1 Envolver rutas de auth (login, registro, recuperar, reset) en grupo `['filter' => 'noauth']`
- [x] 6.2 Envolver rutas de dashboard en grupo `['filter' => 'auth']`
- [x] 6.3 Envolver rutas de admin en grupo `['filter' => 'admin']`
- [x] 6.4 GET /logout sin filter (puede acceder cualquiera)
- [x] 6.5 GET /verificar-pendiente y POST /verificar/reenviar con `['filter' => 'auth']` (de P06)

## 7. Verificación final

- [x] 7.1 POST /login con credenciales correctas: sesión creada con los 6 campos, redirect correcto por role
- [x] 7.2 POST /login con credenciales incorrectas: error genérico, registro en login_attempts success=0
- [x] 7.3 6to intento fallido < 15 min: mensaje de bloqueo sin verificar contraseña
- [x] 7.4 Admin (role=admin) accede a /admin → AdminFilter lo permite
- [x] 7.5 Usuario (role=user) intenta /admin → AdminFilter redirige a /dashboard
- [x] 7.6 Usuario logueado intenta GET /login → NoAuthFilter redirige a /dashboard
- [x] 7.7 GET /logout → sesión destruida, redirect a /login

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. `session()->set()` incluye EXACTAMENTE: `user_id, username, nombres, email, role, email_verified` — estos 6 campos
2. `session('email_verified')` es un INT (0 o 1) — NO un bool directamente
3. `groupStart()/groupEnd()` es obligatorio para el OR en Query Builder — sin ellos el orWhere puede romper la query
4. `logAttempt` registra `success=0` también cuando el usuario está suspendido o no activo
5. El rate limit cuenta `success=0` — NO todos los intentos (los exitosos no cuentan)
6. `$request->getIPAddress()` devuelve la IP real — usar este método, NO `$_SERVER['REMOTE_ADDR']` directamente
7. `session()->regenerate(true)` (con true) para destruir la sesión anterior y generar nueva ID
8. La tabla `ci_sessions` ya existe — la sesión DatabaseHandler funciona sin configuración adicional
9. NO hardcodear `/admin` ni `/dashboard` — siempre `site_url('admin')` y `site_url('dashboard')`
10. `FilterInterface` se importa desde `CodeIgniter\Filters\FilterInterface`
