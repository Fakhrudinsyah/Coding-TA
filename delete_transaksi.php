<?php
// delete_transaksi.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'koneksi.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi ID yang diterima
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $id = intval($_POST['id']);

        // Query untuk menghapus transaksi berdasarkan ID
        $sql = "DELETE FROM transactions WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Redirect kembali ke halaman transaksi dengan pesan sukses
            $_SESSION['success_message'] = "Transaksi berhasil dihapus.";
            header("Location: transaksi.php");
            exit();
        } else {
            // Redirect kembali dengan pesan error jika gagal
            $_SESSION['error_message'] = "Gagal menghapus transaksi. Silakan coba lagi.";
            header("Location: transaksi.php");
            exit();
        }
    } else {
        // Jika ID tidak valid, redirect kembali dengan pesan error
        $_SESSION['error_message'] = "ID transaksi tidak valid.";
        header("Location: transaksi.php");
        exit();
    }
} else {
    // Jika akses langsung tanpa metode POST, redirect kembali
    header("Location: transaksi.php");
    exit();
}
?>
