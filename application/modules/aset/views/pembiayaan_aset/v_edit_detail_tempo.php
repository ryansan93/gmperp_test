<?php
$h = $header ?? [];
$details = $detail ?? [];
$tdpTotal = $tdp_total ?? 0;
$hargaBeli = $harga_beli ?? 0;
$supplierNama = $supplier_nama ?? '-';

$biayaLain = (float) ($h['biaya_lain'] ?? 0);
$totalPembelian = $hargaBeli + $biayaLain;
$sisa = $totalPembelian - $tdpTotal;

$rp = function($num) { return number_format((float) $num, 0, ',', '.'); };
?>

<div style="margin-bottom: 15px;">
    <strong style="font-size: 15px;">Harga beli aset: <?= $rp($hargaBeli) ?></strong> 
    <span class="text-muted">(dari master aset)</span>
</div>

<div class="row" style="margin-bottom: 15px;">
    <div class="col-xs-4">
        <div class="form-group">
            <label>Dibayar ke</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($supplierNama) ?>" readonly style="background-color: #f5f5f5;">
        </div>
    </div>
    <div class="col-xs-4">
        <div class="form-group">
            <label>Biaya lain (ongkir, instalasi)</label>
            <div class="input-group">
                <span class="input-group-addon">Rp</span>
                <input type="text" id="edit_biaya_lain" class="form-control text-right" 
                    value="<?= number_format($biayaLain, 0, ',', '.') ?>" 
                    onkeyup="pba.formatInputRupiah(this); pba.hitung_edit_tempo()">
            </div>
        </div>
    </div>
    <div class="col-xs-4">
        <div class="form-group">
            <label>Jumlah termin <span class="text-danger">*</span></label>
            <input type="number" id="edit_jumlah_termin" class="form-control" value="<?= (int) $h['jumlah_termin'] ?>" min="1" max="60" onchange="pba.generate_edit_tempo_rows()">
        </div>
    </div>
</div>

<hr style="margin: 10px 0;">

<div class="row" style="margin-bottom: 10px; font-weight: bold;">
    <div class="col-xs-1">Termin</div>
    <div class="col-xs-5">Jatuh tempo <span class="text-danger">*</span></div>
    <div class="col-xs-5 text-right">Nominal</div>
    <div class="col-xs-1"></div>
</div>

<div id="edit_tempo_rows">
    <!-- Rows di-generate via JS -->
</div>

<hr style="margin: 10px 0;">

<div class="row">
    <div class="col-xs-8 text-left"><strong>Total pembelian</strong></div>
    <div class="col-xs-4 text-right"><strong id="edit_display_total_pembelian"><?= $rp($totalPembelian) ?></strong></div>
</div>
<div class="row">
    <div class="col-xs-8 text-left">Dikurangi TDP (pengurang pokok)</div>
    <div class="col-xs-4 text-right" id="edit_display_tdp">(<?= $rp($tdpTotal) ?>)</div>
</div>
<div class="row" style="margin-top: 10px;">
    <div class="col-xs-8 text-left"><strong style="font-size: 14px;">Sisa dijadwalkan di termin</strong></div>
    <div class="col-xs-4 text-right"><strong id="edit_display_sisa" style="font-size: 14px;"><?= $rp($sisa) ?></strong></div>
</div>
<div class="row">
    <div class="col-xs-8 text-left">Jumlah semua termin</div>
    <div class="col-xs-4 text-right"><strong id="edit_display_total_termin">0</strong></div>
</div>

<p class="text-muted" style="margin-top: 15px; font-size: 12px;">
    Hutang supplier terbentuk saat aset diterima. Tanggal jatuh tempo diisi sesuai kesepakatan dengan supplier.
</p>

<div style="margin-top: 15px;">
    <button type="button" class="btn btn-primary" onclick="pba.update_rencana_tempo()">
        <i class="fa fa-save"></i> Simpan rencana tempo
    </button>
</div>

<!-- Simpan data existing untuk JS -->
<script id="edit_tempo_data" type="application/json">
<?= json_encode($details) ?>
</script>

<script>
$(document).ready(function() {
    pba.generate_edit_tempo_rows(true);
});
</script>