<?php
    $kodePembiayaan   = htmlspecialchars($pembiayaan['kode_pembiayaan'] ?? '');
    $kodeAset         = htmlspecialchars($aset['kode_aset'] ?? '');
    $deskripsiAset    = htmlspecialchars($aset['deskripsi_aset'] ?? '');
    $kategori         = htmlspecialchars($aset['kategori_name'] ?? '-');
    $unit             = htmlspecialchars($aset['unit_pengguna'] ?? '-');
    $hargaBeli        = number_format((float) ($aset['nilai_perolehan'] ?? 0), 0, ',', '.');
    $hargaBeliRaw     = (float) ($aset['nilai_perolehan'] ?? 0);
    $supplierNama     = htmlspecialchars(($supplier->nama ?? '') . ' - ' . ($supplier->nomor ?? ''));
    
    $tdp              = $tdp ?? [];
    $canSubmitTdp     = !empty($can_submit_tdp);
    $tdpLocked        = !empty($tdp_locked);
    $tdpInsertLocked  = $tdpLocked || !empty($pelunasan);
    $canEditTdp       = !empty($can_edit_tdp);
    $canDeleteTdp     = !empty($can_delete_tdp);
    
    $pelunasan        = $pelunasan ?? null;
    $canSubmitPelunasan = !empty($can_submit_pelunasan);
    $canEditPelunasan   = !empty($can_edit_pelunasan);
    $canDeletePelunasan = !empty($can_delete_pelunasan);
    $pelunasanLocked    = !empty($pelunasan_locked);
    $canMutatePelunasan = !$pelunasanLocked && (!empty($pelunasan) ? $canEditPelunasan : $canSubmitPelunasan);
    
    // Hitung total TDP yang pengurang pokok
    $tandaJadiTotal = 0;
    $adaTdpPengurangPokok = false; // Flag baru
    foreach ($tdp as $row) {
        if ((int) ($row['is_pengurang_pokok'] ?? 0) === 1) {
            $tandaJadiTotal += (float) ($row['nominal'] ?? 0);
            $adaTdpPengurangPokok = true;
        }
    }

    $tdpInputDisabled = !empty($pelunasan) ? 'disabled' : '';
    $sisaPelunasan = $hargaBeliRaw - $tandaJadiTotal;
?>

<fieldset>
    <legend>Proses Pembiayaan Aset - Cash</legend>
    <div class="row" style="padding:0 15px;">
        <div class="col-xs-12">
            <div class="clearfix" style="margin-bottom:10px;">
                <strong><?php echo $kodePembiayaan; ?></strong>
                <span class="pull-right">
                    <span class="label label-success">Cash</span>
                </span>
            </div>
            <p class="text-muted">
                <?php echo $kodeAset; ?> - <?php echo $deskripsiAset; ?> |
                <?php echo $kategori; ?> | Harga beli <?php echo $hargaBeli; ?>
                <br>
                Supplier <?php echo $supplierNama; ?>
            </p>

            <?php if(empty($tdp) && !empty($pelunasan)): ?>
        
                <div class="panel panel-default installment-panel">
                    <div class="panel-heading">
                        <strong>TDP</strong>
                        <input type="hidden" id="kode_pembiayaan_tdp" value="<?php echo $kodePembiayaan; ?>">
                    </div>
                    <div class="panel-body">
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            <span>Pembiayaan ini tanpa TDP. Pokok hutang hanya dikurangi uang muka (DP).</span>
                            <button type="button" class="btn btn-default" style="width:80px" onclick="pba.add_tdp(this, event)">Ada TDP</button>
                        </div>
                    </div>
                </div>
                
            <?php else: ?>
             
                <div class="panel panel-default installment-panel">
                    <div class="panel-heading">
                        <strong>Tanda Jadi</strong>
                        <span class="label <?php echo !empty($tdp) ? 'label-success' : 'label-warning'; ?> pull-right">
                            <?php echo !empty($tdp) ? 'Tersimpan' : 'Belum diajukan'; ?>
                        </span>
                    </div>
                    <div class="panel-body">
                        <p class="text-muted">Pembayaran di muka sebelum aset diterima. Boleh dikosongkan kalau tidak ada tanda jadi.</p>
                        <form id="form-tdp">
                            <input type="hidden" id="kode_pembiayaan_tdp" value="<?php echo $kodePembiayaan; ?>">
                            
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="tgl_tdp">Tanggal Tanda Jadi <span class="text-danger">*</span></label>
                                        <div class="input-group date" id="tgl_tdp_picker">
                                            <input type="text" id="tgl_tdp" class="form-control" placeholder="Pilih Tanggal" style="caret-color:transparent; background-color:<?php echo $tdpInputDisabled ? '#eee' : '#fff'; ?>" onkeydown="return false;" onpaste="return false;" ondrop="return false;" autocomplete="off"
                                                <?php echo $tdpInputDisabled; ?>>
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

                            <div class="row">
                                <div class="col-xs-7"><label>Deskripsi</label></div>
                                <div class="col-xs-4 text-right"><label>Nominal</label></div>
                                <div class="col-xs-1"></div>
                            </div>
                            <div id="tdp-rows">
                                <div class="row tdp-row" style="margin-bottom:8px;">
                                    <div class="col-xs-7">
                                        <input type="text" class="form-control tdp-deskripsi" maxlength="255" value="Tanda jadi" placeholder="Deskripsi" <?php echo $tdpInputDisabled; ?>>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Rp</span>
                                            <input type="text" class="form-control text-right tdp-nominal" inputmode="numeric" autocomplete="off" placeholder="0" <?php echo $tdpInputDisabled; ?>>
                                        </div>
                                    </div>
                                    <div class="col-xs-1">
                                        <button type="button" class="btn btn-danger btn-sm tdp-remove-row" title="Hapus baris" disabled><i class="fa fa-times"></i></button>
                                    </div>
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
                                <button type="button" id="tdp-none" class="btn btn-default" <?php echo $canSubmitTdp && !$tdpInsertLocked ? '' : 'disabled'; ?>>Tidak ada Tanda Jadi</button>
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
                                        <th style="background-color: #f5f5f5;">Pengurang Pokok</th>
                                        <th style="background-color: #f5f5f5;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tdp-history">
                                    <?php if (!empty($tdp)) : ?>
                                        <?php foreach ($tdp as $item) : ?>
                                            
                                            <tr>
                                                <td><?php echo htmlspecialchars( tglIndonesia($item['tanggal'], '-', ' ') ); ?></td>
                                                <td><?php echo htmlspecialchars($item['deskripsi'] ?? '-'); ?></td>
                                                <td class="text-right">Rp <?php echo number_format((float) $item['nominal'], 0, ',', '.'); ?></td>
                                                <td><?php echo !empty($item['is_pengurang_pokok']) ? 'Ya' : 'Tidak'; ?></td>
                                                <td class="text-center">
                                                    <?php if ($tdpLocked || !empty($pelunasan)) : ?>
                                                        <button type="button" style="color:white" class="btn btn-warning btn-xs" disabled title="Terkunci"><i class="fa fa-pencil"></i> Edit</button>
                                                        <button type="button" class="btn btn-danger btn-xs" disabled title="Terkunci"><i class="fa fa-trash"></i> Hapus</button>
                                                    <?php else : ?>
                                                        <?php if ($canEditTdp) : ?>
                                                            <button
                                                                type="button"
                                                                style="color:white" class="btn btn-warning btn-xs"
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
                                        <tr><td colspan="5" class="text-center text-muted">Belum ada Tanda Jadi tersimpan.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Panel Pelunasan -->
            <div class="panel panel-default installment-panel" id="detail-pelunasan"
                data-harga-beli="<?php echo htmlspecialchars((string) $hargaBeliRaw, ENT_QUOTES); ?>"
                data-tanda-jadi-total="<?php echo htmlspecialchars((string) $tandaJadiTotal, ENT_QUOTES); ?>"
                data-pelunasan-disimpan="<?php echo !empty($pelunasan) ? '1' : '0'; ?>"
                data-can-mutate="<?php echo $canMutatePelunasan ? '1' : '0'; ?>">
                <div class="panel-heading">
                    <div class="installment-heading">
                        <div>
                            <strong>Pelunasan</strong>
                        </div>
                        <span class="label <?php echo !empty($pelunasan) ? 'label-success' : 'label-warning'; ?>" id="status-pelunasan">
                            <?php echo !empty($pelunasan) ? 'Tersimpan' : 'Belum disimpan'; ?>
                        </span>
                    </div>
                </div>
                <div class="panel-body">
                    <p class="text-muted">Pembayaran harga aset ke supplier. Tanda jadi (pengurang pokok) sudah dikurangkan.</p>

                    <?php if ($pelunasanLocked) : ?>
                        <div class="alert alert-warning">Pelunasan dikunci karena pembiayaan sudah selesai atau aset sudah masuk tahap Penerimaan Aset.</div>
                    <?php endif; ?>

                    <form id="form-pelunasan">
                        <input type="hidden" id="kode_pembiayaan_pelunasan" value="<?php echo $kodePembiayaan; ?>">
                        <input type="hidden" id="id_pelunasan" value="<?php echo htmlspecialchars((string) ($pelunasan['id'] ?? ''), ENT_QUOTES); ?>">
                        
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="tgl_pelunasan">Tanggal Pelunasan <span class="text-danger">*</span></label>
                                    <div class="input-group date" id="tgl_pelunasan_picker">
                                        <input type="text" id="tgl_pelunasan" class="form-control" placeholder="Pilih Tanggal" style="caret-color:transparent; background-color:#fff;" onkeydown="return false;" onpaste="return false;" ondrop="return false;" value="<?php echo !empty($pelunasan['tanggal_pelunasan']) ? htmlspecialchars($pelunasan['tanggal_pelunasan'], ENT_QUOTES) : ''; ?>" autocomplete="off"
                                            <?php echo $canMutatePelunasan ? '' : 'disabled'; ?>>
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

                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-xs-7"><label>Deskripsi</label></div>
                            <div class="col-xs-4 text-right"><label>Nominal</label></div>
                            <div class="col-xs-1"></div>
                        </div>
                        
                        <div id="pelunasan-rows">
                            <?php
                                $hargaBeliRaw = floatval($hargaBeliRaw);
                                $hargaBeliFormatted = number_format($hargaBeliRaw, 0, ',', '.');
                            ?>

                            <?php $adaDetailBiayaLain = false; ?>

                             <?php $total_pembelian = 0 ?>
                            
                            <?php if (!empty($pelunasan_detail)) { ?>
                                <?php foreach ($pelunasan_detail as $detail) { ?>

                                    <?php $total_pembelian += $detail['nominal'] ?>

                                    <div class="row pelunasan-row" style="margin-bottom:8px;" data-is-harga-beli="0">
                                        <div class="col-xs-7">
                                            <input type="text" class="form-control pelunasan-deskripsi"  maxlength="255" value="<?php echo $detail['deskripsi']; ?>"  placeholder="Deskripsi"  <?php echo $canMutatePelunasan ? '' : 'disabled'; ?>>
                                        </div>
                                        <div class="col-xs-4">
                                            <div class="input-group">
                                                <span class="input-group-addon">Rp</span>
                                                <input type="text" class="form-control text-right pelunasan-nominal" inputmode="numeric"  autocomplete="off" value="<?php echo $detail['nominal']; ?>"  placeholder="0"  <?php echo $canMutatePelunasan ? '' : 'disabled'; ?>>
                                            </div>
                                        </div>
                                        <div class="col-xs-1">
                                            <button type="button" class="btn btn-danger btn-sm pelunasan-remove-row" 
                                                    title="Hapus baris" 
                                                    <?php echo $canMutatePelunasan ? '' : 'disabled'; ?>>
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>

                                <?php } ?>
                            <?php } else {?>

                                <input type="hidden" value="<?php echo $hargaBeliRaw; ?>" id="harga_beli">
                                <div class="row pelunasan-row" style="margin-bottom:8px;" data-is-harga-beli="1">
                                    <div class="col-xs-7">
                                        <div style="padding:6px 12px; background-color:#f5f5f5; border:1px solid #ccc; border-radius:4px;">
                                            Harga beli aset <span class="label label-info">dasar total</span>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="input-group">
                                            <span class="input-group-addon">Rp</span>
                                            <input type="text" class="form-control text-right pelunasan-nominal" 
                                                value="<?php echo $hargaBeliFormatted; ?>" 
                                                readonly style="background-color: #f5f5f5;">
                                        </div>
                                    </div>
                                    <div class="col-xs-1"></div>
                                </div>

                            <?php } ?>
                        </div>

                        <?php if (!empty($pelunasan_detail)) { ?>
                            <hr style="margin: 15px 0;">
                            
                            <div class="row">
                                <div class="col-xs-6"><strong>Total pembelian</strong></div>
                                <div class="col-xs-6 text-right"><strong><?php echo number_format(($total_pembelian ?? 0), 0, ',', '.'); ?></strong></div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">Dikurangi tanda jadi (pengurang pokok)</div>
                                <div class="col-xs-6 text-right"><?php echo number_format(($tandaJadiTotal ?? 0), 0, ',', '.'); ?></div>
                            </div>
                            <div class="row" style="margin-top:10px;">
                                <div class="col-xs-6"><strong>Diajukan</strong></div>
                                <div class="col-xs-6 text-right"><strong><?php echo number_format(($total_pembelian ?? 0), 0, ',', '.'); ?></strong></div>
                            </div>
                        
                        <?php } else {?>
                            <hr style="margin: 15px 0;">
                            ikie
                            <div class="row">
                                <div class="col-xs-6"><strong>Total pembelian</strong></div>
                                <div class="col-xs-6 text-right"><strong id="total-pembelian">0,00</strong></div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">Dikurangi tanda jadi (pengurang pokok)</div>
                                <div class="col-xs-6 text-right" id="dikurangi-tanda-jadi">(0)</div>
                            </div>
                            <div class="row" style="margin-top:10px;">
                                <div class="col-xs-6"><strong>Diajukan</strong></div>
                                <div class="col-xs-6 text-right"><strong id="diajukan">0,00</strong></div>
                            </div>
                        <?php } ?>


                        <hr>
                        <button type="button" id="pelunasan-add-row" class="btn btn-default" <?php echo $canMutatePelunasan ? '' : 'disabled'; ?>>+ Tambah baris</button>
                        <div style="margin-top:10px;">
                            <button type="submit" id="pelunasan-save" class="btn btn-primary" <?php echo $canMutatePelunasan ? '' : 'disabled'; ?>>
                                <?php echo !empty($pelunasan) ? 'Simpan Perubahan' : 'Simpan Pelunasan'; ?>
                            </button>
                            <?php if (!empty($pelunasan) && $canDeletePelunasan) : ?>
                                <button type="button" id="pelunasan-delete" class="btn btn-danger" data-id="<?php echo (int) $pelunasan['id']; ?>" <?php echo $pelunasanLocked ? 'disabled' : ''; ?>>Hapus Pelunasan</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <a href="<?php echo base_url('aset/PembiayaanAset'); ?>" class="btn btn-default">Kembali</a>
        </div>
    </div>
</fieldset>