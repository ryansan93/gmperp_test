<div class="pull-right" style="display:flex; flex-direction:row; gap:20px; border: 1px solid #bbb9b9; border-radius:5px; margin-bottom:10px; padding:5px;">
    <div style="display:flex; flex-direction:row;  align-items:center; gap:5px; ">
        <div style="border:1px solid black; background-color:#FCFF9C; border-radius:50%; width:15px; height:15px"></div>
        <span>Belum isi form</span>
    </div>

    <div style="display:flex; flex-direction:row;  align-items:center; gap:5px; ">
        <div style="border:1px solid black; background-color:#C9FF9C; border-radius:50%; width:15px; height:15px"></div>
        <span>Sudah isi form</span>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center" style="width:5px;">#</th>
            <th class="text-center">Nama Karyawan</th>
            <th class="text-center">Status Karyawan</th>
            <th class="text-center">Pengusul</th>
            <!-- <th class="text-center">Keterangan</th> -->
            <th class="text-center">Document</th>
            <th class="text-center">Link Form</th>
            <th class="text-center">Keputusan</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)) { ?>
            <?php foreach($list as $l){?>
                <tr>
                    <td style="background-color:<?php echo $l['is_active'] == 'NONACTIVE' ? '#C9FF9C' : '#FCFF9C' ?>"></td>
                    <td class="text-left"><?php echo $l['nama'] ?></td>
                    <td class="text-center">
                        <?php echo $l['status_karyawan'] == 3 ? 'Ditolak' : $l['nama_status'] ?>
                    
                    </td>
                    <td class="text-center"><?php echo ucwords(strtolower($l['nama_pengusul'])) . ' - ' . $l['jabatan_pengusul'] ?></td>
                    <td class="text-center">
                        <a href="<?php echo base_url('hris/HrisKandidatBaru/show_document_kandidat?id='. $l['id_data_karyawan']) ?>" target="_blank">
                           <?php echo $l['document'] ? $l['document'] : '-' ?>
                        </a>
                    </td>
                    <td class="text-center" style="position:relative;">

                        <?php 
                            $key = "secretkey";
                            $plaintext = $l['kategori'].'-'.$l['id_data_karyawan'];

                            $encrypted = openssl_encrypt($plaintext, "AES-128-ECB", $key);
                            // $url = "http://localhost/recruitment-gmp-dev/HrisGenerateForm?kode=" . urlencode($encrypted);
                            $url = "http://localhost/recruitment-gmp/Form?kode=" . urlencode($encrypted);
                        ?>

                        <a <?php echo $l['is_active'] == 'NONACTIVE' ? '' : 'url="'.$url.'"; onclick="hf.copy_link(this, event)" ' ?>  style="<?php echo $l['is_active'] == 'NONACTIVE' ? 'pointer-events:none; color:gray; cursor:not-allowed;' : 'color:blue;' ?>" >
                            <i style="margin-right:5px;" class="fa fa-link"></i> Generate Link
                        </a>
                    </td>
                    <td class="text-center">
                        <?php if ($l['is_active'] == 'NONACTIVE'){ ?>

                            <?php if (!empty($l['keterangan_reject'])) { ?>

                                <span><?= $l['keterangan_reject']; ?></span>

                            <?php } else if (!empty($l['tgl_masuk'])) { ?>

                                <span>Tanggal Masuk :  (<?php echo tglIndonesia($l['tgl_masuk'], '-', ' '); ?>)</span>

                            <?php } else { ?>
                                <button type="button" id_data="<?= $l['id_data_karyawan'] ?>" class="btn btn-sm btn-success" onclick="hf.keputusan_akhir(this, event, 1)">Approve</button>
                                <button type="button" id_data="<?= $l['id_data_karyawan'] ?>" class="btn btn-sm btn-danger" onclick="hf.keputusan_akhir(this, event, 2)"> Reject</button>
                            <?php } ?>

                        <?php } else { ?>
                            -
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>

        <tr>
            <td colspan="6" style="text-align:center;">Tidak ada data</td>
        </tr>
        <?php } ?>


    </tbody>
</table>