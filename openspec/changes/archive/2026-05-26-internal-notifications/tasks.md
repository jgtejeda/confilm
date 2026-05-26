## 1. Verificación previa

- [x] 1.1 Verificar columnas `notifications`: user_id, sender_id, type, title, body, read_at, send_email, email_sent_at, created_at (P02)
- [x] 1.2 Verificar NotificationModel.$allowedFields (P02)
- [x] 1.3 Verificar rutas /dashboard/notificaciones/* en Routes.php (P07)
- [x] 1.4 Verificar tipo ENUM válidos: 'info','success','warning','error','document','inscription'

## 2. notification_helper.php

- [x] 2.1 Crear `app/app/Helpers/notification_helper.php` — función `create_notification(int $userId, string $type, string $title, string $body, bool $sendEmail=false): void`
- [x] 2.2 Obtener DB: `$db = \Config\Database::connect();`
- [x] 2.3 INSERT notifications con: user_id, sender_id=NULL, type, title, body, send_email, created_at=NOW()
- [x] 2.4 Si `$sendEmail`: llamar MailService si está disponible (opcional, loggear si falla), UPDATE email_sent_at=NOW()

## 3. Cargar helper en BaseController

- [x] 3.1 En `BaseController.php`: agregar `'notification'` al array `$this->helpers`

## 4. NotificationController

- [x] 4.1 Crear `Controllers/User/NotificationController.php` namespace `App\Controllers\User`
- [x] 4.2 `index()`: `$notifModel->where('user_id',session('user_id'))->orderBy('created_at','DESC')->paginate(20)`; retornar vista
- [x] 4.3 `markRead($id)`: ownership check, UPDATE read_at=NOW(), retornar JSON `{success:true}`
- [x] 4.4 `unreadCount()`: COUNT WHERE user_id=session('user_id') AND read_at IS NULL, retornar JSON `{count:N}`
- [x] 4.5 Todos los métodos que retornan JSON usan `$this->response->setJSON()`

## 5. Vista user/notifications.php

- [x] 5.1 Crear `views/user/notifications.php` — lista paginada de notificaciones
- [x] 5.2 Cada notificación: ícono por tipo (color según type), título, cuerpo, fecha relativa (calculada en JS)
- [x] 5.3 Botón "Marcar leída" con fetch AJAX, onclick actualiza visual (atenuar la notificación)
- [x] 5.4 `<time class="notif-date" data-date="<?= $n['created_at'] ?>">` → JS calcula fecha relativa al cargar

## 6. Verificación final

- [x] 6.1 `create_notification(userId, 'document', ...)` → INSERT en notifications con sender_id=NULL
- [x] 6.2 GET /dashboard/notificaciones/count → JSON con conteo correcto
- [x] 6.3 POST /dashboard/notificaciones/leer (propia notif) → read_at actualizado
- [x] 6.4 POST /dashboard/notificaciones/leer (notif ajena) → 403
- [x] 6.5 Polling en dashboard: badge actualiza cada 30s
- [x] 6.6 Vista muestra fecha relativa correcta ("hace 5 min", "hace 2 h")

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. `create_notification` es función PHP, NO clase — no `new NotificationHelper()`
2. `sender_id = NULL` para notificaciones del sistema — el admin usa su session('user_id') (P16)
3. Cargar helper en BaseController con `$this->helpers[] = 'notification'` — NO en cada controller individual
4. `read_at IS NULL` — condición para no leída; `read_at IS NOT NULL` para leída — nunca comparar con 0 o false
5. `unreadCount()` usa `countAllResults()` — NO cargar todos los registros en memoria
6. Ownership en markRead: `WHERE id=? AND user_id=session('user_id')` — si no existe el par: 403
7. La fecha relativa se calcula en JS con `Date.now() - new Date(dateStr)` — no en PHP
8. type ENUM válidos: `'info','success','warning','error','document','inscription'` — sin 'notification' ni otros
