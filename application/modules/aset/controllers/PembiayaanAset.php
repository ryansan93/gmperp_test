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

    public function ProsesLeasing()
    {
        if (empty($this->hakAkses['a_view'])) {
            showErrorAkses();
            return;
        }

        $id = filter_var($this->input->get('id'), FILTER_VALIDATE_INT);
        $pembiayaan = $id ? \Model\Storage\PembiayaanAset_model::find($id) : null;
        if (!$pembiayaan) {
            show_404();
            return;
        }

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



        $tdpRows = $this->getConnection()
            ->table('pembiayaan_tdp_aset')
            ->selectRaw('id, kode_pembiayaan, CONVERT(varchar(10), tanggal, 23) as tanggal, deskripsi, nominal, is_pengurang_pokok')
            ->where('kode_pembiayaan', $pembiayaan->kode_pembiayaan)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        
        $tdp = [];
        $tdp_pengurang_pokok_total = 0; 
        foreach ($tdpRows as $row) {
            $tdp[] = (array) $row;
            if (!empty($row->is_pengurang_pokok) && $row->is_pengurang_pokok == 1) {
                $tdp_pengurang_pokok_total += floatval($row->nominal);
            }
        }
        
        $tdpLocked = $this->isPembiayaanLocked($pembiayaan);
        
        $dpData = $this->getDpData($pembiayaan->kode_pembiayaan);

        $biaya_penambah_total = 0;
        if (!empty($dpData['rows']) && is_array($dpData['rows'])) {
            foreach ($dpData['rows'] as $dp) {
                if (!empty($dp['is_hutang']) && $dp['is_hutang'] == 1) {
                    $biaya_penambah_total += floatval($dp['nominal']);
                }
            }
        }

        $harga_beli = floatval($aset['nilai_perolehan'] ?? 0);
        $uang_muka = floatval($dpData['uang_muka_total'] ?? 0);
        
        $nilai_pembiayaan = $harga_beli - $uang_muka;
        $pokok_hutang = $nilai_pembiayaan + $biaya_penambah_total - $tdp_pengurang_pokok_total;

        $cicilanHeaderRows = $this->getConnection()->table('pembiayaan_cicilan_aset')
            ->where('kode_pembiayaan', $pembiayaan->kode_pembiayaan)
            ->get();
        $cicilan = !empty($cicilanHeaderRows) ? (array) reset($cicilanHeaderRows) : null;
        
        $cicilanDetail = [];
        if ($cicilan) {
            $cicilanDetailRows = $this->getConnection()->table('pembiayaan_cicilan_detail_aset')
                ->selectRaw('kode_pembiayaan, bunga, pokok, sisa_pokok_hutang, jenis_cicilan, angsuran_ke, CONVERT(varchar(10), jatuh_tempo, 23) as jatuh_tempo, nominal, status')
                ->where('kode_pembiayaan', $pembiayaan->kode_pembiayaan)
                ->orderBy('angsuran_ke', 'asc')
                ->get();
            foreach ($cicilanDetailRows as $row) {
                $cicilanDetail[] = (array) $row;
            }
        }

        $biaya_prepaid = 0;
        foreach($dpData['rows'] as $dp){
            if($dp['jenis_komponen'] != 'UANG_MUKA' && $dp['angsuran_ke'] == null){
                $biaya_prepaid += $dp['nominal'];
            }
        }
     
        $content = [
            'pembiayaan'                => $pembiayaan->toArray(),
            'aset'                      => $aset->toArray(),
            'kode_jenis_pembiayaan'     => $aset->kode_pembiayaan,
            'jenis_pembiayaan'          => $jenisPembiayaan->toArray(),
            'can_submit_tdp'            => !empty($this->hakAkses['a_submit']),
            'can_edit_tdp'              => !empty($this->hakAkses['a_edit']),
            'can_delete_tdp'            => !empty($this->hakAkses['a_delete']),
            'tdp_locked'                => $tdpLocked,
            'tdp'                       => $tdp,
            'dp'                        => $dpData['rows'] ?? [],
            'dp_total'                  => $dpData['total'] ?? 0,
            'dp_uang_muka_total'        => $dpData['uang_muka_total'] ?? 0,
            
            'biaya_prepaid'             => $biaya_prepaid,
            
            'can_submit_dp'             => !empty($this->hakAkses['a_submit']),
            'can_edit_dp'               => !empty($this->hakAkses['a_edit']),
            'can_delete_dp'             => !empty($this->hakAkses['a_delete']),
            'dp_locked'                 => $tdpLocked,
            'cicilan'                   => $cicilan,
            'cicilan_detail'            => $cicilanDetail,
            'supplier'                  => $pembiayaan->id_supplier ? \Model\Storage\Supplier_model::find($pembiayaan->id_supplier) : null,
            'leasing'                   => $pembiayaan->id_leasing ? \Model\Storage\Supplier_model::find($pembiayaan->id_leasing) : null,
            'tanggal_dasar_cicilan'     => $cicilan ? ($cicilan['tanggal_dasar'] ?? date('Y-m-d')) : date('Y-m-d'), // Sesuaikan jika ada field tanggal
            'tanggal_pembiayaan'        => $pembiayaan->tanggal_pembiayaan ?? date('Y-m-d'), // Sesuaikan nama field
            'cicilan_tersimpan'         => !empty($cicilan),
        ];

        $view = 'v_proses_leasing';

        $this->add_external_js([
            'assets/aset/pembiayaan_aset/js/pembiayaan_aset.js',
        ]);
        $this->add_external_css([
            'assets/aset/pembiayaan_aset/css/pembiayaan_aset.css',
        ]);

        $data = $this->includes;
        $content['title_panel'] = 'Proses Pembiayaan Aset';
        $data['title_menu'] = 'Proses Pembiayaan Aset';
        $data['view'] = $this->load->view($this->pathView . $view, $content, true);
        $this->load->view($this->template, $data);
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

        // cetak_r($params, 1);

        try {
            $kodePembiayaan = trim((string) ($params['kode_pembiayaan'] ?? ''));
            $tanggal        = trim((string) ($params['tanggal'] ?? ''));
            $rows           = $params['rows'] ?? null;
            $pengurangPokok = filter_var($params['is_pengurang_pokok'] ?? null, FILTER_VALIDATE_INT);
            $date           = \DateTime::createFromFormat('!Y-m-d', $tanggal);
            $dateErrors     = \DateTime::getLastErrors();
            $tanggalValid   = $date && $date->format('Y-m-d') === $tanggal && (!$dateErrors || ($dateErrors['warning_count'] === 0 && $dateErrors['error_count'] === 0));
            $pembiayaan     = \Model\Storage\PembiayaanAset_model::where('kode_pembiayaan', $kodePembiayaan)->first();
            $aset           = $pembiayaan ? $this->getAset($pembiayaan->kode_aset) : null;

            if (!$pembiayaan) {
                $this->result['message'] = 'Data pembiayaan tidak ditemukan atau tidak mendukung TDP.';
            } elseif ($this->isPembiayaanLocked($pembiayaan)) {
                $this->result['message'] = 'TDP tidak dapat ditambah karena aset sudah masuk tahap Penerimaan Aset.';
            } 
            elseif (strcasecmp(trim((string) $aset->nama_pembiayaan), 'Cash') === 0
                && $this->getConnection()->table('pembiayaan_pelunasan_aset')
                    ->where('kode_pembiayaan', $kodePembiayaan)
                    ->exists()) {
                $this->result['message'] = 'TDP tidak dapat ditambah setelah pelunasan disimpan.';
            } elseif (!$tanggalValid) {
                $this->result['message'] = 'Tanggal TDP wajib diisi dengan tanggal yang valid.';
            } elseif (!is_array($rows) || count($rows) === 0) {
                $this->result['message'] = 'Tambahkan minimal satu baris TDP.';
            } elseif (!in_array($pengurangPokok, [0, 1], true)) {
                $this->result['message'] = 'Pilihan pengurang pokok tidak valid.';
            } else {
                $validRows = [];
                foreach ($rows as $index => $row) {
                    if (!is_array($row)) {
                        $this->result['message'] = 'Baris TDP ke-' . ($index + 1) . ' tidak valid.';
                        break;
                    }

                    $deskripsi = trim((string) ($row['deskripsi'] ?? ''));
                    $nominal = trim((string) ($row['nominal'] ?? ''));
                    $deskripsiLength = function_exists('mb_strlen')
                        ? mb_strlen($deskripsi, 'UTF-8')
                        : strlen($deskripsi);
                    if ($deskripsiLength > 255) {
                        $this->result['message'] = 'Deskripsi TDP maksimal 255 karakter (baris ' . ($index + 1) . ').';
                        break;
                    }
                    if (!preg_match('/^\d{1,13}(\.\d{1,2})?$/', $nominal) || (float) $nominal <= 0) {
                        $this->result['message'] = 'Nominal TDP harus lebih dari 0 dan maksimal 13 digit (baris ' . ($index + 1) . ').';
                        break;
                    }

                    $validRows[] = [
                        'deskripsi' => $deskripsi !== '' ? $deskripsi : null,
                        'nominal' => $nominal,
                    ];
                }

                if (count($validRows) === count($rows)) {
                    $this->getConnection()->transaction(function () use ($kodePembiayaan, $tanggal, $validRows, $pengurangPokok, $aset) {
                        if (strcasecmp(trim((string) $aset->nama_pembiayaan), 'Cash') === 0
                            && $this->getConnection()->table('pembiayaan_pelunasan_aset')
                                ->where('kode_pembiayaan', $kodePembiayaan)
                                ->exists()) {
                            throw new \RuntimeException('TDP tidak dapat ditambah setelah pelunasan disimpan.');
                        }

                        foreach ($validRows as $row) {
                            $model = new \Model\Storage\PembiayaanTdpAset_model();
                            $model->kode_pembiayaan = $kodePembiayaan;
                            $model->tanggal = $tanggal;
                            $model->deskripsi = $row['deskripsi'];
                            $model->nominal = $row['nominal'];
                            $model->is_pengurang_pokok = $pengurangPokok;
                            $model->save();
                        }
                    });

                    $this->result['status'] = 1;
                    $this->result['message'] = count($validRows) . ' baris TDP berhasil disimpan.';
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal menyimpan TDP: ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal menyimpan TDP: ' . $e->getMessage();
        }

        display_json($this->result);
    }

    public function edit_tdp()
    {
        if (empty($this->hakAkses['a_edit'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk mengubah TDP.';
            display_json($this->result);
            return;
        }

        $params = $this->input->post('params');
        $params = is_array($params) ? $params : [];

        try {
            $context = $this->getTdpMutationContext($params['id'] ?? null);
            
            if (!$context) {
                $this->result['message'] = 'Data TDP tidak ditemukan.';
            } else {
                $tanggal        = trim((string) ($params['tanggal'] ?? ''));
                $date           = \DateTime::createFromFormat('!Y-m-d', $tanggal);
                $dateErrors     = \DateTime::getLastErrors();
                $tanggalValid   = $date && $date->format('Y-m-d') === $tanggal && (!$dateErrors || ($dateErrors['warning_count'] === 0 && $dateErrors['error_count'] === 0));
                
                $deskripsi      = trim((string) ($params['deskripsi'] ?? ''));
                $deskripsiLength = function_exists('mb_strlen') ? mb_strlen($deskripsi, 'UTF-8') : strlen($deskripsi);
                
                $nominal        = trim((string) ($params['nominal'] ?? ''));
                $pengurangPokok = filter_var($params['is_pengurang_pokok'] ?? null, FILTER_VALIDATE_INT);

                if (!$tanggalValid) {
                    $this->result['message'] = 'Tanggal TDP tidak valid.';
                } elseif ($deskripsiLength > 255) {
                    $this->result['message'] = 'Deskripsi TDP maksimal 255 karakter.';
                } elseif (!preg_match('/^\d{1,13}(\.\d{1,2})?$/', str_replace(',', '.', $nominal)) || (float) $nominal <= 0) {
                    $this->result['message'] = 'Nominal TDP harus lebih dari 0 dan maksimal 13 digit.';
                } elseif (!in_array($pengurangPokok, [0, 1], true)) {
                    $this->result['message'] = 'Pilihan pengurang pokok tidak valid.';
                } else {
                    $connection = $this->getConnection();
                    $kodePembiayaan = $context['tdp']['kode_pembiayaan'];

                    $m_pembiayaan = \Model\Storage\PembiayaanAset_model::select('ms_aset.kode_pembiayaan as jenis_biaya')
                        ->leftJoin('ms_aset', 'ms_aset.kode_aset', '=', 'pembiayaan_aset.kode_aset')
                        ->where('pembiayaan_aset.kode_pembiayaan', $kodePembiayaan)
                        ->first();
                    
                    $jenisBiaya = $m_pembiayaan ? $m_pembiayaan->jenis_biaya : '';

                    $connection->transaction(function () use ($connection, $context, $kodePembiayaan, $jenisBiaya, $tanggal, $deskripsi, $nominal, $pengurangPokok) {
                        
                        // Update data TDP
                        $connection->table('pembiayaan_tdp_aset')
                            ->where('id', $context['tdp']['id'])
                            ->where('kode_pembiayaan', $kodePembiayaan)
                            ->update([
                                'tanggal'            => $tanggal,
                                'deskripsi'          => $deskripsi !== '' ? $deskripsi : null,
                                'nominal'            => (float) $nominal, // Pastikan disimpan sebagai angka
                                'is_pengurang_pokok' => $pengurangPokok,
                                'updated_at'         => date('Y-m-d H:i:s'),
                            ]);

                        $newTotalPengurang = (int) $connection->table('pembiayaan_tdp_aset')
                        ->where('kode_pembiayaan', $kodePembiayaan)
                        ->where('is_pengurang_pokok', 1)
                        ->sum('nominal');

                        $dataUpdate = [
                            'dikurangi_tanda_jadi' => $newTotalPengurang,
                            'updated_at'           => date('Y-m-d H:i:s')
                        ];

                        // 4. Update tabel yang sesuai berdasarkan jenis pembiayaan
                        if ($jenisBiaya === 'PMB01') {
                            \Model\Storage\PembiayaanPelunasanAset_model::where('kode_pembiayaan', $kodePembiayaan)->update($dataUpdate);
                        } elseif ($jenisBiaya === 'PMB02') {
                            \Model\Storage\PembiayaanTempoRencanaAset_model::where('kode_pembiayaan', $kodePembiayaan)->update($dataUpdate);
                        } elseif ($jenisBiaya === 'PMB03') {
                            \Model\Storage\PembiayaanTempoDetailAset_model::where('kode_pembiayaan', $kodePembiayaan)->update($dataUpdate);
                        }
                    });

                    $this->result['status'] = 1;
                    $this->result['message'] = 'Data TDP berhasil diubah dan total diperbarui.';
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal mengubah TDP (Database): ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal mengubah TDP: ' . $e->getMessage();
        }

        display_json($this->result);
    }

    public function delete_tdp()
    {
        if (empty($this->hakAkses['a_delete'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk menghapus TDP.';
            display_json($this->result);
            return;
        }

        $params = $this->input->post('params');
        $id = is_array($params) ? ($params['id'] ?? null) : $params;

        try {
            $context = $this->getTdpMutationContext($id);
            
            if (!$context) {
                $this->result['message'] = 'Data TDP tidak ditemukan.';
            } else {
                $connection = $this->getConnection();
                $kodePembiayaan = $context['tdp']['kode_pembiayaan'];

                $m_pembiayaan = \Model\Storage\PembiayaanAset_model::select('ms_aset.kode_pembiayaan as jenis_biaya')
                    ->leftJoin('ms_aset', 'ms_aset.kode_aset', '=', 'pembiayaan_aset.kode_aset')
                    ->where('pembiayaan_aset.kode_pembiayaan', $kodePembiayaan)
                    ->first();
                
                $jenisBiaya = $m_pembiayaan ? $m_pembiayaan->jenis_biaya : '';

                $connection->transaction(function () use ($connection, $context, $kodePembiayaan, $jenisBiaya) {
                    
                    $deleted = $connection->table('pembiayaan_tdp_aset')
                        ->where('id', $context['tdp']['id'])
                        ->where('kode_pembiayaan', $kodePembiayaan)
                        ->delete();

                    if ($deleted) {
                        
                        $newTotalPengurang = (int) $connection->table('pembiayaan_tdp_aset')
                            ->where('kode_pembiayaan', $kodePembiayaan)
                            ->where('is_pengurang_pokok', 1)
                            ->sum('nominal');

                        $dataUpdate = [
                            'dikurangi_tanda_jadi' => $newTotalPengurang,
                            'updated_at'           => date('Y-m-d H:i:s')
                        ];

                        if ($jenisBiaya === 'PMB01') {
                            \Model\Storage\PembiayaanPelunasanAset_model::where('kode_pembiayaan', $kodePembiayaan)->update($dataUpdate);
                        } elseif ($jenisBiaya === 'PMB02') {
                            \Model\Storage\PembiayaanTempoRencanaAset_model::where('kode_pembiayaan', $kodePembiayaan)->update($dataUpdate);
                        } elseif ($jenisBiaya === 'PMB03') {
                            \Model\Storage\PembiayaanTempoDetailAset_model::where('kode_pembiayaan', $kodePembiayaan)->update($dataUpdate);
                        }
                    } else {
                        throw new \Exception('Data TDP tidak ditemukan atau sudah berubah.');
                    }
                });

                $this->result['status'] = 1;
                $this->result['message'] = 'Data TDP berhasil dihapus dan total diperbarui.';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal menghapus TDP (Database): ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal menghapus TDP: ' . $e->getMessage();
        }

        display_json($this->result);
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
        $params = $this->input->post('params');
        // cetak_r($params, 1);

        $params = is_array($params) ? $params : [];
        $rows = $params['rows'] ?? null;
        $hasExistingRows = false;
        $hasNewRows = false;
        if (is_array($rows)) {
            foreach ($rows as $row) {
                if (is_array($row) && !empty($row['id'])) {
                    $hasExistingRows = true;
                } else {
                    $hasNewRows = true;
                }
            }
        }

        if ($hasExistingRows && empty($this->hakAkses['a_edit'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk mengubah data DP.';
            display_json($this->result);
            return;
        }
        if ($hasNewRows && empty($this->hakAkses['a_submit'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk menyimpan data DP.';
            display_json($this->result);
            return;
        }

        try {
            $kodePembiayaan = trim((string) ($params['kode_pembiayaan'] ?? ''));
            $tanggalDp  = trim((string) ($params['tanggal_dp'] ?? ''));
            $tanggalDp  = $tanggalDp !== '' ? $tanggalDp : null;
            $pembiayaan = \Model\Storage\PembiayaanAset_model::where('kode_pembiayaan', $kodePembiayaan)->first();
            $aset       = $pembiayaan ? $this->getAset($pembiayaan->kode_aset) : null;
            $locked     = $pembiayaan && $this->isPembiayaanLocked($pembiayaan);

            if (!$pembiayaan) {
                $this->result['message'] = 'Data pembiayaan Leasing tidak ditemukan.';
            } elseif ($locked) {
                $this->result['message'] = 'DP tidak dapat diubah karena pembiayaan sudah selesai atau aset sudah masuk tahap Penerimaan Aset.';
            } elseif ($tanggalDp !== null && !$this->isValidSqlDate($tanggalDp)) {
                $this->result['message'] = 'Tanggal DP tidak valid.';
            } elseif (!is_array($rows) || count($rows) === 0) {
                $this->result['message'] = 'Tambahkan minimal satu baris DP.';
            } else {
                $validRows = [];
                foreach ($rows as $index => $row) {
                    $lineNumber = $index + 1;
                    if (!is_array($row)) {
                        $this->result['message'] = 'Baris DP ke-' . $lineNumber . ' tidak valid.';
                        break;
                    }

                    $id         = !empty($row['id']) ? filter_var($row['id'], FILTER_VALIDATE_INT) : null;
                    $jenis      = strtoupper(trim((string) ($row['jenis_komponen'] ?? '')));
                    $deskripsi  = trim((string) ($row['deskripsi'] ?? ''));
                    $nominal    = trim((string) ($row['nominal'] ?? ''));
                    $angsuranKe = null;
                    $jatuhTempo = null;
                    $deskripsiLength = function_exists('mb_strlen')
                        ? mb_strlen($deskripsi, 'UTF-8')
                        : strlen($deskripsi);

                    if ($jenis === '' || strlen($jenis) > 30) {
                        $this->result['message'] = 'Jenis komponen DP wajib diisi dan maksimal 30 karakter (baris ' . $lineNumber . ').';
                        break;
                    }
                    if ($deskripsiLength > 255) {
                        $this->result['message'] = 'Deskripsi DP maksimal 255 karakter (baris ' . $lineNumber . ').';
                        break;
                    }
                    if (!preg_match('/^\d{1,13}(\.\d{1,2})?$/', $nominal) || (float) $nominal <= 0) {
                        $this->result['message'] = 'Nominal DP harus lebih dari 0 dan maksimal 13 digit (baris ' . $lineNumber . ').';
                        break;
                    }

                    if ($jenis === 'ANGSURAN') {
                        $angsuranKe = filter_var($row['angsuran_ke'] ?? null, FILTER_VALIDATE_INT);
                        $jatuhTempo = trim((string) ($row['jatuh_tempo'] ?? ''));
                        if ($angsuranKe === false || $angsuranKe < 1 || !$this->isValidSqlDate($jatuhTempo)) {
                            $this->result['message'] = 'Nomor angsuran dan jatuh tempo wajib valid (baris ' . $lineNumber . ').';
                            break;
                        }
                    }

                    if ($id !== null && (!$id || !\Model\Storage\PembiayaanDpAset_model::where('id', $id)
                        ->where('kode_pembiayaan', $kodePembiayaan)->exists())) {
                        $this->result['message'] = 'Data DP pada baris ' . $lineNumber . ' tidak ditemukan.';
                        break;
                    }

                    $validRows[] = [
                        'id'                => $id,
                        'jenis_komponen'    => $jenis,
                        'deskripsi'         => $deskripsi !== '' ? $deskripsi : null,
                        'nominal'           => $nominal,
                        'angsuran_ke'       => $angsuranKe,
                        'jatuh_tempo'       => $jatuhTempo !== null && $jatuhTempo !== '' ? $jatuhTempo : null,
                    ];
                }

                if (count($validRows) === count($rows)) {
                    $this->getConnection()->transaction(function () use ($kodePembiayaan, $tanggalDp, $validRows) {
                        foreach ($validRows as $row) {
                            $data = [
                                'tanggal_dp'        => $tanggalDp,
                                'jenis_komponen'    => $row['jenis_komponen'],
                                'deskripsi'         => $row['deskripsi'],
                                'nominal'           => $row['nominal'],
                                'angsuran_ke'       => $row['angsuran_ke'],
                                'jatuh_tempo'       => $row['jatuh_tempo'],
                            ];
                            if ($row['id'] !== null) {
                                $data['updated_at'] = date('Y-m-d H:i:s');
                                $this->getConnection()->table('pembiayaan_dp_aset')
                                    ->where('id', $row['id'])
                                    ->where('kode_pembiayaan', $kodePembiayaan)
                                    ->update($data);
                            } else {
                                $data['kode_pembiayaan'] = $kodePembiayaan;
                                $this->getConnection()->table('pembiayaan_dp_aset')->insert($data);
                            }
                        }
                    });

                    $this->result['status'] = 1;
                    $this->result['message'] = count($validRows) . ' baris DP berhasil disimpan.';
                    $this->result['content'] = $this->getDpData($kodePembiayaan);
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal menyimpan DP: ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal menyimpan DP: ' . $e->getMessage();
        }

        display_json($this->result);
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
    
    $kodePembiayaan = trim((string) ($postData['kode_pembiayaan'] ?? $this->input->post('kode_pembiayaan') ?? ''));
    $tenor          = (int) ($postData['tenor'] ?? 0);
    $bunga          = (float) ($postData['bunga'] ?? 0);
    $pokokHutang    = (int) ($postData['pokok_hutang'] ?? 0);
    $totalAr        = (int) ($postData['total_ar'] ?? 0);

    try {
        if ($kodePembiayaan === '' || $tenor < 1 || $tenor > 600) {
            $this->result['message'] = 'Kode pembiayaan dan tenor cicilan harus valid.';
        } elseif ($bunga < 0 || $bunga > 100) {
            $this->result['message'] = 'Bunga flat harus di antara 0 dan 100 persen.';
        } elseif ($pokokHutang <= 0 || $totalAr <= 0) {
            $this->result['message'] = 'Nilai pokok hutang dan Total A/R tidak valid.';
        } else {
            $pembiayaan = \Model\Storage\PembiayaanAset_model::where('kode_pembiayaan', $kodePembiayaan)->first();
            
            if (!$pembiayaan) {
                $this->result['message'] = 'Data pembiayaan Leasing tidak ditemukan.';
            } elseif ($this->isPembiayaanLocked($pembiayaan)) {
                $this->result['message'] = 'Detail cicilan sudah dikunci atau pembiayaan telah selesai.';
            } else {
                $connection = $this->getConnection();
                
                $stored = $connection->transaction(function () use ($connection, $kodePembiayaan, $pembiayaan, $tenor, $bunga, $pokokHutang, $totalAr) {
                    
                    if ($connection->table('pembiayaan_cicilan_aset')->where('kode_pembiayaan', $kodePembiayaan)->exists()) {
                        throw new \RuntimeException('Detail cicilan untuk pembiayaan ini sudah pernah disimpan.');
                    }

                    // 1. Tentukan Tanggal Dasar
                    $baseDueDate = '';
                    $dpRows = $connection->table('pembiayaan_dp_aset')
                        ->selectRaw('CONVERT(varchar(10), jatuh_tempo, 23) as jatuh_tempo')
                        ->where('kode_pembiayaan', $kodePembiayaan)
                        ->where('jenis_komponen', 'ANGSURAN')
                        ->where('angsuran_ke', 1)
                        ->get();
                    $dpRow = !empty($dpRows) ? (array) reset($dpRows) : [];

                    if (!empty($dpRow['jatuh_tempo'])) {
                        $baseDueDate = (string) $dpRow['jatuh_tempo'];
                    } else {
                        $pembiayaanRows = $connection->table('pembiayaan_aset')
                            ->selectRaw('CONVERT(varchar(10), tgl_pembiayaan, 23) as tgl_pembiayaan')
                            ->where('kode_pembiayaan', $kodePembiayaan)
                            ->get();
                        $pembiayaanRow = !empty($pembiayaanRows) ? (array) reset($pembiayaanRows) : [];
                        $baseDueDate = (string) ($pembiayaanRow['tgl_pembiayaan'] ?? '');
                    }

                    if (!$this->isValidSqlDate($baseDueDate)) {
                        throw new \RuntimeException('Tanggal dasar cicilan tidak ditemukan atau tidak valid.');
                    }

                    // 2. Cek apakah ada DP untuk angsuran ke-1
                    $hasDpAngsuran1 = !empty($dpRow);

                    // 3. Hitung Cicilan Tetap dengan Rumus Anuitas (SAMA DENGAN EXCEL)
                    $monthlyRate = ($bunga / 100) / 12;
                    $cicilanTetap = (int) round(
                        $pokokHutang * ($monthlyRate / (1 - pow(1 + $monthlyRate, -$tenor)))
                    );
                    
                    $totalPayment = $cicilanTetap * $tenor;
                    $totalBunga = $totalPayment - $pokokHutang;
                    $selisihPembulatan = $totalPayment - $totalAr;

                    // Simpan Header Cicilan
                    $connection->table('pembiayaan_cicilan_aset')->insert([
                        'kode_pembiayaan'     => $kodePembiayaan,
                        'tenor_bulan'         => $tenor,
                        'bunga_flat_persen'   => number_format($bunga, 2, '.', ''),
                        'pokok_hutang'        => $pokokHutang,
                        'total_bunga'         => $totalBunga,
                        'total_ar'            => $totalAr,
                        'angsuran_per_bulan'  => $cicilanTetap,
                        'selisih_pembulatan'  => $selisihPembulatan,
                    ]);

                    // 4. Looping Perhitungan Amortisasi
                    $sisaPokok = (float) $pokokHutang;
                    $detailCicilan = [];
                    $totalBungaTerakumulasi = 0;

                    for ($i = 1; $i <= $tenor; $i++) {
                        $monthOffset = $i - 1;
                        $dueDate = $this->addMonthsClamped($baseDueDate, $monthOffset);

                        $sisaPokokSebelumnya = $sisaPokok;
                        $pokokBln = 0;
                        $bungaBln = 0;

                        // PERHITUNGAN NORMAL UNTUK SEMUA BULAN (SESUAI EXCEL)
                        if ($i === $tenor) {
                            // Bulan terakhir: sesuaikan agar sisa pokok = 0
                            $pokokBln = (int) round($sisaPokok);
                            $bungaBln = $cicilanTetap - $pokokBln;
                            $totalCicilan = $pokokBln + $bungaBln;
                        } else {
                            // Bulan biasa: Bunga = Sisa Pokok × Bunga Bulanan
                            $bungaBln = (int) round($sisaPokok * $monthlyRate);
                            // Pokok = Cicilan Tetap - Bunga
                            $pokokBln = $cicilanTetap - $bungaBln;
                            $totalCicilan = $cicilanTetap;
                            $totalBungaTerakumulasi += $bungaBln;
                        }

                        // Update sisa pokok
                        $sisaPokok = $sisaPokok - $pokokBln;
                        if ($sisaPokok < 0) {
                            $sisaPokok = 0;
                        }

                        // ===== PERUBAHAN: Tentukan Jenis & Status =====
                        $jenisCicilan = 'Cicilan';
                        $statusCicilan = 0; // 0 = Pending

                        // Jika angsuran ke-1 dan ada DP untuk angsuran ke-1 → tandai sebagai DP & Lunas
                        if ($i === 1 && $hasDpAngsuran1) {
                            $jenisCicilan = 'DP';
                            $statusCicilan = 1; // 1 = Lunas
                        }

                        $detailCicilan[] = [
                            'kode_pembiayaan'         => $kodePembiayaan,
                            'kode_cicilan_pembiayaan' => $kodePembiayaan . '-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                            'angsuran_ke'             => $i,
                            'jatuh_tempo'             => $dueDate,
                            'total_pokok'             => (int) round($sisaPokokSebelumnya),
                            'pokok'                   => $pokokBln,
                            'bunga'                   => $bungaBln,
                            'nominal'                 => $totalCicilan,
                            'sisa_pokok_hutang'       => (int) round($sisaPokok),
                            'jenis_cicilan'           => $jenisCicilan,
                            'status'                  => $statusCicilan,
                        ];
                    }

                    // 5. Batch Insert Detail
                    if (!empty($detailCicilan)) {
                        $connection->table('pembiayaan_cicilan_detail_aset')->insert($detailCicilan);
                    }

                    // 6. Update Status Pembiayaan
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
            // } elseif (!$this->isSupplierValid($idSupplier)) {
            //     $this->result['message'] = 'Supplier wajib dipilih dari daftar supplier aktif.';
            // } elseif (!$this->isSupplierValid($idLeasing)) {
            //     $this->result['message'] = 'Leasing wajib dipilih dari daftar supplier aktif.';
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
                $this->result['status'] = 1;
                $this->result['message'] = 'Data pembiayaan aset berhasil disimpan.';
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




    public function ProsesCash()
    {
        if (empty($this->hakAkses['a_view'])) {
            showErrorAkses();
            return;
        }

        $id = filter_var($this->input->get('id'), FILTER_VALIDATE_INT);
        $pembiayaan = $id ? \Model\Storage\PembiayaanAset_model::find($id) : null;
        if (!$pembiayaan) {
            show_404();
            return;
        }

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

        if (strcasecmp(trim((string) $aset->nama_pembiayaan), 'Cash') !== 0) {
            show_404();
            return;
        }

        $jenisPembiayaan = \Model\Storage\MsPembiayaan_model::where('kode_jenis_pembiayaan', $aset->kode_pembiayaan)->first();
        if (!$jenisPembiayaan) {
            show_404();
            return;
        }

        $tdpRows = $this->getConnection()
            ->table('pembiayaan_tdp_aset')
            ->selectRaw('id, kode_pembiayaan, CONVERT(varchar(10), tanggal, 23) as tanggal, deskripsi, nominal, is_pengurang_pokok')
            ->where('kode_pembiayaan', $pembiayaan->kode_pembiayaan)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        $tdp = [];
        foreach ($tdpRows as $row) {
            $tdp[] = (array) $row;
        }
        $tdpLocked = $this->isPembiayaanLocked($pembiayaan);

        $pelunasanRows = $this->getConnection()
            ->table('pembiayaan_pelunasan_aset')
            ->selectRaw('id, kode_pembiayaan, CONVERT(varchar(10), tanggal_pelunasan, 23) as tanggal_pelunasan, harga_beli_aset, biaya_lain, total_pembelian, dikurangi_tanda_jadi, diajukan')
            ->where('kode_pembiayaan', $pembiayaan->kode_pembiayaan)
            ->get();
        $pelunasan = !empty($pelunasanRows) ? (array) reset($pelunasanRows) : null;
        $pelunasanLocked = $tdpLocked; 

        $pelunasanDetails = [];
        if (!empty($pelunasan)) {
            $pelunasanDetails = \Model\Storage\PembiayaanPelunasanAsetDetail_model::where('kode_pembiayaan', $pelunasan['kode_pembiayaan'])
                ->orderBy('urutan', 'asc')
                ->get()
                ->toArray();
        }

        // cetak_r($pelunasanDetails, 1);


        $content = [
            'pembiayaan'                => $pembiayaan->toArray(),
            'aset'                      => $aset->toArray(),
            'kode_jenis_pembiayaan'     => $aset->kode_pembiayaan,
            'jenis_pembiayaan'          => $jenisPembiayaan->toArray(),
            
            'can_submit_tdp'            => !empty($this->hakAkses['a_submit']),
            'can_edit_tdp'              => !empty($this->hakAkses['a_edit']),
            'can_delete_tdp'            => !empty($this->hakAkses['a_delete']),
            'tdp_locked'                => $tdpLocked,
            'tdp'                       => $tdp,
            
            'can_submit_pelunasan'      => !empty($this->hakAkses['a_submit']),
            'can_edit_pelunasan'        => !empty($this->hakAkses['a_edit']),
            'can_delete_pelunasan'      => !empty($this->hakAkses['a_delete']),
            'pelunasan'                 => $pelunasan,
            'pelunasan_locked'          => $pelunasanLocked,
            'pelunasan_detail'          => $pelunasanDetails,
            
            'supplier'                  => \Model\Storage\Supplier_model::find($pembiayaan->id_supplier),
        ];

        $this->add_external_js([
            'assets/aset/pembiayaan_aset/js/pembiayaan_aset.js',
        ]);
        $this->add_external_css([
            'assets/aset/pembiayaan_aset/css/pembiayaan_aset.css',
        ]);

        $data                   = $this->includes;
        $content['title_panel'] = 'Proses Pembiayaan Aset - Cash';
        $data['title_menu']     = 'Proses Pembiayaan Aset';
        
        $data['view'] = $this->load->view($this->pathView . 'v_proses_cash', $content, true);
        $this->load->view($this->template, $data);
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

            $tandaJadi = 0;
            $tdpRows = \Model\Storage\PembiayaanTdpAset_model::where('kode_pembiayaan', $kodePembiayaan)->get();
            foreach ($tdpRows as $tdp) {
                $tandaJadi += floatval($tdp->nominal ?? 0);
            }



            if ($diajukan < 0) {
                throw new \Exception('Total tanda jadi tidak boleh melebihi total pembelian.');
            }

            $m_header = new \Model\Storage\PembiayaanPelunasanAset_model();
            $m_header->kode_pembiayaan      = $kodePembiayaan;
            $m_header->tanggal_pelunasan    = $tanggalPelunasan;
            $m_header->harga_beli_aset      = $hargaBeli;
            
            $m_header->save(); 

            if (!empty($rows)) {
                $total_pembelian = 0;
                foreach ($rows as $index => $d) {

                    $total_pembelian += $d['nominal'];

                    $m_detail = new \Model\Storage\PembiayaanPelunasanAsetDetail_model();
                    $m_detail->pelunasan_id    = $m_header->id; 
                    $m_detail->kode_pembiayaan = $kodePembiayaan;
                    $m_detail->urutan          = $index + 1;
                    $m_detail->deskripsi        = trim($d['deskripsi'] ?? '') ?: 'Harga beli aset';
                    $m_detail->nominal         = floatval($d['nominal'] ?? 0);
                    $m_detail->is_harga_beli   = !empty($d['is_harga_beli']) ? 1 : 0;
                    
                    $m_detail->save(); 
                }
            }

            $m_header = new \Model\Storage\PembiayaanPelunasanAset_model();
            $data_update = [
                'total_pembelian' => floatval($total_pembelian ?? 0),
                'diajukan'        => floatval($total_pembelian ?? 0),
            ];

            $m_header->where('kode_pembiayaan', $kodePembiayaan)->update($data_update);
            
            $this->result['status'] = 1;
            $this->result['content'] = ['id' => $m_header->id];
            $this->result['message'] = 'Data pelunasan (header & detail) berhasil disimpan.';

        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal menyimpan pelunasan (Database): ' . $e->getMessage();
        } catch (\Exception $e) {
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




    public function ProsesTempo()
    {
        if (empty($this->hakAkses['a_view'])) {
            showErrorAkses();
            return;
        }

        $id = filter_var($this->input->get('id'), FILTER_VALIDATE_INT);
        $pembiayaan = $id ? \Model\Storage\PembiayaanAset_model::find($id) : null;
        if (!$pembiayaan) {
            show_404();
            return;
        }

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

        if (strcasecmp(trim((string) $aset->nama_pembiayaan), 'Cash Tempo') !== 0) {
            show_404();
            return;
        }

        $jenisPembiayaan = \Model\Storage\MsPembiayaan_model::where('kode_jenis_pembiayaan', $aset->kode_pembiayaan)->first();
        if (!$jenisPembiayaan) {
            show_404();
            return;
        }

        $tdpRows = $this->getConnection()
            ->table('pembiayaan_tdp_aset')
            ->selectRaw('id, kode_pembiayaan, CONVERT(varchar(10), tanggal, 23) as tanggal, deskripsi, nominal, is_pengurang_pokok')
            ->where('kode_pembiayaan', $pembiayaan->kode_pembiayaan)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        $tdp = [];
        foreach ($tdpRows as $row) {
            $tdp[] = (array) $row;
        }
        $tdpLocked = $this->isPembiayaanLocked($pembiayaan);

        $tempoHeaderRows = $this->getConnection()
            ->table('pembiayaan_tempo_rencana_aset')
            ->selectRaw('id, kode_pembiayaan, harga_beli_aset, biaya_lain, total_pembelian, dikurangi_tanda_jadi, sisa_dijadwalkan, jumlah_termin')
            ->where('kode_pembiayaan', $pembiayaan->kode_pembiayaan)
            ->get();
        $tempoHeader = !empty($tempoHeaderRows) ? (array) reset($tempoHeaderRows) : null;
        $tempoLocked = $tdpLocked;

        $tempoDetails = [];
        if ($tempoHeader) {
            $detailRows = $this->getConnection()
                ->table('pembiayaan_tempo_detail_aset')
                ->selectRaw('id, termin_ke, CONVERT(varchar(10), jatuh_tempo, 23) as jatuh_tempo, nominal, status')
                ->where('kode_pembiayaan', $pembiayaan->kode_pembiayaan)
                ->orderBy('termin_ke', 'asc')
                ->get();
            foreach ($detailRows as $row) {
                $tempoDetails[] = (array) $row;
            }
        }

        $content = [
            'pembiayaan'                => $pembiayaan->toArray(),
            'aset'                      => $aset->toArray(),
            'jenis_pembiayaan'          => $jenisPembiayaan->toArray(),
            
            // TDP Variables
            'can_submit_tdp'            => !empty($this->hakAkses['a_submit']),
            'can_edit_tdp'              => !empty($this->hakAkses['a_edit']),
            'can_delete_tdp'            => !empty($this->hakAkses['a_delete']),
            'tdp_locked'                => $tdpLocked,
            'tdp'                       => $tdp,
            
            // Tempo Variables
            'can_submit_tempo'          => !empty($this->hakAkses['a_submit']),
            'can_edit_tempo'            => !empty($this->hakAkses['a_edit']),
            'can_delete_tempo'          => !empty($this->hakAkses['a_delete']),
            'tempo_locked'              => $tempoLocked,
            'tempo_header'              => $tempoHeader,
            'tempo_details'             => $tempoDetails,
            
            'supplier'                  => \Model\Storage\Supplier_model::find($pembiayaan->id_supplier),
        ];

        $this->add_external_js(['assets/aset/pembiayaan_aset/js/pembiayaan_aset.js']);
        $this->add_external_css(['assets/aset/pembiayaan_aset/css/pembiayaan_aset.css']);

        $data = $this->includes;
        $content['title_panel'] = 'Proses Pembiayaan Aset - Cash Tempo';
        $data['title_menu'] = 'Proses Pembiayaan Aset';
        $data['view'] = $this->load->view($this->pathView . 'v_proses_tempo', $content, true);
        $this->load->view($this->template, $data);
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
                } elseif ($this->isPembiayaanLocked($pembiayaan)) {
                    $this->result['message'] = 'Rencana tempo tidak dapat disimpan karena pembiayaan sudah selesai atau aset sudah masuk tahap Penerimaan Aset.';
                } else {
                    $connection = $this->getConnection();
                    $stored = $connection->transaction(function () use ($connection, $kodePembiayaan, $hargaBeli, $biayaLain, $jumlahTermin, $rows) {
                        
                        if ($connection->table('pembiayaan_tempo_rencana_aset')->where('kode_pembiayaan', $kodePembiayaan)->exists()) {
                            throw new \RuntimeException('Rencana tempo untuk pembiayaan ini sudah pernah disimpan.');
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
                                'nominal' => $this->centsToMoney($nominalCents),
                                'status' => 'Belum',
                            ];
                        }

                        if ($totalTerminCents !== $sisaDijadwalkanCents) {
                            throw new \RuntimeException(
                                'Total nominal termin (' . $this->centsToMoney($totalTerminCents) . ') tidak sama dengan sisa dijadwalkan (' . $this->centsToMoney($sisaDijadwalkanCents) . ').'
                            );
                        }

                        $connection->table('pembiayaan_tempo_rencana_aset')->insert([
                            'kode_pembiayaan' => $kodePembiayaan,
                            'harga_beli_aset' => $this->centsToMoney($hargaCents),
                            'biaya_lain' => $this->centsToMoney($biayaCents),
                            'total_pembelian' => $this->centsToMoney($totalPembelianCents),
                            'dikurangi_tanda_jadi' => $this->centsToMoney($tdpCents),
                            'sisa_dijadwalkan' => $this->centsToMoney($sisaDijadwalkanCents),
                            'jumlah_termin' => $jumlahTermin,
                        ]);

                        $connection->table('pembiayaan_tempo_detail_aset')->insert($insertedDetails);

                        $connection->table('pembiayaan_aset')->where('kode_pembiayaan', $kodePembiayaan)->update(['status' => 'SELESAI']);

                        return [
                            'detail_count' => $jumlahTermin,
                            'total_count' => $jumlahTermin + 1,
                        ];
                    });

                    $this->result['status'] = 1;
                    $this->result['total_data'] = $stored['total_count'];
                    $this->result['total_header'] = 1;
                    $this->result['total_detail'] = $stored['detail_count'];
                    $this->result['message'] = 'Rencana tempo berhasil disimpan. ' . $stored['detail_count'] . ' termin dijadwalkan.';
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal menyimpan rencana tempo: ' . $e->getMessage();
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
                                'nominal' => $this->centsToMoney($nominalCents),
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
                            'kode_pembiayaan' => $kodePembiayaan,
                            'harga_beli_aset' => $this->centsToMoney($hargaCents),
                            'biaya_lain' => $this->centsToMoney($biayaCents),
                            'total_pembelian' => $this->centsToMoney($totalPembelianCents),
                            'dikurangi_tanda_jadi' => $this->centsToMoney($tdpCents),
                            'sisa_dijadwalkan' => $this->centsToMoney($sisaDijadwalkanCents),
                            'jumlah_termin' => $jumlahTermin,
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


    public function save_tdp_modal()
    {
        if (empty($this->hakAkses['a_submit'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk menyimpan TDP.';
            display_json($this->result);
            return;
        }

        $params = $this->input->post('params');
        $params = is_array($params) ? $params : [];

        try {
            $kodePembiayaan = $params['kode_pembiayaan'] ?? '';

            $m_pembiayaan = \Model\Storage\PembiayaanAset_model::select(
                'pembiayaan_aset.*',
                'ms_aset.deskripsi_aset',
                'ms_aset.nilai_perolehan',
                'ms_aset.kode_pembiayaan as jenis_biaya',
            )
            ->leftJoin('ms_aset', 'ms_aset.kode_aset', '=', 'pembiayaan_aset.kode_aset')
            ->where('pembiayaan_aset.kode_pembiayaan', $kodePembiayaan)
            ->first();

            $pembiayaan = $m_pembiayaan ? $m_pembiayaan->toArray() : [];

            // cetak_r($pembiayaan, 1);

            if ($pembiayaan['jenis_biaya']  == 'PMB02'){
               
                $total_pengurang_pokok = 0;
                if (!empty($params['rows']) && is_array($params['rows'])) {
                    foreach ($params['rows'] as $tdp) {
                        
                        if (!empty($tdp['is_pengurang_pokok']) && $tdp['is_pengurang_pokok'] == 1) {
                            $total_pengurang_pokok += floatval($tdp['nominal']);
                        }
    
                        $m_tdp = new \Model\Storage\PembiayaanTdpAset_model();
                        $m_tdp->kode_pembiayaan    = $kodePembiayaan;
                        $m_tdp->tanggal            = date("Y-m-d", strtotime($tdp['tanggal']));
                        $m_tdp->nominal            = floatval($tdp['nominal']);
                        $m_tdp->deskripsi          = $tdp['deskripsi'] ?? 'Tanda Jadi';
                        $m_tdp->is_pengurang_pokok = $tdp['is_pengurang_pokok'] ?? 0;
                        $m_tdp->save(); 
                    } 
                    
                    $data_cash_tempo = [
                        'dikurangi_tanda_jadi' => $total_pengurang_pokok,
                        'updated_at'           => date('Y-m-d H:i:s')
                    ];
                    \Model\Storage\PembiayaanTempoRencanaAset_model::where('kode_pembiayaan', $kodePembiayaan)->update($data_cash_tempo);

                }
            
            } else if ($pembiayaan['jenis_biaya']  == 'PMB01'){

                // CASH
                $total_pengurang_pokok = 0;
                if (!empty($params['rows']) && is_array($params['rows'])) {
                    foreach ($params['rows'] as $tdp) {
                        
                        if (!empty($tdp['is_pengurang_pokok']) && $tdp['is_pengurang_pokok'] == 1) {
                            $total_pengurang_pokok += floatval($tdp['nominal']);
                        }
    
                        $m_tdp = new \Model\Storage\PembiayaanTdpAset_model();
                        $m_tdp->kode_pembiayaan    = $kodePembiayaan;
                        $m_tdp->tanggal            = date("Y-m-d", strtotime($tdp['tanggal']));
                        $m_tdp->nominal            = floatval($tdp['nominal']);
                        $m_tdp->deskripsi          = $tdp['deskripsi'] ?? 'Tanda Jadi';
                        $m_tdp->is_pengurang_pokok = $tdp['is_pengurang_pokok'] ?? 0;
                        $m_tdp->save(); 
                    }   
                }
    
                $data_pelunasan = [
                    'dikurangi_tanda_jadi' => $total_pengurang_pokok,
                    'updated_at'           => date('Y-m-d H:i:s')
                ];
    
                \Model\Storage\PembiayaanPelunasanAset_model::where('kode_pembiayaan', $kodePembiayaan)->update($data_pelunasan);
                // END CASH
            } else if ($pembiayaan['jenis_biaya']  == 'PMB03') {

                // Leasing
                $total_pengurang_pokok = 0;
                if (!empty($params['rows']) && is_array($params['rows'])) {
                    foreach ($params['rows'] as $tdp) {
                        
                        if (!empty($tdp['is_pengurang_pokok']) && $tdp['is_pengurang_pokok'] == 1) {
                            $total_pengurang_pokok += floatval($tdp['nominal']);
                        }
    
                        $m_tdp = new \Model\Storage\PembiayaanTdpAset_model();
                        $m_tdp->kode_pembiayaan    = $kodePembiayaan;
                        $m_tdp->tanggal            = date("Y-m-d", strtotime($tdp['tanggal']));
                        $m_tdp->nominal            = floatval($tdp['nominal']);
                        $m_tdp->deskripsi          = $tdp['deskripsi'] ?? 'Tanda Jadi';
                        $m_tdp->is_pengurang_pokok = $tdp['is_pengurang_pokok'] ?? 0;
                        $m_tdp->save(); 
                    }   
                }
    
                $data_pelunasan = [
                    'dikurangi_tanda_jadi' => $total_pengurang_pokok,
                    'updated_at'           => date('Y-m-d H:i:s')
                ];
    
                \Model\Storage\PembiayaanCicilanAset_model::where('kode_pembiayaan', $kodePembiayaan)->update($data_pelunasan);
                // END Leasin

            }


            $this->result['status'] = 1;
            $this->result['message'] = 'Data TDP berhasil disimpan dan pelunasan diperbarui.';

        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal menyimpan TDP (Database): ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal menyimpan TDP: ' . $e->getMessage();
        }

        // Wajib ada di paling bawah
        display_json($this->result);
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
