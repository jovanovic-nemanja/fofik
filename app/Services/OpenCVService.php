<?php

namespace App\Services;

use Closure;
use DB;

use App\Models\CVPhoto;

use CV\Face\LBPHFaceRecognizer, CV\CascadeClassifier, CV\Scalar, CV\Point;
use function CV\{imread, cvtColor, equalizeHist};
use const CV\{COLOR_BGR2GRAY};

class OpenCVService extends BaseService
{
    public function __construct()
    {

    }
    public function recognize($photo)
    {
        $faceClassifier = new CascadeClassifier();
        $faceClassifier->load(public_path('opencv/models/lbpcascades/lbpcascade_frontalface.xml'));
        return null;
    }
}
