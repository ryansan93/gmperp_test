<input type="hidden" class="harga_beli" value="<?php echo $aset['harga_beli']?>">

<div class="panel panel-default installment-panel" id="detail-pelunasan">
    <div class="panel-heading">
        <div class="installment-heading">
            <div>
                <strong>Pelunasan</strong>
            </div>
            <span class="label <?php echo !empty($pelunasan['header']) ? 'label-success' : 'label-warning'; ?>" id="status-pelunasan">
                <?php echo !empty($pelunasan['header']) ? 'Tersimpan' : 'Belum disimpan'; ?>
            </span>
        </div>
    </div>
    <div class="panel-body">
        <p class="text-muted">Pembayaran harga aset ke supplier. Tanda jadi (pengurang pokok) sudah dikurangkan.</p>

        <div id="form-pelunasan">
            <input type="hidden" id="kode_pembiayaan_pelunasan" value="<?php echo $pelunasan['header']['kode_pembiayaan'] ?? ''; ?>">
            
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="tgl_pelunasan">Tanggal Pelunasan <span class="text-danger">*</span></label>
                        <div class="input-group date" id="tgl_pelunasan_picker">
                            <input type="text" id="tgl_pelunasan" class="form-control" placeholder="Pilih Tanggal" style="caret-color:transparent; background-color:<?php echo !empty($pelunasan['header']) ? '#eeeeee' : '#fff' ?>;" <?php echo !empty($pelunasan['header']) ? 'disabled' : '#fff' ?> onkeydown="return false;" onpaste="return false;" ondrop="return false;" value="<?php echo !empty($pelunasan['header']['tanggal_pelunasan']) ? htmlspecialchars($pelunasan['header']['tanggal_pelunasan'], ENT_QUOTES) : ''; ?>" autocomplete="off">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Diajukan ke</label>
                        <input type="text" class="form-control" value="<?php echo $aset['nama_supplier'] ?? ''?>" readonly>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-bottom: 10px;">
                <div class="col-xs-7"><label>Deskripsi</label></div>
                <div class="col-xs-4 text-right"><label>Nominal</label></div>
                <div class="col-xs-1"></div>
            </div>
            
            <div id="pelunasan-rows">

                <?php $total_pembelian = 0 ?>
                
                <?php if (!empty($pelunasan['detail'])) { ?>

                    <?php foreach ($pelunasan['detail'] as $detail) { ?>

                        <?php $total_pembelian += $detail['nominal'] ?>

                        <div class="row pelunasan-row" style="margin-bottom:8px;" data-is-harga-beli="0">
                            <div class="col-xs-7">
                                <input type="text" disabled class="form-control pelunasan-deskripsi"  maxlength="255" value="<?php echo $detail['deskripsi']; ?>"  placeholder="Deskripsi"  >
                            </div>
                            <div class="col-xs-4">
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="text" disabled class="form-control text-right pelunasan-nominal" inputmode="numeric"  oninput="pba.inputRupiah(this); pba.update_pelunasan_total();" autocomplete="off" value="<?php echo number_format($detail['nominal'], 0 , ',' , '.'); ?>"  placeholder="0"  >
                                </div>
                            </div>
                            <div class="col-xs-1">
                                <button type="button" disabled onclick="pba.removeRowPelunasan(this)" class="btn btn-danger btn-sm pelunasan-remove-row" title="Hapus baris" >
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>

                    <?php } ?>

                <?php } else {?>

                    <div class="row pelunasan-row" style="margin-bottom:8px;" data-is-harga-beli="1">
                        <div class="col-xs-7">
                            <input type="text" class="form form-control pelunasan-deskripsi" value="Harga beli aset">
                        </div>
                        <div class="col-xs-4">
                            <div class="input-group">
                                <span class="input-group-addon">Rp</span>
                                <input type="int" oninput="pba.inputRupiah(this); pba.update_pelunasan_total();" class="form-control text-right pelunasan-nominal" 
                                    value="<?php echo number_format($aset['harga_beli'], 0, ',', '.') ?? ''; ?>" style="background-color: #fff;">
                            </div>
                        </div>
                        <div class="col-xs-1"></div>
                    </div>

                <?php } ?>
            </div>

            <?php if (!empty($pelunasan['detail'])) { ?>
                <hr style="margin: 15px 0;">
                
                <div class="row">
                    <div class="col-xs-6"><strong>Total pembelian</strong></div>
                    <div class="col-xs-6 text-right"><strong><?php echo number_format(($total_pembelian ?? 0), 0, ',', '.'); ?></strong></div>
                </div>
                <div class="row">
                    <div class="col-xs-6">Dikurangi tanda jadi (pengurang pokok)</div>
                    <div class="col-xs-6 text-right"><?php echo number_format(($pelunasan['header']['dikurangi_tanda_jadi'] ?? 0), 0, ',', '.'); ?></div>
                </div>
                <div class="row" style="margin-top:10px;">
                    <div class="col-xs-6"><strong>Diajukan</strong></div>
                    <div class="col-xs-6 text-right"><strong><?php echo number_format(($pelunasan['header']['diajukan'] ?? 0), 0, ',', '.'); ?></strong></div>
                </div>
            
            <?php } else {?>
                <hr style="margin: 15px 0;">
                
                <div class="row">
                    <div class="col-xs-6"><strong>Total pembelian</strong></div>
                    <div class="col-xs-6 text-right"><strong id="total-pembelian"><?php echo number_format($aset['harga_beli'], 0, ',', '.') ?? ''; ?><S/strong></div>
                </div>
                <div class="row">
                    <div class="col-xs-6">Dikurangi tanda jadi (pengurang pokok)</div>
                    <div class="col-xs-6 text-right" id="dikurangi-tanda-jadi">(0)</div>
                </div>
                <div class="row" style="margin-top:10px;">
                    <div class="col-xs-6"><strong>Diajukan</strong></div>
                    <div class="col-xs-6 text-right"><strong id="diajukan"><?php echo number_format($aset['harga_beli'], 0, ',', '.') ?? ''; ?></strong></div>
                </div>
            <?php } ?>


            <hr>

            <div style="display:flex; flex-direction:row; gap:10px;">
                <button type="button" id="pelunasan-add-row" onclick="pba.addRowPelunasan();" class="btn btn-default" <?php echo !empty($pelunasan['header']) ? 'disabled' : ''; ?>>+ Tambah baris</button>
             
                <button type="button" id="pelunasan-delete" style="display:none;" onclick="pba.delete_pelunasan(this)" class="btn btn-danger" data-id="<?php echo (int) $pelunasan['header']['id']; ?>">
                    Hapus Pelunasan
                </button>
            </div>

            <div id="button-pelunasan" style="margin-top:10px;">
                <?php if( !empty($pelunasan['header'])){ ?>

                    <button onclick="pba.edit_pelunasan(this, event)" id="pelunasan-save"  class="btn btn-warning">
                        Edit Pelunasan
                    </button>
                
                <?php } else { ?>
                    <button onclick="pba.save_pelunasan(this, event)" id="pelunasan-save"  class="btn btn-primary">
                        Simpan Pelunasan
                    </button>
                <?php } ?>
            </div>
            
        </div>
    </div>
</div>