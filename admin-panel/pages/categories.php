<?php
require_once __DIR__ . '/../../config/env.php';

require_once '../includes/auth.php';
require_once '../../config/database.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$success = '';
$error = '';

// Kategori adının aynı isimde başka bir kategoride kullanılıp kullanılmadığını kontrol eder
function categoryNameExists(PDO $pdo, string $name, int $excludeId = 0): bool {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_types WHERE name = ? AND idproduct_types != ?");
    $stmt->execute([$name, $excludeId]);
    return (int)$stmt->fetchColumn() > 0;
}

// Kategori ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $diff = isset($_POST['diff']) ? (int)$_POST['diff'] : 0;

    if (empty($name)) {
        $error = "Kategori adı boş olamaz.";
    } elseif (categoryNameExists($pdo, $name)) {
        $error = "Bu isimde bir kategori zaten var.";
    } else {
        $maxOrder = (int)$pdo->query("SELECT COALESCE(MAX(sort_order), 0) FROM product_types")->fetchColumn();
        $stmt = $pdo->prepare("INSERT INTO product_types (name, diff, sort_order) VALUES (?, ?, ?)");
        $stmt->execute([$name, $diff, $maxOrder + 1]);
        $success = "Kategori başarıyla eklendi.";
    }
}

// Kategori güncelleme (isim / ayraç)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_category'])) {
    $editId = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $diff = isset($_POST['diff']) ? (int)$_POST['diff'] : 0;

    if (empty($name)) {
        $error = "Kategori adı boş olamaz.";
    } elseif (categoryNameExists($pdo, $name, $editId)) {
        $error = "Bu isimde bir kategori zaten var.";
    } else {
        $stmt = $pdo->prepare("SELECT name FROM product_types WHERE idproduct_types = ?");
        $stmt->execute([$editId]);
        $oldName = $stmt->fetchColumn();

        if ($oldName === false) {
            $error = "Kategori bulunamadı.";
        } else {
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("UPDATE product_types SET name = ?, diff = ? WHERE idproduct_types = ?");
                $stmt->execute([$name, $diff, $editId]);

                // Ürünler kategoriye isim üzerinden bağlı olduğu için isim değiştiyse ürünleri de güncelle
                if ($oldName !== $name) {
                    $stmt = $pdo->prepare("UPDATE products SET type = ? WHERE type = ?");
                    $stmt->execute([$name, $oldName]);
                }

                $pdo->commit();
                $success = "Kategori başarıyla güncellendi.";
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = "Kategori güncellenirken bir hata oluştu.";
            }
        }
    }
}

// Kategori sırasını değiştirme (yukarı / aşağı)
if (isset($_GET['move']) && isset($_GET['id'])) {
    $direction = $_GET['move'] === 'up' ? 'up' : 'down';
    $moveId = (int)$_GET['id'];

    $ordered = $pdo->query("SELECT idproduct_types, sort_order FROM product_types ORDER BY sort_order ASC, idproduct_types ASC")->fetchAll(PDO::FETCH_ASSOC);
    $position = null;
    foreach ($ordered as $i => $row) {
        if ((int)$row['idproduct_types'] === $moveId) {
            $position = $i;
            break;
        }
    }

    if ($position !== null) {
        $swapWith = $direction === 'up' ? $position - 1 : $position + 1;
        if ($swapWith >= 0 && $swapWith < count($ordered)) {
            $current = $ordered[$position];
            $target = $ordered[$swapWith];

            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("UPDATE product_types SET sort_order = ? WHERE idproduct_types = ?");
                $stmt->execute([$target['sort_order'], $current['idproduct_types']]);
                $stmt->execute([$current['sort_order'], $target['idproduct_types']]);
                $pdo->commit();
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = "Sıralama güncellenirken bir hata oluştu.";
            }
        }
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

// Düzenlenecek kategori (varsa)
$editCategory = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM product_types WHERE idproduct_types = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editCategory = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Kategorileri ürün sayısı ile birlikte çek
$stmt = $pdo->query("
    SELECT t.idproduct_types, t.name, t.diff, t.sort_order, COUNT(p.idproducts) AS product_count
    FROM product_types t
    LEFT JOIN products p ON (p.type = t.idproduct_types OR p.type = t.name)
    GROUP BY t.idproduct_types, t.name, t.diff, t.sort_order
    ORDER BY t.sort_order ASC, t.idproduct_types ASC
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
        <div class="card-header bg-success text-white">
            <?= $editCategory ? 'Kategori Düzenle' : 'Yeni Kategori Ekle' ?>
        </div>
        <div class="card-body">
            <form method="POST">
                <?php if ($editCategory): ?>
                    <input type="hidden" name="id" value="<?= $editCategory['idproduct_types'] ?>">
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Kategori Adı</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($editCategory['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Ayraç (0 - 1)</label>
                        <input type="number" name="diff" class="form-control" value="<?= htmlspecialchars($editCategory['diff'] ?? 0) ?>" min="0" max="1">
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end gap-2">
                        <?php if ($editCategory): ?>
                            <button type="submit" name="edit_category" class="btn btn-success w-100">Kaydet</button>
                            <a href="categories.php" class="btn btn-secondary">Vazgeç</a>
                        <?php else: ?>
                            <button type="submit" name="add_category" class="btn btn-success w-100">+ Ekle</button>
                        <?php endif; ?>
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
                        <th style="width: 90px;">Sıra</th>
                        <th>Kategori Adı</th>
                        <th>Ürün Sayısı</th>
                        <th style="width: 220px;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $index => $cat): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td class="text-center">
                                <a href="categories.php?move=up&id=<?= $cat['idproduct_types'] ?>"
                                   class="btn btn-sm btn-outline-secondary <?= $index === 0 ? 'disabled' : '' ?>" title="Yukarı Taşı">▲</a>
                                <a href="categories.php?move=down&id=<?= $cat['idproduct_types'] ?>"
                                   class="btn btn-sm btn-outline-secondary <?= $index === count($categories) - 1 ? 'disabled' : '' ?>" title="Aşağı Taşı">▼</a>
                            </td>
                            <td><?= htmlspecialchars($cat['name'] ?? '-') ?></td>
                            <td><?= (int)$cat['product_count'] ?></td>
                            <td class="text-center align-middle">
                                <a href="categories.php?edit=<?= $cat['idproduct_types'] ?>" class="btn btn-sm btn-success">Düzenle</a>
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
