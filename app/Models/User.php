<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;

use App\Traits\HasAddonTrait;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasAddonTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ff_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'external_id',
        'name',
        'email',
        'password',
        'social_id',
        'social_site',
        'lang',
        'platform',
        'device_id',
        'fb_token',
        'access_token'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $primaryKey = 'id';

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }    
}
