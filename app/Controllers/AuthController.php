<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

// Include Hash and CIAuth Library
use App\Libraries\Hash;
use App\Libraries\CIAuth;
use App\Models\User;
use App\Models\PasswordResetToken;
use Carbon\Carbon;


class AuthController extends BaseController
{
    protected $helpers = ['url','form','CIMail'];

    public function loginForm()
    {
        $data = [
            'pageTitle' => 'Admin Login',
            'validation'=> null,
        ];
        return view('backend/pages/auth/login', $data);
    }

    public function loginHandler()
    {
        $fieldType = filter_var($this->request->getVar('login_id'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        echo $fieldType; // Check if email or username is being used to login
        
        if( $fieldType == 'email')
        {
            $isvalid = $this->validate([
                'login_id' => [
                'rules' => 'required|valid_email|is_not_unique[users.email]',
                'errors' => [
                    'required' => 'You must enter your email address',
                    'valid_email' => 'You must enter a valid email address',
                    'is_not_unique' => 'This email address does not exist in our records'
                ]],
                'password' => [
                'rules' => 'required|min_length[5]|max_length[30]',
                'errors' => [
                    'required' => 'You must enter your password',
                    'min_length' => 'Password must be at least 5 characters long',
                    'max_length' => 'Password cannot exceed 30 characters'
                ]]
            ]);


        }
        else
        {
            $isvalid = $this->validate([
                'login_id' => [
                'rules' => 'required|is_not_unique[users.username]',
                'errors' => [
                    'required' => 'You must enter your username',
                    'is_not_unique' => 'This username does not exist in our records'
                ]],
                'password' => [
                'rules' => 'required|min_length[5]|max_length[30]',
                'errors' => [
                    'required' => 'You must enter your password',
                    'min_length' => 'Password must be at least 5 characters long',
                    'max_length' => 'Password cannot exceed 30 characters'
                ]]
            ]);
        }

        if( !$isvalid )
        {
            return view('backend/pages/auth/login', [
                'pageTitle' => 'Admin Login',
                'validation'=> $this->validator,
            ]);
        }
        else
        {
            //echo "Form validated successfully";
            $user = new User();
            $userInfo = $user->where($fieldType, $this->request->getVar('login_id'))->first();
            $checkPassword = Hash::check($this->request->getVar('password'), $userInfo['password_hash']);

            if( !$checkPassword )
            {
                return redirect()->route('admin.login.form')->with('fail', 'Incorrect password.')->withInput();
            }
            else
            {
                CIAuth::setCIAuth($userInfo);
                return redirect()->route('admin.home')->with('success', 'You are now logged in!');
            }

        }

    }

    // Forgot Password Form
    public function forgotForm()
    {
        $data = [
            'pageTitle' => 'Forgot Password',
            'validation'=> null,
        ];
        return view('backend/pages/auth/forgot', $data);
    }


    // Password Reset Link Handler
    public function sendPasswordResetLink()
    {
        $isvalid = $this->validate([
            'email' => [
            'rules' => 'required|valid_email|is_not_unique[users.email]',
            'errors' => [
                'required' => 'You must enter your email address',
                'valid_email' => 'You must enter a valid email address',
                'is_not_unique' => 'This email address does not exist in our records'
            ]]
        ]);

        if( !$isvalid )
        {
            return view('backend/pages/auth/forgot', [
                'pageTitle' => 'Forgot Password',
                'validation'=> $this->validator,
            ]);
        }
        else
        {
            // Form was validated successfully
            
            $user = new User();
            $userInfo = $user->asObject()->where('email', $this->request->getVar('email'))->first();

            // Generate Token
            $token = bin2hex(openssl_random_pseudo_bytes(65));

            // Generate Password Reset Token 
            $password_reset_token = new PasswordResetToken();
            $isOldTokenExists = $password_reset_token->asObject()->where('email', $userInfo->email)->first();
            
            if( $isOldTokenExists )
            {
                // Delete Old Token
                $password_reset_token->where('email', $userInfo->email)
                                    ->set(['token' => $token, 'created_at' => Carbon::now()])
                                    ->update();
            }
            else
            {
                // Create New Token
                $password_reset_token->insert([
                    'email' => $userInfo->email,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);
            }

            // Create Action Link 
            $actionLink = base_url(route_to('admin.reset-password', $token));

            // Send Email
            $mail_data = [
                'actionLink' => $actionLink,
                'user' => $userInfo,
            ];

            $view = \Config\Services::renderer();
            $mail_body = $view->setVar('mail_data', $mail_data)->render('backend/email-templates/forgot-email-template');

            $mailConfig = [
                'mail_from_email' => env('EMAIL_FROM_ADDRESS'),
                'mail_from_name' => env('EMAIL_FROM_NAME'),
                'mail_recepient_email' => $userInfo->email,
                'mail_recepient_name' => $userInfo->username,
                'mail_subject' => 'Password Reset Request',
                'mail_body' => $mail_body,
            ];

            // Send Email 
            if( sendEmail($mailConfig) )
            {
                return redirect()->route('admin.forgot.form')->with('success', 'We have emailed your password reset link!');
            }
            else
            {
                return redirect()->route('admin.forgot.form')->with('fail', 'Failed to send password reset link. Please try again later.');
            }


        }
    }    // End of sendPasswordResetLink function



}
