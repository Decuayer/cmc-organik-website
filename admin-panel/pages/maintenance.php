<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Flag dosya yolu (projenin kök dizini)
$flagFile = realpath(__DIR__ . '/../../') . '/maintenance.flag';

$success = '';
$error = '';

// Bakım modunu aç
if (isset($_POST['enable_maintenance'])) {
    if (file_put_contents($flagFile, date('Y-m-d H:i:s') . ' - ' . ($_SESSION['admin_username'] ?? 'admin')) !== false) {
        $success = '✅ Bakım modu aktifleştirildi. Site ziyaretçileri artık bakım sayfasını görecek.';
    } else {
        $error = '❌ Flag dosyası oluşturulamadı. Dizin yazma izni kontrol edin.';
    }
}

// Bakım modunu kapat
if (isset($_POST['disable_maintenance'])) {
    if (file_exists($flagFile)) {
        if (unlink($flagFile)) {
            $success = '✅ Bakım modu kapatıldı. Site normal çalışmaya devam ediyor.';
        } else {
            $error = '❌ Flag dosyası silinemedi. Dizin yazma izni kontrol edin.';
        }
    } else {
        $success = '✅ Bakım modu zaten kapalıydı.';
    }
}

// Mevcut durum
$isMaintenanceActive = file_exists($flagFile);
$flagContent = $isMaintenanceActive ? trim(file_get_contents($flagFile)) : '';
?>

<div class="content" id="content" style="padding: 20px;">
    <div class="d-flex align-items-center mb-4">
        <div>
            <h4 class="m-0">🔧 Bakım Modu Yönetimi</h4>
            <p class="text-muted mb-0">Siteyi tek tıkla bakım moduna alın veya çıkarın.</p>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Durum Kartı -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-<?= $isMaintenanceActive ? 'danger' : 'success' ?> shadow-sm h-100">
                <div class="card-header bg-<?= $isMaintenanceActive ? 'danger' : 'success' ?> text-white d-flex align-items-center gap-2">
                    <span style="font-size:1.3rem;"><?= $isMaintenanceActive ? '🔴' : '🟢' ?></span>
                    <strong>Mevcut Durum</strong>
                </div>
                <div class="card-body text-center py-4">
                    <?php if ($isMaintenanceActive): ?>
                        <div class="display-6 text-danger mb-2">⛔ BAKIM MODU AKTİF</div>
                        <p class="text-muted">Site ziyaretçileri bakım sayfasını görüyor.</p>
                        <?php if ($flagContent): ?>
                            <small class="text-muted d-block mt-2">
                                <strong>Aktifleştirilme zamanı:</strong><br>
                                <?= htmlspecialchars($flagContent) ?>
                            </small>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="display-6 text-success mb-2">✅ SİTE ÇALIŞIYOR</div>
                        <p class="text-muted">Site normal şekilde ziyaretçilere açık.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <strong>⚡ Hızlı Kontrol</strong>
                </div>
                <div class="card-body d-flex flex-column justify-content-center gap-3">
                    <?php if ($isMaintenanceActive): ?>
                        <div class="alert alert-warning mb-2">
                            <strong>⚠️ Dikkat:</strong> Bakım modu aktif! Ziyaretçiler siteye erişemiyor.
                        </div>
                        <form method="POST">
                            <button type="submit" name="disable_maintenance"
                                class="btn btn-success btn-lg w-100"
                                onclick="return confirm('Bakım modunu kapatmak istediğinize emin misiniz?')">
                                🟢 Bakım Modunu KAPAT — Siteyi Aç
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-info mb-2">
                            <strong>ℹ️ Bilgi:</strong> Site şu an normal çalışıyor. Bakım çalışması için modu aktifleştirin.
                        </div>
                        <form method="POST">
                            <button type="submit" name="enable_maintenance"
                                class="btn btn-danger btn-lg w-100"
                                onclick="return confirm('Bakım modunu aktifleştirmek istediğinize emin misiniz? Ziyaretçiler siteye erişemeyecek!')">
                                🔴 Bakım Modunu AKTİFLEŞTİR — Siteyi Kapat
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bakım Sayfası Önizleme -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong>👁️ Bakım Sayfası Önizleme</strong>
            <a href="../../maintenance.html" target="_blank" class="btn btn-sm btn-outline-secondary">
                Yeni Sekmede Aç →
            </a>
        </div>
        <div class="card-body p-0">
            <iframe src="../../maintenance.html" 
                    style="width:100%; height:400px; border:none; border-radius: 0 0 4px 4px;"
                    title="Bakım Sayfası Önizleme">
            </iframe>
        </div>
    </div>

    <!-- Nasıl Çalışır -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <strong>📖 Nasıl Çalışır?</strong>
        </div>
        <div class="card-body">
            <ul class="mb-0">
                <li>Bakım modu aktifleştirildiğinde kök dizinde <code>maintenance.flag</code> dosyası oluşturulur.</li>
                <li><code>.htaccess</code> bu dosyanın varlığını kontrol ederek ziyaretçileri bakım sayfasına yönlendirir.</li>
                <li>Admin paneline (<code>/admin-panel</code>) bakım modunda da her zaman erişilebilir.</li>
                <li>Bakım modu kapatıldığında flag dosyası silinir ve site normal çalışmaya döner.</li>
                <li>Statik dosyalara (CSS, JS, görseller) erişim her zaman açıktır — bakım sayfası düzgün görünür.</li>
            </ul>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
