<?php 

namespace App\Libraries;

class Hash
{
    public static function make(string $password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function check(string $password, string $hashedPassword)
    {
        if (password_verify($password, $hashedPassword))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

