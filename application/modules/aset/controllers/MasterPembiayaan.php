<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MasterPembiayaan extends Public_Controller {

    private $pathView = 'aset/master_pembiayaan/';
    private $url;
    private $hakAkses;

    function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;
        $this->hakAkses = hakAkses($this->url);
    }

    public function index($segment = 0)
    {
        if ($this->hakAkses['a_view'] != 1) {
            showErrorAkses();
            return;
        }

        $this->add_external_js(array(
            "assets/aset/master_pembiayaan/js/master_pembiayaan.js",
        ));

        $data = $this->includes;
        $content['akses']       = $this->hakAkses;
        $content['title_panel'] = 'Master Pembiayaan';
        $data['title_menu']     = 'Master Pembiayaan';
        $data['view']           = $this->load->view($this->pathView . 'v_index', $content, TRUE);
        $this->load->view($this->template, $data);
    }

    public function list_data()
    {
        $pembiayaan = (new \Model\Storage\MsPembiayaan_model())
            ->orderBy('nama_pembiayaan', 'asc')
            ->get()
            ->toArray();

        $m_aset = new \Model\Storage\MsAset_model();
        foreach ($pembiayaan as &$row) {
            $row['jumlah_aset'] = $m_aset
                ->where('kode_pembiayaan', $row['kode_pembiayaan'])
                ->count();
        }
        unset($row);

        $content['list'] = $pembiayaan;
        $content['akses'] = $this->hakAkses;
        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }

    public function add_form()
    {
        $data['data'] = null;
        $this->load->view($this->pathView . 'v_form', $data);
    }

    public function edit_form()
    {
        $id = $this->input->get('id');
        $data['data'] = \Model\Storage\MsPembiayaan_model::find($id);
        if (!$data['data']) {
            show_404();
            return;
        }

        $this->load->view($this->pathView . 'v_form', $data);
    }

    public function save_data()
    {
        if (empty($this->hakAkses['a_submit'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk menambahkan jenis pembiayaan.';
            display_json($this->result);
            return;
        }

        $params = $this->input->post('params') ?: [];
        $nama = trim((string) ($params['nama_pembiayaan'] ?? ''));

        try {
            if ($nama === '' || strlen($nama) > 100) {
                $this->result['message'] = 'Nama pembiayaan wajib diisi (maksimal 100 karakter).';
            } else {
                $duplicate = \Model\Storage\MsPembiayaan_model::whereRaw(
                    'LOWER(LTRIM(RTRIM(nama_pembiayaan))) = ?',
                    [strtolower($nama)]
                )->first();

                if ($duplicate) {
                    $this->result['message'] = 'Nama pembiayaan sudah terdaftar.';
                } else {
                    $model = new \Model\Storage\MsPembiayaan_model();
                    $model->kode_pembiayaan = $this->generateKodePembiayaan();
                    $model->nama_pembiayaan = $nama;
                    $model->save();

                    $userNama = $this->userdata['detail_user']['nama_detuser'] ?? 'System';
                    Modules::run('base/event/save', $model, "Master pembiayaan {$model->kode_pembiayaan} - {$nama} ditambahkan oleh {$userNama}", null, $model->id, $model);
                    $this->result['status'] = 1;
                    $this->result['message'] = 'Data berhasil disimpan.';
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal menyimpan data pembiayaan: ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal menyimpan data pembiayaan: ' . $e->getMessage();
        }

        display_json($this->result);
    }

    public function edit_data()
    {
        if (empty($this->hakAkses['a_edit'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk mengubah jenis pembiayaan.';
            display_json($this->result);
            return;
        }

        $params = $this->input->post('params') ?: [];
        $id = $params['id'] ?? null;
        $nama = trim((string) ($params['nama_pembiayaan'] ?? ''));

        try {
            $model = \Model\Storage\MsPembiayaan_model::find($id);
            if (!$model) {
                $this->result['message'] = 'Data pembiayaan tidak ditemukan.';
            } elseif ($nama === '' || strlen($nama) > 100) {
                $this->result['message'] = 'Nama pembiayaan wajib diisi (maksimal 100 karakter).';
            } else {
                $duplicate = \Model\Storage\MsPembiayaan_model::whereRaw(
                    'LOWER(LTRIM(RTRIM(nama_pembiayaan))) = ?',
                    [strtolower($nama)]
                )->where('id', '!=', $id)->first();
                if ($duplicate) {
                    $this->result['message'] = 'Nama pembiayaan sudah terdaftar.';
                } else {
                    $oldName = $model->nama_pembiayaan;
                    $model->nama_pembiayaan = $nama;
                    $model->save();

                    $userNama = $this->userdata['detail_user']['nama_detuser'] ?? 'System';
                    Modules::run('base/event/update', $model, "Master pembiayaan {$model->kode_pembiayaan} - {$oldName} diubah oleh {$userNama}", null, $id, $model);
                    $this->result['status'] = 1;
                    $this->result['message'] = 'Data berhasil diubah.';
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal mengubah data pembiayaan: ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal mengubah data pembiayaan: ' . $e->getMessage();
        }

        display_json($this->result);
    }

    public function delete_data()
    {
        if (empty($this->hakAkses['a_delete'])) {
            $this->result['message'] = 'Anda tidak memiliki akses untuk menghapus jenis pembiayaan.';
            display_json($this->result);
            return;
        }

        $id = $this->input->post('params');

        try {
            $model = \Model\Storage\MsPembiayaan_model::find($id);
            if (!$model) {
                $this->result['message'] = 'Data pembiayaan tidak ditemukan.';
            } elseif (\Model\Storage\MsAset_model::where('kode_pembiayaan', $model->kode_pembiayaan)->exists()) {
                $this->result['message'] = 'Jenis pembiayaan tidak dapat dihapus karena sudah digunakan pada aset.';
            } else {
                $nama = $model->kode_pembiayaan . ' - ' . $model->nama_pembiayaan;
                $model->delete();

                $userNama = $this->userdata['detail_user']['nama_detuser'] ?? 'System';
                Modules::run('base/event/delete', $model, "Master pembiayaan {$nama} dihapus oleh {$userNama}", null, $id, $model);
                $this->result['status'] = 1;
                $this->result['message'] = 'Data berhasil dihapus.';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = 'Gagal menghapus data pembiayaan: ' . $e->getMessage();
        } catch (\Exception $e) {
            $this->result['message'] = 'Gagal menghapus data pembiayaan: ' . $e->getMessage();
        }

        display_json($this->result);
    }

    private function generateKodePembiayaan()
    {
        for ($number = 1; $number < 1000000; $number++) {
            $candidate = 'PMB' . str_pad((string) $number, 2, '0', STR_PAD_LEFT);
            if (!\Model\Storage\MsPembiayaan_model::where('kode_pembiayaan', $candidate)->exists()) {
                return $candidate;
            }
        }

        throw new \RuntimeException('Kode pembiayaan PMB tidak dapat dibuat karena seluruh nomor sudah digunakan.');
    }
}
