<?php
require_once __DIR__ . '/../../config/env.php';

require_once '../includes/auth.php';
require_once '../../config/database.php';

// id kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php?error=invalid_id");
    exit;
}

$id = (int)$_GET['id'];

try {
    // Ürünü çek (imgPath almak için)
    $stmt = $pdo->prepare("SELECT imgPath FROM products WHERE idproducts = :id");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        // Ürün yok
        header("Location: products.php?error=not_found");
        exit;
    }

    // Silme işlemi
    $del = $pdo->prepare("DELETE FROM products WHERE idproducts = :id");
    $del->execute(['id' => $id]);

    if ($del->rowCount() > 0) {
        // Eğer imgPath varsa sunucudan silmeye çalış
        if (!empty($product['imgPath'])) {
            // imgPath projenin köküne göre "public/img/..." gibi kayıtlıysa:
            $filePath = __DIR__ . '/../../' . ltrim($product['imgPath'], '/\\');

            if (file_exists($filePath) && is_writable(dirname($filePath))) {
                @unlink($filePath); // hata fırlatmasını istemiyorsak @ kullanıyoruz
            }
        }

        header("Location: products.php?deleted=1");
        exit;
    } else {
        header("Location: products.php?error=delete_failed");
        exit;
    }
} catch (PDOException $e) {
    // Hata loglama yapabilirsin: error_log($e->getMessage());
    header("Location: products.php?error=db_error");
    exit;
}
