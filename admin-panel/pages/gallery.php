<?php
require_once __DIR__ . '/../../config/env.php';

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$success = '';
$error = '';

$galleryDir = realpath(__DIR__ . '/../../public/img/gallery/');

// Fotoğraf yükleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_image'])) {
    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        $fileName = basename($file['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExt, $allowedExtensions)) {
            $targetPath = $galleryDir . '/' . $fileName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $success = "Fotoğraf başarıyla yüklendi.";
            } else {
                $error = "Fotoğraf yüklenirken bir hata oluştu.";
            }
        } else {
            $error = "Sadece JPG, JPEG, PNG, GIF veya WEBP uzantılı dosyalar yüklenebilir.";
        }
    } else {
        $error = "Lütfen bir fotoğraf seçin.";
    }
}

// Fotoğraf silme
if (isset($_GET['delete'])) {
    $fileToDelete = basename($_GET['delete']); // güvenlik için basename
    $fullPath = $galleryDir . '/' . $fileToDelete;

    if (file_exists($fullPath)) {
        if (unlink($fullPath)) {
            $success = "Fotoğraf silindi.";
        } else {
            $error = "Fotoğraf silinirken bir hata oluştu.";
        }
    } else {
        $error = "Fotoğraf bulunamadı.";
    }
}

// Mevcut fotoğrafları çek
$galleryFiles = [];
if (is_dir($galleryDir)) {
    $allFiles = scandir($galleryDir);
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'JPG'];

    foreach ($allFiles as $file) {
        $filePath = $galleryDir . '/' . $file;
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (in_array($ext, $allowedExtensions)) {
            $galleryFiles[] = $file;
        }
    }
} else {
    $error = "Galeri klasörü bulunamadı.";
}
?>

<div class="content" id="content" style="padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">Galeri Yönetimi</h4>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Fotoğraf Yükleme Formu -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Yeni Fotoğraf Yükle</div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <input type="file" name="image" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button type="submit" name="upload_image" class="btn btn-success w-100">Yükle</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Fotoğraf Listesi -->
    <?php if (count($galleryFiles) > 0): ?>
        <div class="row">
            <?php foreach ($galleryFiles as $file): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4 text-center">
                    <img src="/public/img/gallery/<?= htmlspecialchars($file) ?>" class="img-fluid mb-2" style="max-height:150px;" alt="<?= htmlspecialchars($file) ?>">
                    <div>
                        <a href="gallery.php?delete=<?= urlencode($file) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu fotoğrafı silmek istediğinize emin misiniz?');">Sil</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Henüz galeride fotoğraf yok.</div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
