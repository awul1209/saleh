<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <?php if($data_role=='perusahaan'){ ?>
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="?page=home">
            <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-laugh-wink"></i></div>
            <div class="sidebar-brand-text mx-3">Perusahaan</div>
        </a>
        <hr class="sidebar-divider my-0">
        <li class="nav-item active">
            <a class="nav-link" href="?page=home"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Manajemen Data</div>

        <li class="nav-item">
            <a class="nav-link" href="?page=admin"><i class="fas fa-home"></i><span>Data Admin</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="?page=karyawan"><i class="fa fa-user-circle"></i><span>Data Karyawan</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="?page=calon-karyawan"><i class="fa fa-address-card"></i><span>Data Calon Karyawan</span></a>
        </li>

        <li class="nav-item">
<a class="nav-link collapsed d-flex align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#collapseBidang">
    <i class="fas fa-fw fa-briefcase"></i>
    <span class="flex-grow-1">Data Lowongan Bidang</span>
    <i class="fas fa-fw fa-angle-down"></i>
</a>
            <div id="collapseBidang" class="collapse" data-bs-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Master Data:</h6>
                    <a class="collapse-item" href="?page=bidang">Kelola Semua Bidang</a>
                    <a class="collapse-item" href="?page=standar-bidang">Kelola Bobot</a>
                    <div class="collapse-divider"></div>
                    <h6 class="collapse-header">Bidang Tersedia:</h6>
                    <?php
                    // Ambil semua bidang utama (induk)
                    $query_induk = mysqli_query($config, "SELECT * FROM bidang WHERE parent_id = 0 ORDER BY nama_bidang ASC");
                    while ($induk = mysqli_fetch_assoc($query_induk)) {
                        $id_induk = $induk['id'];
                        // Cek apakah bidang induk ini punya anak/sub-bidang
                        $query_cek_anak = mysqli_query($config, "SELECT id FROM bidang WHERE parent_id = '$id_induk'");
                        $punya_anak = mysqli_num_rows($query_cek_anak) > 0;

                        if ($punya_anak) {
                            // Jika punya anak, buat sebagai dropdown
                    ?>
                            <a class="collapse-item" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSub<?= $id_induk; ?>">
                                <?= htmlspecialchars($induk['nama_bidang']); ?> <i class="fas fa-fw fa-caret-down"></i>
                            </a>
                            <div id="collapseSub<?= $id_induk; ?>" class="collapse" data-bs-parent="#collapseBidang">
                                <div class="bg-light py-2 collapse-inner rounded" style="font-size: 0.85rem;">
                                    <?php
                                    // Ambil semua sub-bidang dari induk ini
                                    $query_anak = mysqli_query($config, "SELECT * FROM bidang WHERE parent_id = '$id_induk' ORDER BY nama_bidang ASC");
                                    while ($anak = mysqli_fetch_assoc($query_anak)) {
                                    ?>
                                        <a class="collapse-item" href="?page=detail-bidang&id=<?= $anak['id']; ?>">- <?= htmlspecialchars($anak['nama_bidang']); ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                    <?php
                        } else {
                            // Jika tidak punya anak, buat sebagai link biasa
                    ?>
                            <a class="collapse-item" href="?page=detail-bidang&id=<?= $id_induk; ?>"><?= htmlspecialchars($induk['nama_bidang']); ?></a>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </li>
        <hr class="sidebar-divider">
        <div class="sidebar-heading">Proses & Hasil</div>

        <li class="nav-item">
            <a class="nav-link" href="http://127.0.0.1:5000" target="_blank"><i class="fas fa-calculator"></i><span>Perhitungan</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="?page=hasil"><i class="fa fa-address-book"></i><span>Hasil Perhitungan</span></a>
        </li>

    <?php } elseif($data_role=='admin') { ?>
 <li class="nav-item active">
            <a class="nav-link" href="?page=home-admin"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a>
        </li>
         <li class="nav-item">
            <a class="nav-link" href="?page=admin"><i class="fas fa-home"></i><span>Data Admin</span></a>
        </li>
        <li class="nav-item">
<a class="nav-link collapsed d-flex align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#collapseBidang">
    <i class="fas fa-fw fa-briefcase"></i>
    <span class="flex-grow-1">Data Lowongan Bidang</span>
    <i class="fas fa-fw fa-angle-down"></i>
</a>
            <div id="collapseBidang" class="collapse" data-bs-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Master Data:</h6>
                    <a class="collapse-item" href="?page=bidang">Kelola Semua Bidang</a>
                    <!-- <a class="collapse-item" href="?page=standar-bidang">Kelola Bobot</a> -->
                    <div class="collapse-divider"></div>
                    <h6 class="collapse-header">Bidang Tersedia:</h6>
                    <?php
                    // Ambil semua bidang utama (induk)
                    $query_induk = mysqli_query($config, "SELECT * FROM bidang WHERE parent_id = 0 ORDER BY nama_bidang ASC");
                    while ($induk = mysqli_fetch_assoc($query_induk)) {
                        $id_induk = $induk['id'];
                        // Cek apakah bidang induk ini punya anak/sub-bidang
                        $query_cek_anak = mysqli_query($config, "SELECT id FROM bidang WHERE parent_id = '$id_induk'");
                        $punya_anak = mysqli_num_rows($query_cek_anak) > 0;

                        if ($punya_anak) {
                            // Jika punya anak, buat sebagai dropdown
                    ?>
                            <a class="collapse-item" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSub<?= $id_induk; ?>">
                                <?= htmlspecialchars($induk['nama_bidang']); ?> <i class="fas fa-fw fa-caret-down"></i>
                            </a>
                            <div id="collapseSub<?= $id_induk; ?>" class="collapse" data-bs-parent="#collapseBidang">
                                <div class="bg-light py-2 collapse-inner rounded" style="font-size: 0.85rem;">
                                    <?php
                                    // Ambil semua sub-bidang dari induk ini
                                    $query_anak = mysqli_query($config, "SELECT * FROM bidang WHERE parent_id = '$id_induk' ORDER BY nama_bidang ASC");
                                    while ($anak = mysqli_fetch_assoc($query_anak)) {
                                    ?>
                                        <a class="collapse-item" href="?page=detail-bidang&id=<?= $anak['id']; ?>">- <?= htmlspecialchars($anak['nama_bidang']); ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                    <?php
                        } else {
                            // Jika tidak punya anak, buat sebagai link biasa
                    ?>
                            <a class="collapse-item" href="?page=detail-bidang&id=<?= $id_induk; ?>"><?= htmlspecialchars($induk['nama_bidang']); ?></a>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </li>
        <hr class="sidebar-divider">
         <li class="nav-item">
            <a class="nav-link" href="http://127.0.0.1:5000" target="_blank"><i class="fas fa-calculator"></i><span>Perhitungan</span></a>
        </li>
        <?php } ?>
    
    <li class="nav-item">
        <a class="nav-link" href="?page=logout"><i class="fa fa-arrow-circle-left"></i><span>Logout</span></a>
    </li>
    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>