<?php

namespace App\Services;

use Closure;
use DB;
use App\Models\Celebrity;

class CelebService extends BaseService
{
    public function __construct()
    {
    }

    public function getModel($params)
    {   
        return Celebrity::where($params)->firstOrFail();
    }
    public function getPersonalInfo($id)
    {
        $celebrity = Celebrity::findOrFail($id);
        
        $relatives = $celebrity->relatives;
        $tmpArr = [];
        foreach ($relatives as $item) {
            $relType = $item->pivot->rel_type;
            if (!@$tmpArr[$relType])
                $tmpArr[$relType] = [];
            $tmpArr[$relType][] = $item->name;
        }
        $celebrity->relatives = $tmpArr;

        return $celebrity;
    }
}
