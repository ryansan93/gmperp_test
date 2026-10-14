<?php
    $kodePembiayaan     = htmlspecialchars($pembiayaan['kode_pembiayaan'] ?? '');
    $kodeAset           = htmlspecialchars($aset['kode_aset'] ?? '');
    $deskripsiAset      = htmlspecialchars($aset['deskripsi_aset'] ?? '');
    $kategori           = htmlspecialchars($aset['kategori_name'] ?? '-');
    $unit               = htmlspecialchars($aset['unit_pengguna'] ?? '-');
    $hargaBeli          = number_format((float) ($aset['nilai_perolehan'] ?? 0), 0, ',', '.');
    $supplierNama       = htmlspecialchars(($supplier->nama ?? '') . ' - ' . ($supplier->nomor ?? ''));
    $leasingNama        = htmlspecialchars(($leasing->nama ?? '') . ' - ' . ($leasing->nomor ?? ''));

?>

<input type="hidden" id="key_kode_pembiayaan" value="<?php echo $kodePembiayaan ?>">

<fieldset>
    <legend style="white-space:nowrap; width:50%">Proses Pembiayaan Aset - Leasing</legend>
    <div class="row" style="padding:0 15px;">
        <div class="col-xs-12">
            <div class="clearfix" style="margin-bottom:10px;">
                <strong><?php echo $kodePembiayaan; ?></strong>
                <span class="pull-right">
                    <span class="label label-info">Leasing</span>
                </span>
            </div>
            <p class="text-muted">
                <?php echo $kodeAset; ?> - <?php echo $deskripsiAset; ?> |
                <?php echo $kategori; ?> | Harga beli <?php echo $hargaBeli; ?>
                <br>
                Supplier <span class="supplier_name"><?php echo $supplierNama; ?></span> | Leasing <?php echo $leasingNama; ?>
            </p>
          
            <div class="load_tdp">
                <div class="panel panel-default">
                    <div class="panel-heading" style="background-color:#fff;">
                        <label style="margin:0;"> TDP</label>
                    </div>
                    <div class="panel-body">
                        <div class="col-xs-12 no-padding notifContain">
                            <div class="spinner-load"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="load_dp">
                <div class="panel panel-default">
                    <div class="panel-heading" style="background-color:#fff;">
                        <label style="margin:0;"> DP</label>
                    </div>
                    <div class="panel-body">
                        <div class="col-xs-12 no-padding notifContain">
                            <div class="spinner-load"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="load_cicilan">
                <div class="panel panel-default">
                    <div class="panel-heading" style="background-color:#fff;">
                        <label style="margin:0;"> Detail Cicilan</label>
                    </div>
                    <div class="panel-body">
                        <div class="col-xs-12 no-padding notifContain">
                            <div class="spinner-load"></div>
                        </div>
                    </div>
                </div>
            </div>

            <a href="<?php echo base_url('aset/PembiayaanAset'); ?>" class="btn btn-default">Kembali</a>
        </div>
    </div>
</fieldset>
