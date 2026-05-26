## Why

El proyecto necesita un entorno Docker reproducible con Apache 2.4 (no Nginx) que replique el subfolder `/comisionfilm` de producción desde el primer `docker compose up`. Sin esta base, no hay entorno donde instalar CI4 ni verificar que las rutas funcionan.

## What Changes

- `docker-compose.yml`: define servicios `rcf_app` (PHP 8.2 + Apache), `rcf_mysql` (MySQL 8), `rcf_maildev` en la red `red_interna`
- `docker/apache/Dockerfile`: imagen `php:8.2-apache` con `mod_rewrite`, `mod_headers`, extensiones PHP requeridas y Composer
- `docker/apache/vhost.conf`: VirtualHost con `Alias /comisionfilm` apuntando a `/var/www/html/public` — obligatorio para el subfolder en desarrollo
- `docker/php/php.ini`: `upload_max_filesize 20M`, `post_max_size 25M`, `timezone America/Mexico_City`
- `docker/mysql/init.sql`: `CREATE DATABASE IF NOT EXISTS registro_comision_film`
- `.gitignore`: excluye `.env`, `vendor/`, `writable/cache/`, `writable/logs/`

## Capabilities

### New Capabilities

- `docker-apache-setup`: Infraestructura Docker con Apache 2.4, PHP 8.2, MySQL 8 y Maildev, configurada para servir el subfolder `/comisionfilm` en desarrollo local

### Modified Capabilities

_(ninguna — proyecto nuevo, sin specs existentes)_

## Impact

- **Dependencias**: Requiere Docker y Docker Compose instalados (OrbStack disponible)
- **Red**: crea la red Docker `red_interna` (bridge)
- **Volúmenes**: crea volumen persistente `mysql_data`
- **Puertos expuestos**: 80 (Apache), 3306 (MySQL), 1080/1025 (Maildev)
- **NO afecta**: código de aplicación CI4 — eso es Bloque 2
