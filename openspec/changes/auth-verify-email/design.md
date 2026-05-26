## Context

La tabla `users` ya tiene: `verify_token VARCHAR(100) NULL`, `verify_exp DATETIME NULL`, `email_verified TINYINT(1) DEFAULT 0`. AuthFilter ya existe con verificación de `user_id` en sesión. LoginController ya guarda `email_verified` en sesión al hacer login. El sistema corre en Docker con MailDev en `rcf_maildev:1025`.

## Goals / Non-Goals

**Goals:**
- VerifyController: confirm(token), pending(), resend()
- AuthFilter actualizado: redirigir a `/verificar-pendiente` si `email_verified=0`
- Vista verify_pending.php con cuenta regresiva JS de 60s antes de permitir reenvío
- Límite de 3 reenvíos por hora
- Rutas: GET /verificar-pendiente (filter:auth), GET /verificar/(:hash) (sin filter), POST /verificar/reenviar (filter:auth)

**Non-Goals:**
- El correo real (MailService se implementa en P09 — aquí se usa log stub o call si existe)
- Cambio de email (P13)
- Recovery de contraseña (P08)

## Decisions

### D1 — Token generation
```php
$token = bin2hex(random_bytes(32)); // 64 chars hex
$exp   = date('Y-m-d H:i:s', strtotime('+24 hours'));
```
Al confirmar: `SET email_verified=1, verify_token=NULL, verify_exp=NULL`.

### D2 — Límite de reenvíos (3/hora)
Verificar que el `verify_exp` actual no sea más reciente que 20 minutos (es decir, el último reenvío fue hace menos de 20 min → rechazar). Alternativa más simple: contar reenvíos en la última hora usando `verify_exp >= DATE_SUB(NOW(), INTERVAL 1 HOUR)` como proxy. Se guarda un contador en sesión o se usa la lógica de verify_exp como semáforo de rate limiting.

Implementación elegida: guardar `resend_count` y `resend_hour` en sesión. Limpiar si `resend_hour` cambió. Máx 3 por hora.

### D3 — AuthFilter actualizado
```php
if (!session()->get('user_id')) {
    return redirect()->to(site_url('login'));
}
if (!session()->get('email_verified')) {
    return redirect()->to(site_url('verificar-pendiente'));
}
```
El valor `email_verified` ya se guarda en sesión al hacer login en LoginController (P07 lo completa, pero el setter ya existe en el login stub de P04).

### D4 — Cuenta regresiva en JS
```javascript
let seconds = 60;
const btn = document.getElementById('btn-reenviar');
const updateTimer = () => {
    btn.disabled = seconds > 0;
    btn.textContent = seconds > 0 ? `Reenviar (${seconds}s)` : 'Reenviar correo';
    if (seconds > 0) { seconds--; setTimeout(updateTimer, 1000); }
};
updateTimer();
```
Sin GSAP — lógica pura de JS nativo.

## Risks / Trade-offs

- **[Riesgo] Token expirado con usuario sin correo accesible** → Mitigación: botón reenviar siempre disponible desde `/verificar-pendiente`
- **[Trade-off] Rate limiting con sesión** → Si el usuario limpia cookies pierde el contador; es aceptable dado que el objetivo es solo evitar spam, no seguridad estricta
