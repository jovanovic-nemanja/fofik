<?php

namespace App\Providers;

use Auth;
use App\Auth\CustomUserProvider;
use App\Models\User;
use Illuminate\Support\ServiceProvider;

class CustomAuthProvider extends ServiceProvider
{

    public function register()
    {
        //
    }

    public function boot()
    {
        Auth::provider('custom',function()
        {
            return new CustomUserProvider(new User());
        });
    }
}