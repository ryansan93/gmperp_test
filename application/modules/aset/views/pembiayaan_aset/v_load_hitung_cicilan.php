<div class="installment-monthly">
    <h4 id="cicilan-nominal-bulanan">
        <?php echo number_format($data_cicilan['header']['angsuran_per_bulan'] ?? 0, 0, ',', '.'); ?>
    </h4>
    <p class="text-muted">Angsuran per bulan, <span id="cicilan-tenor-label"><?php echo $data_cicilan['header']['tenor_bulan'] ?? 0; ?></span> bulan</p>
</div>

<div class="installment-breakdown">
    <?php
        // 1. Harga Beli (Aman dari error null)
        $harga_beli = isset($data_aset[0]['harga_beli']) ? (float) $data_aset[0]['harga_beli'] : 0;

        // 2. Hitung TDP Pengurang Pokok
        $count_tdp = 0;
        if (!empty($data_tdp)) {
            foreach ($data_tdp as $t) {
                if (!empty($t['is_pengurang_pokok']) && $t['is_pengurang_pokok'] == 1) {
                    $count_tdp += (float) $t['nominal'];
                }
            }
        }

        // 3. Hitung DP Uang Muka
        $uang_muka = 0;
        if (!empty($data_dp['uang_muka'])) {
            foreach ($data_dp['uang_muka'] as $dp) {
                $uang_muka += (float) $dp['nominal'];
            }
        }

        // 4. Hitung Sisa & Pokok Hutang
        $sisa = $harga_beli - $uang_muka - $count_tdp;
        $pokok_hutang = $sisa; 

        // 5. PISAHKAN: Biaya Perolehan Aset vs Total Biaya Prepaid
        $biaya_perolehan_aset = 0; // Hanya untuk Total Perolehan (Penyusutan)
        $biaya_prepaid_total = 0;  // Untuk ditampilkan di UI sebagai "Biaya Prepaid"

        if (!empty($data_dp['detail_non_angsuran'])) {
            foreach ($data_dp['detail_non_angsuran'] as $dp) {
                $nominal = (float) $dp['nominal'];
                $jenis = strtoupper(trim($dp['jenis_komponen'] ?? ''));
                
                $biaya_prepaid_total += $nominal; // Semua masuk ke total prepaid

                // HANYA 3 INI yang masuk ke Nilai Perolehan Aset
                if (in_array($jenis, ['ADMINISTRASI', 'NOTARIS', 'FIDUSIA'])) {
                    $biaya_perolehan_aset += $nominal;
                }
            }
        }

        // 6. Total Perolehan = Harga Beli + Biaya Perolehan Aset (TANPA Car Protection)
        $total_perolehan = $harga_beli + $biaya_perolehan_aset;
    ?>

    <div class="installment-summary-row">
        <span>Harga Beli (Aset)</span>
        <span id="cicilan-harga-beli"><?php echo number_format($harga_beli, 0, ',', '.'); ?></span>
    </div>

    <div class="installment-summary-row">
        <span>TDP pengurang pokok</span>
        <span id="cicilan-tdp-pengurang">(<?php echo number_format($count_tdp, 0, ',', '.');?>)</span>
    </div>

    <div class="installment-summary-row">
        <span>DP (uang muka)</span>
        <span id="cicilan-dp-uang-muka">(<?php echo number_format($uang_muka, 0, ',', '.');?>)</span>
    </div>

    <div class="installment-summary-row">
        <span>Sisa </span>
        <span id="cicilan-sisa"><?php echo number_format($sisa, 0, ',', '.');?></span>
    </div>

    <div class="installment-summary-row">
        <span>Pokok Hutang </span>
        <span id="cicilan-pokok-hutang"><?php echo number_format($pokok_hutang, 0, ',', '.');?></span>
    </div>

    <div class="installment-summary-row">
        <span>Biaya Prepaid </span>
        <span id="cicilan-biaya-prepaid"><?php echo number_format($biaya_prepaid_total, 0, ',', '.');?></span>
    </div>

    <div class="installment-summary-row">
        <span>Total Perolehan </span>
        <span id="cicilan-total-perolehan"><?php echo number_format($total_perolehan, 0, ',', '.');?></span>
    </div>

    <div class="installment-summary-row">
        <span>Bunga flat</span>
        <span id="cicilan-total-bunga"><?php echo number_format($data_cicilan['header']['total_bunga'] ?? 0, 0, ',', '.');?></span>
    </div>

    <div class="installment-summary-row installment-total">
        <strong>Total A/R</strong>
        <strong id="cicilan-total-ar"><?php echo number_format($data_cicilan['header']['total_ar'] ?? 0, 0, ',', '.');?></strong>
    </div>
</div>

<div class="table-responsive installment-schedule-wrap">
    <table class="table table-bordered installment-schedule">
        <thead>
            <tr>
                <th style="background-color: #f5f5f5; width: 50px;">Ke</th>
                <th style="background-color: #f5f5f5;">Jatuh tempo</th>
                <th style="background-color: #f5f5f5; width: 100px;" class="text-center">Jenis</th> 
                <th style="background-color: #f5f5f5; width: 120px;" class="text-right">Pokok</th>
                <th style="background-color: #f5f5f5; width: 120px;" class="text-right">Bunga</th>
                <th style="background-color: #f5f5f5; width: 120px;" class="text-right">Cicilan</th>
                <th style="background-color: #f5f5f5; width: 120px;" class="text-right">Sisa Pokok</th>
                <th style="background-color: #f5f5f5; width: 80px;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody id="jadwal-cicilan">
            <?php if (!empty($data_cicilan['detail'])) { ?>
                <?php 
                    // PERBAIKAN: Saldo berjalan dimulai dari POKOK HUTANG, bukan Total A/R
                    $saldoBerjalan = $pokok_hutang; 
                    
                    $total_pokok = 0;
                    $total_bunga = 0;
                    $total_cicilan = 0;
                ?>

                <?php foreach ($data_cicilan['detail'] as $detail) : ?>
                    <?php 
                        $statusDp = !empty($detail['status']) && $detail['status'] == 1 ? 'Lunas' : 'Pending'; 
                        $jenis = strtoupper(trim($detail['jenis_cicilan'] ?? 'Cicilan'));
                        $labelJenis = ($jenis === 'DP') ? 'label-info' : 'label-default';
                        
                        $pokok = isset($detail['pokok']) ? (float) $detail['pokok'] : 0;
                        $bunga = isset($detail['bunga']) ? (float) $detail['bunga'] : 0;
                        $nominal = (float) ($detail['nominal'] ?? 0);

                        $total_pokok += $pokok;
                        $total_bunga += $bunga;
                        $total_cicilan += $nominal;

                        // Kurangi saldo berjalan dengan POKOK (karena ini kolom Sisa Pokok Hutang)
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
                        <td class="text-right"><?php echo number_format($saldoBerjalan, 0, ',', '.'); ?></td>
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