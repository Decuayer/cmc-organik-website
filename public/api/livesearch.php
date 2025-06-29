<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';

if($search === '') {
    echo json_encode([]);
    exit;
}

try {
    $sql = "SELECT * FROM products WHERE name LIKE :q OR type LIKE :q LIMIT 10";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['q' => "%$search%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Veritabanı hatası: ' . $e->getMessage()
    ]);
}
exit;
?>