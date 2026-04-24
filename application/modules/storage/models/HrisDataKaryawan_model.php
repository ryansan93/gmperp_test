<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisDataKaryawan_model extends Conf{
	
	public $table = 'hris_data_karyawan';
	protected $primaryKey = 'id';
	public $timestamps = false;

}
