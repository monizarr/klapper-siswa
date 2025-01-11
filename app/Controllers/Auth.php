<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Userapp;
use App\Models\User;
use App\Models\Sekolah;
use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function index()
    {

        // check session
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/sekolah/dashboard');
        }

        return view('auth/index');
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $sekolahModel = new Sekolah();
        $user = $sekolahModel->where('username', $username)->first();

        if ($user['status'] == 'a') {
            if (password_verify($password, $user['password'])) {
                $data = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'isLoggedIn' => true,
                    'sekolah' => $user
                ];
                session()->set('user', $data);
                return redirect()->to('/sekolah/dashboard');
            }
        } else {
            return redirect()->to('/sekolah/login')->withInput()->with('error', 'Status sekolah tidak aktif');
        }
        return redirect()->to('/sekolah/login')->withInput()->with('error', 'Login failed');
    }

    public function loginAdmin()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }

        return view('auth/index');
    }

    public function storeLoginAdmin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new Userapp();
        $user = $userModel->where('username', $username)->first();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $data = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'isLoggedIn' => true
                ];

                session()->set('user', $data);
                return redirect()->to('/admin/dashboard');
            }
        }

        return redirect()->to('/admin/login')->withInput()->with('error', 'Login failed');
    }

    public function sekolahLogout()
    {
        session()->destroy();
        return redirect()->to('/sekolah/login');
    }

    public function adminLogout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}
