<?php

namespace App\Http\Controllers;

use ZipArchive;
use voku\helper\HtmlDomParser;

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
        // $list = $this->openCVService->getAllRecords();
        $list = $this->openCVService->getCVResource();
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
    public function photos(Request $request)
    {
        $params = $request->all();
        if (!isset($params['id']))
            return response()->json([
                'success' => false
            ]);
        $photos = $this->openCVService->getCVPhotos($params['id']);
        return response()->json([
            'success' => true,
            'photos' => $photos
        ]);
    }
    public function googlePhotos(Request $request)
    {
        $params = $request->all();
        if (!isset($params['name'])) {
            echo "Please select celebrity name"; die();
        }
        $search_query = urlencode(trim($params['name']));
        $html = HtmlDomParser::file_get_html('http://images.google.com/images?as_q='. $search_query .'&hl=en&imgtbs=z&btnG=Search+Images&as_epq=&as_oq=&as_eq=&imgtype=&imgsz=m&imgw=&imgh=&imgar=&as_filetype=&imgc=&as_sitesearch=&as_rights=&safe=images&as_st=y'); 
        $images = $html->find('div>img');

        $tmpFile = 'images/file.zip';

        $zip = new ZipArchive;
        $zip->open($tmpFile, ZipArchive::CREATE);
        foreach ($images as $key => $image) {
            // download file
            $srcimg = $image->src;
            $fileContent = file_get_contents($srcimg);
            $zip->addFromString($key.'.jpg', $fileContent);
        }
        $zip->close();

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=file.zip');
        header('Content-Length: ' . filesize($tmpFile));
        readfile($tmpFile);

        unlink($tmpFile);
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
