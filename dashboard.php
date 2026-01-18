<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Inisialisasi data transaksi dummy jika belum ada
if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [
        ['id' => 1, 'date' => '2025-10-24', 'desc' => 'Belanja Bulanan', 'category' => 'Belanja', 'type' => 'expense', 'amount' => 1500000, 'status' => 'selesai'],
        ['id' => 2, 'date' => '2025-10-23', 'desc' => 'Gaji Oktober', 'category' => 'Pemasukan', 'type' => 'income', 'amount' => 15000000, 'status' => 'selesai'],
        ['id' => 3, 'date' => '2025-10-22', 'desc' => 'Bensin Mobil', 'category' => 'Transportasi', 'type' => 'expense', 'amount' => 350000, 'status' => 'selesai'],
        ['id' => 4, 'date' => '2025-10-21', 'desc' => 'Makan Siang', 'category' => 'Makanan', 'type' => 'expense', 'amount' => 45000, 'status' => 'pending'],
        ['id' => 5, 'date' => '2025-10-20', 'desc' => 'Listrik & Air', 'category' => 'Tagihan', 'type' => 'expense', 'amount' => 750000, 'status' => 'selesai'],
        ['id' => 6, 'date' => '2025-10-19', 'desc' => 'Kopi Pagi', 'category' => 'Makanan', 'type' => 'expense', 'amount' => 25000, 'status' => 'selesai'],
        ['id' => 7, 'date' => '2025-10-18', 'desc' => 'Project Freelance', 'category' => 'Pemasukan', 'type' => 'income', 'amount' => 3000000, 'status' => 'pending'],
    ];
}

// Hitung statistik
$total_saldo = 15000000;
$pemasukan_bulan_ini = 0;
$pengeluaran_bulan_ini = 0;
$total_transaksi = count($_SESSION['transactions']);

foreach ($_SESSION['transactions'] as $t) {
    if ($t['type'] === 'income') {
        $pemasukan_bulan_ini += $t['amount'];
    } else {
        $pengeluaran_bulan_ini += $t['amount'];
    }
}

// Hitung persentase perubahan (dummy data)
$persentase_saldo = 12;
$persentase_pemasukan = 8;
$persentase_pengeluaran = -5;

// Kategori pengeluaran
$kategori_stats = [
    'Makanan & Minuman' => 0,
    'Transportasi' => 0,
    'Belanja' => 0,
    'Lainnya' => 0
];

foreach ($_SESSION['transactions'] as $t) {
    if ($t['type'] === 'expense') {
        if ($t['category'] === 'Makanan' || $t['category'] === 'Minuman') {
            $kategori_stats['Makanan & Minuman'] += $t['amount'];
        } elseif ($t['category'] === 'Transportasi') {
            $kategori_stats['Transportasi'] += $t['amount'];
        } elseif ($t['category'] === 'Belanja') {
            $kategori_stats['Belanja'] += $t['amount'];
        } else {
            $kategori_stats['Lainnya'] += $t['amount'];
        }
    }
}

$total_pengeluaran_kategori = array_sum($kategori_stats);

function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Keuanganku</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
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
                <a href="dashboard.php" class="nav-item active">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="transaksi.php" class="nav-item">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                    <span>Transaksi</span>
                </a>
                <a href="kategori.php" class="nav-item">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                    <span>Kategori</span>
                </a>
                <a href="laporan.php" class="nav-item">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Laporan</span>
                </a>
                
                <div class="nav-divider">SYSTEM</div>
                
                <a href="pengaturan.php" class="nav-item">
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
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="page-header">
                <div class="header-left">
                    <h1>Selamat Datang, <?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]) ?> 👋</h1>
                    <p class="header-subtitle">Senin, 24 Oktober 2025</p>
                </div>
                <div class="header-right">
                    <button class="btn-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                    </button>
                    <button class="btn-primary" onclick="window.location.href='transaksi.php?action=add'">
                        + Tambah Transaksi
                    </button>
                </div>
            </header>
            
            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon blue">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                        <div class="stat-badge green">+<?= $persentase_saldo ?>%</div>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Saldo</div>
                        <div class="stat-value"><?= formatRupiah($total_saldo) ?></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon green">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                <polyline points="17 6 23 6 23 12"></polyline>
                            </svg>
                        </div>
                        <div class="stat-badge green">+<?= $persentase_pemasukan ?>%</div>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Pemasukan Bulan Ini</div>
                        <div class="stat-value"><?= formatRupiah($pemasukan_bulan_ini) ?></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon red">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline>
                                <polyline points="17 18 23 18 23 12"></polyline>
                            </svg>
                        </div>
                        <div class="stat-badge red"><?= $persentase_pengeluaran ?>%</div>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Pengeluaran Bulan Ini</div>
                        <div class="stat-value"><?= formatRupiah($pengeluaran_bulan_ini) ?></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon purple">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="12" y1="18" x2="12" y2="12"></line>
                                <line x1="9" y1="15" x2="15" y2="15"></line>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Transaksi</div>
                        <div class="stat-value"><?= $total_transaksi ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Chart & Categories -->
            <div class="dashboard-content">
                <div class="chart-section">
                    <div class="section-header">
                        <h2>Arus Kas</h2>
                        <p class="section-subtitle">Pemasukan vs Pengeluaran</p>
                        <select class="period-select" id="periodSelect">
                            <option value="week">Minggu Ini</option>
                            <option value="month" selected>Bulan Ini</option>
                            <option value="year">Tahun Ini</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <canvas id="cashFlowChart"></canvas>
                    </div>
                </div>
                
                <div class="categories-section">
                    <div class="section-header">
                        <h2>Kategori Pengeluaran</h2>
                    </div>
                    <div class="categories-list">
                        <?php 
                        $colors = ['blue' => '#3b82f6', 'purple' => '#8b5cf6', 'orange' => '#f59e0b', 'green' => '#10b981'];
                        $color_keys = array_keys($colors);
                        $i = 0;
                        foreach ($kategori_stats as $name => $amount): 
                            if ($amount > 0):
                                $percentage = round(($amount / $total_pengeluaran_kategori) * 100);
                                $color = $color_keys[$i % 4];
                        ?>
                        <div class="category-item">
                            <div class="category-header">
                                <div class="category-info">
                                    <span class="category-dot" style="background: <?= $colors[$color] ?>"></span>
                                    <span class="category-name"><?= $name ?></span>
                                </div>
                                <span class="category-percentage"><?= $percentage ?>%</span>
                            </div>
                            <div class="category-progress">
                                <div class="progress-bar" style="width: <?= $percentage ?>%; background: <?= $colors[$color] ?>"></div>
                            </div>
                        </div>
                        <?php 
                                $i++;
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="script.js"></script>
    <script>
        // Inisialisasi Chart
        const ctx = document.getElementById('cashFlowChart').getContext('2d');
        const cashFlowChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                datasets: [{
                    label: 'Pemasukan',
                    data: [3000000, 3500000, 4200000, 7300000],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3
                }, {
                    label: 'Pengeluaran',
                    data: [2000000, 2200000, 3800000, 4500000],
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleColor: '#f1f5f9',
                        bodyColor: '#cbd5e1',
                        borderColor: '#475569',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#334155',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000) + 'jt';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>