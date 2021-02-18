<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\CVResource;
use App\Models\CVPhoto;
use App\Services\PhotoUploadService;
use App\Services\OpenCVService;

class CVController extends Controller
{
    protected $photoUploadService;
    protected $openCVService;
    //
    public function __construct(PhotoUploadService $photoUploadService, OpenCVService $openCVService)
    {
        $this->photoUploadService = $photoUploadService;
        $this->openCVService = $openCVService;
    }
    public function index()
    {
        $list = $this->openCVService->getAllRecords();
        return view('cv.index')
                ->with('celebs', $list);
    }
    public function add()
    {

    }
    public function store(Request $request) 
    {
        $name = $request->all()['name'];
        $images = $request->file('images');
        $cvPhotos = [];

        if (!isset($name) || !isset($images)) {
            return response()->json([
                'success' => false
            ]);
        }
        
        $model = CVResource::where(['name' => $name])->first();
        if (!$model) {
            $model = new CVResource();
            $model->name = $name;
            $model->save();
        }
        $cvID = $model->id;

        foreach ($images as $image) {
            $model = new CVPhoto();
            $model->cv_id = $cvID;
            $cvPhotos[] = $model->photo = $this->photoUploadService->uploadPhoto($image, 'opencv');
            $model->save();
        }
        $this->openCVService->updateModel($cvPhotos, $cvID);
        return response()->json([
            'success' => true
        ]);
    }

    // Test recognition
    public function test(Request $request)
    {
        $name = null;
        if ($photo = $request->file('photo')) {
            $photo = $this->photoUploadService->uploadPhoto($photo);
            $name = $this->openCVService->recognize($photo);
        }
        return response()->json([
            'name' => $name ? $name : 'can not recognize',
        ]);
    }
}
