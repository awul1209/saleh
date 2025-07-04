<?php
// --- LOGIKA AKSI (Ubah Status & Jadikan Karyawan) ---
if (isset($_GET['action'])) {
    // Penanganan Aksi 'ubah_status'
    if ($_GET['action'] == 'ubah_status' && isset($_GET['id_calon'], $_GET['status'])) {
        $id_calon = (int)$_GET['id_calon'];
        $status_baru = mysqli_real_escape_string($config, $_GET['status']);
        $allowed_statuses = ['Lolos', 'Tidak Lolos', 'Diproses'];

        if ($id_calon > 0 && in_array($status_baru, $allowed_statuses)) {
            $update_status = mysqli_query($config, "UPDATE calon_karyawan SET status = '$status_baru' WHERE id = '$id_calon'");
            $pesan = $update_status ? "Status kandidat berhasil diubah menjadi: $status_baru" : "Gagal mengubah status di database!";
        } else {
            $pesan = "Aksi tidak valid!";
        }
        echo "<script>alert('$pesan'); window.location.href='?page=hasil';</script>";
        exit;
    }

    // Penanganan Aksi 'jadikan_karyawan'
    if ($_GET['action'] == 'jadikan_karyawan' && isset($_GET['id_calon'])) {
        $id_calon = (int)$_GET['id_calon'];
        if ($id_calon > 0) {
            $q_get_calon = mysqli_query($config, "SELECT ck.nama, ck.bidang_id, b.nama_bidang AS jabatan, b.parent_id FROM calon_karyawan ck JOIN bidang b ON ck.bidang_id = b.id WHERE ck.id = $id_calon AND ck.status = 'Lolos'");
            if (mysqli_num_rows($q_get_calon) > 0) {
                $data_calon = mysqli_fetch_assoc($q_get_calon);
                $nik = 'NIK-' . time();
                $nama_karyawan = mysqli_real_escape_string($config, $data_calon['nama']);
                $jabatan = mysqli_real_escape_string($config, $data_calon['jabatan']);
                
                mysqli_begin_transaction($config);
                try {
                    $q_insert = "INSERT INTO karyawan (nik, nama_karyawan, bagian, tanggal_masuk, bidang_id) VALUES ('$nik', '$nama_karyawan', '$jabatan', CURDATE(), '{$data_calon['parent_id']}')";
                    if (!mysqli_query($config, $q_insert)) throw new Exception("Gagal memasukkan data ke tabel karyawan.");
                    if (!mysqli_query($config, "DELETE FROM calon_karyawan WHERE id = $id_calon")) throw new Exception("Gagal menghapus data dari calon karyawan.");
                    mysqli_commit($config);
                    echo "<script>alert('Berhasil! " . addslashes($nama_karyawan) . " telah menjadi karyawan.'); window.location.href='?page=karyawan';</script>";
                } catch (Exception $e) {
                    mysqli_rollback($config);
                    echo "<script>alert('Terjadi kesalahan: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
                }
            } else {
                echo "<script>alert('Aksi tidak valid! Kandidat tidak ditemukan atau belum lolos.'); window.history.back();</script>";
            }
        }
        exit;
    }
}

// --- LOGIKA PENCARIAN & QUERY DATA UTAMA ---
$search_parent_bidang = isset($_GET['search_parent_bidang']) ? (int)$_GET['search_parent_bidang'] : 0;
$search_sub_bidang = isset($_GET['search_sub_bidang']) ? (int)$_GET['search_sub_bidang'] : 0;

$base_query = "
    SELECT 
        hp.id, hp.calon_karyawan_id, hp.skor_kecocokan,
        ck.nama AS nama_calon, ck.status AS status_kandidat,
        b.nama_bidang AS nama_sub_bidang
    FROM hasil_pencocokan AS hp
    JOIN calon_karyawan AS ck ON hp.calon_karyawan_id = ck.id
    JOIN bidang AS b ON hp.bidang_id = b.id
";
$where_clauses = [];
if (!empty($search_sub_bidang)) {
    $where_clauses[] = "hp.bidang_id = $search_sub_bidang";
} elseif (!empty($search_parent_bidang)) {
    $where_clauses[] = "b.parent_id = $search_parent_bidang";
}

if (!empty($where_clauses)) {
    $base_query .= " WHERE " . implode(' AND ', $where_clauses);
}

$base_query .= " ORDER BY hp.skor_kecocokan DESC";
$query_hasil = mysqli_query($config, $base_query);
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Hasil Perhitungan Kecocokan</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter"></i> Filter Hasil</h6>
        </div>
        <div class="card-body">
            <form method="GET">
                <input type="hidden" name="page" value="hasil">
                <div class="row align-items-end">
                    <div class="col-md-4 form-group">
                        <label for="parentBidangFilter">Kategori Bidang</label>
                        <select name="search_parent_bidang" id="parentBidangFilter" class="form-control">
                            <option value="">Semua Kategori</option>
                            <?php
                            $query_parent = mysqli_query($config, "SELECT id, nama_bidang FROM bidang WHERE parent_id = 0 ORDER BY nama_bidang ASC");
                            while ($parent = mysqli_fetch_assoc($query_parent)) {
                                $selected = ($parent['id'] == $search_parent_bidang) ? 'selected' : '';
                                echo "<option value='{$parent['id']}' $selected>" . htmlspecialchars($parent['nama_bidang']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="subBidangFilter">Sub-Bidang (Posisi)</label>
                        <select name="search_sub_bidang" id="subBidangFilter" class="form-control" <?= !$search_parent_bidang ? 'disabled' : '' ?>>
                            <option value="">Pilih Kategori Dahulu</option>
                            </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success w-100"><i class="fas fa-search"></i> Cari</button>
                            <a href="?page=hasil" class="btn btn-outline-secondary w-100"><i class="fas fa-sync-alt"></i> Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Peringkat Calon Karyawan</h6>
            <!-- <a href="#" class="btn btn-sm btn-info"><i class="fas fa-print"></i> Cetak Laporan</a> -->
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Peringkat</th>
                            <th>Nama Calon</th>
                            <th>Posisi Dilamar</th>
                            <th>Skor Akhir</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($query_hasil && mysqli_num_rows($query_hasil) > 0): ?>
                            <?php $peringkat = 1; while ($data = mysqli_fetch_assoc($query_hasil)): ?>
                                <?php
                                $status = $data['status_kandidat'];
                                $badge_color = 'secondary';
                                if ($status == 'Lolos') { $badge_color = 'success'; } 
                                elseif ($status == 'Tidak Lolos') { $badge_color = 'danger'; }
                                ?>
                                <tr>
                                    <td class="text-center align-middle"><span class="badge bg-primary p-2 fs-6"><?= $peringkat++ ?></span></td>
                                    <td class="align-middle"><?= htmlspecialchars($data['nama_calon']) ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($data['nama_sub_bidang']) ?></td>
                                    <td class="align-middle font-weight-bold"><?= number_format($data['skor_kecocokan'], 2) ?></td>
                                    <td class="align-middle"><span class="badge bg-<?= $badge_color; ?>"><?= htmlspecialchars($status); ?></span></td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                Ubah Status
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="?page=hasil&action=ubah_status&status=Lolos&id_calon=<?= $data['calon_karyawan_id']; ?>">Lolos</a></li>
                                                <li><a class="dropdown-item" href="?page=hasil&action=ubah_status&status=Tidak Lolos&id_calon=<?= $data['calon_karyawan_id']; ?>">Tidak Lolos</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="?page=hasil&action=ubah_status&status=Diproses&id_calon=<?= $data['calon_karyawan_id']; ?>">Reset ke Diproses</a></li>
                                            </ul>
                                        </div>

                                        <?php if ($status == 'Lolos' || $status == 'Tidak Lolos'): ?>
    
    <?php if ($status == 'Lolos'): ?>
        <a href="?page=hasil&action=jadikan_karyawan&id_calon=<?= $data['calon_karyawan_id']; ?>" class="btn btn-primary btn-sm ms-2" onclick="return confirm('Anda yakin ingin MEMINDAHKAN kandidat ini ke data karyawan? Aksi ini akan menghapus data pelamar.')">
            <i class="fas fa-user-plus"></i> Jadikan Karyawan
        </a>
    <?php endif; ?>

    <a href="?page=kirim-notifikasi-wa&id_calon=<?= $data['calon_karyawan_id']; ?>" class="btn btn-info btn-sm ms-1" onclick="return confirm('Kirim notifikasi WhatsApp ke kandidat ini?')">
        <i class="fab fa-whatsapp"></i> Kirim WA
    </a>
    
<?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada hasil perhitungan yang ditemukan. Silakan sesuaikan filter.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>