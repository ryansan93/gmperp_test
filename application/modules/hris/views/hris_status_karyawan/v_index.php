<div class="panel-heading no-padding">
    <ul class="nav nav-tabs nav-justified">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#riwayat" data-tab="riwayat">RIWAYAT DATA</a>
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
      
            <div class="detail_area" style="display:flex; flex-direction:column; gap:10px; ">

                <div class="detail_form" style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">

                     <div style="display:flex; flex-direction:row; gap:20px; align-items:center;" >
                        <label style="width:20%;">Nama Status</label>
                        <input type="text" class="form form-control nama_status" style="width:60%;">
            

                        <label style="width:20%;">Pilih Kategori</label>
                        <select class="form form-control kategori" style="width:20%;">
                            <option disabled selected> -- Pilih Kategori --</option>
                            <?php foreach ($kategori as $k){?>
                                <option value="<?php echo $k['kode_kategori']?>" ><?php echo $k['nama_kategori']?></option>
                            <?php } ?>
                        </select>
                        
                        <div style="width:10%; text-align:right">
                            <button class="btn btn-warning" onclick="hf.add_row(this, event);"><span class="fa fa-plus"></span></button>
                            <button class="btn btn-danger" onclick="hf.delete_row(this, event);"><span class="fa fa-close"></span></button>   
                        </div>
                    </div>

                </div>

            </div>
            <br>

            <div class="pull-right">
                <button class="btn btn-secondary " onclick="window.location.href='hris/HrisKategori' "> <i class="fa fa-angle-left" style="margin-right:10px;" aria-hidden="true"></i>  Back</button>
                <button class="btn btn-primary " onclick="hf.save(this, event)"> <i class="fa fa-floppy-o" style="margin-right:10px;" aria-hidden="true"></i>  Save Data</button>
            </div>

        </div>

        </div>

</div>
</div>

