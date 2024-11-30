<?php
// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'koneksi.php';

$success = '';
$error = '';

// Tambah Jenis Barang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $jenis_barang = trim($_POST['jenis_barang']);

    if (empty($jenis_barang)) {
        $error = "Jenis barang tidak boleh kosong.";
    } else {
        // Masukkan data jenis barang ke tabel jenis_barang, bukan items
        $stmt = $conn->prepare("INSERT INTO jenis_barang (nama_jenis_barang) VALUES (?)");
        $stmt->bind_param("s", $jenis_barang);

        if ($stmt->execute()) {
            $success = "Jenis barang berhasil ditambahkan.";
        } else {
            $error = "Terjadi kesalahan saat menambahkan jenis barang.";
        }

        $stmt->close();
    }
}

// Hapus Jenis Barang
if (isset($_GET['delete'])) {
    $jenis_id = intval($_GET['delete']);

    // Hapus data jenis barang dari tabel jenis_barang
    $stmt = $conn->prepare("DELETE FROM jenis_barang WHERE id = ?");
    $stmt->bind_param("i", $jenis_id);

    if ($stmt->execute()) {
        $success = "Jenis barang berhasil dihapus.";
    } else {
        $error = "Gagal menghapus jenis barang.";
    }

    $stmt->close();
}

// Edit Jenis Barang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $jenis_id = intval($_POST['id']);
    $jenis_barang = trim($_POST['jenis_barang']);

    if (empty($jenis_barang)) {
        $error = "Jenis barang tidak boleh kosong.";
    } else {
        // Update jenis barang di tabel jenis_barang, bukan di tabel items
        $stmt = $conn->prepare("UPDATE jenis_barang SET nama_jenis_barang = ? WHERE id = ?");
        $stmt->bind_param("si", $jenis_barang, $jenis_id);

        if ($stmt->execute()) {
            $success = "Jenis barang berhasil diperbarui.";
        } else {
            $error = "Gagal memperbarui jenis barang.";
        }

        $stmt->close();
    }
}

// Ambil Semua Data Jenis Barang dari tabel jenis_barang
$result = $conn->query("SELECT id, nama_jenis_barang FROM jenis_barang");
$jenis_barang_list = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'header.php'; ?>

<div class="container mt-5">
    <h2>Jenis Barang</h2>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Form Tambah Jenis Barang -->
    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="jenis_barang" class="form-label">Jenis Barang</label>
            <input type="text" class="form-control" name="jenis_barang" id="jenis_barang" required>
        </div>
        <button type="submit" name="add" class="btn btn-success">Tambah Jenis Barang</button>
    </form>

    <!-- Tabel Jenis Barang -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Barang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($jenis_barang_list)): ?>
                <?php foreach ($jenis_barang_list as $index => $jenis): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($jenis['nama_jenis_barang']); ?></td>
                        <td>
                            <!-- Tombol Edit -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $jenis['id']; ?>">Edit</button>
                            <!-- Tombol Hapus -->
                            <a href="jenisbarang.php?delete=<?php echo $jenis['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus jenis barang ini?')">Hapus</a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editModal<?php echo $jenis['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Edit Jenis Barang</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?php echo $jenis['id']; ?>">
                                        <div class="mb-3">
                                            <label for="jenis_barang" class="form-label">Jenis Barang</label>
                                            <input type="text" class="form-control" name="jenis_barang" value="<?php echo htmlspecialchars($jenis['nama_jenis_barang']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">Tidak ada jenis barang.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
