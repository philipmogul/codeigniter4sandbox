<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
    // Modify by adding table name, primary key and allowed fields
    protected $table = "users";
    protected $primaryKey = "id";
    protected $returnType = "array"; // So it may return an array and not an object
    protected $allowedFields = ['name','username', 'email', 'password_hash', 'picture', 'bio'];
}
