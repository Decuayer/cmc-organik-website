<?php
require_once __DIR__ . '/../../config/env.php';

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// id kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: contact.php");
    exit;
}
$id = (int)$_GET['id'];

// check migration (aynı fonksiyon kullanılabilir, basit kontrol)
$stmtCheck = $pdo->query("SHOW COLUMNS FROM contact LIKE 'is_read'");
$hasIsRead = (bool)$stmtCheck->fetch();

// mesajı çek (read_by ismi ile join)
$sql = "SELECT c.*, a.name AS read_by_name FROM contact c LEFT JOIN admins a ON c.read_by = a.id WHERE c.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$msg = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$msg) {
    echo "<div class='alert alert-danger'>Mesaj bulunamadı.</div>";
    require_once '../includes/footer.php';
    exit;
}

// eğer migration varsa ve mesaj okunmamışsa otomatik okundu olarak işaretle
$currentAdminId = $_SESSION['admin_id'] ?? null;
if ($hasIsRead && !$msg['is_read'] && $currentAdminId) {
    $u = $pdo->prepare("UPDATE contact SET is_read = 1, read_by = :admin, read_at = NOW() WHERE id = :id");
    $u->execute(['admin' => $currentAdminId, 'id' => $id]);

    // yeniden veriyi çek (okuyan admin ismini almak için)
    $stmt->execute(['id' => $id]);
    $msg = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="content" id="content" style="padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">Mesaj Detayı</h4>
        <a href="contact.php" class="btn btn-secondary">← Gönderilen Mesajlara Dön</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <p><strong>Ad Soyad:</strong> <?= htmlspecialchars($msg['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($msg['email']) ?></p>
            <p><strong>Telefon:</strong> <?= htmlspecialchars($msg['phone']) ?></p>
            <p><strong>Konu:</strong> <?= nl2br(htmlspecialchars($msg['subject'])) ?></p>

            <p><strong>Mesaj:</strong></p>
            <div style="border:1px solid #eee; padding:10px; border-radius:5px; max-height:400px; overflow:auto; white-space:pre-wrap;">
                <?= nl2br(htmlspecialchars($msg['message'])) ?>
            </div>

            <p class="mt-3"><strong>Tarih:</strong> <?= htmlspecialchars($msg['created_at']) ?></p>

            <?php if ($hasIsRead): ?>
                <p><strong>Okundu mu:</strong> <?= $msg['is_read'] ? '<span class="badge bg-success">Evet</span>' : '<span class="badge bg-warning">Hayır</span>' ?></p>
                <p><strong>Okuyan:</strong> <?= htmlspecialchars($msg['read_by_name'] ?? '-') ?> <?= $msg['read_at'] ? '(' . htmlspecialchars($msg['read_at']) . ')' : '' ?></p>
            <?php endif; ?>

            <div class="mt-4">
                <?php if ($hasIsRead): ?>
                    <?php if (!$msg['is_read']): ?>
                        <a href="contact.php?action=mark_read&id=<?= $msg['id'] ?>" class="btn btn-success">Okundu Yap</a>
                    <?php else: ?>
                        <a href="contact.php?action=mark_unread&id=<?= $msg['id'] ?>" class="btn btn-outline-secondary">Okunmadı Yap</a>
                    <?php endif; ?>

                    <?php if ($msg['folder'] !== 'important'): ?>
                        <a href="contact.php?action=move_important&id=<?= $msg['id'] ?>" class="btn btn-outline-success">Önemli</a>
                    <?php else: ?>
                        <a href="contact.php?action=move_inbox&id=<?= $msg['id'] ?>" class="btn btn-secondary">Önemli Kaldır</a>
                    <?php endif; ?>

                    <?php if ($msg['folder'] !== 'trash'): ?>
                        <a href="contact.php?action=move_trash&id=<?= $msg['id'] ?>" class="btn btn-danger" onclick="return confirm('Mesajı çöp klasörüne taşımak istediğinize emin misiniz?');">Çöp</a>
                    <?php else: ?>
                        <a href="contact.php?action=move_inbox&id=<?= $msg['id'] ?>" class="btn btn-success">Geri Al</a>
                        <a href="contact.php?action=delete_perm&id=<?= $msg['id'] ?>" class="btn btn-danger" onclick="return confirm('Mesajı kalıcı olarak silmek istediğinize emin misiniz?');">Kalıcı Sil</a>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning">Veritabanı güncellemesi yapılmamış: okundu/önemli/çöp özellikleri devre dışı.</div>
                <?php endif; ?>

                <a href="contact.php" class="btn btn-secondary">Geri Dön</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
