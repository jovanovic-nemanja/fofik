<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Services\UserService;
use App\Services\CelebService;

class FavoriteController extends Controller
{
    //
    protected $userService;
    protected $celebService;
    
    public function __construct(UserService $userService, CelebService $celebService)
    {   
        $this->userService = $userService;
        $this->celebService = $celebService;
    }
    /**
     * Add celebrity to favorite list
     * @param int celeb_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $params = $request->all();

        if (!@$params['external_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $user = $this->userService->getByID(auth('api')->user()->id);
        $celebrity = $this->celebService->getModel(['external_id' => $params['external_id']]);
        if (!$celebrity) {
            return response()->json([
                'success' => false,
                'message' => 'Can not find celebrity'
            ]);
        }
        $user->favorites()->attach($celebrity, ['created_on' => Date('Y-m-d')]);
        return response()->json(['success' => true]);
    }
    /**
     * Delete celebrity from favorite list
     * @param int celeb_id
     * @return \Illuminate\Http\JsonResponse
     */ 
    public function destroy(Request $request)
    { 
        $params = $request->all();
        
        if (!@$params['external_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }
        
        $user = $this->userService->getByID(auth('api')->user()->id);
        $celebrity = $this->celebService->getModel(['external_id' => $params['external_id']]);
        if (!$celebrity) {
            return response()->json([
                'success' => false,
                'message' => 'Can not find celebrity'
            ]);
        }
        $user->favorites()->detach($celebrity);
        return response()->json(['success' => true]);
    }
    /**
     * Get all of favorite information
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {   
        $user = $this->userService->getByID(auth('api')->user()->id);
        $lang = $user->lang;
        $favorites = $user->favorites;
        $data = [];
        foreach ($favorites as $item) {
            $data[] = $this->celebService->getBriefInfo($item->id, $lang);
        }
        return response()->json(['success'=>true, 'data' => $data]);
    }
}
