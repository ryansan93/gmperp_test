<?php defined('BASEPATH') OR exit('No direct script access allowed');

class FormAckUsulanKaryawan extends Public_Controller {

    private $pathView = 'hris/hris_form_ack_usulan_karyawan/';
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
                "assets/hris/form_ack_usulan_karyawan/js/form_ack_usulan_karyawan.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/hris/form_ack_usulan_karyawan/css/form_ack_usulan_karyawan.css",
            ));

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Hris Ack Usulan Karyawan';
            $data['title_menu']         = 'HRIS - Hris Ack Usulan Karyawan';

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);

        } else {
            showErrorAkses();
        }
    }


    public function filter()
    {
        $content['list'] =  $this->get_list($_POST['status']); 
        $content['unit'] =  $this->get_unit();
        // cetak_r($content, 1);
        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }


    public function load_form()
    {
        $content['list'] =  $this->get_list(); 
        $content['unit'] =  $this->get_unit();
        // cetak_r($content, 1);
        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }
    


    public function get_list($id = null)
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
                        inner join jabatan j on hukb.posisi = j.kode " ;
                        
        if (!empty($id)){
            if($id == 4){
                $sql .= " where hukb.status in (4, 5)";
            } else {
                $sql .= " where hukb.status = " . $id;
            }
        }
        
        // cetak_r($sql, 1);


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


    
    // public function show_detail()
    // {
    //     $content['data_kandidat']   = $this->get_list_detail($_POST['id']);

    //     $id_kandidat = array_column($content['data_kandidat'], 'id_kandidat');

    //     $hasil = implode(',', $id_kandidat);

        
    //     $content['biodata']         = $this->get_bio_data($hasil);
    //     // cetak_r($content['biodata'] , 1);

    //     echo $this->load->view($this->pathView . 'v_detail', $content, TRUE);
    // }

    // public function get_bio_data($id_kandidat)
    // {
    //     $m_conf     = new \Model\Storage\Conf();
    //     $sql        = " select * from hris_data_karyawan_detail where id_data_karyawan in  ($id_kandidat)";

    //     $d_conf     = $m_conf->hydrateRaw( $sql );
    //     // cetak_r($sql, 1);
    //     $data       = null;

    //     if ( $d_conf->count() > 0 ) {
    //         $data = $d_conf->toArray();
    //     }

    //     $result = [];

    //     foreach ($data as $row) {
    //         $id = $row['id_data_karyawan'];

    //         if (!isset($result[$id])) {
    //             $result[$id] = [
    //                 'standalone' => [],
    //                 'grouped' => []
    //             ];
    //         }

    //         if (!empty($row['parent_column'])) {
    //             $result[$id]['grouped'][$row['parent_column']][] = $row;
    //         } else {
    //             $result[$id]['standalone'][$row['label']] = $row;
    //         }
    //     }

    //     return $result;

    // }

    // public function get_list_detail($id)
    // {
    //     $m_conf     = new \Model\Storage\Conf();
    //     $sql        = " select * from hris_usulan_karyawan_baru_detail hukbd 
    //                 inner join hris_data_karyawan hdk on hukbd.id_kandidat = hdk.id 
    //                 where hukbd.id_header = " . $id;

    //     $d_conf     = $m_conf->hydrateRaw( $sql );
    //     // cetak_r(123, 1);
    //     $data       = null;

    //     if ( $d_conf->count() > 0 ) {
    //         $data = $d_conf->toArray();
    //     }

    //     return $data;
        
    // } 


    public function update()
    {
        $params = $_POST;

        //  cetak_r($params, 1);

        try {
            $id_data = (int) $params['id_data'];

            $m_db     = new \Model\Storage\HrisUsulanKaryawan_model();

            $d_form = $m_db->where('id', $id_data)->first();

            // cetak_r($d_form, 1);

            if (!$d_form) {
                throw new \Exception("Data form tidak ditemukan.");
            }

            $update = [
                'status' => $params['keputusan'],
            ];

            $keterangan = !empty($params['keterangan']) ? $params['keterangan'] : '';

            if ($params['keputusan'] == 5) {
                $update['keterangan_ceo'] = $keterangan;
            } else {
                $update['keterangan_hrd'] = $keterangan;
            }

            $m_db->where('id', $id_data)->update($update);

          
            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di update.';

        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }


    public function printPreview() {        
  
        $key = "secretkey";

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            show_error('ID tidak ditemukan', 400);
        }

        $kode = $_GET['id'];
        $kode = str_replace(' ', '+', $kode);

        $decrypted = openssl_decrypt($kode, "AES-128-ECB", $key);

        
        $content['data'] = $this->get_data_print($decrypted)[0];
        // cetak_r($content, 1);
        $content['unit'] =  $this->get_unit();


        $res_view_html = $this->load->view($this->pathView.'v_export_pdf', $content, true);

        echo $res_view_html;
    }

    public function get_data_print($id)
    {
        $m_conf     = new \Model\Storage\Conf();
        $sql        = " select k.nama as nama_karyawan_pengusul, hukb.status as status_usulan, j.nama as nama_jabatan , * from hris_usulan_karyawan_baru  hukb
                        INNER JOIN (
                                SELECT *
                                FROM karyawan
                                WHERE id IN (
                                    SELECT MAX(id)
                                    FROM karyawan
                                    GROUP BY nik
                                )
                            ) k ON hukb.nama_pengusul = k.nik
                        inner join jabatan j on hukb.posisi = j.kode
                        where hukb.id = " . $id;

        $d_conf     = $m_conf->hydrateRaw( $sql );
        // cetak_r($id, 1);
        $data       = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
        
    } 

    public function exportPdf()
    {
        $params = $this->input->post('params');

        try {
            $_no_km = $params['kode'];
            
            $kode = exDecrypt( $_no_km );
            // $kode = 'FP2312060006';

            $m_km = new \Model\Storage\Km_model();
            $d_km = $m_km->getKmCetak( $kode );

            $struktur = "";
            $text = "";
            foreach ($d_km as $k_km => $v_km) {
                $idx = 1;
                foreach ($v_km as $key => $value) {
                    $struktur .= '"'.$key.'"';
                    $text .= '"'.$value.'"';
                    if ( $idx < count($v_km) ) {
                        $struktur .= ',';
                        $text .= ',';
                    }

                    $idx++;
                }

                $text .= "\n";
            }

            $content = $struktur."\n".$text;
            $fp = fopen("cetak/ckmcet.TXT","wb");
            fwrite($fp,$content);
            fclose($fp);

            system("cmd /c C:/xampp_php7/htdocs/sistem_udlancar/copy_file.bat");

            $this->result['status'] = 1;
            // $this->result['content'] = array('url' => $path);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

}