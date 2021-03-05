<?php

namespace App\Services;

use Closure;
use DB;
use App\Models\Celebrity;
use App\Models\CelebDetail;
use App\Models\AutoComplete;

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
        $lang = $params['lang'];
        $externalID = @$params['external_id'];
        $name = @$params['name'];
        if ($externalID) {
            $model = $this->getModel(['external_id' => $externalID]);
            if (!$model)
                return null;
            return CelebDetail::where('celeb_id', $model->id)
                        ->where('lang', $lang)
                        ->first();
        }
        if ($name) {
            return DB::table('ff_celeb_detail')
				    ->where(function($query) use ($name, $lang) {
						$query->where('en_name', $name);
						$query->where('lang', $lang);
					})
					->orWhere(function($query) use ($name, $lang) {
						$query->where('natl_name', $name);
						$query->where('lang', $lang);
					})
					->first();
        }
        return null;
    }

    public function getDetailInfo($id, $lang)
    {
        $model = DB::table('ff_celebs as A')
            ->join('ff_celeb_detail as B', 'A.id', '=', 'B.celeb_id')
            ->select('A.*', 'B.en_name', 'B.natl_name', 'B.comment', 'B.born_in', 'B.citizen_ship', 'B.spouse', 'B.children', 'B.education', 'B.occupation', 'B.net_worth', 'B.description', 'B.lang')
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

    public function getBriefInfo($id, $lang)
    {
        $model = DB::table('ff_celebs as A')
            ->join('ff_celeb_detail as B', 'A.id', '=', 'B.celeb_id')
            ->select('A.external_id', 'A.photo_url', 'A.facebook', 'A.instagram', 'A.twitter', 'B.natl_name', 'B.comment')
            ->where('B.lang', '=', $lang)
            ->where('B.celeb_id', '=', $id)
            ->first();
        return $model;
    }
    public function getRecommendations($keyword, $lang)
    {
        return AutoComplete::select('celeb_name as name')->where([
            ['celeb_name', 'LIKE', '%'.$keyword.'%']
        ])->get();
    }

    public function getName($id)
    {   
        $detail = CelebDetail::where('celeb_id', $id)->first();
        return $detail->en_name ? $detail->en_name : $detail->natl_name;
    }
}
