<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$success = '';
$error = '';

$uploadDir = realpath(__DIR__ . '/../../public/img/bussiness/') . '/';
$allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

// ---- Helper: fotoğraf yükleme ----
function uploadPartnerImage(array $file, string $uploadDir, array $allowedExts): string|false {
    if (empty($file['name'])) return false;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) return false;
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    $filename = uniqid('partner_', true) . '.' . $ext;
    $dest = $uploadDir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) return false;
    return 'public/img/bussiness/' . $filename;
}

// ---- Ortak Ekle ----
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    if ($name === '') {
        $error = 'Ortak adı boş olamaz.';
    } else {
        $image_path = null;
        $logo_path = null;

        if (!empty($_FILES['image']['name'])) {
            $result = uploadPartnerImage($_FILES['image'], $uploadDir, $allowedExts);
            if ($result) $image_path = $result;
            else $error = 'Detay görseli yüklenemedi. Dosya tipi veya boyutunu kontrol edin.';
        }
        if (!empty($_FILES['logo']['name'])) {
            $result = uploadPartnerImage($_FILES['logo'], $uploadDir, $allowedExts);
            if ($result) $logo_path = $result;
            else $error .= ' Logo yüklenemedi.';
        }

        if (!$error) {
            $stmt = $pdo->prepare("INSERT INTO business_partners (name, description, image_path, logo_path, sort_order, is_active) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->execute([$name, $description, $image_path, $logo_path, $sort_order]);
            $success = "✅ \"$name\" başarıyla eklendi.";
        }
    }
}

// ---- Ortak Düzenle ----
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($id && $name !== '') {
        // Mevcut görseli al
        $existing = $pdo->prepare("SELECT image_path, logo_path FROM business_partners WHERE id = ?");
        $existing->execute([$id]);
        $row = $existing->fetch(PDO::FETCH_ASSOC);

        $image_path = $row['image_path'];
        $logo_path = $row['logo_path'];

        if (!empty($_FILES['image']['name'])) {
            $result = uploadPartnerImage($_FILES['image'], $uploadDir, $allowedExts);
            if ($result) {
                // Eski dosyayı sil
                if ($image_path && file_exists(__DIR__ . '/../../' . $image_path)) {
                    @unlink(__DIR__ . '/../../' . $image_path);
                }
                $image_path = $result;
            }
        }
        if (!empty($_FILES['logo']['name'])) {
            $result = uploadPartnerImage($_FILES['logo'], $uploadDir, $allowedExts);
            if ($result) {
                if ($logo_path && file_exists(__DIR__ . '/../../' . $logo_path)) {
                    @unlink(__DIR__ . '/../../' . $logo_path);
                }
                $logo_path = $result;
            }
        }

        $stmt = $pdo->prepare("UPDATE business_partners SET name=?, description=?, image_path=?, logo_path=?, sort_order=?, is_active=? WHERE id=?");
        $stmt->execute([$name, $description, $image_path, $logo_path, $sort_order, $is_active, $id]);
        $success = "✅ \"$name\" güncellendi.";
    }
}

// ---- Ortak Sil ----
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image_path, logo_path FROM business_partners WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        foreach (['image_path', 'logo_path'] as $field) {
            if ($row[$field] && file_exists(__DIR__ . '/../../' . $row[$field])) {
                @unlink(__DIR__ . '/../../' . $row[$field]);
            }
        }
        $pdo->prepare("DELETE FROM business_partners WHERE id = ?")->execute([$id]);
        $success = "✅ İş ortağı silindi.";
    }
}

// ---- Aktif/Pasif Toggle ----
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE business_partners SET is_active = 1 - is_active WHERE id = ?")->execute([$id]);
    header("Location: partners.php");
    exit;
}

// ---- Veri Çek ----
$partners = $pdo->query("SELECT * FROM business_partners ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Düzenleme modu
$editPartner = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM business_partners WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editPartner = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="content" id="content" style="padding: 20px;">
    <div class="d-flex align-items-center mb-4">
        <div>
            <h4 class="m-0">🤝 İş Ortakları Yönetimi</h4>
            <p class="text-muted mb-0">İş ortaklarını ekleyin, düzenleyin, silin ve sıralayın.</p>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Sol: Form -->
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <?= $editPartner ? '✏️ İş Ortağını Düzenle' : '➕ Yeni İş Ortağı Ekle' ?>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?= $editPartner ? 'edit' : 'add' ?>">
                        <?php if ($editPartner): ?>
                            <input type="hidden" name="id" value="<?= $editPartner['id'] ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ortak Adı <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= htmlspecialchars($editPartner['name'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Açıklama / Tanıtım Yazısı</label>
                            <textarea name="description" class="form-control" rows="5"
                                      placeholder="Ortak hakkında açıklama yazısı..."><?= htmlspecialchars($editPartner['description'] ?? '') ?></textarea>
                            <small class="text-muted">Bu metin iş ortakları sayfasında ortağın yanında görünür.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Detay Görseli (Büyük Fotoğraf)</label>
                            <?php if (!empty($editPartner['image_path'])): ?>
                                <div class="mb-2">
                                    <img src="/<?= htmlspecialchars($editPartner['image_path']) ?>"
                                         class="img-thumbnail" style="max-height:80px;" alt="Mevcut">
                                    <small class="text-muted d-block">Mevcut görsel. Yeni dosya seçerseniz değiştirilir.</small>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Logo (Carousel'da Görünür)</label>
                            <?php if (!empty($editPartner['logo_path'])): ?>
                                <div class="mb-2">
                                    <img src="/<?= htmlspecialchars($editPartner['logo_path']) ?>"
                                         class="img-thumbnail" style="max-height:80px;" alt="Logo">
                                    <small class="text-muted d-block">Mevcut logo. Değiştirmek için yeni dosya seçin.</small>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Sıralama</label>
                                <input type="number" name="sort_order" class="form-control"
                                       value="<?= $editPartner['sort_order'] ?? 0 ?>" min="0">
                                <small class="text-muted">Küçük sayı = üstte görünür</small>
                            </div>
                            <?php if ($editPartner): ?>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Durum</label>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="is_active" class="form-check-input" id="isActive"
                                               <?= $editPartner['is_active'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="isActive">Aktif</label>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <?= $editPartner ? '💾 Güncelle' : '➕ Ekle' ?>
                            </button>
                            <?php if ($editPartner): ?>
                                <a href="partners.php" class="btn btn-outline-secondary">İptal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sağ: Liste -->
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <strong>📋 İş Ortakları Listesi</strong>
                    <span class="badge bg-secondary"><?= count($partners) ?> ortak</span>
                </div>
                <div class="card-body p-0">
                    <?php if (count($partners) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:60px;">Logo</th>
                                        <th>Ortak Adı</th>
                                        <th style="width:70px;">Sıra</th>
                                        <th style="width:70px;">Durum</th>
                                        <th style="width:130px;">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($partners as $p): ?>
                                        <tr class="<?= !$p['is_active'] ? 'table-secondary' : '' ?>">
                                            <td>
                                                <?php if ($p['logo_path']): ?>
                                                    <img src="/<?= htmlspecialchars($p['logo_path']) ?>"
                                                         class="img-thumbnail" style="max-height:45px; max-width:55px;" alt="">
                                                <?php elseif ($p['image_path']): ?>
                                                    <img src="/<?= htmlspecialchars($p['image_path']) ?>"
                                                         class="img-thumbnail" style="max-height:45px; max-width:55px;" alt="">
                                                <?php else: ?>
                                                    <span class="text-muted" style="font-size:1.5rem;">🤝</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($p['name']) ?></strong>
                                                <?php if ($p['description']): ?>
                                                    <small class="text-muted d-block" style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                                        <?= htmlspecialchars(substr($p['description'], 0, 60)) ?>...
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border"><?= $p['sort_order'] ?></span>
                                            </td>
                                            <td>
                                                <a href="partners.php?toggle=<?= $p['id'] ?>"
                                                   class="badge <?= $p['is_active'] ? 'bg-success' : 'bg-secondary' ?> text-decoration-none"
                                                   title="Durumu değiştir">
                                                    <?= $p['is_active'] ? '✅ Aktif' : '⛔ Pasif' ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="partners.php?edit=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Düzenle">✏️</a>
                                                <a href="partners.php?delete=<?= $p['id'] ?>"
                                                   class="btn btn-sm btn-outline-danger"
                                                   title="Sil"
                                                   onclick="return confirm('<?= htmlspecialchars($p['name']) ?> silinsin mi? Bu işlem geri alınamaz.')">🗑️</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info m-3">
                            Henüz iş ortağı eklenmemiş.<br>
                            <small class="text-muted">SQL migration dosyasını çalıştırdıysanız varsayılan ortaklar eklenmiş olmalı.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bilgi Kutusu -->
            <div class="card shadow-sm mt-3">
                <div class="card-body py-2">
                    <small class="text-muted">
                        💡 <strong>İpuçları:</strong>
                        Sıralama numarası küçük olan ortak sayfada önce görünür.
                        Pasif ortaklar sitede gösterilmez ama silinmez.
                        Detay görseli iş ortakları sayfasında ortağın yanında büyük görünür.
                        Logo, carousel'da listelenir.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
