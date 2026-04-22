<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisKategori_model extends Conf{
	
	public $table = 'hris_kategori';
	protected $primaryKey = 'kode_kategori';
	public $timestamps = false;

}
