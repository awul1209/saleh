<?php
    // --- BAGIAN 1: KODE PHP DINAMIS ANDA (DIPERBAIKI & LEBIH AMAN) ---
    
    // Inisialisasi variabel sebagai string JSON yang valid
    $labels_json = '[]';
    $data_counts_json = '[]';

    // Pastikan variabel koneksi $config sudah ada dari file include Anda
    if (isset($config) && $config) {
        $sql = "
            SELECT 
                b.nama_bidang, 
                COUNT(k.id) AS jumlah_karyawan 
            FROM bidang b
            LEFT JOIN karyawan k ON b.id = k.bidang_id 
            GROUP BY b.id, b.nama_bidang
            ORDER BY b.nama_bidang ASC
        ";
        
        $query_chart = mysqli_query($config, $sql);
        if ($query_chart) {
            $labels = [];
            $data_counts = [];
            while($row = mysqli_fetch_assoc($query_chart)){
                $labels[] = $row['nama_bidang'];
                $data_counts[] = (int)$row['jumlah_karyawan'];
            }
            // Langsung encode ke format JSON
            $labels_json = json_encode($labels);
            $data_counts_json = json_encode($data_counts);
        }
    }
?>

<!-- BAGIAN 2: KODE HTML DASHBOARD ANDA (TIDAK ADA PERUBAHAN SIGNIFIKAN) -->
<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Dashboard</h1>
        <div class="text-end">
            <p class="mb-0">Selamat Datang kembali, <strong><?php echo isset($data_username) ? htmlspecialchars($data_username) : 'Pengguna'; ?></strong>!</p>
            <small><?php echo date('l, j F Y'); // Menampilkan tanggal hari ini ?></small>
        </div>
    </div>
    <hr>

    <!-- Baris untuk Kartu Statistik -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Jumlah Calon Karyawan</div>
                            <?php
                                $jumlah_karyawan = 0;
                                if (isset($config) && $config) {
                                    $query_karyawan = mysqli_query($config, "SELECT COUNT(id) as total FROM calon_karyawan");
                                    if($query_karyawan) {
                                        $data_karyawan = mysqli_fetch_assoc($query_karyawan);
                                        $jumlah_karyawan = $data_karyawan['total'];
                                    }
                                }
                            ?>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?php echo $jumlah_karyawan; ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Jumlah Bidang</div>
                               <?php
                                    $jumlah_bidang = 0;
                                    if (isset($config) && $config) {
                                        $query_bidang = mysqli_query($config, "SELECT COUNT(id) as total FROM bidang");
                                        if($query_bidang) {
                                            $data_bidang = mysqli_fetch_assoc($query_bidang);
                                            $jumlah_bidang = $data_bidang['total'];
                                        }
                                    }
                                ?>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?php echo $jumlah_bidang; ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-briefcase fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Total Kriteria Bobot</div>
                            <?php
                                $jumlah_bobot = 0;
                                if (isset($config) && $config) {
                                    $query_bobot = mysqli_query($config, "SELECT COUNT(id) as total FROM bobot_kriteria");
                                    if($query_bobot) {
                                        $data_bobot = mysqli_fetch_assoc($query_bobot);
                                        $jumlah_bobot = $data_bobot['total'];
                                    }
                                }
                            ?>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?php echo $jumlah_bobot; ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-weight-hanging fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Jumlah User</div>
                            <?php
                                $jumlah_admin = 0;
                                if (isset($config) && $config) {
                                    $query_admin = mysqli_query($config, "SELECT COUNT(id) as total FROM user");
                                    if($query_admin) {
                                        $data_admin = mysqli_fetch_assoc($query_admin);
                                        $jumlah_admin = $data_admin['total'];
                                    }
                                }
                            ?>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?php echo $jumlah_admin; ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-shield fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Baris untuk Panel Aksi dan Chart -->
    <div class="row">
        <div class="col-xl-5 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Panel Perhitungan Profile Matching</h6>
                </div>
                <div class="card-body text-center">
                    <p>Mulai proses perhitungan untuk menentukan peringkat karyawan berdasarkan profil ideal yang telah ditetapkan.</p>
                   <img src="img/rank.png" alt="Analysis Illustration" style="width: 75px; opacity: 0.9;" class="my-3">
                    <a href="http://127.0.0.1:5000" target="_blank" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-calculator me-2"></i> Mulai Perhitungan Baru
                    </a>
                    <a href="?page=hasil" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                        Lihat Hasil Perhitungan Terakhir
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-7 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Distribusi Karyawan per Bidang</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 280px;">
                        <!-- Kanvas untuk chart, sekarang tidak perlu atribut data-* -->
                        <canvas id="myBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================== -->
<!-- BAGIAN 3: SCRIPT FINAL YANG DIPERBAIKI UNTUK CHART.JS -->
<!-- ============================================================================== -->
<script>
// Menjalankan kode setelah seluruh halaman (termasuk skrip lain dari template) selesai dimuat
window.addEventListener('load', function() {
    
    // Memberi jeda singkat untuk memastikan skrip ini berjalan paling akhir
    setTimeout(function() {
        
        var canvas = document.getElementById('myBarChart');
        // Jika tidak ada elemen canvas di halaman ini, hentikan eksekusi
        if (!canvas) {
            return;
        }

        // PERBAIKAN: Mengambil data langsung dari variabel PHP yang dicetak sebagai JavaScript
        const chartLabels = <?= $labels_json; ?>;
        const chartData = <?= $data_counts_json; ?>;
        
        // Fungsi untuk menghasilkan warna dinamis
        function generateDynamicColors(numColors) {
            const baseColors = [
                'rgba(54, 162, 235, 0.6)', 'rgba(255, 99, 132, 0.6)',
                'rgba(255, 206, 86, 0.6)', 'rgba(75, 192, 192, 0.6)',
                'rgba(153, 102, 255, 0.6)', 'rgba(255, 159, 64, 0.6)'
            ];
            const colors = [];
            for (let i = 0; i < numColors; i++) {
                colors.push(baseColors[i % baseColors.length]);
            }
            return colors;
        }

        const dynamicBackgrounds = generateDynamicColors(chartLabels.length);
        const dynamicBorders = dynamicBackgrounds.map(color => color.replace('0.6', '1'));

        // Membuat chart baru dengan sintaks yang kompatibel dengan Chart.js v2
        var ctx = canvas.getContext('2d');
        var myNewChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: "Jumlah Karyawan",
                    backgroundColor: dynamicBackgrounds,
                    borderColor: dynamicBorders,
                    borderWidth: 1,
                    data: chartData,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    xAxes: [{
                        gridLines: { display: false },
                        ticks: { autoSkip: false, maxRotation: 45, minRotation: 0 }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1,
                            callback: function(value) { if (Number.isInteger(value)) { return value; } }
                        }
                    }]
                }
            }
        });

    }, 250); // Jeda 250 milidetik
});
</script>
