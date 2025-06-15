<?php 
require_once __DIR__ . '/../../config/database.php';

$sql = "
    SELECT *
    FROM products
    WHERE idproducts IN(
        SELECT productid FROM best_products
    )
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>


<div class="container marketing">
    <h2 class="pb-2 border-bottom">Öne Çıkan Ürünlerimiz</h2>
    <div class="row mt-4">
        <div  id="owl-demo" class="owl-carousel owl-theme">
            <?php foreach ($products as $product): ?>
                <div class="item">
                    <img src="<?= htmlspecialchars($product['imgPath']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="bd-placeholder-img rounded" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    <h2 class="fw-normal"><?= htmlspecialchars($product['name']) ?></h2>
                    <p class="text-justify">
                        <?= nl2br(htmlspecialchars(substr($product['description'], 0, 250))) ?>...
                    </p>
                    <p>
                        <a class="btn btn-success" href="product-detail.php?id=<?= $product['idproducts'] ?>">Detayları Görüntüle &raquo;</a>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
        
    </div>
</div>