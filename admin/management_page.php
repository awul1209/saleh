<?php
if (isset($_GET['page'])) {
    $hal = $_GET['page'];

    switch ($hal) {
        case 'home':
            include 'home.php';
            break;
        case 'home-admin':
            include 'home_admin.php';
            break;
        case 'admin':
            include 'user.php';
            break;
        case 'logout':
            include 'logout.php';
            break;
        case 'bidang':
            include 'bidang/index.php';
            break;
        case 'detail-bidang':
            include 'bidang/detail-bidang.php'; // Sesuaikan path jika perlu
            break;
        case 'del-bidang':
            include 'bidang/del.php';
            break;
        case 'usaha':
            include 'usaha.php';
            break;
        case 'toko':
            include 'toko.php';
            break;
        case 'perhitungan':
            include 'templates/index.php';
            break;

            // karyawan
        case 'karyawan':
            include 'karyawan/index.php';
            break;
            // calon-karyawan
        case 'calon-karyawan':
            include 'calon_karyawan/index.php';
            break;
            // hasil
        case 'hasil':
            include 'hasil/index.php';
            break;
// VERSI BARU YANG SUDAH DIPERBAIKI
case 'kirim-notifikasi-wa': // 'notifikasi' sudah benar
    include 'hasil/kirim_notifikasi_wa.php'; // Path include sudah benar
    break;
case 'wati': // 'notifikasi' sudah benar
    include 'hasil/wati_pesan.php'; // Path include sudah benar
    break;
// VERSI BARU YANG SUDAH DIPERBAIKI
case 'ubah-status': // 'notifikasi' sudah benar
    include 'hasil/ubah_status.php'; // Path include sudah benar
    break;
            // bobot
 case 'standar-bidang':
        include 'bobot/standar_bidang.php'; // Panggil file baru Anda
        break;

            // profil
        case 'profil-ideal':
            include 'profil/index.php';
            break;
        // Tambah case lain sesuai kebutuhan

        default:
            echo '<center><h1>ERROR! Halaman tidak ditemukan</h1></center>';
            break;
    }
} else {
    if($data_role=='admin'){
        include 'home_admin.php'; // Bisa juga diganti ke index.php jika diinginkan
    }else{
        include 'home.php'; // Bisa juga diganti ke index.php jika diinginkan
    }
}
?>
