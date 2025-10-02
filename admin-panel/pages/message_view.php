<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Mesaj ID'sini alıyoruz
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: contact.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM contact WHERE id = :id");
$stmt->execute(['id' => $id]);
$msg = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$msg) {
    echo "<div class='alert alert-danger'>Mesaj bulunamadı.</div>";
    require_once '../includes/footer.php';
    exit;
}
?>

<div class="content" id="content" style="padding: 20px;">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h4 class="m-0">Mesaj Detayı</h4>
        <a href="contact.php" class="btn btn-secondary">← Geri Dön</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <p><strong>Ad Soyad:</strong> <?= htmlspecialchars($msg['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($msg['email']) ?></p>
            <p><strong>Telefon:</strong> <?= htmlspecialchars($msg['phone']) ?></p>
            <p><strong>Konu:</strong> <?= nl2br(htmlspecialchars($msg['subject'])) ?></p>
            <p><strong>Mesaj:</strong></p>
            <div style="border:1px solid #ddd; padding:10px; border-radius:5px; max-height:400px; overflow:auto; white-space:pre-wrap;">
                <?= nl2br(htmlspecialchars($msg['message'])) ?>
            </div>
            <p class="mt-3"><strong>Tarih:</strong> <?= htmlspecialchars($msg['created_at']) ?></p>

            <div class="mt-4">
                <a href="message_delete.php?id=<?= $msg['id'] ?>" class="btn btn-danger" onclick="return confirm('Bu mesajı silmek istediğinize emin misiniz?');">Mesajı Sil</a>
                <a href="contact.php" class="btn btn-secondary">Geri Dön</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
