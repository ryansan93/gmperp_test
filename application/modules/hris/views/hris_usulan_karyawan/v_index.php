

<div class="panel-heading no-padding">
    <ul class="nav nav-tabs nav-justified">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#riwayat" data-tab="riwayat">RIWAYAT USULAN</a>
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
                <label style="width:200px;">Cari data</label>
                <input type="text" class="form form-control pengaju-filter" placeholder="Masukan kata kunci" name="" id="">
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
                            <select class="select2 form form-control mengusulkan" onchange="hf.get_jabatan(this, event)">
                                <?php foreach($karyawan as $k){ ?>
                                    <option jabatan="<?php echo $k['jabatan']?>" value="<?php echo $k['nik']?>"> <?php echo ucwords(strtolower($k['nama'])) . ' - ' .  ucwords(strtolower($k['jabatan']))  ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div style="display:flex; flex-direction:column; gap:5px; flex:1; min-width:200px;">
                            <span>Tanggal Mengusulkan</span>
                           <div class="input-group date datetimepicker" id="tgl_pengusulan">
                                <input type="text" name="tgl_pengusulan" class="form-control text-center" placeholder="Tanggal Mengusulan" />
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
                                <?php foreach($posisi as $p){ ?>
                                    <option value="<?php echo $p['kode_posisi']?>"><?php echo $p['kode_posisi'] . ' - ' . $p['nama_posisi']?></option>
                                <?php } ?>
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
                                <option value="<?php echo $u['kode']?>"><?php echo ucwords($u['nama'])?></option>  
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

            <br>
            <div class="pull-right">
                <button class="btn btn-secondary " onclick="window.location.href='master/HrisForm' "> <i class="fa fa-angle-left" style="margin-right:10px;" aria-hidden="true"></i>  Back</button>
                <button class="btn btn-primary " onclick="hf.save(this, event)"> <i class="fa fa-floppy-o" style="margin-right:10px;" aria-hidden="true"></i>  Save Data</button>
            </div>

        </div>

        </div>

</div>
</div>

