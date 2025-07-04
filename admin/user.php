
    <main>
    
        <div class="container-fluid">
            <h1 class="mt-4">Kelola Data Admin</h1>
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Tambah Admin</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-width: 1100px; margin: auto;">
                        <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Email</th>
                                    <th>Nama</th>
                                    <th>Role</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $query = "SELECT * FROM user";
                                $result = mysqli_query($config, $query);
                                $i = 1;
                                while ($data = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= htmlspecialchars($data['email']); ?></td>
                                <td><?= htmlspecialchars($data['nama']); ?></td>
                                <td><?= htmlspecialchars($data['role']); ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit<?= $data['id']; ?>">Edit</button>
                                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete<?= $data['id']; ?>">Hapus</button>
                                </td>
                            </tr>
                            <!-- Modal Edit -->
                            <div class="modal fade" id="edit<?= $data['id']; ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4>Edit Admin</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                                <label>Email</label>
                                                <input type="email" name="email" value="<?= $data['email']; ?>" class="form-control" required>
                                                <input type="password" name="password" class="form-control">
                                                <label>Nama</label>
                                                <input type="text" name="nama" value="<?= $data['nama']; ?>" class="form-control" required>
                                                <label>Role</label>
                                                <input type="text" name="role" value="<?= $data['role']; ?>" class="form-control" required>
                                                <br>
                                                <button type="submit" class="btn btn-primary" name="updateuser">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal Hapus -->
                             <div class="modal fade" id="delete<?= $data['id']; ?>">
                              <div class="modal-dialog">
                              <div class="modal-content">
                              <div class="modal-header">
                               <h4>Hapus Admin</h4>
                               <button type="button" class="close" data-dismiss="modal">&times;</button>
                               </div>
                                <form method="post">
                                 <div class="modal-body">Anda yakin ingin menghapus <?= $data['email']; ?>?
                                <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                <br><br>
                                <button type="submit" class="btn btn-danger" name="hapususer">Hapus</button>
                              </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah Admin -->
<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Tambah Admin</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="config.php" method="post">
                <div class="modal-body">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                    <label>Role</label>
                    <input type="text" name="role" class="form-control" required>
                    <br>
                    <button type="submit" class="btn btn-primary" name="tambahuser">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
    </main>