<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisStatusKaryawan_model extends Conf{
	
	public $table = 'hris_status_karyawan';
	protected $primaryKey = 'id';
	public $timestamps = false;

}
