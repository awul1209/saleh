<?php
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
// Pastikan variabel koneksi $config sudah tersedia.
define('UPLOAD_PATH_BIDANG', 'img/bidang/');

// Fungsi upload tidak perlu diubah, tapi pastikan path-nya benar
function upload() {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) { return null; }
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) { return false; }
    
    $namaFile = $_FILES['file']['name'];
    $ukuranFile = $_FILES['file']['size'];
    $tmpName = $_FILES['file']['tmp_name'];
    $ekstensiFile = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $ekstensiValid = ['jpg', 'jpeg', 'png', 'webp', 'jfif'];

    if (!in_array($ekstensiFile, $ekstensiValid)) { echo "<script>alert('Format gambar tidak valid.');</script>"; return false; }
    if ($ukuranFile > 5000000) { echo "<script>alert('Ukuran gambar terlalu besar!');</script>"; return false; }

    $namaFileBaru = 'bidang_' . uniqid() . '.' . $ekstensiFile;
    if (!is_dir(UPLOAD_PATH_BIDANG)) { mkdir(UPLOAD_PATH_BIDANG, 0777, true); }
    
    if (move_uploaded_file($tmpName, UPLOAD_PATH_BIDANG . $namaFileBaru)) {
        return $namaFileBaru;
    }
    return false;
}

// --- LOGIKA PEMROSESAN FORM (DIPERBARUI) ---

// 1. PROSES TAMBAH DATA
if(isset($_POST['simpanBidang'])){
    $bidang = mysqli_real_escape_string($config, $_POST['bidang']);
    $deskripsi = mysqli_real_escape_string($config, $_POST['deskripsi']);
    $parent_id = (int)$_POST['parent_id']; // Ambil parent_id
    $gambar = upload();

    if ($gambar !== false) {
        $query = "INSERT INTO bidang (nama_bidang, parent_id, gambar, deskripsi) VALUES ('$bidang', '$parent_id', '$gambar', '$deskripsi')";
        $simpan = mysqli_query($config, $query);
        if ($simpan) {
            echo "<script>alert('Tambah Data Berhasil'); document.location.href='?page=bidang';</script>";
        } else {
            echo "<script>alert('Tambah Data Gagal: ".mysqli_error($config)."');</script>";
        }
    }
}

// 2. PROSES UBAH DATA
if(isset($_POST['updateBidang'])){
    $id_bidang = (int)$_POST['id_bidang'];
    $bidang = mysqli_real_escape_string($config, $_POST['bidang']);
    $deskripsi = mysqli_real_escape_string($config, $_POST['deskripsi']);
    $parent_id = (int)$_POST['parent_id']; // Ambil parent_id
    $gambarLama = $_POST['gambarLama'];
    
    $gambar = $gambarLama; // Defaultnya gunakan gambar lama
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $gambarBaru = upload();
        if ($gambarBaru) {
            $gambar = $gambarBaru;
            // Hapus gambar lama jika ada dan berhasil upload yang baru
            if (!empty($gambarLama) && file_exists(UPLOAD_PATH_BIDANG . $gambarLama)) {
                unlink(UPLOAD_PATH_BIDANG . $gambarLama);
            }
        }
    }

    $update = mysqli_query($config, "UPDATE bidang SET nama_bidang='$bidang', parent_id='$parent_id', gambar='$gambar', deskripsi='$deskripsi' WHERE id='$id_bidang'");
    if ($update) {
        echo "<script>alert('Update Data Berhasil'); document.location.href='?page=bidang';</script>";
    } else {
        echo "<script>alert('Update Data Gagal: ".mysqli_error($config)."');</script>";
    }
}

// --- LOGIKA PENCARIAN & QUERY DATA ---
$search_query = isset($_GET['search_bidang']) ? mysqli_real_escape_string($config, $_GET['search_bidang']) : '';
$bidang_data = [];
$query_string = "SELECT * FROM bidang ORDER BY parent_id, nama_bidang ASC";
if (!empty($search_query)) {
    $query_string = "SELECT * FROM bidang WHERE nama_bidang LIKE '%$search_query%' ORDER BY parent_id, nama_bidang ASC";
}
$result = mysqli_query($config, $query_string);
while ($row = mysqli_fetch_assoc($result)) {
    $bidang_data[$row['parent_id']][] = $row;
}
?>

<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Manajemen Data Bidang</h1>
        <p class="mb-4 text-muted">Kelola bidang pekerjaan beserta sub-bidangnya.</p>

        <!-- Form Pencarian dan Tombol Tambah -->
        <div class="row mb-4">
            <div class="col-md-8">
                <form method="GET" class="d-flex">
                    <input type="hidden" name="page" value="bidang">
                    <input type="text" name="search_bidang" class="form-control me-2" placeholder="Cari nama bidang..." value="<?= htmlspecialchars($search_query) ?>">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    <a href="?page=bidang" class="btn btn-outline-secondary ms-2" title="Reset Pencarian"><i class="fas fa-sync-alt"></i></a>
                </form>
            </div>
            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                <button type="button" class="btn btn-primary w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#tambahModal">
                    <i class="fas fa-plus"></i> Tambah Bidang Baru
                </button>
            </div>
        </div>

        <!-- Tampilan Tabel Hierarkis -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nama Bidang</th>
                                <th>Deskripsi Singkat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fungsi rekursif untuk menampilkan data secara hierarkis
                            function display_bidang($parent_id, $level, $bidang_data) {
                                global $config;
                                if (isset($bidang_data[$parent_id])) {
                                    foreach ($bidang_data[$parent_id] as $data) {
                                        $padding = $level * 25; // Jarak indentasi
                                        $icon = ($level > 0) ? '<i class="fas fa-level-up-alt fa-rotate-90 me-2 text-muted"></i>' : '';
                                        $deskripsi_singkat = strlen($data['deskripsi']) > 100 ? substr($data['deskripsi'], 0, 100) . '...' : $data['deskripsi'];
                            ?>
                                        <tr>
                                            <td style="padding-left: <?= $padding ?>px;">
                                                <strong><?= $icon . htmlspecialchars($data['nama_bidang']) ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($deskripsi_singkat) ?></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $data['id'] ?>"><i class="fas fa-edit"></i></button>
                                                <a class="btn btn-danger btn-sm" href="?page=del-bidang&kode=<?= $data['id'] ?>&photo=<?= $data['gambar'] ?>" onclick="return confirm('Yakin hapus data ini?')"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                            <?php
                                        // Panggil fungsi ini lagi untuk anak-anak dari baris saat ini
                                        display_bidang($data['id'], $level + 1, $bidang_data);

                                        // Buat Modal Edit di dalam loop agar datanya sesuai
                                        include 'edit_bidang.php';
                                    }
                                }
                            }

                            // Mulai menampilkan dari level teratas (parent_id = 0)
                            display_bidang(0, 0, $bidang_data);
                            
                            // Cek jika tidak ada hasil sama sekali
                            if (empty($bidang_data)) {
                                echo '<tr><td colspan="3" class="text-center text-muted py-4">Tidak ada data bidang yang ditemukan.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah (DIPERBARUI) -->
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Bidang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Bidang/Sub-Bidang</label>
                        <input type="text" name="bidang" class="form-control" required>
                    </div>
                    <!-- Dropdown untuk memilih Induk -->
                    <div class="mb-3">
                        <label class="form-label">Induk Bidang (Opsional)</label>
                        <select name="parent_id" class="form-select">
                            <option value="0">-- Jadikan sebagai Bidang Utama --</option>
                            <?php
                            $query_parent = mysqli_query($config, "SELECT id, nama_bidang FROM bidang WHERE parent_id = 0 ORDER BY nama_bidang ASC");
                            while ($parent = mysqli_fetch_assoc($query_parent)) {
                                echo "<option value='{$parent['id']}'>" . htmlspecialchars($parent['nama_bidang']) . "</option>";
                            }
                            ?>
                        </select>
                        <small class="text-muted">Pilih ini jika Anda ingin membuat sub-bidang.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="5"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar Browsur</label>
                        <input type="file" name="file" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" name="simpanBidang">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


