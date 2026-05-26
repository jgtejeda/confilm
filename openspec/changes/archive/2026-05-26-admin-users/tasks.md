## 1. Verificación previa

- [x] 1.1 Verificar `UserModel.$allowedFields` incluye todos los campos editables pero NO username (P02)
- [x] 1.2 Verificar rutas `/admin/usuarios*` en Routes.php (P07)
- [x] 1.3 Verificar PasswordGenerator existe (P05)

## 2. UserController — métodos

- [x] 2.1 `index()`: `$userModel->where('role','user')` + filtro por status (GET param) + búsqueda por nombres/email (LIKE) + paginate(20)
- [x] 2.2 `detail($id)`: cargar usuario + sus documentos (JOIN doc_types) + inscripción actual
- [x] 2.3 `edit($id)`: retornar form con datos del usuario
- [x] 2.4 `update($id)`: validar sin username (is_unique excluye propio ID), UPDATE users — si email cambió: email_verified=0, nuevo verify_token/exp, enviar correo
- [x] 2.5 `changeStatus($id)`: `UPDATE users SET status=$newStatus` — verificar que status es uno de los ENUM válidos
- [x] 2.6 `resetPassword($id)`: PasswordGenerator::generate(), bcrypt cost 12, UPDATE password_hash, MailService::sendWelcome
- [x] 2.7 `validateInscription($id)`: — implementado en P15, aquí solo stub

## 3. Vistas

- [x] 3.1 `views/admin/users/index.php`: tabla paginada con nombre completo, email, status chip, fecha registro, botones ver/editar
- [x] 3.2 `views/admin/users/detail.php`: datos personales, tabla de docs con status chips y botones ver/validar, inscripción actual, botones de acción
- [x] 3.3 `views/admin/users/edit.php`: formulario con campos editables (sin username), select status, select role

## 4. Verificación final

- [x] 4.1 Editar email → email_verified=0 en DB, correo de verificación enviado
- [x] 4.2 email duplicado → error de validación sin actualizar DB
- [x] 4.3 Reset password → nuevo hash en DB, correo con contraseña enviado
- [x] 4.4 username no aparece en form ni se procesa en update

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. `username` NO es editable — NO incluir en form ni en $allowedFields del UPDATE
2. `is_unique[users.email,id,{$id}]` — excluir el propio registro en la validación de unicidad
3. `status` ENUM válidos: `'pending'`, `'active'`, `'rejected'`, `'suspended'` — no otros valores
4. `role` ENUM válidos: `'user'`, `'admin'`, `'superadmin'`
5. Si cambia email: TRES cambios: `email_verified=0`, `verify_token=nuevo`, `verify_exp=+24h`
6. resetPassword usa PasswordGenerator (ya existe en P05) — NO crear función nueva
7. La paginación usa `$model->paginate(20)` y `$pager` en la vista — API estándar CI4
