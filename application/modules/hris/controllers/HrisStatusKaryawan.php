<?php defined('BASEPATH') OR exit('No direct script access allowed');

class HrisStatusKaryawan extends Public_Controller {

    private $pathView = 'hris/hris_status_karyawan/';
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
                "assets/hris/hris_status_karyawan/js/hris_status_karyawan.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/hris/hris_status_karyawan/css/hris_status_karyawan.css",
            ));

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Status Karyawan';
            $content['kategori']        = $this->getKategori();

            // cetak_r($content, 1);
          

            // Load Indexx
            $data['title_menu']     = 'HRIS - Status Karyawan';

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);

        } else {
            showErrorAkses();
        }
    }

    public function get_list($kode = null)
    {
    
        $m_conf = new \Model\Storage\Conf();
        
        $sql    = " select hsk.id as id_data, * from hris_status_karyawan hsk
        inner join hris_kategori hk on hsk.kategori = hk.kode_kategori ";

        if(!empty($kode))
        {
             $sql .=" where hsk.id = " . $kode;
        }
        $sql .=" order by hsk.id desc ";

        // cetak_r($sql, 1);

        $d_conf = $m_conf->hydrateRaw( $sql );
        $data   = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }


    public function save()
    {

        $params = $_POST;
        // cetak_r($params, 1);
        
        try {

            foreach ($params['data'] as $v_det) {

                $m_form_detail = new \Model\Storage\HrisStatusKaryawan_model();
                $m_form_detail->nama_status   = $v_det['nama_status'];
                $m_form_detail->kategori      = $v_det['kategori'];
                $m_form_detail->save();

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


    public function edit_data()
    {
        $params = $_POST;
        // cetak_r($params, 1);
        $data                       = $this->includes;
        $content['status_karyawan'] = $this->get_list($params['id_data']);
        $content['kategori']        = $this->getKategori();
        //  cetak_r($content, 1);

        echo $this->load->view($this->pathView . 'v_edit_data', $content, TRUE);
    }

    public function update()
    {
        $params = $_POST;

        // cetak_r($params, 1);


        try {
            $id_data = $params['id_data'];

            $m_status = new \Model\Storage\HrisStatusKaryawan_model();

            $d_status = $m_status->where('id', $id_data)->first();
            if (!$d_status) {
                throw new \Exception("Data form tidak ditemukan.");
            }

            $m_status->where('id', $id_data)->update([
                'nama_status' => $params['nama_status'],
                'kategori' => $params['kategori'],
            ]);

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
        $id_data = $params['id_data'];

        $m_status = new \Model\Storage\HrisStatusKaryawan_model();

        try {

            $m_status->where('id', $id_data)->delete();

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';

        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }


    public function load_form(){

        $content['list'] =  $this->get_list();
        // cetak_r($content, 1);


        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }

    public function generate_kode(){
        $m_kategori = new \Model\Storage\HrisStatusKaryawan_model();
        $last = $m_kategori->where('kode_kategori', 'like', 'HRIS/K/%')->orderBy('kode_kategori', 'desc')->first();

        $no = 1;

        if ($last) {
            // ambil angka terakhir (001, 002, dst)
            $last_kode = $last->kode_kategori;
            $explode = explode('/', $last_kode);
            $no = (int)$explode[2] + 1;
        }

        // format jadi 3 digit
        $no_format = str_pad($no, 3, '0', STR_PAD_LEFT);

        // hasil akhir
        $kode_kategori = 'HRIS/K/' . $no_format;

        return $kode_kategori;
    }


    public function getKategori()
    {
    
        $m_conf = new \Model\Storage\Conf();
        $sql    = " select * from hris_kategori ";

        // cetak_r($sql, 1);

        $d_conf = $m_conf->hydrateRaw( $sql );
        $data   = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

}