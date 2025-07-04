<?php
// Set header sebagai JSON di paling atas
header('Content-Type: application/json');

// Panggil file koneksi database Anda
// Pastikan path ini sudah benar sesuai struktur folder Anda
require_once '../config.php'; 

// Cek apakah variabel koneksi $config ada dan tidak error menggunakan gaya prosedural
if (!isset($config) || mysqli_connect_error()) {
    // Kirim pesan error dalam format JSON jika koneksi gagal
    echo json_encode(['error' => 'Koneksi database gagal: ' . mysqli_connect_error()]);
    exit;
}

// Pastikan ada parameter 'action'
if (!isset($_GET['action'])) {
    echo json_encode(['error' => 'Aksi tidak ditentukan']);
    exit;
}

$action = $_GET['action'];

// Gunakan switch untuk menangani setiap aksi
switch ($action) {
    
    case 'get_kriteria':
        if (!isset($_GET['bidang_id'])) {
            echo json_encode(['error' => 'ID Bidang tidak ditemukan']);
            exit;
        }
        $bidang_id = (int)$_GET['bidang_id'];
        $kriteria = [];
        $query_kriteria = mysqli_query($config, "SELECT kriteria FROM profil_ideal WHERE bidang_id = $bidang_id ORDER BY kriteria ASC");
        if ($query_kriteria) {
            while ($row = mysqli_fetch_assoc($query_kriteria)) {
                $kriteria[] = $row['kriteria'];
            }
        }
        echo json_encode($kriteria);
        break;

    case 'get_nilai':
        if (!isset($_GET['calon_id'])) {
            echo json_encode(['error' => 'ID Calon tidak ditemukan']);
            exit;
        }
        $calon_id = (int)$_GET['calon_id'];
        $nilai = [];
        $query_nilai = mysqli_query($config, "SELECT kriteria, nilai FROM nilai_kandidat WHERE calon_karyawan_id = $calon_id");
        if ($query_nilai) {
            while ($row = mysqli_fetch_assoc($query_nilai)) {
                $nilai[$row['kriteria']] = $row['nilai'];
            }
        }
        echo json_encode($nilai);
        break;

    case 'get_sub_bidang':
        if (!isset($_GET['parent_id'])) {
            echo json_encode(['error' => 'ID Induk tidak ditemukan']);
            exit;
        }
        $parent_id = (int)$_GET['parent_id'];
        $sub_bidang = [];
        if ($parent_id > 0) {
            $query_sub = mysqli_query($config, "SELECT id, nama_bidang FROM bidang WHERE parent_id = $parent_id ORDER BY nama_bidang ASC");
            if ($query_sub) {
                while ($row = mysqli_fetch_assoc($query_sub)) {
                    $sub_bidang[] = $row;
                }
            }
        }
        echo json_encode($sub_bidang);
        break;

    default:
        echo json_encode(['error' => 'Aksi tidak valid']);
        break;
}

// Hentikan skrip setelah selesai
exit;
?>