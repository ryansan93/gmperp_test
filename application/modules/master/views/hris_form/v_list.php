<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center" >No.</th>
            <th class="text-center">Title</th>
            <th class="text-center">Keterangan</th>
            <th class="text-center">Kategori</th>
            <th class="text-center">Urutan</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php if (!empty($list)) { ?>
            <?php foreach($list as $l){?>
                <tr>
                    <td class="text-center" ><?php echo $no++ ?> </td>
                    <td><?php echo $l['title'] ?></td>
                    <td><?php echo $l['keterangan'] ?></td>
                    <td><?php echo $l['kategori'] ?></td>
                    <td class="text-center"><?php echo $l['urutan'] ?></td>
                    <td class="text-center">
                        <button id="<?php echo $l['id'] ?>" class="btn btn-primary" onclick="hf.show_detail(this, event)"><i style="margin-right:5px;" class="fa fa-file" aria-hidden="true"></i> Show Detail</button>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>

        <tr>
            <td colspan="6" style="text-align:center;">Tidak ada data</td>
        </tr>
        <?php } ?>


    </tbody>
</table>