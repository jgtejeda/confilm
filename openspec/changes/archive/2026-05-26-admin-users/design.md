## Context

`users` columnas: id, username (NO editable), email (unique), phone, password_hash, nombres, apellido_pat, apellido_mat, role ENUM('user','admin','superadmin'), status ENUM('pending','active','rejected','suspended'), email_verified, verify_token, verify_exp, recovery_token, recovery_exp, last_login, created_at, updated_at.

## Decisions

### D1 — Campos editables por admin
Editable: nombres, apellido_pat, apellido_mat, phone, email, status, role.
NO editable: username, password_hash (hay opción separada de reset).

### D2 — Unicidad de email al editar
```php
$rules['email'] = "required|valid_email|is_unique[users.email,id,{$id}]";
// CI4 soporta exclusión de propio registro en is_unique
```

### D3 — Si cambia email: re-verificar
Si `$post['email'] !== $user['email']`: setear `email_verified=0`, generar nuevo `verify_token` y `verify_exp`, enviar correo de verificación.

### D4 — Reset de contraseña
```php
$newPass = PasswordGenerator::generate();
$userModel->update($id, ['password_hash' => password_hash($newPass, PASSWORD_BCRYPT, ['cost'=>12])]);
MailService::sendWelcome($user, $newPass);
```

### D5 — Paginación
`$users = $userModel->where('role','user')->paginate(20)` + filtros opcionales por status y búsqueda por nombre/email.
