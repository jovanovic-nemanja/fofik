<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\UserService;
use App\Services\CelebService;
use App\Services\PhotoUploadService;
use App\Services\CloudApiService;
use App\Services\OpenCVService;

use App\Models\Celebrity;
use App\Models\CelebDetail;
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
     * Search all celebrities who have name similar with keyword
     * @param string keyword
     * @return \Illuminate\Http\JsonResponse
     */
    public function recommend(Request $request)
    {
        $params = $request->all();
        $user = $this->userService->getByID(auth('api')->user()->id);
        $lang = $user->lang;

        $keyword = @$params['keyword'];
        if (!$keyword) {
            return response()->json([
                'success' => false,
                'message' => 'Recommendation failed'
            ]);
        }
        $data = $this->celebService->getRecommendations($keyword, $lang);
        // $data = ["Tom Hanks", "Tom Benette", "Tomson Wall"];
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
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
        if ($photo = $request->file('photo')) {
            $photo = $this->photoUploadService->uploadPhoto($photo);
            $params['photo'] = $photo;
        }
        $name = $this->vision($params);
        if (!$name) {
            return response()->json([
                'success' => false, 
                'message' => 'Can not find celebrity'
            ]);
        }
        $params['name'] = $name;
        $data = $this->store($params);
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    /**
     * Search all movies related with celebrity
     * @param string name
     * @return \Illuminate\Http\JsonResponse
     */
    public function movie(Request $request)
    {
        $params = $request->all();
        $celebrity = $this->celebService->getModel(['external_id' => $params['external_id']]);
        if (!$celebrity) {
            return response()->json([
                'success' => false,
                'message' => 'Can not find celebrity'
            ]);
        }
        $imdb = $this->cloudApiService->imdb($params);
        return response()->json([
            'success' => true,
            'data' => $imdb
        ]);
    }
    /**
     * Search all videos related with celebrity
     * @param string name
     * @return \Illuminate\Http\JsonResponse
     */
    public function video(Request $request)
    {
        $params = $request->all();
        $name = $params['name'];
        $celebrity = $this->celebService->getModel(['external_id' => $params['external_id']]);
        if (!$celebrity) {
            return response()->json([
                'success' => false,
                'message' => 'Can not find celebrity'
            ]);
        }
        $youtube = $this->cloudApiService->youtube($params);
        return response()->json([
            'success' => true,
            'data' => $youtube
        ]);
    }
    /**
     * Search all news related with celebrity
     * @param string name
     * @return \Illuminate\Http\JsonResponse
     */
    public function news(Request $request)
    {
        $params = $request->all();
        $name = $params['name'];
        $celebrity = $this->celebService->getModel(['external_id' => $params['external_id']]);
        if (!$celebrity) {
            return response()->json([
                'success' => false,
                'message' => 'Can not find celebrity'
            ]);
        }
        $news = $this->cloudApiService->bing($params);
        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function section(Request $request)
    {
        $user = $this->userService->getByID(auth('api')->user()->id);
        $params = $request->all();
        $params['lang'] = $user->lang;
        $data = $this->cloudApiService->wikiSection($params);
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    protected function store($params)
    {
        $user = $this->userService->getByID(auth('api')->user()->id);
        $params['lang'] = $lang = $user->lang;
        $detail = $this->celebService->getDetailModel($params);
        if (!$detail) {
            $wiki = $this->cloudApiService->wikiBase($params);
            $celebrity = $this->celebService->getModel(['external_id' => $wiki['external_id']]);
            if (!$celebrity) {
                $celebrity = new Celebrity();
                $celebrity->external_id = @$wiki['external_id'];
                $celebrity->photo_url = @$wiki['photo_url'];
                $celebrity->birth_date = @$wiki['birth_date'];
                $celebrity->death_date = @$wiki['death_date'];
                $celebrity->facebook = @$wiki['facebook'];
                $celebrity->instagram = @$wiki['instagram'];
                $celebrity->twitter = @$wiki['twitter'];
                $celebrity->save();
            }

            $detail = new CelebDetail();
            $detail->celeb_id = $celebrity->id;
            $detail->en_name = @$wiki['en_name'];
            $detail->natl_name = @$wiki['natl_name'];
            $detail->born_in = @$wiki['born_in'];
            $detail->citizen_ship = @$wiki['citizen_ship'];
            $detail->spouse = @$wiki['spouse'];
            $detail->children = @$wiki['child'];
            $detail->education = @$wiki['education'];
            $detail->occupation = @$wiki['occupation'];
            $detail->net_worth = @$wiki['net_worth'];
            $detail->lang = $params['lang'];
            $detail->save();
        } else {
            $celebrity = $this->celebService->getModel(['id' => $detail->celeb_id]);
        }

        $user->histories()->attach($celebrity, ['created_on' => Date('Y-m-d')]);

        $data = [];
        $data['about'] = $this->celebService->getDetailInfo($celebrity->id, $lang);
        $data['video'] = $this->cloudApiService->youtube($params);
        $data['movie'] = $this->cloudApiService->imdb($params);
        $data['news'] = $this->cloudApiService->bing($params);
        return $data;
    }
    protected function vision($params)
    {
        if (@$params['name']) {
            return $params['name'];
        }
        if ($photo = @$params['photo']) {
            $name = $this->openCVService->recognize($photo);
            if (!$name) {
                $name = $this->cloudApiService->googleCV($photo);
                if (!$name) {
                    $name = $this->cloudApiService->amazonCV($photo);
                    if (!$name)
                        return null;
                }
            }
            return $name;
        }
    }
}
