<fieldset>
    <legend><?php echo isset($data['id']) ? 'Ubah' : 'Tambah'; ?> Jenis Pembiayaan</legend>
    <div class="row" style="padding:20px;">
        <div class="col-xs-12">
            <form class="form-horizontal" onsubmit="return false;">
                <input type="hidden" id="id_pembiayaan" value="<?php echo isset($data['id']) ? (int) $data['id'] : ''; ?>">

                <div class="form-group">
                    <label for="kode_pembiayaan">Kode Pembiayaan</label>
                    <input type="text" class="form-control" id="kode_pembiayaan" readonly value="<?php echo isset($data['kode_pembiayaan']) ? htmlspecialchars($data['kode_pembiayaan']) : '(otomatis)'; ?>">
                    <small class="text-muted">Kode dibuat otomatis berurutan (PMB01, PMB02, dan seterusnya).</small>
                </div>

                <div class="form-group">
                    <label for="nama_pembiayaan">Jenis Pembiayaan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_pembiayaan" maxlength="100" required value="<?php echo isset($data['nama_pembiayaan']) ? htmlspecialchars($data['nama_pembiayaan']) : ''; ?>" placeholder="Contoh: Cash atau Kredit">
                </div>

                <button type="button" class="btn btn-primary" onclick="<?php echo isset($data['id']) ? 'mp.edit_data()' : 'mp.save_data()'; ?>">
                    <i class="fa fa-save"></i> Simpan
                </button>
                <button type="button" class="btn btn-default" onclick="$('a[href=\'#history\']').trigger('click');">Batal</button>
            </form>
        </div>
    </div>
</fieldset>
