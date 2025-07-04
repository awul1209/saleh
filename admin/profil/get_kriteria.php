<?php
// Sertakan file koneksi database Anda
include '../../config.php'; // Ganti 'koneksi.php' dengan nama file koneksi Anda

// Set header sebagai JSON
header('Content-Type: application/json');

$kriteria_list = [];

if (isset($_GET['bidang'])) {
    $nama_bidang_formatted = $_GET['bidang'];

    // 1. Kembalikan format nama bidang ke bentuk semula (dengan spasi)
    $nama_bidang_asli = str_replace('_', ' ', $nama_bidang_formatted);

    // 2. Cari ID dari tabel 'bidang' berdasarkan nama yang sudah dikembalikan
    $stmt_bidang = $config->prepare("SELECT id FROM bidang WHERE LOWER(nama_bidang) = ?");
    $stmt_bidang->bind_param("s", $nama_bidang_asli);
    $stmt_bidang->execute();
    $result_bidang = $stmt_bidang->get_result();

    if ($row_bidang = $result_bidang->fetch_assoc()) {
        $bidang_id = $row_bidang['id'];

        // 3. Cari semua kriteria di 'bobot_kriteria' berdasarkan bidang_id yang ditemukan
        $stmt_kriteria = $config->prepare("SELECT kriteria FROM bobot_kriteria WHERE bidang_id = ?");
        $stmt_kriteria->bind_param("i", $bidang_id);
        $stmt_kriteria->execute();
        $result_kriteria = $stmt_kriteria->get_result();
        
        while ($row_kriteria = $result_kriteria->fetch_assoc()) {
            // Masukkan setiap kriteria ke dalam array
            $kriteria_list[] = $row_kriteria['kriteria'];
        }
    }
}

// 4. Kembalikan array kriteria sebagai JSON
echo json_encode($kriteria_list);
?>