<?php
require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$categories = $pdo->query("SELECT * FROM product_types")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content" id="content" style="padding: 20px";>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">Yeni Ürün Ekle</h4>
        <a href="products.php" class="btn btn-danger"><- Geri</a>
    </div>
    <form action="products_add_process.php" method="POST" enctype="multipart/form-data">
        <div class="row g-3">
            <div class="col-md-6 my-5">
                <label class="form-label">Ürün Adı</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            
            <div class="col-md-6 my-5">
                <label class="form-label">Kategori</label>
                <select name="type" class="form-select" required>
                    <option value="Kategori Seçin"></option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['idproduct_types'] ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-12 my-5">
                <label class="form-label">Açıklama</label>
                <textarea name="description" rows="4" id="" class="form-control"></textarea>
            </div>

            <div class="col-md-12 my-5">
                <label class="form-label">İçerik (Virgül ile ayır)</label>
                <input type="text" name="contents" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Ambalaj</label>
                <input type="text" name="pack" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Uygulama Tipleri (virgül ile ayır)</label>
                <input type="text" name="applyType" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Uygulama Dozları (virgül ile ayır)</label>
                <input type="text" name="apply" class="form-control">
            </div>

            <div class="col-md-6 d-flex align-items-center mt-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="applySeperate" id="applySeperate" value="1">
                    <label class="form-check-label" for="applySeperate">
                        Dozları ayrı sütunlarda göster (Aktif olduğunda, "-" ile ayrılmış dozlar ayrı sütunlarda gösterilir. "," ile sütunlar ayrılır.")
                    </label>
                </div>
            </div>

            <div class="col-md-12 row my-5">
                <div class="col-md-12">
                    <label class="form-label">Ürün Görseli</label>
                    <input type="file" name="imgPath" class="form-control" required>
                </div>
                <div class="col-md-12 justify-content-center d-flex">
                    <img id="previewImage" src="#" alt="Görsel Önizleme" class="img-fluid mt-3 rounded shadow-sm d-none" style="max-height: 200px;">
                </div>
            </div>

            <div class="col-12 mt-4">
                <button class="btn btn-success w-100">Ürünü Kaydet</button>
            </div>
        </div>
    </form>
</div>

<script>
document.querySelector('input[name="imgPath"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('previewImage');

    if (file) {
        const reader = new FileReader();

        reader.onload = function(event) {
            preview.src = event.target.result;
            preview.classList.remove('d-none');
        };

        reader.readAsDataURL(file);
    } else {
        preview.classList.add('d-none');
        preview.src = '#';
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>