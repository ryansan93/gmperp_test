<?php
    $kodePembiayaan = htmlspecialchars($pembiayaan['kode_pembiayaan'] ?? '');
    $kodeAset = htmlspecialchars($aset['kode_aset'] ?? '');
    $deskripsiAset = htmlspecialchars($aset['deskripsi_aset'] ?? '');
    $kategori = htmlspecialchars($aset['kategori_name'] ?? '-');
    $unit = htmlspecialchars($aset['unit_pengguna'] ?? '-');
    $hargaBeli = number_format((float) ($aset['nilai_perolehan'] ?? 0), 0, ',', '.');
    $supplierNama = htmlspecialchars(($supplier->nama ?? '') . ' - ' . ($supplier->nomor ?? ''));
    $leasingNama = htmlspecialchars(($leasing->nama ?? '') . ' - ' . ($leasing->nomor ?? ''));
    $tdp = $tdp ?? [];
    $canSubmitTdp = !empty($can_submit_tdp);
    $tdpLocked = !empty($tdp_locked);
    $canEditTdp = !empty($can_edit_tdp);
    $canDeleteTdp = !empty($can_delete_tdp);
    $dp = $dp ?? [];
    $cicilan = $cicilan ?? null;
    $cicilanDetail = $cicilan_detail ?? [];
    $dpTotal = (float) ($dp_total ?? 0);
    $dpUangMukaTotal = (float) ($dp_uang_muka_total ?? 0);
    $canSubmitDp = !empty($can_submit_dp);
    $canEditDp = !empty($can_edit_dp);
    $canDeleteDp = !empty($can_delete_dp);
    $dpLocked = !empty($dp_locked);
    $tdpPengurangPokokTotal = 0;
    foreach ($tdp as $row) {
        if (!empty($row['is_pengurang_pokok'])) {
            $tdpPengurangPokokTotal += (float) ($row['nominal'] ?? 0);
        }
    }
    $tanggalPembiayaan = trim((string) ($pembiayaan['tgl_pembiayaan'] ?? ''));
    $tanggalPembiayaan = $tanggalPembiayaan !== '' ? substr($tanggalPembiayaan, 0, 10) : '';
    $canSaveCicilan = !empty($can_submit_dp) && empty($dp_locked);
    $cicilanTersimpan = !empty($cicilan);
    $tanggalDasarCicilan = '';
    foreach ($dp as $row) {
        if (
            strtoupper((string) ($row['jenis_komponen'] ?? '')) === 'ANGSURAN'
            && (int) ($row['angsuran_ke'] ?? 0) === 1
            && !empty($row['jatuh_tempo'])
        ) {
            $tanggalDasarCicilan = (string) $row['jatuh_tempo'];
            break;
        }
    }

    $tdpInputDisabled = !empty($pembiayaan) ? 'disabled' : '';
?>

<fieldset>
    <legend>Proses Pembiayaan Aset - Leasing</legend>
    <div class="row" style="padding:0 15px;">
        <div class="col-xs-12">
            <div class="clearfix" style="margin-bottom:10px;">
                <strong><?php echo $kodePembiayaan; ?></strong>
                <span class="pull-right">
                    <span class="label label-info">Leasing</span>
                </span>
            </div>
            <p class="text-muted">
                <?php echo $kodeAset; ?> - <?php echo $deskripsiAset; ?> |
                <?php echo $kategori; ?> | Harga beli <?php echo $hargaBeli; ?>
                <br>
                Supplier <?php echo $supplierNama; ?> | Leasing <?php echo $leasingNama; ?>
            </p>
          
            <?php if ($tdpLocked) : ?>
                <div class="alert alert-warning">
                    TDP dikunci karena aset sudah masuk tahap Penerimaan Aset. Data tidak dapat ditambah, diubah, atau dihapus.
                </div>
            <?php endif; ?>

            <?php if(empty($tdp) && $cicilanTersimpan){?>

                <div class="panel panel-default installment-panel">
                    <div class="panel-heading">
                        <strong>TDP</strong>
                        <input type="hidden" id="kode_pembiayaan_tdp" value="<?php echo $kodePembiayaan; ?>">
                    </div>
                    <div class="panel-body">
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            <span>Pembiayaan ini tanpa TDP, Pokok hutang hanya dikurangi uang muka DP.</span>
                            <button type="button" class="btn btn-default" style="width:80px" onclick="pba.add_tdp(this, event)">Ada TDP</button>
                        </div>
                    </div>
                </div>

            <?php } else {?>

                <div class="panel panel-default installment-panel">
                    <div class="panel-heading">
                        <strong>TDP</strong>
                        <span class="label <?php echo !empty($tdp) ? 'label-success' : 'label-warning'; ?> pull-right">
                            <?php echo !empty($tdp) ? 'Tersimpan' : 'Belum diajukan'; ?>
                        </span>
                    </div>
                    <div class="panel-body">
                        <p class="text-muted">Tanda jadi, dibayar ke supplier.</p>
                        <form id="form-tdp">
                            <input type="hidden" id="kode_pembiayaan_tdp" value="<?php echo $kodePembiayaan; ?>">
                            <div class="form-group">
                                <label for="tgl_tdp">Tanggal TDP <span class="text-danger">*</span></label>
                                <div class="input-group date" id="tgl_tdp_picker">
                                    <input
                                        type="text"
                                        id="tgl_tdp"
                                        class="form-control"
                                        placeholder="Pilih Tanggal"
                                        style="caret-color:transparent; background-color:#fff;"
                                        onkeydown="return false;"
                                        onpaste="return false;"
                                        ondrop="return false;"
                                        autocomplete="off">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        <div class="checkbox" style="margin-top:-5px; margin-bottom:15px;">
                                <label>
                                    <input type="checkbox" id="tdp_pengurang_pokok" checked <?php echo $tdpInputDisabled; ?>> 
                                    Sebagai pengurang pokok hutang
                                </label>
                            </div>
                            <div class="row">
                                <div class="col-xs-7"><label>Deskripsi</label></div>
                                <div class="col-xs-4 text-right"><label>Nominal</label></div>
                                <div class="col-xs-1"></div>
                            </div>
                            <div id="tdp-rows">
                                <div class="row tdp-row" style="margin-bottom:8px;">
                                    <div class="col-xs-7">
                                        <input type="text" class="form-control tdp-deskripsi" maxlength="255" value="Tanda jadi" placeholder="Deskripsi">
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Rp</span>
                                            <input type="text" class="form-control text-right tdp-nominal" inputmode="numeric" autocomplete="off" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-xs-1">
                                        <button type="button" class="btn btn-danger btn-sm tdp-remove-row" title="Hapus baris" disabled><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6"><strong>Total TDP</strong></div>
                                <div class="col-xs-6 text-right"><strong id="tdp-total">0,00</strong></div>
                            </div>
                            <hr>
                            <button type="button" id="tdp-add-row" class="btn btn-default" <?php echo $canSubmitTdp && !$tdpLocked ? '' : 'disabled'; ?>>+ Tambah baris</button>
                            <div style="margin-top:10px;">
                                <button type="submit" id="tdp-save" class="btn btn-primary" <?php echo $canSubmitTdp && !$tdpLocked ? '' : 'disabled'; ?>>Simpan TDP</button>
                            </div>
                        </form>

                        <hr>
                        <h4>Riwayat TDP</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="background-color: #f5f5f5;">Tanggal</th>
                                        <th style="background-color: #f5f5f5;">Deskripsi</th>
                                        <th style="background-color: #f5f5f5;" class="text-right">Nominal</th>
                                        <th style="background-color: #f5f5f5;">Pengurang pokok</th>
                                        <th style="background-color: #f5f5f5;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tdp-history">
                                    <?php if (!empty($tdp)) : ?>
                                        <?php foreach ($tdp as $item) : ?>
                                            <?php
                                                $tanggalTdp = $item['tanggal'] ?? null;
                                                if ($tanggalTdp instanceof \DateTimeInterface) {
                                                    $tanggalTdp = $tanggalTdp->format('d/m/Y');
                                                } else {
                                                    $tanggalTdpTimestamp = strtotime((string) $tanggalTdp);
                                                    $tanggalTdp = $tanggalTdpTimestamp
                                                        ? date('d/m/Y', $tanggalTdpTimestamp)
                                                        : '';
                                                }
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($tanggalTdp); ?></td>
                                                <td><?php echo htmlspecialchars($item['deskripsi'] ?? '-'); ?></td>
                                                <td class="text-right">Rp <?php echo number_format((float) $item['nominal'], 0, ',', '.'); ?></td>
                                                <td><?php echo !empty($item['is_pengurang_pokok']) ? 'Ya' : 'Tidak'; ?></td>
                                                <td class="text-center">
                                                    <?php if ($tdpLocked) : ?>
                                                        <button type="button" class="btn btn-warning btn-xs" disabled title="TDP terkunci setelah aset masuk Penerimaan Aset"><i class="fa fa-pencil"></i> Edit</button>
                                                        <button type="button" class="btn btn-danger btn-xs" disabled title="TDP terkunci setelah aset masuk Penerimaan Aset"><i class="fa fa-trash"></i> Hapus</button>
                                                    <?php else : ?>
                                                        <?php if ($canEditTdp) : ?>
                                                            <button
                                                                type="button"
                                                                class="btn btn-warning btn-xs"
                                                                data-id="<?php echo (int) $item['id']; ?>"
                                                                data-tanggal="<?php echo htmlspecialchars($item['tanggal'], ENT_QUOTES); ?>"
                                                                data-deskripsi="<?php echo htmlspecialchars($item['deskripsi'] ?? '', ENT_QUOTES); ?>"
                                                                data-nominal="<?php echo htmlspecialchars((string) $item['nominal'], ENT_QUOTES); ?>"
                                                                data-pengurang-pokok="<?php echo !empty($item['is_pengurang_pokok']) ? '1' : '0'; ?>"
                                                                onclick="pba.edit_tdp(this)"><i class="fa fa-pencil"></i> Edit</button>
                                                        <?php endif; ?>
                                                        <?php if ($canDeleteTdp) : ?>
                                                            <button
                                                                type="button"
                                                                class="btn btn-danger btn-xs"
                                                                data-id="<?php echo (int) $item['id']; ?>"
                                                                onclick="pba.delete_tdp(this)"><i class="fa fa-trash"></i> Hapus</button>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr><td colspan="5" class="text-center text-muted">Belum ada TDP tersimpan.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <?php } ?>

            <div class="panel panel-default installment-panel">
                <div class="panel-heading">
                    <strong>DP</strong>
                    <span class="label <?php echo !empty($dp) ? 'label-success' : 'label-warning'; ?> pull-right">
                        <?php echo !empty($dp) ? 'Tersimpan' : 'Belum diajukan'; ?>
                    </span>
                </div>
                <div class="panel-body">
                    <p class="text-muted">Uang muka, biaya awal, dan angsuran yang dibayar di muka.</p>
                    <?php if ($dpLocked) : ?>
                        <div class="alert alert-warning">DP dikunci karena pembiayaan sudah selesai atau aset sudah masuk tahap Penerimaan Aset.</div>
                    <?php endif; ?>
                    <form id="form-dp">
                    <input type="hidden" id="kode_pembiayaan_dp" value="<?php echo $kodePembiayaan; ?>">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="tgl_dp">Tanggal DP</label>
                                <div class="input-group date" id="tgl_dp_picker">
                                    <input
                                        type="text"
                                        id="tgl_dp"
                                        class="form-control"
                                        placeholder="Pilih Tanggal"
                                        style="caret-color:transparent; background-color:#fff;"
                                        onkeydown="return false;"
                                        onpaste="return false;"
                                        ondrop="return false;"
                                        value="<?php echo !empty($dp[0]['tanggal_dp']) ? htmlspecialchars($dp[0]['tanggal_dp'], ENT_QUOTES) : ''; ?>"
                                        autocomplete="off">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Diajukan ke</label>
                                <input type="text" class="form-control" value="<?php echo $supplierNama; ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-8"><label>Jenis Komponen</label></div>
                        <div class="col-xs-3 text-right"><label>Nominal</label></div>
                        <div class="col-xs-1"></div>
                    </div>
                    <div id="dp-rows">
                        <?php
                            $dpRegularRows = array_values(array_filter($dp, function ($row) {
                                return strtoupper((string) ($row['jenis_komponen'] ?? '')) !== 'ANGSURAN';
                            }));
                        ?>
                        <?php if (!empty($dpRegularRows)) : ?>
                            <?php foreach ($dpRegularRows as $row) : ?>
                                <?php $canEditRow = !$dpLocked && $canEditDp; ?>
                                <div class="row dp-row" style="margin-bottom:8px;">
                                    <input type="hidden" class="dp-id" value="<?php echo (int) $row['id']; ?>">
                                    <div class="col-xs-8">
                                        <input type="text" class="form-control dp-komponen" value="<?php echo htmlspecialchars((string) $row['jenis_komponen'], ENT_QUOTES); ?>" placeholder="Jenis komponen (contoh: UANG_MUKA)" <?php echo $canEditRow ? '' : 'disabled'; ?>>
                                    </div>
                                    <div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right dp-nominal" inputmode="numeric" autocomplete="off" value="<?php echo htmlspecialchars(number_format((float) $row['nominal'], 0, ',', '.'), ENT_QUOTES); ?>" placeholder="0" <?php echo $canEditRow ? '' : 'disabled'; ?>></div></div>
                                    <div class="col-xs-1 text-right"><?php if (!$dpLocked && $canDeleteDp) : ?><button type="button" class="btn btn-danger btn-sm dp-delete-row" data-id="<?php echo (int) $row['id']; ?>" title="Hapus"><i class="fa fa-trash"></i></button><?php endif; ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php elseif ($canSubmitDp && !$dpLocked) : ?>
                            <div class="row dp-row" style="margin-bottom:8px;">
                                <input type="hidden" class="dp-id" value="">
                                <div class="col-xs-8">
                                    <div style="padding:6px 12px; background-color:#f5f5f5; border:1px solid #ccc; border-radius:4px;">
                                        Uang Muka <span class="label label-info">Dasar Pokok</span>
                                    </div>
                                </div>
                                <div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right dp-nominal" inputmode="numeric" autocomplete="off" placeholder="0"></div></div>
                                <div class="col-xs-1"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div id="dp-installment-rows">
                        <?php foreach ($dp as $row) : ?>
                            <?php if (strtoupper((string) ($row['jenis_komponen'] ?? '')) === 'ANGSURAN') : ?>
                                <?php $canEditRow = !$dpLocked && $canEditDp; ?>
                                <div class="row dp-installment-row" style="margin-bottom:8px;">
                                    <input type="hidden" class="dp-id" value="<?php echo (int) $row['id']; ?>">
                                    <div class="col-xs-2"><label style="padding-top:7px;">Angsuran ke</label></div>
                                    <div class="col-xs-2"><input type="number" class="form-control dp-angsuran-ke" min="1" value="<?php echo htmlspecialchars((string) ($row['angsuran_ke'] ?? ''), ENT_QUOTES); ?>" placeholder="Ke" <?php echo $canEditRow ? '' : 'disabled'; ?>></div>
                                    <div class="col-xs-3"><div class="input-group date dp-jatuh-tempo-picker"><input type="text" class="form-control dp-jatuh-tempo" autocomplete="off" value="<?php echo htmlspecialchars((string) ($row['jatuh_tempo'] ?? ''), ENT_QUOTES); ?>" placeholder="Jatuh tempo" <?php echo $canEditRow ? '' : 'disabled'; ?>><span class="input-group-addon"><i class="fa fa-calendar"></i></span></div></div>
                                    <div class="col-xs-3"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right dp-nominal" inputmode="numeric" autocomplete="off" value="<?php echo htmlspecialchars(number_format((float) $row['nominal'], 0, ',', '.'), ENT_QUOTES); ?>" placeholder="0" <?php echo $canEditRow ? '' : 'disabled'; ?>></div></div>
                                    <div class="col-xs-2 text-right"><?php if (!$dpLocked && $canDeleteDp) : ?><button type="button" class="btn btn-danger btn-sm dp-delete-row" data-id="<?php echo (int) $row['id']; ?>" title="Hapus angsuran"><i class="fa fa-trash"></i></button><?php endif; ?></div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="row">
                        <div class="col-xs-6"><strong>Total DP</strong></div>
                        <div class="col-xs-6 text-right"><strong id="dp-total"><?php echo number_format($dpTotal, 0, ',', '.'); ?></strong></div>
                    </div>
                    <hr>
                    <button type="button" id="dp-add-row" class="btn btn-default" <?php echo $canSubmitDp && !$dpLocked ? '' : 'disabled'; ?>>+ Tambah baris</button>
                    <button type="button" id="dp-add-installment" class="btn btn-default" <?php echo $canSubmitDp && !$dpLocked ? '' : 'disabled'; ?>>+ Tambah angsuran</button>
                    <div style="margin-top:10px;">
                        <button type="submit" id="dp-save" class="btn btn-primary" <?php echo ($canEditDp && !empty($dp) || $canSubmitDp) && !$dpLocked ? '' : 'disabled'; ?>>Simpan DP</button>
                    </div>
                    </form>
                </div>
            </div>

            <div class="panel panel-default installment-panel"
                id="detail-cicilan"
                data-harga-beli="<?php echo htmlspecialchars((string) ($aset['nilai_perolehan'] ?? 0), ENT_QUOTES); ?>"
                data-tdp-pengurang="<?php echo htmlspecialchars((string) $tdpPengurangPokokTotal, ENT_QUOTES); ?>"
                data-dp-uang-muka="<?php echo htmlspecialchars((string) $dpUangMukaTotal, ENT_QUOTES); ?>"
                data-biaya-penambah="<?php echo htmlspecialchars((string) ($biaya_penambah_total ?? 0), ENT_QUOTES); ?>"
                data-tanggal-dasar="<?php echo htmlspecialchars($tanggalDasarCicilan, ENT_QUOTES); ?>"
                data-tanggal-pembiayaan="<?php echo htmlspecialchars($tanggalPembiayaan, ENT_QUOTES); ?>"
                data-cicilan-disimpan="<?php echo $cicilanTersimpan ? '1' : '0'; ?>">
                <div class="panel-heading">
                    <div class="installment-heading">
                        <div>
                            <strong>Detail cicilan</strong>
                            <span class="label label-default installment-rate-tag">Bunga flat</span>
                        </div>
                        <span class="label <?php echo $cicilanTersimpan ? 'label-success' : 'label-warning'; ?>" id="status-detail-cicilan"><?php echo $cicilanTersimpan ? 'Tersimpan' : 'Belum disimpan'; ?></span>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row installment-inputs">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="tenor">Tenor (bulan) <span class="text-danger">*</span></label>
                                <input type="number" id="tenor" class="form-control" min="1" max="600" value="<?php echo htmlspecialchars((string) ($cicilan['tenor_bulan'] ?? ''), ENT_QUOTES); ?>" <?php echo $cicilanTersimpan ? 'disabled' : ''; ?>>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="bunga_flat">Bunga flat (% per tahun) <span class="text-danger">*</span></label>
                                <input type="number" id="bunga_flat" class="form-control" min="0" max="100" step="0.01" value="<?php echo htmlspecialchars((string) ($cicilan['bunga_flat_persen'] ?? ''), ENT_QUOTES); ?>" <?php echo $cicilanTersimpan ? 'disabled' : ''; ?>>
                            </div>
                        </div>
                    </div>
                    <div class="installment-monthly">
                        <h4 id="cicilan-nominal-bulanan"><?php echo $cicilanTersimpan ? number_format((float) $cicilan['angsuran_per_bulan'], 2, ',', '.') : '-'; ?></h4>
                        <p class="text-muted">Angsuran per bulan, <span id="cicilan-tenor-label"><?php echo $cicilanTersimpan ? (int) $cicilan['tenor_bulan'] : '-'; ?></span> bulan</p>
                    </div>
                    <div class="installment-breakdown">
                        <div class="installment-summary-row">
                            <span>Harga Beli (Aset)</span>
                            <span id="cicilan-harga-beli">
                                <?php echo $hargaBeli; ?>
                            </span>
                        </div>

                        <div class="installment-summary-row">
                            <span>TDP pengurang pokok</span>
                            <span id="cicilan-tdp-pengurang">(<?php echo number_format($tdpPengurangPokokTotal, 0, ',', '.'); ?>)</span>
                        </div>

                        <div class="installment-summary-row">
                            <span>DP (uang muka)</span>
                            <span id="cicilan-dp-uang-muka" dp="<?= (int) $dpUangMukaTotal ?>">(<?php echo number_format($dpUangMukaTotal, 0, ',', '.'); ?>)</span>
                        </div>
                        
                        <?php  $pokok_hutang = $aset['nilai_perolehan'] - $dpUangMukaTotal; ?>
                        <div class="installment-summary-row">
                            <span>Pokok Hutang </span>
                            <span id="cicilan-pokok-hutang"><?php echo  number_format((float) $pokok_hutang, 0, ',', '.'); ?></span>
                        </div>

                        <div class="installment-summary-row">
                            <span>Biaya Prepaid </span>
                            <span id="cicilan-biaya-prepaid"><?php echo  number_format((float) $biaya_prepaid, 0, ',', '.'); ?></span>
                        </div>

                        <?php  $total_perolehan = $pokok_hutang + $biaya_prepaid + $dpUangMukaTotal; ?>
                        <div class="installment-summary-row">
                            <span>Total Perolehan </span>
                            <span id="cicilan-total-perolehan"><?php echo  number_format((float) $total_perolehan, 0, ',', '.'); ?></span>
                        </div>

                        <div class="installment-summary-row">
                            <span>Bunga flat</span>
                            <span id="cicilan-total-bunga"><?php echo $cicilanTersimpan ? number_format((float) $cicilan['total_bunga'], 0, ',', '.') : '0'; ?></span>
                        </div>

                        <div class="installment-summary-row installment-total">
                            <strong>Total A/R</strong><strong id="cicilan-total-ar">
                                <?php echo $cicilanTersimpan ? number_format((float) $cicilan['total_ar'], 0, ',', '.') : '0'; ?>
                            </strong>
                        </div>
                    </div>
                    <div class="table-responsive installment-schedule-wrap">
                        <table class="table table-bordered installment-schedule">
                            <thead>
                                <tr>
                                    <th style="background-color: #f5f5f5; width: 50px;">Ke</th>
                                    <th style="background-color: #f5f5f5;">Jatuh tempo</th>
                                    <th style="background-color: #f5f5f5; width: 100px;" class="text-center">Jenis Cicilan</th> 
                                    <th style="background-color: #f5f5f5; width: 120px;" class="text-right">Pokok</th>
                                    <th style="background-color: #f5f5f5; width: 120px;" class="text-right">Bunga</th>
                                    <th style="background-color: #f5f5f5; width: 120px;" class="text-right">Cicilan</th>
                                    <th style="background-color: #f5f5f5; width: 120px;" class="text-right">Sisa Pokok Hutang</th>
                                    <th style="background-color: #f5f5f5; width: 80px;" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="jadwal-cicilan">
                                <?php if ($cicilanTersimpan && !empty($cicilanDetail)) { ?>
                                    <?php 
                                        $totalAr = floatval($cicilan['total_ar'] ?? 0);
                                        $saldoBerjalan = $totalAr;
                                    ?>

                                    <?php 
                                        $total_pokok = 0;
                                        $total_bunga = 0;
                                        $total_cicilan = 0;
                                    ?>
                                    <?php foreach ($cicilanDetail as $detail) : ?>
                                        <?php 
                                            $statusDp = $detail['status'] == 1 ? 'Lunas' : 'Pending'; 
                                            $jenis = strtoupper(trim($detail['jenis_cicilan'] ?? 'Cicilan'));
                                            $labelJenis = ($jenis === 'DP') ? 'label-info' : 'label-default';
                                            
                                            $pokok = isset($detail['pokok']) ? floatval($detail['pokok']) : 0;
                                            $bunga = isset($detail['bunga']) ? floatval($detail['bunga']) : 0;
                                            $nominal = floatval($detail['nominal'] ?? 0);

                                            $total_pokok += $pokok;
                                            $total_bunga += $bunga;
                                            $total_cicilan += $nominal;
     
                                            if ($pokok == 0 && $bunga == 0) {
                                                $pokok = $nominal;
                                                $bunga = 0;
                                            }
                                            
                                            $saldoBerjalan -= $pokok;
                                            if ($saldoBerjalan < 0) $saldoBerjalan = 0;
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo (int) $detail['angsuran_ke']; ?></td>
                                            <td><?php echo htmlspecialchars(tglIndonesia($detail['jatuh_tempo'], '-', ' ', true), ENT_QUOTES); ?></td>
                                            <td class="text-center">
                                                <span class="label <?php echo $labelJenis; ?>"><?php echo htmlspecialchars($jenis); ?></span>
                                            </td>
                                            <td class="text-right"><?php echo number_format($pokok, 0, ',', '.'); ?></td>
                                            <td class="text-right"><?php echo number_format($bunga, 0, ',', '.'); ?></td>
                                            <td class="text-right"><?php echo number_format($nominal, 0, ',', '.'); ?></td>
                                            <td class="text-right"><?php echo number_format($detail['sisa_pokok_hutang'], 0, ',', '.'); ?></td>
                                            <td class="text-center">
                                                <span class="label <?php echo $detail['status'] ? 'label-success' : 'label-warning'; ?>">
                                                    <?php echo htmlspecialchars($statusDp); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                        <tr class="info">
                                            <td colspan="2"></td>
                                            <td class="text-center"><b>Total</b></td>
                                            <td class="text-right"><?php echo number_format($total_pokok, 0, ',' , '.') ?></td>
                                            <td class="text-right"><?php echo number_format($total_bunga, 0, ',' , '.') ?></td>
                                            <td class="text-right"><?php echo number_format($total_cicilan, 0, ',', '.'); ?></td>
                                            <td class="text-right">-</td>
                                            <td class="text-center">-</td>
                                        </tr>
                                <?php } else { ?>
                                    <tr><td colspan="8" class="text-center text-muted">Jadwal cicilan akan tampil setelah tenor dan bunga diisi.</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <!-- <p id="cicilan-selisih-pembulatan" class="text-muted installment-rounding-note">< ?php echo $cicilanTersimpan ? 'Selisih pembulatan terhadap A/R: ' . number_format((float) $cicilan['selisih_pembulatan'], 2, ',', '.') . '.' : ''; ?></p> -->
                    
                    
                    <button type="button" id="simpan-detail-cicilan" class="btn btn-primary" 
                        data-can-save="<?php echo $canSaveCicilan ? '1' : '0'; ?>" disabled>
                        Simpan Detail Cicilan
                    </button>

                    <!-- < ?php if ($cicilanTersimpan == 1){?>
                        <button type="button" id="edit-detail-cicilan" onclick="pba.edit_leasing(this, event)" class="btn btn-warning">
                            Edit Detail Cicilan 
                        </button>
                    < ?php } ?> -->
             

                </div>
            </div>

            <a href="<?php echo base_url('aset/PembiayaanAset'); ?>" class="btn btn-default">Kembali</a>
        </div>
    </div>
</fieldset>
