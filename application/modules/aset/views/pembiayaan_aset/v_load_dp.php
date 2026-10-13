<div class="panel panel-default installment-panel">
    <div class="panel-heading">
        <strong>DP</strong>
        <span class="label <?php echo !empty($dp) ? 'label-success' : 'label-warning'; ?> pull-right">
            <?php echo !empty($dp) ? 'Tersimpan' : 'Belum diajukan'; ?>
        </span>
    </div>
    <div class="panel-body">
        <p class="text-muted">Uang muka, biaya awal, dan angsuran yang dibayar di muka.</p>
        <div id="form-dp">
            <input type="hidden" id="kode_pembiayaan_dp" value="<?php echo $dp['header']['kode_pembiayaan'] ?? null; ?>">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="tgl_dp">Tanggal DP</label>
                        <div class="input-group date" id="tgl_dp_picker">
                            <?php
                                $dpAda = !empty($dp['header']);
                            ?>

                            <input type="text"
                                id="tgl_dp"
                                class="form-control"
                                placeholder="Pilih Tanggal"
                                style="caret-color: transparent; background-color: <?php echo $dpAda ? '#ebebeb' : '#fff'; ?>;"
                                <?php echo $dpAda ? 'disabled' : ''; ?>
                                onkeydown="return false;"
                                onpaste="return false;"
                                ondrop="return false;"
                                autocomplete="off"
                                value="<?php echo htmlspecialchars($dp['header']['tanggal_dp'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Diajukan ke</label>
                        <input type="text" id="supplier-dp" class="form-control" value="" readonly>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-xs-8"><label>Jenis Komponen</label></div>
                <div class="col-xs-3 text-right"><label>Nominal</label></div>
                <div class="col-xs-1"></div>
            </div>
            <div id="dp-rows">
                <!--  < ?php echo $dp['header']['kode_pembiayaan'] ?? null; ?> -->
                <?php $total_dp_dna = 0?>
                <?php if (!empty($dp['header'])){?>
                    <?php foreach($dp['detail_non_angsuran'] as $dna){?>
                    <?php $total_dp_dna += $dna['nominal']?>

                    <div class="row dp-row" style="margin-bottom:8px;">
                        <div class="col-xs-8">
                            <input disabled type="text" class="form-control dp-komponen" value="<?php echo $dna['jenis_komponen']?>" placeholder="Jenis komponen (contoh: UANG_MUKA)">
                        </div>
                        <div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right dp-nominal" disabled oninput="pba.inputRupiah(this);" autocomplete="off" value="<?php echo number_format((float) ($dna['nominal'] ?? 0), 0, ',', '.'); ?>" placeholder="0"></div></div>
                        <div class="col-xs-1 text-right"><button type="button" disabled onclick="pba.dpRemoveRow(this)" class="btn btn-danger btn-sm dp-delete-row" data-id="" title="Hapus"><i class="fa fa-trash"></i></button></div>
                    </div>

                    <?php } ?>

                <?php } else {?>
              
                    <div class="row dp-row" style="margin-bottom:8px;">
                        <div class="col-xs-8">
                            <input type="text" class="form-control dp-komponen" readonly value="UANG MUKA" placeholder="Jenis komponen (contoh: UANG_MUKA)">
                        </div>
                        <div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right dp-nominal" oninput="pba.inputRupiah(this);" autocomplete="off" value="" placeholder="0"></div></div>
                        <div class="col-xs-1 text-right"><button type="button" onclick="pba.dpRemoveRow(this)" class="btn btn-danger btn-sm dp-delete-row" data-id="" title="Hapus"><i class="fa fa-trash"></i></button></div>
                    </div>

                    <div class="row dp-row" style="margin-bottom:8px;">
                        <input type="hidden" class="dp-id" value="">
                        <div class="col-xs-8">
                            <input type="text" class="form-control dp-komponen" value="ADMINISTRASI" placeholder="Jenis komponen (contoh: UANG_MUKA)">
                        </div>
                        <div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right dp-nominal" oninput="pba.inputRupiah(this);" autocomplete="off" value="" placeholder="0"></div></div>
                        <div class="col-xs-1 text-right"><button type="button" onclick="pba.dpRemoveRow(this)" class="btn btn-danger btn-sm dp-delete-row" data-id="" title="Hapus"><i class="fa fa-trash"></i></button></div>
                    </div>
                    
                    <div class="row dp-row" style="margin-bottom:8px;">
                        <input type="hidden" class="dp-id" value="">
                        <div class="col-xs-8">
                            <input type="text" class="form-control dp-komponen" value="ASURANSI" placeholder="Jenis komponen (contoh: UANG_MUKA)">
                        </div>
                        <div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right dp-nominal" oninput="pba.inputRupiah(this);" autocomplete="off" value="" placeholder="0"></div></div>
                        <div class="col-xs-1 text-right"><button type="button" onclick="pba.dpRemoveRow(this)" class="btn btn-danger btn-sm dp-delete-row" data-id="" title="Hapus"><i class="fa fa-trash"></i></button></div>
                    </div>

                    <div class="row dp-row" style="margin-bottom:8px;">
                        <input type="hidden" class="dp-id" value="">
                        <div class="col-xs-8">
                            <input type="text" class="form-control dp-komponen" value="BIAYA POLIS" placeholder="Jenis komponen (contoh: UANG_MUKA)">
                        </div>
                        <div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right dp-nominal" oninput="pba.inputRupiah(this);" autocomplete="off" value="" placeholder="0"></div></div>
                        <div class="col-xs-1 text-right"><button type="button" onclick="pba.dpRemoveRow(this)" class="btn btn-danger btn-sm dp-delete-row" data-id="" title="Hapus"><i class="fa fa-trash"></i></button></div>
                    </div>

                    <div class="row dp-row" style="margin-bottom:8px;">
                        <input type="hidden" class="dp-id" value="">
                        <div class="col-xs-8">
                            <input type="text" class="form-control dp-komponen" value="FIDUSIA" placeholder="Jenis komponen (contoh: UANG_MUKA)">
                        </div>
                        <div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right dp-nominal" oninput="pba.inputRupiah(this);" autocomplete="off" value="" placeholder="0"></div></div>
                        <div class="col-xs-1 text-right"><button type="button" onclick="pba.dpRemoveRow(this)" class="btn btn-danger btn-sm dp-delete-row" data-id="" title="Hapus"><i class="fa fa-trash"></i></button></div>
                    </div>

                <?php } ?>
                

            </div>
            <div id="dp-installment-rows">
            
                <?php $total_dp_da = 0?>
                <?php if (!empty($dp)){?>
                    <?php foreach($dp['detail_angsuran'] as $da){?>
                        <?php $total_dp_da += $da['nominal']?>
                        <div class="row dp-installment-row" style="margin-bottom:8px;">
                            <div class="col-xs-5"><label style="padding-top:7px;">Angsuran ke</label></div>
                            <div class="col-xs-3"><input disabled type="number" class="form-control dp-angsuran-ke" min="1" value="<?php echo $da['angsuran_ke']?>" placeholder="Ke" ></div>
                            <div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input disabled type="text" class="form-control text-right dp-nominal" oninput="pba.inputRupiah(this);" autocomplete="off" value="<?php echo number_format((float) ($da['nominal'] ?? 0), 0, ',', '.'); ?>"></div></div>
                            <div class="col-xs-1 text-right"><button disabled type="button" onclick="pba.dpRemoveAngsuranRow(this)" class="btn btn-danger btn-sm dp-delete-row" data-id="" title="Hapus angsuran"><i class="fa fa-trash"></i></button></div>
                        </div>
                    <?php } ?>
                
                <?php } else { ?>
                    <div class="row dp-installment-row" style="margin-bottom:8px;">
                        <div class="col-xs-5"><label style="padding-top:7px;">Angsuran ke</label></div>
                        <div class="col-xs-3"><input  type="number" class="form-control dp-angsuran-ke" min="1" value="1" placeholder="Ke" ></div>
                        <div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right dp-nominal" oninput="pba.inputRupiah(this);" autocomplete="off" value=""></div></div>
                        <div class="col-xs-1 text-right"><button type="button" onclick="pba.dpRemoveAngsuranRow(this)" class="btn btn-danger btn-sm dp-delete-row" data-id="" title="Hapus angsuran"><i class="fa fa-trash"></i></button></div>
                    </div>
                <?php } ?>
    
            </div>
            <div class="row">
                <div class="col-xs-6"><strong>Total DP</strong></div>
                <?php $total_dp = $total_dp_dna + $total_dp_da ?>
                <div class="col-xs-6 text-right"><strong id="dp-total"><?php echo number_format((float) ($total_dp ?? 0), 0, ',', '.'); ?></strong></div>
            </div>
            <hr>
            <button type="button" id="dp-add-row" class="btn btn-default" onclick="pba.dpAddRow(this)" <?php echo !empty($dp['header']) ? 'disabled' : ''; ?>>+ Tambah baris</button>
            <button type="button" id="dp-add-installment" class="btn btn-default" onclick="pba.dpAddAngsuranRow(this)" <?php echo !empty($dp['header']) ? 'disabled' : ''; ?>>+ Tambah angsuran</button>

            <div id="button-dp" style="margin-top:10px;">
                <?php if (!empty($dp['header'])){?>
                    <div style="margin-top:10px;">
                        <button id="dp-edit" onclick="pba.edit_dp(this, event)" class="btn btn-warning">Update DP</button>
                    </div>
                <?php } else { ?>
                    <div style="margin-top:10px;">
                        <button id="dp-save" onclick="pba.save_dp(this, event)" class="btn btn-primary">Simpan DP</button>
                    </div>
                <?php } ?>
            </div>
            
        </div>
    </div>
</div>