<?php
require_once 'database.php';


$search = isset($_GET['q']) ? trim($_GET['q']) : '';

if($search === '') {
    echo json_encode([]);
    exit;
}

$sql = "SELECT * FROM products WHERE name LIKE :q OR type LIKE :q LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute(['q' => '%' . $search . '%']);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($results);
exit;

?>