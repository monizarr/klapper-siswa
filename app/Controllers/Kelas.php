<?php

namespace App\Controllers;

use CodeIgniter\Model;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Kelas as ModelsKelas;
use App\Controllers\BaseController;

class Kelas extends BaseController
{
    public function index()
    {
        //
    }

    public function add()
    {
        $data = [
            'id_siswa' => $this->request->getPost('id_siswa'),
            'id_sekolah' => $this->request->getPost('id_sekolah'),
            'kelas' => $this->request->getPost('kelas'),
            'ta' => $this->request->getPost('ta'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $mKelas = new ModelsKelas();
        $mKelas->insert($data);

        //set flashdata
        session()->setFlashdata('success', 'Data kelas berhasil ditambahkan');
        return redirect()->to('/sekolah/siswa');
    }


    public function update($id_kelas)
    {
        $mKelas = new ModelsKelas();
        $data = $this->request->getJSON();

        $update = $mKelas->update($id_kelas, $data);

        if ($update) {
            session()->setFlashdata('success', 'Data kelas berhasil diupdate');
            return redirect()->to('/sekolah/siswa');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data kelas');
            return redirect()->to('/sekolah/siswa');
        }
    }

    public function delete($id_kelas)
    {
        $mKelas = new ModelsKelas();
        $mKelas->delete($id_kelas);

        //set flashdata
        session()->setFlashdata('success', 'Data kelas berhasil dihapus');
        return redirect()->to('/sekolah/siswa');
    }
}
