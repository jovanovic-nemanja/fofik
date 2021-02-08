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
    protected $table = 'ff_cv_photos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cv_id',
        'photo',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $primaryKey = 'id';
}
