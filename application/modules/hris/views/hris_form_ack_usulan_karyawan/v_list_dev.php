<div style="display:flex; flex-wrap:wrap; gap:15px;">

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
                    $color = '#F7F599';
                } else if ( $l['status'] == 2 ){
                    $status = 'Acknowledge';
                    $color = '#AAF799';
                } else if ( $l['status'] == 3 ){
                    $status = 'Approve';
                    $color = '#2283D6';
                } else if ( $l['status'] == 6 ){
                    $status = 'Done';
                    $color = '#C7C7C7';
                } else if ( $l['status'] == 4 || $l['status'] == 5 ){ 
                    $status = $l['status'] == 5 ? 'Reject CEO' :'Reject HRD';
                    $color = '#F76363';
                } else {
                    $status = '-';
                    $color = '#eee';
                }
            ?>

            <div 
                style="
                    flex:1 1 300px;
                    max-width:100%;
                    border:2px solid #151b26;
                    border-radius:8px;
                    padding:10px;
                    display:flex;
                    flex-direction:column;
                    gap:10px;
                "
            >

                <!-- HEADER -->
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <div style="color:grey; font-size:12px;">Nama Pengusul</div>
                        <div style="font-size:18px; font-weight:bold;"><?php echo $l['nama'] ?></div>
                    </div>

                    <div style="
                        background:<?php echo $color;?>;
                        padding:5px 10px;
                        border-radius:5px;
                        font-size:12px;
                        text-align:center;
                        white-space:nowrap;
                    ">
                        <?php echo $status ?>
                    </div>
                </div>

                <!-- CONTENT -->
                <div style="
                    display:flex;
                    flex-wrap:wrap;
                    gap:15px;
                ">

                    <div>
                        <div style="color:grey; font-size:12px;">Tanggal</div>
                        <div style="font-size:13px;">
                            <?php echo tglIndonesia($l['tgl_pengusulan'], '-', ' ') ?>
                        </div>
                    </div>

                    <div>
                        <div style="color:grey; font-size:12px;">Posisi</div>
                        <div style="font-size:13px;"><?php echo $l['posisi'] ?></div>
                    </div>

                    <div>
                        <div style="color:grey; font-size:12px;">Unit</div>
                        <div style="font-size:13px;">
                            <?php echo $unit[$l['unit']]['nama'] ?>
                        </div>
                    </div>

                </div>

            </div>

        <?php } ?>
    <?php } else { ?>
        <div style="width:100%; text-align:center;">Tidak ada data</div>
    <?php } ?>

</div>