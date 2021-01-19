<?php
namespace App;

use Fenos\Notifynder\Notifable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Admin extends Authenticatable
{
    use Notifiable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ff_admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'facebook',
        'google',
        'twitter',
        'instagram',
        'pinerest',
        'contact_address',
        'contact_email',
        'contact_phone',
        'admin_logo',
        'last_ip',
        'current_ip',
        'last_login',
        'current_login',
        'modified_on',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $appends = ['avatar'];
    protected $primaryKey = 'id';
    public function getAvatarattribute()
    {
        $admin_logo = $this->admin_logo ? Storage::url($this->admin_logo) : '/images/default_avatar.jpg';
        return $admin_logo;
    }
}
