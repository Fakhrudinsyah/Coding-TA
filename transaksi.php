<?php
// transaksi.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'koneksi.php';

// Query untuk mendapatkan semua transaksi
$sql = "SELECT id, tipe_transaksi, tanggal, jumlah FROM transactions";
$result = $conn->query($sql);
?>

<?php include 'header.php'; ?>

<h1 class="mb-4">Riwayat Transaksi</h1>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Jenis Transaksi</th>
                <th>Tanggal</th>
                <th>Jumlah</th>
                <th>Aksi</th> <!-- Kolom Aksi -->
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['tipe_transaksi']); ?></td>
                        <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                        <td><?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                        <td>
                            <a href="edit_transaksi.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <!-- Tombol konfirmasi hapus -->
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $row['id']; ?>">
                                Hapus
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Konfirmasi Delete untuk Transaksi -->
                    <div class="modal fade" id="deleteModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $row['id']; ?>">Konfirmasi Hapus Transaksi</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus transaksi <strong><?php echo htmlspecialchars($row['tipe_transaksi']); ?></strong>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <form method="POST" action="delete_transaksi.php" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Tidak ada transaksi.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
