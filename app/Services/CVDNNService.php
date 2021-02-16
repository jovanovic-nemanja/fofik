<?php

namespace App\Services;

use Closure;
use DB;

use App\Models\CVPhoto;
use App\Models\CVResource;

use CV\Scalar, CV\Size;
use function CV\{imread, imwrite, rectangle};

class CVDNNService extends BaseService
{
    protected $netDet;
    protected $netRecogn;
    protected $image2faces;
    protected $face2vec;
    public function __construct()
    {
        $this->netDet = \CV\DNN\readNetFromCaffe(public_path('opencv/models/ssd/res10_300x300_ssd_deploy.prototxt'), public_path('opencv/models/ssd/res10_300x300_ssd_iter_140000.caffemodel'));
        $this->netRecogn = \CV\DNN\readNetFromTorch(public_path('opencv/models/openface/openface.nn4.small2.v1.t7'));
    }
    public function image2faces($src) 
    {
        $size = $src->size(); // 2000x500

        $minSide = min($size->width, $size->height);
        $divider = $minSide / 300;
        \CV\resize($src, $resized, new Size($size->width / $divider, $size->height / $divider)); // 1200x300
        $blob = \CV\DNN\blobFromImage($resized, 1, new Size(), new Scalar(104, 177, 123), true, false);

        $this->netDet->setInput($blob, "");
        $r = $this->netDet->forward();

        $faces = [];

        for ($i = 0; $i < $r->shape[2]; $i++) {
            $confidence = $r->atIdx([0,0,$i,2]);
            if ($confidence > 0.9) {
                //var_export($confidence);echo "\n";
                $startX = $r->atIdx([0,0,$i,3]) * $src->cols;
                $startY = $r->atIdx([0,0,$i,4]) * $src->rows;
                $endX = $r->atIdx([0,0,$i,5]) * $src->cols;
                $endY = $r->atIdx([0,0,$i,6]) * $src->rows;

                $faces[] = $src->getImageROI(new \CV\Rect($startX, $startY, $endX - $startX, $endY - $startY));
            }
        }
 
        return $faces;
    }
    public function face2vec($face) 
    {
        $blob = \CV\DNN\blobFromImage($face, 1.0 / 255, new Size(96, 96), new Scalar(), true, false);
        $this->netRecogn->setInput($blob, "");
        return $this->netRecogn->forward();
    }

    public function faceDistance($face1, $face2)
    {
        $distance = 0;
        foreach ($face1 as $i => $v) {
            $distance += ($face1[$i] - $face2[$i])**2;
        }
        return sqrt($distance);
    }
    public function recognize($photo)
    {
        $faceVectors = [];

        $cvData = DB::table('ff_cv_resource as A')
            ->join('ff_cv_photos as B', 'A.id', '=', 'B.cv_id')
            ->select('A.name as label', 'B.photo')
            ->get();

        foreach ($cvData as $item) {
            $label = $item->label;
            $photo = $item->photo;
            $src = imread(public_path($photo));
            $faces = $this->image2faces($src);
            foreach ($faces as $i => $face) {
                $vec = $this->face2vec($face);
                $faceVectors[$label.$i] = $vec->data();
            }
        }
        //var_export($faceVectors);

        $src = imread(public_path($photo));

        $faces = $this->image2faces($src);
        $face = $faces[0];

        $vec = $this->face2vec($face);
        $minDistance = 1000;
        $faceLabel = '';
        foreach ($faceVectors as $label => $faceVector) {
            $distance = faceDistance($vec->data(), $faceVector);
            if (!$minDistance || $distance < $minDistance) {
                $minDistance = $distance;
                $faceLabel = $label;
            }
        }
        
        $similarity = intval((max(sqrt(2), $minDistance) - $minDistance) / sqrt(2) * 100);
        echo "face $faceLabel distance: $minDistance, similarity: $similarity%\n";
        exit();
    }
}
