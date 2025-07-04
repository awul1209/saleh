
    <main>
    
        <!-- Tambahkan ini di dalam container-fluid -->
        <div class="row mt-4">
        <div class="col-12">
        <div class="card shadow mb-4" style="max-width: 1100px; margin: auto;">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Manajemen Data Profil Ideal</h6>
            </div>
            <div class="card-body">

                <!-- Form Input -->
<div class="container-fluid px-4">
    <div class="card mb-4" style="max-width: 1100px; margin: auto;">
     <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#tambahModal">
    <i class="fa fa-plus"></i> Tambah Profil Ideal
</button>

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
        <thead class="thead">
            <tr>
                <th>No</th>
                <th>Nama Bidang</th>
                <th>Kriteria</th>
                <th>Nilai Ideal</th>
                <th>Opsi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query untuk menampilkan data dengan relasi ke tabel bidang
            $query = "
                SELECT 
                    bidang.nama_bidang, 
                    profil_ideal.id,
                    profil_ideal.kriteria, 
                    profil_ideal.nilai,
                    profil_ideal.bidang_id
                FROM 
                    profil_ideal
                INNER JOIN 
                    bidang ON profil_ideal.bidang_id = bidang.id
                ORDER BY 
                    bidang.nama_bidang, profil_ideal.kriteria
            ";
            
            $getalldata = mysqli_query($config, $query);
            
            $i = 1;
            while ($data = mysqli_fetch_array($getalldata)) {
                $id_profil = $data['id'];
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($data['nama_bidang']) ?></td>
                    <td><?= htmlspecialchars($data['kriteria']) ?></td>
                    <td><?= htmlspecialchars($data['nilai']) ?></td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $id_profil ?>">
                            Ubah
                        </button>
                        <a href="?page=profil-ideal&aksi=hapus_profil&id=<?= $id_profil ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                            Hapus
                        </a>
                    </td>
                </tr>

                <div class="modal fade" id="editModal<?= $id_profil ?>" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Ubah Profil Ideal</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $id_profil ?>">
                                    
                                    <div class="form-group">
                                        <label>Bidang</label>
                            <select name="bidang_id" class="form-control" required>
                            <?php
                            // Query untuk mengisi dropdown bidang
                            $query_bidang_edit = mysqli_query($config, "SELECT * FROM bidang");

                            // Kita butuh nama bidang dari data yang sedang di-edit untuk perbandingan
                            // Diasumsikan $data['nama_bidang'] sudah tersedia dari query JOIN sebelumnya
                            $nama_bidang_tersimpan = strtolower($data['nama_bidang']);
                            $nilai_tersimpan_formatted = str_replace(' ', '_', $nama_bidang_tersimpan);

                            while ($bidang_edit = mysqli_fetch_array($query_bidang_edit)) {
                            // 1. BUAT VALUE BARU: Ubah nama menjadi huruf kecil dan ganti spasi dengan underscore
                            $nama_bidang_saat_ini = strtolower($bidang_edit['nama_bidang']);
                            $value_saat_ini_formatted = str_replace(' ', '_', $nama_bidang_saat_ini);

                            // 2. LOGIKA SELECTED BARU: Bandingkan value yang sudah diformat
                            $selected = ($value_saat_ini_formatted == $nilai_tersimpan_formatted) ? 'selected' : '';

                            // 3. CETAK OPTION dengan value dan logika selected yang baru
                            echo "<option value='" . $value_saat_ini_formatted . "' " . $selected . ">" . htmlspecialchars($bidang_edit['nama_bidang']) . "</option>";
                            }
                            ?>
                            </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Kriteria</label>
                                        <input type="text" name="kriteria" class="form-control" value="<?= htmlspecialchars($data['kriteria']) ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Nilai Ideal (1-5)</label>
                                        <input type="number" name="nilai" class="form-control" min="1" max="5" value="<?= htmlspecialchars($data['nilai']) ?>" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary" name="updateProfil">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            <?php } ?>
        </tbody>
    </table>
</div>


<div class="modal fade" id="tambahModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Profil Ideal Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih Bidang</label>
<select name="bidang_id" id="pilihBidang" class="form-control" required>
    <option value="">-- Pilih Bidang --</option>
    <?php
    // Mengambil data bidang untuk dropdown
    $query_bidang = mysqli_query($config, "SELECT * FROM bidang");
    while ($bidang = mysqli_fetch_array($query_bidang)) {
        // 1. Ubah nama bidang menjadi huruf kecil
        $nama_bidang_lower = strtolower($bidang['nama_bidang']);
        
        // 2. Ganti spasi dengan underscore untuk dijadikan value
        $value_formatted = str_replace(' ', '_', $nama_bidang_lower);

        // 3. Cetak option dengan value yang baru
        echo "<option value='" . $value_formatted . "'>" . htmlspecialchars($bidang['nama_bidang']) . "</option>";
    }
    ?>
</select>

<div id="kriteriaContainer" class="mt-3">
    </div>
                    </div>

                    <div class="form-group">
                        <label>Nilai Ideal (1-5)</label>
                        <input type="number" min="1" max="5" name="nilai" placeholder="Masukkan nilai antara 1-5" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" name="tambahProfil">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end  -->


        </div>
    </div>
</div>

                <!-- End Page Content -->
            </div>
            <!-- End Main Content -->
        </div>
        
        <!-- End Content Wrapper -->
    </div>
    
    <!-- End Page Wrapper -->
    

    <!-- Scroll to Top Button -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

   <script>
document.addEventListener('DOMContentLoaded', function() {

    const pilihBidangDropdown = document.getElementById('pilihBidang');
    const kriteriaContainer = document.getElementById('kriteriaContainer');

    pilihBidangDropdown.addEventListener('change', function() {
        const selectedBidang = this.value;
        kriteriaContainer.innerHTML = ''; // Selalu kosongkan container

        if (selectedBidang === "") {
            return;
        }

        // Path ini sudah benar sesuai struktur folder Anda
        fetch(`/profil/admin/profil/get_kriteria.php?bidang=${selectedBidang}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                // 'data' adalah array berisi nama kriteria, contoh: ["pengalaman_kerja", "pendidikan"]
                
                if (data.length > 0) {
                    // ✨ 1. BUAT ELEMEN DROPDOWN (SELECT) BARU
                    const kriteriaSelect = document.createElement('select');
                    kriteriaSelect.name = 'kriteria'; // Beri nama untuk form
                    kriteriaSelect.className = 'form-control'; // Beri class bootstrap

                    // Tambahkan opsi default pertama
                    const defaultOption = document.createElement('option');
                    defaultOption.value = "";
                    defaultOption.textContent = "-- Pilih Kriteria --";
                    kriteriaSelect.appendChild(defaultOption);

                    // 2. LOOPING DATA UNTUK MEMBUAT SETIAP OPSI
                    data.forEach(kriteriaText => {
                        const option = document.createElement('option');
                        
                        // 🎯 3. FORMAT VALUE: ubah spasi jadi underscore
                        const optionValue = kriteriaText.toLowerCase().replaceAll(' ', '_');
                        option.value = optionValue;
                        
                        // Format teks yang ditampilkan agar mudah dibaca
                        const displayText = kriteriaText.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        option.textContent = displayText;
                        
                        // Masukkan opsi ke dalam dropdown
                        kriteriaSelect.appendChild(option);
                    });

                    // 4. MASUKKAN DROPDOWN YANG SUDAH JADI KE DALAM CONTAINER
                    kriteriaContainer.appendChild(kriteriaSelect);

                } else {
                    kriteriaContainer.innerHTML = '<p class="text-muted">Tidak ada kriteria untuk bidang ini.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                kriteriaContainer.innerHTML = '<p class="text-danger">Terjadi kesalahan saat memuat data.</p>';
            });
    });
});
</script>

   

<?php
// 1. PROSES TAMBAH DATA (CREATE)
if (isset($_POST['tambahProfil'])) {
    // 1. Terima NAMA bidang yang diformat dari form
    $nama_bidang_formatted = mysqli_real_escape_string($config, $_POST['bidang_id']);
    $kriteria = mysqli_real_escape_string($config, $_POST['kriteria']);
    $nilai = mysqli_real_escape_string($config, $_POST['nilai']);

    // 2. Ubah kembali nama menjadi format asli (dengan spasi)
    $nama_bidang_asli = str_replace('_', ' ', $nama_bidang_formatted);

    // 3. Cari ID numerik dari tabel `bidang` berdasarkan nama tersebut
    $query_get_id = mysqli_query($config, "SELECT id FROM bidang WHERE LOWER(nama_bidang) = '$nama_bidang_asli'");
    $data_bidang = mysqli_fetch_assoc($query_get_id);
    $bidang_id_numerik = $data_bidang['id']; // Ini adalah ID yang benar (misal: 3)

    // 4. Gunakan ID numerik yang benar untuk menyimpan data
    if ($bidang_id_numerik) {
        $insert = mysqli_query($config, "INSERT INTO profil_ideal (bidang_id, kriteria, nilai) VALUES ('$bidang_id_numerik', '$kriteria', '$nilai')");

        if ($insert) {
            echo "<script>alert('Profil ideal berhasil ditambahkan!');window.location.href='?page=profil-ideal';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data: " . mysqli_error($config) . "');</script>";
        }
    } else {
        echo "<script>alert('Gagal! Bidang tidak ditemukan.');</script>";
    }
}

// 2. PROSES UBAH DATA (UPDATE)
if (isset($_POST['updateProfil'])) {
    $id = mysqli_real_escape_string($config, $_POST['id']);
    $nama_bidang_formatted = mysqli_real_escape_string($config, $_POST['bidang_id']); // Terima nama bidang yang diformat
    $kriteria = mysqli_real_escape_string($config, $_POST['kriteria']);
    $nilai = mysqli_real_escape_string($config, $_POST['nilai']);

    // KONVERSI NAMA BIDANG MENJADI ID NUMERIK (Sama seperti logika tambah data)
    $nama_bidang_asli = str_replace('_', ' ', $nama_bidang_formatted);
    $query_get_id = mysqli_query($config, "SELECT id FROM bidang WHERE LOWER(nama_bidang) = '$nama_bidang_asli'");
    
    if($data_bidang = mysqli_fetch_assoc($query_get_id)) {
        $bidang_id_numerik = $data_bidang['id']; // Ini adalah ID yang benar

        // Gunakan ID numerik di dalam query UPDATE
        $update = mysqli_query($config, "UPDATE profil_ideal SET bidang_id='$bidang_id_numerik', kriteria='$kriteria', nilai='$nilai' WHERE id='$id'");

        if ($update) {
            echo "<script>alert('Profil ideal berhasil diubah!');window.location.href='?page=profil-ideal';</script>";
        } else {
            echo "<script>alert('Gagal mengubah data: " . mysqli_error($config) . "');</script>";
        }
    } else {
        echo "<script>alert('Gagal mengubah data: Bidang tidak ditemukan.');</script>";
    }
}

?>


