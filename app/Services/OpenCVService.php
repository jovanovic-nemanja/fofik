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
        $faceClassifier = $this->getClassifier();
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
    public function getClassifier()
    {
        $faceClassifier = new CascadeClassifier();
        $faceClassifier->load(public_path('opencv/models/lbpcascades/lbpcascade_frontalface_improved.xml'));
        return $faceClassifier;
    }
    public function updateModel($photos, $label)
    {
        $faceClassifier = $this->getClassifier();
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
}
