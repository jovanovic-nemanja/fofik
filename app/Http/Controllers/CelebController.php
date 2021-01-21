<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\UserService;
use App\Services\CelebService;
use App\Services\PhotoUploadService;
use App\Services\CloudApiService;
use App\Services\OpenCVService;

use App\Models\Celebrity;
use App\Models\Favorite;

class CelebController extends Controller
{
    //
    protected $userService;
    protected $celebService;
    protected $photoUploadService;
    protected $cloudApiService;
    protected $openCVService;

    public function __construct(UserService $userService, CelebService $celebService, PhotoUploadService $photoUploadService, CloudApiService $cloudApiService, OpenCVService $openCVService) 
    {
        $this->userService = $userService;
        $this->celebService = $celebService;
        $this->photoUploadService = $photoUploadService;
        $this->cloudApiService = $cloudApiService;
        $this->openCVService = $openCVService;
    }
    /**
     * Search celebrity
     * @param string name
     * @param file photo
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request) 
    {
        $params = $request->all();
        if (@$params['name']) {
            return $this->store($params);
        }
        if ($photo = $request->file('photo')) {
            $photo = $this->photoUploadService->uploadPhoto($photo);
            $name = $this->openCVService->recognize($photo);
            if (!$name) {
                $name = $this->cloudApiService->googleCV($photo);
                if (!$name) {
                    $name = $this->cloudApiService->amazonCV($photo);
                    if (!$name) {
                        return response()->json([
                            'success' => false, 
                            'message' => 'Matched data does not exist'
                        ]);
                    }
                }
            }
            ///
            $params['name'] = $name;
            ///
            return $this->store($params);
        } 
        return response()->json(['success' => true]);
    }

    public function recommend(Request $request)
    {
        $params = $request->all();
        if (!@$params['keyword']) {
            return response()->json([
                'success' => false,
                'message' => 'Recommendation failed'
            ]);
        }
        $recs = ["Tom Hanks", "Tom Benette", "Tomson Wall"];
        return response()->json([
            'success' => true,
            'data' => $recs
        ]);
    }
    protected function store($params)
    {
        $user = $this->userService->getByID(auth('api')->user()->id);
        $celebrity = $this->celebService->getModel(['fullname' => $params['name']]);
        if (!$celebrity) {

            $this->cloudApiService->wiki($params);
            $this->cloudApiService->youtube($params);
            $this->cloudApiService->imdb($params);
            $this->cloudApiService->gNews($params);
            $this->cloudApiService->songkick($params);
            $this->cloudApiService->serp($params);

            $celebrity = new Celebrity();
            // 
            $celebrity->save();
        }

        $user->histories()->attach($celebrity, ['created_on' => Date('Y-m-d')]);
        $data = $this->celebService->getPersonalInfo($celebrity->id);
        return response()->json(['success' => true, 'data' => $data]);
    }
}
