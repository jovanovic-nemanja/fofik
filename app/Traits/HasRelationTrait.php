<?php

namespace App\Traits;

use App\Models\Person;

trait HasRelationTrait {

    public function relatives()
    {
        return $this->belongsToMany(Person::class,'ff_relations', 'celeb_id', 'person_id')->withPivot('rel_type');
    }
}