<?php

namespace App\Controllers\Sekolah;

use App\Models\Siswa;
use App\Models\Sekolah;
use App\Models\Kelas;
use App\Controllers\BaseController;

class Dashboard extends BaseController
{

    // construct if not session redirect to login
    public function __construct() {}

    public function index()
    {
        if (session()->get('user') == null) {
            return redirect()->to('/sekolah/login');
        }

        $data = [
            'title' => 'Dashboard',
            'content' => 'sekolah/v_index',
            'apath' => 'homeAdmin'
        ];
        return view('layouts/v_wrapper', $data);
    }


    public function mSiswa()
    {

        if (session()->get('user') == null) {
            return redirect()->to('/sekolah/login');
        }

        $mSiswa = new Siswa();
        $mKelas = new Kelas();

        $user  = session()->get('user');
        // if   
        $kelas = $mKelas->where('id_sekolah', $user['sekolah']['id'])->findAll();
        $siswa = $mSiswa->where('id_sekolah', $user['sekolah']['id'])->findAll();
        $tahun = $mSiswa->select('masuk')->where('id_sekolah', $user['sekolah']['id'])->distinct()->findAll();

        helper(['form']);

        $data = [
            'title' => 'Manajemen Siswa',
            'content' => 'sekolah/v_msiswa',
            'apath' => 'mSiswa',
            'siswa' => $siswa,
            'user' => $user,
            'kelas' => $kelas,
            'angkatan' => $tahun
        ];

        // dd($data);
        return view('layouts/v_wrapper', $data);
    }

    public function getSiswa()
    {
        $model = new Siswa();

        // Ambil parameter dari request DataTables
        $start = $this->request->getVar('start');
        $length = $this->request->getVar('length');
        $draw = $this->request->getVar('draw');
        $searchValue = $this->request->getVar('search')['value'] ?? '';
        $angkatan = $this->request->getGet('angkatan'); // Ambil parameter angkatan dari request
        $order = $this->request->getVar('order')[0];

        // Query builder
        $builder = $model->builder();
        $builder->where('id_sekolah', session()->get('user')['sekolah']['id']);

        // Filter berdasarkan angkatan jika ada
        if (!empty($angkatan)) {
            $builder->where('masuk', $angkatan);
        }

        // Filter pencarian jika ada
        if (!empty($searchValue)) {
            $builder->like('nama', $searchValue); // Misalnya pencarian berdasarkan nama
        }

        // Hitung total record sebelum limit
        $totalRecords = $builder->countAllResults(false);

        // order
        $builder->orderBy('nama', $order['dir']);

        // Ambil data dengan limit dan offset
        $data = $builder->limit($length, $start)->get()->getResult();

        // Hitung total record yang sesuai filter pencarian
        $totalFilteredRecords = count($data);

        // Format data untuk DataTables
        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFilteredRecords,
            'data' => $data
        ];

        return $this->response->setJSON($response);
    }


    public function addSiswa()
    {
        $siswa = new Siswa();
        $kelas = new Kelas();

        $file = $this->request->getFile('bukti_masuk');
        $namaFile = $file->getName();
        $file->move(ROOTPATH . 'public/uploads/surat-masuk');
        $data = [
            'id_sekolah' => $this->request->getPost('id_sekolah'),
            'id_status' => $this->request->getPost('status_masuk'),
            'nis' => $this->request->getPost('nis'),
            'nama' => $this->request->getPost('nama'),
            'jk' => $this->request->getPost('jk'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tgl_lahir' => $this->request->getPost('tgl_lahir'),
            'orang_tua' => $this->request->getPost('ortu'),
            'masuk' => $this->request->getPost('masuk'),
            'bukti_masuk' => $namaFile,
            'keluar' => $this->request->getPost('keluar'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $simpanSiswa = $siswa->insert($data);

        if ($simpanSiswa) {
            $id_siswa = $siswa->insertID();
            $dataKelas = [
                'id_sekolah' => $this->request->getPost('id_sekolah'),
                'id_siswa' => $id_siswa,
                'kelas' => 1,
                'ta' => $this->request->getPost('masuk'),
                'created_at' => date('Y-m-d H:i:s')
            ];
            $kelas->insert($dataKelas);

            //set flashdata
            session()->setFlashdata('success', 'Data siswa berhasil ditambahkan');
            return redirect()->to('/sekolah/siswa');
        }
    }

    public function bulkSiswa()
    {
        $data = [
            'title' => 'Tambah Angkatan Siswa',
            'content' => 'sekolah/v_bulk_siswa',
            'apath' => 'bulkSiswa',
            'user' => session()->get('user')
        ];
        return view('layouts/v_wrapper', $data);
    }

    public function saveBulkSiswa()
    {
        $siswa = new Siswa();
        $kelas = new Kelas();
        $file = $this->request->getFile('csv');
        // $csvData = array_map('str_getcsv', file($file));

        $delimiter = $this->request->getPost('delimiter');
        if ($delimiter == ',') {
            $csvData = array_map(function ($v) {
                return str_getcsv($v, ',');
            }, file($file));
        } else {
            $csvData = array_map(function ($v) {
                return str_getcsv($v, ';');
            }, file($file));
        }

        $header = array_shift($csvData);

        // exisiting data
        $existingData = $siswa->where('id_sekolah', session()->get('user')['sekolah']['id'])->findAll();
        $existingNis = array_column($existingData, 'nis');


        foreach ($csvData as $row) {
            if (!in_array($row[0], $existingNis)) {
                $data = [
                    'id_sekolah' => session()->get('user')['sekolah']['id'],
                    'nis' => $row[0],
                    'nama' => $row[1],
                    'jk' => $row[2],
                    'tempat_lahir' => $row[3],
                    'tgl_lahir' => $row[4],
                    'orang_tua' => $row[5],
                    'masuk' => $row[6],
                    'keluar' => $row[7],
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $insiswa = $siswa->insert($data);

                if ($insiswa) {
                    $id_siswa = $siswa->insertID();
                    $dataKelas = [
                        'id_sekolah' => session()->get('user')['sekolah']['id'],
                        'id_siswa' => $id_siswa,
                        'kelas' => 1,
                        'ta' => $row[6],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $kelas->insert($dataKelas);
                }
            }
        }

        session()->setFlashdata('success', 'Data siswa berhasil ditambahkan');
        return redirect()->to('/sekolah/siswa')->withInput();
    }

    public function editSiswa()
    {
        $siswa = new Siswa();

        $id = $this->request->getPost('id');
        $data = [
            // 'id_sekolah' => $this->request->getPost('id_sekolah'),
            'id_status' => $this->request->getPost('status'),
            'nis' => $this->request->getPost('nis'),
            'nama' => $this->request->getPost('nama'),
            'jk' => $this->request->getPost('jk'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tgl_lahir' => $this->request->getPost('tgl_lahir'),
            'orang_tua' => $this->request->getPost('ortu'),
            'masuk' => $this->request->getPost('masuk'),
            'keluar' => $this->request->getPost('keluar'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $siswa->update($id, $data);
        //set flashdata
        session()->setFlashdata('success', 'Data siswa berhasil diubah');
        return redirect()->to('/sekolah/siswa');
    }

    public function profil()
    {
        if (session()->get('user') == null) {
            return redirect()->to('/sekolah/login');
        }

        $sekolah = new Sekolah();
        $data = [
            'title' => 'Profil Sekolah',
            'content' => 'sekolah/v_profil',
            'apath' => 'profil',
            'user' => session()->get('user'),
            'sekolah' => $sekolah->where('id', session()->get('user')['sekolah']['id'])->first()
        ];
        return view('layouts/v_wrapper', $data);
    }

    public function getCsv()
    {
        $file = ROOTPATH . 'public/uploads/file/template_siswa.csv';
        return $this->response->download($file, null);
    }
}
