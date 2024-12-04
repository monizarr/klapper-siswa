<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Sekolah as ModelsSekolah;
use App\Controllers\BaseController;

class Sekolah extends BaseController
{
    public function index()
    {
        $sekolah = new ModelsSekolah();
        $data = [
            'title' => 'Data Sekolah',
            'content' => 'sekolah/v_index',
            'apath' => 'homeAdmin',
            'sekolah' => $sekolah->findAll(),
        ];

        return view('layouts/v_wrapper', $data);
    }
}
