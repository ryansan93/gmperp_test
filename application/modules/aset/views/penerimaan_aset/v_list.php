<?php if ( !empty($list) ) : ?>
    <?php
        $grouped = [];
        foreach ( $list as $row ) {
            $kategori = isset($row['nama_kategori']) && trim($row['nama_kategori']) !== '' ? trim($row['nama_kategori']) : 'Tanpa Kategori';
            $grouped[$kategori][] = $row;
        }
    ?>

    <?php $groupIndex = 0; ?>
    <?php foreach ( $grouped as $kategori => $rows ) : ?>
        <?php $groupId = 'penerimaan-category-' . $groupIndex++; ?>
        <tr class="penerimaan-category-header">
            <td colspan="10">
                <button type="button" class="penerimaan-category-toggle" data-group-id="<?php echo $groupId; ?>" aria-expanded="true">
                    <i class="fa fa-folder-open penerimaan-category-folder" aria-hidden="true"></i>
                    <span><?php echo htmlspecialchars($kategori, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="badge"><?php echo count($rows); ?> Aset</span>
                    <i class="fa fa-chevron-down penerimaan-category-chevron" aria-hidden="true"></i>
                </button>
            </td>
        </tr>

        <?php foreach ( $rows as $key => $row ) : ?>
            <tr class="tr_loop penerimaan-category-item" data-group-id="<?php echo $groupId; ?>">

                <td class="text-center"><?php echo $key + 1; ?></td>
                
                <td class="text-center">
                    <?php echo isset($row['kode_penerimaan']) ? $row['kode_penerimaan'] : '-'; ?>
                </td>

                <td class="text-center">
                    <a href="javascript:void(0);" onclick="pa.show_detail(this)" data-id="<?php echo $row['id']; ?>" title="Lihat Detail">
                        <strong><?php echo isset($row['kode_aset']) ? $row['kode_aset'] : '-'; ?></strong>
                    </a>
                </td>

                <td class="text-left"><?php echo isset($row['nama_kategori']) ? $row['nama_kategori'] : '-'; ?></td>

                <td>
                    <div style="max-width:250px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?php echo isset($row['deskripsi_asset']) ? htmlspecialchars($row['deskripsi_asset']) : ''; ?>">
                        <?php echo isset($row['deskripsi_aset']) ? $row['deskripsi_aset'] : '-'; ?>
                    </div>
                </td>

                <td class="text-center">
                    <?php echo isset($row['tgl_perolehan']) ? tglIndonesia($row['tgl_perolehan'], '-', ' ') : '-'; ?>
                </td>

                <td class="text-center">
                    <?php echo isset($row['tgl_penerimaan']) ? tglIndonesia($row['tgl_penerimaan'], '-', ' ') : '-'; ?>
                </td>

                <td class="text-center">
                    <?php echo isset($row['nama_pic']) ? ucwords(strtolower($row['nama_pic'])) : '-'; ?>
                </td>

                <td class="text-center">
                    <?php echo isset($row['nama_unit']) ? $row['nama_unit'] : '-'; ?>
                </td>

                <td class="text-center" style="width: 120px; white-space: nowrap;">
                    <?php if (isset($akses['a_edit']) && $akses['a_edit'] == 1): ?>
                        <button type="button" class="btn btn-sm btn-warning" data-id="<?php echo $row['id']; ?>" onclick="pa.edit_form(this)" title="Edit">
                            <i class="fa fa-pencil"></i>
                        </button>
                    <?php endif; ?>

                    <?php if (isset($akses['a_delete']) && $akses['a_delete'] == 1): ?>
                        <?php if (!empty($row['is_locked'])): ?>
                            <button type="button" class="btn btn-sm btn-danger" disabled title="Aset sudah terkunci oleh penyusutan">
                                <i class="fa fa-trash"></i>
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-sm btn-danger" data-id="<?php echo $row['id']; ?>" onclick="pa.delete_data(this)" title="Hapus">
                                <i class="fa fa-trash"></i>
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>

<?php else : ?>
    <tr>
        <td colspan="10" class="text-center text-muted" style="padding: 20px;">
            <i class="fa fa-inbox" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
            Tidak ada data penerimaan tersedia
        </td>
    </tr>
<?php endif; ?>