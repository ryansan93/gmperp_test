<?php defined('BASEPATH') OR exit('No direct script access allowed');

class HrisKategori extends Public_Controller {

    private $pathView = 'hris/hris_kategori/';
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
                "assets/hris/hris_kategori/js/hris_kategori.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/hris/hris_kategori/css/hris_kategori.css",
            ));

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Master Kategori';
          

            // Load Indexx
            $data['title_menu']     = 'HRIS - Master Kategori';

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);

        } else {
            showErrorAkses();
        }
    }

    public function getKategori($kode = null)
    {
    
        $m_conf = new \Model\Storage\Conf();
        $sql    = " select * from hris_kategori ";

        if (!empty($kode)){
            $sql .= " where kode_kategori = '" . $kode . "'";
        }
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

            foreach ($params['detail'] as $v_det) {

                $m_form_detail = new \Model\Storage\HrisKategori_model();
                $m_form_detail->kode_kategori      = $this->generate_kode();
                $m_form_detail->nama_kategori      = $v_det['nama_kategori'];
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
        $content['kategori']        = $this->getKategori($params['kode_kategori'])[0];
        //  cetak_r($content, 1);

        echo $this->load->view($this->pathView . 'v_edit_data', $content, TRUE);
    }

    public function update()
    {
        $params = $_POST;

        // cetak_r($params, 1);


        try {
            $kode_kategori = $params['kode_kategori'];

            $m_kategori = new \Model\Storage\HrisKategori_model();

            $d_kategori = $m_kategori->where('kode_kategori', $kode_kategori)->first();
            if (!$d_kategori) {
                throw new \Exception("Data form tidak ditemukan.");
            }

            $m_kategori->where('kode_kategori', $kode_kategori)->update([
                'nama_kategori' => $params['nama_kategori'],
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
        $id_form = $params['kode_kategori'];

        $m_form = new \Model\Storage\HrisKategori_model();

        try {

            $m_form->where('kode_kategori', $id_form)->delete();

    

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';

        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }


    public function load_form(){

        $content['list'] =  $this->getKategori();
        // cetak_r($content, 1);


        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }

    public function generate_kode(){
        $m_kategori = new \Model\Storage\HrisKategori_model();
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

}