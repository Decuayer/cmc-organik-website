<?php

$host = 'localhost';
$dbname = 'cmc';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Kategori sıralaması için gereken sort_order kolonunu garanti altına al.
// Kolon yoksa oluşturulur ve mevcut sıraya göre (idproduct_types) doldurulur.
try {
    $col = $pdo->query("SHOW COLUMNS FROM product_types LIKE 'sort_order'")->fetch();
    if (!$col) {
        $pdo->exec("ALTER TABLE product_types ADD COLUMN sort_order INT NOT NULL DEFAULT 0");
        $pdo->exec("UPDATE product_types SET sort_order = idproduct_types");
    }
} catch (PDOException $e) {
    // Tablo yoksa ya da kolon eklenemiyorsa sessizce devam et; sıralama idproduct_types'a düşer.
}

?>