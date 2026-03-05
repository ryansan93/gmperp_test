<?php

namespace Model\Storage;

defined('BASEPATH') OR exit('No direct script access allowed');

use \Model\Storage\Conf as Conf;

class AttachmentRealisasiPembayaran_model extends Conf
{
    protected $table = 'attachment_realisasi_pembayaran';
    protected $primaryKey = 'id';
    protected $fillable = [
        'realisasi_id',
        'file_name',
        'path',
        'created_at',
        'name_file_old'
    ];


    public static function showAll($realisasi_id = null)
    {
        $query = self::query();
        if ($realisasi_id !== null) {
            $query->where('realisasi_id', $realisasi_id);
        }
        return $query->get()->toArray();
    }
}

