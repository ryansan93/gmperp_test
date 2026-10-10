<?php
    $kodePembiayaan = htmlspecialchars($pembiayaan['kode_pembiayaan'] ?? '');
    $kodeAset       = htmlspecialchars($aset['kode_aset'] ?? '');
    $deskripsiAset  = htmlspecialchars($aset['deskripsi_aset'] ?? '');
    $kategori       = htmlspecialchars($aset['kategori_name'] ?? '-');
    $hargaBeli      = number_format((float) ($aset['nilai_perolehan'] ?? 0), 0, ',', '.');
    $hargaBeliRaw   = (float) ($aset['nilai_perolehan'] ?? 0);
    $supplierNama   = htmlspecialchars(($supplier->nama ?? '') . ' - ' . ($supplier->nomor ?? ''));
    
    $tdp            = $tdp ?? [];
    $canSubmitTdp   = !empty($can_submit_tdp);
    $tdpLocked      = !empty($tdp_locked);
    $tdpInsertLocked = $tdpLocked || !empty($tempo_header);
    $canEditTdp     = !empty($can_edit_tdp);
    $canDeleteTdp   = !empty($can_delete_tdp);
    
    $tempoHeader    = $tempo_header ?? null;
    $tempoDetails   = $tempo_details ?? [];
    $canSubmitTempo = !empty($can_submit_tempo);
    $tempoLocked    = !empty($tempo_locked);
    $canMutateTempo = !$tempoLocked && ($canSubmitTempo || !empty($tempoHeader));
    
    $tandaJadiTotal = 0;
    foreach ($tdp as $row) {
        if ((int) ($row['is_pengurang_pokok'] ?? 0) === 1) {
            $tandaJadiTotal += (float) ($row['nominal'] ?? 0);
        }
    }
    
    $tdpInputDisabled = !empty($tempo_header) ? 'disabled' : '';
    $totalPembelian = $hargaBeliRaw;
    $sisaDijadwalkan = $totalPembelian - $tandaJadiTotal;
?>

<fieldset>
    <legend>Proses Pembiayaan Aset - Cash Tempo</legend>
    <div class="row" style="padding:0 15px;">
        <div class="col-xs-12">
            <div class="clearfix" style="margin-bottom:10px;">
                <strong><?php echo $kodePembiayaan; ?></strong>
                <span class="pull-right"><span class="label label-primary">Cash Tempo</span></span>
            </div>
            <p class="text-muted">
                <?php echo $kodeAset; ?> - <?php echo $deskripsiAset; ?> | <?php echo $kategori; ?>
                <br>Supplier <?php echo $supplierNama; ?>
            </p>

            <?php if ($tdpLocked) : ?>
                <div class="alert alert-warning">Tanda Jadi dikunci karena aset sudah masuk tahap Penerimaan Aset.</div>
            <?php elseif (!empty($tempo_header)) : ?>
                <div class="alert alert-warning">TDP tidak dapat diubah karena data Rencana Tempo sudah tersimpan.</div>
            <?php endif; ?>

            
            <?php if(empty($tdp) && !empty($tempoHeader)){?>

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

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong>Tanda Jadi</strong>
                        <span class="label <?php echo !empty($tdp) ? 'label-success' : 'label-warning'; ?> pull-right"><?php echo !empty($tdp) ? 'Tersimpan' : 'Belum diajukan'; ?></span>
                    </div>
                    <div class="panel-body">
                        <p class="text-muted">Pembayaran di muka sebelum aset diterima.</p>
                        <form id="form-tdp">
                            <input type="hidden" id="kode_pembiayaan_tdp" value="<?php echo $kodePembiayaan; ?>">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Tanggal Tanda Jadi <span class="text-danger">*</span></label>
                                        <div class="input-group date" id="tgl_tdp_picker">
                                            <input type="text" id="tgl_tdp" class="form-control" placeholder="Pilih Tanggal" <?php echo $tdpInputDisabled; ?>>
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

                            <div class="checkbox" style="margin-top:-5px; margin-bottom:15px;">
                                <label>
                                    <input type="checkbox" id="tdp_pengurang_pokok" checked <?php echo $tdpInputDisabled; ?>> 
                                    Sebagai pengurang pokok hutang
                                </label>
                            </div>

                            <div id="tdp-rows">
                                <div class="row tdp-row" style="margin-bottom:8px;">
                                    <div class="col-xs-7"><label>Deskripsi</label></div>
                                    <div class="col-xs-7"><input type="text" class="form-control tdp-deskripsi" maxlength="255" value="Tanda jadi" <?php echo $tdpInputDisabled; ?>></div>
                                    <div class="col-xs-4"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right tdp-nominal" inputmode="numeric" placeholder="0" <?php echo $tdpInputDisabled; ?>></div></div>
                                    <div class="col-xs-1"><button type="button" class="btn btn-danger btn-sm tdp-remove-row" disabled><i class="fa fa-times"></i></button></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6"><strong>Total Tanda Jadi</strong></div>
                                <div class="col-xs-6 text-right"><strong id="tdp-total">0,00</strong></div>
                            </div>
                            <hr>
                            <button type="button" id="tdp-add-row" class="btn btn-default" <?php echo $canSubmitTdp && !$tdpInsertLocked ? '' : 'disabled'; ?>>+ Tambah baris</button>
                            <div style="margin-top:10px;">
                                <button type="submit" id="tdp-save" class="btn btn-primary" <?php echo $canSubmitTdp && !$tdpInsertLocked ? '' : 'disabled'; ?>>Simpan Tanda Jadi</button>
                            </div>
                        </form>
                        <hr>
                        <h4>Riwayat Tanda Jadi</h4>
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
                                    <?php if (!empty($tdp)) : foreach ($tdp as $item) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['tanggal'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($item['deskripsi'] ?? '-'); ?></td>
                                            <td class="text-right">Rp <?php echo number_format((float) $item['nominal'], 0, ',', '.'); ?></td>
                                            <td><?php echo !empty($item['is_pengurang_pokok']) ? 'Ya' : 'Tidak'; ?></td>
                                            <td class="text-center">
                                                <button type="button" style="color:white" class="btn btn-warning btn-xs" data-id="<?php echo (int) $item['id']; ?>" data-tanggal="<?php echo htmlspecialchars($item['tanggal'], ENT_QUOTES); ?>" data-deskripsi="<?php echo htmlspecialchars($item['deskripsi'] ?? '', ENT_QUOTES); ?>" data-nominal="<?php echo htmlspecialchars((string) round($item['nominal']), ENT_QUOTES); ?>" data-pengurang-pokok="<?php echo !empty($item['is_pengurang_pokok']) ? '1' : '0'; ?>" onclick="pba.edit_tdp(this)"><i class="fa fa-pencil"></i> Edit</button>
                                                <button type="button" class="btn btn-danger btn-xs" data-id="<?php echo (int) $item['id']; ?>" onclick="pba.delete_tdp(this)"><i class="fa fa-trash"></i> Hapus</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; else : ?>
                                        <tr><td colspan="5" class="text-center text-muted">Belum ada Tanda Jadi.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <?php } ?>

            <div class="panel panel-default" id="detail-tempo" data-harga-beli="<?php echo $hargaBeliRaw; ?>" data-tdp-total="<?php echo $tandaJadiTotal; ?>" data-can-mutate="<?php echo $canMutateTempo ? '1' : '0'; ?>">
                <div class="panel-heading">
                    <div class="clearfix">
                        <label style="margin:0;"> Rencana tempo</label>
                        <span class="label <?php echo !empty($tempoHeader) ? 'label-success' : 'label-warning'; ?> pull-right"><?php echo !empty($tempoHeader) ? 'Tersimpan' : 'Belum disimpan'; ?></span>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row" style="margin-bottom:15px;">
                        <div class="col-xs-12">
                            <strong>Harga beli aset:</strong> <?php echo $hargaBeli; ?> <span class="text-muted">(dari master aset)</span>
                        </div>
                    </div>

                    <form id="form-tempo">
                        <input type="hidden" id="kode_pembiayaan_tempo" value="<?php echo $kodePembiayaan; ?>">
                        
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Dibayar ke</label>
                                    <input type="text" class="form-control" value="<?php echo $supplierNama; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Biaya lain (ongkir, instalasi)</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">Rp</span>
                                        <input type="text" id="biaya_lain_tempo" class="form-control text-right" value="<?php echo !empty($tempoHeader['biaya_lain']) ? number_format((float) $tempoHeader['biaya_lain'], 0, ',', '.') : '0'; ?>" <?php echo $canMutateTempo ? '' : 'disabled'; ?>>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Jumlah termin <span class="text-danger">*</span></label>
                                    <input type="number" id="jumlah_termin" class="form-control" min="1" max="60" value="<?php echo !empty($tempoHeader['jumlah_termin']) ? $tempoHeader['jumlah_termin'] : ''; ?>" placeholder="Contoh: 2" <?php echo $canMutateTempo ? '' : 'disabled'; ?>>
                                </div>
                            </div>
                        </div>

                        <hr style="margin:15px 0;">
                        
                        <div class="row" style="margin-bottom:10px; font-weight:bold;">
                            <div class="col-xs-1">Termin</div>
                            <div class="col-xs-5">Jatuh tempo <span class="text-danger">*</span></div>
                            <div class="col-xs-5 text-right">Nominal</div>
                            <div class="col-xs-1"></div>
                        </div>
                        
                        <div id="tempo-rows">
                            <?php if (!empty($tempoDetails)) : ?>
                               <?php foreach ($tempoDetails as $detail) : ?>
                                    <div class="row tempo-row" style="margin-bottom:8px;">
                                        <div class="col-xs-1" style="padding-top:7px;"><strong><?php echo (int) $detail['termin_ke']; ?></strong></div>
                                        <div class="col-xs-5">
                                            <div class="input-group date tempo-jatuh-tempo-picker">
                                                <?php
                                                    // Format tanggal dari Y-m-d ke DD MMMM YYYY
                                                    $jt = $detail['jatuh_tempo'] ?? '';
                                                    $jtFormatted = $jt;
                                                    if ($jt) {
                                                        $bulanID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                                                        $parts = explode('-', $jt);
                                                        if (count($parts) === 3) {
                                                            $jtFormatted = (int)$parts[2] . ' ' . $bulanID[(int)$parts[1] - 1] . ' ' . $parts[0];
                                                        }
                                                    }
                                                ?>
                                                <input type="text" class="form-control tempo-jatuh-tempo" 
                                                    value="<?php echo htmlspecialchars($jtFormatted); ?>" 
                                                    <?php echo $canMutateTempo ? '' : 'disabled'; ?>>
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-xs-5">
                                            <div class="input-group">
                                                <span class="input-group-addon">Rp</span>
                                                <input type="text" class="form-control text-right tempo-nominal" 
                                                    value="<?php echo number_format((float) $detail['nominal'], 0, ',', '.'); ?>" 
                                                    <?php echo $canMutateTempo ? '' : 'disabled'; ?>>
                                            </div>
                                        </div>
                                        <div class="col-xs-1 text-right">
                                            <?php if ($canMutateTempo) : ?><button type="button" class="btn btn-danger btn-sm tempo-remove-row"><i class="fa fa-times"></i></button><?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php elseif ($canMutateTempo) : ?>
                                <div class="row tempo-row" style="margin-bottom:8px;">
                                    <div class="col-xs-1" style="padding-top:7px;"><strong>1</strong></div>
                                    <div class="col-xs-5"><div class="input-group date tempo-jatuh-tempo-picker"><input type="text" class="form-control tempo-jatuh-tempo" placeholder="Pilih Tanggal"><span class="input-group-addon"><i class="fa fa-calendar"></i></span></div></div>
                                    <div class="col-xs-5"><div class="input-group"><span class="input-group-addon">Rp</span><input type="text" class="form-control text-right tempo-nominal" placeholder="0"></div></div>
                                    <div class="col-xs-1 text-right"><button type="button" class="btn btn-danger btn-sm tempo-remove-row"><i class="fa fa-times"></i></button></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <hr style="margin:15px 0;">
                        
                        <div class="row">
                            <div class="col-xs-8 text-left"><strong>Total pembelian</strong></div>
                            <div class="col-xs-4 text-right"><strong id="display_total_pembelian"><?php echo number_format($totalPembelian, 0, ',', '.'); ?></strong></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-8 text-left">Dikurangi TDP (pengurang pokok)</div>
                            <div class="col-xs-4 text-right" id="display_tdp">(<?php echo number_format($tandaJadiTotal, 0, ',', '.'); ?>)</div>
                        </div>
                        <div class="row" style="margin-top:10px;">
                            <div class="col-xs-8 text-left"><strong style="font-size:14px;">Sisa dijadwalkan di termin</strong></div>
                            <div class="col-xs-4 text-right"><strong id="display_sisa" style="font-size:14px;"><?php echo number_format($sisaDijadwalkan, 0, ',', '.'); ?></strong></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-8 text-left">Jumlah semua termin</div>
                            <div class="col-xs-4 text-right"><strong id="display_total_termin">0</strong></div>
                        </div>

                        <p class="text-muted" style="margin-top:15px; font-size:12px;">
                            Hutang supplier terbentuk saat aset diterima. Tanggal jatuh tempo diisi sesuai kesepakatan dengan supplier.
                        </p>

                        <?php if ($canMutateTempo) : ?>
                            <div style="margin-top:15px;">
                                <button type="button" id="btn_tambah_termin" class="btn btn-default btn-sm">+ Tambah baris</button>
                                <button type="button" id="btn_simpan_tempo" class="btn btn-primary" disabled>Simpan rencana tempo</button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($tempoHeader)){ ?>
                            <button type="button" id="btn_update_tempo" class="btn btn-warning btn-sm" onclick="pba.edit_tempo(this)">
                                <i class="fa fa-pencil"></i> Update Rencana Tempo
                            </button>
                        <?php } ?>
                    </form>
                </div>
            </div>

            <a href="<?php echo base_url('aset/PembiayaanAset'); ?>" class="btn btn-default">Kembali</a>
        </div>
    </div>
</fieldset>