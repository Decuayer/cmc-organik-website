<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../config/database.php';

// Hangi sayfaya geri döneceğimizi al (referer yoksa ana sayfa)
$back = $_SERVER['HTTP_REFERER'] ?? '/';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $back);
    exit;
}

$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ' . $back . (strpos($back, '?') === false ? '?' : '&') . 'newsletter=invalid');
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (:email)");
    $stmt->execute(['email' => $email]);

    header('Location: ' . $back . (strpos($back, '?') === false ? '?' : '&') . 'newsletter=ok');
    exit;
} catch (PDOException $e) {
    // duplicate entry kontrolü (UNIQUE ile de engelleniyor)
    if ($e->getCode() == 23000) {
        header('Location: ' . $back . (strpos($back, '?') === false ? '?' : '&') . 'newsletter=exists');
        exit;
    } else {
        // loglama istersen: error_log($e->getMessage());
        header('Location: ' . $back . (strpos($back, '?') === false ? '?' : '&') . 'newsletter=error');
        exit;
    }
}
