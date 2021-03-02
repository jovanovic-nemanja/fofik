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
use App\Services\CloudApiService;
class CVController extends Controller
{
    protected $photoUploadService;
    protected $openCVService;
    protected $cloudApiService;
    //
    public function __construct(PhotoUploadService $photoUploadService, OpenCVService $openCVService, CloudApiService $cloudApiService)
    {
        $this->photoUploadService = $photoUploadService;
        $this->openCVService = $openCVService;
        $this->cloudApiService = $cloudApiService;
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
        $photos = [];
        if (!isset($name) || !isset($images)) {
            return response()->json([
                'success' => false
            ]);
        }
        foreach ($images as $image)
        {
            $photos[] = $this->photoUploadService->uploadPhoto($image, 'opencv');
        }
        $this->openCVService->store([
            'name' => $name,
            'photos' => $photos
        ]);
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
        $tmpFile = 'images/file.zip';

        $zip = new ZipArchive;
        $zip->open($tmpFile, ZipArchive::CREATE);
        // $html = HtmlDomParser::file_get_html('http://images.google.com/images?as_q='. $search_query .'&hl=en&imgtbs=z&btnG=Search+Images&as_epq=&as_oq=&as_eq=&imgtype=&imgsz=l&imgw=&imgh=&imgar=&as_filetype=&imgc=&as_sitesearch=&as_rights=&safe=images&as_st=y'); 
		//$html = HtmlDomParser::file_get_html('https://www.google.com/search?q=Tom%20Hanks&tbm=isch');
        
        $images = $this->cloudApiService->bingImages($params);
        $image_count = 0;
        foreach ($images as $key => $image)
        {
            $fileContent = @file_get_contents($image);
			if ($fileContent !== false)
			{
				$fileContent = base64_decode(base64_encode($fileContent));
				
				//print_r($fileContent);exit;
				$randname = $key.'.png';
				$randname = 'images/'.$randname;
				file_put_contents($randname, $fileContent);
				$im = imagecreatefromstring($fileContent);
				$faces = $this->openCVService->detectFaces($randname);
				if (count($faces) > 0)
					$image_count++;
				if ($image_count > 20)
					break;
				foreach ($faces as $face)
				{
					$randname = uniqid();
					$randname .= '.png';
					$crop = imagecrop($im, (array)$face);
					ob_start();
					imagepng($crop);
					$contents = ob_get_contents();
					ob_end_clean();
					imagedestroy($crop);
					$zip->addFromString($randname, $contents);
					// imagepng($crop, $randname); 
				}
			}
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
