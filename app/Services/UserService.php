<?php

namespace App\Services;

use Closure;
use DB;
use App\Models\User;

class UserService extends BaseService
{
    public function __construct()
    {
    }

    public function getById($id) 
    {
        return User::findOrFail($id);
    }
}
