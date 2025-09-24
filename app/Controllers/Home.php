<?php 
namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Where we load in our views 
        // return view('profile'); Will load profile view
        // return view('about'); Will load about view
        return view('welcome_message');

    }

    public function about()
    {   
        return view('about');
    }

    public function contact(): string
    {
        return view('contact');
    }

    public function profile(): string
    {
        return view('profile');
    }


    public function settings(): string
    {
        return view('settings');
    }


}
