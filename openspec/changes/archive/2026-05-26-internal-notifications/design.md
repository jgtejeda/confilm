## Context

`notifications` columnas: id, user_id, sender_id (NULL=sistema), type ENUM('info','success','warning','error','document','inscription'), title, body, read_at, send_email, email_sent_at. NotificationModel ya existe. Polling ya implementado en user.php (P19).

## Decisions

### D1 — create_notification() como función PHP helper (no clase)
```php
// app/Helpers/notification_helper.php
function create_notification(int $userId, string $type, string $title, string $body, bool $sendEmail = false): void {
    $db = \Config\Database::connect();
    $db->table('notifications')->insert([
        'user_id'    => $userId,
        'sender_id'  => NULL, // sistema automático
        'type'       => $type,
        'title'      => $title,
        'body'       => $body,
        'send_email' => (int)$sendEmail,
        'created_at' => date('Y-m-d H:i:s'),
    ]);
}
```
Cargar en BaseController: `$this->helpers[] = 'notification';`

### D2 — markRead() con ownership
```php
$notif = $notifModel->where('id',$id)->where('user_id',session('user_id'))->first();
if (!$notif) return $this->response->setStatusCode(403)->setJSON(['error'=>'Forbidden']);
$notifModel->update($id, ['read_at' => date('Y-m-d H:i:s')]);
```

### D3 — unreadCount() para polling
```php
$count = $notifModel->where('user_id',session('user_id'))->where('read_at',NULL)->countAllResults();
return $this->response->setJSON(['count' => $count]);
```

### D4 — Fecha relativa en JS
```javascript
function timeAgo(dateStr) {
    const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
    if (diff < 60) return 'hace '+diff+' seg';
    if (diff < 3600) return 'hace '+Math.floor(diff/60)+' min';
    if (diff < 86400) return 'hace '+Math.floor(diff/3600)+' h';
    return 'hace '+Math.floor(diff/86400)+' días';
}
```
