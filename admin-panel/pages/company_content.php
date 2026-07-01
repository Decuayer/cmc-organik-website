<?php
require_once __DIR__ . '/../../config/env.php';

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$success = '';
$error = '';

$cinfoDir = realpath(__DIR__ . '/../../public/img/cinfo/') . '/';
$allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'JPG', 'JPEG', 'PNG'];

// ---- Helper: DB içerik okuma / yazma ----
function getContent(PDO $pdo, string $section, string $key, string $default = ''): string {
    $stmt = $pdo->prepare("SELECT field_value FROM site_content WHERE section = ? AND field_key = ?");
    $stmt->execute([$section, $key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (string)$row['field_value'] : $default;
}

function setContent(PDO $pdo, string $section, string $key, string $value): void {
    $stmt = $pdo->prepare("INSERT INTO site_content (section, field_key, field_value) VALUES (?, ?, ?)
                           ON DUPLICATE KEY UPDATE field_value = VALUES(field_value)");
    $stmt->execute([$section, $key, $value]);
}

// ---- Yazı Güncelle ----
if (isset($_POST['action']) && $_POST['action'] === 'update_text') {
    setContent($pdo, 'company', 'title', trim($_POST['company_title'] ?? ''));
    setContent($pdo, 'company', 'paragraph1', trim($_POST['paragraph1'] ?? ''));
    setContent($pdo, 'company', 'paragraph2', trim($_POST['paragraph2'] ?? ''));
    setContent($pdo, 'company', 'paragraph3', trim($_POST['paragraph3'] ?? ''));
    $success = 'Şirket yazısı güncellendi.';
}

// ---- Fotoğraf Yükle ----
if (isset($_POST['action']) && $_POST['action'] === 'upload_photo') {
    if (!empty($_FILES['photo']['name'])) {
        $file = $_FILES['photo'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, array_map('strtolower', $allowedExts))) {
            $filename = uniqid('cinfo_', true) . '.' . $ext;
            $dest = $cinfoDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $success = "Fotoğraf yüklendi: $filename";
            } else {
                $error = 'Fotoğraf yüklenemedi. Dizin izinlerini kontrol edin.';
            }
        } else {
            $error = 'Geçersiz dosya tipi. JPG, JPEG, PNG, GIF, WEBP kabul edilir.';
        }
    } else {
        $error = 'Lütfen bir fotoğraf seçin.';
    }
}

// ---- Fotoğraf Sil ----
if (isset($_GET['delete_photo'])) {
    $filename = basename($_GET['delete_photo']);
    $fullPath = $cinfoDir . $filename;
    if (file_exists($fullPath) && is_file($fullPath)) {
        if (unlink($fullPath)) {
            $success = 'Fotoğraf silindi.';
        } else {
            $error = 'Fotoğraf silinemedi. İzinleri kontrol edin.';
        }
    } else {
        $error = 'Dosya bulunamadı.';
    }
}

// ---- Mevcut Fotoğrafları Tara ----
$photos = [];
if (is_dir($cinfoDir)) {
    $files = scandir($cinfoDir);
    foreach ($files as $f) {
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) && $f !== '.' && $f !== '..') {
            $photos[] = $f;
        }
    }
}

// ---- Mevcut Yazıları Çek ----
$companyTitle = getContent($pdo, 'company', 'title', 'CMC Organik Hakkında');
$para1 = getContent($pdo, 'company', 'paragraph1', '');
$para2 = getContent($pdo, 'company', 'paragraph2', '');
$para3 = getContent($pdo, 'company', 'paragraph3', '');
?>

<div class="content" id="content" style="padding: 20px;">
    <div class="d-flex align-items-center mb-4">
        <div>
            <h4 class="m-0"><i class="bi bi-building me-1"></i> Şirketimiz Sayfası Yönetimi</h4>
            <p class="text-muted mb-0">Şirket tanıtım yazısını ve fotoğraf galerisini yönetin.</p>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-1"></i><?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-x-circle-fill me-1"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Yazı Yönetimi -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <strong><i class="bi bi-pencil-square me-1"></i> Şirket Tanıtım Yazısı</strong>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="update_text">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Başlık</label>
                    <input type="text" name="company_title" class="form-control"
                           value="<?= htmlspecialchars($companyTitle) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">1. Paragraf</label>
                    <textarea name="paragraph1" class="form-control" rows="4"><?= htmlspecialchars($para1) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">2. Paragraf</label>
                    <textarea name="paragraph2" class="form-control" rows="4"><?= htmlspecialchars($para2) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">3. Paragraf</label>
                    <textarea name="paragraph3" class="form-control" rows="4"><?= htmlspecialchars($para3) ?></textarea>
                </div>

                <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Yazıları Kaydet</button>
            </form>
        </div>
    </div>

    <!-- Fotoğraf Yükleme -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <strong><i class="bi bi-camera me-1"></i> Fotoğraf Yükle</strong>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_photo">
                <div class="row align-items-end">
                    <div class="col-md-9 mb-3">
                        <label class="form-label">Fotoğraf Seç (JPG, PNG, GIF, WEBP)</label>
                        <input type="file" name="photo" class="form-control" accept="image/*" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button type="submit" class="btn btn-success w-100"><i class="bi bi-upload me-1"></i> Yükle</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Fotoğraf Galerisi -->
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong><i class="bi bi-images me-1"></i> Şirketimiz Fotoğrafları</strong>
            <span class="badge bg-secondary"><?= count($photos) ?> fotoğraf</span>
        </div>
        <div class="card-body">
            <?php if (count($photos) > 0): ?>
                <div class="row g-3">
                    <?php foreach ($photos as $photo): ?>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 text-center">
                            <div class="border rounded p-2 h-100">
                                <img src="/public/img/cinfo/<?= htmlspecialchars($photo) ?>"
                                     class="img-fluid mb-2"
                                     style="max-height:100px; object-fit:cover;"
                                     alt="<?= htmlspecialchars($photo) ?>">
                                <div class="mt-1">
                                    <small class="text-muted d-block text-truncate" style="max-width:100%;">
                                        <?= htmlspecialchars($photo) ?>
                                    </small>
                                    <a href="company_content.php?delete_photo=<?= urlencode($photo) ?>"
                                       class="btn btn-sm btn-outline-danger mt-1"
                                       onclick="return confirm('Bu fotoğrafı silmek istediğinize emin misiniz?')">
                                        <i class="bi bi-trash me-1"></i> Sil
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">
                    Henüz fotoğraf eklenmemiş. Yukarıdaki formdan fotoğraf yükleyebilirsiniz.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow-sm mt-3">
        <div class="card-body py-2">
            <small class="text-muted">
                <i class="bi bi-lightbulb me-1"></i> Yüklenen fotoğraflar <code>public/img/cinfo/</code> klasörüne kaydedilir ve Şirketimiz sayfasındaki carousel'de otomatik olarak görüntülenir.
            </small>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
