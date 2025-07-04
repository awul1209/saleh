<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Creative Agency</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="admin/js/alert.js"></script>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
    }
    .hero {
      background: url('https://images.unsplash.com/photo-1519389950473-47ba0277781c') center/cover no-repeat;
      height: 100vh;
      position: relative;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }
    .hero::before {
      content: "";
      background: rgba(0, 0, 0, 0.6);
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0; left: 0;
      z-index: 1;
    }
    .hero-content {
      position: relative;
      z-index: 2;
    }
    .hero h1 {
      font-size: 3rem;
      font-weight: bold;
    }
    .section-title {
      margin: 60px 0 20px;
      text-align: center;
    }
    .feature-icon {
      font-size: 3rem;
      margin-bottom: 10px;
      color: #00bcd4;
    }
    #snow-canvas {
      position: fixed;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 10;
    }
        .bidang-icon {
      width: 80px;
      height: 80px;
      object-fit: contain;
      margin-bottom: 10px;
    }
/* Efek shadow hover pada card */
.hover-shadow:hover {
  box-shadow: 0 0 25px rgba(0, 0, 0, 0.15) !important;
  transform: translateY(-5px);
  transition: 0.3s ease-in-out;
}

/* Untuk navbar transparan */
.navbar {
  backdrop-filter: blur(10px);
  background-color: rgba(255, 255, 255, 0.8) !important;
  transition: background-color 0.3s;
}

  </style>

</head>
<body style="padding-top: 50px;">

<canvas id="snow-canvas"></canvas>

<!-- Navbar -->
 <!-- Transparent Navbar -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top bg-transparent">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="#">MyWeb</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="#bidang">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="#kontak">Contact</a></li>
      </ul>
    </div>
    <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
<!-- <form class="d-flex me-2" method="GET" action="?#bidang">
  <input class="form-control rounded-pill me-2" type="search" name="cari" placeholder="Cari bidang..." value="<?= isset($_GET['cari']) ? $_GET['cari'] : '' ?>">
  <button class="btn btn-outline-primary rounded-pill me-2" type="submit">Cari</button>

  <?php if (isset($_GET['cari']) && $_GET['cari'] != '') { ?>
    <a href="index.php#bidang" class="btn btn-secondary rounded-pill">Refresh</a>
  <?php } ?>
</form> -->
<!-- Tombol Kondisional Login/Dashboard -->
            <div class="d-flex">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="btn btn-success rounded-pill me-2">Dashboard</a>
                    <a href="logout.php" class="btn btn-danger rounded-pill">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary rounded-pill">Login</a>
                <?php endif; ?>
            </div>


    </div>
  </div>
</nav>



<!-- Hero Section -->
<section class="hero text-white text-center d-flex align-items-center justify-content-center">
  <div class="hero-content">
<h1>Temukan Bidang yang Sesuai<br>Dengan Keahlian Anda</h1>
<p class="lead">Gabung bersama kami dan kembangkan potensi terbaik Anda di bidang yang tepat.</p>
 <a href="#bidang" class="btn btn-primary rounded-pill px-4 py-2 mt-4">Jelajahi Bidang</a>
  </div>
</section>

<!-- About Section -->
<section class="container my-5 pt-5" id="bidang">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Temukan Posisi Impian Anda</h2>
        <p class="text-muted">Pilih kategori bidang untuk melihat posisi yang tersedia.</p>
    </div>

    <?php
    // Ambil ID kategori (parent) yang aktif dari URL
    $parent_id_aktif = isset($_GET['kategori_id']) ? (int)$_GET['kategori_id'] : 0;
    ?>

    <div class="text-center mb-5">
        <div class="btn-group flex-wrap" role="group">
            <a href="?page=home#bidang" class="btn <?= ($parent_id_aktif == 0) ? 'btn-primary' : 'btn-outline-primary' ?> m-1 rounded-pill">Semua Bidang</a>
            <?php
            // Ambil semua kategori induk (parent_id = 0)
            $query_kategori = mysqli_query($config, "SELECT id, nama_bidang FROM bidang WHERE parent_id = 0 ORDER BY nama_bidang ASC");
            while ($kat = mysqli_fetch_assoc($query_kategori)) {
                $is_active = ($parent_id_aktif == $kat['id']);
            ?>
                <a href="?page=home&kategori_id=<?= $kat['id'] ?>#bidang" class="btn <?= $is_active ? 'btn-primary' : 'btn-outline-primary' ?> m-1 rounded-pill">
                    <?= htmlspecialchars($kat['nama_bidang']) ?>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="row g-4">
        <?php
        // Bangun query untuk mengambil sub-bidang (anak)
        $query_utama_sql = "SELECT * FROM bidang WHERE parent_id != 0";
        if ($parent_id_aktif > 0) {
            // Jika ada kategori yang dipilih, filter berdasarkan parent_id
            $query_utama_sql .= " AND parent_id = $parent_id_aktif";
        }
        
        $query = mysqli_query($config, $query_utama_sql);
        
        if ($query && mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_assoc($query)) {
                $id = $data['id'];
                $nama = $data['nama_bidang'];
                $gambar = $data['gambar'];
                $deskripsi = $data['deskripsi'];
        ?>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 transition-all hover-shadow">
                    <img src="admin/img/bidang/<?= $gambar ?>" class="card-img-top rounded-top-4" style="height: 200px; object-fit: cover;" alt="<?= htmlspecialchars($nama); ?>">
                    <div class="card-body text-center d-flex flex-column">
                        <h5 class="card-title mb-3"><?= htmlspecialchars($nama); ?></h5>
                        <div class="d-grid gap-2 mt-auto">
                            <button class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $id; ?>">Detail</button>
                            <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalDaftar<?= $id; ?>">Daftar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalDetail<?= $id; ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content rounded-4">
                        <div class="modal-header">
                            <h5 class="modal-title">Detail: <?= htmlspecialchars($nama) ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <img src="admin/img/bidang/<?= htmlspecialchars($gambar); ?>" class="img-fluid rounded mb-3" alt="<?= htmlspecialchars($nama); ?>" style="max-height: 250px; width: 100%; object-fit: cover;">
                            <hr>
                            <div>
                                <p class="text-start"><?= nl2br(htmlspecialchars($deskripsi)); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalDaftar<?= $id; ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content rounded-4">
                        <div class="modal-header">
                            <h5 class="modal-title">Form Pendaftaran - <?= htmlspecialchars($nama); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <input type="hidden" name="id_bidang" value="<?= $id; ?>">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" name="nama" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email Aktif</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">No. Handphone</label>
                                        <input type="text" name="nohp" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Konfirmasi Password</label>
                                        <input type="password" name="password_confirm" class="form-control" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Alamat Domisili</label>
                                        <textarea name="alamat" class="form-control" rows="2" required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Tingkat Pendidikan Terakhir</label>
                                        <select name="pendidikan" class="form-select" required>
                                            <option value="" disabled selected>-- Pilih --</option>
                                            <option value="5">S2 (Magister)</option>
                                            <option value="4">S1 (Sarjana)</option>
                                            <option value="3">D3 (Diploma)</option>
                                            <option value="2">SMA/SMK</option>
                                            <option value="1">Di bawah SMA</option>
                                        </select>
                                    </div>
                                    <hr class="my-3">
                                    <p class="text-muted mb-2">Unggah Berkas Lamaran (PDF/JPG/PNG, max 2MB per file)</p>
                                    <div class="col-md-6"><label class="form-label">CV</label><input type="file" name="cv" class="form-control" required></div>
                                    <div class="col-md-6"><label class="form-label">Ijazah</label><input type="file" name="ijazah" class="form-control" required></div>
                                    <div class="col-md-6"><label class="form-label">SKCK</label><input type="file" name="skck" class="form-control" required></div>
                                    <div class="col-md-6"><label class="form-label">Surat Kesehatan</label><input type="file" name="sk" class="form-control"></div>
                                    <div class="col-md-6"><label class="form-label">KTP</label><input type="file" name="ktp" class="form-control"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="submit_daftar" class="btn btn-success rounded-pill w-100 p-2">Kirim Lamaran</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        <?php 
            } // Akhir while
        } else { // Jika tidak ada data yang ditemukan
        ?>
            <div class="col-12 text-center">
                <p class="text-muted fs-4 mt-5">Maaf, belum ada posisi yang tersedia untuk kategori ini.</p>
            </div>
        <?php
        } // Akhir if
        ?>
    </div>
</section>

     



<!-- About Section -->
<!-- Redesigned About Section -->
<section id="about" class="py-5 bg-white">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 mb-4 mb-lg-0">
        <img src="img/about1.png" alt="Tentang Kami" class="img-fluid rounded-4 shadow">
      </div>
      <div class="col-lg-6">
        <h2 class="fw-bold mb-3">Tentang Website Ini</h2>
        <p class="text-muted">Website ini dirancang untuk memudahkan pencari kerja atau individu yang ingin bergabung dalam suatu bidang layanan tertentu. Kami menyediakan informasi lengkap dan sistem pendaftaran yang praktis secara online.</p>
        <p class="text-muted">Dengan tampilan yang modern dan fitur interaktif, pengguna dapat menjelajahi berbagai bidang, melihat detail, dan langsung mendaftar hanya dalam beberapa klik. Semua proses dilakukan secara digital, aman, dan efisien.</p>
        <ul class="list-unstyled mt-4">
          <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Antarmuka yang ramah pengguna</li>
          <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Sistem pendaftaran yang cepat dan mudah</li>
          <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Informasi bidang layanan terupdate</li>
        </ul>
      </div>
    </div>
  </div>
</section>



<!-- Contact Section -->
<section  id="kontak" class="py-5" id="contact" style="background: #f8f9fa;">
  <div class="container">
    <div class="text-center mb-4">
      <h2 class="fw-bold">Hubungi Kami</h2>
      <p class="text-muted">Kami siap membantu Anda kapan saja</p>
    </div>
    <div class="row justify-content-center text-center">
      <div class="col-md-4 mb-3">
        <div class="shadow rounded-4 p-4 bg-white h-100">
          <i class="fab fa-whatsapp fa-2x text-success mb-3"></i>
          <h5 class="fw-bold">WhatsApp</h5>
          <p class="text-muted mb-1">+62 812-3456-7890</p>
          <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-outline-success btn-sm rounded-pill">Chat Sekarang</a>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="shadow rounded-4 p-4 bg-white h-100">
          <i class="fas fa-envelope fa-2x text-danger mb-3"></i>
          <h5 class="fw-bold">Email</h5>
          <p class="text-muted mb-1">kontak@layananbidang.com</p>
          <a href="mailto:kontak@layananbidang.com" class="btn btn-outline-danger btn-sm rounded-pill">Kirim Email</a>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="shadow rounded-4 p-4 bg-white h-100">
          <i class="fas fa-map-marker-alt fa-2x text-primary mb-3"></i>
          <h5 class="fw-bold">Alamat Kantor</h5>
          <p class="text-muted">Jl. Merdeka No. 123, Sumenep, Jawa Timur</p>
        </div>
      </div>
    </div>
  </div>
</section>


<footer class="bg-dark text-white text-center py-4 mt-5">
  <div class="container">
    <p class="mb-1">&copy; <?php echo date("Y"); ?> - Website Bidang Layanan</p>
    <small>Dibuat dengan ❤️ oleh Tim Developer</small>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Snowfall effect -->
<script>
  const canvas = document.getElementById("snow-canvas");
  const ctx = canvas.getContext("2d");
  let width = canvas.width = window.innerWidth;
  let height = canvas.height = window.innerHeight;
  let flakes = [];

  function SnowFlake() {
    this.x = Math.random() * width;
    this.y = Math.random() * height;
    this.r = Math.random() * 4 + 1;
    this.d = Math.random() * flakes.length;
    this.speed = Math.random() + 0.5;
  }

  for (let i = 0; i < 150; i++) {
    flakes.push(new SnowFlake());
  }

  function drawFlakes() {
    ctx.clearRect(0, 0, width, height);
    ctx.fillStyle = "white";
    ctx.beginPath();
    for (let i = 0; i < flakes.length; i++) {
      let f = flakes[i];
      ctx.moveTo(f.x, f.y);
      ctx.arc(f.x, f.y, f.r, 0, Math.PI * 2, true);
    }
    ctx.fill();
    moveFlakes();
  }

  function moveFlakes() {
    for (let i = 0; i < flakes.length; i++) {
      let f = flakes[i];
      f.y += f.speed;
      if (f.y > height) {
        flakes[i] = new SnowFlake();
        flakes[i].y = 0;
      }
    }
  }

  setInterval(drawFlakes, 25);
</script>

</body>
</html>
<?php


function upload_cv() {
    if (!isset($_FILES['cv']) || $_FILES['cv']['error'] == UPLOAD_ERR_NO_FILE) {
        return null; // PERBAIKAN: Jika file tidak ada, kembalikan null (kosong)
    }
    if ($_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Terjadi error saat mengupload CV.');</script>";
        return false; // Kembalikan false hanya jika ada error upload
    }
    // ... (sisa logika validasi ukuran, ekstensi, dan pemindahan file)
    $namaFile = $_FILES['cv']['name'];
    $ukuranFile = $_FILES['cv']['size'];
    $tmpName = $_FILES['cv']['tmp_name'];
    $ekstensiFile = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $ekstensiValid = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array($ekstensiFile, $ekstensiValid)) { echo "<script>alert('Format file CV tidak diizinkan!');</script>"; return false; }
    if ($ukuranFile > 5000000) { echo "<script>alert('Ukuran file CV terlalu besar!');</script>"; return false; }
    $namaFileBaru = 'CV_' . uniqid() . '.' . $ekstensiFile;
    $folderTujuan = 'admin/cv_uploads/';
    if (!is_dir($folderTujuan)) { mkdir($folderTujuan, 0777, true); }
    if (move_uploaded_file($tmpName, $folderTujuan . $namaFileBaru)) { return $namaFileBaru; } 
    else { echo "<script>alert('Gagal memindahkan file CV.');</script>"; return false; }
}

function upload_ijazah() {
    if (!isset($_FILES['ijazah']) || $_FILES['ijazah']['error'] == UPLOAD_ERR_NO_FILE) {
        return null; // PERBAIKAN: Jika file tidak ada, kembalikan null
    }
    if ($_FILES['ijazah']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Terjadi error saat mengupload Ijazah.');</script>";
        return false;
    }
    // ... (sisa logika validasi)
    $namaFile = $_FILES['ijazah']['name'];
    $ukuranFile = $_FILES['ijazah']['size'];
    $tmpName = $_FILES['ijazah']['tmp_name'];
    $ekstensiFile = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $ekstensiValid = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array($ekstensiFile, $ekstensiValid)) { echo "<script>alert('Format file Ijazah tidak diizinkan!');</script>"; return false; }
    if ($ukuranFile > 5000000) { echo "<script>alert('Ukuran file Ijazah terlalu besar!');</script>"; return false; }
    $namaFileBaru = 'IJAZAH_' . uniqid() . '.' . $ekstensiFile;
    $folderTujuan = 'admin/cv_uploads/';
    if (!is_dir($folderTujuan)) { mkdir($folderTujuan, 0777, true); }
    if (move_uploaded_file($tmpName, $folderTujuan . $namaFileBaru)) { return $namaFileBaru; }
    else { echo "<script>alert('Gagal memindahkan file Ijazah.');</script>"; return false; }
}

function upload_skck() {
    if (!isset($_FILES['skck']) || $_FILES['skck']['error'] == UPLOAD_ERR_NO_FILE) {
        return null; // PERBAIKAN: Jika file tidak ada, kembalikan null
    }
    if ($_FILES['skck']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Terjadi error saat mengupload SKCK.');</script>";
        return false;
    }
    // ... (sisa logika validasi)
    $namaFile = $_FILES['skck']['name'];
    $ukuranFile = $_FILES['skck']['size'];
    $tmpName = $_FILES['skck']['tmp_name'];
    $ekstensiFile = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $ekstensiValid = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array($ekstensiFile, $ekstensiValid)) { echo "<script>alert('Format file SKCK tidak diizinkan!');</script>"; return false; }
    if ($ukuranFile > 5000000) { echo "<script>alert('Ukuran file SKCK terlalu besar!');</script>"; return false; }
    $namaFileBaru = 'SKCK_' . uniqid() . '.' . $ekstensiFile;
    $folderTujuan = 'admin/cv_uploads/';
    if (!is_dir($folderTujuan)) { mkdir($folderTujuan, 0777, true); }
    if (move_uploaded_file($tmpName, $folderTujuan . $namaFileBaru)) { return $namaFileBaru; }
    else { echo "<script>alert('Gagal memindahkan file SKCK.');</script>"; return false; }
}

function upload_sk() {
    if (!isset($_FILES['sk']) || $_FILES['sk']['error'] == UPLOAD_ERR_NO_FILE) {
        return null; // PERBAIKAN: Jika file tidak ada, kembalikan null
    }
    if ($_FILES['sk']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Terjadi error saat mengupload Surat Kesehatan.');</script>";
        return false;
    }
    // ... (sisa logika validasi)
    $namaFile = $_FILES['sk']['name'];
    $ukuranFile = $_FILES['sk']['size'];
    $tmpName = $_FILES['sk']['tmp_name'];
    $ekstensiFile = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $ekstensiValid = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array($ekstensiFile, $ekstensiValid)) { echo "<script>alert('Format file Surat Kesehatan tidak diizinkan!');</script>"; return false; }
    if ($ukuranFile > 5000000) { echo "<script>alert('Ukuran file Surat Kesehatan terlalu besar!');</script>"; return false; }
    $namaFileBaru = 'SK_' . uniqid() . '.' . $ekstensiFile;
    $folderTujuan = 'admin/cv_uploads/';
    if (!is_dir($folderTujuan)) { mkdir($folderTujuan, 0777, true); }
    if (move_uploaded_file($tmpName, $folderTujuan . $namaFileBaru)) { return $namaFileBaru; }
    else { echo "<script>alert('Gagal memindahkan file Surat Kesehatan.');</script>"; return false; }
}

function upload_ktp() {
    if (!isset($_FILES['ktp']) || $_FILES['ktp']['error'] == UPLOAD_ERR_NO_FILE) {
        return null; // PERBAIKAN: Jika file tidak ada, kembalikan null
    }
    if ($_FILES['ktp']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Terjadi error saat mengupload KTP.');</script>";
        return false;
    }
    // ... (sisa logika validasi)
    $namaFile = $_FILES['ktp']['name'];
    $ukuranFile = $_FILES['ktp']['size'];
    $tmpName = $_FILES['ktp']['tmp_name'];
    $ekstensiFile = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $ekstensiValid = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array($ekstensiFile, $ekstensiValid)) { echo "<script>alert('Format file KTP tidak diizinkan!');</script>"; return false; }
    if ($ukuranFile > 5000000) { echo "<script>alert('Ukuran file KTP terlalu besar!');</script>"; return false; }
    $namaFileBaru = 'KTP_' . uniqid() . '.' . $ekstensiFile;
    $folderTujuan = 'admin/cv_uploads/';
    if (!is_dir($folderTujuan)) { mkdir($folderTujuan, 0777, true); }
    if (move_uploaded_file($tmpName, $folderTujuan . $namaFileBaru)) { return $namaFileBaru; }
    else { echo "<script>alert('Gagal memindahkan file KTP.');</script>"; return false; }
}


if (isset($_POST['submit_daftar'])) {
    
    // Ambil semua data dari POST
    $id_bidang = (int)$_POST['id_bidang'];
    $nama = mysqli_real_escape_string($config, $_POST['nama']);
    $email = mysqli_real_escape_string($config, $_POST['email']);
    $username = mysqli_real_escape_string($config, $_POST['username']);
    $nohp = mysqli_real_escape_string($config, $_POST['nohp']);
    $alamat = mysqli_real_escape_string($config, $_POST['alamat']);
    $pendidikan_nilai = (int)$_POST['pendidikan'];
    $tahun_daftar = date('Y');
    
    // Validasi & Keamanan Password
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password !== $password_confirm) {
        echo "<script>alert('Konfirmasi password tidak cocok!'); window.history.back();</script>";
        exit;
    }
    // Hashing password sebelum disimpan ke database
    $hashed_password = $password;

    // Mulai transaksi database untuk memastikan semua data tersimpan atau tidak sama sekali
    mysqli_begin_transaction($config);

    try {
        // Panggil setiap fungsi upload Anda
        $nama_file_cv = upload_cv();
        $nama_file_ijazah = upload_ijazah();
        $nama_file_skck = upload_skck();
        $nama_file_sk = upload_sk();
        $nama_file_ktp = upload_ktp();

        // Cek jika ada error saat upload
        $upload_check = [$nama_file_cv, $nama_file_ijazah, $nama_file_skck]; // CV, Ijazah, SKCK wajib
        if (in_array(false, $upload_check, true)) {
            throw new Exception('Upload file wajib (CV/Ijazah/SKCK) gagal. Pastikan format dan ukuran file sesuai.');
        }

        // 1. Simpan data utama ke tabel 'calon_karyawan'
        $query_calon = "INSERT INTO calon_karyawan 
                        (nama, email, username, password, nohp, alamat, tahun_daftar, bidang_id, cv, ijazah, skck, surat_kesehatan, ktp, status) 
                        VALUES 
                        ('$nama', '$email', '$username', '$hashed_password', '$nohp', '$alamat', '$tahun_daftar', '$id_bidang', '$nama_file_cv', '$nama_file_ijazah', '$nama_file_skck', '$nama_file_sk', '$nama_file_ktp', 'Diproses')";
        
        if (!mysqli_query($config, $query_calon)) {
            throw new Exception("Gagal menyimpan data calon karyawan: " . mysqli_error($config));
        }

        // Ambil ID dari calon karyawan yang baru saja dibuat
        $calon_karyawan_id = mysqli_insert_id($config);

        // 2. Simpan nilai pendidikan ke tabel 'nilai_kandidat'
        $query_pendidikan = "INSERT INTO nilai_kandidat (calon_karyawan_id, kriteria, nilai) VALUES ('$calon_karyawan_id', 'pendidikan', '$pendidikan_nilai')";
        
        if (!mysqli_query($config, $query_pendidikan)) {
            throw new Exception("Gagal menyimpan nilai pendidikan: " . mysqli_error($config));
        }

        // Jika semua query berhasil, simpan perubahan secara permanen
        mysqli_commit($config);
        echo "<script>alert('Pendaftaran berhasil! Terima kasih telah melamar, kami akan segera menghubungi Anda.'); window.location.href = '?page=home';</script>";

    } catch (Exception $e) {
        // Jika ada satu saja kesalahan, batalkan semua query yang sudah dijalankan
        mysqli_rollback($config);

        // Hapus file yang mungkin sudah terlanjur di-upload
        $uploaded_files = [$nama_file_cv, $nama_file_ijazah, $nama_file_skck, $nama_file_sk, $nama_file_ktp];
        foreach($uploaded_files as $file) {
            if($file && file_exists('admin/cv_uploads/' . $file)) {
                unlink('admin/cv_uploads/' . $file);
            }
        }
        
        echo "<script>alert('Pendaftaran Gagal: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
    exit; // Hentikan eksekusi skrip setelah pemrosesan form selesai
}


?>
