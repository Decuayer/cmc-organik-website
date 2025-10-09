<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// helpers
function columnExists(PDO $pdo, string $table, string $column): bool {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE :col");
    $stmt->execute(['col' => $column]);
    return (bool)$stmt->fetch();
}

// check migration
$needsMigration = false;
$requiredCols = ['is_read', 'folder', 'read_by', 'read_at'];
foreach ($requiredCols as $col) {
    if (!columnExists($pdo, 'contact', $col)) {
        $needsMigration = true;
        break;
    }
}

// SQL to run if migration needed
$migrationSQL = <<<SQL
ALTER TABLE contact
  ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN folder VARCHAR(20) NOT NULL DEFAULT 'inbox',
  ADD COLUMN read_by INT(11) DEFAULT NULL,
  ADD COLUMN read_at DATETIME DEFAULT NULL;
SQL;

// current admin
$currentAdminId = $_SESSION['admin_id'] ?? null;
$currentAdminName = $_SESSION['admin_username'] ?? null;

// handle actions (only if migration applied)
if (!$needsMigration && isset($_GET['action']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];
    $redirect = 'contact.php';
    // preserve filter param if provided
    if (!empty($_GET['filter'])) {
        $redirect .= '?filter=' . urlencode($_GET['filter']);
    }

    if ($action === 'mark_read') {
        $stmt = $pdo->prepare("UPDATE contact SET is_read = 1, read_by = :admin, read_at = NOW() WHERE id = :id");
        $stmt->execute(['admin' => $currentAdminId, 'id' => $id]);
        header("Location: $redirect");
        exit;
    }

    if ($action === 'mark_unread') {
        $stmt = $pdo->prepare("UPDATE contact SET is_read = 0, read_by = NULL, read_at = NULL WHERE id = :id");
        $stmt->execute(['id' => $id]);
        header("Location: $redirect");
        exit;
    }

    if ($action === 'move_important') {
        $stmt = $pdo->prepare("UPDATE contact SET folder = 'important' WHERE id = :id");
        $stmt->execute(['id' => $id]);
        header("Location: $redirect");
        exit;
    }

    if ($action === 'move_inbox') {
        $stmt = $pdo->prepare("UPDATE contact SET folder = 'inbox' WHERE id = :id");
        $stmt->execute(['id' => $id]);
        header("Location: $redirect");
        exit;
    }

    if ($action === 'move_trash') {
        $stmt = $pdo->prepare("UPDATE contact SET folder = 'trash' WHERE id = :id");
        $stmt->execute(['id' => $id]);
        header("Location: $redirect");
        exit;
    }

    if ($action === 'delete_perm') {
        // kalıcı silme
        $stmt = $pdo->prepare("DELETE FROM contact WHERE id = :id");
        $stmt->execute(['id' => $id]);
        header("Location: $redirect");
        exit;
    }
}

// counts & filters
$filter = $_GET['filter'] ?? 'inbox';
$counts = [
    'all' => 0,
    'inbox' => 0,
    'unread' => 0,
    'important' => 0,
    'trash' => 0,
];

if (!$needsMigration) {
    // compute counts
    $counts['all'] = (int)$pdo->query("SELECT COUNT(*) FROM contact")->fetchColumn();
    $counts['inbox'] = (int)$pdo->query("SELECT COUNT(*) FROM contact WHERE folder = 'inbox'")->fetchColumn();
    $counts['unread'] = (int)$pdo->query("SELECT COUNT(*) FROM contact WHERE is_read = 0 AND folder != 'trash'")->fetchColumn();
    $counts['important'] = (int)$pdo->query("SELECT COUNT(*) FROM contact WHERE folder = 'important'")->fetchColumn();
    $counts['trash'] = (int)$pdo->query("SELECT COUNT(*) FROM contact WHERE folder = 'trash'")->fetchColumn();
} else {
    // fallback: only total
    $counts['all'] = (int)$pdo->query("SELECT COUNT(*) FROM contact")->fetchColumn();
    $counts['inbox'] = $counts['all'];
    $counts['unread'] = 0;
    $counts['important'] = 0;
    $counts['trash'] = 0;
}

// build query depending on filter
$params = [];
$where = "";
if ($needsMigration) {
    // simple fallback: show all messages (no is_read/folder)
    $where = "";
} else {
    switch ($filter) {
        case 'all':
            $where = ""; break;
        case 'unread':
            $where = " WHERE c.is_read = 0 AND c.folder != 'trash'"; break;
        case 'important':
            $where = " WHERE c.folder = 'important'"; break;
        case 'trash':
            $where = " WHERE c.folder = 'trash'"; break;
        case 'inbox':
        default:
            $where = " WHERE c.folder = 'inbox'"; break;
    }
}

// Fetch messages with join to admins for read_by
if ($needsMigration) {
    $sql = "SELECT c.* FROM contact c ORDER BY c.created_at DESC";
    $stmt = $pdo->query($sql);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $sql = "SELECT c.*, a.name AS read_by_name
            FROM contact c
            LEFT JOIN admins a ON c.read_by = a.id
            $where
            ORDER BY c.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<style>
/* Tablo sabit sütun düzeni: sütun genişlikleri bu sınıflarla kontrol edilecek */
.table-fixed {
    table-layout: fixed;
    width: 100%;
}

/* Genel hücre stili (daha sıkışık görünüm) */
.table-fixed th, .table-fixed td {
    vertical-align: middle !important;
    padding: 8px 10px;
    line-height: 1.2;
}

/* Sütun genişlikleri — istediğin gibi ayarla */
.table-fixed th.col-index, .table-fixed td.col-index { width: 48px; }
.table-fixed th.col-name,  .table-fixed td.col-name  { width: 150px; }
.table-fixed th.col-email, .table-fixed td.col-email { width: 220px; }
.table-fixed th.col-phone, .table-fixed td.col-phone { width: 120px; }
.table-fixed th.col-subject,.table-fixed td.col-subject{ width: 20%; } /* ayarla */
.table-fixed th.col-message,.table-fixed td.col-message{ width: 28%; } /* ayarla */
.table-fixed th.col-status, .table-fixed td.col-status{ width: 120px; }
.table-fixed th.col-date, .table-fixed td.col-date { width: 140px; }
.table-fixed th.col-actions, .table-fixed td.col-actions { width: 220px; }

/* Truncate (tek satır: üç nokta) */
.truncate {
    display: block;
    width: 100%;
    overflow: hidden;
    white-space: nowrap;         /* TEK SATIR */
    text-overflow: ellipsis;
    -webkit-box-orient: vertical;
    cursor: help; /* hover için ipucu */
}

/* Tooltip için küçük styling (isteğe bağlı) */
.truncate[title] { text-decoration: none; }

/* Mobilde yatay scroll - responsive */
@media (max-width: 992px) {
    .table-responsive { overflow-x: auto; }
    /* Opsiyonel: subject/message column width daha küçük */
    .table-fixed th.col-subject,.table-fixed td.col-subject{ width: 160px; }
    .table-fixed th.col-message,.table-fixed td.col-message{ width: 200px; }
}

.col-name, .col-email, .col-phone {
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Eğer bootstrap tooltip yüklüyse aktif et
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('.truncate[title]'));
        tooltipTriggerList.forEach(function (el) {
            new bootstrap.Tooltip(el, {container: 'body'});
        });
    }
    // aksi halde, title attribute tarayıcının native tooltip'ini gösterir
});
</script>

<div class="content" id="content" style="padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">Gönderilen Mesajlar</h4>
        <div>
            <a href="contact.php?filter=inbox" class="btn btn-outline-secondary btn-sm <?= $filter==='inbox'?'active':'' ?>">Gelen (<?= $counts['inbox'] ?>)</a>
            <a href="contact.php?filter=unread" class="btn btn-outline-warning btn-sm <?= $filter==='unread'?'active':'' ?>">Yeni (<?= $counts['unread'] ?>)</a>
            <a href="contact.php?filter=important" class="btn btn-outline-primary btn-sm <?= $filter==='important'?'active':'' ?>">Önemli (<?= $counts['important'] ?>)</a>
            <a href="contact.php?filter=trash" class="btn btn-outline-danger btn-sm <?= $filter==='trash'?'active':'' ?>">Çöp (<?= $counts['trash'] ?>)</a>
            <a href="contact.php?filter=all" class="btn btn-outline-info btn-sm <?= $filter==='all'?'active':'' ?>">Hepsi (<?= $counts['all'] ?>)</a>
        </div>
    </div>

    <?php if ($needsMigration): ?>
        <div class="alert alert-warning">
            <strong>Veritabanı güncellemesi gerekli.</strong>
            <p>Mesaj yönetiminin tam fonksiyonel olması için aşağıdaki SQL'i veritabanınızda çalıştırmalısınız (phpMyAdmin veya MySQL client ile):</p>
            <pre style="background:#f8f9fa; padding:10px; border-radius:4px;"><?= htmlspecialchars($migrationSQL) ?></pre>
            <p>Bu işlemden sonra okundu/önemli/çöp gibi işlemler aktif hale gelecektir.</p>
        </div>
    <?php endif; ?>

    <?php if (count($messages) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle table-hover table-fixed">
                <thead class="table-success">
                    <tr>
                        <th class="col-index">#</th>
                        <th class="col-name">Ad Soyad</th>
                        <th class="col-email">Email</th>
                        <th class="col-phone">Telefon</th>
                        <th class="col-subject">Konu</th>
                        <th class="col-message">Mesaj</th>
                        <th class="col-status">Durum</th>
                        <th class="col-date">Tarih</th>
                        <th class="col-actions">İşlemler</th>
                    </tr>İşlemler</th>
                </thead>
                <tbody>
                    <?php foreach ($messages as $index => $msg): ?>
                        <tr class="<?= (!$needsMigration && $msg['is_read']==0 && ($msg['folder'] ?? 'inbox') !== 'trash') ? 'table-warning' : '' ?>">
                            <td class="col-index"><?= $index + 1 ?></td>
                            <td class="col-name"><?= htmlspecialchars($msg['name'] ?? '-') ?></td>
                            <td class="col-email"><?= htmlspecialchars($msg['email'] ?? '-') ?></td>
                            <td class="col-phone"><?= htmlspecialchars($msg['phone'] ?? '-') ?></td>

                            <!-- SUBJECT: tek satır, tooltip ile tam metin -->
                            <td class="col-subject">
                                <div class="truncate" title="<?= htmlspecialchars($msg['subject'] ?? '-') ?>">
                                    <?= htmlspecialchars($msg['subject'] ?? '-') ?>
                                </div>
                            </td>                            
                            
                            <!-- MESSAGE: tek satır görünüp "Devamını Gör" linki ile detay -->
                            <td class="col-message">
                                <div class="truncate" title="<?= htmlspecialchars($msg['message'] ?? '') ?>">
                                    <?= htmlspecialchars(mb_strimwidth($msg['message'] ?? '', 0, 300, '...')) ?>
                                </div>
                                <a href="message_view.php?id=<?= $msg['id'] ?>" class="text-success d-block mt-1" style="font-size:0.9em;">Devamını Gör</a>
                            </td>        
                            
                            <td class="col-status text-center">
                                <?php if ($needsMigration): ?>
                                    <span class="badge bg-secondary">—</span>
                                <?php else: ?>
                                    <?php if ($msg['folder'] === 'trash'): ?>
                                        <span class="badge bg-danger">Çöp</span>
                                    <?php elseif ($msg['folder'] === 'important'): ?>
                                        <span class="badge bg-primary">Önemli</span>
                                    <?php else: ?>
                                        <span class="badge bg-info">Gelen</span>
                                    <?php endif; ?>

                                    <br>
                                    <?php if ($msg['is_read']): ?>
                                        <small class="text-success">Okundu (<?= htmlspecialchars($msg['read_by_name'] ?? '-') ?>)</small>
                                    <?php else: ?>
                                        <small class="text-warning">Yeni</small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($msg['created_at'] ?? '-') ?></td>
                            <td class="text-center align-middle">
                                <a href="message_view.php?id=<?= $msg['id'] ?>" class="btn btn-sm btn-success my-1">Görüntüle</a>

                                <?php if ($needsMigration): ?>
                                    <button class="btn btn-sm btn-secondary my-1" disabled title="DB güncellemesi gerekli">Okundu</button>
                                <?php else: ?>
                                    <?php if (!$msg['is_read']): ?>
                                        <a href="contact.php?action=mark_read&id=<?= $msg['id'] ?>&filter=<?= urlencode($filter) ?>" class="btn btn-sm btn-primary my-1">Okundu Yap</a>
                                    <?php else: ?>
                                        <a href="contact.php?action=mark_unread&id=<?= $msg['id'] ?>&filter=<?= urlencode($filter) ?>" class="btn btn-sm btn-warning my-1">Okunmadı Yap</a>
                                    <?php endif; ?>

                                    <?php if ($msg['folder'] !== 'important'): ?>
                                        <a href="contact.php?action=move_important&id=<?= $msg['id'] ?>&filter=<?= urlencode($filter) ?>" class="btn btn-sm btn-info my-1">Önemli</a>
                                    <?php else: ?>
                                        <a href="contact.php?action=move_inbox&id=<?= $msg['id'] ?>&filter=<?= urlencode($filter) ?>" class="btn btn-sm btn-secondary my-1">Önemli Kaldır</a>
                                    <?php endif; ?>

                                    <?php if ($msg['folder'] !== 'trash'): ?>
                                        <a href="contact.php?action=move_trash&id=<?= $msg['id'] ?>&filter=<?= urlencode($filter) ?>" class="btn btn-sm btn-danger my-1" onclick="return confirm('Mesajı çöp klasörüne taşımak istediğinize emin misiniz?');">Çöp</a>
                                    <?php else: ?>
                                        <a href="contact.php?action=move_inbox&id=<?= $msg['id'] ?>&filter=<?= urlencode($filter) ?>" class="btn btn-sm btn-success my-1">Geri Al</a>
                                        <a href="contact.php?action=delete_perm&id=<?= $msg['id'] ?>&filter=<?= urlencode($filter) ?>" class="btn btn-sm btn-danger my-1" onclick="return confirm('Mesajı kalıcı olarak silmek istediğinize emin misiniz? Bu işlem geri alınamaz.');">Kalıcı Sil</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Henüz gönderilen mesaj yok.</div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
