<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\CVResource;
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
        return view('cv.index');
    }
    public function add()
    {

    }
    public function store(Request $request) 
    {
        $name = $request->all()['name'];
        $images = $request->file('images');
        $cvPhotos = [];
        foreach ($images as $image) {
            $model = new CVResource();
            $model->name = $name;
            $cvPhotos[] = $model->photo = $this->photoUploadService->uploadPhoto($image, 'opencv');
            $model->save();
        }
        print_r($cvPhotos); exit();
        // $this->openCVService->updateModel($cvPhotos, $name);

        return response()->json([
            'success' => true
        ]);
    }
}
