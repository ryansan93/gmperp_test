<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>FILTER</b>
        </div>
    </legend>
    <div style="display:flex; flex-direction:row; gap:10px;">
        
        <div style="display:flex; flex-direction:row; width:50%; gap:10px;">
            <label for="">Kategori</label>
            <select class="form form-control" id="kategori">
                <option>-- Pilih Kategori --</option>
                <?php foreach($kategori as $k) {?>
                    <option value="<?php echo $k['kategori']?>"><?php echo $k['kategori']?></option>
                    <!-- <option value="dfsfs">dfsfs</option> -->
                <?php }?>
            </select>
        </div>

        <div>
            <button class="btn btn-primary" onclick="hf.filter_data(this, event)"><i class="fa fa-search" style="margin-right: 10px;" aria-hidden="true"></i> Filter</button>
            <button class="btn btn-primary" onclick="window.location.href='master/HrisForm/add_data' "><i class="fa fa-plus"  style="margin-right: 10px;" aria-hidden="true"></i> Add Data</button>
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

<!-- <div>
    <button onclick="hf.generate_form(this, event)">Generate Form</button>
</div> -->