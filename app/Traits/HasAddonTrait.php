<?php

namespace App\Traits;

use App\Models\Celebrity;

trait HasAddonTrait {
    public function favorites() 
    {
      return $this->belongsToMany(Celebrity::class,'ff_favorites', 'user_id', 'celeb_id');
    }
    public function histories() 
    {
      return $this->belongsToMany(Celebrity::class,'ff_search_histories', 'user_id', 'celeb_id');
    }
}