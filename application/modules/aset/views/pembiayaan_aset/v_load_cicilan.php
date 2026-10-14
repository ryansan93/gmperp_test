   <div class="panel panel-default installment-panel" id="detail-cicilan">
    <div class="panel-heading">
        <div class="installment-heading">
            <div>
                <strong>Detail cicilan</strong>
                <span class="label label-default installment-rate-tag">Bunga flat</span>
            </div>
            <span class="label <?php echo !empty($data_cicilan['header']) ? 'label-success' : 'label-warning'; ?>" id="status-detail-cicilan"><?php echo !empty($data_cicilan['header']) ? 'Tersimpan' : 'Belum disimpan'; ?></span>
        </div>
    </div>
    <div class="panel-body">

        <div class="row installment-inputs">
            <div class="col-xs-12 col-sm-6 col-md-4">
                <div class="form-group">
                    <label for="tenor">Tenor (bulan) <span class="text-danger">*</span></label>
                    <input type="number" 
                        <?php echo !empty($data_cicilan['header']) ? 'disabled' : ''; ?>  
                        id="tenor" 
                        class="form-control" 
                        min="1" 
                        max="600" 
                        value="<?php echo $data_cicilan['header']['tenor_bulan'] ?? ''?>">
                </div>
            </div>

            <div class="col-xs-12 col-sm-6 col-md-4">
                <div class="form-group">
                    <label for="bunga_flat">Bunga flat (% per tahun) <span class="text-danger">*</span></label>
                    <input type="number"  <?php echo !empty($data_cicilan['header']) ? 'disabled' : ''; ?> id="bunga_flat"  class="form-control"  min="0"  max="100"  step="0.01" value="<?php echo $data_cicilan['header']['bunga_flat_persen'] ?? ''?>">
                </div>
            </div>

            <div class="col-xs-12 col-sm-6 col-md-4">
                <div class="form-group">
                    <label for="tgl_jt">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                    <div class="input-group date" id="tgl_jt_picker">
                        <input type="text" id="tgl_jt"  class="form-control" placeholder="Pilih Tanggal" style="caret-color:transparent; background-color:<?php echo !empty($data_cicilan['header']) ? '#ecebeb' : '#fff'; ?>"  <?php echo !empty($data_cicilan['header']) ? 'disabled' : ''; ?>   value="<?php echo $data_cicilan['header']['tanggal_jt'] ?? ''; ?>" onkeydown="return false;" onpaste="return false;" ondrop="return false;" autocomplete="off">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="cicilan-area">
            
        </div>
        
        <br>
        <!-- <p id="cicilan-selisih-pembulatan" class="text-muted installment-rounding-note">< ?php echo $cicilanTersimpan ? 'Selisih pembulatan terhadap A/R: ' . number_format((float) $cicilan['selisih_pembulatan'], 2, ',', '.') . '.' : ''; ?></p> -->
    
        <div id="button-cicilan" style="margin-top:10px;">
            <?php if (!empty($data_cicilan['header'])){?>
                <button type="button" id="edit-detail-cicilan" onclick="pba.edit_leasing(this, event)" class="btn btn-warning">
                    Edit Detail Cicilan 
                </button>

                <button type="button" id="edit-detail-cicilan" onclick="pba.hapus_leasing(this, event)" class="btn btn-danger">
                    Hapus Detail Cicilan 
                </button>
            <?php } else { ?>
                <button type="button" id="simpan-detail-cicilan" class="btn btn-primary" 
                    data-can-save="1">
                    Simpan Detail Cicilan
                </button>
            <?php } ?>
        </div>
        
        

        
       

    </div>
</div>