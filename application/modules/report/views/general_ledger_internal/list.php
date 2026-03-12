<?php if (!empty($data)) { ?>

<?php
$tot_saldo_awal = 0;
$tot_debet = 0;
$tot_kredit = 0;
$tot_saldo_akhir = 0;
?>

<?php foreach ($data as $noreg => $coas) { ?>

    <?php foreach ($coas as $coa => $rows) { ?>

        <?php foreach ($rows as $value) { ?>

        <tr class="cursor-p data"
            onclick="gl.formDetail(this)"
            data-periode="<?php echo $periode; ?>"
            title="Klik untuk melihat detail">

            <td class="text-left no_coa"><?php echo strtoupper($value['no_coa']); ?></td>
            <td class="text-left unit_tr"><?php echo strtoupper($value['unit']); ?></td>
            <td class="text-left nama_coa"><?php echo strtoupper($value['nama_coa']); ?></td>
            <td class="text-center noreg"><?php echo strtoupper($value['noreg']); ?></td>
            <td class="text-center"><?php echo strtoupper($value['nama_mitra']); ?></td>

            <td class="text-right">
                <?php echo ($value['saldo_awal'] >= 0) ? angkaDecimal($value['saldo_awal']) : '('.angkaDecimal(abs($value['saldo_awal'])).')'; ?>
            </td>

            <td class="text-right">
                <?php echo ($value['debet'] >= 0) ? angkaDecimal($value['debet']) : '('.angkaDecimal(abs($value['debet'])).')'; ?>
            </td>

            <td class="text-right">
                <?php echo ($value['kredit'] >= 0) ? angkaDecimal($value['kredit']) : '('.angkaDecimal(abs($value['kredit'])).')'; ?>
            </td>

            <?php $saldo_akhir = $value['saldo_awal'] + $value['debet'] + $value['kredit']; ?>

            <td class="text-right"><?php echo angkaDecimal($saldo_akhir) ?></td>

        </tr>

        <?php
            $tot_saldo_awal += $value['saldo_awal'];
            $tot_debet += $value['debet'];
            $tot_kredit += $value['kredit'];
            $tot_saldo_akhir += $saldo_akhir;
        ?>

        <?php } ?>

    <?php } ?>

<?php } ?>

<tr>
    <td class="text-right" colspan="5"><b>TOTAL</b></td>

    <td class="text-right"><b>
<?php echo ($tot_saldo_awal >= 0) ? angkaDecimal($tot_saldo_awal) : '('.angkaDecimal(abs($tot_saldo_awal)).')'; ?>
    </b></td>

    <td class="text-right"><b>
        <?php echo ($tot_debet >= 0) ? angkaDecimal($tot_debet) : '('.angkaDecimal(abs($tot_debet)).')'; ?>
    </b></td>

    <td class="text-right"><b>
        <?php echo ($tot_kredit >= 0) ? angkaDecimal($tot_kredit) : '('.angkaDecimal(abs($tot_kredit)).')'; ?>
    </b></td>

    <td class="text-right"><b>
        <?php echo ($tot_saldo_akhir >= 0) ? angkaDecimal($tot_saldo_akhir) : '('.angkaDecimal(abs($tot_saldo_akhir)).')'; ?>
    </b></td>
</tr>

<?php } else { ?>

<tr>
    <td colspan="9">Data tidak ditemukan.</td>
</tr>

<?php } ?>