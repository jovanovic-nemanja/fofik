<?php

namespace App\Services;

use Closure;
use DB;
use App\Models\AutoComplete;

class DocumentService extends BaseService
{
    public function __construct()
    {
    }

    public function setCelebNames($params)
    {
        $names = $params['names'];
        foreach ($names as $name)
        {
            if ($name)
                $this->store($name);
        }
    }
    public function store($name)
    {
        $record = AutoComplete::where(['celeb_name' => $name])->first();
        if (!$record)
        {
            $record = new AutoComplete();
            $record->celeb_name = $name;
            $record->save();
        }
    }
}
