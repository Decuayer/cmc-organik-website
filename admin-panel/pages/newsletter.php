<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';              // oturum kontrolü
require_once '../../config/database.php';         // proje kökündeki db (bu dosya $pdo sağlar)
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$success = '';
$error = '';

// Silme işlemi (GET ile, senin mevcut yapıya uyumlu)
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $del = $pdo->prepare("DELETE FROM newsletter_subscribers WHERE id = :id");
        $del->execute(['id' => $id]);
        $success = "Abone silindi.";
        header("Location: newsletter.php?deleted=1");
        exit;
    } catch (PDOException $e) {
        $error = "Silme işlemi sırasında hata.";
    }
}

// CSV export
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    $stmt = $pdo->query("SELECT id, email, subscribed_at FROM newsletter_subscribers ORDER BY subscribed_at DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=newsletter_subscribers_' . date('Ymd_His') . '.csv');

    $out = fopen('php://output', 'w');
    // başlık
    fputcsv($out, ['id','email','subscribed_at']);
    foreach ($rows as $r) {
        fputcsv($out, [$r['id'], $r['email'], $r['subscribed_at']]);
    }
    fclose($out);
    exit;
}

// aboneleri çek
$stmt = $pdo->query("SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC");
$subs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content" id="content" style="padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">Bülten Aboneleri</h4>
        <div>
            <a href="newsletter.php?export=csv" class="btn btn-sm btn-outline-primary">CSV Dışa Aktar</a>
        </div>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Abone silindi.</div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (count($subs) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle table-hover">
                        <thead class="table-success">
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>E-posta</th>
                                <th style="width:180px;">Kayıt Tarihi</th>
                                <th style="width:120px;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subs as $index => $s): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td class="truncate" title="<?= htmlspecialchars($s['email']) ?>"><?= htmlspecialchars($s['email']) ?></td>
                                    <td><?= htmlspecialchars($s['subscribed_at']) ?></td>
                                    <td class="text-center">
                                        <a href="newsletter.php?delete=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu aboneyi silmek istediğinize emin misiniz?');">Sil</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Henüz abone yok.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
