<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PengirimanPenerimaanOvk extends Public_Controller
{
    private $url;
    private $hakAkses;
    function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;
        $this->hakAkses = hakAkses($this->url);
    }

    public function index()
    {
        if ( $this->hakAkses['a_view'] == 1 ) {
            $this->add_external_js(array(
                'assets/select2/js/select2.min.js',
                'assets/transaksi/pengiriman_penerimaan_ovk/js/pengiriman-penerimaan-ovk.js'
            ));
            $this->add_external_css(array(
                'assets/select2/css/select2.min.css',
                'assets/transaksi/pengiriman_penerimaan_ovk/css/pengiriman-penerimaan-ovk.css'
            ));

            // echo "<pre>";
            // print_r(123);
            // die;

            $data                           = $this->includes;
            $data['title_menu']             = 'Pengiriman & Penerimaan_ovk';

            $content['akses']               = $this->hakAkses;
            $content['unit']                = $this->get_unit();

            $ovk_content['order_pakan']     = null;
            $ovk_content['gudang_asal']     = $this->get_gudang_asal();
            $ovk_content['gudang_tujuan']   = $this->get_gudang_tujuan();
            $ovk_content['peternak']        = null;
            // $ovk_content['pakan']           = $this->get_pakan();
            $ovk_content['unit']            = $this->get_unit();
            $ovk_content['ekspedisi']       = $this->get_ekspedisi();
            $ovk_content['voadip']          = $this->get_voadip();
            $content['add_form_ovk']        = $this->load->view('transaksi/pengiriman_penerimaan_ovk/v_add_form_ovk', $ovk_content, TRUE);

            $data['view']       = $this->load->view('transaksi/pengiriman_penerimaan_ovk/v_index', $content, true);

            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
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

                        $key = $nama.' - '.$kode;

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

                                $key = $nama.' - '.$kode;
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

                        $key = $nama.' - '.$kode;
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

                    $key = $nama.' - '.$kode;
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



    public function get_lists()
    {
        
        $params                 = $this->input->post('params');
    
        $content['data_ovk']    = $this->listDataOvk($params );
        $html = $this->load->view('transaksi/pengiriman_penerimaan_ovk/v_list_ovk', $content, true);
        


        $this->result['status'] = 1;
        $this->result['content'] = $html;

        display_json($this->result);
    }

    
    public function listDataOvk($params)
    {
        
        $data = null;

        $kode_unit = $params['kode_unit'];

        $m_conf = new \Model\Storage\Conf();
        $sql_asal_tujuan = "
            (
                select cast(plg1.nomor as varchar(15)) as kode, plg1.nama, null as unit from pelanggan plg1
                right join
                    (select max(id) as id, nomor from pelanggan where tipe = 'supplier' and jenis <> 'ekspedisi' group by nomor) plg2
                    on
                        plg1.id = plg2.id

                union all

                select 
                    cast(gdg.id as varchar(15)) as kode, 
                    gdg.nama,
                    w.kode as unit
                from gudang gdg
                left join
                    wilayah w
                    on
                        gdg.unit = w.id

                union all

                select
                    cast(rs.noreg as varchar(15)) as kode,
                    mtr.nama,
                    w.kode as unit
                from rdim_submit rs
                left join
                    kandang k
                    on
                        rs.kandang = k.id
                left join
                    wilayah w
                    on
                        k.unit = w.id
                left join
                    (
                        select mm1.* from mitra_mapping mm1
                        right join
                            (select max(id) as id, nim from mitra_mapping group by nim) mm2
                            on
                                mm1.id = mm2.id
                    ) mm
                    on
                        rs.nim = mm.nim
                left join
                    mitra mtr
                    on
                        mtr.id = mm.mitra
            )
        ";
        $sql = "
            select 
                kv.id,
                kv.no_order,
                kv.tgl_kirim,
                asal.nama as asal,
                tujuan.nama as tujuan,
                kv.no_polisi as nopol,
                tv.tgl_terima
            from kirim_voadip kv
            left join
                terima_voadip tv
                on
                    kv.id = tv.id_kirim_voadip
            left join
                ".$sql_asal_tujuan." asal
                on
                    kv.asal = asal.kode
            left join
                ".$sql_asal_tujuan." tujuan
                on
                    kv.tujuan = tujuan.kode
            where
                kv.tgl_kirim between '".$params['start_date']."' and '".$params['end_date']."' ";

            if (strtolower($kode_unit) != 'all') {
                $sql .= " and ((asal.unit = '".$kode_unit."') or (tujuan.unit = '".$kode_unit."')) ";
            }

            $sql .= "group by
                kv.id,
                kv.no_order,
                kv.tgl_kirim,
                asal.nama,
                tujuan.nama,
                kv.no_polisi,
                tv.tgl_terima
            order by
                kv.tgl_kirim desc,
                kv.id desc
        ";
        $d_kirim_voadip = $m_conf->hydrateRaw( $sql );

        if ( $d_kirim_voadip->count() > 0 ) {
            $data = $d_kirim_voadip->toArray();
        }
        // echo "<pre>";
        // print_r($sql);
        // die;


        return $data;
    }


    public function get_gudang_asal()
    {
        $unit = $this->get_unit();

        $data = null;
        foreach ($unit as $k_unit => $v_unit) {
            $m_wilayah = new \Model\Storage\Wilayah_model();
            $d_wilayah = $m_wilayah->select('id')->where('kode', $v_unit['kode'])->get();

            if ( $d_wilayah->count() > 0 ) {
                $d_wilayah = $d_wilayah->toArray();                

                $m_gudang = new \Model\Storage\Gudang_model();
                $d_gudang = $m_gudang->where('jenis', 'PAKAN')->whereIn('unit', $d_wilayah)->orderBy('nama', 'asc')->get();

                if ( $d_gudang->count() > 0 ) {
                    $d_gudang = $d_gudang->toArray();

                    foreach ($d_gudang as $k_gdg => $v_gdg) {
                        $key = $v_gdg['nama'].'-'.$v_gdg['id'];

                        $data[ $key ] = $v_gdg;
                    }
                }
            }
        }

        return $data;
    }

    public function get_gudang_tujuan()
    {
        $m_gudang = new \Model\Storage\Gudang_model();
        $d_gudang = $m_gudang->where('jenis', 'PAKAN')->orderBy('nama', 'asc')->get();

        if ( $d_gudang->count() > 0 ) {
            $d_gudang = $d_gudang->toArray();
        }

        return $d_gudang;
    }


    public function get_ekspedisi()
    {
        $data = null;

        $m_ekspedisi = new \Model\Storage\Ekspedisi_model();
        $sql = "
            select 
                eks.id,
                eks.nomor,
                eks.nama
            from ekspedisi eks 
            right join 
                (select max(id) as id, nomor from ekspedisi group by nomor) as e 
                on
                    eks.id = e.id
            where
                eks.mstatus = 1 
            group by
                eks.id,
                eks.nomor,
                eks.nama
            order by eks.nama asc
        ";
        $d_ekspedisi = $m_ekspedisi->hydrateRaw( $sql );
        if ( $d_ekspedisi->count() > 0 ) {
            $data = $d_ekspedisi->toArray();
        }

        return $data;
    }

    public function get_op_not_kirim_ovk()
    {
        // echo "<pre>";
        // print_r('ovk');
        // die;
        $params = $this->input->post('params');

        $unit = $params['unit'];
        $tgl_kirim = $params['tgl_kirim'];

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                ov.no_order,
                ov.supplier,
                ov.tanggal,
                supl.nomor as supl_nomor,
                supl.nama as supl_nama,
                ovd.perusahaan as kode_prs,
                prs.perusahaan as nama_prs
            from
                (
                    select ov1.* from order_voadip ov1
                    right join
                        (select max(id) as id, no_order from order_voadip group by no_order) ov2
                        on
                            ov1.id = ov2.id
                ) ov
            left join
                (select id_order, perusahaan from order_voadip_detail group by id_order, perusahaan) ovd
                on
                    ov.id = ovd.id_order
            left join
                (
                    select plg1.* from pelanggan plg1
                    right join
                        (select max(id) as id, nomor from pelanggan group by nomor) plg2
                        on
                            plg1.id = plg2.id
                ) supl
                on
                    ov.supplier = supl.nomor
            left join
                (
                    select prs1.* from perusahaan prs1
                    right join
                        (select kode, max(id) as id from perusahaan group by kode) prs2
                        on
                            prs1.id = prs2.id
                ) prs
                on
                    ovd.perusahaan = prs.kode
            where
                -- ov.tanggal between '".$tgl_kirim."' and '".$tgl_kirim."' and
                not exists (select * from kirim_voadip where no_order = ov.no_order) 
                -- and SUBSTRING(ov.no_order, 5, 3) = '".$unit."'
            group by
                ov.no_order,
                ov.supplier,
                ov.tanggal,
                supl.nomor,
                supl.nama,
                ovd.perusahaan,
                prs.perusahaan
            order by
                ov.no_order asc
        ";
        $d_conf = $m_conf->hydrateRaw( $sql );

        //    echo "<pre>";
        // print_r($sql);
        // die;

        $data = array();
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

     
 

        $this->result['content'] = $data;

        display_json( $this->result );
    }


    public function save_ovk()
    {
        $params = $this->input->post('params');

        // echo "<pre>";
        // print_r($params);
        // die;

      
        try {
            $m_kirim_voadip = new \Model\Storage\KirimVoadip_model();
            $now = $m_kirim_voadip->getDate();

            $no_order = null;
            $no_sj = null;
            $kode_unit = null;
            if ( $params['jenis_kirim'] == 'opks' ) {
                $no_order = $params['no_order'];
                $no_sj = $params['no_sj'];
                $kode_unit = 1;
            } else {
                if ( $params['jenis_tujuan'] == 'peternak' ) {
                    $m_rs = new \Model\Storage\RdimSubmit_model();
                    $d_rs = $m_rs->where('noreg', $params['tujuan'])->with(['dKandang'])->first();

                    if ( $d_rs ) {
                        $d_rs = $d_rs->toArray();
                        $kode_unit = strtoupper($d_rs['d_kandang']['d_unit']['kode']);
                    }
                } else {
                    $m_gdg = new \Model\Storage\Gudang_model();
                    $d_gdg = $m_gdg->where('id', $params['tujuan'])->with(['dUnit'])->first();

                    if ( $d_gdg ) {
                        $d_gdg = $d_gdg->toArray();
                        $kode_unit = strtoupper($d_gdg['d_unit']['kode']);
                    }
                }

                $no_order = $m_kirim_voadip->getNextIdOrder('OP/'.$kode_unit);
                $no_sj = $m_kirim_voadip->getNextIdSj('SJ/'.$kode_unit);
            }

            if ( !empty($kode_unit) ) {

                // Pengiriman OVK
                $m_kirim_voadip->tgl_trans      = $now['waktu'];
                $m_kirim_voadip->tgl_kirim      = $params['tgl_kirim'];
                $m_kirim_voadip->no_order       = $no_order;
                $m_kirim_voadip->jenis_kirim    = $params['jenis_kirim'];
                $m_kirim_voadip->asal           = $params['asal'];
                $m_kirim_voadip->jenis_tujuan   = $params['jenis_tujuan'];
                $m_kirim_voadip->tujuan         = $params['tujuan'];
                $m_kirim_voadip->ekspedisi      = $params['ekspedisi'];
                $m_kirim_voadip->no_polisi      = $params['nopol'];
                $m_kirim_voadip->sopir          = $params['sopir'];
                $m_kirim_voadip->no_sj          = $no_sj;
                $m_kirim_voadip->ongkos_angkut  = $params['ongkos_angkut'];
                $m_kirim_voadip->save();

                $id_header = $m_kirim_voadip->id;

                foreach ($params['detail'] as $k_detail => $v_detail) {
                    $m_kirim_apakn_detail               = new \Model\Storage\KirimVoadipDetail_model();
                    $m_kirim_apakn_detail->id_header    = $id_header;
                    $m_kirim_apakn_detail->item         = $v_detail['barang'];
                    $m_kirim_apakn_detail->jumlah       = $v_detail['jumlah'];
                    $m_kirim_apakn_detail->kondisi      = $v_detail['kondisi'];
                    $m_kirim_apakn_detail->save();
                }

                $d_kirim_voadip = $m_kirim_voadip->where('id', $id_header)->with(['detail'])->first();
                // End Pengiriman OVK

                // Penerimaan OVK

                $path_name = null;

                // $m_kv = new \Model\Storage\KirimVoadip_model();
                $d_kv = $m_kirim_voadip->where('id', $id_header)->first();

                $no_bbm = null;
                if ( $d_kv->jenis_kirim == 'opks' ) {
                    $no_bbm = 'BBM/OVK/S'.str_replace('OVO', '', $d_kv->no_order);
                } else if ( $d_kv->jenis_kirim == 'opkg' ) {
                    $no_bbm = 'BBM/OVK/G'.str_replace('OP', '', $d_kv->no_order);
                } else if ( $d_kv->jenis_kirim == 'opkp' ) {
                    $no_bbm = 'BBM/OVK/P'.str_replace('OP', '', $d_kv->no_order);
                }

                $m_terima_voadip = new \Model\Storage\TerimaVoadip_model();
                $now = $m_terima_voadip->getDate();

                $m_terima_voadip->id_kirim_voadip   = $id_header;
                $m_terima_voadip->tgl_trans         = $now['waktu'];
                $m_terima_voadip->tgl_terima        = $params['tgl_terima'];
                $m_terima_voadip->path              = $path_name;
                $m_terima_voadip->no_bbm            = $no_bbm;
             
                $m_terima_voadip->save();

                $id_terima = $m_terima_voadip->id;

                foreach ($params['detail'] as $k_detail => $v_detail) {
                    $m_terima_voadip_detail             = new \Model\Storage\TerimaVoadipDetail_model();
                    $m_terima_voadip_detail->id_header  = $id_terima;
                    $m_terima_voadip_detail->item       = $v_detail['barang'];
                    $m_terima_voadip_detail->jumlah     = $v_detail['jumlah'];
                    $m_terima_voadip_detail->kondisi    = $v_detail['kondisi'];
                    $m_terima_voadip_detail->save();
                }

                $d_terima_voadip = $m_terima_voadip->where('id', $id_terima)->with(['detail'])->first();

                
                // End Penerimaan OVK

                // LOG
                $deskripsi_log_kirim_voadip = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $d_kirim_voadip, $deskripsi_log_kirim_voadip);

                $deskripsi_log_terima_voadip = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $d_terima_voadip, $deskripsi_log_terima_voadip);
                // END LOG

                $this->result['status'] = 1;
                $this->result['message'] = 'Data Pengiriman Voadip berhasil di simpan.';
            } else {
                $this->result['message'] = 'Kode unit masih kosong, harap lengkapi kode unit terlebih dahulu.';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = "Gagal : " . $e->getMessage();
        }

        display_json($this->result);
    }

    public function get_list_table()
    {
        $jenis_pengiriman = $this->input->post('jenis_pengiriman');
        $no_order = $this->input->post('no_order');

        try {
            $data = null;
            if ( $jenis_pengiriman == 'opks' ) {
                $m_op = new \Model\Storage\OrderPakan_model();
                $d_op = $m_op->where('no_order', $no_order)->first();

                $m_opd = new \Model\Storage\OrderPakanDetail_model();
                $d_opd = $m_opd->where('id_header', $d_op->id)->get();

                if ( $d_opd->count() > 0 ) {
                    $data = $d_opd->toArray();
                }
            }
            $content['jenis_pengiriman'] = $jenis_pengiriman;
            // $content['pakan'] = $this->get_pakan();
            $content['data'] = $data;
            $html = $this->load->view('transaksi/pengiriman_penerimaan_ovk/v_list_order_ovk', $content, TRUE);
            
            $this->result['status'] = 1;
            $this->result['content'] = $html;
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = "Gagal : " . $e->getMessage();
        }

        display_json($this->result);
    }

    

    public function get_peternak()
    {
        $params = $this->input->post('params');

        $timestamp = strtotime(substr($params, 0, 10));
        $first_date_of_month = date('Y-m-01', $timestamp);
        $last_date_of_month  = date('Y-m-t', $timestamp); // A leap year!

        $_data = array();

        $m_rs = new \Model\Storage\RdimSubmit_model();
        // $d_rs = $m_rs->where('tgl_docin', '>=', $date)->get()->toArray();
        $d_rs = $m_rs->select('nim', 'kandang', 'noreg', 'tgl_docin')->distinct('nim', 'kandang', 'noreg', 'tgl_docin')->whereBetween('tgl_docin', [$first_date_of_month, $last_date_of_month])->with(['mitra', 'kandang'])->get();

        if ( $d_rs->count() > 0 ) {
            $d_rs = $d_rs->toArray();
            foreach ($d_rs as $k_rs => $v_rs) {
                $m_od = new \Model\Storage\OrderDoc_model();
                $d_od = $m_od->where('noreg', $v_rs['noreg'])->orderBy('id', 'desc')->first();

                $tgl_terima = $v_rs['tgl_docin'];
                if ( $d_od ) {
                    $m_td = new \Model\Storage\TerimaDoc_model();
                    $d_td = $m_td->where('no_order', $d_od->no_order)->orderBy('id', 'desc')->first();

                    if ( $d_td ) {
                        $tgl_terima = $d_td->datang;
                    }
                }

                $rt = !empty($v_rs['mitra']['d_mitra']['alamat_rt']) ? ' ,RT.'.$v_rs['mitra']['d_mitra']['alamat_rt'] : null;
                $rw = !empty($v_rs['mitra']['d_mitra']['alamat_rw']) ? '/RW.'.$v_rs['mitra']['d_mitra']['alamat_rw'] : null;
                $kelurahan = !empty($v_rs['mitra']['d_mitra']['alamat_kelurahan']) ? ' ,'.$v_rs['mitra']['d_mitra']['alamat_kelurahan'] : null;
                $kecamatan = !empty($v_rs['mitra']['d_mitra']['d_kecamatan']) ? ' ,'.$v_rs['mitra']['d_mitra']['d_kecamatan']['nama'] : null;

                $alamat = $v_rs['mitra']['d_mitra']['alamat_jalan'] . $rt . $rw . $kelurahan . $kecamatan;

                $key = $v_rs['kandang']['d_unit']['kode'].'-'.$tgl_terima.' - '.$v_rs['mitra']['d_mitra']['nama'].' - '.$v_rs['noreg'];
                $_data[ $key ] = array(
                    'tgl_terima' => strtoupper(tglIndonesia($tgl_terima, '-', ' ')),
                    'noreg' => $v_rs['noreg'],
                    'kode_unit' => $v_rs['kandang']['d_unit']['kode'],
                    'nomor' => $v_rs['mitra']['d_mitra']['nomor'],
                    'nama' => $v_rs['mitra']['d_mitra']['nama'],
                    'alamat' => strtoupper($alamat)
                );
            }
        }

        $data = array();
        if ( !empty($_data) ) {
            ksort($_data);
            foreach ($_data as $k_data => $v_data) {
                $data[] = $v_data;
            }
        }

        $this->result['status'] = !empty($data) ? 1 : 0;
        $this->result['content'] = $data;

        display_json( $this->result );
    }


    public function cekStokOvk()
    {
        $params = $this->input->post('params');

        try {
            // cetak_r( $params, 1 );

            $id = (isset($params['id']) && !empty($params['id'])) ? $params['id']: null;
            $no_order = (isset($params['no_order']) && !empty($params['no_order'])) ? $params['no_order'] : null;
            $tgl_kirim = $params['tgl_kirim'];
            $jenis_kirim = $params['jenis_kirim'];
            $asal = $params['asal'];
            $detail = $params['detail'];

            $status = 1;
            $message = '';
            if ( $jenis_kirim == 'opkg' ) {
                foreach ($detail as $k_det => $v_det) {
                    $kode_brg = $v_det['barang'];

                    $m_conf = new \Model\Storage\Conf();
                    $sql = "
                        select * from
                        (
                            select brg1.* from barang brg1
                            right join
                                (select max(id) as id, kode from barang group by kode) brg2
                                on
                                    brg1.id = brg2.id
                        ) brg
                        where
                            brg.kode = '".$kode_brg."'
                    ";
                    $d_brg = $m_conf->hydrateRaw( $sql );

                    $nama_brg = '';
                    if ( $d_brg->count() > 0 ) {
                        $nama_brg = $d_brg->toArray()[0]['nama'];
                    }

                    $m_conf = new \Model\Storage\Conf();
                    $sql = "
                        select
                            ds.kode_gudang,
                            ds.kode_barang,
                            sum(isnull(ds.jml_stok, 0)) as jumlah
                        from det_stok ds
                        left join
                            stok s
                            on
                                ds.id_header = s.id
                        where
                            s.periode = '".$tgl_kirim."' and
                            ds.kode_gudang = '".$asal."' and
                            ds.kode_barang = '".$kode_brg."'
                        group by
                            ds.kode_gudang,
                            ds.kode_barang
                    ";
                    $d_conf = $m_conf->hydrateRaw( $sql );

                    $jml_stok = 0;
                    if ( $d_conf->count() > 0 ) {
                        $jml_stok = $d_conf->toArray()[0]['jumlah'];
                    }

                    $m_conf = new \Model\Storage\Conf();
                    $sql = "
                        select
                            kv.asal as kode_gudang,
                            dkv.item as kode_barang,
                            sum(isnull(dkv.jumlah, 0)) as jumlah
                        from det_kirim_voadip dkv
                        left join
                            kirim_voadip kv
                            on
                                dkv.id_header = kv.id
                        left join
                            terima_voadip tv
                            on
                                kv.id = tv.id_kirim_voadip
                        where
                            kv.tgl_kirim = '".$tgl_kirim."' and
                            kv.asal = '".$asal."' and
                            dkv.item = '".$kode_brg."' and
                            kv.id <> '".$id."' and
                            tv.id is null
                        group by
                            kv.asal,
                            dkv.item
                    ";
                    $d_conf = $m_conf->hydrateRaw( $sql );

                    $jml_kirim = 0;
                    if ( $d_conf->count() > 0 ) {
                        $jml_kirim = $d_conf->toArray()[0]['jumlah'];
                    }

                    if ( $jml_stok < ($jml_kirim+$v_det['jumlah']) ) {
                        $status = 0;
                        if ( empty($message)  ) {
                            $message = 'Data yang anda masukkan tidak sesuai !!!<br><br>';
                        }

                        $message .= '<b>'.$nama_brg.'</b><br>';
                        $message .= 'STOK : '.angkaRibuan($jml_stok).' KG<br>';
                        $message .= 'PENGIRIMAN : '.angkaRibuan($jml_kirim).' KG<br>';
                        $message .= 'JUMLAH ANDA : '.angkaRibuan($v_det['jumlah']).' KG<br><br>';
                    }
                }

                if ( $status == 0 ) {
                    $message .= 'Cek data stok dan pengiriman anda.';
                }
            } else if ( $jenis_kirim == 'opkp' ) {
                foreach ($detail as $k_det => $v_det) {
                    $m_conf = new \Model\Storage\Conf();
                    $sql = "
                        select * from
                        (
                            select brg1.* from barang brg1
                            right join
                                (select max(id) as id, kode from barang group by kode) brg2
                                on
                                    brg1.id = brg2.id
                        ) brg
                        where
                            brg.kode = '".$v_det['barang']."'
                    ";
                    $d_brg = $m_conf->hydrateRaw( $sql );

                    $nama_brg = '';
                    if ( $d_brg->count() > 0 ) {
                        $nama_brg = $d_brg->toArray()[0]['nama'];
                    }

                    $m_conf = new \Model\Storage\Conf();
                    $sql = "
                        select sum(dtv.jumlah) as jumlah from det_terima_voadip dtv
                        left join
                            terima_voadip tv
                            on
                                dtv.id_header = tv.id
                        left join
                            kirim_voadip kv
                            on
                                tv.id_kirim_voadip = kv.id
                        where
                            kv.no_sj = '".$v_det['no_sj_asal']."' and
                            dtv.item = '".$v_det['barang']."'
                    ";
                    $d_asal = $m_conf->hydrateRaw( $sql );

                    $jml_terima = 0;
                    if ( $d_asal->count() > 0 ) {
                        $jml_terima = $d_asal->toArray()[0]['jumlah'];
                    }

                    $m_conf = new \Model\Storage\Conf();
                    $sql = "
                        select sum(dkv.jumlah) as jumlah from det_kirim_voadip dkv
                        where
                            dkv.no_sj_asal = '".$v_det['no_sj_asal']."' and
                            dkv.item = '".$v_det['barang']."' and
                            dkv.id_header <> '".$id."'
                    ";
                    $d_pakai = $m_conf->hydrateRaw( $sql );

                    $jml_pakai = $v_det['jumlah'];
                    if ( $d_pakai->count() > 0 ) {
                        $jml_pakai += $d_pakai->toArray()[0]['jumlah'];
                    }

                    if ( $jml_terima < $jml_pakai ) {
                        $status = 0;
                        if ( empty($message)  ) {
                            $message = 'Data yang anda masukkan tidak sesuai !!!<br><br>';
                        }

                        $message .= '<b>'.$nama_brg.'</b><br>';
                        $message .= 'SJ ASAL : <b>'.$v_det['no_sj_asal'].'</b><br>';
                        $message .= 'TERIMA DI KANDANG : '.angkaRibuan($jml_terima).' KG<br>';
                        $message .= 'PINDAH : '.angkaRibuan($jml_pakai).' KG<br><br>';
                    }
                }

                if ( $status == 0 ) {
                    $message .= 'Cek data sj asal yang anda masukkan.';
                }
            }

            $this->result['status'] = $status;
            $this->result['message'] = $message;
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }



     public function listActivity()
    {
        $params = $this->input->get('params');



        
        $m_kirim_voadip = new \Model\Storage\KirimVoadip_model();
        $d_kirim_voadip = $m_kirim_voadip->where('id', $params['id'])->with(['logs'])->first()->toArray();

        $data_kirim_voadip = array(
            'no_order'      => $params['no_order'],
            'tgl_kirim'     => $params['tgl_kirim'],
            'asal'          => $params['asal'],
            'tujuan'        => $params['tujuan'],
            'nopol'         => $params['nopol'],
            'logs'          => $d_kirim_voadip['logs']
        );

        $m_terima_voadip = new \Model\Storage\TerimaVoadip_model();
        $d_terima_voadip = $m_terima_voadip->where('id', $params['id'])->with(['logs'])->first()->toArray();

        $data_terima_voadip = array(
            'no_sj'         => $params['no_sj'],
            'tgl_terima'    => $params['tgl_terima'],
            'asal'          => $params['asal'],
            'tujuan'        => $params['tujuan'],
            'nopol'         => $params['nopol'],
            'logs'          => $d_terima_voadip['logs']
        );

        $content['data_kirim']    = $data_kirim_voadip;
        $content['data_terima']   = $data_terima_voadip;


      
        $html = $this->load->view('transaksi/pengiriman_penerimaan_ovk/v_list_activity', $content, true);

        echo $html;
    }


    public function load_form_ovk()
    {
        $id = $this->input->get('id');
        $resubmit = $this->input->get('resubmit');

        $a_content['order_voadip'] = null;
        $a_content['gudang_asal'] = $this->get_gudang_asal();
        $a_content['gudang_tujuan'] = $this->get_gudang_tujuan();
        $a_content['peternak'] = null;
        $a_content['voadip'] = $this->get_voadip();
        $a_content['unit'] = $this->get_unit();
        // $a_content['pakan']           = $this->get_pakan();

        $html = null;
        if ( !empty($id) ) {
            $m_kv = new \Model\Storage\KirimVoadip_model();
            $d_kv = $m_kv->where('id', $id)->with(['terima', 'detail'])->first()->toArray();

            $asal = null;
            $tujuan = null;
            $tgl_docin_asal = null;
            $tgl_docin_tujuan = null;
            if ( $d_kv['jenis_kirim'] == 'opkp' ) {
                $m_rs = new \Model\Storage\RdimSubmit_model();
                $d_rs_asal = $m_rs->where('noreg', $d_kv['asal'])->with(['mitra'])->orderBy('id', 'desc')->first()->toArray();
                $tgl_docin_asal = $d_rs_asal['tgl_docin'];
                $asal = $d_rs_asal['mitra']['d_mitra']['nama'].' ('.$d_kv['asal'].')';

                if ( $d_kv['jenis_tujuan'] == 'peternak' ) {
                    $d_rs_tujuan = $m_rs->where('noreg', $d_kv['tujuan'])->with(['mitra'])->orderBy('id', 'desc')->first()->toArray();
                    $tgl_docin_tujuan = $d_rs_tujuan['tgl_docin'];
                    $tujuan = $d_rs_tujuan['mitra']['d_mitra']['nama'].' ('.$d_kv['tujuan'].')';
                }
            } else if ( $d_kv['jenis_kirim'] == 'opkg' ) {
                $m_gudang = new \Model\Storage\Gudang_model();
                $d_gudang = $m_gudang->where('id', $d_kv['asal'])->orderBy('id', 'desc')->first();

                $asal = $d_gudang->nama;

                if ( $d_kv['jenis_tujuan'] == 'peternak' ) {
                    if ( !empty($d_kv['tujuan']) ) {
                        $m_rs = new \Model\Storage\RdimSubmit_model();
                        $d_rs_tujuan = $m_rs->where('noreg', $d_kv['tujuan'])->with(['mitra'])->orderBy('id', 'desc')->first();

                        if ( $d_rs_tujuan ) {
                            $d_rs_tujuan = $d_rs_tujuan->toArray();
                            
                            $tgl_docin_tujuan = $d_rs_tujuan['tgl_docin'];
                            $tujuan = $d_rs_tujuan['mitra']['d_mitra']['nama'].' ('.$d_kv['tujuan'].')';
                        }
                    }
                } else {
                    $m_gudang = new \Model\Storage\Gudang_model();
                    $d_gudang = $m_gudang->where('id', $d_kv['tujuan'])->orderBy('id', 'desc')->first();

                    $tujuan = $d_gudang->nama;
                }
            } else {
                $m_supplier = new \Model\Storage\Supplier_model();
                $d_supplier = $m_supplier->where('nomor', $d_kv['asal'])->where('tipe', 'supplier')->where('jenis', '<>', 'ekspedisi')->orderBy('id', 'desc')->first();
                $asal = $d_supplier->nama;

                if ( $d_kv['jenis_tujuan'] == 'peternak' ) {
                    if ( !empty($d_kv['tujuan']) ) {
                        $m_rs = new \Model\Storage\RdimSubmit_model();
                        $d_rs_tujuan = $m_rs->where('noreg', $d_kv['tujuan'])->with(['mitra'])->orderBy('id', 'desc')->first();

                        if ( $d_rs_tujuan ) {
                            $d_rs_tujuan = $d_rs_tujuan->toArray();
                            
                            $tgl_docin_tujuan = $d_rs_tujuan['tgl_docin'];
                            $tujuan = $d_rs_tujuan['mitra']['d_mitra']['nama'].' ('.$d_kv['tujuan'].')';
                        }
                    }
                } else {
                    $m_gudang = new \Model\Storage\Gudang_model();
                    $d_gudang = $m_gudang->where('id', $d_kv['tujuan'])->orderBy('id', 'desc')->first();

                    $tujuan = $d_gudang->nama;
                }
            }

            // $m_ov = new \Model\Storage\OrderVoadip_model();
            // $d_ov = $m_ov->where('no_order', $d_kv['no_order'])->with(['d_supplier'])->first();

            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select
                    ov.no_order,
                    ov.supplier,
                    ov.tanggal,
                    supl.nomor as supl_nomor,
                    supl.nama as supl_nama,
                    ovd.perusahaan as kode_prs,
                    prs.perusahaan as nama_prs
                from order_voadip ov
                left join
                    (select id_order, perusahaan from order_voadip_detail group by id_order, perusahaan) ovd
                    on
                        ov.id = ovd.id_order
                left join
                    (
                        select plg1.* from pelanggan plg1
                        right join
                            (select max(id) as id, nomor from pelanggan group by nomor) plg2
                            on
                                plg1.id = plg2.id
                    ) supl
                    on
                        ov.supplier = supl.nomor
                left join
                    (
                        select prs1.* from perusahaan prs1
                        right join
                            (select kode, max(id) as id from perusahaan group by kode) prs2
                            on
                                prs1.id = prs2.id
                    ) prs
                    on
                        ovd.perusahaan = prs.kode
                where
                    ov.no_order = '".$d_kv['no_order']."'
                group by
                    ov.no_order,
                    ov.supplier,
                    ov.tanggal,
                    supl.nomor,
                    supl.nama,
                    ovd.perusahaan,
                    prs.perusahaan
                order by
                    ov.no_order asc
            ";
            $d_conf = $m_conf->hydrateRaw( $sql );

            $d_ov = null;
            if ( $d_conf->count() > 0 ) {
                $d_ov = $d_conf->toArray()[0];
            }

            $a_content['asal'] = $asal;
            $a_content['tujuan'] = $tujuan;
            $a_content['tgl_docin_asal'] = substr($tgl_docin_asal, 0, 10);
            $a_content['tgl_docin_tujuan'] = !empty($tgl_docin_tujuan) ? substr($tgl_docin_tujuan, 0, 10) : null;
            $a_content['data'] = $d_kv;
            $a_content['data_ov'] = !empty($d_ov) ? $d_ov : null;
            $a_content['terima'] = !empty($d_kv['terima']) ? 1 : 0;

            // echo "<pre>";
            // print_r($a_content['data']);
            // die;

            if ( $resubmit == 'edit' ) {
                $html = $this->load->view('transaksi/pengiriman_penerimaan_ovk/v_edit_form_ovk', $a_content, TRUE);
            } else {
                $html = $this->load->view('transaksi/pengiriman_penerimaan_ovk/v_view_form_ovk', $a_content, TRUE);
            }
        } else {
            $html = $this->load->view('transaksi/pengiriman_penerimaan_ovk/v_add_form_ovk', $a_content, TRUE);
        }

        echo $html;
    }

    public function get_voadip()
    {
        $m_brg = new \Model\Storage\Barang_model();
        $d_nomor = $m_brg->select('kode')->distinct('kode')->where('tipe', 'obat')->get()->toArray();

        $datas = array();
        if ( !empty($d_nomor) ) {
            foreach ($d_nomor as $nomor) {
                $brg = $m_brg->where('tipe', 'obat')
                                          ->where('kode', $nomor['kode'])
                                          ->orderBy('version', 'desc')
                                          ->orderBy('id', 'desc')
                                          ->first()->toArray();

                $key = $brg['nama'].' | '.$brg['kode'];
                $datas[$key] = $brg;

                ksort( $datas );
            }
        }

        return $datas;
    }


    public function edit_ovk()
    {

        $params = $this->input->post('params');
        // echo "<pre>";
        // print_r($params);
        // die;

        try {

            // Pengiriman
            $m_kirim_voadip = new \Model\Storage\KirimVoadip_model();
            $now = $m_kirim_voadip->getDate();

            $m_kirim_voadip->where('id', $params['id'])->update(
                array(
                    'tgl_trans'     => $now['waktu'],
                    'tgl_kirim'     => $params['tgl_kirim'],
                    'no_order'      => $params['no_order'],
                    'jenis_kirim'   => $params['jenis_kirim'],
                    'asal'          => $params['asal'],
                    'jenis_tujuan'  => $params['jenis_tujuan'],
                    'tujuan'        => $params['tujuan'],
                    'ekspedisi'     => $params['ekspedisi'],
                    'no_polisi'     => $params['nopol'],
                    'sopir'         => $params['sopir'],
                    'no_sj'         => $params['no_sj'],
                    'ongkos_angkut' => $params['ongkos_angkut']
                )
            );

            $id_header = $params['id'];

            $m_kirim_voadip_detail = new \Model\Storage\KirimVoadipDetail_model();
            $m_kirim_voadip_detail->where('id_header', $id_header)->delete();

            foreach ($params['detail'] as $k_detail => $v_detail) {
                $m_kirim_voadip_detail              = new \Model\Storage\KirimVoadipDetail_model();
                $m_kirim_voadip_detail->id_header   = $id_header;
                $m_kirim_voadip_detail->item        = $v_detail['barang'];
                $m_kirim_voadip_detail->jumlah      = $v_detail['jumlah'];
                $m_kirim_voadip_detail->kondisi     = $v_detail['kondisi'];
                $m_kirim_voadip_detail->save();
            }
            // End Pengiriman

            // Penerimaan

            $m_kv = new \Model\Storage\KirimVoadip_model();
            $d_kv = $m_kv->where('id', $id_header)->first();

            $path_name = null;
            $no_bbm = null;

            if ($d_kv->jenis_kirim == 'opks') {
                $no_bbm = 'BBM/OVK/S'.str_replace('OVO', '', $d_kv->no_order);
            } else if ($d_kv->jenis_kirim == 'opkg') {
                $no_bbm = 'BBM/OVK/G'.str_replace('OP', '', $d_kv->no_order);
            } else if ($d_kv->jenis_kirim == 'opkp') {
                $no_bbm = 'BBM/OVK/P'.str_replace('OP', '', $d_kv->no_order);
            }

            $m_terima_voadip = new \Model\Storage\TerimaVoadip_model();

            $d_terima_voadip = $m_terima_voadip
                ->where('id_kirim_voadip', $id_header)
                ->first();

            if (!$d_terima_voadip) {
                throw new \Exception("Data terima voadip tidak ditemukan.");
            }

            $id_terima = $d_terima_voadip->id;
            $now = $m_terima_voadip->getDate();

            $m_terima_voadip
                ->where('id_kirim_voadip', $id_header)
                ->update([
                    'id_kirim_voadip'   => $id_header,
                    'tgl_trans'         => $now['waktu'],
                    'tgl_terima'        => $params['tgl_terima'],
                    'path'              => $path_name,
                    'no_bbm'            => $no_bbm
                ]);

            $m_terima_voadip_detail = new \Model\Storage\TerimaVoadipDetail_model();
            $m_terima_voadip_detail
                ->where('id_header', $id_terima) 
                ->delete();


            foreach ($params['detail'] as $k_detail => $v_detail) {
                $m_terima_voadip_detail = new \Model\Storage\TerimaVoadipDetail_model();
                $m_terima_voadip_detail->id_header  = $id_terima; 
                $m_terima_voadip_detail->item       = $v_detail['barang'];
                $m_terima_voadip_detail->jumlah     = $v_detail['jumlah'];
                $m_terima_voadip_detail->kondisi    = $v_detail['kondisi'];
                $m_terima_voadip_detail->save();
            }
            // End Penerimaan
            
            $d_kirim_voadip = $m_kirim_voadip->where('id', $id_header)->with(['detail'])->first();
            $d_terima_voadip = $m_terima_voadip->where('id', $id_header)->with(['detail'])->first();
            
            $deskripsi_log_kirim_voadip = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_kirim_voadip, $deskripsi_log_kirim_voadip);

            $deskripsi_log_terima_voadip = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_terima_voadip, $deskripsi_log_terima_voadip);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data Pengiriman Voadip berhasil di ubah.';
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = "Gagal : " . $e->getMessage();
        }

        display_json($this->result);
        
    }


        public function delete()
        {
            $params = $this->input->post('params');

            try {

                $m_terima_voadip = new \Model\Storage\TerimaVoadip_model();
                $m_terima_voadip_detail = new \Model\Storage\TerimaVoadipDetail_model();

                $d_terima_voadip = $m_terima_voadip
                    ->where('id_kirim_voadip', $params['id'])
                    ->with(['detail'])
                    ->first();

                if ($d_terima_voadip) {

                    Modules::run(
                        'base/event/update',
                        $d_terima_voadip,
                        'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser']
                    );

                    $m_terima_voadip_detail
                        ->where('id_header', $d_terima_voadip->id)
                        ->delete();

                    $m_terima_voadip
                        ->where('id', $d_terima_voadip->id)
                        ->delete();
                }


                $m_kirim = new \Model\Storage\KirimVoadip_model();
                $m_kirim_detail = new \Model\Storage\KirimVoadipDetail_model();

                $d_kirim = $m_kirim->where('id', $params['id'])->first();

                if (!$d_kirim) {
                    throw new \Exception("Data kirim tidak ditemukan.");
                }

                Modules::run(
                    'base/event/update',
                    $d_kirim,
                    'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser']
                );

                $m_kirim_detail->where('id_header', $params['id'])->delete();
                $m_kirim->where('id', $params['id'])->delete();

                $this->result = [
                    'status' => 1,
                    'message' => 'Data Voadip (terima + kirim) berhasil dihapus.'
                ];

            } catch (\Exception $e) {

                $this->result = [
                    'status' => 0,
                    'message' => 'Gagal : ' . $e->getMessage()
                ];
            }

            display_json($this->result);
        }

    
}