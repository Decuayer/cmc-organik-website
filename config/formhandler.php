<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'database.php';

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode([
        "status" => "error",
        "message" => "Lütfen tüm zorunlu alanları doldurunuz."
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM contact WHERE email = ? AND created_at > (NOW() - INTERVAL 24 HOUR)");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Bu e-posta ile son 24 saat içinde bir mesaj gönderildi. Lütfen daha sonra tekrar deneyin."
        ]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO contact (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $subject, $message]);
    echo json_encode([
        "status" => "success",
        "message" => "Mesajınız başarıyla gönderildi. Size en kısa sürede geri dönüş yapılacaktır."
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Veritabanı hatası: " . $e->getMessage()
    ]);
}
?>