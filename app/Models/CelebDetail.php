<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CelebDetail extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ff_celeb_detail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'celeb_id',
        'en_name',
        'natl_name',
        'comment',
        'born_in',
        'citizen_ship',
        'spouse',
        'children',
        'education',
        'occupation',
        'net_worth',
        'award',
        'description',
        'lang'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $primaryKey = 'id';
}
