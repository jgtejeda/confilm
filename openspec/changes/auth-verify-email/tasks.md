## 1. Verificación previa

- [x] 1.1 Verificar que `users` tiene columnas: `verify_token VARCHAR(100) NULL`, `verify_exp DATETIME NULL`, `email_verified TINYINT(1) DEFAULT 0` (propuesta 02)
- [x] 1.2 Verificar que `AuthFilter.php` existe con verificación de `session('user_id')` (propuesta 04/07)
- [x] 1.3 Verificar que LoginController guarda `email_verified` en sesión (propuesta 04 ya lo tiene en el stub)
- [x] 1.4 Confirmar rutas existentes en Routes.php — no duplicar

## 2. VerifyController

- [x] 2.1 Crear `app/app/Controllers/Auth/VerifyController.php` con namespace `App\Controllers\Auth`, extendiendo BaseController
- [x] 2.2 Método `pending()`: verificar que hay sesión activa (user_id en sesión), retornar `view('auth/verify_pending', ['email' => session('email')])`
- [x] 2.3 Método `confirm(string $token)`: `$user = $userModel->where('verify_token', $token)->first()` — si NULL: vista error "Link inválido"; si `verify_exp < NOW()`: vista error "Link expirado"; si OK: UPDATE `email_verified=1, verify_token=NULL, verify_exp=NULL`, redirect `site_url('login')` con flash "Correo verificado ✓"
- [x] 2.4 Método `resend()`: verificar rate limit (sesión: `resend_count` y `resend_hour`); si `resend_count >= 3` y misma hora: retornar error; si OK: regenerar token y exp, UPDATE users, intentar enviar correo (o log_message), actualizar contador en sesión, retornar éxito
- [x] 2.5 En `confirm()`: usar `strtotime($user['verify_exp']) > time()` para verificar expiración — NO usar comparación de strings

## 3. Vista verify_pending.php

- [x] 3.1 Crear `app/app/Views/auth/verify_pending.php` — puede usar layout `layouts/auth.php` o ser standalone simple
- [x] 3.2 Mostrar: título "Verifica tu correo electrónico", email del usuario (de `$email` pasado por controller), instrucción "Haz click en el link que enviamos a..."
- [x] 3.3 Formulario POST a `site_url('verificar/reenviar')` con `csrf_field()`, botón `id="btn-reenviar"` inicialmente deshabilitado
- [x] 3.4 Script JS inline: cuenta regresiva de 60s — `let s=60; const update=()=>{btn.disabled=s>0; btn.textContent=s>0?'Reenviar ('+s+'s)':'Reenviar correo'; if(s>0){s--;setTimeout(update,1000);}}; update();`

## 4. AuthFilter actualizado

- [x] 4.1 En `Filters/AuthFilter.php`, en el método `before()`: después de verificar `session('user_id')`, agregar: `if (!session()->get('email_verified')) { return redirect()->to(site_url('verificar-pendiente')); }`
- [x] 4.2 Verificar que la ruta `/verificar-pendiente` y `/verificar/reenviar` NO están protegidas por AuthFilter de forma que bloquee (verificar en Routes.php que tienen el filter correcto)

## 5. Rutas

- [x] 5.1 Agregar en Routes.php: `$routes->get('verificar-pendiente', 'Auth\VerifyController::pending', ['filter' => 'auth']);`
- [x] 5.2 Agregar: `$routes->get('verificar/(:hash)', 'Auth\VerifyController::confirm/$1');` — sin filter (link del correo no tiene sesión necesariamente)
- [x] 5.3 Agregar: `$routes->post('verificar/reenviar', 'Auth\VerifyController::resend', ['filter' => 'auth']);`

## 6. Verificación final

- [ ] 6.1 GET /verificar/{token_válido} → `email_verified=1` en DB, redirect a /login con mensaje
- [ ] 6.2 GET /verificar/{token_expirado} → vista de error con opción reenviar
- [ ] 6.3 GET /verificar-pendiente con sesión activa email_verified=0 → vista de espera
- [ ] 6.4 POST /verificar/reenviar (3er reenvío) → token regenerado, correo enviado o loggeado
- [ ] 6.5 POST /verificar/reenviar (4o reenvío < 1h) → error de límite
- [ ] 6.6 GET /dashboard con email_verified=0 → AuthFilter redirige a /verificar-pendiente
- [ ] 6.7 GET /dashboard con email_verified=1 → pasa el filtro normalmente

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. `verify_token = bin2hex(random_bytes(32))` — resultado: 64 chars hex
2. `verify_exp = date('Y-m-d H:i:s', strtotime('+24 hours'))` — DATETIME format MySQL
3. Al confirmar: `SET email_verified=1, verify_token=NULL, verify_exp=NULL` — los tres campos
4. Verificar expiración: `strtotime($user['verify_exp']) > time()` — NO `$user['verify_exp'] > date('Y-m-d')`
5. `session()->get('email_verified')` NO es `session()->get('user')['email_verified']` — es un campo plano en sesión
6. La ruta `/verificar/(:hash)` es para el link del correo — NO requiere sesión activa
7. La ruta `/verificar-pendiente` y `/verificar/reenviar` SÍ requieren sesión (filter: auth)
8. AuthFilter verifica `email_verified` en SESIÓN, no hace query a DB en cada request
9. NO modificar la lógica de verificación de `user_id` existente en AuthFilter — solo agregar la verificación de `email_verified`
10. `site_url('verificar/'.$token)` genera la URL correcta con subfolder `/comisionfilm/`
