<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get period filter
$period = $_GET['period'] ?? 'month';
$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? date('m');

// Calculate totals
$total_income = 0;
$total_expense = 0;
$transactions_by_month = [];

if (isset($_SESSION['transactions'])) {
    foreach ($_SESSION['transactions'] as $t) {
        $t_month = date('m', strtotime($t['date']));
        $t_year = date('Y', strtotime($t['date']));
        
        if ($period === 'month' && $t_month == $month && $t_year == $year) {
            if ($t['type'] === 'income') {
                $total_income += $t['amount'];
            } else {
                $total_expense += $t['amount'];
            }
        } elseif ($period === 'year' && $t_year == $year) {
            if ($t['type'] === 'income') {
                $total_income += $t['amount'];
            } else {
                $total_expense += $t['amount'];
            }
            
            if (!isset($transactions_by_month[$t_month])) {
                $transactions_by_month[$t_month] = ['income' => 0, 'expense' => 0];
            }
            
            if ($t['type'] === 'income') {
                $transactions_by_month[$t_month]['income'] += $t['amount'];
            } else {
                $transactions_by_month[$t_month]['expense'] += $t['amount'];
            }
        }
    }
}

$balance = $total_income - $total_expense;
$saving_rate = $total_income > 0 ? round(($balance / $total_income) * 100) : 0;

function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

$months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Keuanganku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-container">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <header class="page-header">
                <div class="header-left">
                    <h1 class="page-title">Laporan Keuangan</h1>
                    <p class="page-description">Analisis mendalam tentang keuangan Anda</p>
                </div>
                <div class="header-right">
                    <button class="btn-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                    </button>
                    <button class="btn-primary" onclick="window.print()">
                        📊 Export PDF
                    </button>
                </div>
            </header>
            
            <!-- Period Filter -->
            <div class="report-filters">
                <form method="GET" class="filter-form">
                    <select name="period" class="filter-select" onchange="this.form.submit()">
                        <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>Bulanan</option>
                        <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>Tahunan</option>
                    </select>
                    
                    <?php if ($period === 'month'): ?>
                    <select name="month" class="filter-select" onchange="this.form.submit()">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $month == $m ? 'selected' : '' ?>>
                            <?= $months[$m - 1] ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                    <?php endif; ?>
                    
                    <select name="year" class="filter-select" onchange="this.form.submit()">
                        <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </form>
            </div>
            
            <!-- Summary Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon green">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                <polyline points="17 6 23 6 23 12"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Pemasukan</div>
                        <div class="stat-value" style="color: var(--success)"><?= formatRupiah($total_income) ?></div>
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
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Pengeluaran</div>
                        <div class="stat-value" style="color: var(--danger)"><?= formatRupiah($total_expense) ?></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon blue">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Saldo Bersih</div>
                        <div class="stat-value" style="color: <?= $balance >= 0 ? 'var(--success)' : 'var(--danger)' ?>">
                            <?= formatRupiah($balance) ?>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon purple">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M12 6v6l4 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Tingkat Tabungan</div>
                        <div class="stat-value"><?= $saving_rate ?>%</div>
                    </div>
                </div>
            </div>
            
            <!-- Charts -->
            <div class="dashboard-content">
                <div class="chart-section">
                    <div class="section-header">
                        <h2>Tren Keuangan</h2>
                        <p class="section-subtitle">Pemasukan vs Pengeluaran</p>
                    </div>
                    <div class="chart-container">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
                
                <div class="categories-section">
                    <div class="section-header">
                        <h2>Ringkasan</h2>
                    </div>
                    <div class="summary-list">
                        <div class="summary-item">
                            <span class="summary-label">Rata-rata Pemasukan</span>
                            <span class="summary-value"><?= formatRupiah($period === 'month' ? $total_income : round($total_income / 12)) ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Rata-rata Pengeluaran</span>
                            <span class="summary-value"><?= formatRupiah($period === 'month' ? $total_expense : round($total_expense / 12)) ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Pengeluaran Terbesar</span>
                            <span class="summary-value">Belanja</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Transaksi</span>
                            <span class="summary-value"><?= isset($_SESSION['transactions']) ? count($_SESSION['transactions']) : 0 ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('trendChart').getContext('2d');
        
        <?php if ($period === 'year'): ?>
        const labels = <?= json_encode($months) ?>;
        const incomeData = <?= json_encode(array_map(function($m) use ($transactions_by_month) {
            return $transactions_by_month[str_pad($m, 2, '0', STR_PAD_LEFT)]['income'] ?? 0;
        }, range(1, 12))) ?>;
        const expenseData = <?= json_encode(array_map(function($m) use ($transactions_by_month) {
            return $transactions_by_month[str_pad($m, 2, '0', STR_PAD_LEFT)]['expense'] ?? 0;
        }, range(1, 12))) ?>;
        <?php else: ?>
        const labels = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'];
        const incomeData = [<?= round($total_income * 0.2) ?>, <?= round($total_income * 0.3) ?>, <?= round($total_income * 0.25) ?>, <?= round($total_income * 0.25) ?>];
        const expenseData = [<?= round($total_expense * 0.3) ?>, <?= round($total_expense * 0.25) ?>, <?= round($total_expense * 0.2) ?>, <?= round($total_expense * 0.25) ?>];
        <?php endif; ?>
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pemasukan',
                    data: incomeData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3
                }, {
                    label: 'Pengeluaran',
                    data: expenseData,
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
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
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
                            color: '#e5e7eb'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>