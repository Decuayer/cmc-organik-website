<?php
require_once __DIR__ . '/../../config/env.php';

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Başarı ve hata mesajları
$success = '';
$error = '';

// Yorum ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $productId = (int)$_POST['productid'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $commentText = trim($_POST['comment']);

    if (!empty($name) && !empty($commentText) && $productId > 0) {
        $stmt = $pdo->prepare("INSERT INTO comments (productid, approved, name, email, phone, comment) VALUES (?, 0, ?, ?, ?, ?)");
        $stmt->execute([$productId, $name, $email, $phone, $commentText]);
        $success = "Yorum başarıyla eklendi, onay bekliyor.";
    } else {
        $error = "Ad, yorum ve ürün seçimi zorunludur.";
    }
}

// Yorum onaylama
if (isset($_GET['approve'])) {
    $commentId = (int)$_GET['approve'];
    $stmt = $pdo->prepare("UPDATE comments SET approved = 1 WHERE id = ?");
    $stmt->execute([$commentId]);
    $success = "Yorum onaylandı.";
}

// Yorum silme
if (isset($_GET['delete'])) {
    $commentId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$commentId]);
    $success = "Yorum silindi.";
}

// Yorumları çek (ürün bilgisi ile)
$stmt = $pdo->query("
    SELECT c.*, p.name AS product_name
    FROM comments c
    LEFT JOIN products p ON c.productid = p.idproducts
    ORDER BY c.id DESC
");
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ürünleri çek (yorum eklemek için)
$stmt = $pdo->query("SELECT idproducts, name FROM products ORDER BY name ASC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content" id="content" style="padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">Yorumlar</h4>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Yorum ekleme -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Yeni Yorum Ekle</div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Ürün Seç</label>
                        <select name="productid" class="form-control" required>
                            <option value="">Seçiniz...</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= $product['idproducts'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Ad Soyad</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Telefon</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Yorum</label>
                        <textarea name="comment" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="submit" name="add_comment" class="btn btn-success">+ Yorum Ekle</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Yorum listesi -->
    <?php if (count($comments) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Ürün</th>
                        <th>Ad Soyad</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Yorum</th>
                        <th>Onay Durumu</th>
                        <th style="width: 180px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comments as $index => $c): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($c['product_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($c['name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($c['email'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($c['phone'] ?? '-') ?></td>
                            <td style="max-width: 250px; word-wrap: break-word;"><?= htmlspecialchars($c['comment'] ?? '-') ?></td>
                            <td class="text-center">
                                <?= $c['approved'] ? "<span class='badge bg-success'>Onaylı</span>" : "<span class='badge bg-warning'>Onaysız</span>" ?>
                            </td>
                            <td class="text-center">
                                <?php if (!$c['approved']): ?>
                                    <a href="comments.php?approve=<?= $c['id'] ?>" class="btn btn-sm btn-success my-1">Onayla</a>
                                <?php endif; ?>
                                <a href="comments.php?delete=<?= $c['id'] ?>" class="btn btn-sm btn-danger my-1" onclick="return confirm('Bu yorumu silmek istediğinize emin misiniz?');">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Henüz yorum yok.</div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
