<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CVPhoto extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ff_news';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'photo_url',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $primaryKey = 'id';
}
