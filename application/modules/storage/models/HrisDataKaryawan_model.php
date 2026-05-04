<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisDataKaryawan_model extends Conf{
	
	public $table = 'hris_data_karyawan';
	protected $primaryKey = 'id';
	public $timestamps = false;

	public function notifData()
	{
		$sql = "select * from hris_data_karyawan where status_karyawan = 1";

		$d_dpd = $this->hydrateRaw($sql);

        $data = null;
        if ($d_dpd->count() > 0) {
            $data = $d_dpd->toArray();
        }

        return $data;
	}
}
