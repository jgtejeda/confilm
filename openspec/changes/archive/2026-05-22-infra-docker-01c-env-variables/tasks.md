## 1. Crear .env.example raíz (Docker Compose)

- [x] 1.1 Crear `.env.example` en la raíz con sección `# Docker MySQL` conteniendo:
       `DB_ROOT_PASSWORD=cambia_esto`, `DB_DATABASE=registro_comision_film`, `DB_USERNAME=rcf_user`, `DB_PASSWORD=cambia_esto`
- [x] 1.2 Agregar comentario al inicio: `# Copiar a .env y ajustar los valores`
- [x] 1.3 Verificar que `.env.example` NO está en `.gitignore` (debe ser versionado)

## 2. Crear .env raíz (Docker Compose — no versionado)

- [x] 2.1 Copiar `.env.example` a `.env`
- [x] 2.2 Establecer `DB_ROOT_PASSWORD` con una contraseña de desarrollo (ej: `dev_root_pass_2025`)
- [x] 2.3 Establecer `DB_DATABASE=registro_comision_film`
- [x] 2.4 Establecer `DB_USERNAME=rcf_user`
- [x] 2.5 Establecer `DB_PASSWORD` con una contraseña de desarrollo (ej: `dev_rcf_pass_2025`)
- [x] 2.6 Verificar con `docker compose config` que las variables `${DB_*}` se interpolan sin warnings

## 3. Crear app/.env.example (CI4 — versionado)

- [x] 3.1 Crear `app/.env.example` con sección `# App`:
       `CI_ENVIRONMENT = development`, `app.baseURL = 'http://localhost/comisionfilm/'`, `app.secretKey = 'GENERAR_CON_php_-r_echo_bin2hex(random_bytes(32));'`
- [x] 3.2 Agregar sección `# Base de datos (CI4)`:
       `database.default.hostname = db`, `database.default.database = ${DB_DATABASE}`, `database.default.username = ${DB_USERNAME}`, `database.default.password = ${DB_PASSWORD}`, `database.default.DBDriver = MySQLi`, `database.default.port = 3306`
- [x] 3.3 Agregar sección `# Docker MySQL (para referencia)`:
       `DB_ROOT_PASSWORD = cambia_esto`, `DB_DATABASE = registro_comision_film`, `DB_USERNAME = rcf_user`, `DB_PASSWORD = cambia_esto`
- [x] 3.4 Agregar sección `# Gmail SMTP`:
       `GMAIL_USER = notificaciones@comisionfilm.gob.mx`, `GMAIL_APP_PASSWORD = xxxx xxxx xxxx xxxx`
- [x] 3.5 Agregar sección `# AWS S3`:
       `AWS_ACCESS_KEY_ID = AKIA_PLACEHOLDER`, `AWS_SECRET_ACCESS_KEY = placeholder_secret`, `AWS_REGION = us-east-1`, `AWS_S3_BUCKET = registro-comision-film`
- [x] 3.6 Agregar sección `# Sesiones`:
       `session.driver = 'CodeIgniter\Session\Handlers\DatabaseHandler'`, `session.savePath = ci_sessions`, `session.expiration = 7200`, `session.cookieSecure = false`, `session.cookieSameSite = Strict`
- [x] 3.7 Agregar sección `# Archivos`: `upload.maxSizeMB = 20`

## 4. Crear app/.env (CI4 — no versionado)

- [x] 4.1 Copiar `app/.env.example` a `app/.env`
- [x] 4.2 Establecer `CI_ENVIRONMENT = development`
- [x] 4.3 Establecer `app.baseURL = 'http://localhost/comisionfilm/'`
- [x] 4.4 Generar `app.secretKey`: ejecutar `docker exec rcf_app php -r "echo bin2hex(random_bytes(32));"` y pegar el resultado
- [x] 4.5 Configurar `database.default.hostname = db`
- [x] 4.6 Configurar `database.default.database`, `database.default.username`, `database.default.password` con los mismos valores que el `.env` raíz (sin `${}` — valores literales)

## 5. Verificación

- [x] 5.1 `docker compose config` — sin warnings de variables sin definir
- [x] 5.2 `docker compose restart app` — reiniciar para que tome el `app/.env`
- [x] 5.3 `GET http://localhost/comisionfilm/` — sin errores de configuración CI4 en la página
- [x] 5.4 `docker exec rcf_app php spark env` — muestra `CI_ENVIRONMENT = development`
- [x] 5.5 `git status` — `.env` y `app/.env` NO aparecen; `.env.example` y `app/.env.example` sí aparecen como nuevos archivos

## ⚠️ Anti-alucinación

- `app.baseURL` DEBE ser `'http://localhost/comisionfilm/'` — con `/comisionfilm/` y trailing slash
- `database.default.hostname` DEBE ser `db` — nombre del servicio Docker, NO `localhost`, NO `127.0.0.1`, NO `rcf_mysql`
- Los archivos `.env` (raíz y `app/`) NO se versionan — ya están en `.gitignore`
- Los archivos `.env.example` SÍ se versionan — son documentación de configuración
- `app.secretKey` debe ser un valor real generado, no el placeholder del `.env.example`
- **NO crear** ni mencionar `writable/uploads/` en ningún contexto de configuración
