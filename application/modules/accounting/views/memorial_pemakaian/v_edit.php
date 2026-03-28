<?php 
    $tot_kredit = 0;
    $tot_debet = 0;

    foreach($detail_data as $dt){
        if ($dt['coa_asal_nama']){
            $tot_kredit += $dt['nilai'];
        }

        if ($dt['coa_tujuan_nama']){
            $tot_debet += $dt['nilai'];
        }
    }
?>


<div class="col-xs-7 no-padding" style="padding-right: 5px;">
    <div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
        <div class="col-xs-3 no-padding"><label class="control-label">No. Memo</label></div>
        <div class="col-xs-4 no-padding">
            <input type="text" class="col-xs-12 form-control no_mmpem uppercase" placeholder="No. Memo" disabled>
        </div>
    </div>

    <div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
		<div class="col-xs-3 no-padding"><label class="control-label">Tanggal Memo</label></div>
		<div class="col-xs-4 no-padding">
			<div class="input-group date datetimepicker lock_date_fiskal" name="tglMm" id="TglMm">
				<input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" data-tgl="<?php echo $detail_data[0]['tgl_mmpem']; ?>" />
				<span class="input-group-addon">
					<span class="glyphicon glyphicon-calendar"></span>
				</span>
			</div>
		</div>
	</div>
    
    
    <div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
        <div class="col-xs-3 no-padding"><label class="control-label">Keterangan</label></div>
        <div class="col-xs-9 no-padding">
            <textarea class="form-control keterangan_hdr"></textarea>
        </div>
    </div>
</div>
<div class="col-xs-5 no-padding" style="padding-left: 5px;">
    <div class="col-xs-12 no-padding hide" style="margin-bottom: 5px;">
        <div class="col-xs-3">&nbsp;</div>
        <div class="col-xs-3 no-padding"><label class="control-label">Total</label></div>
        <div class="col-xs-6 no-padding nilai">
            <input type="text" class="col-xs-12 form-control text-right nilai uppercase" placeholder="Total" disabled>
        </div>
    </div>

    <div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
        <div class="col-xs-3">&nbsp;</div>
        <div class="col-xs-3 no-padding"><label class="control-label">Debet</label></div>
        <div class="col-xs-6 no-padding nilai">
            <input type="text" value="<?php echo angkaDecimal($tot_debet) ?>" class="col-xs-12 form-control text-right tot_debet uppercase" disabled>
        </div>
    </div>
    <div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
        <div class="col-xs-3">&nbsp;</div>
        <div class="col-xs-3 no-padding"><label class="control-label">Kredit</label></div>
        <div class="col-xs-6 no-padding nilai">
            <input type="text" value="<?php echo angkaDecimal($tot_kredit) ?>" class="col-xs-12 form-control text-right tot_kredit uppercase" disabled>
        </div>
    </div>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 no-padding">
    <div class="col-xs-12 no-padding" style="overflow-x: auto;">
        <small>
            <table class="table table-bordered tbl_detail" style="margin-bottom: 0px; max-width: 100%; width: 100%; font-size:10px;">
                <thead>
                    <tr>
                        <th class="col-xs-1">Unit</th>
                        <th class="col-xs-2">Plasma</th>
                        <th class="col-xs-2">No. Reg</th>
                        <th class="col-xs-1">Umur LHK</th>
                        <th class="col-xs-1">Debet</th>
                        <th class="col-xs-1">Kredit</th>
                        <th class="col-xs-2">Keterangan</th>
                        <th class="col-xs-1">Nilai</th>
                        <th class="col-xs-1">Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach($detail_data as $dd){ ?>

                        <tr class="data" data-urut="">
                            <td>
                                <select class="form-control unit" data-required="1">
                                    <?php if ( !empty($unit) ): ?>
                                        <?php foreach ($unit as $k_unit => $v_unit): ?>
                                            <option <?php echo $dd['unit'] ==  $v_unit['kode'] ? 'selected' : ''; ?> value="<?php echo $v_unit['kode']; ?>"><?php echo $v_unit['nama']; ?></option>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </select>
                            </td>
                            <td>
                                <select class="form-control plasma" data-required="1" onchange="mm.setDataNoreg(this, event)">
                                    <?php if ( !empty($plasma) ): ?>
                                        <option disabled selected>-- Pilih Plasma --</option>
                                        <?php foreach ($plasma as $k_plasma => $v_plasma): ?>
                                            <option <?php echo $dd['mitra_plasma'] ==  $v_plasma['nim'] ? 'selected' : ''; ?> value="<?php echo $v_plasma['nim']; ?>"><?php echo $v_plasma['nama']; ?></option>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </select>
                            </td>
                            <td>
                                <div style="display:none" class="noreg-selected"><?php echo $dd['noreg'] ?></div>
                                <select class="form-control noreg" data-required="1" onchange="mm.setUmurLhk(this, event)"> </select>
                            </td>
                            <td>
                                <div class="umur-lhk-selected" style="display:none"><?php echo $dd['umur_lhk'] ?></div>
                                <select class="form-control umur-lhk" data-required="1" onchange=""> </select>
                            </td>
                            <td> 
                                <select class="form-control tujuan" >
                                    <option value="">Pilih COA</option>
                                    <?php if ( !empty($coa) ): ?>
                                        <?php foreach ($coa as $k_coa => $v_coa): ?>
                                            <option <?php echo $dd['coa_tujuan']  == $v_coa['no_coa'] ? 'selected' : ''?> value="<?php echo $v_coa['no_coa']; ?>" data-nama="<?php echo $v_coa['nama_coa']; ?>" ><?php echo $v_coa['no_coa'].' | '.$v_coa['nama_coa']; ?></option>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </select>
                            </td>
                            <td> 
                                <select class="form-control asal" >
                                    <option value="">Pilih COA</option>
                                    <?php if ( !empty($coa) ): ?>
                                        <?php foreach ($coa as $k_coa => $v_coa): ?>
                                            <option <?php echo $dd['coa_asal']  == $v_coa['no_coa'] ? 'selected' : ''?> value="<?php echo $v_coa['no_coa']; ?>" data-nama="<?php echo $v_coa['nama_coa']; ?>" ><?php echo $v_coa['no_coa'].' | '.$v_coa['nama_coa']; ?></option>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </select></td>
                            <td>
                                <input type="text" class="form-default keterangan uppercase" placeholder="Keterangan" value="<?php echo $dd['keterangan'] ?> " maxlength="50">
                            </td>
                    
                            <td>
                                <input type="text" class="form-default text-right nilai" placeholder="Nilai"  value="<?php echo angkaDecimal($dd['nilai']) ?>"  data-tipe="decimal" maxlength="19" data-required="1" onblur="mm.hitGrandTotal(this)">
                            </td>
                            <td>
                                <div class="col-xs-12 no-padding">
                                    <div class="col-xs-6 no-padding" style="padding-right: 3px;">
                                        <button type="button" class=" btn btn-sm btn-danger" onclick="mm.removeRow(this)"><i class="fa fa-times"></i></button>
                                    </div>
                                    <div class="col-xs-6 no-padding" style="padding-left: 3px;">
                                        <button type="button" class=" btn btn-sm btn-primary" onclick="mm.addRow(this)"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </td>
                        </tr>

                    <?php } ?>

                </tbody>
            </table>
        </small>
    </div>
</div>

<div class="col-xs-12 no-padding"><hr></div>

<div class="col-xs-12 no-padding text-right" style="margin-top:10px;">
    <button type="button" class="btn btn-danger btn-batal" onclick="mm.backDetail(this, event)"><i class="fa fa-close"></i> Batal</button>
    <button type="button" class="btn btn-primary" onclick="mm.edit()"><i class="fa fa-save"></i> Update</button>
</div>


                   