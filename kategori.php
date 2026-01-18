<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Hitung statistik per kategori dari transaksi
$categories = [
    'Makanan' => ['total' => 0, 'count' => 0, 'color' => '#3b82f6'],
    'Transportasi' => ['total' => 0, 'count' => 0, 'color' => '#8b5cf6'],
    'Belanja' => ['total' => 0, 'count' => 0, 'color' => '#ef4444'],
    'Tagihan' => ['total' => 0, 'count' => 0, 'color' => '#f59e0b'],
    'Pemasukan' => ['total' => 0, 'count' => 0, 'color' => '#10b981']
];

if (isset($_SESSION['transactions'])) {
    foreach ($_SESSION['transactions'] as $t) {
        if (isset($categories[$t['category']])) {
            $categories[$t['category']]['total'] += $t['amount'];
            $categories[$t['category']]['count']++;
        }
    }
}

function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori - Keuanganku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-container">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <header class="page-header">
                <div class="header-left">
                    <h1 class="page-title">Kategori Transaksi</h1>
                    <p class="page-description">Kelola dan pantau pengeluaran per kategori</p>
                </div>
                <div class="header-right">
                    <button class="btn-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                    </button>
                </div>
            </header>
            
            <!-- Category Stats -->
            <div class="category-grid">
                <?php foreach ($categories as $name => $data): ?>
                <div class="category-card">
                    <div class="category-card-header">
                        <div class="category-icon" style="background: <?= $data['color'] ?>20; color: <?= $data['color'] ?>">
                            <?php
                            $icons = [
                                'Makanan' => '🍔',
                                'Transportasi' => '🚗',
                                'Belanja' => '🛍️',
                                'Tagihan' => '💡',
                                'Pemasukan' => '💰'
                            ];
                            echo $icons[$name];
                            ?>
                        </div>
                        <h3><?= $name ?></h3>
                    </div>
                    <div class="category-card-body">
                        <div class="category-amount">
                            <?= formatRupiah($data['total']) ?>
                        </div>
                        <div class="category-count">
                            <?= $data['count'] ?> transaksi
                        </div>
                    </div>
                    <div class="category-card-footer">
                        <a href="transaksi.php?category=<?= urlencode($name) ?>" class="category-link">
                            Lihat Detail →
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Monthly Comparison -->
            <div class="chart-section" style="margin-top: 32px;">
                <div class="section-header">
                    <h2>Perbandingan Kategori</h2>
                    <p class="section-subtitle">Distribusi pengeluaran bulan ini</p>
                </div>
                <div class="chart-container" style="height: 400px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </main>
    </div>
    
    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Category Chart
        const ctx = document.getElementById('categoryChart').getContext('2d');
        const categoryData = <?= json_encode(array_values(array_column($categories, 'total'))) ?>;
        const categoryLabels = <?= json_encode(array_keys($categories)) ?>;
        const categoryColors = <?= json_encode(array_column($categories, 'color')) ?>;
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: categoryColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 14
                            }
                        }
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
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return context.label + ': Rp ' + value.toLocaleString('id-ID') + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>