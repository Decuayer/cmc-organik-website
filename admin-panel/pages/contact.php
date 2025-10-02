<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$stmt = $pdo->query("
    SELECT * FROM contact
    ORDER BY id DESC
");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="content" id="content" style="padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">Gönderilen Mesajlar</h4>
    </div>
    <?php if (count($messages) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle table-hover">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Ad Soyad</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Konu</th>
                        <th>Mesaj</th>
                        <th>Tarih</th>
                        <th style="width: 120px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $index => $msg): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($msg['name'] ?? 'Belirtilmemiş') ?></td>
                            <td><?= htmlspecialchars($msg['email'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($msg['phone'] ?? '-') ?></td>
                            <td><?= mb_strimwidth(htmlspecialchars($msg['subject'] ?? ''), 0, 50, '...') ?></td>
                            <td><?= mb_strimwidth(htmlspecialchars($msg['message'] ?? ''), 0, 50, '...') ?></td>
                            <td><?= htmlspecialchars($msg['created_at'] ?? '-') ?></td>
                            <td class="text-center align-middle">
                                <a href="message_view.php?id=<?= $msg['id'] ?>" class="btn btn-sm btn-success my-1">Görüntüle</a>
                                <a href="message_delete.php?id=<?= $msg['id'] ?>" class="btn btn-sm btn-danger my-1" onclick="return confirm('Bu mesajı silmek istediğinize emin misiniz?');">Sil</a>
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
