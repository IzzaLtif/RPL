<?php
session_start();

// Redirect ke dashboard jika sudah login, ke login jika belum
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit();
?>