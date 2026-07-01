<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

/**
 * Helper: check if a column exists in a table
 */
function columnExists(PDO $pdo, string $table, string $column): bool {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE :col");
    $stmt->execute(['col' => $column]);
    return (bool)$stmt->fetch();
}

/**
 * Helper: check if table exists
 */
function tableExists(PDO $pdo, string $table): bool {
    try {
        $stmt = $pdo->prepare("SHOW TABLES LIKE :tbl");
        $stmt->execute(['tbl' => $table]);
        return (bool)$stmt->fetch();
    } catch (PDOException $e) {
        return false;
    }
}

/* ---------- Basic DB counts ---------- */
$productCount = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$categoryCount = (int)$pdo->query("SELECT COUNT(*) FROM product_types")->fetchColumn();
$sumComments = (int)$pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
$approvedComments = (int)$pdo->query("SELECT COUNT(*) FROM comments WHERE approved = 1")->fetchColumn();
$pendingComments = (int)$pdo->query("SELECT COUNT(*) FROM comments WHERE approved = 0")->fetchColumn();
$contactMessages = (int)$pdo->query("SELECT COUNT(*) FROM contact")->fetchColumn();

/* ---------- Contact folders (important/trash) if migration applied ---------- */
$contactHasFolder = columnExists($pdo, 'contact', 'folder');
$importantMessages = 0;
$trashMessages = 0;
$unreadMessages = 0;
if ($contactHasFolder) {
    $importantMessages = (int)$pdo->query("SELECT COUNT(*) FROM contact WHERE folder = 'important'")->fetchColumn();
    $trashMessages = (int)$pdo->query("SELECT COUNT(*) FROM contact WHERE folder = 'trash'")->fetchColumn();
    // unread (not in trash)
    $unreadMessages = (int)$pdo->query("SELECT COUNT(*) FROM contact WHERE is_read = 0 AND (folder IS NULL OR folder != 'trash')")->fetchColumn();
}

/* ---------- Gallery image count (scan directory) ---------- */
$galleryCount = 0;
$galleryDir = realpath(__DIR__ . '/../../public/img/gallery/');
if ($galleryDir && is_dir($galleryDir)) {
    $allowed = ['jpg','jpeg','png','gif','webp','svg','JPG','JPEG','PNG'];
    $files = scandir($galleryDir);
    foreach ($files as $f) {
        if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $allowed)) {
            $galleryCount++;
        }
    }
}

/* ---------- Registration PDFs count ---------- */
$registrationCount = 0;
$regDir = realpath(__DIR__ . '/../../data/registration');
if ($regDir && is_dir($regDir)) {
    $pdfs = glob($regDir . '/*.pdf');
    $registrationCount = is_array($pdfs) ? count($pdfs) : 0;
}

/* ---------- Best products count (if table exists) ---------- */
$bestProductsCount = 0;
if (tableExists($pdo, 'best_products')) {
    $bestProductsCount = (int)$pdo->query("SELECT COUNT(*) FROM best_products")->fetchColumn();
}

/* ---------- Products missing image ---------- */
$productsMissingImage = (int)$pdo->query("
    SELECT COUNT(*) FROM products 
    WHERE imgPath IS NULL OR TRIM(imgPath) = '' 
")->fetchColumn();

/* ---------- Optionally: products without valid category (if you want) ---------- */
$productsWithoutCategory = (int)$pdo->query("
    SELECT COUNT(*) 
    FROM products p
    LEFT JOIN product_types t 
        ON TRIM(LOWER(p.type)) = TRIM(LOWER(t.name))
    WHERE t.idproduct_types IS NULL 
      OR p.type = '' 
      OR p.type IS NULL
")->fetchColumn();

<?php
// Bakım modu durum kontrolü
$maintenanceFlag = realpath(__DIR__ . '/../../') . '/maintenance.flag';
$isMaintenanceActive = file_exists($maintenanceFlag);
?>
<div class="content" id="content" style="padding: 20px;">

    <?php if ($isMaintenanceActive): ?>
    <div class="alert alert-danger d-flex align-items-center gap-3 mb-4" role="alert">
        <span style="font-size:1.8rem;">🔴</span>
        <div>
            <strong>Bakım Modu Aktif!</strong> Site ziyaretçileri bakım sayfasını görüyor.
            <a href="maintenance.php" class="btn btn-sm btn-danger ms-3">Yönet →</a>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-success d-flex align-items-center gap-3 mb-4" role="alert" style="padding: 8px 16px;">
        <span>🟢</span>
        <div class="d-flex align-items-center justify-content-between w-100">
            <small><strong>Site Çalışıyor</strong> — Ziyaretçiler siteye erişebiliyor.</small>
            <a href="maintenance.php" class="btn btn-sm btn-outline-success">Bakım Modu →</a>
        </div>
    </div>
    <?php endif; ?>

    <div class="d-flex align-items-center mt-2 mb-3">
        <img src="../assets/uploads/logo-white.png" alt="Logo" style="height: 50px; max-height: 100%;" class="me-3">
        <div>
            <h2 class="m-0">Kontrol Paneli</h2>
            <p class="text-muted mb-0">Hoş geldiniz, <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?> 👋</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card border-success shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Toplam Ürün</h6>
                    <p class="card-text fs-3"><?= $productCount ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Kategoriler</h6>
                    <p class="card-text fs-3"><?= $categoryCount ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-warning shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Onaysız Yorumlar</h6>
                    <p class="card-text fs-3"><?= $pendingComments ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Onaylı Yorumlar</h6>
                    <p class="card-text fs-3"><?= $approvedComments ?></p>
                </div>
            </div>
        </div>

        <!-- second row -->
        <div class="col-md-3">
            <div class="card border-info shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Toplam Yorumlar</h6>
                    <p class="card-text fs-3"><?= $sumComments ?></p>
                    <a href="comments.php" class="btn btn-sm btn-outline-success mt-2">Yorumlar</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-secondary shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Galeri Fotoğrafları</h6>
                    <p class="card-text fs-3"><?= $galleryCount ?></p>
                    <a href="gallery.php" class="btn btn-sm btn-outline-success mt-2">Galeri Yönetimi</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Tescil Dosyaları (PDF)</h6>
                    <p class="card-text fs-3"><?= $registrationCount ?></p>
                    <a href="documents.php" class="btn btn-sm btn-outline-success mt-2">Tescil Yönetimi</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-dark shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Öne Çıkan Ürünler</h6>
                    <p class="card-text fs-3"><?= $bestProductsCount ?></p>
                    <a href="best_products.php" class="btn btn-sm btn-outline-success mt-2">Öne Çıkan Ürünler</a>
                </div>
            </div>
        </div>

        <!-- third row -->
        <div class="col-md-3">
            <div class="card border-primary shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Gelen Mesajlar</h6>
                    <p class="card-text fs-3"><?= $contactMessages ?></p>
                    <?php if ($contactHasFolder): ?>
                        <small class="text-muted">Yeni: <?= $unreadMessages ?> — Önemli: <?= $importantMessages ?> — Çöp: <?= $trashMessages ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-danger shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Görsel Eksik Ürünler</h6>
                    <p class="card-text fs-3"><?= $productsMissingImage ?></p>
                    <small class="text-muted">(Fotoğrafı bulunmayan ürünler)</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-info shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Kategorisiz Ürünler</h6>
                    <p class="card-text fs-3"><?= $productsWithoutCategory ?></p>
                    <small class="text-muted">(Kategori eşleşmiyor ya da boş)</small>
                </div>
            </div>
        </div>

        <!-- you can add more cards here -->
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>
