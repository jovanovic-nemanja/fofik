<?php

namespace App\Services;

use Closure;
use DB;
use App\Models\VisionHistory;
class HistoryService extends BaseService
{
    public function __construct()
    {
    }

    public function addVisionHistory($data)
    {
        return VisionHistory::create($data);
    }
    public function getVisionHistory($params)
    {
        $year = $params['year'];
        $month = $params['month'];
        
        $data = [];
        $daily = [
            'google' => [],
            'amazon' => [],
        ];
        $result = DB::table('ff_vision_history')
            ->select('*')
            ->whereYear('created_on', '=', $year)
            ->whereMonth('created_on', '=', $month)
            ->orderBy('created_on')
            ->get();
        
        foreach ($result as $each) {
            $service = $each->vision_service;
            $date = $each->created_on;
            if (!@$daily[$service][$date])
                $daily[$service][$date] = 0;
            $daily[$service][$date]++;
        }

        $monthly = [
            'google' => [],
            'amazon' => []
        ];

        for ($month = 1; $month <= 12; $month++) {
            $result = DB::table('ff_vision_history')
            ->select('vision_service as service', DB::raw('count(*) as count'))
            ->whereYear('created_on', '=', $year)
            ->whereMonth('created_on', '=', $month)
            ->groupBy('vision_service')
            ->get();
            $date = $year.'-'.$month;
            if (@$result[0])
                $monthly[$result[0]->service][$date] = $result[0]->count;
            if (@$result[1])
                $monthly[$result[1]->service][$date] = $result[1]->count;
        }
        $data['daily'] = $daily;
        $data['monthly'] = $monthly;
        return $data;
    }

    public function getHRSearchHistory($uid)
    {
        return DB::table('ff_search_histories')
                ->select('celeb_id', DB::raw('count(*) as s_times'))
                ->where('user_id', $uid)
                ->groupBy('celeb_id')
                ->orderBy('s_times', 'DESC')
                ->offset(0)
                ->take(5)
                ->get();
    }   
    public function getRecentSearchHistory($uid)
    {
        return DB::table('ff_search_histories')
                ->where('user_id', $uid)
                ->latest('created_on')
                ->first();
    }
}
