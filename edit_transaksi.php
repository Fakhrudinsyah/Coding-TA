<?php
// Memulai sesi dan mengecek apakah user sudah login
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Menghubungkan ke database
require 'koneksi.php';

// Mendapatkan ID transaksi dari parameter URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk mendapatkan detail transaksi berdasarkan ID
    $sql = "SELECT * FROM transactions WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika transaksi ditemukan
    if ($result->num_rows > 0) {
        $transaksi = $result->fetch_assoc();
    } else {
        echo "Transaksi tidak ditemukan.";
        exit();
    }
} else {
    echo "ID tidak valid.";
    exit();
}

// Jika form disubmit untuk mengupdate data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $tipe_transaksi = $_POST['tipe_transaksi'];
    $tanggal = $_POST['tanggal'];
    $jumlah = $_POST['jumlah'];

    // Validasi dan update transaksi
    if (!empty($tipe_transaksi) && !empty($tanggal) && !empty($jumlah)) {
        $sql = "UPDATE transactions SET tipe_transaksi = ?, tanggal = ?, jumlah = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdi", $tipe_transaksi, $tanggal, $jumlah, $id);

        if ($stmt->execute()) {
            header("Location: transaksi.php");
            exit();
        } else {
            echo "Terjadi kesalahan saat memperbarui transaksi.";
        }
    } else {
        echo "Semua kolom harus diisi.";
    }
}
?>

<?php include 'header.php'; ?>

<h1>Edit Transaksi</h1>

<form action="edit_transaksi.php?id=<?php echo $transaksi['id']; ?>" method="POST">
    <input type="hidden" name="id" value="<?php echo $transaksi['id']; ?>">
    <div class="mb-3">
        <label for="tipe_transaksi" class="form-label">Jenis Transaksi</label>
        <select class="form-select" id="tipe_transaksi" name="tipe_transaksi">
            <option value="pemasukan" <?php echo ($transaksi['tipe_transaksi'] === 'pemasukan') ? 'selected' : ''; ?>>Pemasukan</option>
            <option value="pengeluaran" <?php echo ($transaksi['tipe_transaksi'] === 'pengeluaran') ? 'selected' : ''; ?>>Pengeluaran</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="tanggal" class="form-label">Tanggal</label>
        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo $transaksi['tanggal']; ?>">
    </div>
    <div class="mb-3">
        <label for="jumlah" class="form-label">Jumlah</label>
        <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?php echo $transaksi['jumlah']; ?>">
    </div>
    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <a href="transaksi.php" class="btn btn-secondary">Batal</a>
</form>


