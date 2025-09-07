<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$stmt = $pdo->query("
    SELECT p.*, t.name AS type_name 
    FROM products p
    LEFT JOIN product_types t ON p.type = t.idproduct_types
    ORDER BY p.idproducts ASC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="content" id="content" style="padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">Ürünler</h4>
        <a href="product_add.php" class="btn btn-success">+ Yeni Ürün Ekle</a>
    </div>
    <?php if (count($products) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle table-hover">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Görsel</th>
                        <th>Ürün Adı</th>
                        <th>Kategori</th>
                        <th>Açıklama</th>
                        <th style="width: 150px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $index => $product): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td class="text-center align-middle">
                                <img src="../../<?= htmlspecialchars($product['imgPath']) ?>" alt="Ürün Görseli" style="height: 50px;">
                            </td>
                            <td><?= htmlspecialchars($product['name'] ?? 'Belirtilmemiş') ?></td>
                            <td><?= htmlspecialchars($product['type'] ?? 'Belirtilmemiş') ?></td>
                            <td><?= mb_strimwidth(htmlspecialchars($product['description']), 0, 50, '...') ?></td>
                            <td class="text-center align-middle">
                                <a href="product_edit.php?id=<?= $product['idproducts'] ?>" class="btn btn-sm btn-success my-1">Düzenle</a>
                                <a href="product_delete.php?id=<?= $product['idproducts'] ?>" class="btn btn-sm btn-danger my-1" onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?');">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Henüz eklenmiş ürün yok.</div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>