<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
