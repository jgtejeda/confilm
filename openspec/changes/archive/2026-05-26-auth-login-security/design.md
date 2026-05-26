## Context

`login_attempts` ya existe con columnas: `id, identifier, ip_address, success, attempted_at`. LoginController ya tiene `index()` y un `login()` stub básico (en P04 se nombró `login()` pero debe ser `process()` para consistencia con Routes). AuthFilter ya existe. Filters.php ya tiene 'auth' registrado. La sesión usa DatabaseHandler con tabla `ci_sessions` (ya existe de P02).

## Goals / Non-Goals

**Goals:**
- `LoginController::process()` completo: rate limit, búsqueda user por email O username, password_verify, logAttempt, session()->regenerate(), redirect por role
- AdminFilter: verifica role in ['admin','superadmin']
- NoAuthFilter: si hay sesión activa, redirect según role (no permitir ver login/registro si ya está logueado)
- Filters.php: registrar 'auth' (ya existe), 'admin', 'noauth'
- Routes.php: aplicar filters correctamente a todos los grupos

**Non-Goals:**
- 2FA o cualquier otro mecanismo de autenticación
- Recuperación de contraseña (P08)
- Cambio de contraseña (P13)

## Decisions

### D1 — Rate limiting query
```php
$count = $db->table('login_attempts')
    ->where('identifier', $identifier)
    ->where('ip_address', $ip)
    ->where('success', 0)
    ->where('attempted_at >', date('Y-m-d H:i:s', strtotime('-15 minutes')))
    ->countAllResults();
if ($count >= 5) { return redirect()->to(site_url('login'))->with('error','Demasiados intentos...'); }
```

### D2 — Búsqueda por email o username
```php
$user = $userModel->groupStart()
    ->where('email', $credential)
    ->orWhere('username', $credential)
    ->groupEnd()
    ->first();
```
Usar `groupStart()/groupEnd()` para evitar problemas con orWhere sin where previo.

### D3 — logAttempt helper privado
```php
private function logAttempt(string $identifier, string $ip, bool $success): void {
    $db->table('login_attempts')->insert([
        'identifier'   => $identifier,
        'ip_address'   => $ip,
        'success'      => (int)$success,
        'attempted_at' => date('Y-m-d H:i:s'),
    ]);
}
```

### D4 — Sesión al login exitoso
```php
session()->regenerate(true);
session()->set([
    'user_id'        => $user['id'],
    'username'       => $user['username'],
    'nombres'        => $user['nombres'],
    'email'          => $user['email'],
    'role'           => $user['role'],
    'email_verified' => (int)$user['email_verified'],
]);
```

### D5 — NoAuthFilter redirect
```php
if ($userId = session()->get('user_id')) {
    $role = session()->get('role');
    if (in_array($role, ['admin','superadmin'])) return redirect()->to(site_url('admin'));
    return redirect()->to(site_url('dashboard'));
}
```

### D6 — Rutas con filters
```php
// Grupo noauth
$routes->group('', ['filter' => 'noauth'], function($r) {
    $r->get('login', 'Auth\LoginController::index');
    $r->post('login', 'Auth\LoginController::process');
    $r->get('registro', 'Auth\RegisterController::index');
    $r->post('registro', 'Auth\RegisterController::process');
    // ... recuperar, reset
});
// Grupo auth (usuarios)
$routes->group('dashboard', ['filter' => 'auth'], function($r) { ... });
// Grupo admin
$routes->group('admin', ['filter' => 'admin'], function($r) { ... });
```

## Risks / Trade-offs

- **[Riesgo] IP detrás de proxy/load balancer** → `$request->getIPAddress()` usa `$_SERVER['REMOTE_ADDR']`; en prod puede ser la IP del proxy. Aceptable para el MVP.
- **[Riesgo] Rate limit por IP compartida (WiFi pública)** → Los 5 intentos por IP pueden bloquear a múltiples usuarios. Aceptable dado el contexto del proyecto (registro institucional, no masivo).
