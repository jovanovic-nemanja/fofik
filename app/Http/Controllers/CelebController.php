<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\UserService;
use App\Services\CelebService;
use App\Services\PhotoUploadService;
use App\Services\CloudApiService;
use App\Services\OpenCVService;
use App\Services\CVDNNService;
use App\Services\HistoryService;
use App\Services\DocumentService;

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
    protected $cvDNNService;

    public function __construct(UserService $userService, CelebService $celebService, PhotoUploadService $photoUploadService, CloudApiService $cloudApiService, OpenCVService $openCVService, CVDNNService $cvDNNService, HistoryService $historyService, DocumentService $documentService) 
    {
        $this->userService = $userService;
        $this->celebService = $celebService;
        $this->photoUploadService = $photoUploadService;
        $this->cloudApiService = $cloudApiService;
        $this->openCVService = $openCVService;
        $this->cvDNNService = $cvDNNService;
        $this->historyService = $historyService;
        $this->documentService = $documentService;
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
        $data = [];

        $recommends = $this->celebService->getRecommendations($keyword, $lang);
        foreach ($recommends as $each) {
            $data[] = $each['name'];
        }
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
            $name = $this->vision($photo);
            if (!$name) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Can not find celebrity'    
                ]);
            }
            $this->openCVService->store([
                'name' => $name,
                'photos' => [$photo]
            ]);
            $params['name'] = $name;
        }
        $data = $this->store($params);
        if (!$data) {
            return response()->json([
                'success' => false, 
                'message' => 'Can not find celebrity'
            ]);
        }
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
        $user = $this->userService->getByID(auth('api')->user()->id);
        $params['lang'] = $user->lang;
        $celebrity = $this->celebService->getModel(['external_id' => $params['external_id']]);
        if (!$celebrity) {
            return response()->json([
                'success' => false,
                'message' => 'Can not find celebrity'
            ]);
        }
        $params['name'] = $this->celebService->getName($celebrity->id);
        $tmdb = $this->cloudApiService->tmdb($params);
        return response()->json([
            'success' => true,
            'data' => $tmdb
        ]);
    }

    public function movieDetail($id)
    {
        $imdb = $this->cloudApiService->imdb($id);
        return response()->json([
            'success' => true,
            'data' => $imdb
        ]);
    }
    /**
     * Search all videos related with celebrity
     * @param string name
     * @return \Illuminate\Http\JsonResponses
     */
    public function video(Request $request)
    {
        $params = $request->all();
        $user = $this->userService->getByID(auth('api')->user()->id);
        $params['lang'] = $user->lang;
        $celebrity = $this->celebService->getModel(['external_id' => $params['external_id']]);
        if (!$celebrity) {
            return response()->json([
                'success' => false,
                'message' => 'Can not find celebrity'
            ]);
        }
        $params['name'] = $this->celebService->getName($celebrity->id);
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
        $user = $this->userService->getByID(auth('api')->user()->id);
        $params['lang'] = $user->lang;
        $celebrity = $this->celebService->getModel(['external_id' => $params['external_id']]);
        if (!$celebrity) {
            return response()->json([
                'success' => false,
                'message' => 'Can not find celebrity'
            ]);
        }
        $params['name'] = $this->celebService->getName($celebrity->id);
        $news = $this->cloudApiService->bingNews($params);
        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function event(Request $request)
    {
        $params = $request->all();
        $user = $this->userService->getByID(auth('api')->user()->id);
        $params['lang'] = $user->lang;
        $celebrity = $this->celebService->getModel(['external_id' => $params['external_id']]);
        if (!$celebrity) {
            return response()->json([
                'success' => false,
                'message' => 'Can not find celebrity'
            ]);
        }
        $params['name'] = $this->celebService->getName($celebrity->id);
        $news = $this->cloudApiService->predictHQ($params);
        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }
    public function birthday(Request $request)
    {
        $celebs = $this->cloudApiService->rapidBirth();
        return response()->json([
            'success' => true,
            'data' => $celebs
        ]);
    }
    public function popular(Request $request)
    {
        $uid = auth('api')->user()->id;
        $user = $this->userService->getByID($uid);
        $histories = $this->historyService->getHRSearchHistory($uid);
        $data = [];
        foreach ($histories as $item)
        {
            $data[] = $this->celebService->getBriefInfo($item->celeb_id, $user->lang);
        }
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    public function recentSearch(Request $request)
    {
        $uid = auth('api')->user()->id;
        $user = $this->userService->getByID($uid);
        $histories = $this->historyService->getRecentSearchHistory($uid);
        $data = [];
        foreach ($histories as $item)
        {
            $data[] = $this->celebService->getBriefInfo($item->celeb_id, $user->lang);
        }
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    /**
     * 
     */
    protected function store($params)
    {
        $user = $this->userService->getByID(auth('api')->user()->id);
        $params['lang'] = $lang = $user->lang;
        $detail = $this->celebService->getDetailModel($params);
        if (!$detail) {
            $wiki = $this->cloudApiService->wikiBase($params);
            if (!$wiki)
                return null;
            $celebrity = $this->celebService->getModel(['external_id' => $wiki['external_id']]);
            if (!$celebrity) {
                $celebrity = new Celebrity();
                $celebrity->external_id = @$wiki['external_id'] ? $wiki['external_id'] : '';
                $celebrity->photo_url = @$wiki['photo_url'] ? $wiki['photo_url'] : '';
                $celebrity->birth_date = @$wiki['birth_date'] ? $wiki['birth_date'] : '';
                $celebrity->death_date = @$wiki['death_date'] ? $wiki['death_date'] : '';
                $celebrity->facebook = @$wiki['facebook'] ? $wiki['facebook'] : '';
                $celebrity->instagram = @$wiki['instagram'] ? $wiki['instagram'] : '';
                $celebrity->twitter = @$wiki['twitter'] ? $wiki['twitter'] : '';
                $celebrity->save();
            }
            $detail = new CelebDetail(); 
            $detail->celeb_id = $celebrity->id;
            $detail->en_name = @$wiki['en_name'] ? $wiki['en_name'] : '';
            $detail->natl_name = @$wiki['natl_name'] ? $wiki['natl_name'] : '';
            $detail->comment = @$wiki['comment'] ? $wiki['comment'] : '';
            $detail->born_in = @$wiki['born_in'] ? $wiki['born_in'] : '';
            $detail->citizen_ship = @$wiki['citizen_ship'] ? implode('&', $wiki['citizen_ship']) : '';
            $detail->spouse = @$wiki['spouse'] ? implode('&', array_map(function($item){return implode('|', $item);}, $wiki['spouse'])) : '';
            $detail->children = @$wiki['child'] ? implode('&', $wiki['child']) : '' ;
            $detail->education = @$wiki['education'] ? implode('&', $wiki['education']) : '';
            $detail->occupation = @$wiki['occupation'] ? implode('&', $wiki['occupation']) : '';
            $detail->net_worth = @$wiki['net_worth'] ? $wiki['net_worth'] : '';
            $detail->description = @$wiki['description'] ? $wiki['description'] : '';
            $detail->lang = $params['lang'];
            $detail->save();
        } else {
            $celebrity = $this->celebService->getModel(['id' => $detail->celeb_id]);
        }

        $user->histories()->attach($celebrity, ['created_on' => Date('Y-m-d')]);

        $data = $this->celebService->getDetailInfo($celebrity->id, $lang);
        $this->documentService->store($data->natl_name);
        
        $data->favorite = $user->hasInFavList($celebrity->id);
        // $data['video'] = $this->cloudApiService->youtube($params); 
        // $data['movie'] = $this->cloudApiService->imdb($params);
        // $data['news'] = $this->cloudApiService->bingNews($params);
        return $data;
    }

    protected function vision($photo)
    {
        if (!$photo)
            return null;
        if ($name = $this->openCVService->recognize($photo)) {
        // if ($name = $this->cvDNNService->recognize($photo)) {
            return $name; 
        } else {
            if ($name = $this->cloudApiService->googleCV($photo)) {
                $this->historyService->addVisionHistory([
                    'vision_target' => $name,
                    'vision_service' => 'google',
                    'created_on' => Date('Y-m-d')
                ]);
                return $name;
            } else {
                if ($name = $this->cloudApiService->amazonCV($photo)) {
                    $this->historyService->addVisionHistory([
                        'vision_target' => $name,
                        'vision_service' => 'amazon',
                        'created_on' => Date('Y-m-d')
                    ]);
                    return $name;
                }
            }
        }
        return null;
    }
}
