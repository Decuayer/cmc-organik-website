<?php
require_once __DIR__ . '/../../config/env.php';

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/upload.php';
require_once '../includes/csrf.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$error = '';

// Kategoriler
$types = $pdo->query("SELECT * FROM product_types ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $name        = trim($_POST['name'] ?? '');
    $type        = $_POST['type'] ?? null;
    $description = trim($_POST['description'] ?? '');
    $contents    = trim($_POST['contents'] ?? '');
    $pack        = trim($_POST['pack'] ?? '');
    $applyType   = trim($_POST['applyType'] ?? '');
    $apply       = trim($_POST['apply'] ?? '');
    $applySeperate = isset($_POST['applySeperate']) ? 1 : 0;

    $imgPath = '';
    if (!empty($_FILES['imgPath']['name'])) {
        $targetDir = __DIR__ . '/../../data/product';
        $uploaded = handleImageUpload($_FILES['imgPath'], $targetDir, 'data/product', ['jpg','jpeg','png','gif','webp']);
        if ($uploaded['ok']) {
            $imgPath = $uploaded['path'];
        } else {
            $error = $uploaded['error'];
            error_log('Ürün görseli yüklenemedi: ' . $uploaded['error']);
        }
    }

    if (!$error) {
        if (empty($name) || empty($type)) {
            $error = 'Ürün adı ve kategori zorunludur.';
        } else {
            $insert = $pdo->prepare("
                INSERT INTO products (name, type, description, contents, pack, applyType, apply, imgPath, applySeperate)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $insert->execute([$name, $type, $description, $contents, $pack, $applyType, $apply, $imgPath, $applySeperate]);

            header("Location: products.php");
            exit;
        }
    }
}

$mode = 'add';
$product = null;
?>

<div class="content" id="content" style="padding: 20px;">
    <h4 class="mb-4">Yeni Ürün Ekle</h4>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-x-circle-fill me-1"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php require __DIR__ . '/_product_form.php'; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
