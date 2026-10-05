<?php if ( !empty($list) ) : ?>
    <?php
        $grouped = [];
        foreach ( $list as $row ) {
            $supplier = isset($row['nama_supplier']) && trim($row['nama_supplier']) !== '' ? trim($row['nama_supplier']) : '-';
            $grouped[$supplier][] = $row;
        }
    ?>

    <?php $groupIndex = 0; ?>
    <?php foreach ( $grouped as $supplier => $rows ) : ?>
        <?php $groupId = 'sewa-supplier-' . $groupIndex++; ?>
        <tr class="sewa-supplier-header">
            <td colspan="11">
                <button type="button" class="sewa-supplier-toggle" data-group-id="<?php echo $groupId; ?>" aria-expanded="true">
                    <i class="fa fa-folder-open sewa-supplier-folder" aria-hidden="true"></i>
                    <span><?php echo htmlspecialchars($supplier, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="badge"><?php echo count($rows); ?> Sewa</span>
                    <i class="fa fa-chevron-down sewa-supplier-chevron" aria-hidden="true"></i>
                </button>
            </td>
        </tr>

        <?php foreach ( $rows as $key => $row ) : ?>
            <tr class="tr_loop sewa-supplier-item" data-group-id="<?php echo $groupId; ?>">
                <td class="text-center"><?php echo $key + 1; ?></td>
                <td class="text-center"> <a href="javascript:void(0);" onclick="ms.show_detail(this)" data-id="<?php echo $row['id']; ?>"><?php echo isset($row['no_sewa']) ? $row['no_sewa'] : '-'; ?></a></td>
                <td class="text-center"><?php echo isset($row['no_kontrak']) ? $row['no_kontrak'] : '-'; ?></td>
                <td class="text-center"><?php echo isset($row['nama_sewa']) ? $row['nama_sewa'] : '-'; ?></td>
                <td class="text-center"><?php echo isset($row['nama_jenis_sewa']) ? $row['nama_jenis_sewa'] : $row['nama_sewa'] ; ?></td>
                <td class="text-center"><?php echo isset($row['nama_unit']) ? ucwords(strtolower($row['nama_unit'])) : '-'; ?></td>
                <td class="text-center"><?php echo isset($row['jumlah_bulan']) ? $row['jumlah_bulan'] : '-'; ?></td>
                <td class="text-center"><?php echo isset($row['jumlah_siklus']) ? $row['jumlah_siklus'] : '-'; ?></td>
                <td class="text-center"><?php echo isset($row['tanggal_mulai']) ? tglIndonesia($row['tanggal_mulai'], '-', ' ') : '-'; ?></td>
                <td class="text-right">Rp. <?php echo isset($row['nominal_sewa']) ? number_format($row['nominal_sewa'], 0, ',', '.') : '0'; ?></td>
                <td class="text-center" style="width:70px; white-space:nowrap;">
                    <button type="button" class="btn btn-sm btn-warning" data-id="<?php echo $row['id']; ?>" onclick="ms.edit_form(this)">
                        <i class="fa fa-pencil"></i>
                    </button>
                    <?php if ( $akses['a_delete'] == 1 ) { ?>
                        <button type="button" class="btn btn-sm btn-danger" data-id="<?php echo $row['id']; ?>" onclick="ms.delete_data(this)">
                            <i class="fa fa-trash"></i>
                        </button>
                    <?php } ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php else : ?>
    <tr>
        <td colspan="11" class="text-center">Tidak ada data tersedia</td>
    </tr>
<?php endif; ?>
