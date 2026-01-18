<?php
// Dapatkan halaman aktif
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <div class="logo-icon">K</div>
            <div class="logo-text">
                <div class="logo-title">Keuanganku</div>
                <div class="logo-subtitle">KeuanganPribadi</div>
            </div>
        </div>
        <button class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?= $current_page === 'dashboard' ? 'active' : '' ?>">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span>Dashboard</span>
        </a>
        <a href="transaksi.php" class="nav-item <?= $current_page === 'transaksi' ? 'active' : '' ?>">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                <line x1="1" y1="10" x2="23" y2="10"></line>
            </svg>
            <span>Transaksi</span>
        </a>
        <a href="kategori.php" class="nav-item <?= $current_page === 'kategori' ? 'active' : '' ?>">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                <line x1="7" y1="7" x2="7.01" y2="7"></line>
            </svg>
            <span>Kategori</span>
        </a>
        <a href="laporan.php" class="nav-item <?= $current_page === 'laporan' ? 'active' : '' ?>">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Laporan</span>
        </a>
        
        <div class="nav-divider">SYSTEM</div>
        
        <a href="pengaturan.php" class="nav-item <?= $current_page === 'pengaturan' ? 'active' : '' ?>">
            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M12 1v6m0 6v6m0-15a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2 2 2 0 0 0 2-2V3a2 2 0 0 0-2-2zm0 12a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2 2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2z"></path>
            </svg>
            <span>Pengaturan</span>
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?></div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
                <div class="user-email"><?= htmlspecialchars($_SESSION['user_email']) ?></div>
            </div>
        </div>
        <a href="logout.php" class="btn-logout" onclick="return confirm('Yakin ingin keluar?')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            Keluar
        </a>
    </div>
</aside>