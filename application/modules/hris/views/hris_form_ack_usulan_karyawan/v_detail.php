<?php $no = 1; ?>
<?php foreach($data_kandidat as $dt) { ?>

<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>Kandidat <?php echo $no++ ?></b>
        </div>
    </legend>

    <!-- NAMA -->
    <div style="display:flex; gap:10px; padding:5px;">
        <span style="width:150px">Nama Kandidat</span>
        <span>:</span>
        <span><?php echo $dt['nama']; ?></span>
    </div>

    <!-- BUTTON -->
    <div style="display:flex; gap:10px; padding:5px;">
        <span style="width:150px">Lihat Bio Data</span>
        <span>:</span>
        <button class="btn btn-sm btn-secondary" onclick="fr.show_biodata(this, event)">
            Show Bio Data <?php echo $dt['id_kandidat'] ?>
        </button>
    </div>

    <!-- BIODATA -->
    <div class="biodata" style="display:none; padding:10px; margin-top:10px;">
        <?php $bio = $biodata[$dt['id_kandidat']] ?? null; ?>

        <?php if ($bio){?>
            <?php 
                $standalone_not_show = [];

                foreach ($bio['grouped'] as $group => $items) { 
                    foreach ($items as $item) { 
                        if (!empty($item['parent_column'])) {
                            $standalone_not_show[] = $item['parent_column'];
                        } 
                    }

                }

                $label_disabled = array_unique($standalone_not_show);

            ?>



            <?php if (!empty($bio['standalone'])) { ?>
                <div style="border:1px solid #ccc; border-radius:5px; padding:10px; margin-bottom:15px;">
                    <div style="font-weight:bold; margin-bottom:10px;">
                        Data Pribadi
                    </div>
                   <?php foreach ($bio['standalone'] as $index => $item) { ?>

                        <?php if (!in_array($item['label'], $standalone_not_show)) { ?>

                            <div style="display:flex;">
                                <div style="width:200px;"><?= $item['label'] ?></div>
                                <div style="width:20px;">:</div>
                                <div style="flex:1;"><?= $item['value'] ?></div>
                            </div>

                        <?php } ?>

                    <?php } ?>
                </div>
            <?php } ?>

            <?php foreach ($bio['grouped'] as $group => $items) { ?>
                <div style="border:1px solid #ccc; border-radius:5px; padding:10px; margin-bottom:15px;">
                    <div style="font-weight:bold; ">
                        <?php foreach ($bio['standalone'] as $item){
                            if ($item['label'] == $group ) {
                                echo '<span style="width:200px;">'.$group . '</span> : ' . $item['value'];
                            }  
                         } ?>
                    </div>
    
                    <?php foreach ($items as $item) { ?>
                        <div style="display:flex;">
                            <div style="width:200px;"><?= $item['label'] ?></div>
                            <div style="width:20px;">:</div>
                            <div style="flex:1;"><?= $item['value'] ?></div>
                        </div>
                    <?php } ?>

                </div>

            <?php } ?>

        <?php } else { ?>
            <i>Data biodata tidak tersedia</i>
        <?php } ?>

    </div>

</fieldset>

<?php } ?>