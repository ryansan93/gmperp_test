<table class="table table-bordered table-hover" id="table-pembiayaan">
    <thead>
        <tr>
            <th class="text-center" style="width:50px;">No</th>
            <th class="text-center">Kode Pembiayaan</th>
            <th>Jenis Pembiayaan</th>
            <th class="text-center" style="width:130px;">Jumlah Aset</th>
            <th class="text-center" style="width:110px;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)) : ?>
            <?php foreach ($list as $index => $row) : ?>
                <?php $isUsed = !empty($row['jumlah_aset']); ?>
                <tr>
                    <td class="text-center"><?php echo $index + 1; ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['kode_pembiayaan']); ?></td>
                    <td><?php echo htmlspecialchars($row['nama_pembiayaan']); ?></td>
                    <td class="text-center"><?php echo (int) $row['jumlah_aset']; ?></td>
                    <td class="text-center">
                        <?php if (!empty($akses['a_edit'])) : ?>
                            <button type="button" class="btn btn-sm btn-warning" data-id="<?php echo (int) $row['id']; ?>" onclick="mp.edit_form(this)" title="Edit">
                                <i class="fa fa-pencil"></i>
                            </button>
                        <?php endif; ?>
                        <?php if (!empty($akses['a_delete'])) : ?>
                            <button type="button" class="btn btn-sm btn-danger" data-id="<?php echo (int) $row['id']; ?>" onclick="mp.delete_data(this)" title="Hapus" <?php echo $isUsed ? 'disabled' : ''; ?>>
                                <i class="fa fa-trash"></i>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr><td colspan="5" class="text-center">Belum ada jenis pembiayaan.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
