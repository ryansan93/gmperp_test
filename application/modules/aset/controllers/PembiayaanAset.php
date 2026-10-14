<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PembiayaanAset extends Public_Controller
{
    private $pathView = 'aset/pembiayaan_aset/';
    private $url;
    private $hakAkses;

    public function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;
        $this->hakAkses = hakAkses($this->url);
    }

    public function index()
    {
        if (empty($this->hakAkses['a_view'])) {
            showErrorAkses();
            return;
        }

        $this->add_external_js([
            'assets/select2/js/select2.min.js',
            'assets/aset/pembiayaan_aset/js/pembiayaan_aset.js',
        ]);
        $this->add_external_css([
            'assets/select2/css/select2.min.css',
             'assets/aset/pembiayaan_aset/css/pembiayaan_aset.css',
        ]);

        $data                   = $this->includes;
        $content['akses']       = $this->hakAkses;
        $content['kategori_aset'] = \Model\Storage\MsAsetKategori_model::orderBy('id', 'asc')
            ->get()
            ->toArray();
        $content['title_panel'] = 'Pembiayaan Aset';
        $data['title_menu']     = 'Pembiayaan Aset';
        $data['view']           = $this->load->view($this->pathView . 'v_index', $content, true);
        $this->load->view($this->template, $data);
    }

    // public function list_data()
    // {
    //     $kategori = trim((string) $this->input->post('id_kategori'));
    //     $search = trim((string) $this->input->post('search'));

    //     $query = (new \Model\Storage\PembiayaanAset_model())
    //         ->select(
    //             'pembiayaan_aset.*',
    //             'ms_aset.deskripsi_aset',
    //             'ms_aset_kategori.kategori_name',
    //             'ms_aset.unit_pengguna',
    //             'ms_aset.nilai_perolehan',
    //             'ms_aset.kode_pembiayaan as kode_jenis_pembiayaan',
    //             'ms_pembiayaan.nama_pembiayaan',
    //             'supplier.nomor as supplier_nomor',
    //             'supplier.nama as supplier_nama',
    //             'leasing.nomor as leasing_nomor',
    //             'leasing.nama as leasing_nama'
    //         )
    //         ->leftJoin('ms_aset', 'ms_aset.kode_aset', '=', 'pembiayaan_aset.kode_aset')
    //         ->leftJoin('ms_aset_kategori', 'ms_aset_kategori.id', '=', 'ms_aset.id_kategori')
    //         ->leftJoin('ms_pembiayaan', 'ms_pembiayaan.kode_pembiayaan', '=', 'ms_aset.kode_pembiayaan')
    //         ->leftJoin('pelanggan as supplier', 'supplier.id', '=', 'pembiayaan_aset.id_supplier')
    //         ->leftJoin('pelanggan as leasing', 'leasing.id', '=', 'pembiayaan_aset.id_leasing');

    //     if ($kategori !== '') {
    //         $query->where('ms_aset_kategori.kategori_name', $kategori);
    //     }

    //     if ($search !== '') {
    //         $like = '%' . $search . '%';
    //         $query->where(function ($subquery) use ($like) {
    //             $subquery->where('pembiayaan_aset.kode_pembiayaan', 'like', $like)
    //                 ->orWhere('ms_aset.kode_aset', 'like', $like)
    //                 ->orWhere('ms_aset.deskripsi_aset', 'like', $like)
    //                 ->orWhere('ms_aset_kategori.kategori_name', 'like', $like)
    //                 ->orWhere('supplier.nomor', 'like', $like)
    //                 ->orWhere('supplier.nama', 'like', $like)
    //                 ->orWhere('leasing.nomor', 'like', $like)
    //                 ->orWhere('leasing.nama', 'like', $like);
    //         });
    //     }

    //     $list = $query->orderBy('pembiayaan_aset.id', 'desc')
    //         ->get()
    //         ->toArray();

    //     $content['list'] = $list;
    //     $content['akses'] = $this->hakAkses;
    //     echo $this->load->view($this->pathView . 'v_list', $content, true);
    // }

    public function list_data()
    {
        $kategori = trim((string) $this->input->post('id_kategori'));
        $search = trim((string) $this->input->post('search'));

        $query = (new \Model\Storage\PembiayaanAset_model())
            ->select(
                'pembiayaan_aset.*',
                'ms_aset.deskripsi_aset',
                'ms_aset_kategori.kategori_name',
                'ms_aset.unit_pengguna',
                'ms_aset.nilai_perolehan',
                'ms_aset.kode_pembiayaan as kode_jenis_pembiayaan',
                'ms_pembiayaan.nama_pembiayaan',
                'supplier.nomor as supplier_nomor',
                'supplier.nama as supplier_nama',
                'leasing.nomor as leasing_nomor',
                'leasing.nama as leasing_nama'
            )
            ->leftJoin('ms_aset', 'ms_aset.kode_aset', '=', 'pembiayaan_aset.kode_aset')
            ->leftJoin('ms_aset_kategori', 'ms_aset_kategori.id', '=', 'ms_aset.id_kategori')
            ->leftJoin('ms_pembiayaan', 'ms_pembiayaan.kode_jenis_pembiayaan', '=', 'ms_aset.kode_pembiayaan')
            ->leftJoin('pelanggan as supplier', 'supplier.id', '=', 'pembiayaan_aset.id_supplier')
            ->leftJoin('pelanggan as leasing', 'leasing.id', '=', 'pembiayaan_aset.id_leasing');

        if ($kategori !== '') {
            $query->where('ms_aset_kategori.kategori_name', $kategori);
        }

        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(function ($subquery) use ($like) {
                $subquery->where('pembiayaan_aset.kode_pembiayaan', 'like', $like)
                    ->orWhere('ms_aset.kode_aset', 'like', $like)
                    ->orWhere('ms_aset.deskripsi_aset', 'like', $like)
                    ->orWhere('ms_aset_kategori.kategori_name', 'like', $like)
                    ->orWhere('supplier.nomor', 'like', $like)
                    ->orWhere('supplier.nama', 'like', $like)
                    ->orWhere('leasing.nomor', 'like', $like)
                    ->orWhere('leasing.nama', 'like', $like);
            });
        }

        $list = $query->orderBy('pembiayaan_aset.id', 'desc')
            ->get()
            ->toArray();


        if (!empty($list)) {
            $connection = $this->getConnection();
            foreach ($list as &$row) {
                $kode = $row['kode_pembiayaan'];
                
                $sudahDiproses = $connection->table('pembiayaan_tdp_aset')->where('kode_pembiayaan', $kode)->exists()
                    || $connection->table('pembiayaan_dp_aset')->where('kode_pembiayaan', $kode)->exists()
                    || $connection->table('pembiayaan_cicilan_aset')->where('kode_pembiayaan', $kode)->exists()
                    || $connection->table('pembiayaan_pelunasan_aset')->where('kode_pembiayaan', $kode)->exists()
                    || $connection->table('pembiayaan_tempo_rencana_aset')->where('kode_pembiayaan', $kode)->exists();
                
                $row['sudah_diproses'] = $sudahDiproses ? 1 : 0;
            }
            unset($row); 
        }
   

        // cetak_r($list,1);

        $content['list'] = $list;
        $content['akses'] = $this->hakAkses;
        echo $this->load->view($this->pathView . 'v_list', $content, true);
    }

    public function add_form()
    {
        $data = [
            'data' => null,
            'aset' => $this->getAsetOptions(),
            'supplier' => $this->getSupplierOptions(),
        ];
        $this->load->view($this->pathView . 'v_form', $data);
    }

    public function edit_form()
    {
        $id = $this->input->get('id');
        $model = \Model\Storage\PembiayaanAset_model::find($id);
        if (!$model) {
            show_404();
            return;
        }

        $data = [
            'data' => $model->toArray(),
            'aset' => $this->getAsetOptions($model->id),
            'supplier' => $this->getSupplierOptions(),
        ];
        $this->load->view($this->pathView . 'v_form', $data);
    }

    // public function ProsesLeasing()
    // {
    //     if (empty($this->hakAkses['a_view'])) {
    //         showErrorAkses();
    //         return;
    //     }

    //     $id = filter_var($this->input->get('id'), FILTER_VALIDATE_INT);
    //     $pembiayaan = $id ? \Model\Storage\PembiayaanAset_model::find($id) : null;
    //     if (!$pembiayaan) {
    //         show_404();
    //         return;
    //     }

    //     $aset = \Model\Storage\MsAset_model::select(
    //             'ms_aset.*',
    //             'ms_aset_kategori.kategori_name',
    //             'ms_pembiayaan.nama_pembiayaan'
    //         )
    //         ->leftJoin('ms_aset_kategori', 'ms_aset_kategori.id', '=', 'ms_aset.id_kategori')
    //         ->leftJoin('ms_pembiayaan', 'ms_pembiayaan.kode_jenis_pembiayaan', '=', 'ms_aset.kode_pembiayaan')
    //         ->where('ms_aset.kode_aset', $pembiayaan->kode_aset)
    //         ->first();

    //     if (!$aset) {
    //         show_404();
    //         return;
    //     }

    //     $jenisPembiayaan = \Model\Storage\MsPembiayaan_model::where('kode_jenis_pembiayaan', $aset->kode_pembiayaan)->first();
    //     if (!$jenisPembiayaan) {
    //         show_404();
    //         return;
    //     }


    //     // TDP
           
    //     $m_conf = new \Model\Storage\Conf();

    //     $sql_tdp = "SELECT
    //                     pta.id,
    //             pta.kode_pembiayaan,
    //             pta.tanggal,
    //             ptad.deskripsi,
    //             ptad.nominal,
    //             pta.is_pengurang_pokok
    //         FROM pembiayaan_tdp_aset pta
    //         INNER JOIN pembiayaan_tdp_aset_detail ptad
    //             ON pta.id = ptad.id_pembiayaan_tdp_aset
    //         WHERE pta.kode_pembiayaan = '".$pembiayaan->kode_pembiayaan."'";

    //         $data_tdp = $m_conf->hydrateRaw($sql_tdp)->toArray();

    //         $tdp = [
    //             'header' => [],
    //             'detail' => [],
    //         ];

    //         $tdp_pengurang_pokok_total = 0;

    //         foreach ($data_tdp as $row) {
    //             $row = (array) $row;

    //             if (empty($tdp['header'])) {
    //                 $tdp['header'] = [
    //                     'id'               => $row['id'],
    //                     'kode_pembiayaan'  => $row['kode_pembiayaan'],
    //                     'tanggal'          => $row['tanggal'],
                        
    //                     'is_pengurang_pokok' => $row['is_pengurang_pokok'],
    //                 ];
    //             }

    //             $tdp['detail'][] = [
    //                 'nominal'            => $row['nominal'],
    //                 'deskripsi'        => $row['deskripsi'],
    //             ];

    //             if ((int) $row['is_pengurang_pokok'] === 1) {
    //                 $tdp_pengurang_pokok_total += (float) $row['nominal'];
    //             }
    //         }

    //         // cetak_r($tdp, 1);
    //     // END TDP

        
        
    //     $tdpLocked = $this->isPembiayaanLocked($pembiayaan);
        
    //     $dpData = $this->getDpData($pembiayaan->kode_pembiayaan);

    //     $biaya_penambah_total = 0;
    //     if (!empty($dpData['rows']) && is_array($dpData['rows'])) {
    //         foreach ($dpData['rows'] as $dp) {
    //             if (!empty($dp['is_hutang']) && $dp['is_hutang'] == 1) {
    //                 $biaya_penambah_total += floatval($dp['nominal']);
    //             }
    //         }
    //     }

    //     $harga_beli = floatval($aset['nilai_perolehan'] ?? 0);
    //     $uang_muka = floatval($dpData['uang_muka_total'] ?? 0);
        
    //     $nilai_pembiayaan = $harga_beli - $uang_muka;
    //     $pokok_hutang = $nilai_pembiayaan + $biaya_penambah_total - $tdp_pengurang_pokok_total;

    //     $cicilanHeaderRows = $this->getConnection()->table('pembiayaan_cicilan_aset')
    //         ->where('kode_pembiayaan', $pembiayaan->kode_pembiayaan)
    //         ->get();
    //     $cicilan = !empty($cicilanHeaderRows) ? (array) reset($cicilanHeaderRows) : null;
        
    //     $cicilanDetail = [];
    //     if ($cicilan) {
    //         $cicilanDetailRows = $this->getConnection()->table('pembiayaan_cicilan_detail_aset')
    //             ->selectRaw('kode_pembiayaan, bunga, pokok, sisa_pokok_hutang, jenis_cicilan, angsuran_ke, CONVERT(varchar(10), jatuh_tempo, 23) as jatuh_tempo, nominal, status')
    //             ->where('kode_pembiayaan', $pembiayaan->kode_pembiayaan)
    //             ->orderBy('angsuran_ke', 'asc')
    //             ->get();
    //         foreach ($cicilanDetailRows as $row) {
    //             $cicilanDetail[] = (array) $row;
    //         }
    //     }

    //     $biaya_prepaid = 0;
    //     foreach($dpData['rows'] as $dp){
    //         if($dp['jenis_komponen'] != 'UANG_MUKA' && $dp['angsuran_ke'] == null){
    //             $biaya_prepaid += $dp['nominal'];
    //         }
    //     }
     
    //     $content = [
    //         'pembiayaan'                => $pembiayaan->toArray(),
    //         'aset'                      => $aset->toArray(),
    //         'kode_jenis_pembiayaan'     => $aset->kode_pembiayaan,
    //         'jenis_pembiayaan'          => $jenisPembiayaan->toArray(),
           
    //         'tdp'                       => $tdp,
    //         'dp'                        => $dpData['rows'] ?? [],
    //         'dp_total'                  => $dpData['total'] ?? 0,
    //         'dp_uang_muka_total'        => $dpData['uang_muka_total'] ?? 0,
            
    //         'biaya_prepaid'             => $biaya_prepaid,
            
    //         'can_submit_dp'             => !empty($this->hakAkses['a_submit']),
    //         'can_edit_dp'               => !empty($this->hakAkses['a_edit']),
    //         'can_delete_dp'             => !empty($this->hakAkses['a_delete']),
    //         'dp_locked'                 => $tdpLocked,
    //         'cicilan'                   => $cicilan,
    //         'cicilan_detail'            => $cicilanDetail,
    //         'supplier'                  => $pembiayaan->id_supplier ? \Model\Storage\Supplier_model::find($pembiayaan->id_supplier) : null,
    //         'leasing'                   => $pembiayaan->id_leasing ? \Model\Storage\Supplier_model::find($pembiayaan->id_leasing) : null,
    //         'tanggal_dasar_cicilan'     => $cicilan ? ($cicilan['tanggal_dasar'] ?? date('Y-m-d')) : date('Y-m-d'), // Sesuaikan jika ada field tanggal
    //         'tanggal_pembiayaan'        => $pembiayaan->tanggal_pembiayaan ?? date('Y-m-d'), // Sesuaikan nama field
    //         'cicilan_tersimpan'         => !empty($cicilan),
    //     ];

    //     // cetak_r($tdp, 1);

    //     $view = 'v_proses_leasing';

    //     $this->add_external_js([
    //         'assets/aset/pembiayaan_aset/js/pembiayaan_aset.js',
    //     ]);
    //     $this->add_external_css([
    //         'assets/aset/pembiayaan_aset/css/pembiayaan_aset.css',
    //     ]);

    //     $data                   = $this->includes;
    //     $content['title_panel'] = 'Proses Pembiayaan Aset';
    //     $data['title_menu']     = 'Proses Pembiayaan Aset';
    //     $data['view']            = $this->load->view($this->pathView . $view, $content, true);
    //     $this->load->view($this->template, $data);
    // }


    public function ProsesLeasing()
    {
        if (empty($this->hakAkses['a_view'])) {
            showErrorAkses();
            return;
        }

        
        $id = filter_var($this->input->post('id'), FILTER_VALIDATE_INT);
        $pembiayaan = $id ? \Model\Storage\PembiayaanAset_model::find($id) : null;

        $aset = \Model\Storage\MsAset_model::select(
                'ms_aset.*',
                'ms_aset_kategori.kategori_name',
                'ms_pembiayaan.nama_pembiayaan'
            )
            ->leftJoin('ms_aset_kategori', 'ms_aset_kategori.id', '=', 'ms_aset.id_kategori')
            ->leftJoin('ms_pembiayaan', 'ms_pembiayaan.kode_jenis_pembiayaan', '=', 'ms_aset.kode_pembiayaan')
            ->where('ms_aset.kode_aset', $pembiayaan->kode_aset)
            ->first();

        if (!$aset) {
            show_404();
            return;
        }

        $jenisPembiayaan = \Model\Storage\MsPembiayaan_model::where('kode_jenis_pembiayaan', $aset->kode_pembiayaan)->first();
        if (!$jenisPembiayaan) {
            show_404();
            return;
        }
        

        $content['pembiayaan']  = $pembiayaan->toArray();
        $content['aset']        = $aset->toArray();
        $content['kode_jenis_pembiayaan']     = $aset->kode_pembiayaan;
        $content['supplier']    = $pembiayaan->id_supplier ? \Model\Storage\Supplier_model::find($pembiayaan->id_supplier) : null;
        $content['leasing']     = $pembiayaan->id_leasing ? \Model\Storage\Supplier_model::find($pembiayaan->id_leasing) : null;



        echo $this->load->view($this->pathView . 'v_proses_leasing', $content, true);
      
    }
    
    public function save_tdp()
    {
        if (empty($this->hakAkses['a_submit'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk menyimpan TDP.';
            display_json($this->result);
            return;
        }

        $params = $this->input->post('params');
        $params = is_array($params) ? $params : [];

        try {
            $kodePembiayaan = $params['kode_pembiayaan'] ?? null;

            if (empty($kodePembiayaan)) {
                throw new \Exception('Kode pembiayaan wajib diisi.');
            }

            $m_conf     = new \Model\Storage\Conf();
            $sql_aset    = "select ma.kode_pembiayaan  as kode_jenis_pembiayaan  FROM pembiayaan_aset pa 
                        inner join ms_aset ma on pa.kode_aset = ma.kode_aset 
                        where pa.kode_pembiayaan = '".$params['kode_pembiayaan']."'";
            $data_aset = $m_conf->hydrateRaw($sql_aset)->toArray();

            // cetak_r($sql_aset, 1);

            $m_tdp_header = \Model\Storage\PembiayaanTdpAset_model::where('kode_pembiayaan',$kodePembiayaan)->first();

            $isInsert = empty($m_tdp_header);

            if ($isInsert) {
                $m_tdp_header = new \Model\Storage\PembiayaanTdpAset_model();
            }

            $m_tdp_header->kode_pembiayaan    = $kodePembiayaan;
            $m_tdp_header->tanggal            = $params['tanggal'] ?? null;
            $m_tdp_header->is_pengurang_pokok = $params['is_pengurang_pokok'] ?? 0;
         
            $m_tdp_header->save();

            if (!$isInsert) {
                \Model\Storage\PembiayaanTdpAsetDetail_model::where( 'id_pembiayaan_tdp_aset', $m_tdp_header->id)->delete();
            }

            $rows = $params['rows'] ?? [];
            $totap_dp = 0;

            foreach ($rows as $r) {
                $m_tdp_detail = new \Model\Storage\PembiayaanTdpAsetDetail_model();
                $m_tdp_detail->id_pembiayaan_tdp_aset = $m_tdp_header->id;
                $m_tdp_detail->deskripsi = $r['deskripsi'] ?? '';
                $m_tdp_detail->nominal = $r['nominal'] ?? 0;
                $m_tdp_detail->save();
                

                $totap_dp += $r['nominal'];
            }


            if ($data_aset[0]['kode_jenis_pembiayaan'] == 'PMB03'){
                $m_cicilan = new \Model\Storage\PembiayaanCicilanAset_model();
                $data_update = [
                    'dikurangi_tanda_jadi' => $params['is_pengurang_pokok'] ? $totap_dp : '',
                ];
                $m_cicilan->where('kode_pembiayaan', $kodePembiayaan)->update($data_update);
    
                $this->sync_jadwal_cicilan_otomatis($kodePembiayaan);
            } 

            // cetak_r($data_aset[0], 1);


            if ($data_aset[0]['kode_jenis_pembiayaan'] == 'PMB01') {
    
                $total_pembelian = floatval($params['total_pembelian'] ?? 0);
            
                if ($total_pembelian <= 0) {
                    $existing_pelunasan = \Model\Storage\PembiayaanPelunasanAset_model::where('kode_pembiayaan', $kodePembiayaan)->first();
                    $total_pembelian = $existing_pelunasan ? floatval($existing_pelunasan->total_pembelian ?? 0) : 0;
                }

                $is_pengurang = !empty($params['is_pengurang_pokok']);
                $dikurangi_tanda_jadi = $is_pengurang ? floatval($totap_dp) : 0;

                $diajukan = $total_pembelian - $dikurangi_tanda_jadi;

                $data_update = [
                    'dikurangi_tanda_jadi' => $dikurangi_tanda_jadi,
                    'diajukan'             => $diajukan,
                ];

                \Model\Storage\PembiayaanPelunasanAset_model::where('kode_pembiayaan', $kodePembiayaan)->update($data_update);
            }

            if ($data_aset[0]['kode_jenis_pembiayaan'] == 'PMB02') {

                $total_pembelian = 0;
                
                if ($total_pembelian <= 0) {
                    $existing_tempo = \Model\Storage\PembiayaanTempoRencanaAset_model::where('kode_pembiayaan', $kodePembiayaan)->first();
                    $total_pembelian = $existing_tempo ? floatval($existing_tempo->total_pembelian ?? 0) : 0;
                }

                $is_pengurang           = !empty($params['is_pengurang_pokok']);
                $dikurangi_tanda_jadi   = $is_pengurang ? floatval($totap_dp) : 0;

                $sisa_dijadwalkan       = $total_pembelian - $dikurangi_tanda_jadi;

                $data_update = [
                    'dikurangi_tanda_jadi'  => $dikurangi_tanda_jadi,
                    'sisa_dijadwalkan'      => $sisa_dijadwalkan,
                ];

            
                \Model\Storage\PembiayaanTempoRencanaAset_model::where('kode_pembiayaan', $kodePembiayaan)->update($data_update);
            }

            // $userNama = $this->userdata['detail_user']['nama_detuser'] ?? 'System';
            // $aksi = $isInsert ? 'ditambahkan' : 'diperbarui';
            // $deskripsi_log = "Data TDP {$kodePembiayaan} {$aksi} oleh {$userNama}";
            // Modules::run( 'base/event/save', $m_tdp_header, $deskripsi_log, null, $m_tdp_header->id, null );

            $this->result['status'] = 1;
            $this->result['message'] = $isInsert
                ? 'Data TDP berhasil disimpan.'
                : 'Data TDP berhasil diperbarui.';

        } catch (\Exception $e) {
            error_log("Error penyimpanan TDP Aset: " . $e->getMessage());

            $this->result['status'] = 0;
            $this->result['message'] = 'Gagal menyimpan data TDP: ' . $e->getMessage();
        }

        display_json($this->result);
    }


    public function load_tdp()
    {
        // cetak_r($_POST, 1);

        $params     = $_POST;
        $m_conf     = new \Model\Storage\Conf();

        $sql_cicilan   = "select pca.kode_pembiayaan, pca.tanggal_jt, pca.tenor_bulan, pca.bunga_flat_persen, pca.pokok_hutang, pca.total_bunga, pca.total_ar, pca.angsuran_per_bulan, 
        pcda.angsuran_ke, pcda.status, pcda.bunga, pcda.jatuh_tempo, pcda.nominal, pcda.pokok, pcda.sisa_pokok_hutang , pcda.total_pokok  from pembiayaan_cicilan_aset pca
        inner join pembiayaan_cicilan_detail_aset pcda on pca.kode_pembiayaan  = pcda.kode_pembiayaan where pca.kode_pembiayaan = '".$params['kode']."'";
        $data_cicilan  = $m_conf->hydrateRaw($sql_cicilan)->toArray();

        $sql_tdp    = "SELECT pta.id, pta.kode_pembiayaan, pta.tanggal, ptad.deskripsi, ptad.nominal, pta.is_pengurang_pokok
            FROM pembiayaan_tdp_aset pta
            INNER JOIN pembiayaan_tdp_aset_detail ptad
                ON pta.id = ptad.id_pembiayaan_tdp_aset
            WHERE pta.kode_pembiayaan = '".$params['kode']."'";

        $data_tdp = $m_conf->hydrateRaw($sql_tdp)->toArray();

        $tdp = [
            'header' => [],
            'detail' => [],
        ];

        $tdp_pengurang_pokok_total = 0;

        foreach ($data_tdp as $row) {
            $row = (array) $row;

            if (empty($tdp['header'])) {
                $tdp['header'] = [
                    'id'               => $row['id'],
                    'kode_pembiayaan'  => $row['kode_pembiayaan'],
                    'tanggal'          => $row['tanggal'],
                    'is_pengurang_pokok' => $row['is_pengurang_pokok'],
                ];
            }

            $tdp['detail'][] = [
                'nominal'            => $row['nominal'],
                'deskripsi'        => $row['deskripsi'],
            ];

            if ((int) $row['is_pengurang_pokok'] === 1) {
                $tdp_pengurang_pokok_total += (float) $row['nominal'];
            }
        }
        $data['tdp']        = $tdp ?? [];
        $data['cicilan']    = !empty($data_cicilan) ? 1 : 0;

        // cetak_r($data,1);

        echo $this->load->view('pembiayaan_aset/v_load_tdp', $data, true);
    }


    public function load_dp()
    {

        $params     = $_POST;
        $m_conf     = new \Model\Storage\Conf();

        $sql_dp    = "select pda.kode_pembiayaan, pda.tanggal_dp, pdad.jenis_komponen, pdad.nominal, pdad.angsuran_ke from pembiayaan_dp_aset pda
                inner join pembiayaan_dp_aset_detail pdad on pda.id = pdad.id_pembiayaan_dp_aset where pda.kode_pembiayaan = '".$params['kode']."'";

        $data_dp = $m_conf->hydrateRaw($sql_dp)->toArray();

        $dp = [
            'header' => [],
            'detail_non_angsuran' => [],
            'detail_angsuran' => [],
        ];

        foreach ($data_dp as $row) {
            $row = (array) $row;

            if (empty($dp['header'])) {
                $dp['header'] = [
                    'kode_pembiayaan'  => $row['kode_pembiayaan'],
                    'tanggal_dp'       => $row['tanggal_dp'],
                ];
            }

            if ($row['jenis_komponen'] != 'ANGSURAN'){
                $dp['detail_non_angsuran'][] = [
                    'nominal'          => $row['nominal'],
                    'jenis_komponen'   => $row['jenis_komponen'],
                ];
            }

            if ($row['jenis_komponen'] == 'ANGSURAN'){
                $dp['detail_angsuran'][] = [
                    'nominal'          => $row['nominal'],
                    'jenis_komponen'   => $row['jenis_komponen'],
                    'angsuran_ke'      => $row['angsuran_ke'],
                ];
            }

        }
        $data['dp'] = $dp ?? [];

        // cetak_r($data, 1);

        echo $this->load->view('pembiayaan_aset/v_load_dp', $data, true);
    }


    public function load_cicilan()
    {

        $params     = $_POST;
        $m_conf     = new \Model\Storage\Conf();

        $sql_cicilan   = "select pca.kode_pembiayaan, pca.tanggal_jt, pca.tenor_bulan, pca.bunga_flat_persen, pca.pokok_hutang, pca.total_bunga, pca.total_ar, pca.angsuran_per_bulan, 
        pcda.angsuran_ke, pcda.status, pcda.bunga, pcda.jatuh_tempo, pcda.nominal, pcda.pokok, pcda.sisa_pokok_hutang , pcda.total_pokok  from pembiayaan_cicilan_aset pca
        inner join pembiayaan_cicilan_detail_aset pcda on pca.kode_pembiayaan  = pcda.kode_pembiayaan where pca.kode_pembiayaan = '".$params['kode']."'";

        $data_cicilan  = $m_conf->hydrateRaw($sql_cicilan)->toArray();



        $cicilan = [
            'header' => [],
            'detail' => [],
        ];

        foreach ($data_cicilan as $row) {
            $row = (array) $row;

            if (empty($cicilan['header'])) {
                $cicilan['header'] = [
                    // 'id'               => $row['id'],
                    'kode_pembiayaan'       => $row['kode_pembiayaan'],
                    'tenor_bulan'           => $row['tenor_bulan'],
                    'bunga_flat_persen'     => $row['bunga_flat_persen'],
                    'pokok_hutang'          => $row['pokok_hutang'],
                    'total_ar'              => $row['total_ar'],
                    'angsuran_per_bulan'    => $row['angsuran_per_bulan'],
                    'tanggal_jt'            => $row['tanggal_jt'],
                    'total_bunga'           => $row['total_bunga'],
                ];
            }
            
            $cicilan['detail'][] = [
                'angsuran_ke'       => $row['angsuran_ke'],
                'jatuh_tempo'       => $row['jatuh_tempo'],
                'nominal'           => $row['nominal'],
                'pokok'             => $row['pokok'],
                'sisa_pokok_hutang' => $row['sisa_pokok_hutang'],
                'total_pokok'       => $row['total_pokok'],
                'nominal'           => $row['nominal'],
                'status'            => $row['status'],
                'bunga'             => $row['bunga'],
            ];

        }
        
        $data['data_cicilan'] = $cicilan ?? [];
        // cetak_r($data, 1);

        echo $this->load->view('pembiayaan_aset/v_load_cicilan', $data, true);
    }

    

    public function load_hitung_cicilan()
    {

        $params     = $_POST;
        $m_conf     = new \Model\Storage\Conf();


        // GET DATA PEMBIAYAAN
        $sql_aset    = "select ma.nilai_perolehan as harga_beli FROM pembiayaan_aset pa 
                    inner join ms_aset ma on pa.kode_aset = ma.kode_aset 
                    where pa.kode_pembiayaan = '".$params['kode']."'";
        $data_aset = $m_conf->hydrateRaw($sql_aset)->toArray();
        // cetak_r($data_aset, 1);
        // END GET TDP


        // GET TDP
        $sql_tdp    = "SELECT pta.id, pta.kode_pembiayaan, pta.tanggal, ptad.deskripsi, ptad.nominal, pta.is_pengurang_pokok
            FROM pembiayaan_tdp_aset pta
            INNER JOIN pembiayaan_tdp_aset_detail ptad
                ON pta.id = ptad.id_pembiayaan_tdp_aset
            WHERE pta.kode_pembiayaan = '".$params['kode']."'";
        $data_tdp = $m_conf->hydrateRaw($sql_tdp)->toArray();
        // cetak_r($data_tdp, 1);
        // END GET TDP



        // GET DP

        $sql_dp    = "select pda.kode_pembiayaan, pda.tanggal_dp, pdad.jenis_komponen, pdad.nominal, pdad.angsuran_ke from pembiayaan_dp_aset pda
                inner join pembiayaan_dp_aset_detail pdad on pda.id = pdad.id_pembiayaan_dp_aset where pda.kode_pembiayaan = '".$params['kode']."'";

        $data_dp = $m_conf->hydrateRaw($sql_dp)->toArray();

        $dp = [
            'header' => [],
            'detail_non_angsuran' => [],
            'detail_angsuran' => [],
            'uang_muka' => [],
        ];

        foreach ($data_dp as $row) {
            $row = (array) $row;

            if (empty($dp['header'])) {
                $dp['header'] = [
                    'kode_pembiayaan'  => $row['kode_pembiayaan'],
                    'tanggal_dp'       => $row['tanggal_dp'],
                ];
            }

            if ($row['jenis_komponen'] != 'ANGSURAN' && $row['jenis_komponen'] != 'UANG MUKA'){
                $dp['detail_non_angsuran'][] = [
                    'nominal'          => $row['nominal'],
                    'jenis_komponen'   => $row['jenis_komponen'],
                ];
            }

            if ($row['jenis_komponen'] == 'UANG MUKA'){
                $dp['uang_muka'][] = [
                    'nominal'          => $row['nominal'],
                ];
            }

            if ($row['jenis_komponen'] == 'ANGSURAN'){
                $dp['detail_angsuran'][] = [
                    'nominal'          => $row['nominal'],
                    'jenis_komponen'   => $row['jenis_komponen'],
                    'angsuran_ke'      => $row['angsuran_ke'],
                ];
            }

        }
        // cetak_r($dp, 1);

        // END GET DP

        $sql_cicilan   = "select pca.kode_pembiayaan, pca.tanggal_jt, pca.tenor_bulan, pca.bunga_flat_persen, pca.pokok_hutang, pca.total_bunga, pca.total_ar, pca.angsuran_per_bulan, 
        pcda.angsuran_ke, pcda.status, pcda.bunga, pcda.jatuh_tempo, pcda.nominal, pcda.pokok, pcda.sisa_pokok_hutang , pcda.total_pokok  from pembiayaan_cicilan_aset pca
        inner join pembiayaan_cicilan_detail_aset pcda on pca.kode_pembiayaan  = pcda.kode_pembiayaan where pca.kode_pembiayaan = '".$params['kode']."'";

        $data_cicilan  = $m_conf->hydrateRaw($sql_cicilan)->toArray();



        $cicilan = [
            'header' => [],
            'detail' => [],
        ];

        foreach ($data_cicilan as $row) {
            $row = (array) $row;

            if (empty($cicilan['header'])) {
                $cicilan['header'] = [
                    // 'id'               => $row['id'],
                    'kode_pembiayaan'       => $row['kode_pembiayaan'],
                    'tenor_bulan'           => $row['tenor_bulan'],
                    'bunga_flat_persen'     => $row['bunga_flat_persen'],
                    'pokok_hutang'          => $row['pokok_hutang'],
                    'total_ar'              => $row['total_ar'],
                    'angsuran_per_bulan'    => $row['angsuran_per_bulan'],
                    'tanggal_jt'            => $row['tanggal_jt'],
                    'total_bunga'           => $row['total_bunga'],
                ];
            }
            
            $cicilan['detail'][] = [
                'angsuran_ke'       => $row['angsuran_ke'],
                'jatuh_tempo'       => $row['jatuh_tempo'],
                'nominal'           => $row['nominal'],
                'pokok'             => $row['pokok'],
                'sisa_pokok_hutang' => $row['sisa_pokok_hutang'],
                'total_pokok'       => $row['total_pokok'],
                'nominal'           => $row['nominal'],
                'status'            => $row['status'],
                'bunga'             => $row['bunga'],
            ];

        }

        $data['data_aset']   = $data_aset ?? [];
        $data['data_tdp']    = $data_tdp ?? [];
        $data['data_dp']     = $dp ?? [];
        $data['data_cicilan'] = $cicilan ?? [];
        // cetak_r($data, 1);

        echo $this->load->view('pembiayaan_aset/v_load_hitung_cicilan', $data, true);
    }


    public function load_pelunasan()
    {

        $params     = $_POST;
        $m_conf     = new \Model\Storage\Conf();

        $sql_aset   = "select ma.nilai_perolehan as harga_beli, p.nama as nama_supplier FROM pembiayaan_aset pa 
                    inner join ms_aset ma on pa.kode_aset = ma.kode_aset 
                    inner join pelanggan p on p.id = pa.id_supplier 
                    where pa.kode_pembiayaan = '".$params['kode']."'";
        $data_aset = $m_conf->hydrateRaw($sql_aset)->toArray();



        $sql_tdp    = "SELECT pta.id, pta.kode_pembiayaan, pta.tanggal, ptad.deskripsi, ptad.nominal, pta.is_pengurang_pokok
            FROM pembiayaan_tdp_aset pta
            INNER JOIN pembiayaan_tdp_aset_detail ptad
                ON pta.id = ptad.id_pembiayaan_tdp_aset
            WHERE pta.kode_pembiayaan = '".$params['kode']."'";
        $data_tdp = $m_conf->hydrateRaw($sql_tdp)->toArray();
   
        // END GET TDP


        $sql_pelunasan    = "select ppa.id, ppa.diajukan, ppa.dikurangi_tanda_jadi, ppa.kode_pembiayaan , ppa.tanggal_pelunasan , ppa.harga_beli_aset, ppa.biaya_lain,
                        ppa.total_pembelian, ppa.dikurangi_tanda_jadi , ppa.diajukan,
                        ppad.urutan , ppad.deskripsi , ppad.nominal
                        FROM pembiayaan_pelunasan_aset ppa
                        inner join pembiayaan_pelunasan_aset_detail ppad on ppa.kode_pembiayaan = ppad.kode_pembiayaan 
                        where ppa.kode_pembiayaan = '".$params['kode']."'";

                        

        $data_pelunasan = $m_conf->hydrateRaw($sql_pelunasan)->toArray();

        $pelunasan = [
            'header' => [],
            'detail' => [],
        ];

        foreach ($data_pelunasan as $row) {
            $row = (array) $row;

            if (empty($pelunasan['header'])) {
                $pelunasan['header'] = [
                    'id'                    => $row['id'],
                    'kode_pembiayaan'       => $row['kode_pembiayaan'],
                    'tanggal_pelunasan'     => $row['tanggal_pelunasan'],
                    'dikurangi_tanda_jadi'  => $row['dikurangi_tanda_jadi'],
                    'diajukan'              => $row['diajukan'],
                ];
            }

            $pelunasan['detail'][] = [
                'urutan'       => $row['urutan'],
                'deskripsi'    => $row['deskripsi'],
                'nominal'      => $row['nominal'],
            ];

        }
        $data['pelunasan'] = $pelunasan ?? [];
        $data['aset']      = $data_aset[0];
        $data['tdp']       = $data_tdp;

        // cetak_r($data, 1);


        echo $this->load->view('pembiayaan_aset/v_load_pelunasan', $data, true);
    }


    public function load_tempo()
    {

        $params     = $_POST;
        $m_conf     = new \Model\Storage\Conf();

        $sql_aset   = "select ma.nilai_perolehan as harga_beli, p.nama as nama_supplier FROM pembiayaan_aset pa 
                    inner join ms_aset ma on pa.kode_aset = ma.kode_aset 
                    inner join pelanggan p on p.id = pa.id_supplier 
                    where pa.kode_pembiayaan = '".$params['kode']."'";
        $data_aset = $m_conf->hydrateRaw($sql_aset)->toArray();


        $sql_tdp    = "SELECT pta.id, pta.kode_pembiayaan, pta.tanggal, ptad.deskripsi, ptad.nominal, pta.is_pengurang_pokok
            FROM pembiayaan_tdp_aset pta
            INNER JOIN pembiayaan_tdp_aset_detail ptad
                ON pta.id = ptad.id_pembiayaan_tdp_aset
            WHERE pta.kode_pembiayaan = '".$params['kode']."'";
        $data_tdp = $m_conf->hydrateRaw($sql_tdp)->toArray();
   
        // END GET TDP


        $sql_tempo    = "select ptra.id, ptra.dikurangi_tanda_jadi, ptra.biaya_lain, ptra.kode_pembiayaan, ptra.harga_beli_aset, ptra.jumlah_termin, ptra.total_pembelian, ptra.sisa_dijadwalkan, 
                        ptda.jatuh_tempo , ptda.nominal, ptda.termin_ke, ptda.status 
                        from pembiayaan_tempo_rencana_aset ptra  
                        inner join pembiayaan_tempo_detail_aset ptda on ptra.kode_pembiayaan = ptda.kode_pembiayaan 
                        where ptra.kode_pembiayaan = '".$params['kode']."'";

                        
        $data_tempo = $m_conf->hydrateRaw($sql_tempo)->toArray();

        $tempo = [
            'header' => [],
            'detail' => [],
        ];

        foreach ($data_tempo as $row) {
            $row = (array) $row;

            if (empty($tempo['header'])) {
                $tempo['header'] = [
                    'id'                    => $row['id'],
                    'kode_pembiayaan'       => $row['kode_pembiayaan'],
                    'harga_beli_aset'       => $row['harga_beli_aset'],
                    'jumlah_termin'         => $row['jumlah_termin'],
                    'total_pembelian'       => $row['total_pembelian'],
                    'sisa_dijadwalkan'      => $row['sisa_dijadwalkan'],
                    'biaya_lain'            => $row['biaya_lain'],
                    'dikurangi_tanda_jadi'  => $row['dikurangi_tanda_jadi'],
                ];
            }

            $tempo['detail'][] = [
                'jatuh_tempo'       => $row['jatuh_tempo'],
                'termin_ke'    => $row['termin_ke'],
                'nominal'      => $row['nominal'],
                'status'      => $row['status'],
            ];

        }
        $data['pelunasan'] = $pelunasan ?? [];
        $data['aset']      = $data_aset[0];
        $data['tempo']     = $tempo;
        $data['tdp']       = $data_tdp;

        // cetak_r($data, 1);


        echo $this->load->view('pembiayaan_aset/v_load_tempo', $data, true);
    }


    
    











    public function get_dp()
    {
        if (empty($this->hakAkses['a_view'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk melihat data DP.';
            display_json($this->result);
            return;
        }

        $kodePembiayaan = trim((string) $this->input->post('kode_pembiayaan'));
        $pembiayaan = \Model\Storage\PembiayaanAset_model::where('kode_pembiayaan', $kodePembiayaan)->first();
        $aset = $pembiayaan ? $this->getAset($pembiayaan->kode_aset) : null;
        if (!$pembiayaan) {
            $this->result['message'] = 'Data pembiayaan Leasing tidak ditemukan.';
        } else {
            $this->result['status'] = 1;
            $this->result['content'] = $this->getDpData($kodePembiayaan);
        }
        display_json($this->result);
    }

    public function save_dp()
    {
        if (empty($this->hakAkses['a_submit'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk menyimpan TDP.';
            display_json($this->result);
            return;
        }
        
        $params = $this->input->post('params');
        $params = is_array($params) ? $params : [];
        // cetak_r($params, 1);
        try {
            $kodePembiayaan = $params['kode_pembiayaan'] ?? null;

            if (empty($kodePembiayaan)) {
                throw new \Exception('Kode pembiayaan wajib diisi.');
            }

            $m_dp_header = \Model\Storage\PembiayaanDpAset_model::where('kode_pembiayaan',$kodePembiayaan)->first();

            $isInsert = empty($m_dp_header);

            if ($isInsert) {
                $m_dp_header = new \Model\Storage\PembiayaanDpAset_model();
            }

            $m_dp_header->kode_pembiayaan    = $kodePembiayaan;
            $m_dp_header->tanggal_dp         = $params['tanggal_dp'] ?? null;
         
            $m_dp_header->save();

            if (!$isInsert) {
                \Model\Storage\PembiayaanDpAsetDetail_model::where( 'id_pembiayaan_dp_aset', $m_dp_header->id)->delete();
            }

            $rows = $params['rows'] ?? [];

            foreach ($rows as $r) {
                $m_dp_detail = new \Model\Storage\PembiayaanDpAsetDetail_model();
                $m_dp_detail->id_pembiayaan_dp_aset = $m_dp_header->id;
                $m_dp_detail->jenis_komponen    = $r['jenis_komponen'] ?? '';
                $m_dp_detail->nominal           = $r['nominal'] ?? 0;
                $m_dp_detail->angsuran_ke       = $r['angsuran_ke'] ?? 0;
                $m_dp_detail->save();
            }


            $this->sync_jadwal_cicilan_otomatis($kodePembiayaan);

            
            // $userNama = $this->userdata['detail_user']['nama_detuser'] ?? 'System';
            // $aksi = $isInsert ? 'ditambahkan' : 'diperbarui';
            // $deskripsi_log = "Data DP {$kodePembiayaan} {$aksi} oleh {$userNama}";
            // Modules::run( 'base/event/save', $m_dp_header, $deskripsi_log, null, $m_dp_header->id, null );

            $this->result['status'] = 1;
            $this->result['message'] = $isInsert
                ? 'Data DP berhasil disimpan.'
                : 'Data DP berhasil diperbarui.';

        } catch (\Exception $e) {
            error_log("Error penyimpanan DP Aset: " . $e->getMessage());

            $this->result['status'] = 0;
            $this->result['message'] = 'Gagal menyimpan data DP: ' . $e->getMessage();
        }

        display_json($this->result);
    }


    private function sync_jadwal_cicilan_otomatis($kodePembiayaan)
    {
        if (empty($kodePembiayaan)) {
            throw new \Exception('Kode pembiayaan kosong saat sinkronisasi cicilan.');
        }

        $header = \Model\Storage\PembiayaanCicilanAset_model::where('kode_pembiayaan', $kodePembiayaan)->first();
        
        if (!$header || empty($header->tenor_bulan) || empty($header->tanggal_jt)) {
            return;
        }
        
        $m_conf     = new \Model\Storage\Conf();
        $sql_dp     = "select pdad.angsuran_ke from pembiayaan_dp_aset pda
                    inner join pembiayaan_dp_aset_detail pdad on pda.id = pdad.id_pembiayaan_dp_aset where pda.kode_pembiayaan = '".$kodePembiayaan."'";
        $data_dp    = $m_conf->hydrateRaw($sql_dp)->toArray();

        $paidAngsuranList = [];
        if (!empty($data_dp)) {
            foreach ($data_dp as $row) {
                $paidAngsuranList[] = (int) $row['angsuran_ke'];
            }
        }

        // 1. Hapus Detail Lama SAJA (Header aman, kolom dikurangi_tanda_jadi tidak hilang)
        \Model\Storage\PembiayaanCicilanDetailAset_model::where('kode_pembiayaan', $kodePembiayaan)->delete();

        // 2. Hitung Pokok Hutang Sebenarnya (Dikurangi TDP jika ada)
        $dikurangiTandaJadi = (float) ($header->dikurangi_tanda_jadi ?? 0);
        $basePokokHutang    = (float) $header->pokok_hutang - $dikurangiTandaJadi;
        $basePokokHutang    = max(0, $basePokokHutang); // Cegah minus

        // 3. Hitung Ulang Angsuran & Bunga berdasarkan Pokok Hutang Baru
        $tenor       = (int) $header->tenor_bulan;
        $bungaPersen = (float) $header->bunga_flat_persen;
        $monthlyRate = ($bungaPersen / 100) / 12;
        
        // Rumus hitung cicilan tetap (Anuitas)
        $cicilanTetap = (int) round($basePokokHutang * ($monthlyRate / (1 - pow(1 + $monthlyRate, -$tenor))));
        $totalBunga   = ($cicilanTetap * $tenor) - $basePokokHutang;

        // Update Header agar UI menampilkan Angsuran dan Total Bunga yang baru & benar
        $header->angsuran_per_bulan = $cicilanTetap;
        $header->total_bunga        = $totalBunga;
        $header->total_ar           = $basePokokHutang + $totalBunga;
        $header->save();

        // 4. Looping Insert Detail
        $sisaPokok  = $basePokokHutang;
        $baseDate   = $header->tanggal_jt;

        for ($i = 1; $i <= $tenor; $i++) {
            $sisaPokokSebelumnya = $sisaPokok;
            $bungaBlnPresisi     = $sisaPokok * $monthlyRate;
            $bungaBln            = (int) round($bungaBlnPresisi);

            if ($i === $tenor) {
                $pokokBlnPresisi = max($sisaPokok, 0);
                $totalCicilan    = (int) round($pokokBlnPresisi + $bungaBlnPresisi);
            } else {
                $pokokBlnPresisi = $cicilanTetap - $bungaBlnPresisi;
                $totalCicilan    = (int) round($cicilanTetap);
            }

            $pokokBln  = (int) round($pokokBlnPresisi);
            $sisaPokok = max($sisaPokok - $pokokBlnPresisi, 0);

            $isPaidViaDp   = in_array($i, $paidAngsuranList, true);
            $jenisCicilan  = $isPaidViaDp ? 'DP' : 'Cicilan';
            $statusCicilan = $isPaidViaDp ? 1 : 0;

            $jatuhTempo = date('Y-m-d', strtotime($baseDate . " +{$i} months -1 month"));

            $detail = new \Model\Storage\PembiayaanCicilanDetailAset_model();
            $detail->kode_pembiayaan         = $kodePembiayaan;
            $detail->kode_cicilan_pembiayaan = $kodePembiayaan . '-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $detail->angsuran_ke             = $i;
            $detail->jatuh_tempo             = $jatuhTempo;
            $detail->nominal                 = $totalCicilan;
            $detail->pokok                   = $pokokBln;
            $detail->bunga                   = $bungaBln;
            $detail->sisa_pokok_hutang       = (int) round($sisaPokok);
            $detail->total_pokok             = (int) round($sisaPokokSebelumnya);
            $detail->jenis_cicilan           = $jenisCicilan;
            $detail->status                  = $statusCicilan;
            $detail->save();
        }
    }

    public function delete_dp()
    {
        if (empty($this->hakAkses['a_delete'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk menghapus DP.';
            display_json($this->result);
            return;
        }

        $params = $this->input->post('params');
        $id = is_array($params) ? ($params['id'] ?? null) : $params;
        try {
            $id = filter_var($id, FILTER_VALIDATE_INT);
            $dpRows = $id
                ? $this->getConnection()->table('pembiayaan_dp_aset')->where('id', $id)->get()
                : [];
            $dp = !empty($dpRows) ? (array) reset($dpRows) : null;
            $pembiayaan = $dp
                ? \Model\Storage\PembiayaanAset_model::where('kode_pembiayaan', $dp['kode_pembiayaan'])->first()
                : null;
            if (!$dp || !$pembiayaan) {
                $this->result['message'] = 'Data DP tidak ditemukan.';
            } elseif ($this->isPembiayaanLocked($pembiayaan)) {
                $this->result['message'] = 'DP tidak dapat dihapus karena pembiayaan sudah selesai atau aset sudah masuk tahap Penerimaan Aset.';
            } else {
                $deleted = $this->getConnection()->table('pembiayaan_dp_aset')->where('id', $dp['id'])
                    ->where('kode_pembiayaan', $pembiayaan->kode_pembiayaan)
                    ->delete();
                if ($deleted) {
                    $this->result['status'] = 1;
                    $this->result['message'] = 'Data DP berhasil dihapus.';
                    $this->result['content'] = $this->getDpData($pembiayaan->kode_pembiayaan);
                } else {
                    $this->result['message'] = 'Data DP tidak ditemukan atau sudah berubah.';
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal menghapus DP: ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal menghapus DP: ' . $e->getMessage();
        }
        display_json($this->result);
    }

    public function simpan_detail_cicilan()
    {
        if (empty($this->hakAkses['a_submit'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk menyimpan detail cicilan.';
            display_json($this->result);
            return;
        }

        $postData = $this->input->post('params') ?: $this->input->post();
        
        $kodePembiayaan = trim((string) ($postData['kode_pembiayaan'] ?? ''));
        $tenor          = (int) ($postData['tenor'] ?? 0);
        $bunga          = (float) ($postData['bunga'] ?? 0);
        $pokokHutang    = (int) ($postData['pokok_hutang'] ?? 0);
        $totalAr        = (int) ($postData['total_ar'] ?? 0);
        $totalPerolehan = (int) ($postData['total_perolehan'] ?? 0);
        $tanggal_jt     = trim((string) ($postData['tanggal_jatuh_tempo'] ?? ''));

        try {
            if ($kodePembiayaan === '' || $tenor < 1 || $tenor > 600) {
                $this->result['message'] = 'Kode pembiayaan dan tenor cicilan harus valid.';
            } elseif ($bunga < 0 || $bunga > 100) {
                $this->result['message'] = 'Bunga flat harus di antara 0 dan 100 persen.';
            } elseif ($pokokHutang <= 0 || $totalAr <= 0) {
                $this->result['message'] = 'Nilai pokok hutang dan Total A/R tidak valid.';
            } elseif (empty($tanggal_jt)) {
                $this->result['message'] = 'Tanggal jatuh tempo wajib diisi.';
            } else {
                $pembiayaan = \Model\Storage\PembiayaanAset_model::where('kode_pembiayaan', $kodePembiayaan)->first();
                
                if (!$pembiayaan) {
                    $this->result['message'] = 'Data pembiayaan Leasing tidak ditemukan.';
                } else {
                    $connection = $this->getConnection();
                    
                    $stored = $connection->transaction(function () use ($connection, $kodePembiayaan, $pembiayaan, $tenor, $bunga, $pokokHutang, $totalAr, $tanggal_jt) {
                        
                
                        $connection->table('pembiayaan_cicilan_detail_aset')
                            ->where('kode_pembiayaan', $kodePembiayaan)
                            ->delete();
                        
                        $connection->table('pembiayaan_cicilan_aset')
                            ->where('kode_pembiayaan', $kodePembiayaan)
                            ->delete();

                    
                        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_jt)) {
                            $baseDueDate = $tanggal_jt; 
                        } else {
                            $dateObj = \DateTime::createFromFormat('d F Y', $tanggal_jt);
                            if ($dateObj) {
                                $baseDueDate = $dateObj->format('Y-m-d');
                            } else {
                                throw new \RuntimeException('Format tanggal jatuh tempo tidak valid: ' . $tanggal_jt);
                            }
                        }

                      
                        $sql_angsuran = "SELECT angsuran_ke FROM pembiayaan_dp_aset pda
                                        INNER JOIN pembiayaan_dp_aset_detail pdad ON pda.id = pdad.id_pembiayaan_dp_aset
                                        WHERE pdad.jenis_komponen = 'ANGSURAN' AND pda.kode_pembiayaan = ?";
                        
                        $angsuranRows = $connection->select($sql_angsuran, [$kodePembiayaan]);
                        
                        $paidAngsuranList = [];
                        if (!empty($angsuranRows)) {
                            foreach ($angsuranRows as $row) {
                                $paidAngsuranList[] = (int) $row['angsuran_ke'];
                            }
                        }

                      
                        $monthlyRate = ($bunga / 100) / 12;
                        $cicilanTetap = (int) round(
                            $pokokHutang * ($monthlyRate / (1 - pow(1 + $monthlyRate, -$tenor)))
                        );
                        
                        $totalPayment       = $cicilanTetap * $tenor;
                        $totalBunga         = $totalPayment - $pokokHutang;
                        $selisihPembulatan  = $totalPayment - $totalAr;

                        $connection->table('pembiayaan_cicilan_aset')->insert([
                            'kode_pembiayaan'     => $kodePembiayaan,
                            'tenor_bulan'         => $tenor,
                            'bunga_flat_persen'   => number_format($bunga, 2, '.', ''),
                            'pokok_hutang'        => $pokokHutang,
                            'total_bunga'         => $totalBunga,
                            'total_ar'            => $totalAr,
                            'angsuran_per_bulan'  => $cicilanTetap,
                            'tanggal_jt'          => $tanggal_jt,
                            'selisih_pembulatan'  => $selisihPembulatan,
                            'total_perolehan'     => $totalPerolehan,
                        ]);

                        $sisaPokok = (float) $pokokHutang;
                        $detailCicilan = [];

                        for ($i = 1; $i <= $tenor; $i++) {
                            $monthOffset = $i - 1;
                            $dueDate = $this->addMonthsClamped($baseDueDate, $monthOffset);

                            $sisaPokokSebelumnya = $sisaPokok;

                            if ($i === $tenor) {
                                $pokokBln     = $sisaPokok;
                                $bungaBln     = $cicilanTetap - $pokokBln;
                                $totalCicilan = $cicilanTetap;
                            } else {
                                $bungaBln     = $sisaPokok * $monthlyRate;   
                                $pokokBln     = $cicilanTetap - $bungaBln;
                                $totalCicilan = $cicilanTetap;
                            }

                            $sisaPokok = $sisaPokok - $pokokBln;
                            if ($sisaPokok < 0) {
                                $sisaPokok = 0;
                            }

                            // === LOGIKA PENENTUAN STATUS (SESUAI PERMINTAAN) ===
                            $jenisCicilan = 'Cicilan';
                            $statusCicilan = 0; // Default: Pending (Belum Lunas)

                            // CEK: Jika nomor angsuran ($i) ada di daftar yang sudah dibayar, maka set jadi Lunas
                            if (in_array($i, $paidAngsuranList, true)) {
                                $jenisCicilan = 'DP'; 
                                $statusCicilan = 1;   // Lunas
                            }

                            $detailCicilan[] = [
                                'kode_pembiayaan'         => $kodePembiayaan,
                                'kode_cicilan_pembiayaan' => $kodePembiayaan . '-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                                'angsuran_ke'             => $i,
                                'jatuh_tempo'             => $dueDate,
                                'total_pokok'             => (int) round($sisaPokokSebelumnya),
                                'pokok'                   => (int) round($pokokBln),
                                'bunga'                   => (int) round($bungaBln),
                                'nominal'                 => (int) round($totalCicilan),
                                'sisa_pokok_hutang'       => (int) round($sisaPokok),
                                'jenis_cicilan'           => $jenisCicilan,
                                'status'                  => $statusCicilan, // 1 = Lunas, 0 = Pending
                            ];
                        }

                        if (!empty($detailCicilan)) {
                            $connection->table('pembiayaan_cicilan_detail_aset')->insert($detailCicilan);
                        }

                        $connection->table('pembiayaan_aset')->where('kode_pembiayaan', $kodePembiayaan)->update([
                            'status' => 'SELESAI',
                        ]);

                        return ['detail_count' => $tenor];
                    });

                    $this->result['status'] = 1;
                    $this->result['total_data'] = $stored['detail_count'];
                    $this->result['total_header'] = 1;
                    $this->result['total_detail'] = $stored['detail_count'];
                    $this->result['message'] = 'Detail cicilan berhasil disimpan sebanyak ' . $stored['detail_count'] . ' bulan.';
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal menyimpan detail cicilan (Database): ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal menyimpan detail cicilan: ' . $e->getMessage();
        }

        display_json($this->result);
    }

    public function get_cicilan()
    {
        if (empty($this->hakAkses['a_view'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk melihat detail cicilan.';
            display_json($this->result);
            return;
        }

        $params = $this->input->post('params');
        $kodePembiayaan = is_array($params)
            ? trim((string) ($params['kode_pembiayaan'] ?? ''))
            : trim((string) $params);
        $pembiayaan = \Model\Storage\PembiayaanAset_model::where('kode_pembiayaan', $kodePembiayaan)->first();
        $aset = $pembiayaan ? $this->getAset($pembiayaan->kode_aset) : null;

        if (!$pembiayaan) {
            $this->result['message'] = 'Data pembiayaan Leasing tidak ditemukan.';
        } else {
            $headerRows = $this->getConnection()->table('pembiayaan_cicilan_aset')
                ->where('kode_pembiayaan', $kodePembiayaan)
                ->get();
            $header = !empty($headerRows) ? (array) reset($headerRows) : null;
            $details = [];
            if ($header) {
                $detailRows = $this->getConnection()->table('pembiayaan_cicilan_detail_aset')
                    ->selectRaw('kode_pembiayaan, jenis_cicilan ,angsuran_ke, CONVERT(varchar(10), jatuh_tempo, 23) as jatuh_tempo, nominal, status')
                    ->where('kode_pembiayaan', $kodePembiayaan)
                    ->orderBy('angsuran_ke', 'asc')
                    ->get();
                foreach ($detailRows as $row) {
                    $details[] = (array) $row;
                }
            }

            $this->result['status'] = 1;
            $this->result['content'] = [
                'header' => $header,
                'details' => $details,
            ];
        }

        display_json($this->result);
    }

    public function save_data()
    {
        if (empty($this->hakAkses['a_submit'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk menambahkan pembiayaan aset.';
            display_json($this->result);
            return;
        }

        $params = $this->input->post('params');
        $params = is_array($params) ? $params : [];

        try {
            $kodeAset   = trim((string) ($params['kode_aset'] ?? ''));
            $idSupplier = filter_var($params['id_supplier'] ?? null, FILTER_VALIDATE_INT);
            $idLeasing  = filter_var($params['id_leasing'] ?? null, FILTER_VALIDATE_INT);
            $aset       = $this->getAset($kodeAset);

            if (!$aset) {
                $this->result['message'] = 'Aset wajib dipilih dan harus terdaftar.';
            } elseif (\Model\Storage\PembiayaanAset_model::where('kode_aset', $kodeAset)->exists()) {
                $this->result['message'] = 'Aset tersebut sudah memiliki data pembiayaan.';
            } else {
                $model = new \Model\Storage\PembiayaanAset_model();
                $model->kode_pembiayaan = $this->generateKodePembiayaan();
                $model->kode_aset = $kodeAset;
                $model->id_supplier = $idSupplier;
                $model->id_leasing = $aset ? $idLeasing : null;
                $model->save();

                $userNama = $this->userdata['detail_user']['nama_detuser'] ?? 'System';
                Modules::run(
                    'base/event/save',
                    $model,
                    "Pembiayaan aset {$model->kode_pembiayaan} untuk aset {$aset->kode_aset} ditambahkan oleh {$userNama}",
                    null,
                    $model->id,
                    $model
                );

                $data_aset = \Model\Storage\MsAset_model::where(
                                'kode_aset',
                                $kodeAset
                            )->get()->toArray();
                // cetak_r($data_aset, 1);
                
                $this->result['status'] = 1;
                $this->result['message'] = 'Data pembiayaan aset berhasil disimpan.';
                $this->result['data'] = [
                    'id' => $model->id,
                    'kode_jenis_pembiayaan' => $data_aset[0]['kode_pembiayaan']
                ];
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal menyimpan pembiayaan aset: ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal menyimpan pembiayaan aset: ' . $e->getMessage();
        }

        display_json($this->result);
    }

    public function edit_data()
    {
        if (empty($this->hakAkses['a_edit'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk mengubah pembiayaan aset.';
            display_json($this->result);
            return;
        }

        $params = $this->input->post('params');
        $params = is_array($params) ? $params : [];

        try {
            $model = \Model\Storage\PembiayaanAset_model::find($params['id'] ?? null);
            $kodeAset = trim((string) ($params['kode_aset'] ?? ''));
            $idSupplier = filter_var($params['id_supplier'] ?? null, FILTER_VALIDATE_INT);
            $idLeasing = filter_var($params['id_leasing'] ?? null, FILTER_VALIDATE_INT);
            $aset = $this->getAset($kodeAset);

            if (!$model) {
                $this->result['message'] = 'Data pembiayaan aset tidak ditemukan.';
            } elseif (!$aset) {
                $this->result['message'] = 'Aset wajib dipilih dan harus terdaftar.';
            } elseif (\Model\Storage\PembiayaanAset_model::where('kode_aset', $kodeAset)->where('id', '!=', $model->id)->exists()) {
                $this->result['message'] = 'Aset tersebut sudah memiliki data pembiayaan.';
            } elseif (!$this->isSupplierValid($idSupplier)) {
                $this->result['message'] = 'Supplier wajib dipilih dari daftar supplier aktif.';
            } elseif (!$this->isSupplierValid($idLeasing)) {
                $this->result['message'] = 'Leasing wajib dipilih dari daftar supplier aktif.';
            } else {
                $kodeLama = $model->kode_pembiayaan;
                $model->kode_aset = $kodeAset;
                $model->id_supplier = $idSupplier;
                $model->id_leasing = $aset ? $idLeasing : null;
                $model->save();

                $userNama = $this->userdata['detail_user']['nama_detuser'] ?? 'System';
                Modules::run(
                    'base/event/update',
                    $model,
                    "Pembiayaan aset {$kodeLama} untuk aset {$aset->kode_aset} diubah oleh {$userNama}",
                    null,
                    $model->id,
                    $model
                );
                $this->result['status'] = 1;
                $this->result['message'] = 'Data pembiayaan aset berhasil diubah.';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal mengubah pembiayaan aset: ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal mengubah pembiayaan aset: ' . $e->getMessage();
        }

        display_json($this->result);
    }

    public function delete_data()
    {
        if (empty($this->hakAkses['a_delete'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk menghapus pembiayaan aset.';
            display_json($this->result);
            return;
        }

        $id = $this->input->post('params');
        try {
            $model = \Model\Storage\PembiayaanAset_model::find($id);
            if (!$model) {
                $this->result['message'] = 'Data pembiayaan aset tidak ditemukan.';
            } else {
                $kode = $model->kode_pembiayaan;
                $kodeAset = $model->kode_aset;
                $model->delete();

                $userNama = $this->userdata['detail_user']['nama_detuser'] ?? 'System';
                Modules::run('base/event/delete', $model, "Pembiayaan aset {$kode} untuk aset {$kodeAset} dihapus oleh {$userNama}", null, $id, $model);
                $this->result['status'] = 1;
                $this->result['message'] = 'Data pembiayaan aset berhasil dihapus.';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal menghapus pembiayaan aset: ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal menghapus pembiayaan aset: ' . $e->getMessage();
        }

        display_json($this->result);
    }

    private function getAsetOptions($excludePembiayaanId = null)
    {
        $query = \Model\Storage\MsAset_model::select(
                'ms_aset.kode_aset',
                'ms_aset.deskripsi_aset',
                'ms_aset.unit_pengguna',
                'ms_aset.nilai_perolehan',
                'ms_aset.kode_pembiayaan',
                'ms_aset_kategori.kategori_name',
                'ms_pembiayaan.nama_pembiayaan'
            )
            ->leftJoin('ms_aset_kategori', 'ms_aset_kategori.id', '=', 'ms_aset.id_kategori')
            ->leftJoin('ms_pembiayaan', 'ms_pembiayaan.kode_jenis_pembiayaan', '=', 'ms_aset.kode_pembiayaan')
            ->whereNotExists(function ($subquery) use ($excludePembiayaanId) {
                $subquery->selectRaw('1')
                    ->from('pembiayaan_aset')
                    ->whereRaw('pembiayaan_aset.kode_aset = ms_aset.kode_aset');
                
                // ✅ TAMBAHKAN CEK NULL DI SINI
                if ($excludePembiayaanId !== null) {
                    $subquery->where('pembiayaan_aset.id', '!=', $excludePembiayaanId);
                }
            });

        return $query->orderBy('ms_aset.kode_aset', 'asc')->get()->toArray();
    }

    private function getAset($kodeAset)
    {
        if ($kodeAset === '') {
            return null;
        }

        return \Model\Storage\MsAset_model::select('ms_aset.kode_aset', 'ms_aset.nilai_perolehan' , 'ms_pembiayaan.nama_pembiayaan')
            ->leftJoin('ms_pembiayaan', 'ms_pembiayaan.kode_jenis_pembiayaan', '=', 'ms_aset.kode_pembiayaan')
            ->where('ms_aset.kode_aset', $kodeAset)
            ->first();
    }

    private function getDpData($kodePembiayaan)
    {
        $rows = $this->getConnection()
            ->table('pembiayaan_dp_aset')
            ->selectRaw('id, kode_pembiayaan, is_hutang, CONVERT(varchar(10), tanggal_dp, 23) as tanggal_dp, jenis_komponen, deskripsi, nominal, angsuran_ke, CONVERT(varchar(10), jatuh_tempo, 23) as jatuh_tempo')
            ->where('kode_pembiayaan', $kodePembiayaan)
            ->orderBy('jenis_komponen', 'asc')
            ->orderBy('angsuran_ke', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $dpRows = [];
        $totalCents = 0;
        $uangMukaCents = 0;
        foreach ($rows as $row) {
            $dpRow = (array) $row;
            $nominalCents = (int) round((float) $dpRow['nominal'] * 100);
            $totalCents += $nominalCents;
            if (strtoupper((string) $dpRow['jenis_komponen']) === 'UANG_MUKA') {
                $uangMukaCents += $nominalCents;
            }
            $dpRows[] = $dpRow;
        }

        return [
            'rows' => $dpRows,
            'total' => number_format($totalCents / 100, 2, '.', ''),
            'uang_muka_total' => number_format($uangMukaCents / 100, 2, '.', ''),
        ];
    }

    private function moneyToCents($value)
    {
        $value = trim((string) $value);
        if (!preg_match('/^\d{1,13}(\.\d{1,2})?$/', $value)) {
            throw new \RuntimeException('Nilai nominal tidak valid.');
        }

        $parts = array_pad(explode('.', $value, 2), 2, '0');
        return ((int) $parts[0] * 100) + (int) str_pad($parts[1], 2, '0');
    }

    private function centsToMoney($cents)
    {
        $negative = $cents < 0;
        $cents = abs((int) $cents);
        return ($negative ? '-' : '') . intdiv($cents, 100) . '.' . str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }

    private function addMonthsClamped($date, $months)
    {
        $baseDate = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if (!$baseDate) {
            throw new \RuntimeException('Tanggal jatuh tempo cicilan tidak valid.');
        }

        $day = (int) $baseDate->format('d');
        $targetMonth = $baseDate->modify('first day of this month')
            ->modify('+' . (int) $months . ' months');
        $targetDay = min($day, (int) $targetMonth->format('t'));

        return $targetMonth->setDate(
            (int) $targetMonth->format('Y'),
            (int) $targetMonth->format('m'),
            $targetDay
        )->format('Y-m-d');
    }

    private function isPembiayaanLocked($pembiayaan)
    {
        $statusSelesai = isset($pembiayaan->status)
            && strtoupper(trim((string) $pembiayaan->status)) === 'SELESAI';
        $sudahDiterima = \Model\Storage\PenerimaanAset_model::where('kode_aset', $pembiayaan->kode_aset)->exists();

        return $statusSelesai || $sudahDiterima;
    }

    private function isValidSqlDate($value)
    {
        if (!is_string($value) || $value === '') {
            return false;
        }

        $date = \DateTime::createFromFormat('!Y-m-d', $value);
        $errors = \DateTime::getLastErrors();

        return $date && $date->format('Y-m-d') === $value
            && (!$errors || ($errors['warning_count'] === 0 && $errors['error_count'] === 0));
    }

    private function getTdpMutationContext($id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            return null;
        }

        $rows = $this->getConnection()
            ->table('pembiayaan_tdp_aset')
            ->where('id', $id)
            ->get();
        if (!is_array($rows) || empty($rows)) {
            return null;
        }

        $tdp = (array) reset($rows);
        $pembiayaan = \Model\Storage\PembiayaanAset_model::where('kode_pembiayaan', $tdp['kode_pembiayaan'])->first();
        if (!$pembiayaan) {
            return null;
        }

        $aset = $this->getAset($pembiayaan->kode_aset);
        // if (!$this->supportsTdp($aset)) {
        //     return null;
        // }

        return [
            'tdp' => $tdp,
            'locked' => $this->isPembiayaanLocked($pembiayaan),
        ];
    }

    private function getSupplierOptions()
    {
        return \Model\Storage\Supplier_model::select('id', 'nomor', 'nama')
            ->whereIn('kategori_supplier', ['ASET', 'SEWA'])
            ->where('jenis', '<>', 'ekspedisi')
            ->where('mstatus', 1)
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('pelanggan')
                    ->whereIn('kategori_supplier', ['ASET', 'SEWA'])
                    ->where('jenis', '<>', 'ekspedisi')
                    ->groupBy('nomor');
            })
            ->orderBy('nama', 'asc')
            ->get()
            ->toArray();
    }

    private function isSupplierValid($idSupplier)
    {
        return $idSupplier !== false && $idSupplier !== null && $idSupplier > 0
            && \Model\Storage\Supplier_model::where('id', $idSupplier)
            ->where('tipe', 'supplier')
            ->where('jenis', '<>', 'ekspedisi')
            ->where('mstatus', 1)
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('pelanggan')
                    ->where('tipe', 'supplier')
                    ->where('jenis', '<>', 'ekspedisi')
                    ->groupBy('nomor');
            })
            ->exists();
    }

    // private function isLeasing($aset)
    // {
    //     return $aset && strcasecmp(trim((string) $aset->nama_pembiayaan), 'Leasing') === 0;
    // }

    // private function supportsTdp($aset)
    // {
        
    //     if (!$aset) {
    //         return false;
    //     }

    //     $jenisPembiayaan = trim((string) $aset->nama_pembiayaan);
    //     return strcasecmp($jenisPembiayaan, 'Leasing') === 0
    //         || strcasecmp($jenisPembiayaan, 'Cash') === 0
    //         || strcasecmp($jenisPembiayaan, 'Cash Tempo') === 0;
    // }

    private function generateKodePembiayaan()
    {
        $prefix = 'PBY' . date('ym');
        for ($number = 1; $number <= 999; $number++) {
            $candidate = $prefix . str_pad((string) $number, 3, '0', STR_PAD_LEFT);
            if (!\Model\Storage\PembiayaanAset_model::where('kode_pembiayaan', $candidate)->exists()) {
                return $candidate;
            }
        }

        throw new \RuntimeException('Kode pembiayaan aset untuk periode ' . date('ym') . ' sudah mencapai batas nomor 999.');
    }




    // public function ProsesCash()
    // {
    //     if (empty($this->hakAkses['a_view'])) {
    //         showErrorAkses();
    //         return;
    //     }

    //     $id = filter_var($this->input->get('id'), FILTER_VALIDATE_INT);
    //     $pembiayaan = $id ? \Model\Storage\PembiayaanAset_model::find($id) : null;
    //     if (!$pembiayaan) {
    //         show_404();
    //         return;
    //     }

    //     $aset = \Model\Storage\MsAset_model::select(
    //             'ms_aset.*',
    //             'ms_aset_kategori.kategori_name',
    //             'ms_pembiayaan.nama_pembiayaan'
    //         )
    //         ->leftJoin('ms_aset_kategori', 'ms_aset_kategori.id', '=', 'ms_aset.id_kategori')
    //         ->leftJoin('ms_pembiayaan', 'ms_pembiayaan.kode_jenis_pembiayaan', '=', 'ms_aset.kode_pembiayaan')
    //         ->where('ms_aset.kode_aset', $pembiayaan->kode_aset)
    //         ->first();

    //     if (!$aset) {
    //         show_404();
    //         return;
    //     }

    //     if (strcasecmp(trim((string) $aset->nama_pembiayaan), 'Cash') !== 0) {
    //         show_404();
    //         return;
    //     }

    //     $jenisPembiayaan = \Model\Storage\MsPembiayaan_model::where('kode_jenis_pembiayaan', $aset->kode_pembiayaan)->first();
    //     if (!$jenisPembiayaan) {
    //         show_404();
    //         return;
    //     }

    //     $tdpRows = $this->getConnection()
    //         ->table('pembiayaan_tdp_aset')
    //         ->selectRaw('id, kode_pembiayaan, CONVERT(varchar(10), tanggal, 23) as tanggal, deskripsi, nominal, is_pengurang_pokok')
    //         ->where('kode_pembiayaan', $pembiayaan->kode_pembiayaan)
    //         ->orderBy('tanggal', 'asc')
    //         ->orderBy('id', 'asc')
    //         ->get();
    //     $tdp = [];
    //     foreach ($tdpRows as $row) {
    //         $tdp[] = (array) $row;
    //     }
    //     $tdpLocked = $this->isPembiayaanLocked($pembiayaan);

    //     $pelunasanRows = $this->getConnection()
    //         ->table('pembiayaan_pelunasan_aset')
    //         ->selectRaw('id, kode_pembiayaan, CONVERT(varchar(10), tanggal_pelunasan, 23) as tanggal_pelunasan, harga_beli_aset, biaya_lain, total_pembelian, dikurangi_tanda_jadi, diajukan')
    //         ->where('kode_pembiayaan', $pembiayaan->kode_pembiayaan)
    //         ->get();
    //     $pelunasan = !empty($pelunasanRows) ? (array) reset($pelunasanRows) : null;
    //     $pelunasanLocked = $tdpLocked; 

    //     $pelunasanDetails = [];
    //     if (!empty($pelunasan)) {
    //         $pelunasanDetails = \Model\Storage\PembiayaanPelunasanAsetDetail_model::where('kode_pembiayaan', $pelunasan['kode_pembiayaan'])
    //             ->orderBy('urutan', 'asc')
    //             ->get()
    //             ->toArray();
    //     }

    //     // cetak_r($pelunasanDetails, 1);


    //     $content = [
    //         'pembiayaan'                => $pembiayaan->toArray(),
    //         'aset'                      => $aset->toArray(),
    //         'kode_jenis_pembiayaan'     => $aset->kode_pembiayaan,
    //         'jenis_pembiayaan'          => $jenisPembiayaan->toArray(),
            
    //         'can_submit_tdp'            => !empty($this->hakAkses['a_submit']),
    //         'can_edit_tdp'              => !empty($this->hakAkses['a_edit']),
    //         'can_delete_tdp'            => !empty($this->hakAkses['a_delete']),
    //         'tdp_locked'                => $tdpLocked,
    //         'tdp'                       => $tdp,
            
    //         'can_submit_pelunasan'      => !empty($this->hakAkses['a_submit']),
    //         'can_edit_pelunasan'        => !empty($this->hakAkses['a_edit']),
    //         'can_delete_pelunasan'      => !empty($this->hakAkses['a_delete']),
    //         'pelunasan'                 => $pelunasan,
    //         'pelunasan_locked'          => $pelunasanLocked,
    //         'pelunasan_detail'          => $pelunasanDetails,
            
    //         'supplier'                  => \Model\Storage\Supplier_model::find($pembiayaan->id_supplier),
    //     ];

    //     $this->add_external_js([
    //         'assets/aset/pembiayaan_aset/js/pembiayaan_aset.js',
    //     ]);
    //     $this->add_external_css([
    //         'assets/aset/pembiayaan_aset/css/pembiayaan_aset.css',
    //     ]);

    //     $data                   = $this->includes;
    //     $content['title_panel'] = 'Proses Pembiayaan Aset - Cash';
    //     $data['title_menu']     = 'Proses Pembiayaan Aset';
        
    //     $data['view'] = $this->load->view($this->pathView . 'v_proses_cash', $content, true);
    //     $this->load->view($this->template, $data);
    // }


    public function ProsesCash()
    {
        if (empty($this->hakAkses['a_view'])) {
            showErrorAkses();
            return;
        }

        $id = filter_var($this->input->post('id'), FILTER_VALIDATE_INT);
        $pembiayaan = $id ? \Model\Storage\PembiayaanAset_model::find($id) : null;

        $aset = \Model\Storage\MsAset_model::select(
                'ms_aset.*',
                'ms_aset_kategori.kategori_name',
                'ms_pembiayaan.nama_pembiayaan'
            )
            ->leftJoin('ms_aset_kategori', 'ms_aset_kategori.id', '=', 'ms_aset.id_kategori')
            ->leftJoin('ms_pembiayaan', 'ms_pembiayaan.kode_jenis_pembiayaan', '=', 'ms_aset.kode_pembiayaan')
            ->where('ms_aset.kode_aset', $pembiayaan->kode_aset)
            ->first();

        if (!$aset) {
            show_404();
            return;
        }

        $jenisPembiayaan = \Model\Storage\MsPembiayaan_model::where('kode_jenis_pembiayaan', $aset->kode_pembiayaan)->first();
        if (!$jenisPembiayaan) {
            show_404();
            return;
        }
        

        $content['pembiayaan']  = $pembiayaan->toArray();
        $content['aset']        = $aset->toArray();
        $content['kode_jenis_pembiayaan']     = $aset->kode_pembiayaan;
        $content['supplier']    = $pembiayaan->id_supplier ? \Model\Storage\Supplier_model::find($pembiayaan->id_supplier) : null;
        $content['leasing']     = $pembiayaan->id_leasing ? \Model\Storage\Supplier_model::find($pembiayaan->id_leasing) : null;

        echo $this->load->view($this->pathView . 'v_proses_cash', $content, true);
    }

    public function save_pelunasan()
    {
        $params = $this->input->post('params');
        $params = is_array($params) ? $params : [];

        // cetak_r($params, 1);

        try {
            $kodePembiayaan     = trim($params['kode_pembiayaan'] ?? '');
            $tanggalPelunasan   = trim($params['tanggal_pelunasan'] ?? '');
            $rows               = isset($params['rows']) && is_array($params['rows']) ? $params['rows'] : [];

            if (empty($kodePembiayaan) || empty($tanggalPelunasan)) {
                throw new \Exception('Kode pembiayaan dan tanggal pelunasan wajib diisi.');
            }

            $pembiayaan = \Model\Storage\PembiayaanAset_model::where('kode_pembiayaan', $kodePembiayaan)->first();
            if (!$pembiayaan) {
                throw new \Exception('Data pembiayaan tidak ditemukan.');
            }

            $aset = $this->getAset($pembiayaan->kode_aset); 
            if (!$aset || !isset($aset->nilai_perolehan)) {
                throw new \Exception('Data aset atau harga beli tidak ditemukan.');
            }

            $hargaBeli = floatval($aset->nilai_perolehan);

            // 1. CEK: Apakah data header sudah ada? (Untuk menentukan Insert atau Update)
            $existing_header = \Model\Storage\PembiayaanPelunasanAset_model::where('kode_pembiayaan', $kodePembiayaan)->first();
            $isInsert = empty($existing_header);

            if ($isInsert) {
                $m_header = new \Model\Storage\PembiayaanPelunasanAset_model();
            } else {
                $m_header = $existing_header; // Gunakan data yang sudah ada untuk di-update
            }

            // 2. Hitung total pembelian dari rows TERLEBIH DAHULU
            $total_pembelian = 0;
            if (!empty($rows)) {
                foreach ($rows as $d) {
                    $total_pembelian += floatval($d['nominal'] ?? 0);
                }
            }

            // 3. Isi data header dan simpan (Hanya 1x save, otomatis Insert atau Update)
            $m_header->kode_pembiayaan   = $kodePembiayaan;
            $m_header->tanggal_pelunasan = $tanggalPelunasan;
            $m_header->harga_beli_aset   = $hargaBeli;
            $m_header->total_pembelian   = $total_pembelian;
            $m_header->diajukan          = $total_pembelian;
            $m_header->save(); 

            // 4. HANDLE DETAIL: Jika Update, hapus detail lama terlebih dahulu
            if (!$isInsert) {
                \Model\Storage\PembiayaanPelunasanAsetDetail_model::where('pelunasan_id', $m_header->id)->delete();
            }

            // 5. INSERT DETAIL BARU
            if (!empty($rows)) {
                foreach ($rows as $index => $d) {
                    $m_detail = new \Model\Storage\PembiayaanPelunasanAsetDetail_model();
                    $m_detail->pelunasan_id    = $m_header->id; 
                    $m_detail->kode_pembiayaan = $kodePembiayaan;
                    $m_detail->urutan          = $index + 1;
                    $m_detail->deskripsi       = trim($d['deskripsi'] ?? '') ?: 'Harga beli aset';
                    $m_detail->nominal         = floatval($d['nominal'] ?? 0);
                    $m_detail->is_harga_beli   = !empty($d['is_harga_beli']) ? 1 : 0;
                    $m_detail->save(); 
                }
            }
            
            $this->result['status'] = 1;
            $this->result['content'] = ['id' => $m_header->id]; // Sekarang ID-nya BENAR, tidak null
            $this->result['message'] = $isInsert 
                ? 'Data pelunasan berhasil disimpan.' 
                : 'Data pelunasan berhasil diperbarui.';

        } catch (\Exception $e) {
            // Catch exception biasa (tanpa Illuminate)
            $this->result['message'] = 'Gagal menyimpan pelunasan: ' . $e->getMessage();
        }

        display_json($this->result);
    }

    public function delete_pelunasan()
    {
        if (empty($this->hakAkses['a_delete'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk menghapus pelunasan.';
            display_json($this->result);
            return;
        }

        $params = $this->input->post('params');
        $params = is_array($params) ? $params : [];
        $id = filter_var($params['id'] ?? null, FILTER_VALIDATE_INT);

        try {
            $connection = $this->getConnection();
            $rows = $id
                ? $connection->table('pembiayaan_pelunasan_aset')->where('id', $id)->get()
                : [];
            $pelunasan = !empty($rows) ? (array) reset($rows) : null;
            $pembiayaan = $pelunasan
                ? \Model\Storage\PembiayaanAset_model::where('kode_pembiayaan', $pelunasan['kode_pembiayaan'])->first()
                : null;
            $aset = $pembiayaan ? $this->getAset($pembiayaan->kode_aset) : null;

            if (!$pelunasan || !$pembiayaan || !$aset || strcasecmp(trim((string) $aset->nama_pembiayaan), 'Cash') !== 0) {
                $this->result['message'] = 'Data pelunasan Cash tidak ditemukan.';
            } elseif ($this->isPembiayaanLocked($pembiayaan)) {
                $this->result['message'] = 'Pelunasan tidak dapat dihapus karena pembiayaan sudah selesai atau aset sudah masuk Penerimaan Aset.';
            } else {
                
                $connection->transaction(function () use ($connection, $pelunasan) {
                    
                    $connection->table('pembiayaan_pelunasan_aset_detail')
                        ->where('pelunasan_id', $pelunasan['id'])
                        ->delete();

                    $deleted = $connection->table('pembiayaan_pelunasan_aset')
                        ->where('id', $pelunasan['id'])
                        ->where('kode_pembiayaan', $pelunasan['kode_pembiayaan'])
                        ->delete();

                    if (!$deleted) {
                        throw new \RuntimeException('Data pelunasan tidak ditemukan atau sudah berubah.');
                    }
                });

                $this->result['status'] = 1;
                $this->result['message'] = 'Data pelunasan (header & detail) berhasil dihapus.';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal menghapus pelunasan: ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal menghapus pelunasan: ' . $e->getMessage();
        }

        display_json($this->result);
    }




    // public function ProsesTempo()
    // {
    //     if (empty($this->hakAkses['a_view'])) {
    //         showErrorAkses();
    //         return;
    //     }

    //     $id = filter_var($this->input->get('id'), FILTER_VALIDATE_INT);
    //     $pembiayaan = $id ? \Model\Storage\PembiayaanAset_model::find($id) : null;
    //     if (!$pembiayaan) {
    //         show_404();
    //         return;
    //     }

    //     $aset = \Model\Storage\MsAset_model::select(
    //             'ms_aset.*',
    //             'ms_aset_kategori.kategori_name',
    //             'ms_pembiayaan.nama_pembiayaan'
    //         )
    //         ->leftJoin('ms_aset_kategori', 'ms_aset_kategori.id', '=', 'ms_aset.id_kategori')
    //         ->leftJoin('ms_pembiayaan', 'ms_pembiayaan.kode_jenis_pembiayaan', '=', 'ms_aset.kode_pembiayaan')
    //         ->where('ms_aset.kode_aset', $pembiayaan->kode_aset)
    //         ->first();

    //     if (!$aset) {
    //         show_404();
    //         return;
    //     }

    //     if (strcasecmp(trim((string) $aset->nama_pembiayaan), 'Cash Tempo') !== 0) {
    //         show_404();
    //         return;
    //     }

    //     $jenisPembiayaan = \Model\Storage\MsPembiayaan_model::where('kode_jenis_pembiayaan', $aset->kode_pembiayaan)->first();
    //     if (!$jenisPembiayaan) {
    //         show_404();
    //         return;
    //     }

    //     $tdpRows = $this->getConnection()
    //         ->table('pembiayaan_tdp_aset')
    //         ->selectRaw('id, kode_pembiayaan, CONVERT(varchar(10), tanggal, 23) as tanggal, deskripsi, nominal, is_pengurang_pokok')
    //         ->where('kode_pembiayaan', $pembiayaan->kode_pembiayaan)
    //         ->orderBy('tanggal', 'asc')
    //         ->orderBy('id', 'asc')
    //         ->get();
    //     $tdp = [];
    //     foreach ($tdpRows as $row) {
    //         $tdp[] = (array) $row;
    //     }
    //     $tdpLocked = $this->isPembiayaanLocked($pembiayaan);

    //     $tempoHeaderRows = $this->getConnection()
    //         ->table('pembiayaan_tempo_rencana_aset')
    //         ->selectRaw('id, kode_pembiayaan, harga_beli_aset, biaya_lain, total_pembelian, dikurangi_tanda_jadi, sisa_dijadwalkan, jumlah_termin')
    //         ->where('kode_pembiayaan', $pembiayaan->kode_pembiayaan)
    //         ->get();
    //     $tempoHeader = !empty($tempoHeaderRows) ? (array) reset($tempoHeaderRows) : null;
    //     $tempoLocked = $tdpLocked;

    //     $tempoDetails = [];
    //     if ($tempoHeader) {
    //         $detailRows = $this->getConnection()
    //             ->table('pembiayaan_tempo_detail_aset')
    //             ->selectRaw('id, termin_ke, CONVERT(varchar(10), jatuh_tempo, 23) as jatuh_tempo, nominal, status')
    //             ->where('kode_pembiayaan', $pembiayaan->kode_pembiayaan)
    //             ->orderBy('termin_ke', 'asc')
    //             ->get();
    //         foreach ($detailRows as $row) {
    //             $tempoDetails[] = (array) $row;
    //         }
    //     }

    //     $content = [
    //         'pembiayaan'                => $pembiayaan->toArray(),
    //         'aset'                      => $aset->toArray(),
    //         'jenis_pembiayaan'          => $jenisPembiayaan->toArray(),
            
    //         // TDP Variables
    //         'can_submit_tdp'            => !empty($this->hakAkses['a_submit']),
    //         'can_edit_tdp'              => !empty($this->hakAkses['a_edit']),
    //         'can_delete_tdp'            => !empty($this->hakAkses['a_delete']),
    //         'tdp_locked'                => $tdpLocked,
    //         'tdp'                       => $tdp,
            
    //         // Tempo Variables
    //         'can_submit_tempo'          => !empty($this->hakAkses['a_submit']),
    //         'can_edit_tempo'            => !empty($this->hakAkses['a_edit']),
    //         'can_delete_tempo'          => !empty($this->hakAkses['a_delete']),
    //         'tempo_locked'              => $tempoLocked,
    //         'tempo_header'              => $tempoHeader,
    //         'tempo_details'             => $tempoDetails,
            
    //         'supplier'                  => \Model\Storage\Supplier_model::find($pembiayaan->id_supplier),
    //     ];

    //     $this->add_external_js(['assets/aset/pembiayaan_aset/js/pembiayaan_aset.js']);
    //     $this->add_external_css(['assets/aset/pembiayaan_aset/css/pembiayaan_aset.css']);

    //     $data = $this->includes;
    //     $content['title_panel'] = 'Proses Pembiayaan Aset - Cash Tempo';
    //     $data['title_menu'] = 'Proses Pembiayaan Aset';
    //     $data['view'] = $this->load->view($this->pathView . 'v_proses_tempo', $content, true);
    //     $this->load->view($this->template, $data);
    // }


    public function ProsesTempo()
    {
         if (empty($this->hakAkses['a_view'])) {
            showErrorAkses();
            return;
        }

        $id = filter_var($this->input->post('id'), FILTER_VALIDATE_INT);
        $pembiayaan = $id ? \Model\Storage\PembiayaanAset_model::find($id) : null;

        $aset = \Model\Storage\MsAset_model::select(
                'ms_aset.*',
                'ms_aset_kategori.kategori_name',
                'ms_pembiayaan.nama_pembiayaan'
            )
            ->leftJoin('ms_aset_kategori', 'ms_aset_kategori.id', '=', 'ms_aset.id_kategori')
            ->leftJoin('ms_pembiayaan', 'ms_pembiayaan.kode_jenis_pembiayaan', '=', 'ms_aset.kode_pembiayaan')
            ->where('ms_aset.kode_aset', $pembiayaan->kode_aset)
            ->first();

        if (!$aset) {
            show_404();
            return;
        }

        $jenisPembiayaan = \Model\Storage\MsPembiayaan_model::where('kode_jenis_pembiayaan', $aset->kode_pembiayaan)->first();
        if (!$jenisPembiayaan) {
            show_404();
            return;
        }
        

        $content['pembiayaan']  = $pembiayaan->toArray();
        $content['aset']        = $aset->toArray();
        $content['kode_jenis_pembiayaan']     = $aset->kode_pembiayaan;
        $content['supplier']    = $pembiayaan->id_supplier ? \Model\Storage\Supplier_model::find($pembiayaan->id_supplier) : null;
        $content['leasing']     = $pembiayaan->id_leasing ? \Model\Storage\Supplier_model::find($pembiayaan->id_leasing) : null;

        echo $this->load->view($this->pathView . 'v_proses_tempo', $content, true);
    }


    public function save_rencana_tempo()
    {
        if (empty($this->hakAkses['a_submit'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk menyimpan rencana tempo.';
            display_json($this->result);
            return;
        }

        $params = $this->input->post('params');
        $params = is_array($params) ? $params : [];

        try {
            $kodePembiayaan = $params['kode_pembiayaan'] ?? '';
            $hargaBeli      = $params['harga_beli'] ?? '0';
            $biayaLain      = $params['biaya_lain'] ?? '0';
            $jumlahTermin   = filter_var($params['jumlah_termin'] ?? null, FILTER_VALIDATE_INT);
            $rows           = $params['rows'] ?? [];

            if ($kodePembiayaan === '') {
                throw new \Exception('Kode pembiayaan tidak valid.');
            }
            if ($jumlahTermin === false || $jumlahTermin < 1 || $jumlahTermin > 60) {
                throw new \Exception('Jumlah termin harus antara 1 dan 60.');
            }
            if (!is_array($rows) || count($rows) !== $jumlahTermin) {
                throw new \Exception('Jumlah baris termin tidak sesuai.');
            }
            if (!preg_match('/^\d{1,13}(\.\d{1,2})?$/', str_replace(',', '.', $hargaBeli))) {
                throw new \Exception('Harga beli tidak valid.');
            }
            if (!preg_match('/^\d{1,13}(\.\d{1,2})?$/', str_replace(',', '.', $biayaLain))) {
                throw new \Exception('Biaya lain tidak valid.');
            }

            $pembiayaan = \Model\Storage\PembiayaanAset_model::where('kode_pembiayaan', $kodePembiayaan)->first();
            $aset = $pembiayaan ? $this->getAset($pembiayaan->kode_aset) : null;

            if (!$pembiayaan || !$aset) {
                throw new \Exception('Data pembiayaan atau aset tidak ditemukan.');
            }

            $hargaCents = $this->moneyToCents($hargaBeli);
            $biayaCents = $this->moneyToCents($biayaLain);
            $totalPembelianCents = $hargaCents + $biayaCents;

            $tdpRows = \Model\Storage\PembiayaanTdpAset_model::where('kode_pembiayaan', $kodePembiayaan)->where('is_pengurang_pokok', 1)->get();
            $tdpCents = 0;
            foreach ($tdpRows as $tdpRow) {
                $tdpCents += $this->moneyToCents($tdpRow->nominal ?? 0);
            }

            $sisaDijadwalkanCents = $totalPembelianCents - $tdpCents;
            if ($sisaDijadwalkanCents < 0) {
                throw new \Exception('Total TDP melebihi total pembelian.');
            }

            $existingHeader = \Model\Storage\PembiayaanTempoRencanaAset_model::where('kode_pembiayaan', $kodePembiayaan)->first();
            
            if ($existingHeader) {
                \Model\Storage\PembiayaanTempoDetailAset_model::where('kode_pembiayaan', $kodePembiayaan)->delete();
                \Model\Storage\PembiayaanTempoRencanaAset_model::where('kode_pembiayaan', $kodePembiayaan)->delete();
            }

            $totalTerminCents = 0;
            $insertedDetails = [];
            $seenTerminKe = [];

            foreach ($rows as $index => $row) {
                if (!is_array($row)) {
                    throw new \Exception('Baris termin ke-' . ($index + 1) . ' tidak valid.');
                }

                $terminKe = $index + 1;
                $jatuhTempo = trim((string) ($row['jatuh_tempo'] ?? ''));
                $nominal = trim((string) ($row['nominal'] ?? ''));

                if (!$this->isValidSqlDate($jatuhTempo)) {
                    throw new \Exception('Tanggal jatuh tempo termin ke-' . $terminKe . ' tidak valid.');
                }
                if (!preg_match('/^\d{1,13}(\.\d{1,2})?$/', str_replace(',', '.', $nominal))) {
                    throw new \Exception('Nominal termin ke-' . $terminKe . ' tidak valid.');
                }

                $nominalCents = $this->moneyToCents($nominal);
                if ($nominalCents <= 0) {
                    throw new \Exception('Nominal termin ke-' . $terminKe . ' harus lebih dari 0.');
                }

                if (isset($seenTerminKe[$jatuhTempo])) {
                    throw new \Exception('Tanggal jatuh tempo termin ke-' . $terminKe . ' duplikat.');
                }
                $seenTerminKe[$jatuhTempo] = $terminKe;

                $totalTerminCents += $nominalCents;
                $insertedDetails[] = [
                    'kode_pembiayaan' => $kodePembiayaan,
                    'termin_ke'       => $terminKe,
                    'jatuh_tempo'     => $jatuhTempo,
                    'nominal'         => (int) substr($nominalCents, 0, -2),
                    'status'          => 0,
                ];
            }

            if ($totalTerminCents !== $sisaDijadwalkanCents) {
                throw new \Exception('Total nominal termin tidak sama dengan sisa dijadwalkan.');
            }

            $m_header = new \Model\Storage\PembiayaanTempoRencanaAset_model();
            $m_header->kode_pembiayaan      = $kodePembiayaan;
            $m_header->harga_beli_aset      = (int) substr($hargaCents, 0, -2);
            $m_header->biaya_lain           = (int) substr($biayaCents, 0, -2);
            $m_header->total_pembelian      = (int) substr($totalPembelianCents, 0, -2);
            $m_header->dikurangi_tanda_jadi = (int) substr($tdpCents, 0, -2);
            $m_header->sisa_dijadwalkan     = (int) substr($sisaDijadwalkanCents, 0, -2);
            $m_header->jumlah_termin        = (int) $jumlahTermin; 
            $m_header->save();

            foreach ($insertedDetails as $detail) {
                $m_detail = new \Model\Storage\PembiayaanTempoDetailAset_model();
                $m_detail->kode_pembiayaan = $detail['kode_pembiayaan'];
                $m_detail->termin_ke       = $detail['termin_ke'];
                $m_detail->jatuh_tempo     = $detail['jatuh_tempo'];
                $m_detail->nominal         = $detail['nominal'];
                $m_detail->status          = $detail['status'];
                $m_detail->save();
            }

            $this->result['status'] = 1;
            $this->result['total_data'] = $jumlahTermin + 1;
            $this->result['total_header'] = 1;
            $this->result['total_detail'] = $jumlahTermin;
            $this->result['message'] = $existingHeader ? 'Rencana tempo berhasil diperbarui.' : 'Rencana tempo berhasil disimpan.';

        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal menyimpan rencana tempo: ' . $e->getMessage();
        }

        display_json($this->result);
    }

    public function get_detail_cicilan()
    {
    
        if (empty($this->hakAkses['a_edit'])) {
            echo '<div class="alert alert-danger">Anda tidak memiliki akses untuk melihat detail cicilan.</div>';
            return;
        }
        
        $params = $this->input->post('params');
        $params = is_array($params) ? $params : [];
        $kodePembiayaan = trim((string) ($params['kode_pembiayaan'] ?? ''));

        if ($kodePembiayaan === '') {
            echo '<div class="alert alert-danger">Kode pembiayaan tidak valid.</div>';
            return;
        }

        try {
            $m_conf = new \Model\Storage\Conf();
            
            $safeKode   = $this->getConnection()->getPdo()->quote($kodePembiayaan);
            
            $sql_header = "SELECT id, kode_pembiayaan, tenor_bulan, bunga_flat_persen, pokok_hutang, total_bunga, total_ar, angsuran_per_bulan, selisih_pembulatan 
                           FROM pembiayaan_cicilan_aset 
                           WHERE kode_pembiayaan = $safeKode";
            $data_header = $m_conf->hydrateRaw($sql_header)->toArray();
            
            if (empty($data_header)) {
                echo '<div class="alert alert-warning">Data detail cicilan tidak ditemukan.</div>';
                return;
            }

            $sql_detail = "SELECT kode_cicilan_pembiayaan, jenis_cicilan, angsuran_ke, CONVERT(varchar(10), jatuh_tempo, 23) as jatuh_tempo, nominal, status 
                           FROM pembiayaan_cicilan_detail_aset 
                           WHERE kode_pembiayaan = $safeKode
                           ORDER BY angsuran_ke ASC";
            $data_detail = $m_conf->hydrateRaw($sql_detail)->toArray();


            $sql_aset = "select ma.nilai_perolehan from ms_aset ma 
            inner join pembiayaan_aset pa on ma.kode_aset = pa.kode_aset 
            where pa.kode_pembiayaan = $safeKode";

            $data_aset = $m_conf->hydrateRaw($sql_aset)->toArray();  
            // cetak_r($data_aset, 1); 

            $data = [
                'harga_beli'    => $data_aset[0]['nilai_perolehan'],
                'header'        => $data_header[0], 
                'detail'        => $data_detail
            ];


            echo $this->load->view('aset/pembiayaan_aset/v_edit_detail_cicilan', $data, true);

        } catch (\Exception $e) {
            echo '<div class="alert alert-danger">Gagal mengambil data: ' . $e->getMessage() . '</div>';
        }
    }

    public function update_detail_cicilan()
    {
        if (empty($this->hakAkses['a_edit'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk mengubah detail cicilan.';
            display_json($this->result);
            return;
        }

        $params = $this->input->post('params');
        $params = is_array($params) ? $params : [];
        $kodePembiayaan = trim((string) ($params['kode_pembiayaan'] ?? ''));
        $tenor = filter_var($params['tenor_bulan'] ?? null, FILTER_VALIDATE_INT);
        $bunga = trim((string) ($params['bunga_flat_persen'] ?? ''));

        try {
            if ($kodePembiayaan === '' || $tenor === false || $tenor < 1 || $tenor > 600) {
                $this->result['message'] = 'Kode pembiayaan dan tenor cicilan harus valid.';
            } elseif (!preg_match('/^\d{1,3}(\.\d{1,2})?$/', $bunga) || (float) $bunga > 100) {
                $this->result['message'] = 'Bunga flat harus di antara 0 dan 100 persen.';
            } else {
                $pembiayaan = \Model\Storage\PembiayaanAset_model::where('kode_pembiayaan', $kodePembiayaan)->first();
                $aset = $pembiayaan ? $this->getAset($pembiayaan->kode_aset) : null;
                
                if (!$pembiayaan) {
                    $this->result['message'] = 'Data pembiayaan tidak ditemukan.';
                } else {
                    $connection = $this->getConnection();
                    
                    $stored = $connection->transaction(function () use ($connection, $kodePembiayaan, $pembiayaan, $tenor, $bunga) {
                        
                        $connection->table('pembiayaan_cicilan_detail_aset')->where('kode_pembiayaan', $kodePembiayaan)->delete();
                        $connection->table('pembiayaan_cicilan_aset')->where('kode_pembiayaan', $kodePembiayaan)->delete();

                        $asetRows = $connection->table('ms_aset')
                            ->where('kode_aset', $pembiayaan->kode_aset)
                            ->get();
                        $asetRow = !empty($asetRows) ? (array) reset($asetRows) : null;
                        if (!$asetRow || !array_key_exists('nilai_perolehan', $asetRow)) {
                            throw new \RuntimeException('Harga beli aset tidak ditemukan.');
                        }

                        $priceCents = $this->moneyToCents($asetRow['nilai_perolehan']);
                        if ($priceCents === null) {
                            throw new \RuntimeException('Harga beli aset tidak valid.');
                        }

                        $tdpRows = $connection->table('pembiayaan_tdp_aset')
                            ->where('kode_pembiayaan', $kodePembiayaan)
                            ->where('is_pengurang_pokok', 1)
                            ->get();
                        $tdpCents = 0;
                        foreach ($tdpRows as $tdpRow) {
                            $tdpCents += $this->moneyToCents(((array) $tdpRow)['nominal'] ?? null);
                        }

                        $dpRows = $connection->table('pembiayaan_dp_aset')
                            ->selectRaw('jenis_komponen, angsuran_ke, nominal, CONVERT(varchar(10), jatuh_tempo, 23) as jatuh_tempo')
                            ->where('kode_pembiayaan', $kodePembiayaan)
                            ->get();
                        $dpUangMukaCents = 0;
                        $dpInstallments = [];
                        $dpFirstDueDate = null;
                        foreach ($dpRows as $dpRow) {
                            $dpRow = (array) $dpRow;
                            $component = strtoupper(trim((string) ($dpRow['jenis_komponen'] ?? '')));
                            if ($component === 'UANG_MUKA') {
                                $dpUangMukaCents += $this->moneyToCents($dpRow['nominal'] ?? null);
                            } elseif ($component === 'ANGSURAN') {
                                $installmentNumber = filter_var($dpRow['angsuran_ke'] ?? null, FILTER_VALIDATE_INT);
                                if ($installmentNumber !== false && $installmentNumber > 0) {
                                    $dpInstallments[$installmentNumber] = true;
                                    if ($installmentNumber === 1 && !empty($dpRow['jatuh_tempo'])) {
                                        $dpFirstDueDate = (string) $dpRow['jatuh_tempo'];
                                    }
                                }
                            }
                        }

                        $principalCents = $priceCents - $tdpCents - $dpUangMukaCents;
                        if ($principalCents < 0) {
                            throw new \RuntimeException('Total TDP dan DP uang muka melebihi harga beli aset.');
                        }

                        $rateParts = array_pad(explode('.', $bunga, 2), 2, '0');
                        $rateBasisPoints = ((int) $rateParts[0] * 100) + (int) str_pad($rateParts[1], 2, '0');
                        $totalInterestCents = (int) round(
                            $principalCents * $rateBasisPoints * $tenor / 120000,
                            0,
                            PHP_ROUND_HALF_UP
                        );
                        $totalArCents = $principalCents + $totalInterestCents;
                        $monthlyCents = (int) (ceil($totalArCents / $tenor / 10000) * 10000);
                        $roundingDifferenceCents = ($monthlyCents * $tenor) - $totalArCents;
                        $lastInstallmentCents = $monthlyCents - $roundingDifferenceCents;
                        if ($lastInstallmentCents < 0) {
                            throw new \RuntimeException('Hasil pembulatan menghasilkan nominal cicilan terakhir yang tidak valid.');
                        }

                        $fallbackRows = $connection->table('pembiayaan_aset')
                            ->selectRaw('CONVERT(varchar(10), tgl_pembiayaan, 23) as tgl_pembiayaan')
                            ->where('kode_pembiayaan', $kodePembiayaan)
                            ->get();
                        $fallbackRow = !empty($fallbackRows) ? (array) reset($fallbackRows) : [];
                        $baseDueDate = $dpFirstDueDate ?: (string) ($fallbackRow['tgl_pembiayaan'] ?? '');
                        if (!$this->isValidSqlDate($baseDueDate)) {
                            throw new \RuntimeException('Tanggal dasar cicilan tidak ditemukan atau tidak valid.');
                        }

                        $connection->table('pembiayaan_cicilan_aset')->insert([
                            'kode_pembiayaan' => $kodePembiayaan,
                            'tenor_bulan' => $tenor,
                            'bunga_flat_persen' => $bunga,
                            'pokok_hutang' => $this->centsToMoney($principalCents),
                            'total_bunga' => $this->centsToMoney($totalInterestCents),
                            'total_ar' => $this->centsToMoney($totalArCents),
                            'angsuran_per_bulan' => $this->centsToMoney($monthlyCents),
                            'selisih_pembulatan' => $this->centsToMoney($roundingDifferenceCents),
                        ]);

                        for ($installmentNumber = 1; $installmentNumber <= $tenor; $installmentNumber++) {
                            $monthOffset    = $dpFirstDueDate ? $installmentNumber - 1 : $installmentNumber;
                            $dueDate        = $this->addMonthsClamped($baseDueDate, $monthOffset);
                            $nominalCents   = $installmentNumber === $tenor ? $lastInstallmentCents : $monthlyCents;
                            $connection->table('pembiayaan_cicilan_detail_aset')->insert([
                                'kode_pembiayaan'           => $kodePembiayaan,
                                'kode_cicilan_pembiayaan'   => $kodePembiayaan . '-' . str_pad((string) (int) $installmentNumber, 3, '0', STR_PAD_LEFT),
                                'angsuran_ke'               => $installmentNumber,
                                'jatuh_tempo'               => $dueDate,
                                'nominal'                   => $this->centsToMoney($nominalCents),
                                'jenis_cicilan'             => isset($dpInstallments[$installmentNumber]) ? 'DP' : 'Cicilan',
                                'status'                    => 0,
                            ]);
                        }

                        return [
                            'detail_count' => $tenor,
                            'total_count' => $tenor + 1,
                        ];
                    });

                    $this->result['status'] = 1;
                    $this->result['total_data'] = $stored['total_count'];
                    $this->result['total_header'] = 1;
                    $this->result['total_detail'] = $stored['detail_count'];
                    $this->result['message'] = 'Detail cicilan berhasil diperbarui (' . $stored['detail_count'] . ' angsuran).';
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal memperbarui detail cicilan: ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal memperbarui detail cicilan: ' . $e->getMessage();
        }

        display_json($this->result);
    }


    public function get_detail_tempo()
    {
        if (empty($this->hakAkses['a_edit'])) {
            echo '<div class="alert alert-danger">Akses ditolak.</div>';
            return;
        }

        $params = $this->input->post('params');
        
        $kode = trim($params['kode_pembiayaan'] ?? '');
        if (!$kode) {
            echo '<div class="alert alert-danger">Kode pembiayaan tidak valid.</div>';
            return;
        }

        try {
        
            $m_conf = new \Model\Storage\Conf();
            $safeKode   = $this->getConnection()->getPdo()->quote($kode);

            $sql_header = "select kode_pembiayaan, harga_beli_aset, biaya_lain, total_pembelian, dikurangi_tanda_jadi, sisa_dijadwalkan, jumlah_termin from pembiayaan_tempo_rencana_aset WHERE kode_pembiayaan = '$kode'";
            $data_header = $m_conf->hydrateRaw($sql_header)->toArray();
            $header = $data_header;
            if (!$header) {
                echo '<div class="alert alert-warning">Data tempo tidak ditemukan.</div>';
                return;
            }

           

            $sql_detail = "select kode_pembiayaan, termin_ke, jatuh_tempo, nominal, status from pembiayaan_tempo_detail_aset WHERE kode_pembiayaan = '$kode'";
            $data_detail = $m_conf->hydrateRaw($sql_detail)->toArray();
            $detail = $data_detail;

            
            $tdpTotal = \Model\Storage\PembiayaanTdpAset_model::where('kode_pembiayaan', $kode)
                ->where('is_pengurang_pokok', 1)
                ->sum('nominal');

            
            $pembiayaan = \Model\Storage\PembiayaanAset_model::where('kode_pembiayaan', $kode)->first();

            
            $hargaBeli = 0;
            $supplierNama = '-';

            if ($pembiayaan) {
            
                $aset = \Model\Storage\MsAset_model::where('kode_aset', $pembiayaan->kode_aset)->first();

                if ($aset) {
                    $hargaBeli = (float) $aset->nilai_perolehan;
                }
         
                if (!empty($pembiayaan->id_supplier)) {
                    $supplier = \Model\Storage\Supplier_model::where('id', $pembiayaan->id_supplier)->first();
                    if ($supplier) {
                        $supplierNama = ($supplier->nama ?? '') . ' - ' . ($supplier->nomor ?? '');
                    }
                }
            }

            $data = [
                'header'        => $header[0],
                'detail'        => $detail,
                'tdp_total'     => (float) $tdpTotal,
                'harga_beli'    => $hargaBeli,
                'supplier_nama' => $supplierNama
            ];


            echo $this->load->view('aset/pembiayaan_aset/v_edit_detail_tempo', $data, true);
        } catch (\Exception $e) {
            echo '<div class="alert alert-danger">Gagal: ' . $e->getMessage() . '</div>';
        }
    }

        public function update_rencana_tempo()
    {
        if (empty($this->hakAkses['a_edit'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk mengubah rencana tempo.';
            display_json($this->result);
            return;
        }

        $params = $this->input->post('params');
        $params = is_array($params) ? $params : [];

        try {
            $kodePembiayaan = trim((string) ($params['kode_pembiayaan'] ?? ''));
            $hargaBeli      = trim((string) ($params['harga_beli'] ?? ''));
            $biayaLain      = trim((string) ($params['biaya_lain'] ?? '0'));
            $jumlahTermin   = filter_var($params['jumlah_termin'] ?? null, FILTER_VALIDATE_INT);
            $rows           = $params['rows'] ?? null;

            if ($kodePembiayaan === '') {
                $this->result['message'] = 'Kode pembiayaan tidak valid.';
            } elseif ($jumlahTermin === false || $jumlahTermin < 1 || $jumlahTermin > 60) {
                $this->result['message'] = 'Jumlah termin harus antara 1 dan 60.';
            } elseif (!is_array($rows) || count($rows) !== $jumlahTermin) {
                $this->result['message'] = 'Jumlah baris termin tidak sesuai dengan jumlah termin yang diminta.';
            } elseif (!preg_match('/^\d{1,13}(\.\d{1,2})?$/', $hargaBeli)) {
                $this->result['message'] = 'Harga beli tidak valid.';
            } elseif (!preg_match('/^\d{1,13}(\.\d{1,2})?$/', $biayaLain)) {
                $this->result['message'] = 'Biaya lain tidak valid.';
            } else {
                $pembiayaan = \Model\Storage\PembiayaanAset_model::where('kode_pembiayaan', $kodePembiayaan)->first();
                $aset = $pembiayaan ? $this->getAset($pembiayaan->kode_aset) : null;

                if (!$pembiayaan || !$aset) {
                    $this->result['message'] = 'Data pembiayaan atau aset tidak ditemukan.';
                } else {
                    $connection = $this->getConnection();
                    $updated = $connection->transaction(function () use ($connection, $kodePembiayaan, $hargaBeli, $biayaLain, $jumlahTermin, $rows) {
                    
                        $existingHeader = $connection->table('pembiayaan_tempo_rencana_aset')
                            ->where('kode_pembiayaan', $kodePembiayaan)
                            ->first();
                        
                        if (!$existingHeader) {
                            throw new \RuntimeException('Rencana tempo untuk pembiayaan ini belum pernah disimpan. Gunakan tombol Simpan, bukan Update.');
                        }

                        $hargaCents = $this->moneyToCents($hargaBeli);
                        $biayaCents = $this->moneyToCents($biayaLain);
                        $totalPembelianCents = $hargaCents + $biayaCents;

                        $tdpRows = $connection->table('pembiayaan_tdp_aset')
                            ->where('kode_pembiayaan', $kodePembiayaan)
                            ->where('is_pengurang_pokok', 1)
                            ->get();
                        $tdpCents = 0;
                        foreach ($tdpRows as $tdpRow) {
                            $tdpCents += $this->moneyToCents(((array) $tdpRow)['nominal'] ?? '0');
                        }

                        $sisaDijadwalkanCents = $totalPembelianCents - $tdpCents;
                        if ($sisaDijadwalkanCents < 0) {
                            throw new \RuntimeException('Total TDP melebihi total pembelian.');
                        }

                        $totalTerminCents = 0;
                        $insertedDetails = [];
                        $seenTerminKe = [];

                        foreach ($rows as $index => $row) {
                            if (!is_array($row)) {
                                throw new \RuntimeException('Baris termin ke-' . ($index + 1) . ' tidak valid.');
                            }

                            $terminKe = $index + 1;
                            $jatuhTempo = trim((string) ($row['jatuh_tempo'] ?? ''));
                            $nominal = trim((string) ($row['nominal'] ?? ''));

                            if (!$this->isValidSqlDate($jatuhTempo)) {
                                throw new \RuntimeException('Tanggal jatuh tempo termin ke-' . $terminKe . ' tidak valid.');
                            }
                            if (!preg_match('/^\d{1,13}(\.\d{1,2})?$/', $nominal)) {
                                throw new \RuntimeException('Nominal termin ke-' . $terminKe . ' tidak valid.');
                            }

                            $nominalCents = $this->moneyToCents($nominal);
                            if ($nominalCents <= 0) {
                                throw new \RuntimeException('Nominal termin ke-' . $terminKe . ' harus lebih dari 0.');
                            }

                            if (isset($seenTerminKe[$jatuhTempo])) {
                                throw new \RuntimeException('Tanggal jatuh tempo termin ke-' . $terminKe . ' duplikat dengan termin ke-' . $seenTerminKe[$jatuhTempo] . '.');
                            }
                            $seenTerminKe[$jatuhTempo] = $terminKe;

                            $totalTerminCents += $nominalCents;
                            $insertedDetails[] = [
                                'kode_pembiayaan' => $kodePembiayaan,
                                'termin_ke' => $terminKe,
                                'jatuh_tempo' => $jatuhTempo,
                                'nominal' => (int) substr($nominalCents, 0, -2),
                                'status' => 'Belum',
                            ];
                        }

                        if ($totalTerminCents !== $sisaDijadwalkanCents) {
                            throw new \RuntimeException(
                                'Total nominal termin (' . $this->centsToMoney($totalTerminCents) . ') tidak sama dengan sisa dijadwalkan (' . $this->centsToMoney($sisaDijadwalkanCents) . ').'
                            );
                        }

                        $connection->table('pembiayaan_tempo_detail_aset')
                            ->where('kode_pembiayaan', $kodePembiayaan)
                            ->delete();
                        
                        $connection->table('pembiayaan_tempo_rencana_aset')
                            ->where('kode_pembiayaan', $kodePembiayaan)
                            ->delete();

                        $connection->table('pembiayaan_tempo_rencana_aset')->insert([
                            'kode_pembiayaan'       => $kodePembiayaan,
                            'harga_beli_aset'       => (int) substr($hargaCents, 0, -2),
                            'biaya_lain'            => (int) substr($biayaCents, 0, -2),
                            'total_pembelian'       => (int) substr($totalPembelianCents, 0, -2),
                            'dikurangi_tanda_jadi'  => (int) substr($tdpCents, 0, -2),
                            'sisa_dijadwalkan'      => (int) substr($sisaDijadwalkanCents, 0, -2),
                            'jumlah_termin'         => $jumlahTermin,
                        ]);

                        $connection->table('pembiayaan_tempo_detail_aset')->insert($insertedDetails);

                        return [
                            'detail_count' => $jumlahTermin,
                            'total_count' => $jumlahTermin + 1,
                        ];
                    });

                    $this->result['status'] = 1;
                    $this->result['total_data'] = $updated['total_count'];
                    $this->result['total_header'] = 1;
                    $this->result['total_detail'] = $updated['detail_count'];
                    $this->result['message'] = 'Rencana tempo berhasil diperbarui. ' . $updated['detail_count'] . ' termin dijadwalkan.';
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal memperbarui rencana tempo: ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal memperbarui rencana tempo: ' . $e->getMessage();
        }

        display_json($this->result);
    }


    public function get_modal_add_tdp()
    {

        echo $this->load->view('aset/pembiayaan_aset/v_add_tdp', [], true);
    }




    public function test()
    {
            $sisaPokok = 197440000; 
            $bunga = 2.25;
            $monthlyRate = ($bunga / 100) / 12;
            $tenor = 35;
            $cicilanPerBulan =  5854700;


            for ($i = 1; $i <= $tenor; $i++) {
                // $monthOffset = $hasDpAngsuran1 ? ($i - 1) : $i;
                // $dueDate = $this->addMonthsClamped($baseDueDate, $monthOffset);

                $pokokBln = 0;
                $bungaBln = 0;

                if ($i === 1) {
                    // Bulan 1: Bunga = 0, Cicilan masuk pokok semua
                    $pokokBln = $cicilanPerBulan;
                    $bungaBln = (int) round($sisaPokok * $monthlyRate);
                } elseif ($i === $tenor) {
                    // Bulan Terakhir: Pokok = Sisa Pokok, Bunga = Sisa Bunga
                    $pokokBln = $sisaPokok;
                    $bungaBln = $totalBunga - $totalBungaTerakumulasi;
                } else {
                    // Bulan Tengah: Bunga dihitung dari SISA POKOK HUTANG
                    $bungaBln = (int) round($sisaPokok * $monthlyRate);
                    $pokokBln = $cicilanPerBulan - $bungaBln;
                }

                $totalCicilan = $pokokBln + $bungaBln;
                
                // Kurangi sisa pokok (Bukan sisa AR!)
                $sisaPokok -= $pokokBln;
                if ($sisaPokok < 0) $sisaPokok = 0;
                
                // $totalBungaTerakumulasi += $bungaBln;

                // Tentukan jenis & status
                // $jenisCicilan = 'Cicilan';
                // $statusCicilan = 0;
                
                // if ($i === 1 && $hasDpAngsuran1) {
                //     $jenisCicilan = 'DP';
                //     $statusCicilan = 1;
                // }

                $d_ar = array([
                    'angsuran_ke'             => $i,
                    'pokok'                   => $pokokBln,
                    'bunga'                   => $bungaBln,
                    'nominal'                 => $totalCicilan,
                ]);
                cetak_r($d_ar);
            }


            // return ['detail_count' => $tenor];
        
    }

}
