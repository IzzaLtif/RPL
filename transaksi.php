<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Inisialisasi data transaksi jika belum ada
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

// Handle actions
$action = $_GET['action'] ?? '';
$message = '';
$message_type = '';

// Tambah transaksi
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_id = max(array_column($_SESSION['transactions'], 'id')) + 1;
    $new_transaction = [
        'id' => $new_id,
        'date' => $_POST['date'],
        'desc' => $_POST['description'],
        'category' => $_POST['category'],
        'type' => $_POST['type'],
        'amount' => (int)$_POST['amount'],
        'status' => $_POST['status']
    ];
    array_unshift($_SESSION['transactions'], $new_transaction);
    $message = 'Transaksi berhasil ditambahkan!';
    $message_type = 'success';
    $action = '';
}

// Edit transaksi
if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id = (int)$_POST['id'];
    foreach ($_SESSION['transactions'] as &$t) {
        if ($t['id'] === $edit_id) {
            $t['date'] = $_POST['date'];
            $t['desc'] = $_POST['description'];
            $t['category'] = $_POST['category'];
            $t['type'] = $_POST['type'];
            $t['amount'] = (int)$_POST['amount'];
            $t['status'] = $_POST['status'];
            break;
        }
    }
    $message = 'Transaksi berhasil diperbarui!';
    $message_type = 'success';
    $action = '';
}

// Hapus transaksi
if ($action === 'delete' && isset($_GET['id'])) {
    $delete_id = (int)$_GET['id'];
    $_SESSION['transactions'] = array_filter($_SESSION['transactions'], function($t) use ($delete_id) {
        return $t['id'] !== $delete_id;
    });
    $_SESSION['transactions'] = array_values($_SESSION['transactions']);
    $message = 'Transaksi berhasil dihapus!';
    $message_type = 'success';
    header('Location: transaksi.php');
    exit();
}

// Filter transaksi
$filtered_transactions = $_SESSION['transactions'];
$search = $_GET['search'] ?? '';
$filter_category = $_GET['category'] ?? '';
$filter_status = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

if ($search) {
    $filtered_transactions = array_filter($filtered_transactions, function($t) use ($search) {
        return stripos($t['desc'], $search) !== false;
    });
}

if ($filter_category && $filter_category !== 'all') {
    $filtered_transactions = array_filter($filtered_transactions, function($t) use ($filter_category) {
        return $t['category'] === $filter_category;
    });
}

if ($filter_status && $filter_status !== 'all') {
    $filtered_transactions = array_filter($filtered_transactions, function($t) use ($filter_status) {
        return $t['status'] === $filter_status;
    });
}

if ($date_from) {
    $filtered_transactions = array_filter($filtered_transactions, function($t) use ($date_from) {
        return $t['date'] >= $date_from;
    });
}

if ($date_to) {
    $filtered_transactions = array_filter($filtered_transactions, function($t) use ($date_to) {
        return $t['date'] <= $date_to;
    });
}

// Pagination
$per_page = 7;
$total = count($filtered_transactions);
$total_pages = ceil($total / $per_page);
$page = isset($_GET['page']) ? max(1, min($total_pages, (int)$_GET['page'])) : 1;
$offset = ($page - 1) * $per_page;
$paged_transactions = array_slice($filtered_transactions, $offset, $per_page);

// Get transaction for edit
$edit_transaction = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $edit_id = (int)$_GET['id'];
    foreach ($_SESSION['transactions'] as $t) {
        if ($t['id'] === $edit_id) {
            $edit_transaction = $t;
            break;
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
    <title>Transaksi - Keuanganku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="page-header">
                <div class="header-left">
                    <h1 class="page-title">Daftar Transaksi</h1>
                    <p class="page-description">Kelola semua pemasukan dan pengeluaran Anda</p>
                </div>
                <div class="header-right">
                    <button class="btn-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                    </button>
                    <button class="btn-primary" onclick="showModal('addModal')">
                        + Tambah Transaksi
                    </button>
                </div>
            </header>
            
            <?php if ($message): ?>
            <div class="alert alert-<?= $message_type ?>" id="alertMessage">
                <?= htmlspecialchars($message) ?>
            </div>
            <?php endif; ?>
            
            <!-- Filters -->
            <form method="GET" class="filters-container">
                <div class="search-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" name="search" placeholder="Cari transaksi..." value="<?= htmlspecialchars($search) ?>">
                </div>
                
                <input type="date" name="date_from" class="date-input" placeholder="Dari tanggal" value="<?= htmlspecialchars($date_from) ?>">
                
                <input type="date" name="date_to" class="date-input" placeholder="Sampai tanggal" value="<?= htmlspecialchars($date_to) ?>">
                
                <select name="category" class="filter-select">
                    <option value="all">Semua Kategori</option>
                    <option value="Pemasukan" <?= $filter_category === 'Pemasukan' ? 'selected' : '' ?>>Pemasukan</option>
                    <option value="Makanan" <?= $filter_category === 'Makanan' ? 'selected' : '' ?>>Makanan</option>
                    <option value="Transportasi" <?= $filter_category === 'Transportasi' ? 'selected' : '' ?>>Transportasi</option>
                    <option value="Belanja" <?= $filter_category === 'Belanja' ? 'selected' : '' ?>>Belanja</option>
                    <option value="Tagihan" <?= $filter_category === 'Tagihan' ? 'selected' : '' ?>>Tagihan</option>
                </select>
                
                <select name="status" class="filter-select">
                    <option value="all">Semua Status</option>
                    <option value="selesai" <?= $filter_status === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    <option value="pending" <?= $filter_status === 'pending' ? 'selected' : '' ?>>Pending</option>
                </select>
                
                <button type="submit" class="btn-primary">Filter</button>
                <a href="transaksi.php" class="btn-action" style="width: auto; padding: 10px 16px;">Reset</a>
            </form>
            
            <!-- Table -->
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>TANGGAL</th>
                            <th>DESKRIPSI</th>
                            <th>KATEGORI</th>
                            <th>STATUS</th>
                            <th>JUMLAH</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($paged_transactions)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-gray);">
                                Tidak ada transaksi ditemukan
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($paged_transactions as $t): ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($t['date'])) ?></td>
                            <td><strong><?= htmlspecialchars($t['desc']) ?></strong></td>
                            <td>
                                <span class="category-badge <?= strtolower($t['category']) ?>">
                                    <?= htmlspecialchars($t['category']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge <?= $t['status'] ?>">
                                    <?= ucfirst($t['status']) ?>
                                </span>
                            </td>
                            <td class="amount <?= $t['type'] ?>">
                                <?= $t['type'] === 'income' ? '+' : '-' ?> <?= formatRupiah($t['amount']) ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action" onclick="editTransaction(<?= htmlspecialchars(json_encode($t)) ?>)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </button>
                                    <button class="btn-action" onclick="confirmDelete(<?= $t['id'] ?>)">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <?php if ($total > 0): ?>
                <div class="pagination">
                    <div class="pagination-info">
                        Menampilkan <?= $offset + 1 ?> sampai <?= min($offset + $per_page, $total) ?> dari <?= $total ?> transaksi
                    </div>
                    <div class="pagination-buttons">
                        <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&<?= http_build_query($_GET) ?>" class="page-btn">Sebelumnya</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == $page): ?>
                            <button class="page-btn active"><?= $i ?></button>
                            <?php else: ?>
                            <a href="?page=<?= $i ?>&<?= http_build_query($_GET) ?>" class="page-btn"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?>&<?= http_build_query($_GET) ?>" class="page-btn">Selanjutnya</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <!-- Modal Add/Edit -->
    <div class="modal" id="transactionModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah Transaksi</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST" id="transactionForm">
                <input type="hidden" name="id" id="transactionId">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="date" id="transactionDate" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Tipe</label>
                        <select name="type" id="transactionType" required onchange="updateCategories()">
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Deskripsi</label>
                    <input type="text" name="description" id="transactionDesc" placeholder="Contoh: Gaji bulan ini" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="category" id="transactionCategory" required>
                            <option value="Pemasukan">Pemasukan</option>
                            <option value="Makanan">Makanan & Minuman</option>
                            <option value="Transportasi">Transportasi</option>
                            <option value="Belanja">Belanja</option>
                            <option value="Tagihan">Tagihan</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="transactionStatus" required>
                            <option value="selesai">Selesai</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Jumlah (Rp)</label>
                    <input type="number" name="amount" id="transactionAmount" placeholder="0" required min="0">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="script.js"></script>
    <script>
        function showModal(type) {
            const modal = document.getElementById('transactionModal');
            const form = document.getElementById('transactionForm');
            const title = document.getElementById('modalTitle');
            
            form.action = '?action=add';
            title.textContent = 'Tambah Transaksi';
            form.reset();
            document.getElementById('transactionDate').value = new Date().toISOString().split('T')[0];
            modal.classList.add('show');
        }
        
        function editTransaction(transaction) {
            const modal = document.getElementById('transactionModal');
            const form = document.getElementById('transactionForm');
            const title = document.getElementById('modalTitle');
            
            form.action = '?action=edit';
            title.textContent = 'Edit Transaksi';
            
            document.getElementById('transactionId').value = transaction.id;
            document.getElementById('transactionDate').value = transaction.date;
            document.getElementById('transactionDesc').value = transaction.desc;
            document.getElementById('transactionType').value = transaction.type;
            document.getElementById('transactionCategory').value = transaction.category;
            document.getElementById('transactionAmount').value = transaction.amount;
            document.getElementById('transactionStatus').value = transaction.status;
            
            updateCategories();
            modal.classList.add('show');
        }
        
        function closeModal() {
            document.getElementById('transactionModal').classList.remove('show');
        }
        
        function confirmDelete(id) {
            if (confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
                window.location.href = '?action=delete&id=' + id;
            }
        }
        
        function updateCategories() {
            const type = document.getElementById('transactionType').value;
            const category = document.getElementById('transactionCategory');
            
            if (type === 'income') {
                category.innerHTML = '<option value="Pemasukan">Pemasukan</option>';
            } else {
                category.innerHTML = `
                    <option value="Makanan">Makanan & Minuman</option>
                    <option value="Transportasi">Transportasi</option>
                    <option value="Belanja">Belanja</option>
                    <option value="Tagihan">Tagihan</option>
                `;
            }
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('transactionModal');
            if (event.target === modal) {
                closeModal();
            }
        }
        
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