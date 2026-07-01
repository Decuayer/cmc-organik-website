<?php
require_once __DIR__ . '/../../config/env.php';

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/upload.php';
require_once '../includes/csrf.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$success = '';
$error = '';

$imgDir  = realpath(__DIR__ . '/../../public/img/');
$allowedExts = ['jpg','jpeg','png','gif','webp'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    csrf_verify();
}

// ── SLAYT EKLE ──────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'add_slide') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $btn_text    = trim($_POST['button_text'] ?? 'Devamını Oku');
    $btn_link    = trim($_POST['button_link'] ?? '#');
    $text_align  = in_array($_POST['text_align'] ?? '', ['start','center','end']) ? $_POST['text_align'] : 'start';
    $is_active   = isset($_POST['is_active']) ? 1 : 0;

    $maxOrder = (int)$pdo->query("SELECT COALESCE(MAX(sort_order),0) FROM carousel_slides")->fetchColumn();
    $sort_order = $maxOrder + 1;

    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        $uploaded = handleImageUpload($_FILES['image'], $imgDir, 'public/img', $allowedExts, 5 * 1024 * 1024, 'carousel_');
        if ($uploaded['ok']) {
            $image_path = $uploaded['path'];
        } else {
            $error = $uploaded['error'];
        }
    }

    if (!$error) {
        if (empty($title)) {
            $error = 'Başlık alanı zorunludur.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO carousel_slides (title, description, button_text, button_link, image_path, text_align, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$title, $description, $btn_text, $btn_link, $image_path, $text_align, $sort_order, $is_active]);
            $success = 'Slayt başarıyla eklendi.';
        }
    }
}

// ── SLAYT DÜZENLE ───────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'edit_slide') {
    $id          = (int)($_POST['slide_id'] ?? 0);
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $btn_text    = trim($_POST['button_text'] ?? 'Devamını Oku');
    $btn_link    = trim($_POST['button_link'] ?? '#');
    $text_align  = in_array($_POST['text_align'] ?? '', ['start','center','end']) ? $_POST['text_align'] : 'start';
    $is_active   = isset($_POST['is_active']) ? 1 : 0;

    $existing = $pdo->prepare("SELECT image_path FROM carousel_slides WHERE id = ?");
    $existing->execute([$id]);
    $row = $existing->fetch(PDO::FETCH_ASSOC);
    $image_path = $row ? $row['image_path'] : null;

    if (!empty($_FILES['image']['name'])) {
        $uploaded = handleImageUpload($_FILES['image'], $imgDir, 'public/img', $allowedExts, 5 * 1024 * 1024, 'carousel_');
        if ($uploaded['ok']) {
            if ($image_path && strpos($image_path, 'carousel_') !== false) {
                $oldFile = realpath(__DIR__ . '/../../') . '/' . $image_path;
                if ($oldFile && file_exists($oldFile)) @unlink($oldFile);
            }
            $image_path = $uploaded['path'];
        } else {
            $error = $uploaded['error'];
        }
    }

    if (isset($_POST['remove_image']) && empty($_FILES['image']['name'])) {
        if ($image_path && strpos($image_path, 'carousel_') !== false) {
            $oldFile = realpath(__DIR__ . '/../../') . '/' . $image_path;
            if (file_exists($oldFile)) @unlink($oldFile);
        }
        $image_path = null;
    }

    if (!$error) {
        if (empty($title)) {
            $error = 'Başlık alanı zorunludur.';
        } else {
            $stmt = $pdo->prepare("UPDATE carousel_slides SET title=?, description=?, button_text=?, button_link=?, image_path=?, text_align=?, is_active=? WHERE id=?");
            $stmt->execute([$title, $description, $btn_text, $btn_link, $image_path, $text_align, $is_active, $id]);
            $success = 'Slayt güncellendi.';
        }
    }
}

// ── SLAYT SİL ───────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'delete_slide') {
    $id = (int)($_POST['slide_id'] ?? 0);
    $row = $pdo->prepare("SELECT image_path FROM carousel_slides WHERE id = ?");
    $row->execute([$id]);
    $r = $row->fetch(PDO::FETCH_ASSOC);
    if ($r && $r['image_path'] && strpos($r['image_path'], 'carousel_') !== false) {
        $oldFile = realpath(__DIR__ . '/../../') . '/' . $r['image_path'];
        if (file_exists($oldFile)) @unlink($oldFile);
    }
    $pdo->prepare("DELETE FROM carousel_slides WHERE id = ?")->execute([$id]);
    $success = 'Slayt silindi.';
}

// ── AKTİF/PASİF TOGGLE ──────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'toggle_slide') {
    $id = (int)($_POST['slide_id'] ?? 0);
    $pdo->prepare("UPDATE carousel_slides SET is_active = 1 - is_active WHERE id = ?")->execute([$id]);
    header("Location: carousel.php");
    exit;
}

// ── SIRAYI GÜNCELLE ─────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'reorder') {
    $ids = json_decode($_POST['order'] ?? '[]', true);
    if (is_array($ids)) {
        foreach ($ids as $i => $sid) {
            $pdo->prepare("UPDATE carousel_slides SET sort_order = ? WHERE id = ?")->execute([$i + 1, (int)$sid]);
        }
    }
    echo json_encode(['ok' => true]);
    exit;
}

$slides = $pdo->query("SELECT * FROM carousel_slides ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content" id="content" style="padding: 20px;">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="m-0"><i class="bi bi-images me-1"></i> Carousel Yönetimi</h4>
            <p class="text-muted mb-0 mt-1">Anasayfa carousel slaytlarını ekleyin, düzenleyin ve sıralayın.</p>
        </div>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSlideModal">
            <i class="bi bi-plus-lg me-1"></i> Yeni Slayt Ekle
        </button>
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

    <?php if (empty($slides)): ?>
        <div class="alert alert-info">
            Henüz hiç slayt eklenmemiş. "Yeni Slayt Ekle" butonuyla ilk slaytı oluşturun.
        </div>
    <?php else: ?>
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
            <span><strong>Slaytlar</strong> — Toplam: <?= count($slides) ?></span>
            <small class="text-muted">Sıralamayı değiştirmek için <i class="bi bi-grip-vertical"></i> ikonunu sürükleyin</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:36px;"></th>
                        <th style="width:80px;">Görsel</th>
                        <th>Başlık / Açıklama</th>
                        <th>Buton</th>
                        <th>Hiza</th>
                        <th>Durum</th>
                        <th style="width:160px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody id="sortableSlides">
                    <?php foreach ($slides as $slide): ?>
                    <tr data-id="<?= $slide['id'] ?>">
                        <td class="drag-handle text-muted ps-3" style="cursor:grab; user-select:none; font-size:1.1rem;"><i class="bi bi-grip-vertical"></i></td>
                        <td>
                            <?php if ($slide['image_path']): ?>
                                <img src="../../<?= htmlspecialchars($slide['image_path']) ?>"
                                     alt="Slayt" style="width:70px; height:44px; object-fit:cover; border-radius:6px; border:1px solid #dee2e6;">
                            <?php else: ?>
                                <div style="width:70px;height:44px;background:#f8f9fa;border-radius:6px;border:1px dashed #dee2e6;display:flex;align-items:center;justify-content:center;">
                                    <small class="text-muted"><i class="bi bi-camera"></i></small>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong class="d-block"><?= htmlspecialchars($slide['title']) ?></strong>
                            <?php if ($slide['description']): ?>
                                <small class="text-muted"><?= htmlspecialchars(mb_substr($slide['description'], 0, 70)) ?>…</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($slide['button_text']) ?></span>
                            <br><small class="text-muted"><?= htmlspecialchars($slide['button_link']) ?></small>
                        </td>
                        <td>
                            <span class="badge <?= ['start'=>'bg-secondary','center'=>'bg-info','end'=>'bg-warning text-dark'][$slide['text_align']] ?? 'bg-secondary' ?>">
                                <?= ['start'=>'Sol', 'center'=>'Orta', 'end'=>'Sağ'][$slide['text_align']] ?? $slide['text_align'] ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" class="d-inline">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="action" value="toggle_slide">
                                <input type="hidden" name="slide_id" value="<?= $slide['id'] ?>">
                                <button type="submit" class="btn btn-sm <?= $slide['is_active'] ? 'btn-success' : 'btn-outline-secondary' ?>" style="min-width:75px; font-size:0.78rem;">
                                    <?= $slide['is_active'] ? '<i class="bi bi-check-circle-fill me-1"></i>Aktif' : '<i class="bi bi-square me-1"></i>Pasif' ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-success me-1"
                                    onclick='openEditModal(<?= htmlspecialchars(json_encode($slide), ENT_QUOTES) ?>)'>
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form method="POST" class="d-inline"
                                  onsubmit="return confirm('Bu slaytı silmek istediğinizden emin misiniz?')">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="action" value="delete_slide">
                                <input type="hidden" name="slide_id" value="<?= $slide['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- SLAYT EKLE MODALİ -->
<div class="modal fade" id="addSlideModal" tabindex="-1" aria-labelledby="addSlideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <?php csrf_field(); ?>
                <input type="hidden" name="action" value="add_slide">
                <div class="modal-header border-success">
                    <h5 class="modal-title" id="addSlideModalLabel"><i class="bi bi-plus-lg me-1"></i> Yeni Slayt Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Başlık <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="örn: İzmir Merkezli Köklü Firma" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Açıklama</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Slayt altında görünecek kısa tanıtım metni..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Buton Metni</label>
                        <input type="text" name="button_text" class="form-control" value="Devamını Oku" placeholder="örn: Hakkımızda">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Buton Linki</label>
                        <input type="text" name="button_link" class="form-control" value="about.php" placeholder="örn: about.php veya https://...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Metin Hizası</label>
                        <select name="text_align" class="form-select">
                            <option value="start">Sol (önerilen)</option>
                            <option value="center">Orta</option>
                            <option value="end">Sağ</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end pb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="add_is_active" checked>
                            <label class="form-check-label" for="add_is_active">Hemen yayınla (aktif)</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Slayt Görseli</label>
                        <input type="file" name="image" id="addImageInput" class="form-control" accept="image/*"
                               onchange="previewImage(this, 'addPreview')">
                        <div id="addPreview" class="mt-2" style="display:none;">
                            <img src="" alt="Önizleme" style="max-height:150px; border-radius:8px; border:1px solid #dee2e6;">
                        </div>
                        <small class="text-muted">JPG, PNG, WebP. Önerilen boyut: 1920×600px.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Slaytı Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SLAYT DÜZENLE MODALİ -->
<div class="modal fade" id="editSlideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <?php csrf_field(); ?>
                <input type="hidden" name="action" value="edit_slide">
                <input type="hidden" name="slide_id" id="edit_slide_id">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-1"></i> Slayt Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Başlık <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit_title" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Açıklama</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Buton Metni</label>
                        <input type="text" name="button_text" id="edit_button_text" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Buton Linki</label>
                        <input type="text" name="button_link" id="edit_button_link" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Metin Hizası</label>
                        <select name="text_align" id="edit_text_align" class="form-select">
                            <option value="start">Sol</option>
                            <option value="center">Orta</option>
                            <option value="end">Sağ</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end pb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active">
                            <label class="form-check-label" for="edit_is_active">Aktif olarak yayınla</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Görsel</label>
                        <div id="edit_current_img_wrap" class="mb-2"></div>
                        <div class="form-check mb-2" id="edit_remove_wrap" style="display:none;">
                            <input class="form-check-input" type="checkbox" name="remove_image" id="edit_remove_image">
                            <label class="form-check-label text-danger" for="edit_remove_image">Mevcut görseli kaldır</label>
                        </div>
                        <input type="file" name="image" id="editImageInput" class="form-control" accept="image/*"
                               onchange="previewImage(this, 'editPreview')">
                        <div id="editPreview" class="mt-2" style="display:none;">
                            <img src="" alt="Önizleme" style="max-height:150px; border-radius:8px; border:1px solid #dee2e6;">
                        </div>
                        <small class="text-muted">Yeni görsel seçerseniz mevcut olanın yerini alır.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Değişiklikleri Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const wrap = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            wrap.style.display = 'block';
            wrap.querySelector('img').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function openEditModal(slide) {
    document.getElementById('edit_slide_id').value    = slide.id;
    document.getElementById('edit_title').value        = slide.title || '';
    document.getElementById('edit_description').value  = slide.description || '';
    document.getElementById('edit_button_text').value  = slide.button_text || '';
    document.getElementById('edit_button_link').value  = slide.button_link || '';
    document.getElementById('edit_is_active').checked  = (slide.is_active == 1);
    document.getElementById('edit_text_align').value   = slide.text_align || 'start';

    const imgWrap = document.getElementById('edit_current_img_wrap');
    const rmWrap  = document.getElementById('edit_remove_wrap');
    if (slide.image_path) {
        imgWrap.innerHTML = `<img src="../../${slide.image_path}" alt="Mevcut" style="max-height:100px;border-radius:6px;border:1px solid #dee2e6;">`;
        rmWrap.style.display = 'block';
    } else {
        imgWrap.innerHTML = '<span class="text-muted small">Görsel yok</span>';
        rmWrap.style.display = 'none';
    }

    document.getElementById('editPreview').style.display = 'none';
    document.getElementById('editImageInput').value = '';

    new bootstrap.Modal(document.getElementById('editSlideModal')).show();
}

// Drag-drop sıralama (vanilla JS)
(function() {
    const tbody = document.getElementById('sortableSlides');
    if (!tbody) return;
    let dragging = null;

    tbody.querySelectorAll('tr').forEach(row => {
        const handle = row.querySelector('.drag-handle');
        if (!handle) return;

        handle.addEventListener('mousedown', () => { row.draggable = true; });
        row.addEventListener('dragstart', () => {
            dragging = row;
            row.style.opacity = '0.5';
        });
        row.addEventListener('dragend', () => {
            row.style.opacity = '';
            row.draggable = false;
            saveOrder();
        });
        row.addEventListener('dragover', e => {
            e.preventDefault();
            const rect = row.getBoundingClientRect();
            if (e.clientY < rect.y + rect.height / 2) {
                tbody.insertBefore(dragging, row);
            } else {
                tbody.insertBefore(dragging, row.nextSibling);
            }
        });
    });

    function saveOrder() {
        const ids = [...tbody.querySelectorAll('tr[data-id]')].map(r => r.dataset.id);
        fetch('carousel.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=reorder&csrf_token=<?= urlencode(csrf_token()) ?>&order=' + encodeURIComponent(JSON.stringify(ids))
        });
    }
})();
</script>

<?php require_once '../includes/footer.php'; ?>
