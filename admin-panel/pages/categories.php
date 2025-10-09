<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Kategori ekleme
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $diff = isset($_POST['diff']) ? (int)$_POST['diff'] : 0;

    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO product_types (name, diff) VALUES (?, ?)");
        $stmt->execute([$name, $diff]);
        $success = "Kategori başarıyla eklendi.";
    } else {
        $error = "Kategori adı boş olamaz.";
    }
}

// Kategori silme
if (isset($_GET['delete'])) {
    $catId = (int)$_GET['delete'];

    // Kategori adını al
    $stmt = $pdo->prepare("SELECT name FROM product_types WHERE idproduct_types = ?");
    $stmt->execute([$catId]);
    $catName = $stmt->fetchColumn();

    // Hem id'ye hem isme göre ürün sayısını kontrol et
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE type = ? OR type = ?");
    $stmt->execute([$catId, $catName]);
    $productCount = (int)$stmt->fetchColumn();

    if ($productCount > 0) {
        $error = "Bu kategoriye bağlı ürünler bulunduğu için silinemez!";
    } else {
        $stmt = $pdo->prepare("DELETE FROM product_types WHERE idproduct_types = ?");
        $stmt->execute([$catId]);
        $success = "Kategori başarıyla silindi.";
    }
}

// Kategorileri ürün sayısı ile birlikte çek
// JOIN koşulunu hem id hem name ile kontrol edecek şekilde genişlettik
$stmt = $pdo->query("
    SELECT t.idproduct_types, t.name, COUNT(p.idproducts) AS product_count
    FROM product_types t
    LEFT JOIN products p ON (p.type = t.idproduct_types OR p.type = t.name)
    GROUP BY t.idproduct_types, t.name
    ORDER BY t.idproduct_types ASC
");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content" id="content" style="padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">Kategoriler</h4>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header bg-success text-white">Yeni Kategori Ekle</div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Kategori Adı</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Ayraç (0 - 1)</label>
                        <input type="number" name="diff" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" name="add_category" class="btn btn-success w-100">+ Ekle</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (count($categories) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle table-hover">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Kategori Adı</th>
                        <th>Ürün Sayısı</th>
                        <th style="width: 120px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $index => $cat): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($cat['name'] ?? '-') ?></td>
                            <td><?= (int)$cat['product_count'] ?></td>
                            <td class="text-center align-middle">
                                <a href="categories.php?delete=<?= $cat['idproduct_types'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?');">
                                   Sil
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Henüz kategori yok.</div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
