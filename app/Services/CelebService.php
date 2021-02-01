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
        $model = DB::table('ff_celebs as A')
            ->join('ff_celeb_detail as B', 'A.id', '=', 'B.celeb_id')
            ->select('A.*', 'B.en_name', 'B.natl_name', 'B.born_in', 'B.citizen_ship', 'B.spouse', 'B.children', 'B.education', 'B.occupation', 'B.net_worth', 'B.description', 'B.lang')
            ->where('B.lang', '=', $lang)
            ->where('B.celeb_id', '=', $id)
            ->first();
        $model->spouse = array_map(function($item){return explode('|', $item);}, explode('&', $model->spouse));
        $model->children = explode('&', $model->children);
        $model->education = explode('&', $model->education);
        $model->occupation = explode('&', $model->occupation);
        $model->citizen_ship = explode('&', $model->citizen_ship);
        $model->description = json_decode($model->description);
        return $model;
    }
    public function getRecommendations($keyword, $lang)
    {
        return CelebDetail::select('natl_name as name')->where([
            ['natl_name', 'LIKE', '%'.$keyword.'%'],
            ['lang', '=', $lang]
        ])->get();
    }

    public function getName($id)
    {   
        $detail = CelebDetail::where('celeb_id', $id)->first();
        return $detail->en_name ? $detail->en_name : $detail->natl_name;
    }
}
