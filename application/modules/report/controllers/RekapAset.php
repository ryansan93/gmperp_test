<?php defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use Dompdf\Dompdf;

class RekapAset extends Public_Controller {

    private $pathView = 'report/rekap_aset/';
    private $url;
    private $hakAkses;

    function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;
        $this->hakAkses = hakAkses($this->url);
    }

    public function index($segment=0)
    {
        if ( $this->hakAkses['a_view'] == 1 ) {

            $this->add_external_js(array(
                "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
                "assets/select2/js/select2.min.js",
                "assets/report/rekap_aset/js/rekap_aset.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/report/rekap_aset/css/rekap_aset.css",
            ));

            $m_aset = new \Model\Storage\MsAset_model();
            

            $data = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'Rekap Aset';
            $content['kategori_aset']   = $this->get_kategori_aset_list();

            $content['total_aset']      = $m_aset->count();
            $content['aset_aktif']      = $this->get_aset_aktif();
            $content['aset_selesai']    = $this->get_aset_selesai();
    
            // cetak_r($content, 1);

            $data['title_menu']         = 'Rekap Aset';

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    private function get_kategori_aset_list()
    {
        $m_kategori = new \Model\Storage\MsAsetKategori_model();
        return $m_kategori->orderBy('id', 'asc')->get()->toArray();
    }

    public function get_aset_aktif()
    {
        $m_conf = new \Model\Storage\Conf();

        $sql = " SELECT COUNT(DISTINCT ma.id) as total
                FROM ms_aset ma
                WHERE EXISTS (
                    SELECT 1 FROM penyusutan_komersial_aset 
                    WHERE kode_aset = ma.kode_aset AND status = 0
                )
                OR EXISTS (
                    SELECT 1 FROM penyusutan_fiskal_aset 
                    WHERE kode_aset = ma.kode_aset AND status = 0
                ) ";

        $d_conf = $m_conf->hydrateRaw($sql);
        $d_aset = $d_conf->count() > 0 ? $d_conf->toArray() : [];

        return $d_aset[0]['total'];
    }

    public function get_aset_selesai()
    {
        $m_conf = new \Model\Storage\Conf();

        $sql = " SELECT COUNT(DISTINCT ma.id) as total
            FROM ms_aset ma
            WHERE NOT EXISTS (
                SELECT 1 FROM penyusutan_komersial_aset 
                WHERE kode_aset = ma.kode_aset AND status = 0
            )
            AND NOT EXISTS (
                SELECT 1 FROM penyusutan_fiskal_aset 
                WHERE kode_aset = ma.kode_aset AND status = 0
            )
            AND (
                EXISTS (SELECT 1 FROM penyusutan_komersial_aset WHERE kode_aset = ma.kode_aset)
                OR EXISTS (SELECT 1 FROM penyusutan_fiskal_aset WHERE kode_aset = ma.kode_aset)
            ) ";

        $d_conf = $m_conf->hydrateRaw($sql);
        $d_aset = $d_conf->count() > 0 ? $d_conf->toArray() : [];

        return $d_aset[0]['total'];
    }

    public function list_data()
    {
        $akses           = hakAkses($this->url);
        
        $filter_kategori = trim($this->input->post('id_kategori'));
        $filter_status   = trim($this->input->post('filter_status')); 
        $search          = trim($this->input->post('search'));

        // cetak_r($_POST, 1);

        $m_conf = new \Model\Storage\Conf();
        
        // $sql = "SELECT 
        //             ma.kode_aset,
        //             ma.document_no,
        //             ma.tgl_perolehan,
        //             ma.keterangan AS keterangan_pembelian,
        //             ma.attachment AS attachment_pembelian,
        //             ma.deskripsi_aset,
        //             ma.unit_pengguna,
        //             ma.nilai_perolehan,
        //             mak.kategori_kode,
        //             mak.kategori_name,
        //             mak.masa_manfaat_komersial,
        //             mak.masa_manfaat_fiskal,
        //             mk.nama_kelompok,
        //             pa.no_bukti_terima,
        //             pa.tgl_penerimaan,
        //             pa.attachment AS attachment_penerimaan,
        //             k.nama AS pic,
        //             w.nama AS lokasi_pengguna,
        //             pa.keterangan_terima AS keterangan_penerimaan,
        //             blm_proses_komersial.total_blm_proses_komersial,
        //             sdh_proses_komersial.total_sdh_proses_komersial,
        //             blm_proses_fiskal.total_blm_proses_fiskal,
        //             sdh_proses_fiskal.total_sdh_proses_fiskal,

        //             bulan_berjalan_komersial.bb_komersial,
        //             bulan_berjalan_fiskal.bb_fiskal,
        //             bulan_berjalan_komersial.beban_penyusutan AS nominal_komersial,
        //             bulan_berjalan_fiskal.beban_penyusutan AS nominal_fiskal
                    
        //         FROM ms_aset ma
        //         INNER JOIN ms_aset_kategori mak ON ma.id_kategori = mak.id 
        //         LEFT JOIN penerimaan_aset pa ON ma.kode_aset = pa.kode_aset 
        //         INNER JOIN ms_kelompok mk ON mak.id_kelompok = mk.id 
        //         LEFT JOIN karyawan k ON pa.pic = k.nik AND k.status = 1
        //         OUTER APPLY (
        //             SELECT TOP 1 nama, kode 
        //             FROM wilayah 
        //             WHERE kode = pa.lokasi_pengguna
        //         ) w
        //         OUTER APPLY (
        //             SELECT COUNT(id) AS total_blm_proses_komersial
        //             FROM penyusutan_komersial_aset
        //             WHERE status = 0 AND kode_aset = ma.kode_aset 
        //         ) blm_proses_komersial
        //         OUTER APPLY (
        //             SELECT COUNT(id) AS total_sdh_proses_komersial
        //             FROM penyusutan_komersial_aset
        //             WHERE status = 1 AND kode_aset = ma.kode_aset 
        //         ) sdh_proses_komersial
        //         OUTER APPLY (
        //             SELECT COUNT(id) AS total_blm_proses_fiskal
        //             FROM penyusutan_fiskal_aset
        //             WHERE status = 0 AND kode_aset = ma.kode_aset 
        //         ) blm_proses_fiskal
        //         OUTER APPLY (
        //             SELECT COUNT(id) AS total_sdh_proses_fiskal
        //             FROM penyusutan_fiskal_aset
        //             WHERE status = 1 AND kode_aset = ma.kode_aset 
        //         ) sdh_proses_fiskal
        //         OUTER APPLY (
        //             SELECT TOP 1 tanggal_jatuh_tempo AS bb_komersial, beban_penyusutan
        //             FROM penyusutan_komersial_aset
        //             WHERE status = 0 AND kode_aset = ma.kode_aset 
        //             ORDER BY id ASC
        //         ) bulan_berjalan_komersial
        //         OUTER APPLY (
        //             SELECT TOP 1 tanggal_jatuh_tempo AS bb_fiskal, beban_penyusutan
        //             FROM penyusutan_fiskal_aset
        //             WHERE status = 0 AND kode_aset = ma.kode_aset 
        //             ORDER BY id ASC
        //         ) bulan_berjalan_fiskal
        //     ";

        $sql = " SELECT 
                    ma.kode_aset,
                    ma.document_no,
                    ma.tgl_perolehan,
                    ma.keterangan AS keterangan_pembelian,
                    ma.attachment AS attachment_pembelian,
                    ma.deskripsi_aset,
                    ma.unit_pengguna,
                    ma.nilai_perolehan,
                    mak.kategori_kode,
                    mak.kategori_name,
                    mak.masa_manfaat_komersial,
                    mak.masa_manfaat_fiskal,
                    mk.nama_kelompok,
                    pa.no_bukti_terima,
                    pa.tgl_penerimaan,
                    pa.attachment AS attachment_penerimaan,
                    k.nama AS pic,
                    w.nama AS lokasi_pengguna,
                    pa.keterangan_terima AS keterangan_penerimaan,
                    blm_proses_komersial.total_blm_proses_komersial,
                    sdh_proses_komersial.total_sdh_proses_komersial,
                    blm_proses_fiskal.total_blm_proses_fiskal,
                    sdh_proses_fiskal.total_sdh_proses_fiskal,
                    
                    blm_proses_komersial.komerisal_blm_bayar,
                    sdh_proses_komersial.komerisal_sdh_bayar,

                    blm_proses_fiskal.fiskal_blm_bayar,
                    sdh_proses_fiskal.fiskal_sdh_bayar,
                    
                    bulan_berjalan_komersial.bb_komersial,
                    bulan_berjalan_fiskal.bb_fiskal,
                    bulan_berjalan_komersial.beban_penyusutan AS nominal_komersial,
                    bulan_berjalan_fiskal.beban_penyusutan AS nominal_fiskal,

                    dp.nominal_dp,
                    sdh_bayar.sudah_bayar,
                    blm_bayar.belum_bayar,
                    cicilan.nominal_cicilan

                FROM ms_aset ma
                INNER JOIN ms_aset_kategori mak ON ma.id_kategori = mak.id 
                LEFT JOIN penerimaan_aset pa ON ma.kode_aset = pa.kode_aset 
                INNER JOIN ms_kelompok mk ON mak.id_kelompok = mk.id 
                LEFT JOIN karyawan k ON pa.pic = k.nik AND k.status = 1
                OUTER APPLY (
                    SELECT TOP 1 nama, kode 
                    FROM wilayah 
                    WHERE kode = pa.lokasi_pengguna
                ) w
                OUTER APPLY (
                    SELECT 
                    	COUNT(id) AS total_blm_proses_komersial,
                    	sum(beban_penyusutan) as komerisal_blm_bayar
                    FROM penyusutan_komersial_aset
                    WHERE status = 0 AND kode_aset = ma.kode_aset 
                ) blm_proses_komersial
                OUTER APPLY (
                    SELECT 
                    	COUNT(id) AS total_sdh_proses_komersial,
                    	sum(beban_penyusutan) as komerisal_sdh_bayar
                    FROM penyusutan_komersial_aset
                    WHERE status = 1 AND kode_aset = ma.kode_aset 
                ) sdh_proses_komersial
                OUTER APPLY (
                    SELECT 
                    	COUNT(id) AS total_blm_proses_fiskal,
                    	sum(beban_penyusutan) as fiskal_blm_bayar
                    FROM penyusutan_fiskal_aset
                    WHERE status = 0 AND kode_aset = ma.kode_aset 
                ) blm_proses_fiskal
                OUTER APPLY (
                    SELECT 
                    	COUNT(id) AS total_sdh_proses_fiskal,
                    	sum(beban_penyusutan) as fiskal_sdh_bayar
                    FROM penyusutan_fiskal_aset
                    WHERE status = 1 AND kode_aset = ma.kode_aset 
                ) sdh_proses_fiskal
                OUTER APPLY (
                    SELECT TOP 1 tanggal_jatuh_tempo AS bb_komersial, beban_penyusutan
                    FROM penyusutan_komersial_aset
                    WHERE status = 0 AND kode_aset = ma.kode_aset 
                    ORDER BY id ASC
                ) bulan_berjalan_komersial
                OUTER APPLY (
                    SELECT TOP 1 tanggal_jatuh_tempo AS bb_fiskal, beban_penyusutan
                    FROM penyusutan_fiskal_aset
                    WHERE status = 0 AND kode_aset = ma.kode_aset 
                    ORDER BY id ASC
                ) bulan_berjalan_fiskal  

                OUTER APPLY (
                    SELECT COUNT(id) AS sudah_bayar
                    FROM termin_aset
                    WHERE status = 1 AND kode_aset = ma.kode_aset and jenis_pembayaran != 'dp'
                ) sdh_bayar     

                OUTER APPLY (
                    SELECT COUNT(id) AS belum_bayar
                    FROM termin_aset
                    WHERE status = 0 AND kode_aset = ma.kode_aset and jenis_pembayaran != 'dp'
                ) blm_bayar

                OUTER APPLY (
                    SELECT nominal AS nominal_dp
                    FROM termin_aset
                    WHERE kode_aset = ma.kode_aset  and jenis_pembayaran = 'dp'
                ) dp     

                OUTER APPLY (
                    SELECT TOP 1 nominal AS nominal_cicilan
                    FROM termin_aset
                    WHERE kode_aset = ma.kode_aset  
                    AND jenis_pembayaran = 'cicilan'
                ) cicilan
         ";

        $where_clauses = [];
        $bindings      = [];

        if (!empty($filter_kategori)) {
            $where_clauses[] = "mak.kategori_name = ?";
            $bindings[]      = $filter_kategori;
        }

        if (!empty($filter_status)) {
            if ($filter_status === 'belum_terproses') {
                $where_clauses[] = "(EXISTS (SELECT 1 FROM penyusutan_komersial_aset WHERE status = 0 AND kode_aset = ma.kode_aset) OR EXISTS (SELECT 1 FROM penyusutan_fiskal_aset WHERE status = 0 AND kode_aset = ma.kode_aset))";
            } elseif ($filter_status === 'sudah_terproses') {
                $where_clauses[] = "(NOT EXISTS (SELECT 1 FROM penyusutan_komersial_aset WHERE status = 0 AND kode_aset = ma.kode_aset) AND NOT EXISTS (SELECT 1 FROM penyusutan_fiskal_aset WHERE status = 0 AND kode_aset = ma.kode_aset))";
            } elseif ($filter_status === 'belum_terima') {
                $where_clauses[] = "pa.no_bukti_terima IS NULL";
            } elseif ($filter_status === 'sudah_terima') {
                $where_clauses[] = "pa.no_bukti_terima IS NOT NULL";
            }
        }

        if (!empty($search)) {
            $like = '%' . $search . '%';
            $where_clauses[] = "(ma.kode_aset LIKE ? OR ma.deskripsi_aset LIKE ? OR mak.kategori_name LIKE ? OR mk.nama_kelompok LIKE ? OR pa.pic LIKE ?)";
            $bindings = array_merge($bindings, [$like, $like, $like, $like, $like]);
        }

        if (!empty($where_clauses)) {
            $sql .= " WHERE " . implode(' AND ', $where_clauses);
        }

        $sql .= " ORDER BY ma.id DESC";

        $d_conf = $m_conf->hydrateRaw($sql, $bindings);
        $d_aset = $d_conf->count() > 0 ? $d_conf->toArray() : [];

        // cetak_r($d_aset, 1);

        $content['akses']   = $akses;
        $content['list']    = $d_aset;
        
        $html = $this->load->view($this->pathView . 'v_list', $content, TRUE);
        echo $html;
    }


    public function export_data()
    {
        $forceString = function($val) {
            if (is_array($val)) {
                return isset($val[0]) ? trim((string)$val[0]) : '';
            }
            return trim((string)($val ?? ''));
        };

        $filter_kategori = $forceString($this->input->get('id_kategori'));
        $filter_status   = $forceString($this->input->get('filter_status'));
        $search          = $forceString($this->input->get('search'));

        // cetak_r($_GET, 1);

        $m_conf = new \Model\Storage\Conf();
        
        $sql = " SELECT 
                    ma.kode_aset,
                    ma.document_no,
                    ma.tgl_perolehan,
                    ma.keterangan AS keterangan_pembelian,
                    ma.attachment AS attachment_pembelian,
                    ma.deskripsi_aset,
                    ma.unit_pengguna,
                    ma.nilai_perolehan,
                    mak.kategori_kode,
                    mak.kategori_name,
                    mak.masa_manfaat_komersial,
                    mak.masa_manfaat_fiskal,
                    mk.nama_kelompok,
                    pa.no_bukti_terima,
                    pa.tgl_penerimaan,
                    pa.attachment AS attachment_penerimaan,
                    k.nama AS pic,
                    w.nama AS lokasi_pengguna,
                    pa.keterangan_terima AS keterangan_penerimaan,
                    blm_proses_komersial.total_blm_proses_komersial,
                    sdh_proses_komersial.total_sdh_proses_komersial,
                    blm_proses_fiskal.total_blm_proses_fiskal,
                    sdh_proses_fiskal.total_sdh_proses_fiskal,
                    
                    blm_proses_komersial.komerisal_blm_bayar,
                    sdh_proses_komersial.komerisal_sdh_bayar,

                    blm_proses_fiskal.fiskal_blm_bayar,
                    sdh_proses_fiskal.fiskal_sdh_bayar,
                    
                    bulan_berjalan_komersial.bb_komersial,
                    bulan_berjalan_fiskal.bb_fiskal,
                    bulan_berjalan_komersial.beban_penyusutan AS nominal_komersial,
                    bulan_berjalan_fiskal.beban_penyusutan AS nominal_fiskal,

                    dp.nominal_dp,
                    sdh_bayar.sudah_bayar,
                    blm_bayar.belum_bayar,
                    cicilan.nominal_cicilan

                FROM ms_aset ma
                INNER JOIN ms_aset_kategori mak ON ma.id_kategori = mak.id 
                LEFT JOIN penerimaan_aset pa ON ma.kode_aset = pa.kode_aset 
                INNER JOIN ms_kelompok mk ON mak.id_kelompok = mk.id 
                LEFT JOIN karyawan k ON pa.pic = k.nik AND k.status = 1
                OUTER APPLY (
                    SELECT TOP 1 nama, kode 
                    FROM wilayah 
                    WHERE kode = pa.lokasi_pengguna
                ) w
                OUTER APPLY (
                    SELECT 
                    	COUNT(id) AS total_blm_proses_komersial,
                    	sum(beban_penyusutan) as komerisal_blm_bayar
                    FROM penyusutan_komersial_aset
                    WHERE status = 0 AND kode_aset = ma.kode_aset 
                ) blm_proses_komersial
                OUTER APPLY (
                    SELECT 
                    	COUNT(id) AS total_sdh_proses_komersial,
                    	sum(beban_penyusutan) as komerisal_sdh_bayar
                    FROM penyusutan_komersial_aset
                    WHERE status = 1 AND kode_aset = ma.kode_aset 
                ) sdh_proses_komersial
                OUTER APPLY (
                    SELECT 
                    	COUNT(id) AS total_blm_proses_fiskal,
                    	sum(beban_penyusutan) as fiskal_blm_bayar
                    FROM penyusutan_fiskal_aset
                    WHERE status = 0 AND kode_aset = ma.kode_aset 
                ) blm_proses_fiskal
                OUTER APPLY (
                    SELECT 
                    	COUNT(id) AS total_sdh_proses_fiskal,
                    	sum(beban_penyusutan) as fiskal_sdh_bayar
                    FROM penyusutan_fiskal_aset
                    WHERE status = 1 AND kode_aset = ma.kode_aset 
                ) sdh_proses_fiskal
                OUTER APPLY (
                    SELECT TOP 1 tanggal_jatuh_tempo AS bb_komersial, beban_penyusutan
                    FROM penyusutan_komersial_aset
                    WHERE status = 0 AND kode_aset = ma.kode_aset 
                    ORDER BY id ASC
                ) bulan_berjalan_komersial
                OUTER APPLY (
                    SELECT TOP 1 tanggal_jatuh_tempo AS bb_fiskal, beban_penyusutan
                    FROM penyusutan_fiskal_aset
                    WHERE status = 0 AND kode_aset = ma.kode_aset 
                    ORDER BY id ASC
                ) bulan_berjalan_fiskal  


                OUTER APPLY (
                    SELECT COUNT(id) AS sudah_bayar
                    FROM termin_aset
                    WHERE status = 1 AND kode_aset = ma.kode_aset and jenis_pembayaran != 'dp'
                ) sdh_bayar     

                OUTER APPLY (
                    SELECT COUNT(id) AS belum_bayar
                    FROM termin_aset
                    WHERE status = 0 AND kode_aset = ma.kode_aset and jenis_pembayaran != 'dp'
                ) blm_bayar

                OUTER APPLY (
                    SELECT top 1 nominal AS nominal_dp
                    FROM termin_aset
                    WHERE kode_aset = ma.kode_aset  and jenis_pembayaran = 'dp'
                ) dp     

                OUTER APPLY (
                    SELECT TOP 1 nominal AS nominal_cicilan
                    FROM termin_aset
                    WHERE kode_aset = ma.kode_aset  
                    AND jenis_pembayaran = 'cicilan'
                ) cicilan
         ";

        $where_clauses = [];
        $bindings      = [];

        if (!empty($filter_kategori)) {
            $where_clauses[] = "mak.kategori_name = ?";
            $bindings[]      = $filter_kategori;
        }

        if (!empty($filter_status)) {
            if ($filter_status === 'Aktif') {
                $where_clauses[] = "(EXISTS (SELECT 1 FROM penyusutan_komersial_aset WHERE status = 0 AND kode_aset = ma.kode_aset) OR EXISTS (SELECT 1 FROM penyusutan_fiskal_aset WHERE status = 0 AND kode_aset = ma.kode_aset))";
            } elseif ($filter_status === 'Selesai') {
                $where_clauses[] = "(NOT EXISTS (SELECT 1 FROM penyusutan_komersial_aset WHERE status = 0 AND kode_aset = ma.kode_aset) AND NOT EXISTS (SELECT 1 FROM penyusutan_fiskal_aset WHERE status = 0 AND kode_aset = ma.kode_aset))";
            }
        }

        if (!empty($search)) {
            $like = '%' . $search . '%';
            $where_clauses[] = "(ma.kode_aset LIKE ? OR ma.deskripsi_aset LIKE ? OR mak.kategori_name LIKE ? OR mk.nama_kelompok LIKE ? OR pa.pic LIKE ?)";
            $bindings = array_merge($bindings, [$like, $like, $like, $like, $like]);
        }

        if (!empty($where_clauses)) {
            $sql .= " WHERE " . implode(' AND ', $where_clauses);
        }

        $sql .= " ORDER BY ma.id DESC";
        
        $d_conf = $m_conf->hydrateRaw($sql, $bindings);
        $d_aset = $d_conf->count() > 0 ? $d_conf->toArray() : [];

        if (ob_get_level()) ob_end_clean();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Aset');

        $colLetter = function($col) {
            return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
        };

        $sheet->getRowDimension(1)->setRowHeight(5);

        $row_header = 2;
        
        $sheet->setCellValue('B' . $row_header, 'DATA ASET');
        $sheet->mergeCells('B' . $row_header . ':P' . $row_header);

        $sheet->setCellValue('Q' . $row_header, 'TERMIN');
        $sheet->mergeCells('Q' . $row_header . ':T' . $row_header);

        $sheet->setCellValue('U' . $row_header, 'KOMERSIAL');
        $sheet->mergeCells('U' . $row_header . ':X' . $row_header);

        $sheet->setCellValue('Y' . $row_header, 'FISKAL');
        $sheet->mergeCells('Y' . $row_header . ':AB' . $row_header);

        $row_subheader = 3;
        
        $headers_detail = [
            'NO', 'KODE ASET', 'KATEGORI', 'DESKRIPSI ASET', 'NO. BUKTI PEMBELIAN', 'NO. BUKTI PENERIMAAN', 
            'UNIT PENGGUNA', 'KELOMPOK', 'MASA MANFAAT (KOM)', 'MASA MANFAAT (FIS)', 
            'NILAI PEROLEHAN', 'TGL PEROLEHAN', 'TGL PENERIMAAN', 'PIC', 'LOKASI DIGUNAKAN'
        ];
        $sheet->fromArray($headers_detail, null, 'B' . $row_subheader);

        $subheaders_termin = ['DP', 'CICILAN', "JURNAL DIPROSES\nSUDAH / BELUM", 'PENYELESAIAN'];
        $sheet->fromArray($subheaders_termin, null, 'Q' . $row_subheader);

        $subheaders_kom = ['BULAN BERJALAN', 'NOMINAL CICILAN', "JURNAL DIPROSES\nSUDAH / BELUM", 'PENYELESAIAN'];
        $sheet->fromArray($subheaders_kom, null, 'U' . $row_subheader);

        $subheaders_fis = ['BULAN BERJALAN', 'NOMINAL CICILAN', "JURNAL DIPROSES\nSUDAH / BELUM", 'PENYELESAIAN'];
        $sheet->fromArray($subheaders_fis, null, 'Y' . $row_subheader);

        $styleHeader = [
            'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => '363636']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'E0E0E0']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['argb' => '000000']]]
        ];
        $sheet->getStyle('B' . $row_header . ':AB' . $row_subheader)->applyFromArray($styleHeader);
        $sheet->getRowDimension($row_header)->setRowHeight(30);
        $sheet->getRowDimension($row_subheader)->setRowHeight(35); 

        $row_data = 4;
        $no = 1;

        foreach ($d_aset as $row) {
            $sheet->setCellValueExplicit('B' . $row_data, $no++, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $row_data, $row['kode_aset'] ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row_data, $row['kategori_name'] ?? '-');
            $sheet->setCellValue('E' . $row_data, $row['deskripsi_aset'] ?? '-');
            $sheet->setCellValueExplicit('F' . $row_data, $row['document_no'] ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('G' . $row_data, $row['no_bukti_terima'] ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('H' . $row_data, $row['unit_pengguna'] ?? '-');
            $sheet->setCellValue('I' . $row_data, $row['nama_kelompok'] ?? '-');
            $sheet->setCellValue('J' . $row_data, ($row['masa_manfaat_komersial'] ?? 0) . ' Bln');
            $sheet->setCellValue('K' . $row_data, ($row['masa_manfaat_fiskal'] ?? 0) . ' Bln');
            $sheet->setCellValue('L' . $row_data, (float)($row['nilai_perolehan'] ?? 0));
            $sheet->setCellValue('M' . $row_data, !empty($row['tgl_perolehan']) ? tglIndonesia($row['tgl_perolehan'], '-', ' ') : '-');
            $sheet->setCellValue('N' . $row_data, !empty($row['tgl_penerimaan']) ? tglIndonesia($row['tgl_penerimaan'], '-', ' ') : '-');
            $sheet->setCellValue('O' . $row_data, $row['pic'] ?? '-');
            $sheet->setCellValue('P' . $row_data, $row['lokasi_pengguna'] ?? '-');

            $total_termin = (int)($row['sudah_bayar'] ?? 0) + (int)($row['belum_bayar'] ?? 0);
            $sudah_bayar_termin = (int)($row['sudah_bayar'] ?? 0);
            $belum_bayar_termin = (int)($row['belum_bayar'] ?? 0);
            $persen_termin = $total_termin > 0 ? round(($sudah_bayar_termin / $total_termin) * 100, 2) : 0;
            $sheet->setCellValue('Q' . $row_data, (float)($row['nominal_dp'] ?? 0));
            $sheet->setCellValue('R' . $row_data, (float)($row['nominal_cicilan'] ?? 0));
            $sheet->setCellValue('S' . $row_data, $sudah_bayar_termin . ' / ' . $belum_bayar_termin);
            $sheet->setCellValue('T' . $row_data, $persen_termin . '%');

            $total_kom  = ($row['total_sdh_proses_komersial'] ?? 0) + ($row['total_blm_proses_komersial'] ?? 0);
            $sdh_kom    = $row['total_sdh_proses_komersial'] ?? 0;
            $blm_kom    = $row['total_blm_proses_komersial'] ?? 0;
            $persen_kom = $total_kom > 0 ? round(($sdh_kom / $total_kom) * 100, 2) : 0;
            
            $bb_kom_display = '-';
            if (!empty($row['bb_komersial'])) {
                $ts = strtotime($row['bb_komersial']);
                $namaBulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                $bb_kom_display = $namaBulan[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts);
            }

            $sheet->setCellValue('U' . $row_data, $bb_kom_display);
            $sheet->setCellValue('V' . $row_data, (float)($row['nominal_komersial'] ?? 0));
            $sheet->setCellValue('W' . $row_data, $sdh_kom . ' / ' . $blm_kom);
            $sheet->setCellValue('X' . $row_data, $persen_kom . '%');

            $total_fis  = ($row['total_sdh_proses_fiskal'] ?? 0) + ($row['total_blm_proses_fiskal'] ?? 0);
            $sdh_fis    = $row['total_sdh_proses_fiskal'] ?? 0;
            $blm_fis    = $row['total_blm_proses_fiskal'] ?? 0;
            $persen_fis = $total_fis > 0 ? round(($sdh_fis / $total_fis) * 100, 2) : 0;
            
            $bb_fis_display = '-';
            if (!empty($row['bb_fiskal'])) {
                $ts = strtotime($row['bb_fiskal']);
                $namaBulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                $bb_fis_display = $namaBulan[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts);
            }

            $sheet->setCellValue('Y' . $row_data, $bb_fis_display);
            $sheet->setCellValue('Z' . $row_data, (float)($row['nominal_fiskal'] ?? 0));
            $sheet->setCellValue('AA' . $row_data, $sdh_fis . ' / ' . $blm_fis);
            $sheet->setCellValue('AB' . $row_data, $persen_fis . '%');

            $row_data++;
        }

        $last_row = $row_data - 1;

        $styleData = [
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['argb' => '000000']]],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
        ];
        
        $sheet->getStyle('B4:AB' . $last_row)->applyFromArray($styleData);
        
        $cleanStyle = [
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE]],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE]
        ];
        $sheet->getStyle('A4:A' . $last_row)->applyFromArray($cleanStyle);
        $sheet->getStyle('AC4:AC' . $last_row)->applyFromArray($cleanStyle);

        $sheet->getStyle('H4:H' . $last_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('I4:I' . $last_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('J4:J' . $last_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('K4:K' . $last_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('M4:M' . $last_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('N4:N' . $last_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('S4:S' . $last_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('T4:T' . $last_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('U4:U' . $last_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('X4:X' . $last_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('Y4:Y' . $last_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('AA4:AA' . $last_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('AB4:AB' . $last_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle('L4:L' . $last_row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('Q4:R' . $last_row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('V4:V' . $last_row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('Z4:Z' . $last_row)->getNumberFormat()->setFormatCode('#,##0.00');

        $sheet->getColumnDimension('A')->setWidth(2);   
        $sheet->getColumnDimension('B')->setWidth(5);   // NO
        $sheet->getColumnDimension('C')->setWidth(15);  // KODE ASET
        $sheet->getColumnDimension('D')->setWidth(20)->setAutoSize(true);  // KATEGORI
        $sheet->getColumnDimension('E')->setWidth(35)->setAutoSize(true);  // DESKRIPSI
        $sheet->getColumnDimension('F')->setWidth(20);  // NO. BUKTI PEMBELIAN
        $sheet->getColumnDimension('G')->setWidth(20);  // NO. BUKTI PENERIMAAN
        $sheet->getColumnDimension('H')->setWidth(15);  // UNIT
        $sheet->getColumnDimension('I')->setWidth(18);  // KELOMPOK
        $sheet->getColumnDimension('J')->setWidth(16);  // MASA MANFAAT KOM
        $sheet->getColumnDimension('K')->setWidth(16);  // MASA MANFAAT FIS
        $sheet->getColumnDimension('L')->setWidth(18);  // NILAI PEROLEHAN
        $sheet->getColumnDimension('M')->setWidth(14);  // TGL PEROLEHAN
        $sheet->getColumnDimension('N')->setWidth(14);  // TGL PENERIMAAN
        $sheet->getColumnDimension('O')->setWidth(20)->setAutoSize(true);  // PIC
        $sheet->getColumnDimension('P')->setWidth(20);  // LOKASI
        
        foreach (range(17, 28) as $col) {
            $sheet->getColumnDimension($colLetter($col))->setWidth(18);
        }

        $sheet->getColumnDimension('Y')->setWidth(18);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="REKAP_ASET_' . date('Y-m-d_His') . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    
}