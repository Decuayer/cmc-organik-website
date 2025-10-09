<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Tüm ürünleri çek
$stmt = $pdo->query("
    SELECT p.*, 
        CASE WHEN b.productid IS NOT NULL THEN 1 ELSE 0 END AS is_best 
    FROM products p
    LEFT JOIN best_products b ON p.idproducts = b.productid
    ORDER BY p.idproducts ASC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Eğer form gönderildiyse güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eski best_products kayıtlarını temizle
    $pdo->exec("DELETE FROM best_products");

    // Seçilenleri yeniden ekle
    if (!empty($_POST['best_products']) && is_array($_POST['best_products'])) {
        $insert = $pdo->prepare("INSERT INTO best_products (productid) VALUES (?)");
        foreach ($_POST['best_products'] as $productid) {
            $insert->execute([$productid]);
        }
    }

    echo '<div class="alert alert-success text-center m-3">Önerilen ürünler başarıyla güncellendi!</div>';
    header("refresh:2;url=best_products.php");
}
?>

<div class="content" id="content" style="padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">Öne Çıkan Ürünler</h4>
        <a href="products.php" class="btn btn-secondary">← Ürün Listesine Dön</a>
    </div>

    <form method="POST">
        <?php if (count($products) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Seç</th>
                            <th>Görsel</th>
                            <th>Ürün Adı</th>
                            <th>Kategori</th>
                            <th>Açıklama</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $index => $product): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td class="text-center align-middle">
                                    <input type="checkbox" name="best_products[]" value="<?= $product['idproducts'] ?>"
                                        <?= $product['is_best'] ? 'checked' : '' ?>>
                                </td>
                                <td class="text-center align-middle">
                                    <img src="../../<?= htmlspecialchars($product['imgPath']) ?>" alt="Ürün Görseli" style="height: 50px;">
                                </td>
                                <td><?= htmlspecialchars($product['name'] ?? 'Belirtilmemiş') ?></td>
                                <td><?= htmlspecialchars($product['type'] ?? 'Belirtilmemiş') ?></td>
                                <td><?= mb_strimwidth(htmlspecialchars($product['description']), 0, 50, '...') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-success">Kaydet</button>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Henüz ürün eklenmemiş.</div>
        <?php endif; ?>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
