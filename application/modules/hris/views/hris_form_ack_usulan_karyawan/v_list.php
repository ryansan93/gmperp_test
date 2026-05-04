<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center"  style="white-space:nowrap">Nama Pengusul</th>
            <th class="text-center"  style="white-space:nowrap">Tgl. Pengusulan</th>
            <th class="text-center"  style="white-space:nowrap">Posisi</th>
            <th class="text-center"  style="white-space:nowrap">Jumlah</th>
            <th class="text-center"  style="white-space:nowrap">Unit</th>
            <th class="text-center"  style="white-space:nowrap">Status</th>
            <th class="text-center"  style="white-space:nowrap">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)) { ?>
            <?php 
                usort($list, function($a, $b) {
                    $order = [1, 2, 3, 4];

                    return array_search($a['status'], $order) - array_search($b['status'], $order);
                });
            ?>
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
                <?php 
                    $key = "secretkey";
                    $plaintext = $l['id'];
                ?>

                <tr keterangan_ceo="<?php echo $l['keterangan_ceo'] ?>" keterangan_hrd="<?php echo $l['keterangan_hrd'] ?>" encrypted="<?php echo $encrypted = openssl_encrypt($plaintext, "AES-128-ECB", $key); ?>" document="<?php echo $l['document'] ?>" id_data="<?php echo $l['id'] ?>" status_key="<?php echo $l['status'] ?>" status="<?php echo $status ?>" unit="<?php echo $unit[$l['unit']]['nama'] ?>" jumlah="<?php echo $l['jumlah'] ?>" alasan="<?php echo $l['alasan'] ?>" posisi="<?php echo $l['posisi'] ?>" nama_pengusul="<?php echo $l['nama'] ?>" tgl_pengusul="<?php echo tglIndonesia($l['tgl_pengusulan'], '-', ' ') ?>">
                    <td class="text-center" style="white-space:nowrap"><?php echo $l['nama'] ?></td>
                    <td class="text-center" style="white-space:nowrap"><?php echo tglIndonesia($l['tgl_pengusulan'], '-', ' ') ?></td>
                    <td class="text-center" style="white-space:nowrap"><?php echo $l['nama_posisi'] ?></td>
                    <td class="text-center" style="white-space:nowrap"><?php echo $l['jumlah'] ?></td>
                    <td class="text-center" style="white-space:nowrap"><?php echo $unit[$l['unit']]['nama'] ?></td>

                    <td class="text-center"  style="white-space:nowrap">
                        <div style="font-weight:600; padding:5px; border-radius:10px; text-align:center; <?php echo $color; ?>"><?php echo $status; ?> </div>
                    </td>
                    <td class="text-center"  style="white-space:nowrap">

                        <button class="btn btn-secondary" onclick="fr.show_usulan(this, event)"><i class="fa fa-file" aria-hidden="true"></i> Detail Usulan</button>
                        <!-- < ?php if( $l['status'] == 1 ){ ?>
                            <button class="btn btn-success" id_data="< ?php echo $l['id'] ?>" onclick="fr.keputusan(this, event, 2)">Acknowledge</button>
                            <button class="btn btn-danger" id_data="< ?php echo $l['id'] ?>" onclick="fr.keputusan(this, event, 4)">Reject</button>
                        < ?php } else if ( $l['status'] == 2 ) { ?>
                            <button class="btn btn-secondary" id_data="< ?php echo $l['id'] ?>" onclick="fr.keputusan(this, event, 3)">Done</button>
                        < ?php } else { ?>
                            -
                        < ?php } ?> -->
                        <!-- <button id="< ?php echo $l['id'] ?>" class="btn btn-primary" onclick="fr.show_detail(this, event)"><i style="margin-right:5px;" class="fa fa-file" aria-hidden="true"></i> Show Candidate</button> -->
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