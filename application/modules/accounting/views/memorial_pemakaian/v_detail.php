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
            <input type="text" class="col-xs-12 form-control no_mm uppercase" value="<?php echo $detail_data[0]['no_mmpem'] ?>" placeholder="No. Memo" disabled>
        </div>
    </div>
    
    <div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
        <div class="col-xs-3 no-padding"><label class="control-label">Tanggal Memo </label></div>
        <div class="col-xs-4 no-padding">
            <div class="input-group date datetimepicker lock_date_fiskal TglMmPemDetail" name="tglMm" id="TglMmPemDetail">
                <input type="text" class="form-control text-center" readonly value="<?php echo tglIndonesia($detail_data[0]['tgl_mmpem'], '-', ' '); ?>" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </div>
    
    
    <div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
        <div class="col-xs-3 no-padding"><label class="control-label">Keterangan</label></div>
        <div class="col-xs-9 no-padding">
            <textarea class="form-control keterangan" readonly><?php echo $header_data['keterangan']?> </textarea>
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
            <table class="table table-bordered tbl_detail" style="margin-bottom: 0px; max-width: 100%; width: 100%; ">
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
                    </tr>
                </thead>
                <tbody>
                   
                    <?php foreach($detail_data as $dt){ ?>
                        <?php 
                            if ($dt['coa_asal_nama'] ){
                                $tot_kredit += $dt['nilai'];
                            }

                            if ($dt['coa_tujuan_nama'] ){
                                $tot_debet += $dt['nilai'];
                            }
                        ?>

                        <tr class="data" data-urut="">

                            <?php 
                                $unit_map = array_column($unit, 'nama', 'kode');
                                $plasma_map = array_column($plasma, 'nama', 'nim');
                            ?>

                            <td><?php echo isset($unit_map[$dt['unit']]) ? $unit_map[$dt['unit']] : ''; ?></td>
                            <td><?php echo isset($plasma_map[$dt['mitra_plasma']]) ? $plasma_map[$dt['mitra_plasma']] : ''; ?></td>
                            <td><?php echo $dt['noreg'] ?></td>
                            <td><?php echo $dt['umur_lhk'] ?></td>
                            <td style="white-space: nowrap;"><?php echo $dt['coa_tujuan_nama'] ? $dt['coa_tujuan_nama'] : ' - ' ?></td>
                            <td style="white-space: nowrap;"><?php echo $dt['coa_asal_nama']  ?  $dt['coa_asal_nama'] : ' - '?></td>
                            <td><?php echo $dt['keterangan'] ? $dt['keterangan'] : '-' ?></td>
                            <td class="text-right"><?php echo angkaDecimal($dt['nilai']) ?></td>
                        </tr>
                        <?php } ?>
                </tbody>
            </table>
        </small>
    </div>

</div>


<div class="col-xs-12 no-padding text-left" style="margin-top:10px;">
    <label style="text-decoration: underline;">Keterangan</label>
    <ul style="padding-left: 20px;">
        <li><?php echo $status['deskripsi'] . ' ' . $status['waktu'] ?></li>
    </ul>
</div>


<div class="col-xs-12 no-padding text-right" style="margin-top:10px;">
    <button type="button" class="btn btn-default" no_mmpem="<?php echo exEncrypt($detail_data[0]['no_mmpem']) ?>" onclick="mm.printPreview(this, event)"><i class="fa fa-print"></i>Cetak</button>
    <span style="border-left: 2px solid black; margin-right:7px;"></span>
    <button type="button" class="btn btn-danger" no_mmpem="<?php echo $detail_data[0]['no_mmpem'] ?>" onclick="mm.delete(this, event)"><i class="fa fa-trash"></i>Delete</button>
    <button type="button" class="btn btn-primary btn-edit" no_mmpem="<?php echo $detail_data[0]['no_mmpem'] ?>" onclick="mm.edit_data(this, event)"> <i class="fa fa-edit"></i>Edit</button>
</div>

<!-- <div class="header_data" style="display:none"></div> -->

