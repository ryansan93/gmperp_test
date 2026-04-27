

<div class="panel-heading no-padding">
    <ul class="nav nav-tabs nav-justified">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#riwayat" data-tab="riwayat">RIWAYAT FORM</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#action" data-tab="action">ADD DATA</a>
        </li>
    </ul>
</div>


<div class="tab-content"> 
<div id="riwayat" class="tab-pane fade show active" role="tabpanel" style="padding-top: 10px;">

    <fieldset style="margin-bottom: 15px;">
        <legend>
            <div class="col-xs-12 no-padding">
                <b>FILTER</b>
            </div>
        </legend>
        <div style="display:flex; flex-direction:row; gap:10px;">
            
            <div style="display:flex; flex-direction:row; width:50%; gap:10px;">
                <label style="width:200px;">Nama Pengaju</label>
                <select class="select2 pengaju-filter" id="">
                    <option>-- Pilih Pengaju --</option>
                    <?php foreach($karyawan as $k) {?>
                        <option value="<?php echo $k['nik']?>"><?php echo $k['nama']?></option>
                        <!-- <option value="dfsfs">dfsfs</option> -->
                    <?php }?>
                </select>
            </div>

            <div>
                <button class="btn btn-primary" onclick="hf.filter_data(this, event)"><i class="fa fa-search" style="margin-right: 10px;" aria-hidden="true"></i> Filter</button>
                <!-- <button class="btn btn-primary" onclick="window.location.href='master/HrisForm/add_data' "><i class="fa fa-plus"  style="margin-right: 10px;" aria-hidden="true"></i> Add Data</button> -->
                <button class="btn btn-primary" onclick="hf.changeTabActive()"><i class="fa fa-plus"  style="margin-right: 10px;" aria-hidden="true"></i> Add Data</button>

                
            </div>


        </div>
    </fieldset>

    <fieldset style="margin-bottom: 15px;">
        <legend>
            <div class="col-xs-12 no-padding">
                <b>OUTSTANDING USULAN</b>
            </div>
        </legend>
        <div class="col-xs-12 no-padding list_data">
            <div class="spinner-load"></div>
        </div>
    </fieldset>

</div>

<div id="action" class="tab-pane fade tab-detail" role="tabpanel" style="padding-top: 10px;">

    <div class="panel panel-default">
        <div class="panel-heading"><span style="font-size:17px;">Tambah Data</span></div>
        <div class="panel-body">

            <div style="display:flex; flex-direction:row; gap:50px;">
                <div style="display:flex; flex-direction:column; gap:10px; width:100%">

                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <div style="display:flex; flex-direction:column; gap:5px; flex:1; min-width:200px;">
                            <span>Yang Mengusulkan</span>
                            <select class="select2 form form-control mengusulkan">
                                <?php foreach($karyawan as $k){ ?>
                                    <option value="<?php echo $k['nik']?>"><?php echo $k['nama']?></option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div style="display:flex; flex-direction:column; gap:5px; flex:1; min-width:200px;">
                            <span>Tanggal Mengusulkan</span>
                           <div class="input-group date datetimepicker" id="tgl_pengusulan">
                                <input type="text" name="tgl_pengusulan" class="form-control text-center" placeholder="Tanggal Kirim" />
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
                                    <option value="PPL">PPL</option>
                                <!-- < ?php } ?> -->
                            </select>
                        </div>

                        <div style="display:flex; flex-direction:column; gap:5px; flex:1; min-width:200px;">
                            <span>Jumlah</span>
                            <input type="number" class="form form-control jumlah">
                        </div>

                    </div>

                    <div style="display:flex; flex-direction:column; gap:5px;">
                        <span>Unit</span>
                        <select class="select2 form form-control unit">
                            <?php foreach($unit as $u){ ?>
                                <option value="<?php echo $u['kode']?>"><?php echo $u['nama']?></option>  
                            <?php } ?>
                        </select>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:5px;">
                        <span>Alasan</span>
                        <textarea rows="3" class="form form-control alasan"></textarea>
                    </div>

                </div>
            </div>

            <br>
           

            <span style="font-size:17px;">Daftar Kandidat</span>
            <hr>
            <div class="detail_area" style="display:flex; flex-direction:column; gap:10px; ">

                <div class="detail_form" style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">

                    <div style="display:flex; flex-direction:row; gap:10px; align-items:center;">
                        <label style="width:10%;">Nama</label>
                         <select class="select2 nama_kandidat" style="width:40%;">
                            <option disabled selected>-- Pilih Kandidat --</option>
                            <?php foreach($kandidat as $k){?>
                                <option <?php echo $k['document']?> value="<?php echo $k['id']?>"><?php echo $k['nama']?></option>
                            <?php }?>
                        </select>
                        
                        <div style="width:40%; text-align:right">
                            <button class="btn btn-warning" onclick="hf.add_row(this, event);"><span class="fa fa-plus"></span></button>
                            <button class="btn btn-danger" onclick="hf.delete_row(this, event);"><span class="fa fa-close"></span></button>   
                        </div>
                    </div>
                </div>

            </div>
            <br>

            <br>
            <div class="pull-right">
                <button class="btn btn-secondary " onclick="window.location.href='master/HrisForm' "> <i class="fa fa-angle-left" style="margin-right:10px;" aria-hidden="true"></i>  Back</button>
                <button class="btn btn-primary " onclick="hf.save(this, event)"> <i class="fa fa-floppy-o" style="margin-right:10px;" aria-hidden="true"></i>  Save Data</button>
            </div>

        </div>

        </div>

</div>
</div>

