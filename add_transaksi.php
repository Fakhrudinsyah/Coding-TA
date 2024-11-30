<?php
// add_transaksi.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}   

require 'koneksi.php';




// Set Page Title
$page_title = "Tambah Transaksi";

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = intval($_POST['item_id']);
    $jumlah = intval($_POST['jumlah']);
    $tipe_transaksi = $_POST['tipe_transaksi'];
    $tanggal = $_POST['tanggal'];

    // Validasi input
    if ($item_id <= 0 || $jumlah <= 0 || empty($tipe_transaksi) || empty($tanggal)) {
        $error = "Semua field wajib diisi dengan benar.";
    } else {
        // Mulai transaksi database
        $conn->begin_transaction();

        try {
            // Cek apakah barang ada dan dapatkan stok saat ini
            $stmt_check = $conn->prepare("SELECT stok FROM items WHERE id = ?");
            $stmt_check->bind_param("i", $item_id);
            $stmt_check->execute();
            $stmt_check->bind_result($current_stok);
            if (!$stmt_check->fetch()) {
                throw new Exception("Barang tidak ditemukan.");
            }
            $stmt_check->close();

            // Validasi stok untuk pengeluaran
            if ($tipe_transaksi == 'pengeluaran' && $current_stok < $jumlah) {
                throw new Exception("Stok tidak mencukupi untuk pengeluaran.");
            }

            // Update stok barang
            if ($tipe_transaksi == 'pemasukan') {
                $stmt_update = $conn->prepare("UPDATE items SET stok = stok + ? WHERE id = ?");
                $stmt_update->bind_param("ii", $jumlah, $item_id);
            } else { // pengeluaran
                $stmt_update = $conn->prepare("UPDATE items SET stok = stok - ? WHERE id = ?");
                $stmt_update->bind_param("ii", $jumlah, $item_id);
            }
            $stmt_update->execute();
            $stmt_update->close();

            // Insert transaksi
            $stmt_insert = $conn->prepare("INSERT INTO transactions (user_id, item_id, jumlah, tipe_transaksi, tanggal) VALUES (?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("iiiss", $_SESSION['user_id'], $item_id, $jumlah, $tipe_transaksi, $tanggal);
            if (!$stmt_insert->execute()) {
                throw new Exception("Gagal menambahkan transaksi.");
            }
            $stmt_insert->close();

            // Catat perubahan stok ke stock_history
            $new_stok = ($tipe_transaksi == 'pemasukan') ? ($current_stok + $jumlah) : ($current_stok - $jumlah);

            /*
            $stmt_history = $conn->prepare("INSERT INTO stock_history (item_id, previous_stok, new_stok, changed_by) VALUES (?, ?, ?, ?)");
            $stmt_history->bind_param("iiii", $item_id, $current_stok, $new_stok, $_SESSION['user_id']);
            if (!$stmt_history->execute()) {
                throw new Exception("Gagal mencatat history stok.");
            }
            $stmt_history->close();
*/
            // Commit transaksi
            $conn->commit();

            // Set pesan sukses dan redirect ke halaman transaksi
            $_SESSION['success'] = "Transaksi berhasil ditambahkan.";
            header("Location: transaksi.php");
            exit();
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $conn->rollback();
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<?php include 'header.php'; ?>

    <h2 class="mb-4">Tambah Transaksi</h2>
    
    <?php if(!empty($error)): ?>
        <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="mb-3">
            <label for="item_id" class="form-label">Nama Barang</label>
            <select class="form-select" name="item_id" required>
                <option value="">Pilih Barang</option>
                <?php
                    $items = $conn->query("SELECT id, nama_barang, stok FROM items");
                    while($item = $items->fetch_assoc()){
                        echo '<option value="'.$item['id'].'">'.htmlspecialchars($item['nama_barang']).' (Stok: '.$item['stok'].')</option>';
                    }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="number" class="form-control" name="jumlah" required min="1" placeholder="Masukkan jumlah">
        </div>
        <div class="mb-3">
            <label for="tipe_transaksi" class="form-label">Tipe Transaksi</label>
            <select class="form-select" name="tipe_transaksi" required>
                <option value="">Pilih Tipe</option>
                <option value="pemasukan">Pemasukan</option>
                <option value="pengeluaran">Pengeluaran</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" name="tanggal" required value="<?php echo date('Y-m-d'); ?>">
        </div>
        <button type="submit" class="btn btn-warning w-100">Tambah Transaksi</button>
    </form>
