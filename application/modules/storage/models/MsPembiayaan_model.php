<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class MsPembiayaan_model extends Conf {

    public $table = 'ms_pembiayaan';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
