## Requirements

### Requirement: Contenedores Docker con nombres exactos
El sistema SHALL levantarse con exactamente tres contenedores: `rcf_app`, `rcf_mysql` y `rcf_maildev`, conectados a la red `red_interna`.

#### Scenario: Build y arranque sin errores
- **WHEN** el usuario ejecuta `docker compose up --build`
- **THEN** los tres contenedores arrancan sin errores de build ni de runtime
- **THEN** `docker ps` muestra `rcf_app`, `rcf_mysql` y `rcf_maildev` con estado `Up`

#### Scenario: Red interna creada
- **WHEN** los contenedores están corriendo
- **THEN** `docker network ls` muestra una red con el nombre que contiene `red_interna`
- **THEN** los tres contenedores pertenecen a esa red

---

### Requirement: Apache sirve el subfolder /comisionfilm
Apache 2.4 en el contenedor `rcf_app` SHALL servir contenido en la ruta `/comisionfilm/` del host mediante un `Alias` en el `vhost.conf`.

#### Scenario: Acceso al subfolder en desarrollo
- **WHEN** el contenedor `rcf_app` está corriendo
- **THEN** `GET http://localhost/comisionfilm/` retorna HTTP 200 o 302 (no 404 ni 403)

#### Scenario: mod_rewrite activo
- **WHEN** se accede a una ruta como `http://localhost/comisionfilm/cualquier-ruta`
- **THEN** Apache no retorna 404 de DirectoryListing (el Alias + AllowOverride All está activo)

---

### Requirement: PHP 8.2 con extensiones requeridas
El contenedor `rcf_app` SHALL tener PHP 8.2 con las extensiones `pdo_mysql`, `intl`, `mbstring`, `gd` y `zip` instaladas.

#### Scenario: Extensiones PHP presentes
- **WHEN** se ejecuta `docker exec rcf_app php -m`
- **THEN** la salida incluye: `pdo_mysql`, `intl`, `mbstring`, `gd`, `zip`

#### Scenario: Versión de PHP correcta
- **WHEN** se ejecuta `docker exec rcf_app php -v`
- **THEN** la salida indica `PHP 8.2.x`

---

### Requirement: MySQL 8 inicializado con la base de datos correcta
El contenedor `rcf_mysql` SHALL inicializar MySQL 8 con la base de datos `registro_comision_film` al primer arranque.

#### Scenario: Base de datos creada automáticamente
- **WHEN** el contenedor `rcf_mysql` arranca por primera vez
- **THEN** la base de datos `registro_comision_film` existe con charset `utf8mb4`

#### Scenario: Conexión desde rcf_app a rcf_mysql
- **WHEN** se ejecuta una consulta de prueba desde `rcf_app` al host `db` puerto 3306
- **THEN** la conexión es exitosa con las credenciales del `.env`

---

### Requirement: Maildev disponible en puerto 1080
El contenedor `rcf_maildev` SHALL exponer la interfaz web de Maildev en `http://localhost:1080`.

#### Scenario: UI de Maildev accesible
- **WHEN** el contenedor `rcf_maildev` está corriendo
- **THEN** `GET http://localhost:1080` retorna HTTP 200 con la UI de Maildev

#### Scenario: SMTP de Maildev accesible desde rcf_app
- **WHEN** el contenedor `rcf_app` intenta conectar a `maildev:1025`
- **THEN** la conexión SMTP es exitosa (los contenedores están en la misma red `red_interna`)

---

### Requirement: PHP.ini con configuración de uploads y timezone
El archivo `docker/php/php.ini` SHALL configurar `upload_max_filesize = 20M`, `post_max_size = 25M` y `date.timezone = America/Mexico_City`.

#### Scenario: Configuración activa en el contenedor
- **WHEN** se ejecuta `docker exec rcf_app php -i | grep upload_max`
- **THEN** la salida muestra `upload_max_filesize => 20M`

#### Scenario: Timezone configurada
- **WHEN** se ejecuta `docker exec rcf_app php -r "echo date_default_timezone_get();"`
- **THEN** la salida es `America/Mexico_City`

---

### Requirement: .gitignore excluye archivos sensibles
El archivo `.gitignore` en la raíz SHALL excluir `.env`, `vendor/`, `writable/cache/`, `writable/logs/`.

#### Scenario: .env no versionado
- **WHEN** existe el archivo `.env` en la raíz
- **THEN** `git status` no lo muestra como archivo a versionar

#### Scenario: vendor no versionado
- **WHEN** existe la carpeta `app/vendor/`
- **THEN** `git status` no la muestra como carpeta a versionar
