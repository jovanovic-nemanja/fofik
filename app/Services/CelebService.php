<?php

namespace App\Services;

use Closure;
use DB;
use App\Models\Celebrity;
use App\Models\CelebDetail;

class CelebService extends BaseService
{
    public function __construct()
    {
    }

    public function getModel($params)
    {   
        return Celebrity::where($params)->first();
    }

    public function getDetailModel($params)
    {
        return CelebDetail::where('en_name', $params['name'])
                          ->orWhere('natl_name', $params['name'])
                          ->where('lang', $params['lang'])
                          ->first();      
    }

    public function getDetailInfo($id, $lang)
    {
        return DB::table('ff_celebs as A')
            ->join('ff_celeb_detail as B', 'A.id', '=', 'B.celeb_id')
            ->select('A.*', 'B.en_name, B.natl_name, B.born_in, B.citizen_ship, B.spouse, B.children, B.education, B.occupation, B.net_worth, B.lang')
            ->where('B.lang', '=', $lang)
            ->where('B.celeb_id', '=', $id)
            ->first();
    }
    public function getRecommendations($keyword, $lang)
    {
        return CelebDetail::select('fullname')->where([
            ['fullname', 'LIKE', '%'.$keyword.'%'],
            ['lang', '=', $lang]
        ])->get();
    }
}
