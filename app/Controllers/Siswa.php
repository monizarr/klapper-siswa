<?php

namespace App\Controllers;

use CodeIgniter\Model;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Siswa as ModelsSiswa;
use App\Models\Kelas;
use App\Controllers\BaseController;

class Siswa extends BaseController
{
    public function index()
    {
        $id_sekolah = $this->request->getGet('id_sekolah');

        $siswaModel = new ModelsSiswa();
        if (empty($id_sekolah)) {
            // join with sekolah
            $select = 'siswa.*, sekolah.nama as nama_sekolah';
            $siswa = $siswaModel->select($select)->join('sekolah', 'sekolah.id = siswa.id_sekolah')->findAll();
        } else {
            $siswa = $siswaModel->where('id_sekolah', $id_sekolah)->findAll();
        }

        //return json
        return $this->response->setJSON($siswa);
    }

    public function show($id)
    {
        // get data from model
        $model = new ModelsSiswa();
        $siswa = $model->find($id);

        return $this->response->setJSON($siswa);
    }


    public function getKelasSiswa($id_siswa)
    {
        $mKelas = new Kelas();
        // $kelas = $mKelas->where('id_siswa', $id_siswa)->findAll();
        // join with siswa 
        $kelas = $mKelas->join('siswa', 'siswa.id = kelas.id_siswa')->where('id_siswa', $id_siswa)->findAll();

        $response = [
            'data' => $kelas
        ];

        return $this->response->setJSON($response);
    }

    public function deleteSiswa($id)
    {
        $mSiswa = new ModelsSiswa();
        $mSiswa->where('id', $id)->delete();

        //set flashdata
        session()->setFlashdata('success', 'Data siswa berhasil dihapus');
        return redirect()->to('/sekolah/siswa');
    }

    public function uploadIjazah()
    {
        // helper(['form']);
        // if ($this->request->getMethod() !== 'post') {
        //     return redirect()->to('/sekolah/siswa');
        // }

        // $validationRules = [
        //     'id' => 'required',
        //     'file' => 'uploaded[file]|max_size[file,1024]|ext_in[file,jpg,png,pdf]'
        // ];

        // $validated = $this->validate($validationRules);

        // if (!$validated) {
        //     session()->setFlashdata('error', $this->validator->getErrors());
        //     return redirect()->to('/sekolah/siswa');
        // } else {

        $id = $this->request->getPost('id');
        $file = $this->request->getFile('bukti');
        $namaFile = $file->getName();
        // $namaFile = $file->getRandomName();
        $file->move(ROOTPATH . 'public/uploads/file');

        $data = [
            'keluar' => date('Y'),
            'bukti' => $namaFile,
            'status_keluar' => 'lulus',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $siswa = new ModelsSiswa();
        $save = $siswa->update($id, $data);

        if ($save) {
            session()->setFlashdata('success', 'Data ijazah berhasil diupload');
            return redirect()->to('/sekolah/siswa');
        } else {
            session()->setFlashdata('error', 'Gagal mengupload data ijazah');
            return redirect()->to('/sekolah/siswa');
        }
        // }
    }


    public function uploadSrtPindah()
    {
        $id = $this->request->getPost('id');
        $file = $this->request->getFile('bukti');
        $namaFile = $file->getName();
        $file->move(ROOTPATH . 'public/uploads/file');

        $data = [
            'keluar' => date('Y'),
            'bukti' => $namaFile,
            'status_keluar' => 'pindah',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $siswa = new ModelsSiswa();
        $save = $siswa->update($id, $data);

        if ($save) {
            session()->setFlashdata('success', 'Data surat pindah berhasil diupload');
            return redirect()->to('/sekolah/siswa');
        } else {
            session()->setFlashdata('error', 'Gagal mengupload data surat pindah');
            return redirect()->to('/sekolah/siswa');
        }
    }
    public function uploadSrtKeluar()
    {
        $id = $this->request->getPost('id');
        $file = $this->request->getFile('bukti');
        $namaFile = $file->getName();
        $file->move(ROOTPATH . 'public/uploads/file');

        $data = [
            'keluar' => date('Y'),
            'bukti' => $namaFile,
            'status_keluar' => 'pindah',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $siswa = new ModelsSiswa();
        $save = $siswa->update($id, $data);

        if ($save) {
            session()->setFlashdata('success', 'Data surat putus sekolah berhasil diupload');
            return redirect()->to('/sekolah/siswa');
        } else {
            session()->setFlashdata('error', 'Gagal mengupload data surat putus sekolah');
            return redirect()->to('/sekolah/siswa');
        }
    }
}
