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

    // reset Password Form after user clicks link from their email 
    public function resetPassword($token)
    {
        $passwordResetPassword = new PasswordResetToken();
        $checktoken = $passwordResetPassword->asObject()->where('token', $token)->first();
        if( !$checktoken )
        {
            return redirect()->route('admin.forgot.form')->with('fail', 'Invalid password reset token.');
        }
        else
        {
            // check token not expired
            $diffMins = Carbon::createFromFormat('Y-m-d H:i:s', $checktoken->created_at)->diffInMinutes(Carbon::now());
            if( $diffMins > 15 ) // Token expired after 15 minutes
            {
                // token expired
                return redirect()->route('admin.forgot.form')->with('fail', 'This password reset token has expired. Please request a new one.');
            }
            else
            {
                // display reset password page 
                return view('backend/pages/auth/reset', [
                    'pageTitle' => 'Reset Password',
                    'validation'=> null,
                    'token' => $token,
                ]);
            }
        }
    }



    // resetPasswordHandler after user submits new password
    public function resetPasswordHandler($token)
    {
        // in terminal: create custom validation rule for checking strong password
        // php spark make:validation isPasswordStrong
        // check app/Validation/isPasswordStrong.php : modify method 
        // go to app/Config/Validation.php : add new rule
        $isvalid = $this->validate([
            'new_password' => [
            'rules' => 'required|min_length[5]|max_length[20]|is_password_strong[new_password]',
            'errors' => [
                'required' => 'You must enter your new password',
                'min_length' => 'New password must be at least 5 characters long',
                'max_length' => 'New password cannot exceed 30 characters',
                'is_password_strong' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.'
            ]],
            'confirm_new_password' => [
            'rules' => 'required|matches[new_password]',
            'errors' => [
                'required' => 'You must confirm your new password',
                'matches' => 'New password and confirm new password do not match'
            ]]
        ]);

        if( !$isvalid )
        {
            return view('backend/pages/auth/reset', [
                'pageTitle' => 'Reset Password',
                'validation'=> $this->validator,
                'token' => $token,
            ]);
        }
        else
        {
            //echo "Form validated successfully";

            $passwordResetPassword = new PasswordResetToken();
            $get_token = $passwordResetPassword->asObject()->where('token', $token)->first();

            // get user details 
            $user = new User();
            $userInfo = $user->asObject()->where('email', $get_token->email)->first();

            if( !$get_token )
            {
                return redirect()->back()->with('fail', 'Invalid password reset token.')->withInput();
            }
            else
            {
                // update password 
                $user->where('email', $userInfo->email)
                     ->set(['password_hash' => Hash::make($this->request->getVar('new_password'))])
                     ->update();

                // send notification to admin email address 
                $mail_data = array(
                    'user' => $userInfo,
                    'new_password' => $this->request->getVar('new_password')
                );

                $view = \Config\Services::renderer();
                $mail_body = $view->setVar('mail_data', $mail_data)->render('backend/email-templates/password-changed-email-template');

                $mailConfig = [
                    'mail_from_email' => env('EMAIL_FROM_ADDRESS'),
                    'mail_from_name' => env('EMAIL_FROM_NAME'),
                    'mail_recepient_email' => $userInfo->email,
                    'mail_recepient_name' => $userInfo->username,
                    'mail_subject' => 'Your Password Has Been Changed',
                    'mail_body' => $mail_body,
                ];

                if( sendEmail($mailConfig) )
                {
                    // delete token 
                    $passwordResetPassword->where('email', $userInfo->email)->delete();

                    return redirect()->route('admin.login.form')->with('success', 'Your password has been changed successfully. You can now login with your new password.');
                }
                else
                {
                    return redirect()->back()->with('fail', 'Failed to send notification email. Please try again later.')->withInput();
                }


            }

        }


    }

}
