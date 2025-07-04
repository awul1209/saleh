<?php
define('UPLOAD_FOLDER', 'cv_uploads/');

// --- FUNGSI UPLOAD FILE ---
function upload_file($input_name, $file_prefix) {
    if (!isset($_FILES[$input_name]) || $_FILES[$input_name]['error'] == UPLOAD_ERR_NO_FILE) return null;
    if ($_FILES[$input_name]['error'] !== UPLOAD_ERR_OK) return false;
    
    $namaFile = $_FILES[$input_name]['name'];
    $ukuranFile = $_FILES[$input_name]['size'];
    $tmpName = $_FILES[$input_name]['tmp_name'];
    $ekstensiFile = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $ekstensiValid = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
    if (!in_array($ekstensiFile, $ekstensiValid) || $ukuranFile > 5000000) return false;
    
    $namaFileBaru = strtoupper($file_prefix) . '_' . uniqid() . '.' . $ekstensiFile;
    if (!is_dir(UPLOAD_FOLDER)) mkdir(UPLOAD_FOLDER, 0777, true);
    
    return move_uploaded_file($tmpName, UPLOAD_FOLDER . $namaFileBaru) ? $namaFileBaru : false;
}

// --- LOGIKA PEMROSESAN FORM ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambahCalon'])) {
        mysqli_begin_transaction($config);
        try {
            $nama = mysqli_real_escape_string($config, $_POST['nama']);
            $email = mysqli_real_escape_string($config, $_POST['email']);
            $nohp = mysqli_real_escape_string($config, $_POST['nohp']);
            $alamat = mysqli_real_escape_string($config, $_POST['alamat']);
            $tahun_daftar = mysqli_real_escape_string($config, $_POST['tahun_daftar']);
            $bidang_id = (int)$_POST['bidang_id'];
            
            $files_to_upload = ['cv' => upload_file('cv', 'CV'), 'ijazah' => upload_file('ijazah', 'Ijazah'), 'skck' => upload_file('skck', 'SKCK'), 'surat_kesehatan' => upload_file('sk', 'SK'), 'ktp' => upload_file('ktp', 'KTP')];
            if (in_array(false, $files_to_upload, true)) throw new Exception("Terjadi kesalahan saat mengupload salah satu berkas.");
            
            $query_insert_calon = "INSERT INTO calon_karyawan (nama, email, nohp, alamat, tahun_daftar, bidang_id, cv, ijazah, skck, surat_kesehatan, ktp) VALUES ('$nama', '$email', '$nohp', '$alamat', '$tahun_daftar', '$bidang_id', '{$files_to_upload['cv']}', '{$files_to_upload['ijazah']}', '{$files_to_upload['skck']}', '{$files_to_upload['surat_kesehatan']}', '{$files_to_upload['ktp']}')";
            if (!mysqli_query($config, $query_insert_calon)) throw new Exception("Gagal menyimpan data pribadi: " . mysqli_error($config));
            
            $calon_id = mysqli_insert_id($config);
            if (isset($_POST['kriteria']) && is_array($_POST['kriteria'])) {
                foreach ($_POST['kriteria'] as $nama_kriteria => $nilai) {
                    $nama_kriteria_safe = mysqli_real_escape_string($config, $nama_kriteria);
                    $nilai_safe = (int)$nilai;
                    $query_insert_nilai = "INSERT INTO nilai_kandidat (calon_karyawan_id, kriteria, nilai) VALUES ('$calon_id', '$nama_kriteria_safe', '$nilai_safe')";
                    if (!mysqli_query($config, $query_insert_nilai)) throw new Exception("Gagal menyimpan nilai kriteria: " . mysqli_error($config));
                }
            }
            mysqli_commit($config);
            echo "<script>alert('Data calon berhasil ditambahkan!');window.location.href='?page=calon-karyawan';</script>";
        } catch (Exception $e) {
            mysqli_rollback($config);
            echo "<script>alert('Terjadi kesalahan: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
    
    if (isset($_POST['updateCalon'])) {
        mysqli_begin_transaction($config);
        try {
            $id = (int)$_POST['id'];
            $nama = mysqli_real_escape_string($config, $_POST['nama']);
            $email = mysqli_real_escape_string($config, $_POST['email']);
            $nohp = mysqli_real_escape_string($config, $_POST['nohp']);
            $alamat = mysqli_real_escape_string($config, $_POST['alamat']);
            $tahun_daftar = mysqli_real_escape_string($config, $_POST['tahun_daftar']);
            $bidang_id = (int)$_POST['bidang_id'];

            $file_fields = ['cv' => $_POST['cv_lama'], 'ijazah' => $_POST['ijazah_lama'], 'skck' => $_POST['skck_lama'], 'surat_kesehatan' => $_POST['sk_lama'], 'ktp' => $_POST['ktp_lama']];
            $files_baru = [];
            foreach($file_fields as $db_key => $file_lama) {
                $input_name = ($db_key === 'surat_kesehatan') ? 'sk' : $db_key;
                $file_baru = upload_file($input_name, strtoupper($db_key));
                if ($file_baru === false) throw new Exception("Gagal mengupload file " . strtoupper($db_key));
                $files_baru[$db_key] = ($file_baru === null) ? $file_lama : $file_baru;
                if ($file_baru !== null && !empty($file_lama) && file_exists(UPLOAD_FOLDER . $file_lama)) {
                    unlink(UPLOAD_FOLDER . $file_lama);
                }
            }
            $update_pribadi = "UPDATE calon_karyawan SET nama='$nama', email='$email', nohp='$nohp', alamat='$alamat', tahun_daftar='$tahun_daftar', bidang_id='$bidang_id', cv='{$files_baru['cv']}', ijazah='{$files_baru['ijazah']}', skck='{$files_baru['skck']}', surat_kesehatan='{$files_baru['surat_kesehatan']}', ktp='{$files_baru['ktp']}' WHERE id=$id";
            if (!mysqli_query($config, $update_pribadi)) throw new Exception("Gagal mengubah data pribadi: " . mysqli_error($config));
            
            mysqli_query($config, "DELETE FROM nilai_kandidat WHERE calon_karyawan_id = $id");
            if (isset($_POST['kriteria']) && is_array($_POST['kriteria'])) {
                foreach ($_POST['kriteria'] as $nama_kriteria => $nilai) {
                    $nama_kriteria_safe = mysqli_real_escape_string($config, $nama_kriteria);
                    $nilai_safe = (int)$nilai;
                    $query_insert_nilai = "INSERT INTO nilai_kandidat (calon_karyawan_id, kriteria, nilai) VALUES ('$id', '$nama_kriteria_safe', '$nilai_safe')";
                    if (!mysqli_query($config, $query_insert_nilai)) throw new Exception("Gagal menyimpan nilai kriteria baru: " . mysqli_error($config));
                }
            }
            mysqli_commit($config);
            echo "<script>alert('Data calon berhasil diubah!');window.location.href='?page=calon-karyawan';</script>";
        } catch (Exception $e) {
            mysqli_rollback($config);
            echo "<script>alert('Terjadi kesalahan saat mengubah data: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
}

// Ambil semua data calon karyawan untuk ditampilkan di tabel
$calon_karyawan_list = [];
$query_data = mysqli_query($config, "SELECT ck.*, b.nama_bidang FROM calon_karyawan ck LEFT JOIN bidang b ON ck.bidang_id = b.id ORDER BY ck.nama ASC");
if ($query_data) {
    while ($row = mysqli_fetch_assoc($query_data)) {
        $calon_karyawan_list[] = $row;
    }
}

// Ambil data bidang untuk dropdown di modal (cukup sekali saja)
$q_bidang_all = mysqli_query($config, "SELECT id, nama_bidang FROM bidang WHERE parent_id != 0 ORDER BY nama_bidang ASC");
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Data Calon Karyawan</h1>
    <hr>
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahModal"><i class="fas fa-plus"></i> Tambah Calon</button>

    <div class="card shadow mb-4">
        <div class="card-header"><h6 class="m-0 fw-bold text-primary">Daftar Calon Karyawan</h6></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama & Kontak</th>
                            <th>Bidang Dilamar</th>
                            <th>Nilai Kriteria</th>
                            <th>Berkas</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($calon_karyawan_list)): ?>
                            <?php $i = 1; foreach ($calon_karyawan_list as $data): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><strong><?= htmlspecialchars($data['nama']) ?></strong><br><small><?= htmlspecialchars($data['email']) ?></small></td>
                                    <td><?= htmlspecialchars($data['nama_bidang'] ?? '<i>N/A</i>') ?></td>
                                    <td>
                                        <ul class="list-unstyled mb-0">
                                            <?php
                                            $query_nilai_list = mysqli_query($config, "SELECT kriteria, nilai FROM nilai_kandidat WHERE calon_karyawan_id = {$data['id']}");
                                            if ($query_nilai_list && mysqli_num_rows($query_nilai_list) > 0) {
                                                while($nilai = mysqli_fetch_assoc($query_nilai_list)) {
                                                    echo "<li><small>" . htmlspecialchars(ucwords(str_replace('_', ' ', $nilai['kriteria']))) . ": <strong>" . $nilai['nilai'] . "</strong></small></li>";
                                                }
                                            } else {
                                                echo "<li><small class='text-muted'><i>Belum ada nilai.</i></small></li>";
                                            }
                                            ?>
                                        </ul>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-info btn-sm btn-toggle-berkas" data-calon-id="<?= $data['id'] ?>"><i class="fas fa-folder-open"></i> Lihat</button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $data['id'] ?>">Ubah</button>
                                    </td>
                                </tr>
                                <tr class="berkas-list" id="berkas-list-<?= $data['id'] ?>" style="display: none;">
                                    <td colspan="6">
                                        <div class="p-3 bg-light border rounded">
                                            <h6 class="fw-bold mb-3">Daftar Berkas: <?= htmlspecialchars($data['nama']) ?></h6>
                                            <table class="table table-sm table-hover mb-0">
                                                <tbody>
                                                    <?php
                                                    $file_list = ['cv' => 'CV', 'ijazah' => 'Ijazah', 'skck' => 'SKCK', 'surat_kesehatan' => 'Surat Kesehatan', 'ktp' => 'KTP'];
                                                    $berkas_ditemukan = false;
                                                    foreach ($file_list as $db_column => $label) {
                                                        if (!empty($data[$db_column]) && file_exists(UPLOAD_FOLDER . $data[$db_column])) {
                                                            echo '<tr><td>' . $label . '</td><td class="text-end"><a href="' . UPLOAD_FOLDER . htmlspecialchars($data[$db_column]) . '" target="_blank" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Lihat File</a></td></tr>';
                                                            $berkas_ditemukan = true;
                                                        }
                                                    }
                                                    if (!$berkas_ditemukan) echo '<tr><td colspan="2" class="text-center text-muted p-3"><em>Tidak ada berkas yang diunggah.</em></td></tr>';
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">Tidak ada data calon karyawan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Tambah Calon Karyawan Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                     <h6>Data Diri</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label>Nama Lengkap</label><input type="text" name="nama" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label>No. HP</label><input type="text" name="nohp" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label>Tahun Daftar</label><input type="number" name="tahun_daftar" class="form-control" value="<?= date('Y') ?>" required></div>
                        <div class="col-12 mb-3"><label>Alamat</label><textarea name="alamat" class="form-control"></textarea></div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Bidang yang Dilamar</label>
                            <select name="bidang_id" class="form-select" id="tambahBidangSelect" required>
                                <option value="">-- Pilih Bidang --</option>
                                <?php
                                if ($q_bidang_all) mysqli_data_seek($q_bidang_all, 0);
                                while($d_bidang = mysqli_fetch_array($q_bidang_all)) {
                                    echo "<option value='{$d_bidang['id']}'>" . htmlspecialchars($d_bidang['nama_bidang']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <hr><p class="fw-bold">Unggah Berkas (Opsional)</p>
                    <div class="row g-3">
                        <div class="col-md-6"><label>CV</label><input type="file" name="cv" class="form-control"></div>
                        <div class="col-md-6"><label>Ijazah</label><input type="file" name="ijazah" class="form-control"></div>
                        <div class="col-md-6"><label>SKCK</label><input type="file" name="skck" class="form-control"></div>
                        <div class="col-md-6"><label>Surat Kesehatan</label><input type="file" name="sk" class="form-control"></div>
                        <div class="col-md-6"><label>KTP</label><input type="file" name="ktp" class="form-control"></div>
                    </div>
                    <hr>
                    <p class="fw-bold">Masukkan Nilai Kriteria (1-5)</p>
                    <div id="tambahKriteriaContainer">
                        <p class="text-muted"><i>Pilih bidang terlebih dahulu untuk menampilkan kriteria yang relevan.</i></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" name="tambahCalon">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (!empty($calon_karyawan_list)): ?>
    <?php foreach ($calon_karyawan_list as $data): ?>
        <div class="modal fade" id="editModal<?= $data['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Ubah Data Calon</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <form method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" name="id" value="<?= $data['id'] ?>">
                            <input type="hidden" name="cv_lama" value="<?= htmlspecialchars($data['cv']) ?>">
                            <input type="hidden" name="ijazah_lama" value="<?= htmlspecialchars($data['ijazah']) ?>">
                            <input type="hidden" name="skck_lama" value="<?= htmlspecialchars($data['skck']) ?>">
                            <input type="hidden" name="sk_lama" value="<?= htmlspecialchars($data['surat_kesehatan']) ?>">
                            <input type="hidden" name="ktp_lama" value="<?= htmlspecialchars($data['ktp']) ?>">
                            
                            <h6>Data Diri</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3"><label>Nama Lengkap</label><input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required></div>
                                <div class="col-md-6 mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" required></div>
                                <div class="col-md-6 mb-3"><label>No. HP</label><input type="text" name="nohp" class="form-control" value="<?= htmlspecialchars($data['nohp']) ?>"></div>
                                <div class="col-md-6 mb-3"><label>Tahun Daftar</label><input type="number" name="tahun_daftar" class="form-control" value="<?= htmlspecialchars($data['tahun_daftar']) ?>" required></div>
                                <div class="col-md-12 mb-3"><label>Bidang yang Dilamar</label>
                                    <select name="bidang_id" class="form-select editBidangSelect">
                                        <?php
                                        if ($q_bidang_all) mysqli_data_seek($q_bidang_all, 0);
                                        while($d_bidang = mysqli_fetch_array($q_bidang_all)){
                                            $selected = ($d_bidang['id'] == $data['bidang_id']) ? 'selected' : '';
                                            echo "<option value='{$d_bidang['id']}' $selected>" . htmlspecialchars($d_bidang['nama_bidang']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-12 mb-3"><label>Alamat</label><textarea name="alamat" class="form-control"><?= htmlspecialchars($data['alamat']) ?></textarea></div>
                            </div>
                            <hr><p class="fw-bold">Ganti Berkas (Opsional)</p>
                            <div class="row g-3">
                                <div class="col-md-6"><label>CV</label><input type="file" name="cv" class="form-control"></div>
                                <div class="col-md-6"><label>Ijazah</label><input type="file" name="ijazah" class="form-control"></div>
                                <div class="col-md-6"><label>SKCK</label><input type="file" name="skck" class="form-control"></div>
                                <div class="col-md-6"><label>Surat Kesehatan</label><input type="file" name="sk" class="form-control"></div>
                                <div class="col-md-6"><label>KTP</label><input type="file" name="ktp" class="form-control"></div>
                            </div>
                            <hr><p class="fw-bold">Nilai Kriteria:</p>
                            <div class="dynamic-criteria-container" data-calon-id="<?= $data['id'] ?>">
                                <p class="text-muted"><i>Memuat kriteria...</i></p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" name="updateCalon">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>