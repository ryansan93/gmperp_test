<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center">Nama Karyawan</th>
            <th class="text-center">Status Karyawan</th>
            <th class="text-center">Pengusul</th>
            <th class="text-center">Keterangan</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)) { ?>
            <?php foreach($list as $l){?>
                <tr>
                    <td class="text-center"><?php echo $l['nama'] ?></td>
                    <td class="text-center"><?php echo $l['nama_status'] ?></td>
                    <td class="text-center"><?php echo $l['nama_pengusul'] ?></td>
                    <td class="text-center"><?php echo $l['is_active'] ?></td>
                    <td class="text-center">

                        <?php 
                            $key = "secretkey";
                            $plaintext = $l['kategori'].'-'.$l['id_data_karyawan'];

                            $encrypted = openssl_encrypt($plaintext, "AES-128-ECB", $key);
                            // $url = "http://localhost/recruitment-gmp-dev/HrisGenerateForm?kode=" . urlencode($encrypted);
                            $url = "http://localhost/recruitment-gmp/Form?kode=" . urlencode($encrypted);
                        ?>

                        <a <?php echo $l['is_active'] == 'NONACTIVE' ? '' : 'href="'.$url.'" target="_blank"' ?> 
                            style="<?php echo $l['is_active'] == 'NONACTIVE' ? 'pointer-events:none; color:gray; cursor:not-allowed;' : '' ?>"
                        >
                            <i style="margin-right:5px;" class="fa fa-link"></i> Generate Link
                        </a>
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