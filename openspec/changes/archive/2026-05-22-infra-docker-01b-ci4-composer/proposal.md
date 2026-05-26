## Why

Con Docker ya corriendo (Bloque 1), este bloque instala CodeIgniter 4 dentro del contenedor `rcf_app` y lo configura para que funcione bajo el subfolder `/comisionfilm/`. Sin esta configuración, Apache sirve el `public/` de CI4 pero las rutas y assets usan URLs incorrectas porque CI4 no sabe que vive en un subfolder.

## What Changes

- `app/` — directorio creado por `composer create-project codeigniter4/appstarter app`
- `app/vendor/` — dependencias instaladas: `aws/aws-sdk-php ^3.0` y `phpmailer/phpmailer ^6.8`
- `app/public/.htaccess` — `RewriteBase /comisionfilm/` + headers de seguridad
- `app/app/Config/App.php` — `$baseURL = 'http://localhost/comisionfilm/'`
- `app/app/Config/Database.php` — credenciales leídas desde `$_ENV` (variables del `.env` de CI4)
- **NO se crea** `writable/uploads/` — los archivos van a AWS S3

## Capabilities

### New Capabilities

- `ci4-subfolder-config`: CodeIgniter 4 instalado y configurado para operar bajo el subfolder `/comisionfilm/` tanto en dev como en prod, con dependencias AWS SDK y PHPMailer instaladas vía Composer

### Modified Capabilities

_(ninguna — primera instalación de CI4)_

## Impact

- **Dependencia**: Requiere que `rcf_app` esté corriendo (Bloque 1 completado)
- **Archivos nuevos**: todo el árbol `app/` generado por CI4 appstarter
- **Archivos clave modificados post-install**: `app/public/.htaccess`, `app/app/Config/App.php`, `app/app/Config/Database.php`
- **Composer packages**: `codeigniter4/framework ^4.5`, `aws/aws-sdk-php ^3.0`, `phpmailer/phpmailer ^6.8`
- **NO afecta**: archivos Docker del Bloque 1
