<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class MmPemItem_model extends Conf{
	protected $table = 'mmpem_item';
	public $timestamps = false;

	public function getMmPemItem($id = null, $column = 'no_mmpem') {
		$data = null;
        
        $sql_id = "";
        if ( !empty($id) ) {
            $sql_id = "where ".$column." in ('".$id."')";
        }

		$sql = "select * from mmpem_item $sql_id order by no_urut asc ";
		$d_mi = $this->hydrateRaw($sql);

		// echo "<pre>";
		// print_r($sql);
		// die;

        if ( !empty($d_mi) && $d_mi->count() > 0 ) {
            $data = $d_mi->toArray();
        }

		return $data;
	}
}
