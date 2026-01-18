<?php
session_start();

// Redirect ke dashboard jika sudah login
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Inisialisasi data user dummy di session (untuk tahap awal)
if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        [
            'id' => 1,
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => password_hash('demo123', PASSWORD_DEFAULT)
        ]
    ];
}

$error = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    foreach ($_SESSION['users'] as $user) {
        if ($user['email'] === $email && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            header('Location: dashboard.php');
            exit();
        }
    }
    
    $error = 'Email atau password salah!';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Keuangan Harian</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>💰 Keuangan Harian</h1>
                <p>Kelola keuangan Anda dengan mudah</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="demo@example.com">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="demo123">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Masuk</button>
            </form>
            
            <div class="auth-footer">
                <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>
            
            <div class="demo-info">
                <strong>Demo Account:</strong><br>
                Email: demo@example.com<br>
                Password: demo123
            </div>
        </div>
    </div>
</body>
</html>