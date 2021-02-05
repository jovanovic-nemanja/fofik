<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Services\HistoryService;

class HistoryController extends Controller
{
    //
    protected $historyService;
    public function __construct(HistoryService $historyService)
    {
        $this->historyService = $historyService;
    }

    public function visionHistory(Request $request)
    {   
        $params = $request->all();
        $year = @$params['year'];
        $month = @$params['month'];

        if (!$year || !$month) {
            return response()->json([
                'success' => false
            ]);
        }

        $data = $this->historyService->getVisionHistory($params);
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
