<?php if(empty($tdp['header']) ){?>

    <div class="panel panel-default installment-panel">
        <div class="panel-heading">
            <strong>TDP</strong>
            <input type="hidden" id="kode_pembiayaan_tdp" value="<?php echo $tdp['header']['kode_pembiayaan'] ?? ''; ?>">
        </div>
        <div class="panel-body">
            <div style="display:flex; flex-direction:column; gap:10px;">
                <span>Pembiayaan ini tanpa TDP, Pokok hutang hanya dikurangi uang muka DP.</span>
                <button type="button" class="btn btn-default" style="width:80px" onclick="pba.add_tdp(this, event)">Ada TDP</button>
            </div>
        </div>
    </div>


    <div class="panel-body" style="display:none;" id="body_tdp">
        <p class="text-muted">Tanda jadi, dibayar ke supplier.</p>
        <div id="form-tdp">
            <input type="hidden" id="kode_pembiayaan_tdp" value="<?php echo $tdp['header']['kode_pembiayaan'] ?? ''; ?>">
            <div class="form-group">
                <label for="tgl_tdp">Tanggal TDP <span class="text-danger">*</span></label>
                <div class="input-group date" id="tgl_tdp_clone_picker">
                    <?php
                        $tdpAda = !empty($tdp['header']);
                    ?>

                    <input type="text" id="tgl_tdp_clone" class="form-control" placeholder="Pilih Tanggal" style="caret-color:transparent; background-color:<?php echo $tdpAda ? '#ebebeb' : '#fff'; ?>;" <?php echo $tdpAda ? 'disabled' : ''; ?> value="<?php echo $tdp['header']['tanggal'] ?? ''; ?>" onkeydown="return false;" onpaste="return false;" ondrop="return false;" autocomplete="off">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
            <div class="checkbox" style="margin-top:-5px; margin-bottom:15px;">
                <label>
                    <input type="checkbox"
                        id="tdp_pengurang_pokok"
                        <?php echo !empty($tdp['header']['is_pengurang_pokok']) && $tdp['header']['is_pengurang_pokok'] == 1 ? 'checked' : ''; ?>
                        <?php echo !empty($tdp['header']) ? 'disabled' : ''; ?>>
                        Sebagai pengurang pokok hutang
                </label>
            </div>

            <div class="row">
                <div class="col-xs-7"><label>Deskripsi</label></div>
                <div class="col-xs-4 text-right"><label>Nominal</label></div>
                <div class="col-xs-1"></div>
            </div>
            <?php $total_tdp = 0;?>
            <?php if (!empty($tdp['detail'])){?>
                <div id="tdp-rows">
                    <?php foreach($tdp['detail'] as $t) { ?>
                        <?php $total_tdp += $t['nominal'];?>
                        <div class="row tdp-row" style="margin-bottom:8px;">
                            <div class="col-xs-7">
                                <input disabled type="text" class="form-control tdp-deskripsi" maxlength="255" value="<?php echo $t['deskripsi'] ?? 'Tanda jadi'?>" placeholder="Deskripsi">
                            </div>
                            <div class="col-xs-4">
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input <?php !empty($tdp['detail']) ? 'disabled' : '' ?> oninput="pba.formatInputRupiah(this)" type="text" class="form-control text-right tdp-nominal" value="<?php echo number_format((float) ($t['nominal'] ?? 0), 0, ',', '.'); ?>" inputmode="numeric" autocomplete="off" placeholder="0">
                                </div>
                            </div>
                            <div class="col-xs-1">
                                <button type="button" class="btn btn-danger btn-sm tdp-remove-row" onclick="pba.tdpRemoveRow(this)" title="Hapus baris" disabled><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                    <?php } ?>
                </div>                                    
            <?php } else { ?>
                <div id="tdp-rows">
                    <div class="row tdp-row" style="margin-bottom:8px;">
                        <div class="col-xs-7">
                            <input type="text" class="form-control tdp-deskripsi" maxlength="255" value="<?php echo $t['deskripsi'] ?? 'Tanda jadi'?>" placeholder="Deskripsi">
                        </div>
                        <div class="col-xs-4">
                            <div class="input-group">
                                <span class="input-group-addon">Rp</span>
                                <input type="text"  oninput="pba.formatInputRupiah(this); pba.hitung_tdp_total();" class="form-control text-right tdp-nominal" value="" inputmode="numeric" autocomplete="off" placeholder="0">
                            </div>
                        </div>
                        <div class="col-xs-1">
                            <button type="button" class="btn btn-danger btn-sm tdp-remove-row" onclick="pba.tdpRemoveRow(this)" title="Hapus baris" disabled><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                </div>                                
            <?php } ?>
                

            <div class="row">
                <div class="col-xs-6"><strong>Total TDP</strong></div>
                <div class="col-xs-6 text-right"><strong id="tdp-total"><?php echo number_format((float) ($total_tdp), 0, ',', '.')?></strong></div>
            </div>
            <hr>
            <button type="button" id="tdp-add-row" onclick="pba.tdpAddRow()" class="btn btn-default" <?php echo !empty($tdp['header']) ? 'disabled' : ''; ?>> + Tambah baris </button>
            <div id="button-tdp" style="margin-top:10px;">
                <?php if (!empty($tdp['header'])){?>
                    <button id="tdp_edit" onclick="pba.edit_tdp()" class="btn btn-warning">Update TDP</button>
                   
                <?php } else { ?>
                    <button id="tdp-save" onclick="pba.save_tdp()" class="btn btn-primary">Simpan TDP</button>
                    <button  onclick="pba.batal_tdp()" class="btn btn-danger">Batal</button>
                <?php } ?>
            </div>
        </div>

        <hr>
    </div>


<?php } else {?>

    <div class="panel panel-default installment-panel" idate>
        <div class="panel-heading">
            <strong>TDP</strong>
            <span class="label <?php echo !empty($tdp) ? 'label-success' : 'label-warning'; ?> pull-right">
                <?php echo !empty($tdp) ? 'Tersimpan' : 'Belum diajukan'; ?>
            </span>
        </div>
        <div class="panel-body" id="body_tdp">
            <p class="text-muted">Tanda jadi, dibayar ke supplier.</p>
            <div id="form-tdp">
                <input type="hidden" id="kode_pembiayaan_tdp" value="<?php echo $tdp['header']['kode_pembiayaan'] ?? ''; ?>">
                <div class="form-group">
                    <label for="tgl_tdp">Tanggal TDP <span class="text-danger">*</span></label>
                    <div class="input-group date" id="tgl_tdp_picker">
                        <?php
                            $tdpAda = !empty($tdp['header']);
                        ?>

                        <input type="text" id="tgl_tdp" class="form-control" placeholder="Pilih Tanggal" style="caret-color:transparent; background-color:<?php echo $tdpAda ? '#ebebeb' : '#fff'; ?>;" <?php echo $tdpAda ? 'disabled' : ''; ?> value="<?php echo $tdp['header']['tanggal'] ?? ''; ?>" onkeydown="return false;" onpaste="return false;" ondrop="return false;" autocomplete="off">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
                <div class="checkbox" style="margin-top:-5px; margin-bottom:15px;">
                    <label>
                        <input type="checkbox"
                            id="tdp_pengurang_pokok"
                            <?php echo !empty($tdp['header']['is_pengurang_pokok']) && $tdp['header']['is_pengurang_pokok'] == 1 ? 'checked' : ''; ?>
                            <?php echo !empty($tdp['header']) ? 'disabled' : ''; ?>>
                            Sebagai pengurang pokok hutang
                    </label>
                </div>

                <div class="row">
                    <div class="col-xs-7"><label>Deskripsi</label></div>
                    <div class="col-xs-4 text-right"><label>Nominal</label></div>
                    <div class="col-xs-1"></div>
                </div>
                <?php $total_tdp = 0;?>
                <?php if (!empty($tdp['detail'])){?>
                    <div id="tdp-rows">
                        <?php foreach($tdp['detail'] as $t) { ?>
                            <?php $total_tdp += $t['nominal'];?>
                            <div class="row tdp-row" style="margin-bottom:8px;">
                                <div class="col-xs-7">
                                    <input disabled type="text" class="form-control tdp-deskripsi" maxlength="255" value="<?php echo $t['deskripsi'] ?? 'Tanda jadi'?>" placeholder="Deskripsi">
                                </div>
                                <div class="col-xs-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">Rp</span>
                                        <input disabled oninput="pba.formatInputRupiah(this)" type="text" class="form-control text-right tdp-nominal" value="<?php echo number_format((float) ($t['nominal'] ?? 0), 0, ',', '.'); ?>" inputmode="numeric" autocomplete="off" placeholder="0">
                                    </div>
                                </div>
                                <div class="col-xs-1">
                                    <button type="button" class="btn btn-danger btn-sm tdp-remove-row" onclick="pba.tdpRemoveRow(this)" title="Hapus baris" disabled><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>                                    
                <?php } else { ?>
                    <div id="tdp-rows">
                        <div class="row tdp-row" style="margin-bottom:8px;">
                            <div class="col-xs-7">
                                <input type="text" class="form-control tdp-deskripsi" maxlength="255" value="<?php echo $t['deskripsi'] ?? 'Tanda jadi'?>" placeholder="Deskripsi">
                            </div>
                            <div class="col-xs-4">
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="text" oninput="pba.formatInputRupiah(this); pba.hitung_tdp_total();" class="form-control text-right tdp-nominal" value="" inputmode="numeric" autocomplete="off" placeholder="0">
                                </div>
                            </div>
                            <div class="col-xs-1">
                                <button type="button" class="btn btn-danger btn-sm tdp-remove-row" onclick="pba.tdpRemoveRow(this)" title="Hapus baris" disabled><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                    </div>                                
                <?php } ?>
                    

                <div class="row">
                    <div class="col-xs-6"><strong>Total TDP</strong></div>
                    <div class="col-xs-6 text-right"><strong id="tdp-total"><?php echo number_format((float) ($total_tdp), 0, ',', '.')?></strong></div>
                </div>
                <hr>
                <button type="button" id="tdp-add-row" onclick="pba.tdpAddRow()" class="btn btn-default" <?php echo !empty($tdp['header']) ? 'disabled' : ''; ?>> + Tambah baris </button>
                <div id="button-tdp" style="margin-top:10px;">
                    <?php if (!empty($tdp['header'])){?>
                        <button id="tdp_edit" onclick="pba.edit_tdp()" class="btn btn-warning">Update TDP</button>
                        <button type="button" onclick="pba.hapus_tdp()" class="btn btn-danger"> Hapus </button>
                    <?php } else { ?>
                        <button id="tdp-save" onclick="pba.save_tdp()" class="btn btn-primary">Simpan TDP</button>
                    <?php } ?>
                </div>
            </div>

            <hr>
        </div>
    </div>

<?php } ?>



