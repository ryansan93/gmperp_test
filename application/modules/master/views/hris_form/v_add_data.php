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
                <input type="text" class="form form-control kategori">
            </div>

        </div>
    </div>

    <br><br>
    <span style="font-size:17px;">Daftar Label</span>
    <hr>
    <div class="detail_area" style="display:flex; flex-direction:column; gap:10px; ">

        <div style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">

            <div style="display:flex; flex-direction:row; gap:10px; align-items:center;" class="detail_form">
                <label style="width:10%;">Label</label>
                <input type="text" class="form form-control label_dtl" style="width:40%;">
    
                <input type="text" placeholder="urutan" class="form form-control urutan_dtl" style="width:10%;">
                
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