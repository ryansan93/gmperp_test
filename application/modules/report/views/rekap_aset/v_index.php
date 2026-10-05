
    <div class="row content-panel">
        <div class="col-lg-12">
            <div class="panel-body ra-page">

                <div class="ra-stats">
                    <div class="ra-stat-card ra-stat-card--total">
                        <div class="ra-stat-tile"><i class="fa fa-file-text-o"></i></div>
                        <div>
                            <div class="ra-stat-value" id="stat-total"><?php echo $total_aset ?></div>
                            <div class="ra-stat-label">Total Aset</div>
                        </div>
                    </div>
                    <div class="ra-stat-card ra-stat-card--aktif">
                        <div class="ra-stat-tile"><i class="fa fa-bolt"></i></div>
                        <div>
                            <div class="ra-stat-value" id="stat-aktif"><?php echo $aset_aktif ?></div>
                            <div class="ra-stat-label">Aktif</div>
                        </div>
                    </div>
                    <div class="ra-stat-card ra-stat-card--selesai">
                        <div class="ra-stat-tile"><i class="fa fa-check"></i></div>
                        <div>
                            <div class="ra-stat-value" id="stat-selesai"><?php echo $aset_selesai ?></div>
                            <div class="ra-stat-label">Selesai</div>
                        </div>
                    </div>
                </div>

                <div class="ra-panel">
                    <div class="ra-panel-head">
                        <div class="ra-panel-icon"><i class="fa fa-sliders"></i></div>
                        <div class="ra-panel-title">Filter Data</div>
                    </div>
                    <div class="ra-panel-body">
                        <div class="ra-filter-grid">
                            <div class="ra-field">
                                <label for="filter_kategori_aset">Kategori Aset</label>
                                <select id="filter_kategori_aset" style="width:100%;">
                                    <option value="">Semua Jenis</option>
                                    <?php foreach($kategori_aset as $k){ ?>
                                        <option value="<?php echo $k['kategori_name']?>"><?php echo $k['kategori_name']?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <!-- <div class="ra-field">
                                <label for="filter_supplier">Supplier</label>
                                <select id="filter_supplier" style="width:100%;">
                                    <option value="">Semua Supplier</option>
                                </select>
                            </div> -->

                            <div class="ra-field">
                                <label for="filter_status">Status Aset</label>
                                <select id="filter_status" style="width:100%;">
                                    <option value="">Semua Status</option>
                                    <option value="belum_terproses">Belum Terproses</option>
                                    <option value="sudah_terproses">Sudah Terproses</option>
                                    <option value="belum_terima">Belum Diterima</option>
                                    <option value="sudah_terima">Sudah Diterima</option>
                                </select>
                            </div>
                        </div>

                        <div class="ra-filter-actions">
                            <button type="button" class="ra-btn ra-btn-primary" id="btn-filter" onclick="ra.filterData(event)"><i class="fa fa-filter"></i> Filter</button>
                            <button type="button" class="ra-btn ra-btn-ghost" id="btn-reset" onclick="ra.resetFilter(event)"><i class="fa fa-rotate-left"></i> Reset</button>
                            <button type="button" class="ra-btn ra-btn-outline-success" id="btn-export" onclick="ra.exportData(event)"><i class="fa fa-download"></i> Export Excel</button>
                        </div>
                    </div>
                </div>

                <div class="ra-panel">
                    <div class="ra-panel-head">
                        <div class="ra-panel-icon"><i class="fa fa-table"></i></div>
                        <div class="ra-panel-title">Rekap Aset</div>
                        <div class="ra-panel-sub" id="ra-row-count"></div>
                    </div>
                    <div class="ra-panel-body">
                        <div class="ra-table-toolbar">
                            <div class="ra-input-icon-wrap ra-table-search">
                                <i class="fa fa-search"></i>
                                <input type="text" class="ra-input ra-input-icon" id="search_aset" placeholder="Cari nama, no. sewa, no. kontrak">
                            </div>
                        </div>
                        <div class="ra-table-scroll">
                            <table class="ra-table" id="table-rekap-aset">
                                <thead>
                                    <tr>
                                        <th rowspan="2" width="4%">No</th>
                                        <th rowspan="2">Kode Asset</th>
                                        <th rowspan="2">Kategori Name</th>
                                        <th rowspan="2">Deskripsi Aset</th>
                                        <th rowspan="2">No. Faktur / Bukti Pembelian</th>
                                        <th rowspan="2">Unit Terdaftar</th>
                                        <th rowspan="2">Pengelompokan Aset</th>
                                        <th rowspan="2">Masa Manfaat (Komersial)</th>
                                        <th rowspan="2">Masa Manfaat (Fiskal)</th>
                                        <th rowspan="2" class="text-right">Nilai Perolehan</th>
                                        <th rowspan="2">Tgl. Perolehan</th>
                                        <th rowspan="2">Tgl. Penerimaan</th>
                                        <th rowspan="2">PIC Pengguna</th>
                                        <th rowspan="2">Lokasi Pengguna</th>
                                        <th colspan="2">Attachment</th>
                                        <th colspan="4">Termin</th>
                                        <th colspan="6">Komersial</th>
                                        <th colspan="6">Fiskal</th>
                                    </tr>
                                    <tr>
                                        <th>Pembelian</th>
                                        <th>Penerimaan</th>

                                        <th>DP</th>
                                        <th>Cicilan</th>
                                        <th>Cicilan Diproses <br> (Sudah / Belum)</th>
                                        <th>Penyelesaian</th>

                                        <th>Periode Tagihan</th>
                                        <th>Nominal Cicilan</th>
                                        <th>Sudah Terbayar</th>
                                        <th>Belum Terbayar</th>
                                        <th>Penyusutan Diproses <br> (Sudah / Belum)</th>
                                        <th>Penyelesaian</th>

                                        <th>Periode Tagihan</th>
                                        <th>Nominal Cicilan </th>
                                        <th>Sudah Terbayar</th>
                                        <th>Belum Terbayar</th>
                                        <th>Penyusutan Diproses <br> (Sudah / Belum)</th>
                                        <th>Penyelesaian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="32" class="ra-empty"><i class="fa fa-spinner fa-spin"></i> Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
