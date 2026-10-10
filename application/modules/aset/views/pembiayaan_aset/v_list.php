<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th class="text-center" style="width:50px;">No</th>
            <th>Kode Pembiayaan</th>
            <th>Aset</th>
            <th>Kategori</th>
            <th>Unit</th>
            <th class="text-right">Harga Beli</th>
            <th>Supplier</th>
            <th>Leasing</th>
            <th class="text-center" style="width:110px;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)) : ?>
            <?php foreach ($list as $index => $row) : ?>
                <tr>
                    <td class="text-center"><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($row['kode_pembiayaan']); ?></td>
                    <td><?php echo htmlspecialchars(($row['kode_aset'] ?? '-') . ' - ' . ($row['deskripsi_aset'] ?? '')); ?></td>
                    <td><?php echo htmlspecialchars($row['kategori_name'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['unit_pengguna'] ?? '-'); ?></td>
                    <td class="text-right"><?php echo number_format((float) ($row['nilai_perolehan'] ?? 0), 0, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars(($row['supplier_nomor'] ?? $row['id_supplier']) . ' - ' . ($row['supplier_nama'] ?? '')); ?></td>
                    <td><?php echo !empty($row['id_leasing']) ? htmlspecialchars(($row['leasing_nomor'] ?? $row['id_leasing']) . ' - ' . ($row['leasing_nama'] ?? '')) : '-'; ?></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-info" data-id="<?php echo (int) $row['id']; ?>" data-kode-jenis-pembiayaan="<?php echo htmlspecialchars($row['kode_jenis_pembiayaan'] ?? ''); ?>" onclick="pba.process_form(this)"title="Proses Pembiayaan">
                            <i class="fa fa-cogs"></i>
                            Proses Pembiayaan
                        </button>
                        <?php if (!empty($akses['a_edit'])) : ?>
                            <button type="button" style="color: white;" class="btn btn-sm btn-warning" <?php echo $row['sudah_diproses'] == 1 ? 'disabled' : ''  ?> data-id="<?php echo (int) $row['id']; ?>" onclick="pba.edit_form(this)" title="Ubah">
                                <i class="fa fa-pencil"></i>
                            </button>
                        <?php endif; ?>
                        <?php if (!empty($akses['a_delete'])) : ?>
                            <button type="button" class="btn btn-sm btn-danger" <?php echo $row['sudah_diproses'] == 1 ? 'disabled' : ''  ?> data-id="<?php echo (int) $row['id']; ?>" onclick="pba.delete_data(this)" title="Hapus">
                                <i class="fa fa-trash"></i>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr><td colspan="9" class="text-center">Belum ada data pembiayaan aset.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
