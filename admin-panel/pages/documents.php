<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$dir = realpath(__DIR__ . '/../../data/registration');
$baseUrl = '/data/registration/';
$success = '';
$error = '';

// PDF Yükleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_pdf'])) {
    if (!empty($_FILES['pdf_file']['name'])) {
        $file = $_FILES['pdf_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($ext === 'pdf') {
            $targetPath = $dir . '/' . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $success = "PDF başarıyla yüklendi.";
            } else {
                $error = "PDF yüklenirken bir hata oluştu.";
            }
        } else {
            $error = "Sadece PDF dosyası yükleyebilirsiniz.";
        }
    } else {
        $error = "Lütfen bir PDF seçin.";
    }
}

// PDF Silme
if (isset($_GET['delete'])) {
    $fileToDelete = basename($_GET['delete']); // güvenlik
    $fullPath = $dir . '/' . $fileToDelete;
    if (file_exists($fullPath)) {
        if (unlink($fullPath)) {
            $success = "PDF silindi.";
        } else {
            $error = "PDF silinirken bir hata oluştu.";
        }
    } else {
        $error = "PDF bulunamadı.";
    }
}

// Mevcut PDF’leri çek
$pdfs = glob($dir . '/*.pdf');
?>

<div class="content" id="content" style="padding:20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">Tescil Dosyaları Yönetimi</h4>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- PDF Yükleme Formu -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Yeni PDF Yükle</div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <input type="file" name="pdf_file" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button type="submit" name="upload_pdf" class="btn btn-success w-100">Yükle</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- PDF Listesi -->
    <?php if (count($pdfs) > 0): ?>
        <div class="row">
            <?php foreach ($pdfs as $pdf): 
                $filename = basename($pdf);
                $displayName = preg_replace('/\.pdf$/i', '', $filename);
                $fileUrl = $baseUrl . rawurlencode($filename);
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title mb-3"><?= htmlspecialchars($displayName) ?></h5>
                            <embed src="<?= htmlspecialchars($fileUrl) ?>" type="application/pdf" width="100%" height="200px" style="border:1px solid #eee; border-radius:4px;" />
                            <div class="mt-2 d-flex justify-content-between">
                                <a href="<?= htmlspecialchars($fileUrl) ?>" target="_blank" class="btn btn-success btn-sm">PDF'yi Aç</a>
                                <a href="registration_files.php?delete=<?= urlencode($filename) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bu PDF dosyasını silmek istediğinize emin misiniz?');">Sil</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Henüz tescil dosyası eklenmemiş.</div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
