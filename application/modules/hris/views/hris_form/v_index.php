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
                <b>LIST DATA</b>
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
                <div style="display:flex; flex-direction:column; gap:10px; width:50%">
                    <div style="display:flex; flex-direction:column;">
                        <span style="width:200px;">Title</span>
                        <input type="text" class="form form-control title_hdr">
                    </div>
                    
                    <div style="display:flex; flex-direction:column;">
                        <span style="width:200px;">Keterangan</span>
                        <textarea type="text" rows="5" class="form form-control keterangan"></textarea>
                    </div>
                </div>

                <div style="display:flex; flex-direction:column; gap:10px; width:50%">
                    
                    <div style="display:flex; flex-direction:column;">
                        <span style="width:200px;">Urutan</span>
                        <input type="text" class="form form-control urutan_hdr">
                    </div>

                    <div style="display:flex; flex-direction:column;">
                        <span style="width:200px;">Kategori</span>
                        <input type="text" class="form form-control kategori" onclick="hf.show_kategori_list()">

                        <div class="kategori_list" >
                            <?php foreach($add_kategori as $k){ ?>
                                <div onclick="hf.select_kategori_list(this, event)" style="cursor: pointer;" value_kategori="<?php echo $k['kode_kategori']?>"><?php echo $k['nama_kategori']?></div>
                            <?php } ?>
                        </div>
                    </div>

                </div>
            </div>

            <br><br>
            <span style="font-size:17px;">Daftar Label</span>
            <hr>
            <div class="detail_area" style="display:flex; flex-direction:column; gap:10px; ">

                <div class="detail_form" style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">

                    <div style="display:flex; flex-direction:row; gap:10px; align-items:center;">
                        <label style="width:10%;">Label</label>
                        <input type="text" class="form form-control label_dtl" style="width:40%;">
            
                        <input type="text" placeholder="urutan" class="form form-control urutan_dtl" value="1" style="width:10%;">

                        <input type="text" placeholder="parent label" class="form form-control parent_label" style="width:10%;">
                        
                        <div style="width:40%; text-align:right">
                            <button class="btn btn-warning" onclick="hf.add_row(this, event);"><span class="fa fa-plus"></span></button>
                            <button class="btn btn-danger" onclick="hf.delete_row(this, event);"><span class="fa fa-close"></span></button>   
                        </div>
                    </div>
                </div>

            </div>
            <br>

            <div class="pull-right">
                <button class="btn btn-secondary " onclick="window.location.href='master/HrisForm' "> <i class="fa fa-angle-left" style="margin-right:10px;" aria-hidden="true"></i>  Back</button>
                <button class="btn btn-primary " onclick="hf.save(this, event)"> <i class="fa fa-floppy-o" style="margin-right:10px;" aria-hidden="true"></i>  Save Data</button>
            </div>

        </div>

        </div>

</div>
</div>

