<div class="row content-panel">
    <div class="col-lg-12">
        <div class="panel-heading">
            <ul class="nav nav-tabs nav-justified">
                <li class="nav-item active">
                    <a class="nav-link active" data-toggle="tab" href="#history">Daftar Jenis Pembiayaan</a>
                </li>
                <?php if (!empty($akses['a_submit'])) : ?>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#action">Tambah Data</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="panel-body">
            <div class="tab-content">
                <div id="history" class="tab-pane fade in active">
                    <fieldset>
                        <legend>Master Pembiayaan</legend>
                        <div class="table-area">
                            <table class="table table-bordered table-hover" id="table-pembiayaan">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:50px;">No</th>
                                        <th class="text-center">Kode Pembiayaan</th>
                                        <th>Jenis Pembiayaan</th>
                                        <th class="text-center" style="width:130px;">Jumlah Aset</th>
                                        <th class="text-center" style="width:110px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody><tr><td colspan="5" class="text-center">Memuat data...</td></tr></tbody>
                            </table>
                        </div>
                    </fieldset>
                </div>
                <div id="action" class="tab-pane fade">
                    <div id="tab-action-content"></div>
                </div>
            </div>
        </div>
    </div>
</div>
