<?php
session_start();
require 'koneksi.php';



// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if 'id' is set in POST data
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        // Validate that 'id' is a number to prevent SQL injection
        if (!is_numeric($id)) {
            $_SESSION['error'] = "ID barang tidak valid.";
            header("Location: dashboard.php");
            exit();
        }

        // Check if the item is referenced in the 'transactions' table
        $checkSql = "SELECT COUNT(*) AS count FROM transactions WHERE item_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        if ($checkStmt === false) {
            $_SESSION['error'] = "Gagal menyiapkan query: " . htmlspecialchars($conn->error);
            header("Location: dashboard.php");
            exit();
        }

        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $row = $checkResult->fetch_assoc();
        $checkStmt->close();

        if ($row['count'] > 0) {
            $_SESSION['error'] = "Tidak dapat menghapus barang karena masih ada transaksi yang terkait.";
            header("Location: dashboard.php");
            exit();
        }

        // Prepare the DELETE statement
        $sql = "DELETE FROM items WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $_SESSION['error'] = "Gagal menyiapkan query: " . htmlspecialchars($conn->error);
            header("Location: dashboard.php");
            exit();
        }

        $stmt->bind_param("i", $id);

        // Execute the DELETE statement
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['success'] = "Barang berhasil dihapus.";
            } else {
                $_SESSION['error'] = "Barang tidak ditemukan atau sudah dihapus.";
            }
        } else {
            // Handle foreign key constraint failure
            if ($conn->errno === 1451) { // MySQL error code for foreign key constraint failure
                $_SESSION['error'] = "Tidak dapat menghapus barang karena masih ada transaksi yang terkait.";
            } else {
                $_SESSION['error'] = "Gagal menghapus barang: " . htmlspecialchars($stmt->error);
            }
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "ID barang tidak ditemukan.";
    }
} else {
    $_SESSION['error'] = "Permintaan tidak valid.";
}

// Close the database connection
$conn->close();

// Redirect back to the dashboard after deletion
header("Location: dashboard.php");
exit();
?>
