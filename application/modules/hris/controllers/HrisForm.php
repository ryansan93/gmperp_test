<?php defined('BASEPATH') OR exit('No direct script access allowed');

class HrisForm extends Public_Controller {

    private $pathView = 'hris/hris_form/';
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
                "assets/hris/hris_form/js/hris_form.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/hris/hris_form/css/hris_form.css",
            ));

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Hris Form';
            $content['add_kategori']    = $this->getKategori();

            // cetak_r($content['kategori'], 1);
            $content['kategori']        = $this->getKategori();

            // Load Indexx
            $data['title_menu']     = 'HRIS - Hris Form';

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
            "assets/hris/hris_form/js/hris_form.js",
        ));
        $this->add_external_css(array(
            "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
            "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
            "assets/select2/css/select2.min.css",
            "assets/hris/hris_form/css/hris_form.css",
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
            $m_form     = new \Model\Storage\HrisForm_model();

            $m_form->title          = $params['header']['title'];
            $m_form->keterangan     = $params['header']['keterangan'];
            $m_form->urutan         = $params['header']['urutan'];
            $m_form->kategori       = $params['header']['kategori'];
            $m_form->save();

            $id_form = $m_form->id;

            foreach ($params['detail'] as $v_det) {

                $m_form_detail = new \Model\Storage\HrisFormDetail_model();
                $m_form_detail->id_form     = $id_form;
                $m_form_detail->nama        = $v_det['label'];
                $m_form_detail->urutan      = $v_det['urutan'];
                $m_form_detail->parent_column  = $v_det['parent_label'];
                $m_form_detail->kode_detail = 'DTL-'. $id_form;
                $m_form_detail->save();

            }

            $id            = $m_form->id;
            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/save', $m_form, $deskripsi_log, null, $id, $m_form);


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

        $content['list'] =  $this->get_list_data($_POST['kategori']);
        // cetak_r($_POST, 1);


        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }


    public function get_list($id )
    {
         
        $m_conf     = new \Model\Storage\Conf();
        $sql        = " select * from hris_form hf 
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


    public function get_list_data($kategori = null)
    {
         
        $m_conf     = new \Model\Storage\Conf();
        $sql        = " select * from hris_form hf 
                        inner join hris_kategori hk on hf.kategori = hk.kode_kategori ";

        if (!empty($kategori)){
            $sql .= " where hf.kategori = '".$kategori."' ";
        }

        $sql .= " order by id desc ";

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
        $sql        = " select * from hris_form_detail where id_form = " . $id;
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
            "assets/hris/hris_form/js/hris_form.js",
        ));
        $this->add_external_css(array(
            "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
            "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
            "assets/select2/css/select2.min.css",
            "assets/hris/hris_form/css/hris_form.css",
        ));

        $data                       = $this->includes;
        $content['header']          = $this->get_list($_GET['id_data'])[0];
        $content['detail']          = $this->get_list_data_ketegori($_GET['id_data']);
        $content['add_kategori']    = $this->getKategori();
        //  cetak_r($content, 1);
        $content['title_panel']     = 'HRIS - Hris Form / Edit Data';

        $data['view']         = $this->load->view($this->pathView . 'v_edit_data', $content, TRUE);
        $this->load->view($this->template, $data);
    }

    public function update()
    {
        $params = $_POST;

        //  cetak_r($params, 1);


        try {
            $id_form = (int) $params['id_data'];

            $m_form = new \Model\Storage\HrisForm_model();

            $d_form = $m_form->where('id', $id_form)->first();
            if (!$d_form) {
                throw new \Exception("Data form tidak ditemukan.");
            }


            $m_form->where('id', $id_form)->update([
                'title'        => $params['header']['title'],
                'keterangan'   => $params['header']['keterangan'],
                'urutan'       => $params['header']['urutan'],
                'kategori'     => $params['header']['kategori']
            ]);

          
            $m_form_detail = new \Model\Storage\HrisFormDetail_model();
            $m_form_detail->where('id_form', $id_form)->delete();

            if (!empty($params['detail'])) {
                foreach ($params['detail'] as $v_det) {
                    $m_form_detail = new \Model\Storage\HrisFormDetail_model();

                    $m_form_detail->id_form = $id_form;
                    $m_form_detail->nama    = $v_det['label'];
                    $m_form_detail->urutan  = $v_det['urutan'];
                    $m_form_detail->parent_column  = $v_det['parent_label'];
                    $m_form_detail->kode_detail      = 'DTL-'. $id_form;
                    $m_form_detail->save();
                }
            }

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/update', $m_form, $deskripsi_log, null, $id_form, $m_form);

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
        $id_form = (int) $params['id'];

        $m_form = new \Model\Storage\HrisForm_model();
        $m_form_detail = new \Model\Storage\HrisFormDetail_model();

        try {
            // delete detail
            $m_form_detail = new \Model\Storage\HrisFormDetail_model();
            $m_form_detail->where('id_form', $id_form)->delete();

            // delete header
            $m_form->where('id', $id_form)->delete();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/delete', $m_form, $deskripsi_log, null, $id_form, $m_form);

    

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';

        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }

}