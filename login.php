<?php
session_start();
require 'koneksi.php';

// Jika pengguna sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

// Menangani pesan logout sukses dan timeout
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $logout_message = "Anda telah berhasil logout.";
}

if (isset($_GET['timeout']) && $_GET['timeout'] == '1') {
    $timeout_message = "Sesi Anda telah berakhir karena inaktivitas.";
}

// Menangani login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validasi input
    if (empty($username) || empty($password)) {
        $error = "Semua field wajib diisi.";
    } else {
        // Persiapkan query untuk cek username dan password
        $query = "SELECT id, username, password FROM users WHERE username = ?";
        if ($stmt = $conn->prepare($query)) {
            // Bind parameter dan eksekusi query
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            // Jika username ditemukan
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($user_id, $username_db, $hashed_password);
                $stmt->fetch();

                // Verifikasi password
                if (password_verify($password, $hashed_password)) {
                    // Regenerate session ID untuk keamanan
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username_db;

                    // Redirect ke dashboard
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Password salah.";
                }
            } else {
                $error = "Username tidak ditemukan.";
            }

            $stmt->close();  // Tutup statement
        } else {
            $error = "Terjadi kesalahan saat mempersiapkan query.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Toko Komputer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="assets/css/styles.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            background-image: url('Background.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
            padding: 30px;
            max-width: 400px;
            width: 100%;
        }

        .login-card .form-control {
            border-radius: 10px;
        }

        .login-card .btn-primary {
            border-radius: 10px;
        }

        .login-card .social-icons a {
            color: #fff;
            margin: 0 10px;
            font-size: 1.5rem;
            transition: color 0.3s;
        }

        .login-card .social-icons a:hover {
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="text-center mb-4">
                <h3 class="mt-2">Toko Komputer</h3>
            </div>
            <?php if(isset($logout_message)): ?>
                <div class="alert alert-success text-center"><?php echo htmlspecialchars($logout_message); ?></div>
            <?php endif; ?>
            <?php if(isset($timeout_message)): ?>
                <div class="alert alert-warning text-center"><?php echo htmlspecialchars($timeout_message); ?></div>
            <?php endif; ?>
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" class="mt-4">
                <div class="mb-3">
                    <label for="username" class="form-label"><i class="bi bi-person-fill"></i> Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label"><i class="bi bi-lock-fill"></i> Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordField = document.getElementById('password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="bi bi-eye-fill"></i>' : '<i class="bi bi-eye-slash-fill"></i>';
        });
    </script>
</body>
</html>
