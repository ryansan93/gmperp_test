
<div class="posisi-val" style="display:none;"><?php echo $edit_data[0]['posisi'] ?></div>


<div class="panel panel-default">
    <div class="panel-heading"><span style="font-size:17px;">Edit Data</span></div>
    <div class="panel-body">

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <div style="display:flex; flex-direction:column; gap:5px; flex:1; min-width:200px;">
                    <span>Yang Mengusulkan</span>

                    <select class="select2 form form-control mengusulkan" onchange="hf.get_jabatan(this, event)">
                        <?php foreach($karyawan as $k){ ?>
                            <option <?php echo $edit_data[0]['nama_pengusul'] == $k['nik']  ? 'selected' : '' ?> jabatan="<?php echo $k['jabatan']?>" value="<?php echo $k['nik']?>"> <?php echo ucwords(strtolower($k['nama'])) . ' - ' .  ucwords(strtolower($k['jabatan']))  ?> </option>
                        <?php } ?>
                    </select>
                </div>
                
                <div style="display:flex; flex-direction:column; gap:5px; flex:1; min-width:200px;">

                <?php $tgl = date('d M Y', strtotime($edit_data[0]['tgl_pengusulan'])); ?>
                    <span>Tanggal Mengusulkan</span>
                    <div class="input-group date datetimepicker" id="tgl_pengusulan">
                        <input type="text" name="tgl_pengusulan" class="form-control text-center" value="<?php echo $tgl ?>" placeholder="Tanggal Kirim" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                
                <div style="display:flex; flex-direction:column; gap:5px; flex:1; min-width:200px;">
                    <span>Posisi</span>
                    <select class="select2 form form-control posisi">
                     
                        <select class="select2 form form-control posisi">
                            <?php foreach($posisi as $p){ ?>
                                <option value="<?php echo $p['kode_dokumen']?>" <?php echo $edit_data[0]['posisi'] == $p['kode'] ? 'selected' : '' ?> value="<?php echo $p['kode_posisi']?>"><?php echo $p['kode_posisi'] . ' - ' . $p['nama_posisi']?></option>
                            <?php } ?>
                        </select>
                        
                    </select>
                </div>

                <div style="display:flex; flex-direction:column; gap:5px; flex:1; min-width:200px;">
                    <span>Jumlah</span>
                    <input type="number" value="<?php echo $edit_data[0]['jumlah'] ?>" class="form form-control jumlah">
                </div>

            </div>

            <div style="display:flex; flex-direction:column; gap:5px;">
                <span>Unit</span>
                 <select class="select2 form form-control unit">
                    <?php foreach($unit as $u){ ?>

                        <option <?php echo trim($edit_data[0]['unit']) == trim($u['kode']) ? 'selected' : '' ?>  value="<?php echo $u['kode']?>"><?php echo $u['nama']?></option>
                    <?php } ?>
                </select>
                    
            </div>

            <div style="display:flex; flex-direction:column; gap:5px;">
                <span>Alasan</span>
                <textarea rows="3" class="form form-control alasan"><?php echo $edit_data[0]['alasan'] ?></textarea>
            </div>

        </div>

        <br>

        </div>
    
        <div class="pull-right" style="margin-top:10px;">
            <button class="btn btn-secondary " onclick="window.location.href='hris/HrisUsulanKaryawan' "> <i class="fa fa-angle-left" style="margin-right:10px;" aria-hidden="true"></i>  Back</button>
            <button class="btn btn-primary " id_data="<?php echo $_GET['id_data'] ?>" onclick="hf.update(this, event)"> <i class="fa fa-floppy-o" style="margin-right:10px;" aria-hidden="true"></i>  Update Data</button>
        </div>

        <br>
        <br>

        

    </div>

</div>
