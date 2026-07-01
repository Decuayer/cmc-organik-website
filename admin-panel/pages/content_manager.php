<?php
require_once __DIR__ . '/../../config/env.php';

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$success = '';
$error = '';

$imgDir = realpath(__DIR__ . '/../../public/img/') . '/';
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

function uploadImage(array $file, string $imgDir, array $allowedExts): string|false {
    if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) return false;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, array_map('strtolower', $allowedExts))) return false;
    $filename = 'content_' . uniqid('', true) . '.' . $ext;
    $dest = $imgDir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) return false;
    return 'public/img/' . $filename;
}

$activeTab = $_POST['active_tab'] ?? $_GET['tab'] ?? 'homepage';

// =============================
// ANASAYFA İÇERİKLERİ
// =============================
if (isset($_POST['action']) && $_POST['action'] === 'update_homepage') {
    $activeTab = 'homepage';

    // Misyon
    setContent($pdo, 'homepage_mission', 'title', trim($_POST['hp_mission_title'] ?? ''));
    setContent($pdo, 'homepage_mission', 'text', trim($_POST['hp_mission_text'] ?? ''));
    if (!empty($_FILES['hp_mission_img']['name'])) {
        $path = uploadImage($_FILES['hp_mission_img'], $imgDir, $allowedExts);
        if ($path) setContent($pdo, 'homepage_mission', 'image_path', $path);
        else $error .= ' Misyon görseli yüklenemedi.';
    }

    // Vizyon
    setContent($pdo, 'homepage_vision', 'title', trim($_POST['hp_vision_title'] ?? ''));
    setContent($pdo, 'homepage_vision', 'text', trim($_POST['hp_vision_text'] ?? ''));
    if (!empty($_FILES['hp_vision_img']['name'])) {
        $path = uploadImage($_FILES['hp_vision_img'], $imgDir, $allowedExts);
        if ($path) setContent($pdo, 'homepage_vision', 'image_path', $path);
        else $error .= ' Vizyon görseli yüklenemedi.';
    }

    // Yol Haritası
    setContent($pdo, 'homepage_roadmap', 'slogan', trim($_POST['hp_roadmap_slogan'] ?? ''));
    setContent($pdo, 'homepage_roadmap', 'description', trim($_POST['hp_roadmap_desc'] ?? ''));

    if (!$error) $success = 'Anasayfa içerikleri güncellendi.';
}

// =============================
// ROADMAP MADDESİ EKLE/DÜZENLE/SİL
// =============================
if (isset($_POST['action']) && $_POST['action'] === 'add_roadmap_item') {
    $activeTab = 'roadmap';
    $title = trim($_POST['ri_title'] ?? '');
    if ($title) {
        $pdo->prepare("INSERT INTO roadmap_items (title, description, icon_id, sort_order) VALUES (?, ?, ?, ?)")
            ->execute([$title, trim($_POST['ri_desc'] ?? ''), trim($_POST['ri_icon'] ?? 'gear-fill'), (int)($_POST['ri_sort'] ?? 0)]);
        $success = 'Roadmap maddesi eklendi.';
    } else {
        $error = 'Başlık boş olamaz.';
    }
}

if (isset($_POST['action']) && $_POST['action'] === 'edit_roadmap_item') {
    $activeTab = 'roadmap';
    $id = (int)($_POST['ri_id'] ?? 0);
    if ($id) {
        $pdo->prepare("UPDATE roadmap_items SET title=?, description=?, icon_id=?, sort_order=?, is_active=? WHERE id=?")
            ->execute([
                trim($_POST['ri_title'] ?? ''),
                trim($_POST['ri_desc'] ?? ''),
                trim($_POST['ri_icon'] ?? 'gear-fill'),
                (int)($_POST['ri_sort'] ?? 0),
                isset($_POST['ri_active']) ? 1 : 0,
                $id
            ]);
        $success = 'Roadmap maddesi güncellendi.';
    }
}

if (isset($_GET['delete_ri']) && is_numeric($_GET['delete_ri'])) {
    $activeTab = 'roadmap';
    $pdo->prepare("DELETE FROM roadmap_items WHERE id = ?")->execute([(int)$_GET['delete_ri']]);
    $success = 'Roadmap maddesi silindi.';
    header("Location: content_manager.php?tab=roadmap");
    exit;
}

// =============================
// HAKKIMIZDA SAYFA İÇERİKLERİ
// =============================
if (isset($_POST['action']) && $_POST['action'] === 'update_about') {
    $activeTab = 'about';

    // Hoşgeldiniz
    setContent($pdo, 'about_welcome', 'title', trim($_POST['ab_welcome_title'] ?? ''));
    setContent($pdo, 'about_welcome', 'text', trim($_POST['ab_welcome_text'] ?? ''));
    if (!empty($_FILES['ab_welcome_img']['name'])) {
        $path = uploadImage($_FILES['ab_welcome_img'], $imgDir, $allowedExts);
        if ($path) setContent($pdo, 'about_welcome', 'image_path', $path);
        else $error .= ' Hoşgeldiniz görseli yüklenemedi.';
    }

    // Misyon
    setContent($pdo, 'about_mission', 'title', trim($_POST['ab_mission_title'] ?? ''));
    setContent($pdo, 'about_mission', 'text', trim($_POST['ab_mission_text'] ?? ''));
    if (!empty($_FILES['ab_mission_img']['name'])) {
        $path = uploadImage($_FILES['ab_mission_img'], $imgDir, $allowedExts);
        if ($path) setContent($pdo, 'about_mission', 'image_path', $path);
        else $error .= ' Misyon görseli yüklenemedi.';
    }

    // Vizyon
    setContent($pdo, 'about_vision', 'title', trim($_POST['ab_vision_title'] ?? ''));
    setContent($pdo, 'about_vision', 'text', trim($_POST['ab_vision_text'] ?? ''));
    if (!empty($_FILES['ab_vision_img']['name'])) {
        $path = uploadImage($_FILES['ab_vision_img'], $imgDir, $allowedExts);
        if ($path) setContent($pdo, 'about_vision', 'image_path', $path);
        else $error .= ' Vizyon görseli yüklenemedi.';
    }

    // Alt bölüm
    setContent($pdo, 'about_bottom', 'title', trim($_POST['ab_bottom_title'] ?? ''));
    setContent($pdo, 'about_bottom', 'paragraph1', trim($_POST['ab_bottom_p1'] ?? ''));
    setContent($pdo, 'about_bottom', 'paragraph2', trim($_POST['ab_bottom_p2'] ?? ''));
    setContent($pdo, 'about_bottom', 'paragraph3', trim($_POST['ab_bottom_p3'] ?? ''));
    setContent($pdo, 'about_bottom', 'paragraph4', trim($_POST['ab_bottom_p4'] ?? ''));

    if (!$error) $success = 'Hakkımızda sayfası içerikleri güncellendi.';
}

// ---- Roadmap maddelerini çek ----
$roadmapItems = $pdo->query("SELECT * FROM roadmap_items ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
$editRi = null;
if (isset($_GET['edit_ri']) && is_numeric($_GET['edit_ri'])) {
    $stmt = $pdo->prepare("SELECT * FROM roadmap_items WHERE id = ?");
    $stmt->execute([(int)$_GET['edit_ri']]);
    $editRi = $stmt->fetch(PDO::FETCH_ASSOC);
    $activeTab = 'roadmap';
}

// SVG sprite ikon listesi (mevcut svg.php'den)
$availableIcons = [
    'gear-fill' => 'Dişli (gear-fill)',
    'recycle' => 'Geri dönüşüm (recycle)',
    'book' => 'Kitap (book)',
    'speedometer' => 'Hız göstergesi (speedometer)',
    'star' => 'Yıldız (star)',
    'shield' => 'Kalkan (shield)',
    'leaf' => 'Yaprak (leaf)',
    'globe' => 'Küre (globe)',
    'graph-up' => 'Yükselen grafik (graph-up)',
    'people' => 'İnsanlar (people)',
    'check-circle' => 'Onay işareti (check-circle)',
    'lightbulb' => 'Ampul (lightbulb)',
];
?>

<div class="content" id="content" style="padding: 20px;">
    <div class="d-flex align-items-center mb-4">
        <div>
            <h4 class="m-0"><i class="bi bi-pencil-square me-1"></i> İçerik Yönetimi</h4>
            <p class="text-muted mb-0">Anasayfa ve Hakkımızda sayfalarının metinlerini ve görsellerini yönetin.</p>
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
            <i class="bi bi-x-circle-fill me-1"></i><?= nl2br(htmlspecialchars(trim($error))) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Sekmeler -->
    <ul class="nav nav-tabs mb-4" id="contentTabs">
        <li class="nav-item">
            <a class="nav-link text-success <?= $activeTab === 'homepage' ? 'active' : '' ?>" href="?tab=homepage"><i class="bi bi-house me-1"></i> Anasayfa</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-success <?= $activeTab === 'roadmap' ? 'active' : '' ?>" href="?tab=roadmap"><i class="bi bi-map me-1"></i> Yol Haritası</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-success <?= $activeTab === 'about' ? 'active' : '' ?>" href="?tab=about"><i class="bi bi-info-circle me-1"></i> Hakkımızda</a>
        </li>
    </ul>

    <!-- ===== ANASAYFA SEKMESİ ===== -->
    <?php if ($activeTab === 'homepage'): ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update_homepage">
        <input type="hidden" name="active_tab" value="homepage">

        <!-- Misyon -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white"><strong><i class="bi bi-bullseye me-1"></i> Misyonumuz (Anasayfa)</strong></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Başlık</label>
                            <input type="text" name="hp_mission_title" class="form-control"
                                   value="<?= htmlspecialchars(getContent($pdo, 'homepage_mission', 'title', 'Misyonumuz')) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Metin</label>
                            <textarea name="hp_mission_text" class="form-control" rows="4"><?= htmlspecialchars(getContent($pdo, 'homepage_mission', 'text', '')) ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Fotoğraf</label>
                        <?php $imgPath = getContent($pdo, 'homepage_mission', 'image_path', ''); ?>
                        <?php if ($imgPath): ?>
                            <img src="/<?= htmlspecialchars($imgPath) ?>" class="img-thumbnail w-100 mb-2" style="max-height:120px; object-fit:cover;">
                            <small class="text-muted d-block mb-2">Mevcut fotoğraf</small>
                        <?php endif; ?>
                        <input type="file" name="hp_mission_img" class="form-control" accept="image/*">
                        <small class="text-muted">Boş bırakılırsa mevcut fotoğraf korunur.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vizyon -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white"><strong><i class="bi bi-binoculars me-1"></i> Vizyonumuz (Anasayfa)</strong></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Başlık</label>
                            <input type="text" name="hp_vision_title" class="form-control"
                                   value="<?= htmlspecialchars(getContent($pdo, 'homepage_vision', 'title', 'Vizyonumuz')) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Metin</label>
                            <textarea name="hp_vision_text" class="form-control" rows="4"><?= htmlspecialchars(getContent($pdo, 'homepage_vision', 'text', '')) ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Fotoğraf</label>
                        <?php $imgPath = getContent($pdo, 'homepage_vision', 'image_path', ''); ?>
                        <?php if ($imgPath): ?>
                            <img src="/<?= htmlspecialchars($imgPath) ?>" class="img-thumbnail w-100 mb-2" style="max-height:120px; object-fit:cover;">
                            <small class="text-muted d-block mb-2">Mevcut fotoğraf</small>
                        <?php endif; ?>
                        <input type="file" name="hp_vision_img" class="form-control" accept="image/*">
                        <small class="text-muted">Boş bırakılırsa mevcut fotoğraf korunur.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yol Haritası Ana Yazı -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white"><strong><i class="bi bi-map me-1"></i> Yol Haritamız – Ana Yazı</strong></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Slogan</label>
                    <input type="text" name="hp_roadmap_slogan" class="form-control"
                           value="<?= htmlspecialchars(getContent($pdo, 'homepage_roadmap', 'slogan', '"Bilimden Doğaya"')) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Açıklama Metni</label>
                    <textarea name="hp_roadmap_desc" class="form-control" rows="4"><?= htmlspecialchars(getContent($pdo, 'homepage_roadmap', 'description', '')) ?></textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-save me-1"></i> Anasayfa İçeriklerini Kaydet</button>
    </form>
    <?php endif; ?>

    <!-- ===== ROADMAP SEKMESİ ===== -->
    <?php if ($activeTab === 'roadmap'): ?>
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <?= $editRi ? '<i class="bi bi-pencil-square me-1"></i> Maddeyi Düzenle' : '<i class="bi bi-plus-lg me-1"></i> Yeni Madde Ekle' ?>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="<?= $editRi ? 'edit_roadmap_item' : 'add_roadmap_item' ?>">
                        <input type="hidden" name="active_tab" value="roadmap">
                        <?php if ($editRi): ?>
                            <input type="hidden" name="ri_id" value="<?= $editRi['id'] ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Başlık <span class="text-danger">*</span></label>
                            <input type="text" name="ri_title" class="form-control"
                                   value="<?= htmlspecialchars($editRi['title'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Açıklama</label>
                            <textarea name="ri_desc" class="form-control" rows="4"><?= htmlspecialchars($editRi['description'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sembol (SVG ikon ID)</label>
                            <select name="ri_icon" class="form-select">
                                <?php foreach ($availableIcons as $iconId => $label): ?>
                                    <option value="<?= $iconId ?>"
                                            <?= ($editRi['icon_id'] ?? 'gear-fill') === $iconId ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">SVG sprite'ta bulunan ikonlar listeleniyor.</small>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">Sıralama</label>
                                <input type="number" name="ri_sort" class="form-control"
                                       value="<?= $editRi['sort_order'] ?? 0 ?>" min="0">
                            </div>
                            <?php if ($editRi): ?>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-semibold">Durum</label>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="ri_active" id="ri_active" class="form-check-input"
                                               <?= ($editRi['is_active'] ?? 1) ? 'checked' : '' ?>>
                                        <label for="ri_active" class="form-check-label">Aktif</label>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success"><?= $editRi ? '<i class="bi bi-save me-1"></i> Güncelle' : '<i class="bi bi-plus-lg me-1"></i> Ekle' ?></button>
                            <?php if ($editRi): ?><a href="?tab=roadmap" class="btn btn-outline-secondary">İptal</a><?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-list-ul me-1"></i> Yol Haritası Maddeleri</strong>
                    <span class="badge bg-secondary"><?= count($roadmapItems) ?> madde</span>
                </div>
                <div class="card-body p-0">
                    <?php if (count($roadmapItems) > 0): ?>
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>İkon</th>
                                    <th>Başlık</th>
                                    <th>Sıra</th>
                                    <th>Durum</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($roadmapItems as $ri): ?>
                                    <tr class="<?= !$ri['is_active'] ? 'table-secondary' : '' ?>">
                                        <td>
                                            <span class="badge bg-success bg-gradient fs-6" style="width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center;">
                                                <svg class="bi" width="14" height="14"><use xlink:href="#<?= htmlspecialchars($ri['icon_id']) ?>"/></svg>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($ri['title']) ?></strong>
                                            <small class="text-muted d-block"><?= htmlspecialchars(substr($ri['description'], 0, 50)) ?>...</small>
                                        </td>
                                        <td><?= $ri['sort_order'] ?></td>
                                        <td>
                                            <span class="badge <?= $ri['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= $ri['is_active'] ? 'Aktif' : 'Pasif' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="?edit_ri=<?= $ri['id'] ?>" class="btn btn-sm btn-outline-success me-1"><i class="bi bi-pencil-square"></i></a>
                                            <a href="?delete_ri=<?= $ri['id'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Bu maddeyi silmek istediğinize emin misiniz?')"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-info m-3">Henüz madde eklenmemiş.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ===== HAKKIMIZDA SEKMESİ ===== -->
    <?php if ($activeTab === 'about'): ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update_about">
        <input type="hidden" name="active_tab" value="about">

        <!-- Hoşgeldiniz -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white"><strong><i class="bi bi-hand-thumbs-up me-1"></i> CMC Organik'e Hoş Geldiniz</strong></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Başlık</label>
                            <input type="text" name="ab_welcome_title" class="form-control"
                                   value="<?= htmlspecialchars(getContent($pdo, 'about_welcome', 'title', 'CMC Organik\'e Hoş Geldiniz')) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Metin</label>
                            <textarea name="ab_welcome_text" class="form-control" rows="4"><?= htmlspecialchars(getContent($pdo, 'about_welcome', 'text', '')) ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Fotoğraf</label>
                        <?php $imgPath = getContent($pdo, 'about_welcome', 'image_path', ''); ?>
                        <?php if ($imgPath): ?>
                            <img src="/<?= htmlspecialchars($imgPath) ?>" class="img-thumbnail w-100 mb-2" style="max-height:120px; object-fit:cover;">
                        <?php endif; ?>
                        <input type="file" name="ab_welcome_img" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>
        </div>

        <!-- Misyon (Hakkımızda) -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white"><strong><i class="bi bi-bullseye me-1"></i> Misyonumuz (Hakkımızda Sayfası)</strong></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Başlık</label>
                            <input type="text" name="ab_mission_title" class="form-control"
                                   value="<?= htmlspecialchars(getContent($pdo, 'about_mission', 'title', 'Misyonumuz')) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Metin</label>
                            <textarea name="ab_mission_text" class="form-control" rows="4"><?= htmlspecialchars(getContent($pdo, 'about_mission', 'text', '')) ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Fotoğraf</label>
                        <?php $imgPath = getContent($pdo, 'about_mission', 'image_path', ''); ?>
                        <?php if ($imgPath): ?>
                            <img src="/<?= htmlspecialchars($imgPath) ?>" class="img-thumbnail w-100 mb-2" style="max-height:120px; object-fit:cover;">
                        <?php endif; ?>
                        <input type="file" name="ab_mission_img" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>
        </div>

        <!-- Vizyon (Hakkımızda) -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white"><strong><i class="bi bi-binoculars me-1"></i> Vizyonumuz (Hakkımızda Sayfası)</strong></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Başlık</label>
                            <input type="text" name="ab_vision_title" class="form-control"
                                   value="<?= htmlspecialchars(getContent($pdo, 'about_vision', 'title', 'Vizyonumuz')) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Metin</label>
                            <textarea name="ab_vision_text" class="form-control" rows="4"><?= htmlspecialchars(getContent($pdo, 'about_vision', 'text', '')) ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Fotoğraf</label>
                        <?php $imgPath = getContent($pdo, 'about_vision', 'image_path', ''); ?>
                        <?php if ($imgPath): ?>
                            <img src="/<?= htmlspecialchars($imgPath) ?>" class="img-thumbnail w-100 mb-2" style="max-height:120px; object-fit:cover;">
                        <?php endif; ?>
                        <input type="file" name="ab_vision_img" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>
        </div>

        <!-- Alt Bölüm -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white"><strong><i class="bi bi-file-text me-1"></i> Alt Bölüm: "Tarımda Güvenin ve Kalitenin Adresi"</strong></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Başlık</label>
                    <input type="text" name="ab_bottom_title" class="form-control"
                           value="<?= htmlspecialchars(getContent($pdo, 'about_bottom', 'title', 'CMC Organik ile Tarımda Güvenin ve Kalitenin Adresi')) ?>">
                </div>
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= $i ?>. Paragraf</label>
                        <textarea name="ab_bottom_p<?= $i ?>" class="form-control" rows="3"><?= htmlspecialchars(getContent($pdo, 'about_bottom', "paragraph$i", '')) ?></textarea>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-save me-1"></i> Hakkımızda İçeriklerini Kaydet</button>
    </form>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
