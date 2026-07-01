<?php
require_once __DIR__ . '/../../config/env.php';

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../../config/site_content.php';
require_once '../includes/csrf.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    setSiteContent($pdo, 'social_media', 'facebook_url',       trim($_POST['facebook_url'] ?? ''));
    setSiteContent($pdo, 'social_media', 'instagram_url',      trim($_POST['instagram_url'] ?? ''));
    setSiteContent($pdo, 'social_media', 'section_visible',    isset($_POST['section_visible']) ? '1' : '0');
    setSiteContent($pdo, 'social_media', 'facebook_visible',   isset($_POST['facebook_visible']) ? '1' : '0');
    setSiteContent($pdo, 'social_media', 'instagram_visible',  isset($_POST['instagram_visible']) ? '1' : '0');
    setSiteContent($pdo, 'social_media', 'instagram_embed',    trim($_POST['instagram_embed'] ?? ''));
    setSiteContent($pdo, 'social_media', 'facebook_desc',      trim($_POST['facebook_desc'] ?? ''));
    setSiteContent($pdo, 'social_media', 'instagram_desc',     trim($_POST['instagram_desc'] ?? ''));

    $success = 'Sosyal medya ayarları kaydedildi.';
}

// Mevcut değerleri oku
$fbUrl       = getSiteContent($pdo, 'social_media', 'facebook_url',      'https://www.facebook.com/cmcorganikizmir');
$igUrl       = getSiteContent($pdo, 'social_media', 'instagram_url',     'https://www.instagram.com/cmcorganik');
$sVisible    = getSiteContent($pdo, 'social_media', 'section_visible',   '1');
$fbVisible   = getSiteContent($pdo, 'social_media', 'facebook_visible',  '1');
$igVisible   = getSiteContent($pdo, 'social_media', 'instagram_visible', '1');
$igEmbed     = getSiteContent($pdo, 'social_media', 'instagram_embed',   '');
$fbDesc      = getSiteContent($pdo, 'social_media', 'facebook_desc',     'Facebook sayfamızı takip ederek güncel haberler ve kampanyalarımızdan haberdar olun.');
$igDesc      = getSiteContent($pdo, 'social_media', 'instagram_desc',    'Instagram hesabımızı takip ederek tarla fotoğraflarımızı ve güncel içeriklerimizi görün.');
?>

<div class="content" id="content" style="padding: 20px;">

    <div class="d-flex align-items-center mb-4">
        <div>
            <h4 class="m-0"><i class="bi bi-share me-1"></i> Sosyal Medya Yönetimi</h4>
            <p class="text-muted mb-0 mt-1">Anasayfadaki sosyal medya bölümünü yönetin.</p>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-1"></i><?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST">
        <?php csrf_field(); ?>

        <!-- Genel Görünürlük -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <strong><i class="bi bi-gear me-1"></i> Genel Ayarlar</strong>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="section_visible" id="section_visible"
                           <?= $sVisible === '1' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="section_visible">
                        <strong>Sosyal Medya Bölümünü Göster</strong>
                        <small class="text-muted d-block">Bu kapalıysa anasayfada sosyal medya bölümü görünmez.</small>
                    </label>
                </div>
            </div>
        </div>

        <!-- Facebook -->
        <div class="card shadow-sm mb-4">
            <div class="card-header" style="background: #1877f2; color: white;">
                <strong><i class="bi bi-facebook me-1"></i> Facebook Ayarları</strong>
            </div>
            <div class="card-body row g-3">
                <div class="col-12">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="facebook_visible" id="facebook_visible"
                               <?= $fbVisible === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="facebook_visible">Facebook kartını göster</label>
                    </div>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Facebook Sayfa URL'si</label>
                    <input type="url" name="facebook_url" class="form-control"
                           value="<?= htmlspecialchars($fbUrl) ?>"
                           placeholder="https://www.facebook.com/sayfaniz">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Açıklama Metni</label>
                    <textarea name="facebook_desc" class="form-control" rows="2"
                              placeholder="Facebook kartında görünecek açıklama..."><?= htmlspecialchars($fbDesc) ?></textarea>
                </div>
                <div class="col-12">
                    <div class="alert alert-info py-2 mb-0">
                        <small><i class="bi bi-info-circle me-1"></i> Facebook sayfanıza yönlendiren bir buton ve açıklama kartı oluşturulacak.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instagram -->
        <div class="card shadow-sm mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); color:white;">
                <strong><i class="bi bi-instagram me-1"></i> Instagram Ayarları</strong>
            </div>
            <div class="card-body row g-3">
                <div class="col-12">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="instagram_visible" id="instagram_visible"
                               <?= $igVisible === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="instagram_visible">Instagram kartını göster</label>
                    </div>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Instagram Profil URL'si</label>
                    <input type="url" name="instagram_url" class="form-control"
                           value="<?= htmlspecialchars($igUrl) ?>"
                           placeholder="https://www.instagram.com/hesabiniz">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Açıklama Metni</label>
                    <textarea name="instagram_desc" class="form-control" rows="2"
                              placeholder="Instagram kartında görünecek açıklama..."><?= htmlspecialchars($igDesc) ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Öne Çıkan Gönderi Embed Kodu <span class="text-muted">(isteğe bağlı)</span></label>
                    <textarea name="instagram_embed" class="form-control" rows="4"
                              placeholder="Instagram'dan kopyaladığınız embed kodunu buraya yapıştırın (blockquote...)&#10;Boş bırakırsanız sadece profil linki gösterilir."><?= htmlspecialchars($igEmbed) ?></textarea>
                    <small class="text-muted">
                        Instagram'da bir gönderi açın → "..." menüsü → "Göm" → kodu kopyalayın.
                    </small>
                </div>
            </div>
        </div>

        <!-- Önizleme Linki -->
        <div class="card shadow-sm mb-4 border-success">
            <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                <span><i class="bi bi-globe me-1"></i> Anasayfada nasıl göründüğünü kontrol edin</span>
                <a href="../../index.php" target="_blank" class="btn btn-outline-success">
                    Anasayfayı Aç →
                </a>
            </div>
        </div>

        <button type="submit" class="btn btn-success btn-lg px-5">
            <i class="bi bi-save me-1"></i> Ayarları Kaydet
        </button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
