



<div class="panel panel-default">
    <div class="panel-heading"><span style="font-size:17px;">Edit Data</span></div>
    <div class="panel-body">

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <div style="display:flex; flex-direction:column; gap:5px; flex:1; min-width:200px;">
                    <span>Yang Mengusulkan</span>
                    <select class="select2 form form-control mengusulkan">
                        <?php foreach($karyawan as $k){ ?>
                            <option <?php echo $edit_data[0]['nama_pengusul'] == $k['nik']  ? 'selected' : '' ?> value="<?php echo $k['nik'] ?>"><?php echo $k['nama']?></option>
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
                        <!-- < ?php foreach($karyawan as $k){ ?> -->
                            <!-- <option value="< ?php echo $k['nik']?>">< ?php echo $k['nama']?></option> -->
                            <option value="IT">IT</option>
                            <option <?php echo $edit_data[0]['posisi'] == "PPL" ? 'selected' : '' ?> value="PPL">PPL</option>
                            
                        <!-- < ?php } ?> -->
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
                        <!-- < ?php foreach($karyawan as $k){ ?> -->
                            <!-- <option value="< ?php echo $k['nik']?>">< ?php echo $k['nama']?></option> -->
                            <option value="GSK">Gresik</option>
                            <option <?php echo $edit_data[0]['unit'] == "MLG" ? 'selected' : '' ?>  value="MLG">Malang</option>
                        <!-- < ?php } ?> -->
                    </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:5px;">
                <span>Alasan</span>
                <textarea rows="3" class="form form-control alasan"><?php echo $edit_data[0]['alasan'] ?></textarea>
            </div>

        </div>

        <br>

        <span style="font-size:17px; margin-left:10px;">Daftar Kandidat</span>
        <hr>
        <div class="detail_area" style="display:flex; flex-direction:column; gap:10px; padding:10px ">

            <?php foreach ($detail as $d) { ?>
                <div class="detail_form" style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">

                    <div style="display:flex; flex-direction:row; gap:10px; align-items:center;">
                        <label style="width:10%;">Nama</label>
                            <select class="select2 nama_kandidat" style="width:40%;">
                            <option disabled selected>-- Pilih Kandidat --</option>
                            <?php  foreach($kandidat as $k){?>
                                <option  <?php echo $d['id_kandidat'] ==  $k['id'] ? 'selected' : '' ?> <?php echo $k['document']?> value="<?php echo $k['id']?>"><?php echo $k['nama']?></option>
                            <?php }?>
                        </select>
                        
                        <div style="width:40%; text-align:right">
                            <button class="btn btn-warning" onclick="hf.add_row(this, event);"><span class="fa fa-plus"></span></button>
                            <button class="btn btn-danger" onclick="hf.delete_row(this, event);"><span class="fa fa-close"></span></button>   
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>
    
        <div class="pull-right" style="margin-top:10px;">
            <button class="btn btn-secondary " onclick="window.location.href='hris/HrisUsulanKaryawan' "> <i class="fa fa-angle-left" style="margin-right:10px;" aria-hidden="true"></i>  Back</button>
            <button class="btn btn-primary " id_data="<?php echo $_GET['id_data'] ?>" onclick="hf.update(this, event)"> <i class="fa fa-floppy-o" style="margin-right:10px;" aria-hidden="true"></i>  Update Data</button>
        </div>

        <br>
        <br>

        <br>

        

    </div>

</div>