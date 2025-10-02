<?php

if (!is_dir('../../data/product/')) {
    die('Klasör yok!');
}

require_once '../includes/auth.php';
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $type = $_POST['type'] ?? '';
    $description = $_POST['description'] ?? '';
    $contents = $_POST['contents'] ?? '';
    $pack = $_POST['pack'] ?? '';
    $applyType = $_POST['applyType'] ?? '';
    $apply = $_POST['apply'] ?? '';
    $applySeperate = isset($_POST['applySeperate']) ? 1 : 0;

    if (isset($_FILES['imgPath']) && $_FILES['imgPath']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = realpath(__DIR__ . '/../../data/product/') . '/';
        $fileTmp = $_FILES['imgPath']['tmp_name'];
        $fileName = basename($_FILES['imgPath']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueName = uniqid('product_') . '.' . $fileExt;
        $uploadPath = $uploadDir . $uniqueName;

        echo 'Yükleme yolu: ' . realpath($uploadPath);
        exit;


        if (!move_uploaded_file($fileTmp, $uploadPath)) {
            die('Dosya yüklenemedi.');
        }
    } else {
        die('Görsel yükleme başarısız.');
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, type, description, contents, pack, applyType, apply, imgPath, applySeperate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $name,
        $type,
        $description,
        $contents,
        $pack,
        $applyType,
        $apply,
        'data/product/' . $uniqueName,
        $applySeperate
    ]);

    header('Location: products.php');
    exit;

} else {
    header('Location: product_add.php');
    exit;
}
?>
