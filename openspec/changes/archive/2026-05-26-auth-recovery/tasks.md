## 1. Verificación previa

- [x] 1.1 Verificar `users.recovery_token VARCHAR(100) NULL` y `users.recovery_exp DATETIME NULL` existen (P02)
- [x] 1.2 Verificar que el grupo noauth está configurado en Routes.php (P07)

## 2. RecoveryController

- [x] 2.1 Crear `Controllers/Auth/RecoveryController.php` namespace `App\Controllers\Auth`
- [x] 2.2 `index()`: retornar `view('auth/recovery')`
- [x] 2.3 `sendLink()`: buscar email en UserModel; si existe → generar token+exp, UPDATE users, enviar correo (log si P09 no listo); SIEMPRE retornar misma respuesta genérica
- [x] 2.4 `resetForm($hash)`: buscar por recovery_token; si NULL o expirado → error; si OK → retornar `view('auth/reset_form', ['token'=>$hash])`
- [x] 2.5 `resetProcess()`: validar new_password (min 8) y confirm_password (matches); buscar token; si OK → `password_hash(PASSWORD_BCRYPT, cost 12)`, UPDATE users, limpiar token, redirect login
- [x] 2.6 Verificar expiración con `strtotime($user['recovery_exp']) > time()`

## 3. Vistas

- [x] 3.1 Crear `views/auth/recovery.php` — formulario con input email, botón "Enviar link", mensaje de respuesta genérica si hay flash
- [x] 3.2 Crear `views/auth/reset_form.php` — formulario con input hidden token, new_password, confirm_password, botón "Cambiar contraseña"
- [x] 3.3 Ambas vistas usan `site_url()` para action y `csrf_field()`

## 4. Rutas

- [x] 4.1 En grupo noauth: `$routes->get('recuperar', 'Auth\RecoveryController::index')`
- [x] 4.2 En grupo noauth: `$routes->post('recuperar', 'Auth\RecoveryController::sendLink')`
- [x] 4.3 En grupo noauth: `$routes->get('reset/(:hash)', 'Auth\RecoveryController::resetForm/$1')`
- [x] 4.4 En grupo noauth: `$routes->post('reset', 'Auth\RecoveryController::resetProcess')`

## 5. Verificación final

- [x] 5.1 POST /recuperar email existente → respuesta genérica, token en DB
- [x] 5.2 POST /recuperar email no existente → misma respuesta genérica, DB sin cambios
- [x] 5.3 GET /reset/{token_válido} → formulario de nueva contraseña
- [x] 5.4 POST /reset con nueva contraseña → hash actualizado, token limpiado, redirect login
- [x] 5.5 GET /reset/{token_expirado} → mensaje de error

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. Respuesta de sendLink() es SIEMPRE igual — no revelar si el email existe
2. Token: `bin2hex(random_bytes(32))` — 64 chars hex; exp: `NOW()+1h`
3. Al resetear: limpiar `recovery_token=NULL` Y `recovery_exp=NULL` — ambos campos
4. `password_hash($pass, PASSWORD_BCRYPT, ['cost'=>12])` — cost 12 explícito
5. `site_url('reset/'.$token)` en el correo — con subfolder /comisionfilm/
6. NO usar PasswordGenerator aquí — el usuario elige su propia contraseña nueva
7. `users.recovery_token` y `recovery_exp` ya existen — NO crear migración
