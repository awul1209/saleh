<?php
// Pastikan variabel koneksi $config sudah tersedia

if (isset($_GET['id_calon']) && isset($_GET['status'])) {
    $id_calon = mysqli_real_escape_string($config, $_GET['id_calon']);
    $status_baru = mysqli_real_escape_string($config, $_GET['status']);

    // Validasi input untuk keamanan
    $allowed_statuses = ['Lolos', 'Tidak Lolos', 'Diproses'];
    if (in_array($status_baru, $allowed_statuses)) {
        
        $update_status = mysqli_query($config, "UPDATE calon_karyawan SET status = '$status_baru' WHERE id = '$id_calon'");

        if ($update_status) {
            echo "<script>alert('Status kandidat berhasil diubah menjadi: $status_baru'); window.location.href='?page=hasil';</script>";
        } else {
            echo "<script>alert('Gagal mengubah status di database!'); window.location.href='?page=hasil';</script>";
        }
    } else {
        echo "<script>alert('Status tidak valid!'); window.location.href='?page=hasil';</script>";
    }
} else {
    // Redirect jika parameter tidak lengkap
    header('Location: ?page=hasil');
    exit;
}
?>
