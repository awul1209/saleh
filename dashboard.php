<?php
session_start();
include 'config.php';

// Lindungi halaman: jika belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// --- FUNGSI UPLOAD (Dibutuhkan untuk proses update) ---
function upload_file_dashboard($input_name, $file_prefix) {
    // Cek jika tidak ada file baru yang diupload, ini bukan error.
    if (!isset($_FILES[$input_name]) || $_FILES[$input_name]['error'] == UPLOAD_ERR_NO_FILE) {
        return null; 
    }
    
    // Cek jika ada error lain saat upload
    if ($_FILES[$input_name]['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Terjadi error saat mengupload file {$file_prefix}.');</script>";
        return false;
    }

    $namaFile = $_FILES[$input_name]['name'];
    $ukuranFile = $_FILES[$input_name]['size'];
    $tmpName = $_FILES[$input_name]['tmp_name'];
    $ekstensiFile = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $ekstensiValid = ['pdf', 'jpg', 'jpeg', 'png'];

    if (!in_array($ekstensiFile, $ekstensiValid)) {
        echo "<script>alert('Format file {$file_prefix} tidak diizinkan!');</script>";
        return false;
    }
    if ($ukuranFile > 5000000) { // Maks 5MB
        echo "<script>alert('Ukuran file {$file_prefix} terlalu besar!');</script>";
        return false;
    }

    $namaFileBaru = strtoupper($file_prefix) . '_' . uniqid() . '.' . time() . '.' . $ekstensiFile;
    $folderTujuan = 'admin/cv_uploads/'; // Sesuaikan dengan path folder Anda
    if (!is_dir($folderTujuan)) {
        mkdir($folderTujuan, 0777, true);
    }
    if (move_uploaded_file($tmpName, $folderTujuan . $namaFileBaru)) {
        return $namaFileBaru;
    } else {
        echo "<script>alert('Gagal memindahkan file {$file_prefix}.');</script>";
        return false;
    }
}

// --- LOGIKA UPDATE PROFIL ---
if (isset($_POST['update_profil'])) {
    $nama = mysqli_real_escape_string($config, $_POST['nama']);
    $email = mysqli_real_escape_string($config, $_POST['email']);
    $nohp = mysqli_real_escape_string($config, $_POST['nohp']);
    $alamat = mysqli_real_escape_string($config, $_POST['alamat']);

    // Daftar file dan nama input lama dari hidden fields
    $file_fields = [
        'cv' => $_POST['cv_lama'],
        'ijazah' => $_POST['ijazah_lama'],
        'skck' => $_POST['skck_lama'],
        'surat_kesehatan' => $_POST['sk_lama'],
        'ktp' => $_POST['ktp_lama']
    ];
    
    $files_baru = [];
    $upload_error = false;

    foreach($file_fields as $db_key => $file_lama) {
        $input_name = ($db_key === 'surat_kesehatan') ? 'sk' : $db_key;
        $file_baru = upload_file_dashboard($input_name, strtoupper($db_key));

        if ($file_baru === false) { // Jika upload gagal
            $upload_error = true;
            break; 
        }
        
        $files_baru[$db_key] = ($file_baru === null) ? $file_lama : $file_baru;

        // Hapus file lama jika ada file baru yang diupload
        if ($file_baru !== null && !empty($file_lama) && file_exists('admin/cv_uploads/' . $file_lama)) {
            unlink('admin/cv_uploads/' . $file_lama);
        }
    }
    
    if (!$upload_error) {
        $update_query = "UPDATE calon_karyawan SET 
                            nama='$nama', email='$email', nohp='$nohp', alamat='$alamat', 
                            cv='{$files_baru['cv']}', ijazah='{$files_baru['ijazah']}', skck='{$files_baru['skck']}', 
                            surat_kesehatan='{$files_baru['surat_kesehatan']}', ktp='{$files_baru['ktp']}'
                        WHERE id='$user_id'";
        $update = mysqli_query($config, $update_query);

        if ($update) {
            // Update nama di session jika berhasil
            $_SESSION['user_nama'] = $nama;
            echo "<script>alert('Profil berhasil diperbarui!');window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui profil: " . mysqli_error($config) . "');</script>";
        }
    }
}


// --- Mengambil data terbaru untuk ditampilkan ---
$query = "SELECT ck.*, b.nama_bidang 
          FROM calon_karyawan ck 
          LEFT JOIN bidang b ON ck.bidang_id = b.id 
          WHERE ck.id = '$user_id'";
$result = mysqli_query($config, $query);
$user_data = mysqli_fetch_assoc($result);

$file_list = [
    'cv' => 'Curriculum Vitae (CV)', 'ijazah' => 'Ijazah', 'skck' => 'SKCK',
    'surat_kesehatan' => 'Surat Kesehatan', 'ktp' => 'KTP'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - <?= htmlspecialchars($_SESSION['user_nama']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-light">

    <!-- Navbar untuk Dashboard -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Dashboard Pelamar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDashboard">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDashboard">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><span class="navbar-text me-3">Halo, <?= htmlspecialchars($_SESSION['user_nama']); ?></span></li>
                    <li class="nav-item"><a class="btn btn-danger" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <!-- Kolom Kiri: Info Profil -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-user-circle fa-5x text-secondary mb-3"></i>
                        <h4 class="card-title"><?= htmlspecialchars($user_data['nama']); ?></h4>
                        <p class="text-muted"><?= htmlspecialchars($user_data['email']); ?></p>
                        <hr>
                        <p class="card-text text-start">
                            <strong><i class="fas fa-phone me-2"></i>No. HP:</strong> <?= htmlspecialchars($user_data['nohp']); ?><br>
                            <strong><i class="fas fa-map-marker-alt me-2"></i>Alamat:</strong> <?= htmlspecialchars($user_data['alamat']); ?>
                        </p>
                        <!-- Tombol Edit Baru -->
                        <button class="btn btn-warning w-100 mt-3" data-bs-toggle="modal" data-bs-target="#editProfilModal">
                            <i class="fas fa-edit me-2"></i>Edit Profil & Berkas
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Detail Lamaran -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Detail Lamaran Anda</h5>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Status Lamaran: 
                            <span class="badge bg-info"><?= htmlspecialchars($user_data['status']); ?></span>
                        </h5>
                        <p>Anda telah mendaftar pada bidang **<?= htmlspecialchars($user_data['nama_bidang'] ?? 'Belum Ditentukan'); ?>** pada tahun **<?= htmlspecialchars($user_data['tahun_daftar']); ?>**.</p>
                        <hr>
                        <h6>Berkas yang Telah Diunggah:</h6>
                        <ul class="list-group">
                            <?php foreach($file_list as $key => $label): ?>
                                <?php if(!empty($user_data[$key])): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= $label; ?>
                                        <a href="admin/cv_uploads/<?= htmlspecialchars($user_data[$key]); ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> Lihat File
                                        </a>
                                    </li>
                                <?php else: ?>
                                     <li class="list-group-item d-flex justify-content-between align-items-center text-muted">
                                        <?= $label; ?>
                                        <span><i>(Tidak diunggah)</i></span>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Profil & Berkas -->
    <div class="modal fade" id="editProfilModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profil dan Berkas Lamaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="modal-body">
                        <!-- Kirim nama file lama untuk proses update -->
                        <input type="hidden" name="cv_lama" value="<?= htmlspecialchars($user_data['cv']) ?>">
                        <input type="hidden" name="ijazah_lama" value="<?= htmlspecialchars($user_data['ijazah']) ?>">
                        <input type="hidden" name="skck_lama" value="<?= htmlspecialchars($user_data['skck']) ?>">
                        <input type="hidden" name="sk_lama" value="<?= htmlspecialchars($user_data['surat_kesehatan']) ?>">
                        <input type="hidden" name="ktp_lama" value="<?= htmlspecialchars($user_data['ktp']) ?>">

                        <h6>Data Diri</h6>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Nama Lengkap</label><input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user_data['nama']) ?>" required></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_data['email']) ?>" required></div>
                            <div class="col-md-6"><label class="form-label">No. HP</label><input type="text" name="nohp" class="form-control" value="<?= htmlspecialchars($user_data['nohp']) ?>" required></div>
                            <div class="col-12"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2" required><?= htmlspecialchars($user_data['alamat']) ?></textarea></div>
                        </div>
                        
                        <hr class="my-4">
                        <h6>Ganti Berkas (Kosongkan jika tidak ingin mengubah)</h6>
                        <div class="row g-3">
                            <?php foreach($file_list as $key => $label): 
                                $input_name = ($key === 'surat_kesehatan') ? 'sk' : $key;
                            ?>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><?= $label ?></label>
                                    <input type="file" name="<?= $input_name ?>" class="form-control">
                                    <?php if(!empty($user_data[$key])): ?>
                                        <small class="text-muted">File saat ini: <a href="admin/cv_uploads/<?= htmlspecialchars($user_data[$key]) ?>" target="_blank">Lihat</a></small>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update_profil" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PERBAIKAN: Menambahkan script Bootstrap JS di akhir body -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
