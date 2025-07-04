    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<?php
if (isset($_POST['tambahKaryawan'])) {

$nik = mysqli_real_escape_string($config, $_POST['nik']);

$nama_karyawan = mysqli_real_escape_string($config, $_POST['nama_karyawan']);

$jabatan = mysqli_real_escape_string($config, $_POST['jabatan']);

$tanggal_masuk = mysqli_real_escape_string($config, $_POST['tanggal_masuk']);

$bidang_id = mysqli_real_escape_string($config, $_POST['bidang_id']);



$insert = mysqli_query($config, "INSERT INTO karyawan (nik, nama_karyawan, jabatan, tanggal_masuk, bidang_id) VALUES ('$nik', '$nama_karyawan', '$jabatan', '$tanggal_masuk', '$bidang_id')");



if ($insert) {

echo "<script>alert('Data karyawan berhasil ditambahkan!');window.location.href='?page=karyawan';</script>";

} else {

echo "<script>alert('Gagal menambahkan data: " . mysqli_error($config) . "');</script>";

}

}



// 2. PROSES UBAH DATA KARYAWAN

if (isset($_POST['updateKaryawan'])) {

$id = mysqli_real_escape_string($config, $_POST['id']);

$nik = mysqli_real_escape_string($config, $_POST['nik']);

$nama_karyawan = mysqli_real_escape_string($config, $_POST['nama_karyawan']);

$jabatan = mysqli_real_escape_string($config, $_POST['jabatan']);

$tanggal_masuk = mysqli_real_escape_string($config, $_POST['tanggal_masuk']);

$bidang_id = mysqli_real_escape_string($config, $_POST['bidang_id']);



$update = mysqli_query($config, "UPDATE karyawan SET nik='$nik', nama_karyawan='$nama_karyawan', jabatan='$jabatan', tanggal_masuk='$tanggal_masuk', bidang_id='$bidang_id' WHERE id='$id'");



if ($update) {

echo "<script>alert('Data karyawan berhasil diubah!');window.location.href='?page=karyawan';</script>";

} else {

echo "<script>alert('Gagal mengubah data: " . mysqli_error($config) . "');</script>";

}

}



// 3. PROSES HAPUS DATA KARYAWAN

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus_karyawan') {

$id = mysqli_real_escape_string($config, $_GET['id']);

$delete = mysqli_query($config, "DELETE FROM karyawan WHERE id='$id'");



if ($delete) {

echo "<script>alert('Data karyawan berhasil dihapus!');window.location.href='?page=karyawan';</script>";

} else {

echo "<script>alert('Gagal menghapus data: " . mysqli_error($config) . "');</script>";

}

}



// --- BARU: LOGIKA PENCARIAN & FILTER ---
$search_parent_bidang = isset($_GET['search_parent_bidang']) ? (int)$_GET['search_parent_bidang'] : 0;
$search_sub_bidang_id = isset($_GET['search_sub_bidang']) ? (int)$_GET['search_sub_bidang'] : 0;
$search_keyword = isset($_GET['search_keyword']) ? mysqli_real_escape_string($config, $_GET['search_keyword']) : '';

// --- Query Utama dengan Filter Dinamis ---
$query = "
    SELECT 
        karyawan.id, karyawan.nik, karyawan.nama_karyawan, 
        karyawan.jabatan, karyawan.tanggal_masuk, karyawan.bidang_id,
        karyawan.bagian, 
        bidang.nama_bidang 
    FROM 
        karyawan 
    LEFT JOIN 
        bidang ON karyawan.bidang_id = bidang.id
";

$where_clauses = [];
if (!empty($search_parent_bidang)) {
    $where_clauses[] = "karyawan.bidang_id = $search_parent_bidang";
}
if (!empty($search_sub_bidang_id)) {
    // Karena 'jabatan' disimpan sebagai nama, kita perlu mengambil namanya dari ID
    $q_sub_nama = mysqli_query($config, "SELECT nama_bidang FROM bidang WHERE id = $search_sub_bidang_id");
    if ($row = mysqli_fetch_assoc($q_sub_nama)) {
        $nama_sub_bidang = mysqli_real_escape_string($config, $row['nama_bidang']);
        $where_clauses[] = "karyawan.jabatan = '$nama_sub_bidang'";
    }
}
if (!empty($search_keyword)) {
    $where_clauses[] = "(karyawan.nama_karyawan LIKE '%$search_keyword%' OR karyawan.nik LIKE '%$search_keyword%')";
}

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(' AND ', $where_clauses);
}

$query .= " ORDER BY karyawan.nama_karyawan ASC";
$getalldata = mysqli_query($config, $query);

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Data Karyawan</h1>
    <hr>
    
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahModal">
        <i class="fas fa-plus"></i> Tambah Karyawan
    </button>

    <!-- ====================================================== -->
    <!-- === BARU: FORM PENCARIAN & FILTER === -->
    <!-- ====================================================== -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-search"></i> Cari & Filter Karyawan</h6>
        </div>
        <div class="card-body">
            <form method="GET">
                <input type="hidden" name="page" value="karyawan">
                <div class="row align-items-end g-3">
                    <div class="col-md-3">
                        <label for="karyawanParentBidangFilter" class="form-label">Kategori Bidang</label>
                        <select name="search_parent_bidang" id="karyawanParentBidangFilter" class="form-select">
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
                    <div class="col-md-3">
                        <label for="karyawanSubBidangFilter" class="form-label">Sub-Bidang (Posisi)</label>
                        <select name="search_sub_bidang" id="karyawanSubBidangFilter" class="form-select" <?= !$search_parent_bidang ? 'disabled' : '' ?>>
                            <option value="">Pilih Kategori Dahulu</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="searchKeyword" class="form-label">Cari Nama atau NIK</label>
                        <input type="text" name="search_keyword" id="searchKeyword" class="form-control" placeholder="Masukkan nama atau NIK..." value="<?= htmlspecialchars($search_keyword) ?>">
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success w-100"><i class="fas fa-search"></i></button>
                            <a href="?page=karyawan" class="btn btn-outline-secondary w-100"><i class="fas fa-sync-alt"></i></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- ====================================================== -->

    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 fw-bold text-primary">Daftar Karyawan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                   <thead class="thead">
    <tr>
        <th>No</th>
        <th>NIK</th>
        <th>Nama Karyawan</th>
        <th>Bidang</th>
        <th>Jabatan</th>
        <th>Bagian</th> 
        <th>Tanggal Masuk</th>
        <th>Opsi</th>
    </tr>
</thead>
                    <tbody>
                        <?php
                        if ($getalldata && mysqli_num_rows($getalldata) > 0) {
                            $i = 1;
                            while ($data = mysqli_fetch_array($getalldata)) {
                                $id_karyawan = $data['id'];
                        ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($data['nik']) ?></td>
                                    <td><?= htmlspecialchars($data['nama_karyawan']) ?></td>
                                    <td><?= htmlspecialchars($data['nama_bidang'] ?? '<i>N/A</i>') ?></td>
                                    <td><?= htmlspecialchars($data['jabatan']) ?></td>
<td><?= htmlspecialchars($data['bagian']) ?></td> <td>
    <?= date('d F Y', strtotime($data['tanggal_masuk'])) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $id_karyawan ?>">
                                            Ubah
                                        </button>
                                        <a href="?page=karyawan&aksi=hapus_karyawan&id=<?= $id_karyawan ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                               




 <div class="modal fade" id="editModal<?= $id_karyawan ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Ubah Data Karyawan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?= $id_karyawan ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">NIK</label>
                                                    <input type="text" name="nik" class="form-control" value="<?= htmlspecialchars($data['nik']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Karyawan</label>
                                                    <input type="text" name="nama_karyawan" class="form-control" value="<?= htmlspecialchars($data['nama_karyawan']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Jabatan</label>
                                                    <input type="text" name="jabatan" class="form-control" value="<?= htmlspecialchars($data['jabatan']) ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Tanggal Masuk</label>
                                                    <input type="date" name="tanggal_masuk" class="form-control" value="<?= htmlspecialchars($data['tanggal_masuk']) ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Bidang</label>
                                                    <select name="bidang_id" class="form-select" required>
                                                        <option value="">-- Pilih Bidang --</option>
                                                        <?php
                                                        $query_bidang_edit = mysqli_query($config, "SELECT * FROM bidang");
                                                        while($bidang_edit = mysqli_fetch_array($query_bidang_edit)){
                                                            $selected = ($bidang_edit['id'] == $data['bidang_id']) ? 'selected' : '';
                                                            echo "<option value='".$bidang_edit['id']."' ".$selected.">".htmlspecialchars($bidang_edit['nama_bidang'])."</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary" name="updateKaryawan">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            } // Akhir while
                        } else {
                            echo "<tr><td colspan='7' class='text-center text-muted'>Data tidak ditemukan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Karyawan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">NIK</label>
                        <input type="text" name="nik" placeholder="Masukkan NIK" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Karyawan</label>
                        <input type="text" name="nama_karyawan" placeholder="Masukkan Nama Lengkap" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="jabatan" placeholder="Contoh: Staff Marketing" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bidang</label>
                        <select name="bidang_id" class="form-select" required>
                            <option value="">-- Pilih Bidang --</option>
                            <?php
                            $query_bidang = mysqli_query($config, "SELECT * FROM bidang");
                            while($bidang = mysqli_fetch_array($query_bidang)){
                                echo "<option value='".$bidang['id']."'>".htmlspecialchars($bidang['nama_bidang'])."</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" name="tambahKaryawan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>