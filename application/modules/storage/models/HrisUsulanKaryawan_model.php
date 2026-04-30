<?php
namespace Model\Storage;

use \Model\Storage\Conf as Conf;

class HrisUsulanKaryawan_model extends Conf
{
    public $table = 'hris_usulan_karyawan_baru';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function notifData()
    {
        $sql = "SELECT * FROM hris_usulan_karyawan_baru hukb 
        INNER JOIN (
            SELECT *
            FROM karyawan
            WHERE id IN (
                SELECT MAX(id)
                FROM karyawan
                GROUP BY nik
            )
        ) k ON hukb.nama_pengusul = k.nik
        WHERE hukb.status = 1 ";

        $d_dpd = $this->hydrateRaw($sql);

        $data = null;
        if ($d_dpd->count() > 0) {
            $data = $d_dpd->toArray();
        }

        return $data;
    }
}