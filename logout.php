<?php
session_start();

// Hapus session user (tapi pertahankan data users)
$users_backup = $_SESSION['users'] ?? [];

session_destroy();

// Mulai session baru dan restore data users
session_start();
$_SESSION['users'] = $users_backup;

header('Location: login.php');
exit();
?>