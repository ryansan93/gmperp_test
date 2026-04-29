

    <div class="detail_area" style="display:flex; flex-direction:column; gap:10px;">

        <div  class="detail_form" style="display:flex; flex-direction:column; gap:10px; padding:10px; border-right: 2px solid #d2d2d2; border-top: 2px solid #d2d2d2; border-bottom: 2px solid #d2d2d2; border-left: 4px solid #ababab;">
            <div style="display:flex; flex-direction:row; gap:10px; align-items:center;">
                    <label style="width:15%;">Nama Status</label>
                    <input type="text" class="form form-control nama_status" value="<?php echo $status_karyawan[0]['nama_status']?>" style="width:30%;">
        

                    <label style="width:15%;">Pilih Kategori</label>
                    <select class="form form-control kategori" style="width:30%;">
                        <option disabled selected> -- Pilih Kategori --</option>
                        <?php foreach ($kategori as $k){?>
                            <option value="<?php echo $k['kode_kategori']?>" <?php echo $status_karyawan[0]['kode_kategori'] == $k['kode_kategori'] ? 'selected': '' ?> ><?php echo $k['nama_kategori']?></option>
                        <?php } ?>
                    </select>

                    <div style="display:none" class="id_data"  kode="<?php echo $status_karyawan[0]['id_data'] ?>"></div>
            </div>
        </div>

    </div>


