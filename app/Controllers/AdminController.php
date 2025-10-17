<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\User;

// Include CIAuth Library
use App\Libraries\CIAuth;

class AdminController extends BaseController
{
    protected $helpers = ['form', 'url','CIMail', 'CIFunctions_helper'];
    public function index()
    {
        $data = [
            'pageTitle' => 'Admin Dashboard Home Page',
        ];
        return view('backend/pages/home', $data);
    }

    public function logoutHandler()
    {
        CIAuth::forget();
        return redirect()->route('admin.login.form');
    }

    // new method for profile page
    public function profile()
    {
        $data = [
            'pageTitle' => 'Admin Profile Page',
        ];
        return view('backend/pages/profile', $data);
    }


    // Update personal details handler
   public function updatePersonalDetails()
{
    $request    = \Config\Services::request();
    $validation = \Config\Services::validation();
    $user_id    = CIAuth::id();
    

}


}