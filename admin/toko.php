
    <main>
    
             <!-- Tambahkan ini di dalam container-fluid -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow mb-4" style="max-width: 1100px; margin: auto;">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Manajemen Master Data</h6>
                                </div>
                                <div class="card-body">

                                    <!-- Tab Button -->
                                    <div class="mb-3">
    <a href="?page=browsur" class="btn btn-light btn-sm">Perusahaan</a>
    <a href="?page=usaha" class="btn btn-light btn-sm">Usaha</a>
    <a href="?page=toko" class="btn btn-light btn-sm">Toko</a>
</div>

                                    <!-- Form Input -->
                                    <div class="container-fluid px-4">
    <div class="card mb-4" style="max-width: 1100px; margin: auto;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalGalerifoto">Tambah Foto</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $getalldata = mysqli_query($config, 'SELECT * FROM usaha');
                        $i = 1;
                        while ($data = mysqli_fetch_array($getalldata)) {
                            $id_foto = $data['id_foto'];
                            $gambar = $data['gambar'];
                            $img = $gambar ? '<img src="imgs/' . $gambar . '" class="zoomable" width="150px">' : 'No Photo';
                        ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $img ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit<?= $id_foto ?>">Edit</button>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete<?= $id_foto ?>">Hapus</button>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="edit<?= $id_foto ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Edit Galeri Foto</h4>
                                            <button type="button" class="btn-close" data-dismiss="modal"></button>
                                        </div>
                                        <form method="post" enctype="multipart/form-data">
                                            <div class="modal-body">
                                                <p class="fs-5 fw-semibold">Gambar</p>
                                                <input type="file" name="file" class="form-control">
                                                <input type="hidden" name="id_foto" value="<?= $id_foto ?>">
                                                <br>
                                                <button type="submit" class="btn btn-primary" name="updatefoto">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="delete<?= $id_foto ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Hapus Galeri Foto</h4>
                                            <button type="button" class="btn-close" data-dismiss="modal"></button>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus foto ini?
                                                <input type="hidden" name="id_foto" value="<?= $id_foto ?>">
                                                <br><br>
                                                <button type="submit" class="btn btn-danger" name="hapusfoto">Hapus</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
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

    <!-- Logout Modal -->
    <div class="modal fade" id="modalGalerifoto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Galeri Foto</h5>
                    <button class="btn-close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <p class="fs-5 fw-semibold">Gambar</p>
                        <input type="file" name="file" placeholder="Masukkan Foto" class="form-control" required>
                        <br>
                        <button type="submit" class="btn btn-primary" name="fotousaha">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



