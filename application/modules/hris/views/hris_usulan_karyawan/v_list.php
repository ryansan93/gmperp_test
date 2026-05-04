<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center">Yang Mengajukan</th>
            <th class="text-center">Jabatan</th>
            <th class="text-center">Posisi</th>
            <th class="text-center">Jumlah Butuh</th>
            <th class="text-center">Unit</th>
            <th class="text-center">Status Usulan</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)) { ?>
            <?php foreach($list as $l){?>
                <?php 
                    $status = '';
                    $color = '';
                    if( $l['status'] == 1 ){
                        $status = 'Draft';
                        $color = 'border: 2px solid #f7f578;';
                    } else if ( $l['status'] == 2 ){
                        $status = 'Acknowledge';
                        $color = 'border: 2px solid #AAF799;';
                    } else if ( $l['status'] == 3 ){
                        $status = 'Approved';
                        $color = 'border: 2px solid #2283D6;';
                    } else if ( $l['status'] == 6 ){
                        $status = 'Done';
                        $color = 'border: 2px solid #C7C7C7;';
                    } else if ( $l['status'] == 4 || $l['status'] == 5 ){ 
                        $status = $l['status'] == 5 ? 'Reject CEO' :'Reject HRD';
                        $color = 'border: 2px solid #F76363;';
                    } else {
                        $status = ' - ';
                        $color = ' ';
                    }
                ?>
       

                <tr class="data-row">
                    <td class="text-left"><?php echo ucwords(strtolower($l['nama'])) ?></td>
                    <td class="text-center"><?php echo ucwords(strtolower($l['jabatan'])) ?></td>
                    <td class="text-center"><?php echo $l['nama_posisi'] ?></td>
                    <td class="text-center"><?php echo $l['jumlah'] ?> Orang</td>
                    <td class="text-center"><?php echo $unit[$l['unit']]['nama'] ?></td>
                    <td class="text-center">
                        <div style="font-weight:600; padding:2px; border-radius:10px; text-align:center; <?php echo $color; ?>"><?php echo $status; ?> </div>
                    </td>
                    <td class="text-center">
                        <!-- <button class="btn btn-secondary" onclick="hf.show_detail(this, event);"><i class="fa fa-file"></i>  Show Detail</button> -->

                        <?php if( $l['status'] == 1 ){ ?>
                            <button id="<?php echo $l['id'] ?>" class="btn btn-warning" onclick="hf.edit(this, event)"><i style="margin-right:5px;" class="fa fa-edit" aria-hidden="true"></i> Edit</button>
                            <button id_data="<?php echo $l['id'] ?>" class="btn btn-danger" onclick="hf.delete(this, event)"><i style="margin-right:5px;" class="fa fa-trash" aria-hidden="true"></i> Hapus</button>
                        <?php } else {?>
                            <span>-</span>
                        <?php } ?>

                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>

        <tr>
            <td colspan="7" style="text-align:center;">Tidak ada data</td>
        </tr>
        <?php } ?>

        <tr class="no-data" style="display:none;">
            <td colspan="7" style="text-align:center;">Tidak ada data</td>
        </tr>


    </tbody>
</table>