<?php
// add_item.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'koneksi.php';

// Set Page Title
$page_title = "Tambah Barang";

$success = '';
$error = '';

// Ambil daftar jenis barang dari tabel jenis_barang
$result = $conn->query("SELECT id, nama_jenis_barang FROM jenis_barang");
$jenis_barang_list = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_barang = trim($_POST['nama_barang']);
    $jenis_barang_id = intval($_POST['jenis_barang']);
    $harga = floatval($_POST['harga']);
    $stok = intval($_POST['stok']);

    // Validasi input
    if (empty($nama_barang) || $jenis_barang_id <= 0 || $harga <= 0 || $stok < 0) {
        $error = "Semua field wajib diisi dengan benar.";
    } else {
        // Insert barang baru
        $stmt = $conn->prepare("INSERT INTO items (nama_barang, jenis_barang_id, harga, stok) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sidi", $nama_barang, $jenis_barang_id, $harga, $stok);

        if ($stmt->execute()) {
            $success = "Barang berhasil ditambahkan.";
        } else {
            $error = "Terjadi kesalahan saat menambahkan barang.";
        }

        $stmt->close();
    }
}
?>

<?php include 'header.php'; ?>

    <h2 class="mb-4">Tambah Barang Baru</h2>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="mb-3">
            <label for="nama_barang" class="form-label">Nama Barang</label>
            <input type="text" class="form-control" name="nama_barang" id="nama_barang" required>
        </div>
        <div class="mb-3">
            <label for="jenis_barang" class="form-label">Jenis Barang</label>
            <select class="form-select" name="jenis_barang" id="jenis_barang" required>
                <option value="" disabled selected>Pilih Jenis Barang</option>
                <?php foreach ($jenis_barang_list as $jenis): ?>
                    <option value="<?php echo $jenis['id']; ?>"><?php echo htmlspecialchars($jenis['nama_jenis_barang']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="harga" class="form-label">Harga</label>
            <input type="number" step="0.01" class="form-control" name="harga" id="harga" required>
        </div>
        <div class="mb-3">
            <label for="stok" class="form-label">Stok</label>
            <input type="number" class="form-control" name="stok" id="stok" required>
        </div>
        <button type="submit" class="btn btn-success">Tambah Barang</button>
    </form>
