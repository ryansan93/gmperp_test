<?php 
        function formatBulanTahun($tanggal) {
        if (empty($tanggal)) {
            return '-';
        }
        
        $namaBulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $timestamp = strtotime($tanggal);
        
        if ($timestamp === false) {
            return '-';
        }
        
        $bulan = (int)date('n', $timestamp); 
        $tahun = date('Y', $timestamp);
        
        return $namaBulan[$bulan] . ' ' . $tahun;
    }
?>
<?php if ( !empty($list) && count($list) > 0 ){ ?>
<?php $no = 1; ?>
    <?php foreach($list as $row){ ?>
        <tr class="tr_loop">
            <td class="text-center"><?php echo $no++; ?></td>
            <td>
                <?php echo !empty($row['kode_aset']) ? $row['kode_aset'] : '-'; ?>
            </td>
            <td><?php echo !empty($row['kategori_name']) ? $row['kategori_name'] : '-'; ?></td>
            <td><?php echo !empty($row['deskripsi_aset']) ? $row['deskripsi_aset'] : '-'; ?></td>
            <td><?php echo !empty($row['document_no']) ? $row['document_no'] : '-'; ?></td>
            <td><?php echo !empty($row['unit_pengguna']) ? $row['unit_pengguna'] : '-'; ?></td>
            <td><?php echo !empty($row['nama_kelompok']) ? $row['nama_kelompok'] : '-'; ?></td>
            <td class="text-center"><?php echo !empty($row['masa_manfaat_komersial']) ? $row['masa_manfaat_komersial'] . ' bln' : '-'; ?></td>
            <td class="text-center"><?php echo !empty($row['masa_manfaat_fiskal']) ? $row['masa_manfaat_fiskal'] . ' bln' : '-'; ?></td>
            <td class="text-right">
                <?php echo !empty($row['nilai_perolehan']) ? 'Rp ' . number_format($row['nilai_perolehan'], 0, ',', '.') : '-'; ?>
            </td>
            <td class="text-center">
                <?php echo !empty($row['tgl_perolehan']) ? tglIndonesia($row['tgl_perolehan'], '-', ' ') : '-'; ?>
            </td>
            <td class="text-center">
                <?php echo !empty($row['tgl_penerimaan']) ? tglIndonesia($row['tgl_penerimaan'], '-', ' ') : '-'; ?>
            </td>
            <td><?php echo !empty($row['pic']) ? $row['pic'] : '-'; ?></td>
            <td><?php echo !empty($row['lokasi_pengguna']) ? $row['lokasi_pengguna'] : '-'; ?></td>
            
            <td>
                <?php if (!empty($row['attachment_pembelian'])): ?>
                    <a href="uploads/aset/<?php echo $row['attachment_pembelian'] ?>" target="_blank">Lihat Attachment</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
            <td>
                <?php if (!empty($row['attachment_penerimaan'])): ?>
                    <a href="uploads/aset/<?php echo $row['attachment_penerimaan'] ?>" target="_blank">Lihat Attachment</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>

            <td class="text-right">
                <?php echo !empty($row['nominal_dp']) ? 'Rp ' . number_format($row['nominal_dp'], 0, ',', '.') : '-'; ?>
            </td>
            <td class="text-right">
                <?php echo !empty($row['nominal_cicilan']) ? 'Rp ' . number_format($row['nominal_cicilan'], 0, ',', '.') : '-'; ?>
            </td>
            <td class="text-center">
                <?php echo !empty($row['sudah_bayar']) ? $row['sudah_bayar'] : '0'; ?>
                /
                <?php echo !empty($row['belum_bayar']) ? $row['belum_bayar'] : '0'; ?>
            </td>
            <td class="text-center">
                <?php 
                    $sdh_byr = (int)($row['sudah_bayar'] ?? 0);
                    $blm_byr = (int)($row['belum_bayar'] ?? 0);
                    $total_byr = $sdh_byr + $blm_byr;
                    $persen_byr = $total_byr > 0 ? round(($sdh_byr / $total_byr) * 100, 2) : 0;
                ?>
                <span class="badge <?php echo $persen_byr == 100 ? 'badge-success' : ($persen_byr > 0 ? 'badge-warning' : 'badge-secondary'); ?>" style="font-size: 12px; padding: 5px 10px;">
                    <?php echo $persen_byr; ?>%
                </span>
            </td>
            <td><?php echo formatBulanTahun($row['bb_komersial']) ?></td>
            <td class="text-right">
                <?php echo !empty($row['nominal_komersial']) ? 'Rp ' . number_format($row['nominal_komersial'], 0, ',', '.') : '-'; ?>
            </td>
            <td class="text-right">
                <?php echo !empty($row['komerisal_sdh_bayar']) ? 'Rp ' . number_format($row['komerisal_sdh_bayar'], 0, ',', '.') : '-'; ?>
            </td>
            <td class="text-right">
                <?php echo !empty($row['komerisal_blm_bayar']) ? 'Rp ' . number_format($row['komerisal_blm_bayar'], 0, ',', '.') : '-'; ?>
            </td>
            <?php 
                $sdh_kom = (int)($row['total_sdh_proses_komersial'] ?? 0);
                $blm_kom = (int)($row['total_blm_proses_komersial'] ?? 0);
                $total_kom = $sdh_kom + $blm_kom;
                $persen_kom = $total_kom > 0 ? round(($sdh_kom / $total_kom) * 100, 2) : 0;

                $sdh_fis = (int)($row['total_sdh_proses_fiskal'] ?? 0);
                $blm_fis = (int)($row['total_blm_proses_fiskal'] ?? 0);
                $total_fis = $sdh_fis + $blm_fis;
                $persen_fis = $total_fis > 0 ? round(($sdh_fis / $total_fis) * 100, 2) : 0;
            ?>
            <td class="text-center"><?php echo $sdh_kom; ?> / <?php echo $blm_kom; ?></td>
            <td class="text-center">
                <span class="badge <?php echo $persen_kom == 100 ? 'badge-success' : ($persen_kom > 0 ? 'badge-warning' : 'badge-secondary'); ?>" style="font-size: 12px; padding: 5px 10px;">
                    <?php echo $persen_kom; ?>%
                </span>
            </td>

            <td><?php echo formatBulanTahun($row['bb_fiskal']) ?></td>
            <td class="text-right">
                <?php echo !empty($row['nominal_fiskal']) ? 'Rp ' . number_format($row['nominal_fiskal'], 0, ',', '.') : '-'; ?>
            </td>
            <td class="text-right">
                <?php echo !empty($row['fiskal_sdh_bayar']) ? 'Rp ' . number_format($row['fiskal_sdh_bayar'], 0, ',', '.') : '-'; ?>
            </td>
            <td class="text-right">
                <?php echo !empty($row['fiskal_blm_bayar']) ? 'Rp ' . number_format($row['fiskal_blm_bayar'], 0, ',', '.') : '-'; ?>
            </td>
            <td class="text-center"><?php echo $sdh_fis; ?> / <?php echo $blm_fis; ?></td>
            <td class="text-center">
                <span class="badge <?php echo $persen_fis == 100 ? 'badge-success' : ($persen_fis > 0 ? 'badge-warning' : 'badge-secondary'); ?>" style="font-size: 12px; padding: 5px 10px;">
                    <?php echo $persen_fis; ?>%
                </span>
            </td>
        </tr>
    <?php } ?>
<?php } else { ?>
    <tr>
        <td colspan="32" class="text-center text-muted" style="padding: 20px;">
            <i class="fa fa-inbox" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
            Tidak ada data penerimaan tersedia
        </td>
    </tr>
<?php } ?>