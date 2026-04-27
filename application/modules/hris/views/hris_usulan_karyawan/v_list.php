<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center">Yang Mengajukan</th>
            <th class="text-center">Jabatan</th>
            <th class="text-center">Posisi</th>
            <th class="text-center">Jumlah Kandidat</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)) { ?>
            <?php foreach($list as $l){?>
                <tr>
                    <td class="text-center"><?php echo $l['nama'] ?></td>
                    <td class="text-center"><?php echo $l['jabatan'] ?></td>
                    <td class="text-center"><?php echo $l['posisi'] ?></td>
                    <td class="text-center"><?php echo $l['jumlah'] ?></td>
                    <td class="text-center">
                        <button id="<?php echo $l['id'] ?>" class="btn btn-warning" onclick="hf.edit(this, event)"><i style="margin-right:5px;" class="fa fa-edit" aria-hidden="true"></i> Edit</button>
                        <button id_data="<?php echo $l['id'] ?>" class="btn btn-danger" onclick="hf.delete(this, event)"><i style="margin-right:5px;" class="fa fa-trash" aria-hidden="true"></i> Hapus</button>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>

        <tr>
            <td colspan="7" style="text-align:center;">Tidak ada data</td>
        </tr>
        <?php } ?>


    </tbody>
</table>