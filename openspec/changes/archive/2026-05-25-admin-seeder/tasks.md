## 1. Crear el seeder

- [x] 1.1 Crear `app/app/Database/Seeds/AdminUserSeeder.php` que extiende `Seeder`
- [x] 1.2 Definir la contraseña inicial como constante o variable local: `$rawPassword = 'Admin@2025!'` (documentar que debe cambiarse en producción)
- [x] 1.3 Generar el hash: `$hash = password_hash($rawPassword, PASSWORD_BCRYPT, ['cost' => 12])`
- [x] 1.4 Verificar si el admin ya existe: `$exists = $db->table('users')->where('email', 'admin@comisionfilm.gob.mx')->countAllResults()`
- [x] 1.5 Si no existe, insertar: username='admin', email='admin@comisionfilm.gob.mx', phone='0000000000', password_hash=$hash, nombres='Administrador', apellido_pat='Sistema', role='admin', status='active', email_verified=1
- [x] 1.6 Agregar comentario prominente: `// ⚠️ CAMBIAR CONTRASEÑA ANTES DE DEPLOY EN PRODUCCIÓN`

## 2. Verificación

- [x] 2.1 Ejecutar `php spark db:seed AdminUserSeeder` — sin errores
- [x] 2.2 Verificar en MySQL que el usuario existe con `SELECT id, username, email, role, status, email_verified FROM users WHERE role='admin'`
- [x] 2.3 Verificar que password_hash empieza con '$2y$12$' (bcrypt cost 12)
- [x] 2.4 Ejecutar el seeder una segunda vez — confirmar que no crea duplicado ni lanza error
- [x] 2.5 Intentar login con las credenciales del admin — debe funcionar y redirigir a /comisionfilm/admin ✓ (LoginController + admin dashboard creados y verificados)

## ⚠️ Anti-alucinación

- [x] 3.1 El archivo se llama EXACTAMENTE `AdminUserSeeder.php` (PascalCase)
- [x] 3.2 NUNCA insertar la contraseña en texto plano en ningún campo — SOLO el hash bcrypt en password_hash
- [x] 3.3 email_verified DEBE ser 1 — el admin no pasa por verificación de correo
- [x] 3.4 status DEBE ser 'active' — el admin puede hacer login inmediatamente
- [x] 3.5 El seeder NO crea document_types ni periods — solo el usuario admin
- [x] 3.6 Verificar existencia antes de INSERT para hacer el seeder idempotente
