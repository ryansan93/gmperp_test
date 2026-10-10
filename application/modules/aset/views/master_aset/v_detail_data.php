<style>
    .detail-wrap {
        max-width: 960px;
        margin: 0 auto;
        font-family: Arial, sans-serif;
    }

    .detail-wrap .detail-grid {
        display: flex;
        flex-wrap: wrap;
    }

    .detail-wrap .detail-col {
        width: 50%;
        box-sizing: border-box;
        padding-right: 15px;
    }

    .detail-wrap .detail-col:nth-child(even) {
        padding-right: 0;
        padding-left: 15px;
    }

    .detail-wrap .detail-row {
        display: flex;
        align-items: center;
        min-height: 38px;
        padding: 6px 0;
        font-size: 13px;
    }

    .detail-wrap .detail-label {
        width: 160px;
        font-weight: bold;
        color: #555;
        flex-shrink: 0;
    }

    .detail-wrap .detail-value {
        color: #141414;
        flex-grow: 1;
        overflow-wrap: anywhere;
    }

    @media (max-width: 600px) {
        .detail-wrap .detail-col {
            width: 100%;
            padding: 0;
        }
    }
</style>

<?php if (!empty($data)) : ?>
    <?php
        $escape = function ($value) {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        };
    ?>
    <div class="detail-wrap">
        <fieldset>
            <legend>Detail Data Aset</legend>

            <div class="detail-grid">
                <div class="detail-col">
                    <div class="detail-row">
                        <div class="detail-label">Kode Aset</div>
                        <div class="detail-value">: <?= $escape($data['kode_aset'] ?? '-') ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Kategori Aset</div>
                        <div class="detail-value">: <?= $escape($data['kategori_name'] ?? '-') ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Unit Aset</div>
                        <div class="detail-value">: <?= $escape($data['unit_pengguna'] ?? '-') ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Tanggal Perolehan</div>
                        <div class="detail-value">: <?= !empty($data['tgl_perolehan']) ? $escape(tglIndonesia($data['tgl_perolehan'], '-', ' ')) : '-' ?></div>
                    </div>
                </div>

                <div class="detail-col">
                    <div class="detail-row">
                        <div class="detail-label">Deskripsi Aset</div>
                        <div class="detail-value">: <?= $escape($data['deskripsi_aset'] ?? '-') ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Harga Beli</div>
                        <div class="detail-value">: Rp <?= isset($data['nilai_perolehan']) ? $escape(angkaRibuan($data['nilai_perolehan'])) : '-' ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Jenis Pembiayaan</div>
                        <div class="detail-value">: <?= $escape($data['nama_pembiayaan'] ?? '-') ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Dokumen Pendukung</div>
                        <div class="detail-value">:
                            <?php if (!empty($data['attachment'])) : ?>
                                <a href="<?= $escape(base_url('uploads/aset/' . $data['attachment'])) ?>" target="_blank" rel="noopener noreferrer">Lihat / Unduh Dokumen</a>
                            <?php else : ?>
                                -
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
<?php else : ?>
    <div class="alert alert-warning">Data aset tidak ditemukan.</div>
<?php endif; ?>
