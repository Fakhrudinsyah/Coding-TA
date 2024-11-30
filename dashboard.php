<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'koneksi.php';

// Set Page Title
$page_title = "Dashboard";

// Display flash messages
$success = '';
$error = '';

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Query to get all items and their stock with jenis_barang
$sql = "
    SELECT 
        items.id, 
        items.nama_barang, 
        jenis_barang.nama_jenis_barang AS jenis_barang, 
        items.harga, 
        items.stok 
    FROM 
        items 
    JOIN 
        jenis_barang 
    ON 
        items.jenis_barang_id = jenis_barang.id";
$result = $conn->query($sql);
?>

<?php include 'header.php'; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- Stock Quantity Table -->
<h2 class="mb-4">Jumlah Stok Barang</h2>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nama Barang</th>
                <th>Jenis Barang</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                        <td><?php echo htmlspecialchars($row['jenis_barang']); ?></td>
                        <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td>
                            <?php
                                if ($row['stok'] <= 10) {
                                    echo '<span class="badge bg-danger">' . htmlspecialchars($row['stok']) . '</span>';
                                } elseif ($row['stok'] <= 20) {
                                    echo '<span class="badge bg-warning">' . htmlspecialchars($row['stok']) . '</span>';
                                } else {
                                    echo '<span class="badge bg-success">' . htmlspecialchars($row['stok']) . '</span>';
                                }
                            ?>
                        </td>
                        <td>
                            <a href="edit_item.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <form method="POST" action="delete_item.php" class="d-inline" onsubmit="return confirmDelete('<?php echo htmlspecialchars($row['nama_barang'], ENT_QUOTES); ?>');">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Tidak ada barang.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function confirmDelete(namaBarang) {
        return confirm('Apakah Anda yakin ingin menghapus barang "' + namaBarang + '"?');
    }
</script>
