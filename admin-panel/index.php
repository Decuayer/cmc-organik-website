<?php
echo "Admin Paneli Giriş Kontrolü";
session_start();

if (isset($_SESSION['admin_id'])) {
    header("Location: pages/dashboard.php");
} else {
    header("Location: login.php");
}
exit;
?>