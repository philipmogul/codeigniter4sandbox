<?php 

namespace App\Libraries;

// include user file so as to deal with user model
use App\Models\User;

class CIAuth
{
    public static function setCIAuth($result)
    {
        $session = session();
        $array = ['logged_in' => TRUE];
        $userdata = $result;
        $session->set('userdata', $userdata);
        $session->set($array);
    }

    // function for returning an authenticated admin value 
    public static function id()
    {
        $session = session();
        if ($session->has('logged_in')) {
            if( $session->has('userdata') ) {
                // Basic login 
                //return $session->get('userdata')['id'];
                
                // Once logged in, fetch the user from database
                $user = new User();
                $userdata = $session->get('userdata');
                return $userdata['id'];
                // Create another helper file in Helpers : Helper/CIFunctions_helper.php 
                // Make sure to load helpers in both admin + auth controllers 
                // protected $helpers = ['form', 'url','CIMail', 'CIFunctions_helper'];
                // Go to public folder of project, create : extra-assets folder 
                // Create another folder inside extra-assets called : jscssassets (
                // Contains js and css files. Can be named anything ) 
                // I didnt have access to ijabo folder, so i added jquery instead 
                // then updated links in page-layout.php file
                // Create another folder called images 
                // in images folder, create another folder called users 
                // drop default avatar image in users folder 
                // go to profile.php and load default avater image 




            } else {
                return null;
            }
        }
        return null;
    }


    // return true if authenticated admin is found 
    public static function check()
    {
        $session = session();
        return $session->has('logged_in');
    }


    // delete an authenticated admin session
    public static function forget()
    {
        $session = session();
        $session->remove('logged_in');
        $session->remove('userdata');
    }


    // return all authenticated admin data
    public static function user()
    {
        $session = session();
        if ($session->has('logged_in')) {
            if( $session->has('userdata') ) {
                if( $session->has('userdata') ) {
                    return $session->get('userdata');
                }
                else{
                    return null;
                }

            } else {
                return null;
            }
        }
        return null;
    }


}

