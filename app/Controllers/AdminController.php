<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\User;

// Include CIAuth Library
use App\Libraries\CIAuth;

class AdminController extends BaseController
{
    protected $helpers = ['url', 'form','CIMail', 'CIFunctions_helper'];
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
    
    // validation form 
    if( $request->isAJAX() )
    {
        $this->validate([
            'name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'The Name field is required.',
                ],
            ],
            'username'    => [
                'rules' => 'required|min_length[4]|is_unique[users.username, id, ' . $user_id . ']',
                'errors' => [
                    'required'    => 'The Username field is required.',
                    'min_length'  => 'The Username must be at least 4 characters long.',
                    'is_unique'   => 'The Username is already taken.',
                ]
            ]
        ]);


        if( $validation->run() == FALSE )
        {
            $errors = $validation->getErrors();
            return json_encode(['status'=>0,'error'=>$errors]); 
        }
        else
        {
            // update details 
            $user = new User();
            $update = $user->where('id', $user_id)->set([
                'name'     => $request->getVar('name'),
                'username' => $request->getVar('username'),
                'bio' => $request->getVar('bio'),
            ])->update();

            if( $update )
            {
                //return json_encode(['status'=>1,'msg'=>'Profile updated successfully.']);
                $user_info = $user->find($user_id);
                return json_encode(['status'=>1,'msg'=>'Profile updated successfully.', 'user_info'=>[
                    'name' => $user_info['name'],
                    'username' => $user_info['username'],
                    'bio' => $user_info['bio'],
                ]]);

            }
            else
            {
                return json_encode(['status'=>0,'msg'=>'Failed to update profile. Please try again.']);
            }

        }


    }

}


}