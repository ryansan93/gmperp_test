<style>
    /* Mencegah teks di dalam tabel turun ke baris baru */
    .table-area table th,
    .table-area table td {
        white-space: nowrap;
        vertical-align: middle; /* Agar teks tetap rapi di tengah secara vertikal */
    }
    
    /* Memungkinkan scroll horizontal jika tabel melebihi lebar layar */
    .table-area {
        overflow-x: auto;
    }
</style>

<div class="row content-panel">
    <div class="col-lg-12">
        <div class="panel-heading">
            <ul class="nav nav-tabs nav-justified">
                <li class="nav-item active">
                    <a class="nav-link active" data-toggle="tab" href="#history">Daftar Pembiayaan Aset</a>
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
                        <legend>Filter Data</legend>
                        <div class="row" style="padding:0 10px;">
                            <div class="col-xs-12 col-sm-4" style="padding:0 6px; margin-bottom:15px;">
                                <label for="filter_kategori_aset" style="display:block; font-weight:600; margin-bottom:6px;">Kategori Aset</label>
                                <select id="filter_kategori_aset" class="form-control" style="width:100%;">
                                    <option value="">All</option>
                                    <?php if (!empty($kategori_aset)) : ?>
                                        <?php foreach ($kategori_aset as $row) : ?>
                                            <?php
                                                $kode = trim((string) ($row['kategori_kode'] ?? ''));
                                                $nama = trim((string) ($row['kategori_name'] ?? ''));
                                            ?>
                                            <option value="<?php echo htmlspecialchars($nama); ?>">
                                                <?php echo htmlspecialchars($kode . ' - ' . $nama); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="padding:0 10px;">
                            <div class="col-xs-12" style="padding-left:6px;">
                                <button type="button" class="btn btn-primary" style="width:100px;" onclick="pba.filter_data(event)">Filter</button>
                                <button type="button" class="btn btn-default" style="width:100px;" onclick="pba.reset_filter(event)">Reset</button>
                            </div>
                        </div>
                    </fieldset>

                    <br>

                    <fieldset>
                        <legend>List Data</legend>
                        <div class="row" style="padding:0 10px;">
                            <div class="col-xs-12 col-sm-4" style="padding:0 6px; margin-bottom:15px;">
                                <label for="search_pembiayaan_aset" style="display:block; font-weight:600; margin-bottom:6px;">Cari Data</label>
                                <input type="text" class="form-control" id="search_pembiayaan_aset" placeholder="Cari kode pembiayaan, aset, supplier">
                            </div>
                        </div>
                        
                        <!-- Tambahkan style overflow-x: auto di sini -->
                        <div class="table-area" style="overflow-x: auto;">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:50px;">No</th>
                                        <th>Kode Pembiayaan</th>
                                        <th>Aset</th>
                                        <th>Kategori</th>
                                        <th>Unit</th>
                                        <th class="text-right">Harga Beli</th>
                                        <th>Supplier</th>
                                        <th>Leasing</th>
                                        <th class="text-center" style="width:180px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody><tr><td colspan="9" class="text-center">Memuat data...</td></tr></tbody>
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