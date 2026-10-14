<div class="panel panel-default" id="detail-tempo" data-harga-beli="<?php echo (int) ($aset['harga_beli'] ?? 0); ?>"
    data-tdp-total="<?php echo (int) ($tdp_total ?? 0); ?>">

    <div class="panel-heading">
        <div class="clearfix">
            <label style="margin:0;"> Rencana tempo</label>
            <span class="label <?php echo !empty($tempoHeader) ? 'label-success' : 'label-warning'; ?> pull-right"><?php echo !empty($tempoHeader) ? 'Tersimpan' : 'Belum disimpan'; ?></span>
        </div>
    </div>
    <div class="panel-body">
        <div class="row" style="margin-bottom:15px;">
            <div class="col-xs-12">
                <strong>Harga beli aset: </strong>Rp.  <?php echo number_format($aset['harga_beli'], 0, ',' , '.') ?? '' ; ?> <span class="text-muted"></span>
            </div>
        </div>

        <div id="form-tempo">
            <input type="hidden" id="kode_pembiayaan_tempo" value="">
            
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Dibayar ke</label>
                        <input type="text" class="form-control" value="<?php echo $aset['nama_supplier'] ?? '' ?>" readonly>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Biaya lain (ongkir, instalasi)</label>
                        <div class="input-group">
                            <span class="input-group-addon">Rp</span>
                            <input <?php echo !empty($tempo['header']) ? 'disbled' : '' ?> type="text" id="biaya_lain_tempo" oninput="pba.formatInputRupiah(this); pba.hitung_tempo();" class="form-control text-right" value="<?php echo number_format($tempo['header']['biaya_lain'], 0 , ',' , '.') ?? '' ?>" >
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Jumlah termin <span class="text-danger">*</span></label>
                        <input <?php echo !empty($tempo['header']) ? 'disbled' : '' ?>  type="number" onchange="pba.generate_tempo_rows(this)" id="jumlah_termin" class="form-control" min="1" max="60" value="<?php echo $tempo['header']['jumlah_termin'] ?? '' ?>" placeholder="Contoh: 2" >
                    </div>
                </div>
            </div>

            <hr style="margin:15px 0;">
            
            <div class="row" style="margin-bottom:10px; font-weight:bold;">
                <div class="col-xs-1">Termin</div>
                <div class="col-xs-5">Jatuh tempo <span class="text-danger">*</span></div>
                <div class="col-xs-5 text-right">Nominal</div>
                <div class="col-xs-1"></div>
            </div>
            
            <div id="tempo-rows">
                <?php if (!empty($tempo['detail'])) { ?>
                    <?php $total_nominal_termin = 0; ?>
                    <?php foreach ($tempo['detail'] as $detail) { ?>

                         <?php $total_nominal_termin += $detail['nominal']; ?>
                    
                        <div class="row tempo-row" style="margin-bottom:8px;">
                            <div class="col-xs-1" style="padding-top:7px;"><strong><?php echo (int) $detail['termin_ke']; ?></strong></div>
                            <div class="col-xs-5">
                                <div class="input-group date tempo-jatuh-tempo-picker">
                                    <?php
                                        $jt = $detail['jatuh_tempo'] ?? '';
                                        $jtFormatted = $jt;
                                        if ($jt) {
                                            $bulanID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                                            $parts = explode('-', $jt);
                                            if (count($parts) === 3) {
                                                $jtFormatted = (int)$parts[2] . ' ' . $bulanID[(int)$parts[1] - 1] . ' ' . $parts[0];
                                            }
                                        }
                                    ?>
                                    <input type="text" disabled class="form-control tempo-jatuh-tempo"  value="<?php echo htmlspecialchars($jtFormatted); ?>" >
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-xs-5">
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="text" disabled class="form-control text-right tempo-nominal"  value="<?php echo number_format((float) $detail['nominal'], 0, ',', '.'); ?>"  >
                                </div>
                            </div>
                            <div class="col-xs-1 text-right">
                                <button type="button" disabled class="btn btn-danger btn-sm tempo-remove-row" onclick="pba.remove_row_tempo(this)"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                    <?php } ?>

                <?php } ?>
            </div>

            <hr style="margin:15px 0;">
            
            <div class="row">
                <div class="col-xs-8 text-left"><strong>Total pembelian</strong></div>
                <div class="col-xs-4 text-right"><strong id="display_total_pembelian"><?php echo number_format($tempo['header']['total_pembelian'] ?? 0), 0 , ',', '.' ?></strong></div>
            </div>
            <div class="row">
                <div class="col-xs-8 text-left">Dikurangi TDP (pengurang pokok)</div>
                <div class="col-xs-4 text-right" id="display_tdp">(<?php echo number_format(($tempo['header']['dikurangi_tanda_jadi'] ?? 0), 0, ',', '.'); ?>)</div>
            </div>
            <div class="row" style="margin-top:10px;">
                <div class="col-xs-8 text-left"><strong style="font-size:14px;">Sisa dijadwalkan di termin</strong></div>
                <div class="col-xs-4 text-right"><strong id="display_sisa" style="font-size:14px;"> <?php echo number_format(($tempo['header']['sisa_dijadwalkan'] ?? 0), 0, ',', '.'); ?></strong></div>
            </div>
            <div class="row">
                <div class="col-xs-8 text-left">Jumlah semua termin</div>
                <div class="col-xs-4 text-right"><strong id="display_total_termin"><?php echo number_format(( $total_nominal_termin ?? 0), 0, ',', '.'); ?></strong></div>
            </div>

            <p class="text-muted" style="margin-top:15px; font-size:12px;">
                Hutang supplier terbentuk saat aset diterima. Tanggal jatuh tempo diisi sesuai kesepakatan dengan supplier.
            </p>

            <div id="button-tempo" style="margin-top:15px;">

                <?php if (!empty($tempo['header'])) { ?>
                    <button type="button" id="btn_update_tempo" class="btn btn-warning btn-sm" onclick="pba.edit_tempo(this)">
                        <i class="fa fa-pencil"></i> Update Rencana Tempo
                    </button>
                <?php } else {?>
                    <button type="button" id="btn_simpan_tempo" onclick="pba.save_rencana_tempo();" class="btn btn-primary" disabled>
                        Simpan rencana tempo
                    </button>
                <?php }?>

            </div>

           
          
        </div>
    </div>
</div>