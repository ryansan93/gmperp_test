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

    public static function deleteNotInOldFile($realisasi_id, $old_file = [])
    {
        $ids = [];

        if (!empty($old_file)) {
            foreach ($old_file as $row) {
                if (!empty($row['id_file'])) {
                    $ids[] = $row['id_file'];
                }
            }
        }

        $query = self::where('realisasi_id', $realisasi_id);

        if (!empty($ids)) {
            $query->whereNotIn('id', $ids);
        }

        $files = $query->get();

        foreach ($files as $file) {
            if (!empty($file->path) && file_exists($file->path)) {
                unlink($file->path);
            }
        }

        return $query->delete();
    }

    public static function deleteByRealisasiId($realisasi_id)
    {
      
        $files = self::where('realisasi_id', $realisasi_id)->get();

        if ($files->count() > 0) {

            foreach ($files as $file) {
                if (!empty($file->path) && file_exists($file->path)) {
                    unlink($file->path);
                }
            }
            self::where('realisasi_id', $realisasi_id)->delete();
        }
    }


}

