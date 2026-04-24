<div class="panel panel-default">
  <div class="panel-heading"><span style="font-size:17px;">Tambah Data</span></div>
  <div class="panel-body">

    <div class="detail_area" style="display:flex; flex-direction:column; gap:10px; ">

        <div class="detail_form" style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">

            <div style="display:flex; flex-direction:row; gap:10px; align-items:center;" class="detail_form">
                <label style="width:10%;">Nama karyawan</label>
                <input type="text" class="form form-control nama_kategori" style="width:40%;">
    

                <label style="width:10%;">Status karyawan</label>
                <select name="" id="">
                    <option value="">Interview</option>
                    <option value="">Training</option>
                    <option value="">Tetap</option>
                </select>
                
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