## Requirements

### Requirement: .env raíz contiene variables para Docker Compose
El archivo `.env` en la raíz del proyecto SHALL contener las variables `DB_ROOT_PASSWORD`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` con valores de desarrollo (no vacíos).

#### Scenario: Docker Compose interpola las variables
- **WHEN** se ejecuta `docker compose config`
- **THEN** no aparecen warnings de variables sin definir (`${DB_ROOT_PASSWORD}` resuelto, etc.)

#### Scenario: .env raíz no versionado
- **WHEN** se ejecuta `git status` con el archivo `.env` presente
- **THEN** el archivo `.env` no aparece como tracked ni como untracked a commitear

---

### Requirement: app/.env contiene todas las variables de CI4
El archivo `app/.env` SHALL contener las claves definidas en ARQUITECTURA.md §18: `CI_ENVIRONMENT`, `app.baseURL`, `app.secretKey`, `database.default.*`, `session.*`, `GMAIL_*`, `AWS_*`.

#### Scenario: CI_ENVIRONMENT configurado para desarrollo
- **WHEN** se lee `app/.env`
- **THEN** contiene `CI_ENVIRONMENT = development`

#### Scenario: app.baseURL con subfolder correcto
- **WHEN** se lee `app/.env`
- **THEN** contiene `app.baseURL = 'http://localhost/comisionfilm/'` (con subfolder y trailing slash)

#### Scenario: database.default.hostname apunta al servicio Docker
- **WHEN** se lee `app/.env`
- **THEN** contiene `database.default.hostname = db` (nombre del servicio, no localhost ni 127.0.0.1)

#### Scenario: app/.env no versionado
- **WHEN** se ejecuta `git status` con `app/.env` presente
- **THEN** el archivo no aparece como archivo a commitear

---

### Requirement: .env.example raíz documenta todas las variables de Docker
El archivo `.env.example` en la raíz SHALL contener todas las variables de Docker Compose con valores placeholder y comentarios explicativos.

#### Scenario: .env.example contiene las variables DB_*
- **WHEN** se lee `.env.example`
- **THEN** contiene `DB_ROOT_PASSWORD`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` con valores de ejemplo

#### Scenario: .env.example está versionado
- **WHEN** se ejecuta `git add .env.example`
- **THEN** git lo acepta (no está en `.gitignore`)

---

### Requirement: app/.env.example documenta todas las variables de CI4
El archivo `app/.env.example` SHALL contener todas las variables de CI4 con valores placeholder, siguiendo exactamente las claves de ARQUITECTURA.md §18.

#### Scenario: app/.env.example contiene las claves críticas
- **WHEN** se lee `app/.env.example`
- **THEN** contiene las secciones: App, Base de datos, Docker MySQL, Gmail, AWS S3, Sesiones

#### Scenario: app/.env.example cubre variables AWS con placeholder
- **WHEN** se lee `app/.env.example`
- **THEN** contiene `AWS_ACCESS_KEY_ID = AKIA_PLACEHOLDER`, `AWS_SECRET_ACCESS_KEY = placeholder`, `AWS_REGION = us-east-1`, `AWS_S3_BUCKET = nombre-del-bucket`

---

### Requirement: CI4 conecta a MySQL con la configuración del app/.env
Con el `app/.env` correctamente configurado, CI4 SHALL poder conectarse a la base de datos `registro_comision_film` en el contenedor `rcf_mysql`.

#### Scenario: Conexión a base de datos exitosa
- **WHEN** el contenedor `rcf_mysql` está corriendo y `app/.env` tiene `database.default.hostname = db`
- **THEN** `docker exec rcf_app php spark db:connect` termina sin error de conexión
