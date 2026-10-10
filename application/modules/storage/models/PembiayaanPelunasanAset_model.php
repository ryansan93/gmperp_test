<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class PembiayaanPelunasanAset_model extends Conf
{
    public $table = 'pembiayaan_pelunasan_aset';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
