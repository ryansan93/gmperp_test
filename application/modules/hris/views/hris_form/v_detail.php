
    <div style="display:flex; flex-direction:row; gap:50px;">
        <div style="display:flex; flex-direction:column; gap:5px; width:50%">
            <div style="display:flex; flex-direction:row; gap:10px;">
                <span style="width:100px;">Title</span>
                <label for=""><?php echo $header['title'] ?></label>
            </div>
            
            <div style="display:flex; flex-direction:row; gap:10px;">
                <span style="width:100px;">Keterangan</span>
                <label for=""><?php echo $header['keterangan'] ?></label>
            </div>

            <div style="display:flex; flex-direction:row; gap:10px;">
                <span style="width:100px;">Kategori</span>
                <label for=""><?php echo $header['nama_kategori'] ?></label>
            </div>
        </div>
    
        <div style="display:flex; flex-direction:column; gap:5px;">
            <div style="display:flex; flex-direction:row; gap:10px;">
                <span style="width:100px;">Urutan</span>
                <label for=""><?php echo $header['urutan'] ?></label>
            </div>
        </div>
    </div>

    <br>
    <h4 style="">Daftar Label</h4>
    <hr>
    <?php $height = count($detail) > 6 ? ' height:350px; overflow-y:scroll' : ''; ?>
    <div class="detail_area" style="display:flex; flex-direction:column; gap:8px; <?php echo $height ?> ">

        <?php foreach($detail as $d){ ?>
        <div style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">
            <div style="display:flex; flex-direction:row; gap:10px; align-items:center;" class="detail_form">
                <span style="width:100px;">Label</span>
                <label for="" style="width:200px;"><?php echo $d['nama'] ?></label>
                <span style="width:100px;">Urutan</span>
                <label for="" style="width:200px;"><?php echo $d['urutan'] ?></label>
            </div>
        </div>
         <?php } ?>

    </div>
