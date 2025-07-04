
<div class="modal fade" id="editModal<?= $data['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Bidang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id_bidang" value="<?= $data['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama Bidang</label>
                        <input type="text" name="bidang" class="form-control" required value="<?= htmlspecialchars($data['nama_bidang']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Induk Bidang</label>
                        <select name="parent_id" class="form-select">
                            <option value="0">-- Jadikan sebagai Bidang Utama --</option>
                            <?php
                            $query_parent_edit = mysqli_query($config, "SELECT id, nama_bidang FROM bidang WHERE parent_id = 0 AND id != '{$data['id']}' ORDER BY nama_bidang ASC");
                            while ($parent = mysqli_fetch_assoc($query_parent_edit)) {
                                $selected = ($parent['id'] == $data['parent_id']) ? 'selected' : '';
                                echo "<option value='{$parent['id']}' $selected>" . htmlspecialchars($parent['nama_bidang']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="5"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ganti Gambar</label>
                        <input class="form-control" type="file" name="file">
                        <input type="hidden" name="gambarLama" value="<?= $data['gambar'] ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" name="updateBidang">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

