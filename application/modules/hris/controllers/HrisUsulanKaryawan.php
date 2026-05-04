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
            // $content['posisi']          = $this->get_data_posisi();
            $content['unit']            = $this->get_unit();

            // cetak_r($content['posisi'], 1);

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
            $m_db->document       = $this->generate_document($params['header']['kode_dokumen']);
            $m_db->save();

            $id             = $m_db->id;
            $deskripsi_log  = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_db, $deskripsi_log, null, $id, $m_db);
            

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
            // $this->result['content'] = array('id' => $no_mm);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );

    }

    public function generate_document($posisi)
    {
  
        $tahun = date('Y');
        $bulan = date('m');

        $m_conf     = new \Model\Storage\Conf();

        $sql = "SELECT MAX(CAST(RIGHT(document, 3) AS INT)) AS last_number
		FROM hris_usulan_karyawan_baru
		WHERE document LIKE 'PROP/{$posisi}/{$tahun}{$bulan}%'";

        $d_conf     = $m_conf->hydrateRaw( $sql );
        
        $data       = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }
        
        $last = $data[0]['last_number'] ?? 0;
        $new  = str_pad($last + 1, 3, '0', STR_PAD_LEFT);
        
        $kode = "PROP/{$posisi}/$tahun$bulan$new";
        // cetak_r($kode, 1);
        // echo $kode;
        return $kode;
    }


    public function load_form(){

        $content['list'] =  $this->get_list_data();
        $content['unit'] = $this->get_unit();
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
        $sql        = " SELECT hukb.*, k.nama, k.jabatan, j.nama as nama_posisi
                        FROM hris_usulan_karyawan_baru hukb
                        INNER JOIN (
                            SELECT *
                            FROM karyawan
                            WHERE id IN (
                                SELECT MAX(id)
                                FROM karyawan
                                GROUP BY nik
                            )
                        ) k ON hukb.nama_pengusul = k.nik
                        inner join jabatan j on hukb.posisi = j.kode";

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
        $content['edit_data']       = $this->get_data_edit($_GET['id_data']);
        // cetak_r($content, 1);
        // $content['detail']          = $this->get_data_detail($_GET['id_data']);
        $content['unit']            = $this->get_unit();
        $content['title_panel']     = 'HRIS - Hris Usulan Karyawan / Edit Data';
        $data['title_menu']     = 'HRIS - Usulan Karyawan';
        

        $data['view']         = $this->load->view($this->pathView . 'v_edit_data', $content, TRUE);
        $this->load->view($this->template, $data);
    }

    public function get_data_edit($id)
    {
        $m_conf     = new \Model\Storage\Conf();

        $sql = "select * from hris_usulan_karyawan_baru where id = " . $id;

        // cetak_r($sql, 1);

        $d_conf     = $m_conf->hydrateRaw( $sql );
        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
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


        try {

            $id_data = (int) $params['id_data'];

            $m_db = new \Model\Storage\HrisUsulanKaryawan_model();
            $data = $m_db->where('id', $id_data)->first();

            // cetak_r($params, 1);


            if (!$data) {
                throw new \Exception("Data tidak ditemukan");
            }

            $data->nama_pengusul  = $params['header']['mengusulkan'];
            $data->tgl_pengusulan = $params['header']['tgl_pengusulan'];
            $data->posisi         = $params['header']['posisi'];
            $data->jumlah         = $params['header']['jumlah'];
            $data->unit           = $params['header']['unit'];
            $data->alasan         = $params['header']['alasan'];
            $data->document       = $this->generate_document($params['header']['kode_dokumen']);
            $data->save();

            $deskripsi_log = 'di-edit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/update', $data, $deskripsi_log, null, $id_data, json_encode($data));

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
           
            $id_data = (int) $params['id_data'];
            $m_db = new \Model\Storage\HrisUsulanKaryawan_model();
            $data = $m_db->where('id', $id_data)->first();

            if (!$data) {
                throw new \Exception("Data tidak ditemukan");
            }

            $id = $data->id;
            $data->delete();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/delete', $data, $deskripsi_log, null, $id, $data);

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

        $sql = " select * from hris_data_karyawan where tgl_selesai_isi is not null ";

        $d_conf     = $m_conf->hydrateRaw( $sql );
        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

    public function get_unit()
    {
        $m_duser = new \Model\Storage\DetUser_model();
        $d_duser = $m_duser->where('id_user', $this->userid)->first();

        $m_karyawan = new \Model\Storage\Karyawan_model();
        $d_karyawan = $m_karyawan->where('nama', 'like', strtolower(trim($d_duser->nama_detuser)).'%')->orderBy('id', 'desc')->first();

        $data = null;

        // $kode_unit = array();
        // $kode_unit_all = null;
        $data = null;
        if ( $d_karyawan ) {
            $m_ukaryawan = new \Model\Storage\UnitKaryawan_model();
            $d_ukaryawan = $m_ukaryawan->where('id_karyawan', $d_karyawan->id)->get();

            if ( $d_ukaryawan->count() > 0 ) {
                $d_ukaryawan = $d_ukaryawan->toArray();

                foreach ($d_ukaryawan as $k_ukaryawan => $v_ukaryawan) {
                    if ( stristr($v_ukaryawan['unit'], 'all') === false ) {
                        $m_wil = new \Model\Storage\Wilayah_model();
                        $d_wil = $m_wil->where('id', $v_ukaryawan['unit'])->first();

                        $nama = str_replace('Kab ', '', str_replace('Kota ', '', $d_wil->nama));
                        $kode = $d_wil->kode;

                        $key = $kode;

                        $data[$key] = array(
                            'nama' => $nama,
                            'kode' => $kode
                        );
                    } else {
                        $m_wil = new \Model\Storage\Wilayah_model();
                        $d_wil = $m_wil->select('nama', 'kode')->where('jenis', 'UN')->get();

                        if ( $d_wil->count() > 0 ) {
                            $d_wil = $d_wil->toArray();
                            foreach ($d_wil as $k_wil => $v_wil) {
                                $nama = str_replace('Kab ', '', str_replace('Kota ', '', $v_wil['nama']));
                                $kode = $v_wil['kode'];

                                $key = $kode;
                                $data[$key] = array(
                                    'nama' => $nama,
                                    'kode' => $kode
                                );
                            }
                        }
                    }
                }
            } else {
                $m_wil = new \Model\Storage\Wilayah_model();
                $d_wil = $m_wil->select('nama', 'kode')->where('jenis', 'UN')->get();

                if ( $d_wil->count() > 0 ) {
                    $d_wil = $d_wil->toArray();
                    foreach ($d_wil as $k_wil => $v_wil) {
                        $nama = str_replace('Kab ', '', str_replace('Kota ', '', $v_wil['nama']));
                        $kode = $v_wil['kode'];

                        $key = $kode;
                        $data[$key] = array(
                            'nama' => $nama,
                            'kode' => $kode
                        );
                    }
                }
            }
        } else {
            $m_wil = new \Model\Storage\Wilayah_model();
            $d_wil = $m_wil->select('nama', 'kode')->where('jenis', 'UN')->get();

            if ( $d_wil->count() > 0 ) {
                $d_wil = $d_wil->toArray();
                foreach ($d_wil as $k_wil => $v_wil) {
                    $nama = str_replace('Kab ', '', str_replace('Kota ', '', $v_wil['nama']));
                    $kode = $v_wil['kode'];

                    $key = $kode;
                    $data[$key] = array(
                        'nama' => $nama,
                        'kode' => $kode
                    );
                }
            }
        }

        if ( !empty($data) ) {
            ksort($data);
        }

        return $data;
    }

    public function get_jabatan()
    {
        $jabatan = $this->get_data_posisi($_POST['jabatan']);
        // cetak_r($jabatan, 1);
        echo json_encode($jabatan);
    }
    

    public function get_data_posisi($jabatan)
    {
        $m_conf     = new \Model\Storage\Conf();

        $sql        = " select * from jabatan j
                    inner join jabatan_atasan ja on j.kode = ja.kode_jabatan
                    where ja.kode_jabatan_atasan = '".$jabatan."' ";

        $d_conf     = $m_conf->hydrateRaw( $sql );
        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

}