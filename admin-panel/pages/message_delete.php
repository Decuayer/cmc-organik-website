<?php
require_once __DIR__ . '/../../config/env.php';

require_once '../includes/auth.php';
require_once '../../config/database.php';

// Mesaj ID kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: contact.php");
    exit;
}

$id = (int)$_GET['id'];

// Silme işlemi
$stmt = $pdo->prepare("DELETE FROM contact WHERE id = :id");
$stmt->execute(['id' => $id]);

// İşlem tamamlandıktan sonra mesaj listesine dön
header("Location: contact.php?deleted=1");
exit;
