## Context

`users` ya tiene: `recovery_token VARCHAR(100) NULL`, `recovery_exp DATETIME NULL`. Rutas en grupo noauth configurado en P07. PasswordGenerator ya existe (P05).

## Goals / Non-Goals

**Goals:** RecoveryController completo con 4 métodos, vistas simples, rutas en noauth.
**Non-Goals:** 2FA, cambio de email, historial de passwords.

## Decisions

### D1 — Respuesta siempre genérica en sendLink()
Independientemente de si el email existe o no, retornar: "Si ese correo está registrado, recibirás un link en breve." Nunca revelar existencia del usuario.

### D2 — Token y expiración
```php
$token = bin2hex(random_bytes(32)); // 64 chars
$exp   = date('Y-m-d H:i:s', strtotime('+1 hour'));
// Al resetear: SET recovery_token=NULL, recovery_exp=NULL
```

### D3 — Validación de nueva contraseña
- `new_password` required, min_length 8
- `confirm_password` required, matches[new_password]
- Hash con bcrypt cost 12

### D4 — Link en correo
`site_url('reset/'.$token)` → genera `http://localhost/comisionfilm/reset/{token}`

## Risks / Trade-offs
- **[Riesgo] Token de reset activo durante 1 hora** → Aceptable; si el usuario ya cambió contraseña, el token queda NULL y cualquier link antiguo falla al buscar por token.
