<?php defined('BASEPATH') OR exit('No direct script access allowed');

class HrisUsulanKaryawan extends Public_Controller {

    private $pathView = 'hris/hris_usulan_karyawan/';
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
                "assets/hris/hris_usulan_karyawan/js/hris_usulan_karyawan.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/hris/hris_usulan_karyawan/css/hris_usulan_karyawan.css",
            ));

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Hris Usulan Karyawan';
            $content['karyawan']        = $this->get_data_karyawan();
            $content['kandidat']        = $this->get_data_kandidat();

            // cetak_r($content['kadidat'], 1);
            $content['kategori']        = $this->getKategori();

            // Load Indexx
            $data['title_menu']     = 'HRIS - Usulan Karyawan';

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);

        } else {
            showErrorAkses();
        }
    }

    public function getKategori()
    {
    
        $m_conf = new \Model\Storage\Conf();
        $sql    = " select * from hris_kategori ";

        $d_conf = $m_conf->hydrateRaw( $sql );
        $data   = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

    public function add_data()
    {  
        $this->add_external_js(array(
            "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
            "assets/select2/js/select2.min.js",
            "assets/hris/hris_usulan_karyawan/js/hris_usulan_karyawan.js",
        ));
        $this->add_external_css(array(
            "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
            "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
            "assets/select2/css/select2.min.css",
            "assets/hris/hris_usulan_karyawan/css/hris_usulan_karyawan.css",
        ));

        $data                 = $this->includes;

        $data['view']         = $this->load->view($this->pathView . 'v_add_data', $content, TRUE);
        $this->load->view($this->template, $data);
    }

    public function save()
    {
        

        $params = $_POST;
        // cetak_r($params, 1);
        
        try {
            $m_db     = new \Model\Storage\HrisUsulanKaryawan_model();

            $m_db->nama_pengusul  = $params['header']['mengusulkan'];
            $m_db->tgl_pengusulan = $params['header']['tgl_pengusulan'];
            $m_db->posisi         = $params['header']['posisi'];
            $m_db->jumlah         = $params['header']['jumlah'];
            $m_db->unit           = $params['header']['unit'];
            $m_db->alasan         = $params['header']['alasan'];
            $m_db->status         = 1;
            $m_db->save();
            $id_header = $m_db->id;

            foreach ($params['detail'] as $v_det) {
                $m_db_detail = new \Model\Storage\HrisUsulanKaryawanDetail_model();
                $m_db_detail->id_header     = $id_header;
                $m_db_detail->id_kandidat   = $v_det['id_kandidat'];
                $m_db_detail->save();

            }

            // $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            // Modules::run( 'base/event/save', $m_mm, $deskripsi_log, null, $no_mm );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
            // $this->result['content'] = array('id' => $no_mm);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );

    }


    public function load_form(){

        $content['list'] =  $this->get_list_data();
        // cetak_r($content, 1);


        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }

     public function filter_data(){

        $content['list'] =  $this->get_list_data($_POST['pengaju']);
        // cetak_r($_POST, 1);


        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }

    public function get_data_karyawan()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "select * from karyawan order by nama asc";

        $d_conf = $m_conf->hydrateRaw($sql);
        $data = null;

        if ($d_conf->count() > 0) {
            $rows = $d_conf->toArray();

            $unik = [];
            foreach ($rows as $row) {
                $nik = $row['nik'];

                if (!isset($unik[$nik])) {
                    $unik[$nik] = $row;
                }
            }

            $data = array_values($unik);
        }

        return $data;
    }


    public function get_list($id )
    {
         
        $m_conf     = new \Model\Storage\Conf();
        $sql        = " select * from hris_usulan_karyawan hf 
                        inner join hris_kategori hk on hf.kategori = hk.kode_kategori ";

        if (!empty($id)){
            $sql .= " where id = ". $id ;
        }

        // cetak_r($sql, 1);

        $d_conf     = $m_conf->hydrateRaw( $sql );
        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }


    public function get_list_data($id = null)
    {
        
         
        $m_conf     = new \Model\Storage\Conf();
        $sql        = " SELECT hukb.*, k.nama, k.jabatan
                        FROM hris_usulan_karyawan_baru hukb
                        INNER JOIN (
                            SELECT *
                            FROM karyawan
                            WHERE id IN (
                                SELECT MAX(id)
                                FROM karyawan
                                GROUP BY nik
                            )
                        ) k ON hukb.nama_pengusul = k.nik";

                    if (!empty($id)){
                        $sql .= " where hukb.nama_pengusul = '" . $id . "'";
                    }

                        $sql .= " ORDER BY hukb.id DESC ";

        // cetak_r($sql, 1);


        // if (!empty($kategori)){
        //     $sql .= " where hf.kategori = '".$kategori."' ";
        // }



        // cetak_r($sql, 1);

        $d_conf     = $m_conf->hydrateRaw( $sql );
        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

    public function show_detail()
    {
        $content['header']  = $this->get_list($_POST['id'])[0];
        $content['detail']  = $this->get_list_data_ketegori($_POST['id']);
        // cetak_r($content, 1);

        echo $this->load->view($this->pathView . 'v_detail', $content, TRUE);
    }

    public function get_list_data_ketegori($id)
    {

        $m_conf     = new \Model\Storage\Conf();
        $sql        = " select * from hris_usulan_karyawan_detail where id_form = " . $id;
        $d_conf     = $m_conf->hydrateRaw( $sql );
        // cetak_r(123, 1);
        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;

    } 

    public function edit_data()
    {
        

        $this->add_external_js(array(
            "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
            "assets/select2/js/select2.min.js",
            "assets/hris/hris_usulan_karyawan/js/hris_usulan_karyawan.js",
        ));
        $this->add_external_css(array(
            "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
            "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
            "assets/select2/css/select2.min.css",
            "assets/hris/hris_usulan_karyawan/css/hris_usulan_karyawan.css",
        ));

        $data                       = $this->includes;
        $content['karyawan']        = $this->get_data_karyawan();
        $content['kandidat']        = $this->get_data_kandidat();
        $content['edit_data']       = $this->get_list_data($_GET['id_data']);
        $content['detail']          = $this->get_data_detail($_GET['id_data']);
        //  cetak_r($content, 1);
        $content['title_panel']     = 'HRIS - Hris Usulan Karyawan / Edit Data';

        $data['view']         = $this->load->view($this->pathView . 'v_edit_data', $content, TRUE);
        $this->load->view($this->template, $data);
    }

    public function get_data_detail($id)
    {
        $m_conf     = new \Model\Storage\Conf();

        $sql = "select * from hris_usulan_karyawan_baru_detail hukbd
        inner join hris_data_karyawan hdk on hdk.id =  hukbd.id_kandidat
        where hukbd.id_header = " . $id;

        // cetak_r($sql, 1);

        $d_conf     = $m_conf->hydrateRaw( $sql );
        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

    public function update()
    {
        $params = $_POST;

        // cetak_r($params, 1);

        try {

            $id_data = (int) $params['id_data'];
            
            $m_db     = new \Model\Storage\HrisUsulanKaryawan_model();

            $m_db->where('id', $id_data)->update([
                'nama_pengusul'     => $params['header']['mengusulkan'],
                'tgl_pengusulan'    => $params['header']['tgl_pengusulan'],
                'posisi'            => $params['header']['posisi'],
                'jumlah'            => $params['header']['jumlah'],
                'unit'              => $params['header']['unit'],
                'alasan'            => $params['header']['alasan'],
                'status'            => 1,
            ]);
          
            $m_db_detail = new \Model\Storage\HrisUsulanKaryawanDetail_model();
            $m_db_detail->where('id_header', $id_data)->delete();

          if (!empty($params['detail'])) {
                foreach ($params['detail'] as $v_det) {

                    $m_db_detail = new \Model\Storage\HrisUsulanKaryawanDetail_model();

                    $m_db_detail->id_header   = $id_data;
                    $m_db_detail->id_kandidat = $v_det['id_kandidat'];
                    $m_db_detail->save();
                }
            }

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di update.';

        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }


    public function delete()
    {
        $params = $_POST;
        // cetak_r($params, 1);
        $id_data = (int) $params['id_data'];

        $m_db     = new \Model\Storage\HrisUsulanKaryawan_model();
        

        try {
            // delete detail
            $m_db_detail = new \Model\Storage\HrisUsulanKaryawanDetail_model();
            $m_db_detail->where('id_header', $id_data)->delete();

            // delete header
            $m_db->where('id', $id_data)->delete();

    

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';

        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }

    public function get_data_kandidat()
    {

        $m_conf     = new \Model\Storage\Conf();

        $sql = "select * from hris_data_karyawan ";

        $d_conf     = $m_conf->hydrateRaw( $sql );
        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

}