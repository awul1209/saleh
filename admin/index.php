<?php
require '../config.php';
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}
// Ambil data user dari session
$data_id = $_SESSION['admin'];
$data_username = $_SESSION['nama'];
$data_email = $_SESSION['email'];
$data_role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Admin</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

<script src="js/alert.js"></script>

</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include 'layout/sidebar.php'; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">
                <?php include 'management_page.php'; ?>
            </div>
            <!-- End of Main Content -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Pilih "Logout" di bawah jika kamu ingin mengakhiri sesi ini.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <a class="btn btn-primary" href="?page=logout">Logout</a>
                </div>
            </div>
        </div>
    </div>



  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <script src="js/sb-admin-2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>


<script>


  $(document).ready(function() {
    // Definisikan nama file AJAX handler Anda
    const ajax_url = 'ajax_handler.php';

    // --- Fungsi untuk membuat input kriteria ---
    function buildCriteriaInputs(container, kriteriaList, nilaiMap = {}) {
        container.empty();
        if (kriteriaList && kriteriaList.length > 0) {
            const row = $('<div class="row"></div>');
            kriteriaList.forEach(function(kriteria) {
                const nilai = nilaiMap[kriteria] || '';
                const labelText = kriteria.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                const col = $('<div class="col-md-4 mb-3"></div>');
                const label = $('<label class="form-label"></label>').text(labelText);
                const input = $(`<input type="number" class="form-control" name="kriteria[${kriteria}]" value="${nilai}" min="1" max="5" required>`);
                col.append(label, input);
                row.append(col);
            });
            container.append(row);
        } else {
            container.html('<p class="text-muted"><i>Tidak ada kriteria yang ditetapkan untuk bidang ini.</i></p>');
        }
    }

    // --- Fungsi untuk memuat kriteria via AJAX ---
    function loadKriteria(bidangId, container, nilaiMap = {}) {
        if (!bidangId) {
            container.html('<p class="text-muted"><i>Pilih bidang terlebih dahulu.</i></p>');
            return;
        }
        container.html('<p class="text-info"><i>Memuat kriteria...</i></p>');
        
        // Panggil file AJAX yang baru
        $.getJSON('ajax_handler.php', { action: 'get_kriteria', bidang_id: bidangId })
            .done(function(kriteriaList) {
                buildCriteriaInputs(container, kriteriaList, nilaiMap);
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
                container.html('<p class="text-danger"><i>Gagal memuat kriteria. Cek Console (F12).</i></p>');
            });
    }

    // --- Event Handler untuk Tombol "Lihat Berkas" ---
    $('body').on('click', '.btn-toggle-berkas', function() {
        const calonId = $(this).data('calon-id');
        $('#berkas-list-' + calonId).toggle();
        if ($('#berkas-list-' + calonId).is(':visible')) {
            $(this).html('<i class="fas fa-times"></i> Tutup').removeClass('btn-info').addClass('btn-secondary');
        } else {
            $(this).html('<i class="fas fa-folder-open"></i> Lihat').removeClass('btn-secondary').addClass('btn-info');
        }
    });

    // --- Event handler untuk modal TAMBAH ---
    $('body').on('change', '#tambahBidangSelect', function() {
        const bidangId = $(this).val();
        const container = $('#tambahKriteriaContainer');
        loadKriteria(bidangId, container);
    });

    // --- Event handler untuk modal EDIT (saat akan ditampilkan) ---
    $('body').on('show.bs.modal', '.modal[id^="editModal"]', function () {
        const modal = $(this);
        const container = modal.find('.dynamic-criteria-container');
        const calonId = container.data('calon-id');
        const bidangId = modal.find('.editBidangSelect').val();
        
        // Panggil file AJAX yang baru
        const kriteriaRequest = $.getJSON(ajax_url, { action: 'get_kriteria', bidang_id: bidangId });
        const nilaiRequest = $.getJSON(ajax_url, { action: 'get_nilai', calon_id: calonId });

        $.when(kriteriaRequest, nilaiRequest).done(function(kriteriaResult, nilaiResult) {
            const kriteriaList = kriteriaResult[0];
            const nilaiMap = nilaiResult[0];
            buildCriteriaInputs(container, kriteriaList, nilaiMap);
        }).fail(function() {
            container.html('<p class="text-danger"><i>Gagal memuat data kriteria.</i></p>');
        });
    });

    // --- Event handler untuk dropdown bidang di dalam modal EDIT ---
    $('body').on('change', '.editBidangSelect', function() {
        const modal = $(this).closest('.modal');
        const container = modal.find('.dynamic-criteria-container');
        const bidangId = $(this).val();
        loadKriteria(bidangId, container, {}); 
    });
});
</script>

<!-- script hasil -->
 <script>
$(document).ready(function() {
    // Jalankan skrip ini hanya jika kita berada di halaman hasil
    if ($('#parentBidangFilter').length) {
        
        var selectedSubBidang = '<?= $search_sub_bidang ?? 0; ?>';

        function populateSubBidang(parentId, callback) {
            var subBidangSelect = $('#subBidangFilter');
            
            if (parentId && parentId !== "") {
                subBidangSelect.prop('disabled', true).html('<option value="">Memuat...</option>');
                $.ajax({
                    url: 'ajax_handler.php',
                    type: 'GET',
                    data: { action: 'get_sub_bidang', parent_id: parentId },
                    dataType: 'json',
                    success: function(data) {
                        subBidangSelect.prop('disabled', false).html('<option value="">Semua Sub-Bidang</option>');
                        $.each(data, function(key, value) {
                            subBidangSelect.append($('<option></option>').attr('value', value.id).text(value.nama_bidang));
                        });
                        if (callback) callback();
                    },
                    error: function() {
                        subBidangSelect.prop('disabled', false).html('<option value="">Gagal memuat</option>');
                    }
                });
            } else {
                subBidangSelect.prop('disabled', true).html('<option value="">Pilih Kategori Dahulu</option>');
            }
        }

        function initializeDropdown() {
            var parentId = $('#parentBidangFilter').val();
            if (parentId) {
                populateSubBidang(parentId, function() {
                    if (selectedSubBidang) {
                        $('#subBidangFilter').val(selectedSubBidang);
                    }
                });
            }
        }

        initializeDropdown();

        $('#parentBidangFilter').on('change', function() {
            selectedSubBidang = null;
            populateSubBidang($(this).val());
        });
    }

    // ... (kode JavaScript lain untuk halaman lain bisa ditambahkan di sini) ...
});
</script>

<script>
    // Di dalam file index.php, di dalam $(document).ready()

// --- LOGIKA UNTUK FILTER DI HALAMAN KARYAWAN ---
if ($('#karyawanParentBidangFilter').length) {
    
    // Ambil ID sub bidang yang sudah terpilih dari URL (jika ada)
    const urlParams = new URLSearchParams(window.location.search);
    var selectedSubBidang = urlParams.get('search_sub_bidang');

    function populateKaryawanSubBidang(parentId, callback) {
        var subBidangSelect = $('#karyawanSubBidangFilter');
        
        if (parentId && parentId !== "") {
            subBidangSelect.prop('disabled', true).html('<option value="">Memuat...</option>');
            $.ajax({
                url: 'ajax_handler.php', // Panggil AJAX handler yang sudah ada
                type: 'GET',
                data: { action: 'get_sub_bidang', parent_id: parentId },
                dataType: 'json',
                success: function(data) {
                    subBidangSelect.prop('disabled', false).html('<option value="">Semua Sub-Bidang</option>');
                    $.each(data, function(key, value) {
                        subBidangSelect.append($('<option></option>').attr('value', value.id).text(value.nama_bidang));
                    });
                    if (callback) callback();
                },
                error: function() {
                    subBidangSelect.prop('disabled', false).html('<option value="">Gagal memuat</option>');
                }
            });
        } else {
            subBidangSelect.prop('disabled', true).html('<option value="">Pilih Kategori Dahulu</option>');
        }
    }

    // Fungsi untuk menginisialisasi dropdown saat halaman dimuat
    function initializeKaryawanDropdown() {
        var parentId = $('#karyawanParentBidangFilter').val();
        if (parentId) {
            populateKaryawanSubBidang(parentId, function() {
                // Setelah opsi dimuat, set nilai yang terpilih
                if (selectedSubBidang) {
                    $('#karyawanSubBidangFilter').val(selectedSubBidang);
                }
            });
        }
    }

    // Panggil saat halaman pertama kali siap
    initializeKaryawanDropdown();

    // Event handler untuk mengubah dropdown kategori bidang
    $('#karyawanParentBidangFilter').on('change', function() {
        selectedSubBidang = null; // Reset pilihan lama
        populateKaryawanSubBidang($(this).val());
    });
}
</script>


</body>

</html>
