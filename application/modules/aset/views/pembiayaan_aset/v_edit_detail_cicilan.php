<?php
$h = $header ?? [];
$details = $detail ?? [];
$harga_beli = $harga_beli ?? 0;

$rp = function($num) {
    return number_format((float) $num, 2, ',', '.');
};

?>

<div class="row" style="margin-bottom: 10px;">
    <div class="col-xs-6">
        <div class="form-group" style="margin-bottom: 5px;">
            <label>Tenor (bulan) <span class="text-danger">*</span></label>
            <input type="number" id="edit_tenor" class="form-control input-sm" onchange="pba.hitung_ulang_cicilan()"
                   value="<?= (int) ($h['tenor_bulan'] ?? 0) ?>" min="1" max="600">
        </div>
    </div>
    <div class="col-xs-6">
        <div class="form-group" style="margin-bottom: 5px;">
            <label>Bunga flat (% per tahun) <span class="text-danger">*</span></label>
            <input type="number" id="edit_bunga" class="form-control input-sm" onchange="pba.hitung_ulang_cicilan()"
                   value="<?= $h['bunga_flat_persen'] ?? '0' ?>" min="0" max="100" step="0.01">
        </div>
    </div>
</div>

<div style="margin-bottom: 10px;">
    <h3 style="margin: 0 0 3px 0;"><span id="edit_angsuran_bulanan"><?= $rp($h['angsuran_per_bulan'] ?? 0) ?></span> / bulan</h3>
    <p class="text-muted" style="margin: 0; font-size: 12px;">Angsuran per bulan, <span id="edit_tenor_label"><?= $h['tenor_bulan'] ?? '-' ?></span> bulan</p>
</div>

<div class="table-responsive" style="margin-bottom: 10px;">
    <table class="table table-condensed" style="margin-bottom: 0; font-size: 13px;">
        <tr>
            <td>Harga Beli (Aset)</td>
            <td class="text-right"><?= $rp($harga_beli) ?></td>
        </tr>
        <tr style="font-weight: bold;">
            <td>Pokok hutang</td>
            <td class="text-right" id="edit_pokok_hutang"><?= $rp($h['pokok_hutang'] ?? 0) ?></td>
        </tr>
        <tr>
            <td>Bunga flat</td>
            <td class="text-right" id="edit_total_bunga"><?= $rp($h['total_bunga'] ?? 0) ?></td>
        </tr>
        <tr style="font-weight: bold;">
            <td>Total A/R</td>
            <td class="text-right" id="edit_total_ar"><?= $rp($h['total_ar'] ?? 0) ?></td>
        </tr>
    </table>
</div>

<div class="table-responsive" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd;">
    <table class="table table-condensed table-bordered table-striped" style="margin-bottom: 0; font-size: 13px;">
        <thead>
            <tr style="background-color: #f5f5f5;">
                <th class="text-center" style="width: 40px;">Ke</th>
                <th>Jatuh tempo</th>
                <th class="text-center" style="width: 80px;">Jenis</th>
                <th class="text-right" style="width: 110px;">Pokok</th>
                <th class="text-right" style="width: 110px;">Bunga</th>
                <th class="text-right" style="width: 110px;">Cicilan</th>
                <th class="text-right" style="width: 110px;">Saldo</th>
                <th class="text-center" style="width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody id="edit_detail_cicilan_body">
            <?php if (!empty($details)) : ?>
                <?php 
                    // Hitung Saldo Awal dari Total A/R
                    $saldo = floatval($h['total_ar'] ?? 0);
                ?>
                
                <!-- Baris 0: Saldo Awal -->
                <tr class="info">
                    <td class="text-center">0</td>
                    <td>-</td>
                    <td class="text-center">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right"><?= $rp($saldo) ?></td>
                    <td class="text-center">-</td>
                </tr>

                <?php foreach ($details as $d) : ?>
                    <?php 
                        $pokok = floatval($d['pokok'] ?? 0);
                        $bunga = floatval($d['bunga'] ?? 0);
                        $nominal = floatval($d['nominal'] ?? 0);
                        
                        // Jika kolom pokok/bunga tidak ada, fallback
                        if ($pokok == 0 && $bunga == 0) {
                            $pokok = $nominal;
                            $bunga = 0;
                        }
                        
                        $saldo -= $pokok;
                        if ($saldo < 0) $saldo = 0;
                        
                        $status = (int) ($d['status'] ?? 0) === 1 ? 'Lunas' : 'Pending';
                        $label = (int) ($d['status'] ?? 0) === 1 ? 'label-success' : 'label-warning';
                        $jenis = htmlspecialchars($d['jenis_cicilan'] ?? 'Cicilan');
                        $labelJenis = (strtoupper($jenis) === 'DP') ? 'label-info' : 'label-default';
                    ?>
                    <tr>
                        <td class="text-center"><?= (int) $d['angsuran_ke'] ?></td>
                        <td><?= htmlspecialchars($d['jatuh_tempo'] ?? '-') ?></td>
                        <td class="text-center">
                            <span class="label <?= $labelJenis ?>"><?= $jenis ?></span>
                        </td>
                        <td class="text-right"><?= $rp($pokok) ?></td>
                        <td class="text-right"><?= $rp($bunga) ?></td>
                        <td class="text-right"><?= $rp($nominal) ?></td>
                        <td class="text-right"><?= $rp($saldo) ?></td>
                        <td class="text-center">
                            <span class="label <?= $label ?>"><?= htmlspecialchars($status) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="8" class="text-center text-muted">Tidak ada data detail.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<p id="edit_selisih_pembulatan" class="text-muted" style="margin-top: 8px; font-size: 12px; margin-bottom: 0;">
    <?php if (!empty($h['selisih_pembulatan'])) : ?>
        Selisih pembulatan terhadap A/R: <?= $rp($h['selisih_pembulatan']) ?>.
    <?php endif; ?>
</p>

<div class="row" style="margin-top: 12px;">
    <div class="col-xs-12 text-right">
        <button type="button" class="btn btn-primary btn-sm" onclick="pba.simpan_perubahan_cicilan()">
            <i class="fa fa-save"></i> Simpan Perubahan
        </button>
    </div>
</div>