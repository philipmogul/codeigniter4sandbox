<?php 

use App\Libraries\CIAuth;
use App\Models\User;

// function for returning an authenticated admin value
if( ! function_exists("get_user") )
{
    function get_user()
    {
        if( CIAuth::check() )
        {
            return new User();
            return $user->asObject()->where('id', CIAuth::id())->first();
        }
        else
        {
            return null;
        }
    }
}
