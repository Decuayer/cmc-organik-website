<?php
session_start();
require_once '../config/database.php';


$hata = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_role'] = $admin['role'];
        header("Location: pages/dashboard.php");
        exit;
    } else {
        $hata = 'Kullanıcı adı veya şifre hatalı.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/uploads/logo-white.png" type="image/x-icon">
    <title>Admin Giriş</title>
    <!-- CSS LIBS -->
    <link rel="stylesheet" href="assets/css/bootstrap.css?v=<?php echo time(); ?>">

</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 400px;">
            <h4 class="text-center mb-4">Admin Paneli Giriş</h4>
            <?php if ($hata): ?>
                <div class="alert alert-danger"><?= $hata ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label>Kullanıcı Adı</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label>Şifre</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button class="btn btn-success w-100">Giriş Yap</button>
            </form>
        </div>
    </div>    
</body>
</html>