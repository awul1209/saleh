<?php
// Pastikan semua link CSS dan JS Bootstrap sudah dimuat.
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<?php
// Pastikan variabel koneksi $config sudah tersedia.

$selected_bidang_id = isset($_GET['bidang_id']) ? (int)$_GET['bidang_id'] : 0;

// --- LOGIKA PEMROSESAN FORM ---

// 1. PROSES TAMBAH KRITERIA BARU
if (isset($_POST['simpanKriteria'])) {
    $bidang_id = (int)$_POST['bidang_id'];
    $kriteria = mysqli_real_escape_string($config, str_replace(' ', '_', strtolower($_POST['kriteria'])));
    $nilai_ideal = (int)$_POST['nilai_ideal'];
    $bobot = (float)$_POST['bobot'];

    mysqli_begin_transaction($config);
    try {
        // Simpan ke profil_ideal
        $q1 = "INSERT INTO profil_ideal (bidang_id, kriteria, nilai) VALUES ($bidang_id, '$kriteria', $nilai_ideal)";
        if (!mysqli_query($config, $q1)) throw new Exception(mysqli_error($config));

        // Simpan ke bobot_kriteria
        $q2 = "INSERT INTO bobot_kriteria (bidang_id, kriteria, bobot) VALUES ($bidang_id, '$kriteria', $bobot)";
        if (!mysqli_query($config, $q2)) throw new Exception(mysqli_error($config));
        
        mysqli_commit($config);
        echo "<script>alert('Kriteria baru berhasil ditambahkan!'); window.location.href='?page=standar-bidang&bidang_id=$bidang_id';</script>";
    } catch (Exception $e) {
        mysqli_rollback($config);
        echo "<script>alert('Gagal menambahkan kriteria: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// 2. PROSES UBAH KRITERIA
if (isset($_POST['updateKriteria'])) {
    $profil_id = (int)$_POST['profil_id'];
    $bobot_id = (int)$_POST['bobot_id'];
    $bidang_id = (int)$_POST['bidang_id'];
    $nilai_ideal = (int)$_POST['nilai_ideal'];
    $bobot = (float)$_POST['bobot'];

    mysqli_begin_transaction($config);
    try {
        $q1 = "UPDATE profil_ideal SET nilai = $nilai_ideal WHERE id = $profil_id";
        if (!mysqli_query($config, $q1)) throw new Exception(mysqli_error($config));

        $q2 = "UPDATE bobot_kriteria SET bobot = $bobot WHERE id = $bobot_id";
        if (!mysqli_query($config, $q2)) throw new Exception(mysqli_error($config));
        
        mysqli_commit($config);
        echo "<script>alert('Data kriteria berhasil diubah!'); window.location.href='?page=standar-bidang&bidang_id=$bidang_id';</script>";
    } catch (Exception $e) {
        mysqli_rollback($config);
        echo "<script>alert('Gagal mengubah data: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// 3. PROSES HAPUS KRITERIA
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus_kriteria') {
    $profil_id = (int)$_GET['profil_id'];
    $bobot_id = (int)$_GET['bobot_id'];
    $bidang_id = (int)$_GET['bidang_id'];

    mysqli_begin_transaction($config);
    try {
        mysqli_query($config, "DELETE FROM profil_ideal WHERE id = $profil_id");
        mysqli_query($config, "DELETE FROM bobot_kriteria WHERE id = $bobot_id");
        mysqli_commit($config);
        echo "<script>alert('Kriteria berhasil dihapus.'); window.location.href='?page=standar-bidang&bidang_id=$bidang_id';</script>";
    } catch (Exception $e) {
        mysqli_rollback($config);
        echo "<script>alert('Gagal menghapus kriteria.');</script>";
    }
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Pengaturan Standar Bidang</h1>
    <p class="mb-4 text-muted">Definisikan kriteria, nilai ideal, dan bobot untuk setiap bidang pekerjaan.</p>

    <div class="card shadow-sm mb-4">
        <div class="card-header"><h5 class="m-0 fw-bold text-primary"><i class="fas fa-filter me-2"></i>Pilih Bidang untuk Diatur</h5></div>
        <div class="card-body">
            <form method="GET">
                <input type="hidden" name="page" value="standar-bidang">
                <div class="row align-items-end">
                    <div class="col-md-10">
                        <label for="bidang_id_select" class="form-label">Bidang Pekerjaan</label>
                        <select name="bidang_id" id="bidang_id_select" class="form-select form-select-lg" onchange="this.form.submit()">
                            <option value="">-- Pilih Bidang --</option>
                            <?php
                            $query_bidang = mysqli_query($config, "SELECT id, nama_bidang FROM bidang WHERE parent_id != 0 ORDER BY nama_bidang ASC");
                            while ($bidang = mysqli_fetch_assoc($query_bidang)) {
                                $selected = ($selected_bidang_id == $bidang['id']) ? 'selected' : '';
                                echo "<option value='{$bidang['id']}' $selected>" . htmlspecialchars($bidang['nama_bidang']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($selected_bidang_id > 0): ?>
    <?php
        // Ambil data gabungan dari profil_ideal dan bobot_kriteria
        $kriteria_data = [];
        $query_gabungan = mysqli_query($config, "
            SELECT 
                p.id as profil_id, 
                b.id as bobot_id, 
                p.kriteria, 
                p.nilai, 
                b.bobot 
            FROM profil_ideal p 
            JOIN bobot_kriteria b ON p.kriteria = b.kriteria AND p.bidang_id = b.bidang_id
            WHERE p.bidang_id = $selected_bidang_id
            ORDER BY p.kriteria ASC
        ");
        $total_bobot = 0;
    ?>
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold text-primary"><i class="fas fa-tasks me-2"></i>Standar untuk: <?= htmlspecialchars(mysqli_fetch_assoc(mysqli_query($config, "SELECT nama_bidang FROM bidang WHERE id=$selected_bidang_id"))['nama_bidang']); ?></h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahKriteriaModal"><i class="fas fa-plus me-2"></i>Tambah Kriteria</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kriteria</th>
                                <th>Nilai Ideal</th>
                                <th>Bobot</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($query_gabungan) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($query_gabungan)): 
                                    $total_bobot += $row['bobot'];
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars(ucwords(str_replace('_', ' ', $row['kriteria']))) ?></td>
                                        <td><span class="badge bg-info"><?= $row['nilai'] ?></span></td>
                                        <td><span class="badge bg-secondary"><?= $row['bobot'] ?></span></td>
                                        <td class="text-center">
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['profil_id'] ?>"><i class="fas fa-edit"></i></button>
                                            <a href="?page=standar-bidang&aksi=hapus_kriteria&profil_id=<?= $row['profil_id'] ?>&bobot_id=<?= $row['bobot_id'] ?>&bidang_id=<?= $selected_bidang_id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus kriteria ini?')"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    
                                    <!-- Modal Edit -->
                                    <div class="modal fade" id="editModal<?= $row['profil_id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header"><h5 class="modal-title">Edit Kriteria: <?= htmlspecialchars(ucwords(str_replace('_', ' ', $row['kriteria']))) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="profil_id" value="<?= $row['profil_id'] ?>">
                                                        <input type="hidden" name="bobot_id" value="<?= $row['bobot_id'] ?>">
                                                        <input type="hidden" name="bidang_id" value="<?= $selected_bidang_id ?>">
                                                        <div class="mb-3"><label>Nilai Ideal (1-5)</label><input type="number" name="nilai_ideal" class="form-control" min="1" max="5" value="<?= $row['nilai'] ?>" required></div>
                                                        <div class="mb-3"><label>Bobot (0.01 - 1.00)</label><input type="number" name="bobot" class="form-control" step="0.01" min="0" max="1" value="<?= $row['bobot'] ?>" required></div>
                                                    </div>
                                                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary" name="updateKriteria">Simpan</button></div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada kriteria yang ditetapkan untuk bidang ini.</td></tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if(mysqli_num_rows($query_gabungan) > 0): ?>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Total Bobot:</th>
                                <th colspan="2">
                                    <span id="totalBobotBadge" class="badge <?= (round($total_bobot, 2) == 1.00) ? 'bg-success' : 'bg-danger' ?>">
                                        <?= number_format($total_bobot, 2) ?>
                                    </span>
                                    <?php if(round($total_bobot, 2) != 1.00): ?>
                                        <small class="text-danger ms-2">Total bobot harus 1.00!</small>
                                    <?php endif; ?>
                                </th>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah Kriteria -->
<div class="modal fade" id="tambahKriteriaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Tambah Kriteria Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="bidang_id" value="<?= $selected_bidang_id; ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama Kriteria</label>
                        <input type="text" name="kriteria" class="form-control" placeholder="Contoh: Pengalaman Organisasi" required>
                        <small class="text-muted">Gunakan underscore (_) sebagai pengganti spasi.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nilai Ideal (1-5)</label>
                        <input type="number" name="nilai_ideal" class="form-control" min="1" max="5" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bobot (Contoh: 0.25)</label>
                        <input type="number" name="bobot" class="form-control" step="0.01" min="0" max="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" name="simpanKriteria">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
