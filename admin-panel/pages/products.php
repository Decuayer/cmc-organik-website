<?php
require_once __DIR__ . '/../../config/env.php';

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$stmt = $pdo->query("SELECT * FROM products ORDER BY idproducts ASC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = $pdo->query("SELECT name FROM product_types ORDER BY sort_order ASC, idproduct_types ASC")->fetchAll(PDO::FETCH_COLUMN);
?>
<div class="content" id="content" style="padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">Ürünler</h4>
        <a href="product_add.php" class="btn btn-success">+ Yeni Ürün Ekle</a>
    </div>
    <?php if (count($products) > 0): ?>
        <div class="row g-2 mb-3 align-items-center">
            <div class="col-md-5">
                <input type="search" id="productSearch" class="form-control" placeholder="Ürün adı veya açıklamada ara...">
            </div>
            <div class="col-md-4">
                <select id="categoryFilter" class="form-select">
                    <option value="">Tüm Kategoriler</option>
                    <?php foreach ($categories as $catName): ?>
                        <option value="<?= htmlspecialchars($catName) ?>"><?= htmlspecialchars($catName) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 text-md-end text-muted">
                <span id="resultCount"></span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle table-hover" id="productsTable">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Görsel</th>
                        <th class="sortable" data-sort="name" style="cursor:pointer;">Ürün Adı <span class="sort-arrow"></span></th>
                        <th class="sortable" data-sort="type" style="cursor:pointer;">Kategori <span class="sort-arrow"></span></th>
                        <th>Açıklama</th>
                        <th style="width: 150px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $index => $product): ?>
                        <tr data-name="<?= htmlspecialchars(mb_strtolower($product['name'] ?? '')) ?>"
                            data-description="<?= htmlspecialchars(mb_strtolower($product['description'] ?? '')) ?>"
                            data-type="<?= htmlspecialchars($product['type'] ?? '') ?>">
                            <td><?= $index + 1 ?></td>
                            <td class="text-center align-middle">
                                <img src="../../<?= htmlspecialchars($product['imgPath']) ?>" alt="Ürün Görseli" style="height: 50px;">
                            </td>
                            <td><?= htmlspecialchars($product['name'] ?? 'Belirtilmemiş') ?></td>
                            <td><span class="badge bg-success-subtle text-success-emphasis border border-success-subtle"><?= htmlspecialchars($product['type'] ?? 'Belirtilmemiş') ?></span></td>
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
        <div id="noResults" class="alert alert-info d-none">Aramanızla eşleşen ürün bulunamadı.</div>

        <script>
        (function () {
            var searchInput = document.getElementById('productSearch');
            var categorySelect = document.getElementById('categoryFilter');
            var table = document.getElementById('productsTable');
            var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
            var resultCount = document.getElementById('resultCount');
            var noResults = document.getElementById('noResults');
            var totalCount = rows.length;

            function applyFilters() {
                var query = searchInput.value.trim().toLowerCase();
                var category = categorySelect.value;
                var visibleCount = 0;

                rows.forEach(function (row) {
                    var matchesQuery = !query ||
                        row.dataset.name.indexOf(query) !== -1 ||
                        row.dataset.description.indexOf(query) !== -1;
                    var matchesCategory = !category || row.dataset.type === category;
                    var visible = matchesQuery && matchesCategory;
                    row.classList.toggle('d-none', !visible);
                    if (visible) visibleCount++;
                });

                resultCount.textContent = visibleCount + ' / ' + totalCount + ' ürün gösteriliyor';
                noResults.classList.toggle('d-none', visibleCount !== 0);
                table.closest('.table-responsive').classList.toggle('d-none', visibleCount === 0);
            }

            searchInput.addEventListener('input', applyFilters);
            categorySelect.addEventListener('change', applyFilters);
            applyFilters();

            // Sıralanabilir başlıklar
            var sortState = { column: null, asc: true };
            table.querySelectorAll('th.sortable').forEach(function (th) {
                th.addEventListener('click', function () {
                    var column = th.dataset.sort;
                    var asc = sortState.column === column ? !sortState.asc : true;
                    sortState = { column: column, asc: asc };

                    table.querySelectorAll('th.sortable .sort-arrow').forEach(function (arrow) {
                        arrow.textContent = '';
                    });
                    th.querySelector('.sort-arrow').textContent = asc ? '▲' : '▼';

                    var tbody = table.querySelector('tbody');
                    var key = column === 'name' ? 'name' : 'type';
                    var sorted = rows.slice().sort(function (a, b) {
                        var va = (key === 'name' ? a.dataset.name : a.dataset.type).toLowerCase();
                        var vb = (key === 'name' ? b.dataset.name : b.dataset.type).toLowerCase();
                        if (va < vb) return asc ? -1 : 1;
                        if (va > vb) return asc ? 1 : -1;
                        return 0;
                    });
                    sorted.forEach(function (row) { tbody.appendChild(row); });
                });
            });
        })();
        </script>
    <?php else: ?>
        <div class="alert alert-info">Henüz eklenmiş ürün yok.</div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
