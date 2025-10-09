<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// ID al
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ürün bilgisi
$stmt = $pdo->prepare("SELECT * FROM products WHERE idproducts = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo '<div class="alert alert-danger">Ürün bulunamadı.</div>';
    require_once '../includes/footer.php';
    exit;
}

// Kategoriler
$types = $pdo->query("SELECT * FROM product_types ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = $_POST['name'] ?? '';
    $type        = $_POST['type'] ?? null;
    $description = $_POST['description'] ?? '';
    $contents    = $_POST['contents'] ?? '';
    $pack        = $_POST['pack'] ?? '';
    $applyType   = $_POST['applyType'] ?? '';
    $apply       = $_POST['apply'] ?? '';
    $applySeperate = isset($_POST['applySeperate']) ? 1 : 0;

    // Fotoğraf yükleme
    $imgPath = null;
    $imgPath = $product['imgPath'] ?? null; // add.php'de $product olmayabilir, o yüzden null fallback

    $imgPath = $product['imgPath'] ?? "";

    // Eğer formda yeni görsel seçilmişse
    if (!empty($_FILES['imgPath']['name'])) {
        // Hedef klasörü ayarla
        $targetDir = __DIR__ . "/../../data/product/";

        // Klasör yoksa oluştur
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Dosya ismini güvenli hale getir
        $fileName = time() . "_" . basename($_FILES['imgPath']['name']);
        $targetFile = $targetDir . $fileName;

        // move_uploaded_file ile dosyayı taşı
        if (move_uploaded_file($_FILES['imgPath']['tmp_name'], $targetFile)) {
            // DB’de saklanacak yol (göreceli path)
            $imgPath = "data/product/" . $fileName;
        } else {
            // Debug çıktısı
            echo "<p style='color:red'><strong>Dosya yüklenemedi!</strong></p>";
            echo "<pre>";
            echo "Temp file: " . $_FILES['imgPath']['tmp_name'] . "\n";
            echo "Target file: " . $targetFile . "\n";
            echo "is_writable targetDir? " . (is_writable($targetDir) ? "YES" : "NO") . "\n";
            print_r(error_get_last());
            echo "</pre>";
        }
    }

    // Update
    $update = $pdo->prepare("
        UPDATE products SET 
            name = ?, 
            type = ?, 
            description = ?, 
            contents = ?, 
            pack = ?, 
            applyType = ?, 
            apply = ?, 
            imgPath = ?, 
            applySeperate = ?
        WHERE idproducts = ?
    ");
    $update->execute([$name, $type, $description, $contents, $pack, $applyType, $apply, $imgPath, $applySeperate, $id]);

    header("Location: products.php");
    exit;
}
?>

<div class="content" id="content" style="padding: 20px;">
    <h4 class="mb-4">Ürün Düzenle</h4>
    <form method="POST" enctype="multipart/form-data" class="row g-3">

        <div class="col-md-6">
            <label class="form-label">Ürün Adı</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Kategori</label>
            <select name="type" class="form-select" required>
                <option value="">Seçiniz</option>
                <?php foreach ($types as $t): ?>
                    <option value="<?= $t['name'] ?>" <?= ($product['type'] == $t['idproduct_types']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Açıklama</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>

        <div class="col-12">
            <label class="form-label">İçerikler</label>
            <textarea name="contents" class="form-control" rows="3"><?= htmlspecialchars($product['contents']) ?></textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">Ambalaj</label>
            <input type="text" name="pack" class="form-control" value="<?= htmlspecialchars($product['pack']) ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">Uygulama Türü (Virgül ile ayır)</label>
            <input type="text" name="applyType" class="form-control" value="<?= htmlspecialchars($product['applyType']) ?>">
        </div>

        <div class="col-12">
            <label class="form-label">Uygulama (Virgül ile ayır)</label>
            <textarea name="apply" class="form-control" rows="3"><?= htmlspecialchars($product['apply']) ?></textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">Ürün Görseli</label><br>
            <img src="../../<?= htmlspecialchars($product['imgPath']) ?>" alt="Mevcut Görsel" style="max-height: 100px; display:block; margin-bottom:10px;">
            <input type="file" name="imgPath" class="form-control">
        </div>

        <div class="col-md-6 d-flex align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="applySeperate" id="applySeperate" <?= $product['applySeperate'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="applySeperate">
                    Ayrı Uygulama Var mı? (Dozları ayrı sütunlarda göster. Aktif olduğunda, "-" ile ayrılmış dozlar ayrı sütunlarda gösterilir. "," ile sütunlar ayrılır.")
                </label>
            </div>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-success">Kaydet</button>
            <a href="products.php" class="btn btn-secondary">Geri Dön</a>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
