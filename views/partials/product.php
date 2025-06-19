<?php
require_once __DIR__ . '/../../config/database.php';

$category = isset($_GET['category']) ? $_GET['category'] : null;
$search = isset($_GET['q']) ? trim($_GET['q']) : null;


if ($search) {
    $sql = "SELECT * FROM products WHERE name LIKE :q OR description LIKE :q OR contents LIKE :q OR applyType LIKE :q OR apply LIKE :q OR type LIKE :q";    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['q' => '%' . $search . '%']);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pageTitle = "Arama Sonucu: " . htmlspecialchars($search);

    if(count($products) === 0) {
        $pageTitle .= " (Sonuç Bulunamadı, tüm ürünler listeleniyor)";

        $sqlAll = "SELECT * FROM products";
        $stmtAll = $pdo->prepare($sqlAll);
        $stmtAll->execute();
        $products = $stmtAll->fetchAll(PDO::FETCH_ASSOC);

        $showWarning = true;
    } else {
        $showWarning = false;
    }
} elseif ($category) {
    $sql = "SELECT * FROM products WHERE type = :category";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['category' => $category]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pageTitle = "Kategori: " . htmlspecialchars($category);
    $showWarning = false;
} else {
    $sql = "SELECT * FROM products";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pageTitle = "Tüm Ürünler";
    $showWarning = false;
}

?>
<section class="container py-5">
    <h2 class="pb-2 border-bottom"><?= $pageTitle ?></h2>
    <?php if (!empty($showWarning) && $showWarning): ?>
        <div class="alert alert-warning">
            Aramanıza uygun sonuç bulunamadı. Tüm ürünler listelenmektedir.
        </div>
    <?php endif; ?>
    <div class="row g-4">
    <?php foreach ($products as $product): ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm" style="cursor: pointer;" onclick="window.location='product-detail.php?id=<?= urlencode($product['idproducts']) ?>'">
                <div class="product-image-wrapper py-2">
                    <img src="<?= htmlspecialchars($product['imgPath']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                    <p class="card-text">
                        <?= mb_strimwidth(strip_tags($product['description']), 0, 100, "...") ?>
                    </p>
                    <ul>
                        <?php foreach( explode(",", $product['contents']) as $item): ?>
                            <?php if (trim($item) !== ''): ?>
                                <li><?= htmlspecialchars(trim($item)) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                    <?php 
                    $applyTypes = explode(",", $product['applyType']);
                    $apply = explode(",", $product['apply']);
                    ?>
                    <?php if (trim($product['apply']) !== ''): ?>
                        <p><strong>Kullanım:</strong></p>
                    <?php endif; ?>
                    <?php if (isset($product['applySeperate']) && $product['applySeperate'] == 1): ?>
                        <div class="row">
                            <?php foreach ($applyTypes as $index => $applyType): ?>
                                <?php if (trim($applyType) !== ''): ?>
                                    <div class="col-md-6 mb2">
                                        <strong><?= htmlspecialchars(trim($applyType)) ?></strong>
                                        <ul>
                                            <?php
                                            // Her apply verisi birden fazla satıra "-" ile ayrılıyorsa
                                            $lines = isset($apply[$index]) ? explode("-", $apply[$index]) : [];
                                            foreach ($lines as $line):
                                                if (trim($line) !== ''):
                                            ?>
                                                <li><?= htmlspecialchars(trim($line)) ?></li>
                                            <?php
                                                endif;
                                            endforeach;
                                            ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <ul>
                            <?php foreach($applyTypes as $index => $applyType): ?>
                                <?php if (trim($applyType) !== ''): ?>
                                    <li>
                                        <?= htmlspecialchars(trim($applyType)) ?>:
                                        <?= isset($apply[$index]) ? htmlspecialchars(trim($apply[$index])) : 'Bilinmiyor' ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-center">
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="product-detail.php?id=<?= urlencode($product['idproducts']) ?>'" class="btn btn-success">Detayları Görüntüle</a>
                        <a href="contact.php" class="btn btn-success">İletişime Geçin</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</section>