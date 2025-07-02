<?php
require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require '../includes/sidebar.php';

$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$categoryCount = $pdo->query("SELECT COUNT(*) FROM product_types")->fetchColumn();
$pendingComments = $pdo->query("SELECT COUNT(*) FROM comments WHERE approved = 0")->fetchColumn();
$contactMessages = $pdo->query("SELECT COUNT(*) FROM contact")->fetchColumn();



?>
<div class="content" id="content" style="padding: 20px;">
    <div class="d-flex align-items-center mt-4 mb-3">
        <img src="../assets/uploads/logo-white.png" alt="Logo" style="height: 50px; max-height: 100%;" class="me-3">
        <div>
            <h2 class="m-0">Kontrol Paneli</h2>
            <p class="text-muted mb-0">Hoş geldiniz, <?= htmlspecialchars($_SESSION['admin_username']) ?> 👋</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="card border-success shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title">Toplam Ürün</h5>
                    <p class="card-text fs-3"><?= $productCount ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title">Kategoriler</h5>
                    <p class="card-text fs-3"><?= $categoryCount ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title">Onaysız Yorumlar</h5>
                    <p class="card-text fs-3"><?= $contactMessages ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title">Gelen Mesajlar</h5>
                    <p class="card-text fs-3"><?= $contactMessages ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    require_once '../includes/footer.php';
?>