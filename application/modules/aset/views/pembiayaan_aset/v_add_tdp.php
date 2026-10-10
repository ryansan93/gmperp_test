<div style="margin-bottom: 15px;">
    <h4 style="margin: 0 0 10px 0; font-size: 16px;">TDP</h4>
    <p class="text-muted" style="margin: 0; font-size: 13px;">Tanda jadi, dibayar ke supplier.</p>
</div>

<form id="form-modal-tdp">
     <div class="form-group">
        <label for="tgl_tdp">Tanggal TDP <span class="text-danger">*</span></label>
        <div class="input-group date" id="tgl_modal_tdp_picker">
            <input type="text" id="tgl_modal_tdp" class="form-control" placeholder="Pilih Tanggal" style="caret-color:transparent; background-color:#fff;" onkeydown="return false;" onpaste="return false;" ondrop="return false;" autocomplete="off">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
    </div>

    <div class="checkbox" style="margin: -5px 0 15px 0;">
        <label>
            <input type="checkbox" id="modal_tdp_pengurang_pokok" checked> 
            Sebagai pengurang pokok hutang
        </label>
    </div>

    <!-- <div class="form-group">
        <label>Deskripsi</label>
        <input type="text" id="modal_tdp_deskripsi" class="form-control" value="Tanda jadi" maxlength="255" placeholder="Deskripsi">
    </div> -->

    <div class="row" style="margin-bottom: 10px;">
        <div class="col-xs-7"></div>
        <div class="col-xs-4 text-right"><strong>Nominal</strong></div>
        <div class="col-xs-1"></div>
    </div>

    <div id="modal-tdp-rows">
        <div class="row modal-tdp-row" style="margin-bottom:8px;">
            <div class="col-xs-7">
                <input type="text" class="form-control modal-tdp-deskripsi" value="Tanda jadi" maxlength="255" placeholder="Deskripsi">
            </div>
            <div class="col-xs-4">
                <div class="input-group">
                    <span class="input-group-addon">Rp</span>
                    <input type="text" class="form-control text-right modal-tdp-nominal" placeholder="0" inputmode="numeric">
                </div>
            </div>
            <div class="col-xs-1">
                <button type="button" class="btn btn-danger btn-sm modal-tdp-remove" disabled><i class="fa fa-times"></i></button>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee;">
        <div class="col-xs-6"><strong>Total TDP</strong></div>
        <div class="col-xs-6 text-right"><strong id="modal-tdp-total">0</strong></div>
    </div>

    <div style="margin-top: 15px;">
        <button type="button" id="modal-add-tdp-row" class="btn btn-default btn-sm">
            <i class="fa fa-plus"></i> Tambah baris
        </button>
    </div>
</form>