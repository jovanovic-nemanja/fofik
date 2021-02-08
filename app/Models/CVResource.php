<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CVResource extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ff_cv_resource';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'photo',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $primaryKey = 'id';
}
