## 1. Dockerfile — PHP 8.2 + Apache

- [x] 1.1 Crear `docker/apache/Dockerfile` con `FROM php:8.2-apache`
- [x] 1.2 Agregar `RUN a2enmod rewrite headers ssl`
- [x] 1.3 Agregar `RUN apt-get update && apt-get install -y libzip-dev zip unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev`
- [x] 1.4 Agregar `RUN docker-php-ext-install pdo pdo_mysql zip gd intl mbstring`
- [x] 1.5 Copiar Composer: `COPY --from=composer:latest /usr/bin/composer /usr/bin/composer`
- [x] 1.6 Agregar `WORKDIR /var/www/html`

## 2. VirtualHost Apache con Alias /comisionfilm

- [x] 2.1 Crear `docker/apache/vhost.conf` con `<VirtualHost *:80>` y `ServerName localhost`
- [x] 2.2 Agregar `DocumentRoot /var/www/html/public` en el VirtualHost
- [x] 2.3 Agregar el bloque `Alias /comisionfilm /var/www/html/public` con `<Directory>` `AllowOverride All` y `Require all granted`
- [x] 2.4 Agregar `<FilesMatch "\.(env|git|htaccess|log)$"> Require all denied </FilesMatch>`
- [x] 2.5 Agregar `ErrorLog ${APACHE_LOG_DIR}/error.log` y `CustomLog ${APACHE_LOG_DIR}/access.log combined`

## 3. PHP.ini

- [x] 3.1 Crear `docker/php/php.ini` con `upload_max_filesize = 20M`
- [x] 3.2 Agregar `post_max_size = 25M`, `max_execution_time = 60`, `memory_limit = 256M`
- [x] 3.3 Agregar `display_errors = On`, `log_errors = On`
- [x] 3.4 Agregar `date.timezone = America/Mexico_City`

## 4. MySQL init.sql

- [x] 4.1 Crear `docker/mysql/init.sql` con `CREATE DATABASE IF NOT EXISTS registro_comision_film CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`

## 5. docker-compose.yml

- [x] 5.1 Crear `docker-compose.yml` con `version: '3.9'`
- [x] 5.2 Definir servicio `app` con `container_name: rcf_app`, `build: context: ./docker/apache`, `ports: "80:80"`
- [x] 5.3 Agregar volumes al servicio `app`: `./app:/var/www/html`, `./docker/apache/vhost.conf:/etc/apache2/sites-enabled/000-default.conf`, `./docker/php/php.ini:/usr/local/etc/php/conf.d/custom.ini`
- [x] 5.4 Agregar al servicio `app`: `networks: [red_interna]`, `depends_on: [db]`, `environment: CI_ENVIRONMENT=development`
- [x] 5.5 Definir servicio `db` con `container_name: rcf_mysql`, `image: mysql:8.0`, `restart: always`
- [x] 5.6 Agregar `environment` al servicio `db`: `MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}`, `MYSQL_DATABASE: ${DB_DATABASE}`, `MYSQL_USER: ${DB_USERNAME}`, `MYSQL_PASSWORD: ${DB_PASSWORD}`
- [x] 5.7 Agregar volumes al servicio `db`: `mysql_data:/var/lib/mysql`, `./docker/mysql/init.sql:/docker-entrypoint-initdb.d/init.sql`, y `ports: "3306:3306"`, `networks: [red_interna]`
- [x] 5.8 Definir servicio `maildev` con `container_name: rcf_maildev`, `image: maildev/maildev`, `ports: ["1080:1080", "1025:1025"]`, `networks: [red_interna]`
- [x] 5.9 Declarar `volumes: mysql_data:` y `networks: red_interna: driver: bridge`

## 6. .gitignore

- [x] 6.1 Crear `.gitignore` en la raíz con las entradas: `.env`, `vendor/`, `app/vendor/`, `writable/cache/`, `writable/logs/`, `.DS_Store`, `*.log`, `docker/mysql/data/`

## 7. Verificación

- [x] 7.1 Ejecutar `docker compose up --build` y confirmar que no hay errores
- [x] 7.2 Verificar con `docker ps` que aparecen `rcf_app`, `rcf_mysql`, `rcf_maildev` con estado `Up`
- [x] 7.3 Verificar con `docker network ls` que existe la red `red_interna`
- [x] 7.4 Verificar extensiones PHP: `docker exec rcf_app php -m | grep -E "pdo_mysql|intl|mbstring|gd|zip"`
- [x] 7.5 Verificar Maildev: `GET http://localhost:1080` retorna 200

## ⚠️ Anti-alucinación

- Servidor web: **Apache 2.4** — NO Nginx en ningún lugar
- Nombres de contenedores **EXACTOS**: `rcf_app`, `rcf_mysql`, `rcf_maildev`
- Red Docker: **`red_interna`**
- El `Alias /comisionfilm` en `vhost.conf` es **OBLIGATORIO** — sin él `http://localhost/comisionfilm/` da 404
- **NO** crear carpeta `writable/uploads/` — los archivos van a AWS S3 (se configura en propuestas posteriores)
- Las extensiones PHP `intl` y `zip` son requeridas por CI4 y aws-sdk-php respectivamente
