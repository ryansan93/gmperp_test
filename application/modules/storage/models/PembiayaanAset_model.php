<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class PembiayaanAset_model extends Conf
{
    public $table = 'pembiayaan_aset';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
