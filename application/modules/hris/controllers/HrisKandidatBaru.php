<?php defined('BASEPATH') OR exit('No direct script access allowed');

class HrisKandidatBaru extends Public_Controller {

    private $pathView = 'hris/hris_kandidat_baru/';
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
                "assets/hris/hris_kandidat_baru/js/hris_kandidat_baru.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/hris/hris_kandidat_baru/css/hris_kandidat_baru.css",
            ));

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Kadidat Baru';
            $content['status']          = $this->get_status_karyawan();
            $content['usulan']          = $this->get_data_usulan();

            // cetak_r($content['usulan'], 1);
          

            // Load Indexx
            $data['title_menu']     = 'HRIS - Kadidat Baru';

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);

        } else {
            showErrorAkses();
        }
    }

    public function get_data_usulan()
    {
        $m_conf     = new \Model\Storage\Conf();
        $sql        = " select hukb.*, k.nama from hris_usulan_karyawan_baru hukb 
						INNER JOIN (
                            SELECT * FROM karyawan
                                WHERE id IN (
                                    SELECT MAX(id)
                                    FROM karyawan
                                    GROUP BY nik
                                )
                            ) k ON hukb.nama_pengusul = k.nik
						where hukb.status = 6 ";
        $result     = $m_conf->hydrateRaw( $sql )->toArray();

        return $result;
    }


    public function save()
    {

        $params = $_POST;
        // cetak_r($params, 1);
        
        try {

            foreach ($params['detail'] as $v_det) {
                $m_db = new \Model\Storage\HrisDataKaryawan_model();
                $m_db->nama             = $v_det['nama_karyawan'];
                $m_db->status_karyawan  = $v_det['status_karyawan'];
                $m_db->is_active        = 'ACTIVE';
                $m_db->usulan_id        = $v_det['usulan'];
                $m_db->save();
            }

            // $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            // Modules::run( 'base/event/save', $m_mm, $deskripsi_log, null, $no_mm );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
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

        $content['list']    =  $this->get_data_form();
       
        // cetak_r($content, 1);

        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }


    public function get_data_form(){
        
        $m_conf     = new \Model\Storage\Conf();
        $sql        = " select hdk.id as id_data_karyawan, hdk.nama, hsk.nama_status , hsk.kategori, hdk.is_active, k.nama as nama_pengusul 
                        from hris_data_karyawan hdk
                        inner join hris_status_karyawan hsk on hdk.status_karyawan = hsk.id 
                        inner join hris_usulan_karyawan_baru hukb on hdk.usulan_id = hukb.id
                        INNER JOIN (
                            SELECT *
                            FROM karyawan
                            WHERE id IN (
                                SELECT MAX(id)
                                FROM karyawan
                                GROUP BY nik
                            )
                        ) k ON hukb.nama_pengusul = k.nik
                        order by hdk.id desc ";
        $result     = $m_conf->hydrateRaw( $sql )->toArray();

        return $result;
    }

       public function get_status_karyawan(){
        
        $m_conf     = new \Model\Storage\Conf();
        $sql        = " select * from hris_status_karyawan  ";
        $result     = $m_conf->hydrateRaw( $sql )->toArray();

        return $result;
    }

   

   

}