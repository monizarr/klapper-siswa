<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // check session
        if (session()->get('user') == null) {
            return redirect()->to('/sekolah/login');
        }

        if (session()->get('user') == null) {
            return redirect()->to('sekolah/login');
        }
    }
}
