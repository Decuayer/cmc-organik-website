<?php
session_start();
require_once '../../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$currentRole = $_SESSION['admin_role'];
$currentId   = $_SESSION['admin_id'];

// Admin ekleme
if ($currentRole === 'superadmin' && isset($_POST['add_admin'])) {
    $username = $_POST['username'];
    $name     = $_POST['name'];
    $role     = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO admins (username, password, name, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $password, $name, $role]);
    header("Location: user.php");
    exit;
}

// Admin silme
if ($currentRole === 'superadmin' && isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id !== $currentId) { // kendi kendini silmeyi engelle
        $pdo->prepare("DELETE FROM admins WHERE id = ?")->execute([$id]);
    }
    header("Location: user.php");
    exit;
}

// Şifre değiştirme
if (isset($_POST['change_password'])) {
    $id = intval($_POST['id']);
    if ($currentRole === 'superadmin' || $id === $currentId) {
        $newPassword = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?")->execute([$newPassword, $id]);
    }
    header("Location: user.php");
    exit;
}

// Tüm adminleri çek (sadece superadmin görebilir)
$admins = [];
if ($currentRole === 'superadmin') {
    $admins = $pdo->query("SELECT * FROM admins ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$currentId]);
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="content" id="content" style="padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">Admin Yönetimi</h4>
    </div>

    <?php if ($currentRole === 'superadmin'): ?>
        <!-- Admin Ekleme Formu -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">Yeni Admin Ekle</div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <input type="text" name="username" class="form-control" placeholder="Kullanıcı Adı" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="text" name="name" class="form-control" placeholder="Ad Soyad" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Şifre" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <select name="role" class="form-control" required>
                                <option value="superadmin">Superadmin</option>
                                <option value="editor">Editor</option>
                                <option value="viewer">Viewer</option>
                            </select>
                        </div>
                        <div class="col-md-1 mb-3">
                            <button type="submit" name="add_admin" class="btn btn-success w-100">Ekle</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Admin Listesi -->
    <div class="table-responsive">
        <table class="table table-bordered align-middle table-hover">
            <thead class="table-success">
                <tr>
                    <th>#</th>
                    <th>Kullanıcı Adı</th>
                    <th>Ad Soyad</th>
                    <th>Rol</th>
                    <th>Şifre İşlemleri</th>
                    <?php if ($currentRole === 'superadmin'): ?>
                        <th>Sil</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $index => $admin): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($admin['username']) ?></td>
                        <td><?= htmlspecialchars($admin['name']) ?></td>
                        <td><?= htmlspecialchars($admin['role']) ?></td>
                        <td>
                            <form method="POST" class="d-flex">
                                <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                <input type="password" name="new_password" class="form-control form-control-sm me-2" placeholder="Yeni Şifre" required>
                                <button type="submit" name="change_password" class="btn btn-sm btn-primary">Değiştir</button>
                            </form>
                        </td>
                        <?php if ($currentRole === 'superadmin'): ?>
                            <td>
                                <?php if ($admin['id'] !== $currentId): ?>
                                    <a href="user.php?delete=<?= $admin['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu admini silmek istediğinize emin misiniz?');">Sil</a>
                                <?php else: ?>
                                    <span class="text-muted">Kendiniz</span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
