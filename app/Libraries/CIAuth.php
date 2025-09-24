<?php 

namespace App\Libraries;

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
                return $session->get('userdata')['id'];
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

