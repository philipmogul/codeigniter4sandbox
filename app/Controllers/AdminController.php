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
    
    if ($request->isAJAX()) {
        $rules = [
            'name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Name is required'
                ]
            ],
            'username' => [
                'rules' => 'required|min_length[4]|is_unique[users.username,id,{id}]',
                'errors' => [
                    'required'   => 'Username is required',
                    'min_length' => 'Username must be at least 4 characters',
                    'is_unique'  => 'Username already exists',
                ]
            ],
            'bio' => [
                'rules' => 'permit_empty|max_length[250]',
                'errors' => [
                    'max_length' => 'Bio must not exceed 250 characters'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 0,
                'msg'    => 'Please correct the highlighted errors.',
                'error'  => $validation->getErrors()
            ]);
        }

        // ✅ Success response
        return $this->response->setJSON([
            'status' => 1,
            'msg'    => 'Profile updated successfully!',
            'user_info' => [
                'name' => $request->getPost('name')
            ]
        ]);
    }
}



}
