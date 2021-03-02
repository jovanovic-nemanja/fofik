<?php

namespace App\Services;

use Closure;
use DB;

use App\Models\CVPhoto;
use App\Models\CVResource;

use CV\Face\LBPHFaceRecognizer, CV\CascadeClassifier, CV\Scalar, CV\Point;
use function CV\{imread, imwrite, cvtColor, equalizeHist};
use const CV\{COLOR_BGR2GRAY};

class OpenCVService extends BaseService
{
    protected $bTrained;
    public function __construct()
    {
        $this->bTrained = false;
    }
    public function recognize($photo)
    {
        $faceClassifier = $this->getClassifier('lbp');
        $faceRecognizer = $this->getModel();

        if (!$this->bTrained)
            return null;

        $src = imread(public_path($photo));    
        $gray = cvtColor($src, COLOR_BGR2GRAY);
        $faceClassifier->detectMultiScale($gray, $faces);
        equalizeHist($gray, $gray);
        if (count($faces) == 0)
            return null;
        $face = $faces[0];
        $faceImage = $gray->getImageROI($face); // face coordinates to image
        $faceLabel = $faceRecognizer->predict($faceImage, $faceConfidence);
        if ($faceConfidence < 80) {
            $cvData = CVResource::where(['id' => $faceLabel])->first();
            return isset($cvData) ? $cvData->name : null;
        }
        return null;
    }
    public function getModel()
    {
        $faceRecognizer = LBPHFaceRecognizer::create();
        if (file_exists(public_path('opencv/celeb_recognize_model.xml'))) {
            $this->bTrained = true;
            $faceRecognizer->read(public_path('opencv/celeb_recognize_model.xml'));
        } else {
            $this->bTrained = false;
        }
        return $faceRecognizer;
    }
    public function getClassifier($type)
    {
        $faceClassifier = new CascadeClassifier();
		if ($type == 'lbp')
        	$faceClassifier->load(public_path('opencv/models/lbpcascades/lbpcascade_frontalface_improved.xml'));
		else if ($type == 'haar')
			$faceClassifier->load(public_path('opencv/models/haarcascades/haarcascade_frontalface_alt_tree.xml'));
        return $faceClassifier;
    }
    public function updateModel($photos, $label)
    {
        $faceClassifier = $this->getClassifier('lbp');
        $faceRecognizer = $this->getModel();
        $faceImages = $faceLabels = [];
        foreach ($photos as $photo)
        {
            $src = imread(public_path($photo));    
            $gray = cvtColor($src, COLOR_BGR2GRAY);
            $faceClassifier->detectMultiScale($gray, $faces);    
            equalizeHist($gray, $gray);
            foreach ($faces as $k => $face) {
                $faceImages[] = $gray->getImageROI($face); // face coordinates to image
                $faceLabels[] = $label;
            }
        }
        if (!$this->bTrained)
            $faceRecognizer->train($faceImages, $faceLabels);
        else
            $faceRecognizer->update($faceImages, $faceLabels);
        $faceRecognizer->write(public_path('opencv/celeb_recognize_model.xml'));
    }
	public function detectFaces($photo)
    {
        $faceClassifier = $this->getClassifier('haar');
		try {
        	$src = imread(public_path($photo));
			$gray = cvtColor($src, COLOR_BGR2GRAY);
			$faceClassifier->detectMultiScale($gray, $faces);       
			return $faces;
		} catch (\Exception $e) {
			return [];
		}
    }
    public function getAllRecords()
    {
        $records = CVResource::all();
        $data = [];
        foreach ($records as $record)
        {
            $rrs = CVPhoto::where(['cv_id' => $record->id])->get();
            $data[$record->name] = [];
            foreach ($rrs as $rr) 
            {
                $data[$record->name][] = $rr->photo;
            }
        }
        return $data;
    }
    public function getCVResource()
    {
        return CVResource::all();
    }
    public function getCVPhotos($id)
    {
        $records = CVPhoto::where(['cv_id' => $id])->get();
        $photos = [];
        foreach ($records as $item)
        {
            $photos[] = $item->photo;
        }
        return $photos;
    }
    public function store($params)
    {
        $name = $params['name'];
        $photos = $params['photos'];
        $model = CVResource::where(['name' => $name])->first();
        if (!$model) {
            $model = new CVResource();
            $model->name = $name;
            $model->save();
        }
        $cvID = $model->id;

        foreach ($photos as $photo) {
            $model = new CVPhoto();
            $model->cv_id = $cvID;
            $model->photo = $photo;
            $model->save();
        }
        $this->updateModel($photos, $cvID);
    }
}
