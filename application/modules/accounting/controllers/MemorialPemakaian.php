<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MemorialPemakaian extends Public_Controller {

    private $pathView = 'accounting/memorial_pemakaian/';
    private $url;
    private $hakAkses;

    function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;
        $this->hakAkses = hakAkses($this->url);
    }

    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/
    /**
     * Default
     */
    public function index($segment=0)
    {
        // if ( $this->hakAkses['a_view'] == 1 ) {
            $this->add_external_js(array(
                "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
                "assets/select2/js/select2.min.js",
                "assets/accounting/memorial_pemakaian/js/memorial_pemakaian.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/accounting/memorial_pemakaian/css/memorial_pemakaian.css",
            ));

            $data       = $this->includes;

            $m_wilayah  = new \Model\Storage\Wilayah_model();
            $m_coa      = new \Model\Storage\Coa_model();

    
            $content['akses']       = $this->hakAkses;
            $content['title_panel'] = 'Memorial Pemakaian';
            $content['unit']        = $m_wilayah->getDataUnit(1, $this->userid);
            $content['plasma']      = $this->getDataPlasma();
            $content['coa']         = $m_coa->getDataCoa();

            // echo "<pre>";
            // print_r($content['plasma']);
            // die;

            // Load Indexx
            $data['title_menu'] = 'Memorial Pemakaian';
            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);
        // } else {
        //     showErrorAkses();
        // }
    }

    public function add_data()
    {
        $m_wilayah  = new \Model\Storage\Wilayah_model();
        $m_coa      = new \Model\Storage\Coa_model();
    
        $content['akses']       = $this->hakAkses;
        $content['title_panel'] = 'Memorial Pemakaian';
        $content['unit']        = $m_wilayah->getDataUnit(1, $this->userid);
        $content['plasma']      = $this->getDataPlasma();
        $content['coa']         = $m_coa->getDataCoa();

        $html = $this->load->view($this->pathView.'v_add_data', $content, true);
        echo $html;
    }


    public function getListData($start_date, $end_date)
    {
        $m_conf = new \Model\Storage\Conf();
        $sql    = " SELECT 
                        mp.*,
                        x.total_debet,
                        x.total_kredit
                    FROM mmpem mp
                    INNER JOIN (
                        SELECT 
                            no_mmpem,
                            SUM(CASE 
                                WHEN coa_asal IS NOT NULL AND coa_asal <> '' THEN nilai 
                                ELSE 0 
                            END) AS total_debet,
                            
                            SUM(CASE 
                                WHEN coa_tujuan IS NOT NULL AND coa_tujuan <> '' THEN nilai 
                                ELSE 0 
                            END) AS total_kredit
                        FROM mmpem_item
                        GROUP BY no_mmpem
                    ) x ON mp.no_mmpem = x.no_mmpem ";
                    
                    if($start_date){
                         $sql .= " where mp.tgl_memo between '".$start_date."' and '".$end_date."' ";
                    }


                    $sql .= " ORDER BY mp.id DESC ";
        $d_conf = $m_conf->hydrateRaw($sql);
        
        $data = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

    public function getLists()
    {
        $params     = $this->input->get('params');
        $start_date = $params['start_date'];
        $end_date   = $params['end_date'];

        $content['data'] = $this->getListData($start_date, $end_date);
        // echo "<pre>";
        // print_r($content);
        // die;

        $html = $this->load->view($this->pathView . 'v_list', $content, true);

        echo $html;
    }

    public function riwayat() {
        $start_date = substr(date('Y-m-d'), 0, 7).'-01';
        $end_date = date("Y-m-t", strtotime($start_date));

        $content['start_date'] = $start_date;
        $content['end_date'] = $end_date;
        $content['akses'] = $this->hakAkses;
        $html = $this->load->view($this->pathView . 'riwayat', $content, TRUE);

        return $html;
    }


    // public function viewForm($kode)
    // {
    //     $m_mm = new \Model\Storage\Mm_model();
    //     $d_mm = $m_mm->getMm( $kode )[0];

    //     $m_mmi = new \Model\Storage\MmItem_model();
    //     $d_mmi = $m_mmi->getMmItem( $kode );

    //     $m_log = new \Model\Storage\LogTables_model();
    //     $d_log = $m_log->getLog($m_mm->table, $kode);

    //     $content['akses'] = $this->hakAkses;
    //     $content['data'] = $d_mm;
    //     $content['detail'] = $d_mmi;
    //     $content['log'] = !empty($d_log) ? $d_log : null;

    //     $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

    //     return $html;
    // }

    // public function editForm($kode)
    // {
    //     $m_coa = new \Model\Storage\Coa_model();
    //     $m_plg = new \Model\Storage\Pelanggan_model();
    //     $m_supl = new \Model\Storage\Supplier_model();
    //     $m_wilayah = new \Model\Storage\Wilayah_model();
    //     $m_jt = new \Model\Storage\JurnalTrans_model();
    //     $m_djt = new \Model\Storage\DetJurnalTrans_model();
    //     $m_mm = new \Model\Storage\Mm_model();
    //     $d_mm = $m_mm->getMm( $kode )[0];
    //     $m_mmi = new \Model\Storage\MmItem_model();
    //     $d_mmi = $m_mmi->getMmItem( $kode );
    //     $content['coa'] = $m_coa->getDataCoa();
    //     $content['pelanggan'] = $m_plg->getDataPelanggan();
    //     $content['supplier'] = $m_supl->getDataSupplier();
    //     $content['unit'] = $m_wilayah->getDataUnit(1, $this->userid);
    //     $content['jurnal_trans'] = $m_jt->getJurnalTransByUrl( $this->url );
    //     $content['det_jurnal_trans'] = $m_djt->getDetJurnalTransByUrl( $this->url );
    //     $content['data'] = $d_mm;
    //     $content['detail'] = $d_mmi;
    //     $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);
    //     return $html;
    // }

    public function save()
    {
        $params = $this->input->post('params');

        // echo "<pre>";
        // print_r($params);
        // die;

        try {
            // cetak_r( $params, 1 );

            $no_mm      = $this->generateNoMemo($params['tgl_mm']);
            $m_mm       = new \Model\Storage\MmPem_model();
            // $now        = $m_mm->getDate();

            $m_mm->no_mmpem        = $no_mm;
            // $m_mm->tgl_mm       = $params['tgl_mm'];
            $m_mm->periode      = substr($params['tgl_mm'], 0, 7);
            $m_mm->keterangan   = $params['keterangan'];
            $m_mm->nilai        = $params['nilai'];
            $m_mm->tgl_memo     = $params['tgl_mm'];
  
  
            $m_mm->save();

            $no_urut = 1;
            foreach ($params['detail'] as $v_det) {

                $m_mmi                  = new \Model\Storage\MmPemItem_model();
                $m_mmi->no_mmpem        = $no_mm;
                $m_mmi->tgl_mmpem       = $params['tgl_mm'];
                $m_mmi->no_urut         = $no_urut; 
                $m_mmi->coa_asal        = !empty($v_det['coa_asal']) ? $v_det['coa_asal'] : '';
                $m_mmi->coa_tujuan      = !empty($v_det['coa_tujuan']) ? $v_det['coa_tujuan'] : '';
                $m_mmi->keterangan      = $v_det['keterangan'] ?? '';
                $m_mmi->nilai           = $v_det['nilai'] ?? 0;
                $m_mmi->unit            = $v_det['unit'] ?? '';

                $m_mmi->mitra_plasma    = $v_det['plasma'] ?? '';
                $m_mmi->noreg           = $v_det['noreg'] ?? '';
                $m_mmi->coa_asal_nama   = $v_det['coa_asal_nama'] ?? '';
                $m_mmi->coa_tujuan_nama = $v_det['coa_tujuan_nama'] ?? '';
                $m_mmi->umur_lhk        = $v_det['umur_lhk'] ?? '';
                $m_mmi->lhk_id          = $v_det['id_lhk'] ?? '';

                $m_mmi->save();

                $no_urut++; 
            }


            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_mm, $deskripsi_log, null, $no_mm );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
            $this->result['content'] = array('id' => $no_mm);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function edit()
    {
        $params = $this->input->post('params');

        // echo "<pre>";
        // print_r($params);
        // die;

        try {
            $no_mm = $params['no_mmpem'];

            $m_mm = new \Model\Storage\MmPem_model();
            $m_mm = $m_mm->where('no_mmpem', $no_mm)->first();

            if ( !$m_mm ) {
                throw new Exception("Data tidak ditemukan.");
            }

            $m_mm->periode     = substr($params['tgl_mm'], 0, 7);
            $m_mm->keterangan  = $params['keterangan'];
            $m_mm->nilai       = $params['nilai'];
            $m_mm->tgl_memo    = $params['tgl_mm'];

            $m_mm->save();

            $m_mmi = new \Model\Storage\MmPemItem_model();
            $m_mmi->where('no_mmpem', $no_mm)->delete();

            $no_urut = 1;
            foreach ($params['detail'] as $v_det) {

                $m_mmi                  = new \Model\Storage\MmPemItem_model();
                $m_mmi->no_mmpem        = $no_mm;
                $m_mmi->tgl_mmpem       = $params['tgl_mm'];
                $m_mmi->no_urut         = $no_urut; 
                $m_mmi->coa_asal        = !empty($v_det['coa_asal']) ? $v_det['coa_asal'] : '';
                $m_mmi->coa_tujuan      = !empty($v_det['coa_tujuan']) ? $v_det['coa_tujuan'] : '';
                $m_mmi->keterangan      = $v_det['keterangan'] ?? '';
                $m_mmi->nilai           = $v_det['nilai'] ?? 0;
                $m_mmi->unit            = $v_det['unit'] ?? '';
                $m_mmi->mitra_plasma    = $v_det['plasma'] ?? '';
                $m_mmi->noreg           = $v_det['noreg'] ?? '';
                $m_mmi->coa_asal_nama   = $v_det['coa_asal_nama'] ?? '';
                $m_mmi->coa_tujuan_nama = $v_det['coa_tujuan_nama'] ?? '';
                $m_mmi->umur_lhk        = $v_det['umur_lhk'] ?? '';
                $m_mmi->lhk_id          = $v_det['id_lhk'] ?? '';

                $m_mmi->save();

                $no_urut++; 
            }

            $deskripsi_log = 'di-edit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/save', $m_mm, $deskripsi_log, null, $no_mm);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di update.';
            $this->result['content'] = array('id' => $no_mm);

        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }

    public function delete()
    {
        $params = $this->input->post('params');

        try {            
            $no_mmpem = $params['no_mmpem'];

            $m_mm = new \Model\Storage\MmPem_model();
            $d_mm = $m_mm->where('no_mmpem', $no_mmpem)->first();
            
            $m_mmi = new \Model\Storage\MmPemItem_model();
            $m_mmi->where('no_mmpem', $no_mmpem)->delete();

            $m_mm->where('no_mmpem', $no_mmpem)->delete();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_mm, $deskripsi_log, null, $no_mmpem );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    // public function updatePo($no_po)
    // {
    //     $m_conf = new \Model\Storage\Conf();
    //     $sql = "
    //         select 
    //             pi.po_no as no_po,
    //             pi.item_kode as item_kode,
    //             pi.harga as harga,
    //             pi.jumlah as jumlah_po,
    //             isnull(t.jumlah_terima, 0) as jumlah_terima
    //         from po_item pi
    //         right join
    //             po p 
    //             on
    //                 pi.po_no = p.no_po
    //         left join
    //             (
    //                 select ti.item_kode, ti.harga, sum(ti.jumlah_terima) as jumlah_terima, t.po_no from terima_item ti 
    //                 right join
    //                     terima t
    //                     on
    //                         ti.terima_kode = t.kode_terima 
    //                 where
    //                     t.po_no is not null
    //                 group by
    //                     ti.item_kode, ti.harga, t.po_no
    //             ) t
    //             on
    //                 t.po_no = p.no_po and
    //                 t.item_kode = pi.item_kode
    //         where
    //             pi.jumlah > isnull(t.jumlah_terima, 0) and
    //             p.no_po = '".$no_po."'
    //     ";
    //     $d_po = $m_conf->hydrateRaw( $sql );

    //     if ( $d_po->count() == 0 ) {
    //         $m_po = new \Model\Storage\Po_model();
    //         $m_po->where('no_po', $no_po)->update(
    //             array('done' => 1)
    //         );
    //     } else {
    //         $m_po = new \Model\Storage\Po_model();
    //         $m_po->where('no_po', $no_po)->update(
    //             array('done' => 0)
    //         );
    //     }
    // }

    public function printPreview($no_mmpem) {        
        $kode = exDecrypt( $no_mmpem );

      

        $m_mm = new \Model\Storage\Mmpem_model();
        $d_mm = $m_mm->getMmPem( $kode )[0];

       

        $m_mmi = new \Model\Storage\MmPemItem_model();
        $d_mmi = $m_mmi->getMmPemItem( $kode );

        //   echo "<pre>";
        // print_r($d_mmi);
        // die;

        $m_prs = new \Model\Storage\Perusahaan_model();
        $d_prs = $m_prs->orderBy('id', 'desc')->with(['d_kota'])->first();


        $content['perusahaan'] = $d_prs->toArray();
        $content['data'] = $d_mm;
        $content['detail'] = $d_mmi;

        $res_view_html = $this->load->view($this->pathView.'v_export_pdf', $content, true);

        echo $res_view_html;
    }

    public function exportPdf()
    {
        $params = $this->input->post('params');

        try {
            $_no_mm = $params['kode'];
            
            $kode = exDecrypt( $_no_mm );
            // $kode = 'FP2312060006';

            $m_mm = new \Model\Storage\Mm_model();
            $d_mm = $m_mm->getMmCetak( $kode );

            $struktur = "";
            $text = "";
            foreach ($d_mm as $k_mm => $v_mm) {
                $idx = 1;
                foreach ($v_mm as $key => $value) {
                    $struktur .= '"'.$key.'"';
                    $text .= '"'.$value.'"';
                    if ( $idx < count($v_mm) ) {
                        $struktur .= ',';
                        $text .= ',';
                    }

                    $idx++;
                }

                $text .= "\n";
            }

            $content = $struktur."\n".$text;
            $fp = fopen("cetak/cmmcet.TXT","wb");
            fwrite($fp,$content);
            fclose($fp);

            system("cmd /c C:/xampp_php7/htdocs/sistem_udlancar/copy_file.bat");

            // $m_mm = new \Model\Storage\Mm_model();
            // $d_mm = $m_mm->getMm( $kode )[0];

            // $m_mmi = new \Model\Storage\MmItem_model();
            // $d_mmi = $m_mmi->getMmItem( $kode );

            // $content['data'] = $d_mm;
            // $content['detail'] = $d_mmi;

            // $res_view_html = $this->load->view($this->pathView.'exportPdf', $content, true);

            // $this->load->library('PDFGenerator');
            // // $this->pdfgenerator->generate($res_view_html, $kode, "letter", "portrait");
            // $this->pdfgenerator->upload($res_view_html, $kode, "letter", "portrait", "uploads/po/");

            // $path = "uploads/po/".$kode.".pdf";

            $this->result['status'] = 1;
            // $this->result['content'] = array('url' => $path);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function tes()
    {
        $tanggal = '2025-11-10';
        $periode = substr(str_replace('-', '', $tanggal), 2, 6);

        cetak_r( $periode );
    }

    public function getDataPlasma()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = " select distinct m.nomor, m.nama, mm.nim from mitra m
                inner join mitra_mapping mm on m.id = mm.mitra 
                order by m.nama asc ";
        $d_conf = $m_conf->hydrateRaw($sql);
        

        $data = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

    public function setDataNoreg()
    {
        $nomor  = $_POST['nomor'];
        $m_conf = new \Model\Storage\Conf();
        $sql    = " select * from rdim_submit where nim = '$nomor'    ";
        $d_conf = $m_conf->hydrateRaw($sql);
        

        $data = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        echo json_encode($data);
    }

    public function setUmurLhk()
    {
        $noreg  = $_POST['noreg'];
        $m_conf = new \Model\Storage\Conf();
        $sql    = " select id, noreg, tgl_docin, umur from lhk where noreg = '$noreg'    ";
        $d_conf = $m_conf->hydrateRaw($sql);
        

        $data = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        echo json_encode($data);
    }

    public function getStatusMemoPemakaian($no_mmpem)
    {
        $m_conf = new \Model\Storage\Conf();

        $sql = " select top 1 id, tbl_id, deskripsi, waktu
                from log_tables where tbl_name = 'mmpem'
                and tbl_id  = '". $no_mmpem ."'
                order by id desc ";

        // echo "<pre>";
        // print_r($sql);
        // die;

        $d_conf = $m_conf->hydrateRaw($sql);
        

        $data = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->first();
        }

        return $data;
    }

    public function generateNoMemo($date)
    {
        $m_conf = new \Model\Storage\Conf();
        $kode = 'MM-PEM';
        $time = strtotime($date) ?: time();

        $tahun = date('y', $time); 
        $bulan = date('m', $time); 

        $prefix = $kode . $tahun . $bulan;

        $sql = "
            SELECT TOP 1 no_mmpem 
            FROM mmpem 
            WHERE no_mmpem LIKE '{$prefix}%'
            ORDER BY no_mmpem DESC
        ";

        $d_conf = $m_conf->hydrateRaw($sql);

        $last = $d_conf->first(); 

        if ($last) {
            $last_no = substr($last->no_mmpem, -4);
            $next_no = (int)$last_no + 1;
        } else {
            $next_no = 1;
        }

        $sequence = str_pad($next_no, 4, '0', STR_PAD_LEFT);

        return $prefix . $sequence;
    }

    public function showDetailMemoPemakaian()
    {

        $m_wilayah  = new \Model\Storage\Wilayah_model();

     
        $content['header_data'] = $_POST;
        $content['detail_data'] = $this->getDetailData($_POST['no_mmpem']);
        $content['unit']        = $m_wilayah->getDataUnit(1, $this->userid);
        $content['plasma']      = $this->getDataPlasma();
        $content['status']      = $this->getStatusMemoPemakaian($_POST['no_mmpem']);
     
        $html = $this->load->view($this->pathView.'v_detail', $content, true);

        echo $html;
    }

    public function getDetailData($no_mmpem){

        $m_conf = new \Model\Storage\Conf();
        $sql    = " select * from mmpem_item where no_mmpem = '".$no_mmpem. "'";
        $d_conf = $m_conf->hydrateRaw($sql);

        $data = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

    public function edit_data()
    {

        $m_wilayah  = new \Model\Storage\Wilayah_model();
        $m_coa      = new \Model\Storage\Coa_model();


        $content['detail_data'] = $this->getDetailData($_POST['no_mmpem']);
        $content['unit']        = $m_wilayah->getDataUnit(1, $this->userid);
        $content['plasma']      = $this->getDataPlasma();
        $content['coa']         = $m_coa->getDataCoa();

        
        $html = $this->load->view($this->pathView.'v_edit', $content, true);
        echo $html;
    }
}