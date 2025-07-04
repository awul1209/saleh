<?php
// Pastikan semua link CSS dan JS Bootstrap sudah dimuat.
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
// Pastikan variabel koneksi $config sudah tersedia.

// 1. Ambil ID bidang dari URL dengan aman
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID Bidang tidak valid.</div>";
    exit;
}
$id_bidang = (int)$_GET['id'];

// 2. Query untuk mengambil detail bidang dan nama induknya
$query_detail = mysqli_query($config, "
    SELECT 
        b.*, 
        p.nama_bidang AS nama_induk 
    FROM 
        bidang b 
    LEFT JOIN 
        bidang p ON b.parent_id = p.id 
    WHERE 
        b.id = $id_bidang
");

if (mysqli_num_rows($query_detail) === 0) {
    echo "<div class='alert alert-warning'>Data bidang tidak ditemukan.</div>";
    exit;
}
$detail = mysqli_fetch_assoc($query_detail);
?>

<main>
    <div class="container-fluid px-4">
        <!-- Header Halaman -->
        <h1 class="mt-4"><?= htmlspecialchars($detail['nama_bidang']) ?></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="?page=bidang">Manajemen Bidang</a></li>
            <?php if ($detail['parent_id'] != 0): ?>
                <li class="breadcrumb-item"><?= htmlspecialchars($detail['nama_induk']) ?></li>
            <?php endif; ?>
            <li class="breadcrumb-item active">Detail</li>
        </ol>

        <!-- Deskripsi Bidang -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle me-1"></i>
                Deskripsi Bidang
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($detail['deskripsi'])) ?></p>
            </div>
        </div>

        <div class="row">
            <!-- Kolom Kiri: Profil Ideal & Bobot -->
            <div class="col-lg-6">
                <!-- Card untuk Profil Ideal -->
                <div class="card mb-4">
                    <div class="card-header"><i class="fas fa-star me-1"></i>Profil Ideal</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php
                            $query_profil = mysqli_query($config, "SELECT kriteria, nilai FROM profil_ideal WHERE bidang_id = $id_bidang");
                            if (mysqli_num_rows($query_profil) > 0) {
                                while ($profil = mysqli_fetch_assoc($query_profil)) {
                                    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>" . htmlspecialchars($profil['kriteria']) . "<span class='badge bg-primary rounded-pill'>{$profil['nilai']}</span></li>";
                                }
                            } else {
                                echo "<li class='list-group-item text-muted'>Belum ada profil ideal yang ditetapkan.</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>

                <!-- Card untuk Bobot Kriteria -->
                <div class="card mb-4">
                    <div class="card-header"><i class="fas fa-weight-hanging me-1"></i>Bobot Kriteria</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php
                            $query_bobot = mysqli_query($config, "SELECT kriteria, bobot FROM bobot_kriteria WHERE bidang_id = $id_bidang");
                            if (mysqli_num_rows($query_bobot) > 0) {
                                while ($bobot = mysqli_fetch_assoc($query_bobot)) {
                                    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>" . htmlspecialchars($bobot['kriteria']) . "<span class='badge bg-secondary rounded-pill'>{$bobot['bobot']}</span></li>";
                                }
                            } else {
                                echo "<li class='list-group-item text-muted'>Belum ada bobot kriteria yang ditetapkan.</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Daftar Pelamar -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header"><i class="fas fa-users me-1"></i>Daftar Pelamar di Bidang Ini</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama Pelamar</th>
                                        <th>Tahun Daftar</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query_pelamar = mysqli_query($config, "SELECT nama, tahun_daftar, status FROM calon_karyawan WHERE bidang_id = $id_bidang ORDER BY nama ASC");
                                    if (mysqli_num_rows($query_pelamar) > 0) {
                                        while ($pelamar = mysqli_fetch_assoc($query_pelamar)) {
                                            $status = $pelamar['status'];
                                            $badge_color = 'secondary';
                                            if ($status == 'Lolos') { $badge_color = 'success'; } 
                                            elseif ($status == 'Tidak Lolos') { $badge_color = 'danger'; }
                                    ?>
                                            <tr>
                                                <td><?= htmlspecialchars($pelamar['nama']) ?></td>
                                                <td><?= htmlspecialchars($pelamar['tahun_daftar']) ?></td>
                                                <td><span class="badge bg-<?= $badge_color ?>"><?= htmlspecialchars($status) ?></span></td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="3" class="text-center text-muted">Belum ada pelamar untuk bidang ini.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
