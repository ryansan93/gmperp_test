<div class="col-xs-12" style="padding:0;">
    <form class="form-horizontal" role="form" onsubmit="return false;">
        <input type="hidden" id="id_pembiayaan_aset" value="<?php echo isset($data['id']) ? (int) $data['id'] : ''; ?>">

        <fieldset>
            <legend>Detail Aset</legend>
            <div class="row" style="margin:0 -6px;">
                <div class="col-xs-12" style="padding:0 6px; margin-bottom:15px;">
                    <label for="kode_aset" style="display:block; font-weight:600; margin-bottom:6px;">
                        Pilih Aset <span class="text-danger">*</span>
                    </label>
                    <select id="kode_aset" class="form-control" required>
                        <option value="">-- Pilih Aset --</option>
                        <?php foreach ($aset as $row) : ?>
                            <?php
                                $kodeAset = trim((string) $row['kode_aset']);
                                $selected = isset($data['kode_aset'])
                                    && $data['kode_aset'] === $kodeAset;
                            ?>
                            <option
                                value="<?php echo htmlspecialchars($kodeAset); ?>"
                                data-kode="<?php echo htmlspecialchars($kodeAset); ?>"
                                data-kategori="<?php echo htmlspecialchars($row['kategori_name'] ?? '-'); ?>"
                                data-unit="<?php echo htmlspecialchars($row['unit_pengguna'] ?? '-'); ?>"
                                data-harga="<?php echo htmlspecialchars(number_format((float) $row['nilai_perolehan'], 0, ',', '.')); ?>"
                                data-jenis="<?php echo htmlspecialchars($row['nama_pembiayaan'] ?? ''); ?>"
                                data-deskripsi="<?php echo htmlspecialchars($row['deskripsi_aset'] ?? ''); ?>"
                                <?php echo $selected ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($kodeAset . ' - ' . ($row['deskripsi_aset'] ?? '')); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row" id="asset-summary" style="display:none; margin:0 -6px;">
                <div class="col-xs-12 col-sm-6 col-md-4" style="padding:0 6px; margin-bottom:15px;">
                    <span style="display:block; font-weight:600; margin-bottom:6px;">Kode Aset</span>
                    <div id="summary-kode-aset">-</div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4" style="padding:0 6px; margin-bottom:15px;">
                    <span style="display:block; font-weight:600; margin-bottom:6px;">Kategori</span>
                    <div id="summary-kategori">-</div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4" style="padding:0 6px; margin-bottom:15px;">
                    <span style="display:block; font-weight:600; margin-bottom:6px;">Unit</span>
                    <div id="summary-unit">-</div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4" style="padding:0 6px; margin-bottom:15px;">
                    <span style="display:block; font-weight:600; margin-bottom:6px;">Harga Beli</span>
                    <div id="summary-harga">-</div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4" style="padding:0 6px; margin-bottom:15px;">
                    <span style="display:block; font-weight:600; margin-bottom:6px;">Jenis Pembiayaan</span>
                    <div id="summary-jenis">-</div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4" style="padding:0 6px; margin-bottom:15px;">
                    <span style="display:block; font-weight:600; margin-bottom:6px;">Deskripsi</span>
                    <div id="summary-deskripsi">-</div>
                </div>
            </div>
        </fieldset>

        <br>

        <fieldset>
            <legend>Data Pembiayaan Aset</legend>
            <div class="row" style="margin:0 -6px;">
                <div class="col-xs-12 col-sm-6" style="padding:0 6px; margin-bottom:15px;">
                    <label for="kode_pembiayaan" style="display:block; font-weight:600; margin-bottom:6px;">
                        Kode Pembiayaan
                    </label>
                    <input
                        type="text"
                        id="kode_pembiayaan"
                        class="form-control"
                        readonly
                        value="<?php echo isset($data['kode_pembiayaan']) ? htmlspecialchars($data['kode_pembiayaan']) : '(otomatis saat disimpan)'; ?>">
                </div>
                <div class="col-xs-12 col-sm-6" style="padding:0 6px; margin-bottom:15px;">
                    <label for="id_supplier" style="display:block; font-weight:600; margin-bottom:6px;">
                        Supplier <span class="text-danger">*</span>
                    </label>
                    <select id="id_supplier" class="form-control" required>
                        <option value="">-- Pilih Supplier --</option>
                        <?php foreach ($supplier as $row) : ?>
                            <?php
                                $idSupplier = (int) $row['id'];
                                $selected = isset($data['id_supplier'])
                                    && (int) $data['id_supplier'] === $idSupplier;
                            ?>
                            <option value="<?php echo $idSupplier; ?>" <?php echo $selected ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(($row['nomor'] ?? '') . ' - ' . ($row['nama'] ?? '')); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-6" id="leasing-group" style="display:none; padding:0 6px; margin-bottom:15px;">
                    <label for="id_leasing" style="display:block; font-weight:600; margin-bottom:6px;">
                        Leasing <span class="text-danger">*</span>
                    </label>
                    <select id="id_leasing" class="form-control">
                        <option value="">-- Pilih Leasing --</option>
                        <?php foreach ($supplier as $row) : ?>
                            <?php
                                $idLeasing = (int) $row['id'];
                                $selected = isset($data['id_leasing'])
                                    && (int) $data['id_leasing'] === $idLeasing;
                            ?>
                            <option value="<?php echo $idLeasing; ?>" <?php echo $selected ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(($row['nomor'] ?? '') . ' - ' . ($row['nama'] ?? '')); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Pilih jika jenis pembiayaan aset adalah Leasing.</small>
                </div>
            </div>
        </fieldset>

        <div class="form-group" style="margin-bottom:0; margin-left:0px;">
            <div style="padding-top:5px; display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <button
                        type="button"
                        class="btn btn-default"
                        onclick="$('a[href=\'#history\']').trigger('click');"
                        style="margin-right:8px;">
                        Batal
                    </button>
                    <?php if (isset($data['id'])) : ?>
                        <button type="button" class="btn btn-primary" onclick="pba.edit_data()">
                            <i class="fa fa-save"></i> Simpan Perubahan
                        </button>
                    <?php else : ?>
                        <button type="button" class="btn btn-primary" onclick="pba.save_data()">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>
</div>
