<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$message_type = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);
    
    if (!empty($new_name) && !empty($new_email)) {
        $_SESSION['user_name'] = $new_name;
        $_SESSION['user_email'] = $new_email;
        
        // Update di array users juga
        foreach ($_SESSION['users'] as &$user) {
            if ($user['id'] === $_SESSION['user_id']) {
                $user['name'] = $new_name;
                $user['email'] = $new_email;
                break;
            }
        }
        
        $message = 'Profil berhasil diperbarui!';
        $message_type = 'success';
    } else {
        $message = 'Nama dan email tidak boleh kosong!';
        $message_type = 'error';
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $user = null;
    foreach ($_SESSION['users'] as &$u) {
        if ($u['id'] === $_SESSION['user_id']) {
            $user = &$u;
            break;
        }
    }
    
    if ($user && password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 6) {
                $user['password'] = password_hash($new_password, PASSWORD_DEFAULT);
                $message = 'Password berhasil diubah!';
                $message_type = 'success';
            } else {
                $message = 'Password minimal 6 karakter!';
                $message_type = 'error';
            }
        } else {
            $message = 'Password baru tidak cocok!';
            $message_type = 'error';
        }
    } else {
        $message = 'Password saat ini salah!';
        $message_type = 'error';
    }
}

// Handle reset data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_data'])) {
    unset($_SESSION['transactions']);
    $message = 'Data transaksi berhasil direset!';
    $message_type = 'success';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Keuanganku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-container">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <header class="page-header">
                <div class="header-left">
                    <h1 class="page-title">Pengaturan</h1>
                    <p class="page-description">Kelola akun dan preferensi Anda</p>
                </div>
            </header>
            
            <?php if ($message): ?>
            <div class="alert alert-<?= $message_type ?>" id="alertMessage">
                <?= htmlspecialchars($message) ?>
            </div>
            <?php endif; ?>
            
            <!-- Settings Sections -->
            <div class="settings-container">
                <!-- Profile Settings -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h2>👤 Profil Pengguna</h2>
                        <p>Informasi akun Anda</p>
                    </div>
                    <form method="POST" class="settings-form">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($_SESSION['user_name']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['user_email']) ?>" required>
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn-primary">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
                
                <!-- Password Settings -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h2>🔒 Keamanan</h2>
                        <p>Ubah password Anda</p>
                    </div>
                    <form method="POST" class="settings-form">
                        <div class="form-group">
                            <label>Password Saat Ini</label>
                            <input type="password" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" name="new_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" name="change_password" class="btn-primary">
                            Ubah Password
                        </button>
                    </form>
                </div>
                
                <!-- App Settings -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h2>⚙️ Preferensi</h2>
                        <p>Atur pengalaman aplikasi Anda</p>
                    </div>
                    <div class="settings-list">
                        <div class="settings-item">
                            <div class="settings-item-info">
                                <div class="settings-item-title">Notifikasi</div>
                                <div class="settings-item-desc">Terima pemberitahuan transaksi</div>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="settings-item">
                            <div class="settings-item-info">
                                <div class="settings-item-title">Dark Mode</div>
                                <div class="settings-item-desc">Tema gelap untuk kenyamanan mata</div>
                            </div>
                            <label class="toggle">
                                <input type="checkbox">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="settings-item">
                            <div class="settings-item-info">
                                <div class="settings-item-title">Mata Uang</div>
                                <div class="settings-item-desc">Rupiah (IDR)</div>
                            </div>
                            <select class="mini-select">
                                <option>IDR - Rupiah</option>
                                <option>USD - Dollar</option>
                                <option>EUR - Euro</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Data Management -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h2>🗄️ Manajemen Data</h2>
                        <p>Kelola data transaksi Anda</p>
                    </div>
                    <div class="settings-actions">
                        <button class="btn-secondary" onclick="alert('Export feature coming soon!')">
                            📥 Export Data
                        </button>
                        <button class="btn-secondary" onclick="alert('Import feature coming soon!')">
                            📤 Import Data
                        </button>
                        <form method="POST" style="display: inline;">
                            <button type="submit" name="reset_data" class="btn-danger" onclick="return confirm('Yakin ingin menghapus semua data transaksi?')">
                                🗑️ Reset Data
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- About -->
                <div class="settings-card">
                    <div class="settings-header">
                        <h2>ℹ️ Tentang Aplikasi</h2>
                    </div>
                    <div class="about-info">
                        <div class="about-item">
                            <strong>Versi:</strong> 1.0.0
                        </div>
                        <div class="about-item">
                            <strong>Dibuat:</strong> 2026
                        </div>
                        <div class="about-item">
                            <strong>Teknologi:</strong> PHP, MySQL, JavaScript
                        </div>
                        <div class="about-item">
                            <strong>Lisensi:</strong> MIT License
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="script.js"></script>
    <script>
        // Auto hide alert
        setTimeout(() => {
            const alert = document.getElementById('alertMessage');
            if (alert) {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }
        }, 3000);
    </script>
</body>
</html>