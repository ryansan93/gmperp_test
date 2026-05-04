<?php 
    $id_karyawan = $_GET['id'] ?? null;
    $data = $biodata[$id_karyawan] ?? [];

    $standalone = $data['standalone'] ?? [];
    $grouped    = $data['grouped'] ?? [];


    $standaloneMap = [];
    foreach ($standalone as $item) {
        $standaloneMap[$item['label']] = $item['value'];
    }

    $byTitle = [];

    foreach ($standalone as $item) {
        $title = $item['title'] ?? 'Lainnya';
        $byTitle[$title]['standalone'][] = $item;
    }


    foreach ($grouped as $group => $items) {
        $title = $items[0]['title'] ?? 'Lainnya';
        $byTitle[$title]['grouped'][$group] = $items;
    }

    // cetak_r($byTitle,1);
?>

<?php if (!empty($byTitle)) { ?>

    <?php foreach ($byTitle as $title => $section) { ?>

        <fieldset style="margin-bottom:15px;">
            <legend><b><?= htmlspecialchars($title) ?></b></legend>

            <?php foreach ($section['standalone'] ?? [] as $item) { ?>

                <?php if (!isset($grouped[$item['label']])) { ?>

                    <div style="display:flex; margin-bottom:4px;">
                        <div style="width:200px;">
                            <?= htmlspecialchars($item['label']) ?>
                        </div>
                        <div style="width:20px;">:</div>
                        <div style="flex:1;">
                            <?= htmlspecialchars($item['value']) ?>
                        </div>
                    </div>

                <?php } ?>

            <?php } ?>

            <?php foreach ($section['grouped'] ?? [] as $group => $items) { ?>

                <div style="border:1px solid #ccc; border-radius:5px; padding:10px; margin-top:10px;">
                    
                    <div style="font-weight:bold; margin-bottom:8px;">
                        <?= htmlspecialchars($group) ?> :
                        <?= htmlspecialchars($standaloneMap[$group] ?? '-') ?>
                    </div>

                    <?php foreach ($items as $item) { ?>

                        <div style="display:flex; margin-bottom:4px;">
                            <div style="width:200px;">
                                <?= htmlspecialchars($item['label']) ?>
                            </div>
                            <div style="width:20px;">:</div>
                            <div style="flex:1;">
                                <?= htmlspecialchars($item['value']) ?>
                            </div>
                        </div>

                    <?php } ?>

                </div>

            <?php } ?>

        </fieldset>

    <?php } ?>

<?php } else { ?>
    <i>Data biodata tidak tersedia</i>
<?php } ?>

<button class="btn btn-secondary" onclick="window.location.href='<?php echo base_url();?>hris/HrisKandidatBaru/'">Kembali</button>