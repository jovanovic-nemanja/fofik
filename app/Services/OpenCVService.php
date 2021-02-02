<?php

namespace App\Services;

use Closure;
use DB;

use App\Models\CVPhoto;

use CV\Face\LBPHFaceRecognizer, CV\CascadeClassifier, CV\Scalar, CV\Point;
use function CV\{imread, imwrite, cvtColor, equalizeHist};
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

        $faceRecognizer = LBPHFaceRecognizer::create();

        $gates = ['g1.jpg', 'g2.jpg', 'g3.jpg', 'g4.jpg', 'g5.jpg', 'g6.jpg', 'g7.jpg', 'g8.jpg', 'g9.jpg'];
        $test = ['t1.jpg', 't2.jpg', 't3.jpg', 't4.jpg'];
        $labels = ['unknown', 'Bill Gates', 'Angelina'];
        $faceImages = $faceLabels = [];

        foreach ($gates as $photo) {
            $src = imread(public_path('uploads/cv_photos/'.$photo));    
            $gray = cvtColor($src, COLOR_BGR2GRAY);
            $faceClassifier->detectMultiScale($gray, $faces);    
            equalizeHist($gray, $gray);
            foreach ($faces as $k => $face) {
                $faceImages[] = $gray->getImageROI($face); // face coordinates to image
                $faceLabels[] = 1;
            }
        }
        $faceRecognizer->train($faceImages, $faceLabels);
        
        $faceImages = $faceLabels = [];
        $src = imread(public_path('uploads/cv_photos/faces.jpg'));    
        $gray = cvtColor($src, COLOR_BGR2GRAY);
        $faceClassifier->detectMultiScale($gray, $faces);    
        equalizeHist($gray, $gray);
        foreach ($faces as $k => $face) {
            $faceImages[] = $gray->getImageROI($face); // face coordinates to image
            $faceLabels[] = 2;
        }
        $faceRecognizer->update($faceImages, $faceLabels);


        $faceRecognizer->write(public_path('uploads/cv_photos/lbph_model.xml'));
        $faceRecognizer = LBPHFaceRecognizer::create();
        $faceRecognizer->read(public_path('uploads/cv_photos/lbph_model.xml'));

        $src = imread(public_path('uploads/cv_photos/t5.jpg'));    
        $gray = cvtColor($src, COLOR_BGR2GRAY);
        $faceClassifier->detectMultiScale($gray, $faces);   
        equalizeHist($gray, $gray);
        foreach ($faces as $k => $face) {
            $faceImage = $gray->getImageROI($face); // face coordinates to image
            $faceLabel = $faceRecognizer->predict($faceImage, $faceConfidence);
            echo $labels[$faceLabel]." ".$faceConfidence."\n";
        }
        // $labels = ['unknown', 'me', 'angelina'];

        // // me
        // $src = imread(public_path('uploads/cv_photos/faces.jpg'));
        // $gray = cvtColor($src, COLOR_BGR2GRAY);
        // $faceClassifier->detectMultiScale($gray, $faces);
        // //var_export($faces);
        // equalizeHist($gray, $gray);
        // $faceImages = $faceLabels = [];
        // foreach ($faces as $k => $face) {
        //     $faceImages[] = $gray->getImageROI($face); // face coordinates to image
        //     $faceLabels[] = 1; // me
        //     //cv\imwrite("results/recognize_face_by_lbph_me$k.jpg", $gray->getImageROI($face));
        // }
        // $faceRecognizer->train($faceImages, $faceLabels);

        // // angelina
        // $src = imread(public_path('uploads/cv_photos/angelina_faces.png'));
        // $gray = cvtColor($src, COLOR_BGR2GRAY);
        // $faceClassifier->detectMultiScale($gray, $faces);
        // //var_export($faces);
        // equalizeHist($gray, $gray);
        // $faceImages = $faceLabels = [];
        // foreach ($faces as $k => $face) {
        //     $faceImages[] = $gray->getImageROI($face); // face coordinates to image
        //     $faceLabels[] = 2; // Angelina
        //     //cv\imwrite("results/recognize_face_by_lbph_angelina$k.jpg", $gray->getImageROI($face));
        // }
        // $faceRecognizer->update($faceImages, $faceLabels);

        // //$faceRecognizer->write('results/lbph_model.xml');
        // //$faceRecognizer->read('results/lbph_model.xml');

        // // test image
        // $src = imread(public_path('uploads/cv_photos/angelina_and_me.png'));
        // $gray = cvtColor($src, COLOR_BGR2GRAY);
        // $faceClassifier->detectMultiScale($gray, $faces);
        // //var_export($faces);
        // equalizeHist($gray, $gray);
        // foreach ($faces as $face) {
        //     $faceImage = $gray->getImageROI($face);

        //     //predict
        //     $faceLabel = $faceRecognizer->predict($faceImage, $faceConfidence);
        //     echo "{$faceLabel}, {$faceConfidence}\n";

        //     $scalar = new \CV\Scalar(0, 0, 255);
        //     \CV\rectangleByRect($src, $face, $scalar, 2);

        //     $text = $labels[$faceLabel];
        //     \CV\rectangle($src, $face->x, $face->y, $face->x + ($faceLabel == 1 ? 50 : 130), $face->y - 30, new Scalar(255,255,255), -2);
        //     \CV\putText($src, "$text", new Point($face->x, $face->y - 2), 0, 1.5, new Scalar(), 2);
        // }

        // imwrite(public_path('uploads/cv_photos/results/_recognize_face_by_lbph.jpg'), $src);
        return null;
    }
}
