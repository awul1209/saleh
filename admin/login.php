<?php
session_start();
include 'config.php'; // Pastikan file ini mendefinisikan $conn

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Mengambil data pengguna dari database
    $cekdatabase = mysqli_query($config, "SELECT * FROM user WHERE email='$email' and role='$role'");
    $user = mysqli_fetch_assoc($cekdatabase);  
    if ($user) {
        // Memverifikasi password
        if (password_verify($password,$user['password'])) {
            // Jika password benar, simpan ID pengguna dalam sesi
            session_start();
            $_SESSION['admin'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login'] = true;
            $_SESSION['success_message'] = "Login berhasil. Selamat datang, " . $user['nama'] . "!";
            header('Location: index.php');
            exit(); // Pastikan untuk menghentikan script setelah pengalihan
        } else {
            echo '<script>alert("Email atau Password Anda Salah");</script>';
        }
    } else {
        echo '<script>alert("Email Anda Salah");</script>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Lowowngan</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-info">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">

    <div class="col-lg-6">
        <div class="card o-hidden border-0 shadow-lg">
            <div class="card-body p-5">
                <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Selamat Datang!</h1>
                    <p class="small text-muted">Silakan login untuk melanjutkan</p>
                </div>
                <form class="user" method="POST" action="">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            <input type="email" name="email" class="form-control form-control-user"
                                placeholder="Masukkan Email Anda" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                            <input type="password" name="password" class="form-control form-control-user"
                                placeholder="Masukkan Password Anda" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <select name="role" class="form-control custom-select" required>
                            <option value="" disabled selected>-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="perusahaan">Perusahaan</option>
                        </select>
                    </div>
                    <button type="submit" name="login" class="btn btn-info btn-user btn-block">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </button>
                </form>
                <hr>
            </div>
        </div>
    </div>

</div>



    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>