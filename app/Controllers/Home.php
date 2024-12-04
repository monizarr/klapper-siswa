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
        $role = session()->get('user')['role'];

        if (session()->get('user') == null) {
            return redirect()->to($role . '/login');
        }
        return redirect()->to($role . '/dashboard');
    }
}
