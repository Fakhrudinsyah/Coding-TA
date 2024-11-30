<?php
require 'koneksi.php';
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Periksa apakah parameter ID ada
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$item_id = intval($_GET['id']);

// Ambil data barang berdasarkan ID
$stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: dashboard.php");
    exit();
}

$item = $result->fetch_assoc();
$stmt->close();

// Ambil daftar jenis barang
$result = $conn->query("SELECT id, nama_jenis_barang FROM jenis_barang");
$jenis_barang_list = $result->fetch_all(MYSQLI_ASSOC);

// Proses form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_barang = htmlspecialchars(trim($_POST['nama_barang']));
    $jenis_barang_id = intval($_POST['jenis_barang']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);

    // Validasi input
    if (empty($nama_barang) || $jenis_barang_id <= 0 || $harga <= 0 || $stok < 0) {
        $error = "Semua field harus diisi dengan benar.";
    } else {
        // Mulai transaksi database
        $conn->begin_transaction();

        try {
            // Update data barang
            $stmt = $conn->prepare("UPDATE items SET nama_barang = ?, jenis_barang_id = ?, harga = ?, stok = ? WHERE id = ?");
            $stmt->bind_param("siiii", $nama_barang, $jenis_barang_id, $harga, $stok, $item_id);
            $stmt->execute();
            $stmt->close();

            // Commit transaksi
            $conn->commit();

            // Set pesan sukses dan redirect ke dashboard
            $_SESSION['success'] = "Barang berhasil diupdate.";
            header("Location: dashboard.php");
            exit();
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $conn->rollback();
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Toko Komputer</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_item.php">Tambah Barang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transactions.php">Transaksi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_transaction.php">Tambah Transaksi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="stock_history.php">History Stok</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Edit Barang</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="nama_barang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" name="nama_barang" required value="<?php echo htmlspecialchars($item['nama_barang']); ?>">
            </div>
            <div class="mb-3">
                <label for="jenis_barang" class="form-label">Jenis Barang</label>
                <select class="form-select" name="jenis_barang" id="jenis_barang" required>
                    <option value="" disabled>Pilih Jenis Barang</option>
                    <?php foreach ($jenis_barang_list as $jenis): ?>
                        <option value="<?php echo $jenis['id']; ?>" <?php echo ($jenis['id'] == $item['jenis_barang_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($jenis['nama_jenis_barang']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="number" class="form-control" name="harga" required min="1" value="<?php echo htmlspecialchars($item['harga']); ?>">
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" class="form-control" name="stok" required min="0" value="<?php echo htmlspecialchars($item['stok']); ?>">
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Barang</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/scripts.js"></script>
</body>
</html>
