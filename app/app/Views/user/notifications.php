<style>
.notif-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.notif-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.notif-header h1 {
    font-size: 1.5rem;
    color: #1a1a1a;
    margin: 0;
}

.notif-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.notif-item {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
    transition: opacity 0.3s ease;
}

.notif-item.read {
    opacity: 0.6;
}

.notif-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.2rem;
}

.notif-icon.info { background: #dbeafe; color: #1d4ed8; }
.notif-icon.success { background: #dcfce7; color: #16a34a; }
.notif-icon.warning { background: #fef3c7; color: #d97706; }
.notif-icon.error { background: #fee2e2; color: #dc2626; }
.notif-icon.document { background: #e0e7ff; color: #4f46e5; }
.notif-icon.inscription { background: #f3e8ff; color: #9333ea; }

.notif-content {
    flex: 1;
    min-width: 0;
}

.notif-title {
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.25rem;
}

.notif-body {
    color: #6b7280;
    font-size: 0.875rem;
    line-height: 1.4;
}

.notif-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
}

.notif-date {
    font-size: 0.75rem;
    color: #9ca3af;
}

.notif-mark-read {
    background: none;
    border: 1px solid #d1d5db;
    border-radius: 0.25rem;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    color: #4b5563;
    cursor: pointer;
    transition: all 0.2s ease;
}

.notif-mark-read:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.notif-empty {
    text-align: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.notif-pagination {
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
}
</style>

<div class="notif-container">
    <div class="notif-header">
        <h1>Notificaciones</h1>
    </div>

    <?php if (empty($notifications)): ?>
        <div class="notif-empty">
            <p>No tienes notificaciones</p>
        </div>
    <?php else: ?>
        <div class="notif-list">
            <?php foreach ($notifications as $n): ?>
                <div class="notif-item <?= !empty($n['read_at']) ? 'read' : '' ?>" id="notif-<?= $n['id'] ?>">
                    <div class="notif-icon <?= esc($n['type']) ?>">
                        <?php
                            $icons = [
                                'info' => 'ℹ️',
                                'success' => '✅',
                                'warning' => '⚠️',
                                'error' => '❌',
                                'document' => '📄',
                                'inscription' => '📝',
                            ];
                            echo $icons[$n['type']] ?? 'ℹ️';
                        ?>
                    </div>
                    <div class="notif-content">
                        <div class="notif-title"><?= esc($n['title']) ?></div>
                        <div class="notif-body"><?= esc($n['body']) ?></div>
                        <div class="notif-footer">
                            <time class="notif-date" data-date="<?= esc($n['created_at']) ?>"></time>
                            <?php if (empty($n['read_at'])): ?>
                                <button class="notif-mark-read" onclick="markAsRead(<?= $n['id'] ?>)">Marcar leída</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($pager->getPageCount() > 1): ?>
            <div class="notif-pagination">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function timeAgo(dateStr) {
    const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
    if (diff < 60) return 'hace ' + diff + ' seg';
    if (diff < 3600) return 'hace ' + Math.floor(diff / 60) + ' min';
    if (diff < 86400) return 'hace ' + Math.floor(diff / 3600) + ' h';
    return 'hace ' + Math.floor(diff / 86400) + ' días';
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.notif-date').forEach(function(el) {
        el.textContent = timeAgo(el.getAttribute('data-date'));
    });
});

function markAsRead(id) {
    fetch('/dashboard/notificaciones/leer/' + id, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(function(response) {
        if (response.ok) {
            var item = document.getElementById('notif-' + id);
            if (item) {
                item.classList.add('read');
                var btn = item.querySelector('.notif-mark-read');
                if (btn) btn.remove();
            }
        }
    })
    .catch(function(err) {
        console.error('Error marking notification as read:', err);
    });
}
</script>
