<?php

namespace App\Services;

use Closure;
use DB;

use Google\Cloud\Core\ServiceBuilder;

class CloudApiService extends BaseService
{
    public function __construct()
    {

    }

    public function googleCV($photo)
    {
        // $cloud = new ServiceBuilder([
        //     'keyFilePath' => public_path('api-key/folkloric-grid-255513.json'),
        //     'projectId' => 'folkloric-grid-255513'
        // ]);
        // $vision = $cloud->vision();
        // $image = $vision->image(file_get_contents(public_path('uploads/photos/steve_jobs.jpg')), ['FACE_DETECTION']);
        // $result = $vision->annotate($image);
        
        // print_r($result);exit();

        // API URL
        $url = 'https://vision.googleapis.com/v1p4beta1/images:annotate?key=AIzaSyCZIfP_mQ6-EQzEB_ECRqxqjQQCmiIVJUA';

        // Create a new cURL resource
        $ch = curl_init($url);

        //Getting image
        // $image = file_get_contents('https://pbs.twimg.com/profile_images/988775660163252226/XpgonN0X_400x400.jpg');
        $image = file_get_contents(public_path('uploads/photos/Bill_Gates.jpg'));
        //converting image into base64
        $image_64= base64_encode($image);

        // Setup request to send json via POST
        $data = [
            "requests" => [
                [
                    "image" => [
                    "content" => $image_64 
                    ], 
                    "features" => [
                        [
                            "type" => "FACE_DETECTION" 
                        ] 
                    ], 
                    "imageContext" => [
                        "faceRecognitionParams" => [
                            "celebritySet" => [
                                "builtin/default" 
                            ] 
                        ] 
                    ] 
                ] 
            ] 
        ]; 
    
        $payload = json_encode($data);

        // Attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the POST request
        $result = curl_exec($ch);
        // Close cURL resource
        curl_close($ch);

        $result = json_decode($result);
        if (count($result->responses) > 0) {
            $faces = $result->responses[0]->faceAnnotations;
            if (count($faces) > 0) {
                if (!@$faces[0]['recognitionResult'])
                    return null;
                $celebs = $faces[0]['recognitionResult'];
                $confidence = 0; $top = 0;
                foreach ($celebs as $itr => $each) {
                    if ($each['confidence'] > $confidence)
                        $top = $itr;
                }
                return $celebs[$top]['celebrity']['displayName'];
            }
        }
        return null;
    }
    public function amazonCV($photo)
    {
        
    }
    public function youtube($params)
    {

    }
    public function imdb($params)
    {
        
    }
    public function gNews($params)
    {
        
    }
    public function songkick($params)
    {
        
    }
    public function wiki($params)
    {
        $name = $params['name'];
        
    }
    public function serp($params)
    {
        
    }
}
