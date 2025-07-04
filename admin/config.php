<?php

	$config = new mysqli('localhost', 'root', '', 'lowong');

// Proses tambah admin
if (isset($_POST['tambahuser'])) {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = $_POST['nama'];
    $role = $_POST['role'];

    $query = "INSERT INTO user (email, password, nama, role) VALUES ('$email', '$password', '$nama', '$role')";
    mysqli_query($config, $query);
    header("Location: user.php");
    exit();
}

// Proses edit admin
if (isset($_POST['updateuser'])) {
    $id = $_POST['id'];
    $email = $_POST['email'];
    $nama = $_POST['nama'];
    $role = $_POST['role'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = "UPDATE user SET email='$email', password='$password', nama='$nama', role='$role, WHERE id='$id'";
    } else {
        $query = "UPDATE user SET email='$email', nama='$nama' WHERE id='$id'";
    }
    mysqli_query($config, $query);
    header("Location: user.php");
    exit();
}

// Proses hapus admin
if (isset($_POST['hapususer'])) {
    $id = $_POST['id'];
    $query = "DELETE FROM user WHERE id='$id'";
    mysqli_query($config, $query);
    header("Location: user.php");
    exit();
}

// Tambah foto
if(isset($_POST['fotobaru'])){
    $gambar = $_FILES['file']['name'];
    $tmp_name = $_FILES['file']['tmp_name'];
    $path = "imgs/".$gambar;

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($_FILES['file']['type'], $allowedTypes)) {
        if (move_uploaded_file($tmp_name, $path)) {
            $query = "INSERT INTO perusahaan (gambar) VALUES ('$gambar')";
            mysqli_execute_query($config, $query);
        }
    }
}

//update galeri foto
if(isset($_POST['updatefoto'])){
    $id_foto = mysqli_real_escape_string($config, $_POST['id_foto']);
    $gambar = $_FILES['file']['name'];
    $tmp_name = $_FILES['file']['tmp_name'];
    $path = "imgs/".$gambar;

    if ($gambar != "") {
        if (move_uploaded_file($tmp_name, $path)) {
            $query = "UPDATE perusahaan SET gambar='$gambar' WHERE id_foto='$id_foto'";
        }
    } else {
        $query = "UPDATE perusahaan WHERE id_foto='$id_foto'";
    }
    
    mysqli_execute_query($config, $query);
}

// Hapus foto
if(isset($_POST['hapusfoto'])){
    $id_foto = mysqli_real_escape_string($config, $_POST['id_foto']);
    $query = "SELECT gambar FROM perusahaan WHERE id_foto='$id_foto'";
    $result = mysqli_execute_query($config, $query);
    $data = mysqli_fetch_array($result);
    $path = "imgs/".$data['gambar'];

    if (file_exists($path)) {
        unlink($path);
    }

    $query = "DELETE FROM perusahaan WHERE id_foto='$id_foto'";
    mysqli_execute_query($config, $query);
}