<?php
// sidebar.php

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil halaman saat ini
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="sidebar d-flex flex-column p-3" style="background-color: #343a40; min-height: 100vh;">
    <!-- Brand -->
    <a href="dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="bi bi-shop"></i>
        <span class="ms-2 d-none d-md-inline">Toko Komputer</span>
    </a>
    <hr>
    
    <!-- Menu Navigasi -->
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="dashboard.php" 
               class="nav-link text-white <?php echo ($current_page == 'dashboard.php') ? 'fw-bold' : ''; ?>">
                <i class="bi bi-card-checklist"></i>
                <span class="ms-2 d-none d-md-inline">Stok Barang</span>
            </a>
        </li>
        <li>
            <a href="add_item.php" 
               class="nav-link text-white <?php echo ($current_page == 'add_item.php') ? 'fw-bold' : ''; ?>">
                <i class="bi bi-plus-circle"></i>
                <span class="ms-2 d-none d-md-inline">Tambah Barang</span>
            </a>
        </li>
        <li>
            <a href="transaksi.php" 
               class="nav-link text-white <?php echo ($current_page == 'transaksi.php') ? 'fw-bold' : ''; ?>">
                <i class="bi bi-card-checklist"></i>
                <span class="ms-2 d-none d-md-inline">Transaksi</span>
            </a>
        </li>
        <li>
            <a href="add_transaksi.php" 
               class="nav-link text-white <?php echo ($current_page == 'add_transaksi.php') ? 'fw-bold' : ''; ?>">
                <i class="bi bi-plus-square"></i>
                <span class="ms-2 d-none d-md-inline">Tambah Transaksi</span>
            </a>
        </li>
        <li>
            <a href="jenisbarang.php" 
               class="nav-link text-white <?php echo ($current_page == 'jenis_barang.php') ? 'fw-bold' : ''; ?>">
                <i class="bi bi-tags"></i>
                <span class="ms-2 d-none d-md-inline">Jenis Barang</span>
            </a>
        </li>
    </ul>
    <hr>
    <!-- Logout -->
    <div class="mt-auto">
        <a href="logout.php" class="btn btn-danger w-100">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</nav>
