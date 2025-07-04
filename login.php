<?php
session_start();
include 'config.php'; // Panggil file koneksi

// Jika user sudah login, arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error_message = '';
if (isset($_POST['submit_login'])) {
    $username = mysqli_real_escape_string($config, $_POST['username']);
    $password = $_POST['password']; // Ambil password mentah

    // Cari user berdasarkan username
    $query = mysqli_query($config, "SELECT * FROM calon_karyawan WHERE username = '$username'");
    
    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);
        
        // Verifikasi password yang diinput dengan hash di database
        if ($password == $user['password']) {
            // Jika berhasil, simpan data ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            
            // Arahkan ke dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            $error_message = "Password yang Anda masukkan salah.";
        }
    } else {
        $error_message = "Username tidak ditemukan.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .login-card { max-width: 450px; margin: 100px auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card login-card shadow-lg">
            <div class="card-body p-5">
                <h3 class="card-title text-center mb-4">Login Calon Karyawan</h3>
                <?php if($error_message): ?>
                    <div class="alert alert-danger"><?= $error_message; ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" name="submit_login" class="btn btn-primary">Login</button>
                    </div>
                    <div class="text-center mt-3">
                        <a href="index.php">Kembali ke Halaman Utama</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>