## 1. Verificación previa

- [x] 1.1 Verificar columnas `notifications`: user_id, sender_id, type, title, body, send_email, email_sent_at (P02)
- [x] 1.2 Verificar rutas `/admin/notificaciones*` en Routes.php (P07)
- [x] 1.3 `type` ENUM válidos: 'info','success','warning','error','document','inscription'

## 2. Admin\NotificationController

- [x] 2.1 `index()`: historial de notificaciones enviadas por el admin (`WHERE sender_id=session('user_id')`) + formulario de envío
- [x] 2.2 `send()`: validar target_type, title (required), body (required); si 'user': INSERT 1 notificación; si 'group': SELECT ids por status, loop INSERT + MailService opcional
- [x] 2.3 sender_id = `session('user_id')` — NUNCA NULL
- [x] 2.4 Loop de envío masivo: iterar sobre array de `['id','email','nombres']` — NO cargar objetos UserModel completos
- [x] 2.5 Si MailService falla en algún usuario: log y continuar con los demás

## 3. Vista admin/notifications.php

- [x] 3.1 Select target_type (usuario/grupo), condicional: si usuario → buscar por email; si grupo → select status
- [x] 3.2 Inputs title y body (textarea), checkbox send_email
- [x] 3.3 Tabla de historial de notificaciones enviadas por este admin

## 4. Verificación final

- [x] 4.1 Enviar a usuario individual → 1 notificación en DB con sender_id correcto
- [x] 4.2 Enviar a grupo 'pending' → N notificaciones insertadas
- [x] 4.3 sender_id en DB = session('user_id') del admin — nunca NULL

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. `sender_id = session('user_id')` — nunca NULL (NULL es solo para notificaciones automáticas del sistema)
2. Loop de envío masivo: SELECT solo `id, email, nombres` — NO cargar todo el objeto usuario
3. MailService::sendAdminMessage() retorna bool — si false: loggear y continuar (no abortar el loop)
4. `type` para mensajes del admin = `'info'` por defecto — si el admin puede elegir tipo, usar los ENUM válidos
5. Las rutas ya existen (P07) — NO recrear en Routes.php
