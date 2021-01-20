<?php 

namespace App\Auth;

use App\Models\User;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\UserProvider;

class CustomUserProvider implements UserProvider {

    protected $model;
    public function __construct($model)
    {
        $this->model = $model;
    }

    public function retrieveByID($identifier)
    {   
        $this->user = User::find($identifier);
        return $this->user;
    }

    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();

        $retrievedModel = $this->newModelQuery($model)->where(
            $model->getAuthIdentifierName(), $identifier
        )->first();

        if (! $retrievedModel) {
            return;
        }

        $rememberToken = $retrievedModel->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token)
                        ? $retrievedModel : null;
    }
    public function updateRememberToken(UserContract $user, $token)
    {
        $user->setRememberToken($token);

        $timestamps = $user->timestamps;

        $user->timestamps = false;

        $user->save();

        $user->timestamps = $timestamps;
    }
    public function retrieveByCredentials(array $credentials)
    {
       // find user by device id
        $user = User::where('device_id', $credentials['device_id'])->first();

        // validate
        return $user;
    }

    public function validateCredentials(UserContract $user, array $credentials)
    {
        /*
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
        */
        return true;
    }
}