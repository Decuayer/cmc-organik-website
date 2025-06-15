<?php
require_once __DIR__ . '/../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM products WHERE idproducts = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$product) {
    echo "Ürün bulunamadı.";
    exit;
}

$applyTypes = array_map('trim', explode(',', $product['applyType']));
$applyValues = array_map('trim', explode(',', $product['apply']));
$applySeperate = isset($product['applySeperate']) && $product['applySeperate']; // 1 ya da true ise aktif

$contents = array_filter(array_map('trim', explode(',', $product['contents'])));

$currentType = $product['type'];
$sqlRelated = "SELECT * FROM products WHERE type = ? AND idproducts != ? LIMIT 10";
$stmtRelated = $pdo->prepare($sqlRelated);
$stmtRelated->execute([$currentType, $product['idproducts']]);
$relatedProducts = $stmtRelated->fetchAll(PDO::FETCH_ASSOC);

$sqlComments = "SELECT * FROM comments WHERE productid = ? AND approved = 1 ORDER BY id DESC";
$stmtComments = $pdo->prepare($sqlComments);
$stmtComments->execute([$product['idproducts']]);
$comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);

?>

<section class="container py-5 my-5">
    <div class="card shadow-lg border-0">
        <div class="row g-0">
            <!-- Product Image -->
            <div class="col-md-5 outter-wrapper">
                <div class="product-detail-image-wrapper d-flex justify-content-center align-items-center rounded">
                    <img src="<?= htmlspecialchars($product['imgPath']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-detail-image">
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-md-7">
                <div class="card-body p-5">
                    <h2 class="card-title mb-3 display-5 fw-bold text-success">
                        <?= htmlspecialchars($product['name']) ?>
                    </h2>
                    <p class="text-muted mb-2 fs-5">
                        <strong>
                            Kategori:
                        </strong>
                        <?= htmlspecialchars($product['type']) ?>
                    </p>
                    <hr>
                    <p class="card-text fs-6">
                        <?= nl2br(htmlspecialchars($product['description'])) ?>
                    </p>
                    <?php if (!empty($contents)): ?>
                        <h5 class="mt-4 fw-semibold text-success">
                            İçerik
                        </h5>
                        <table class="table table-striped">
                            <tbody>
                                <?php foreach ($contents as $item): ?>
                                    <tr>
                                        <td>
                                            <?= htmlspecialchars($item) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    
                    <?php if (!empty($product['pack'])): ?>
                        <p class="mt-3">
                            <strong>
                                Ambalaj:
                            </strong>
                            <?= htmlspecialchars($product['pack']) ?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($applyTypes) && !empty($applyValues)): ?>
                        <h5 class="mt-4 fw-semibold text-success">Kullanım Dozları</h5>
                        <?php if ($applySeperate): ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <?php foreach ($applyTypes as $type): ?>
                                            <th><?= htmlspecialchars($type) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php foreach ($applyValues as $value): ?>
                                            <td>
                                                <ul class="mb-0">
                                                    <?php foreach (explode('-', $value) as $dose): ?>
                                                        <li><?= htmlspecialchars(trim($dose)) ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <ul>
                                <?php foreach ($applyTypes as $index => $type): ?>
                                    <li>
                                        <?= htmlspecialchars($type) ?>:
                                        <?= isset($applyValues[$index]) ? htmlspecialchars($applyValues[$index]) : 'Bilinmiyor' ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    <?php endif; ?>
                    <!-- Social Share -->
                    <div class="mt-4">
                        <h6 class="fw-bold">Bu ürünü paylaş:</h6>
                        <a href="#" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-facebook"></i> Facebook</a>
                        <a href="#" class="btn btn-outline-danger btn-sm me-2"><i class="fab fa-instagram"></i> Instagram</a>
                        <a href="#" class="btn btn-outline-info btn-sm me-2"><i class="fab fa-twitter"></i> X</a>
                        <a href="#" class="btn btn-outline-secondary btn-sm"><i class="fab fa-linkedin"></i> LinkedIn</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-5">
        <h3 class="mb-4 text-center text-success">Ürün Hakkında Yorumlar</h3>
        <?php if (count($comments) > 0): ?>
            <div id="comments-carousel" class="owl-carousel owl-theme">
                <?php foreach ($comments as $comment): ?>
                    <div class="item">
                        <div class="card h-100 p-4 border-0 custom-shadow">
                            <div class="d-flex flex-column align-items-center text-center">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" 
                                    style="width: 60px; height: 60px; font-size: 24px;">
                                    <?= strtoupper(substr($comment['name'], 0, 1)) ?>
                                </div>
                                <h5 class="mt-3 mb-1 fw-bold"><?= htmlspecialchars($comment['name']) ?></h5>
                                <p class="text-muted small mb-2"><?= htmlspecialchars($comment['email']) ?></p>
                            </div>
                            <p class="mt-3 text-center"><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">Bu ürün hakkında henüz yorum yapılmamış.</p>
        <?php endif; ?>
    </div>
    <!-- Related Products -->
    <div class="mt-5">
        <h3 class="mb-4 text-center text-success">İlginizi Çekebilecek Diğer Ürünler</h3>
        <div id="owl-demo" class="owl-carousel owl-theme">
            <?php foreach ($relatedProducts as $related): ?>
                <div class="item">
                    <div class="card h-100 shadow-sm pt-3" style="cursor: pointer;" onclick="window.location='product-detail.php?id=<?= urlencode($related['idproducts']) ?>'">
                        <img src="<?= htmlspecialchars($related['imgPath']) ?>" class="card-img-top" alt="<?= htmlspecialchars($related['name']) ?>" style=" max-width: 100%; max-height: 100%; object-fit: contain;">
                        <div class="card-body">
                            <h5 class="card-title text-success"><?= htmlspecialchars($related['name']) ?></h5>
                            <p class="card-text small text-muted">
                                <?= mb_strimwidth(strip_tags($related['description']), 0, 80, '...') ?>
                            </p>
                            <a href="product-detail.php?id=<?= $related['idproducts'] ?>" class="btn btn-sm btn-success">Detay</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>