<?php

namespace App\Services;

use App\Traits\UploadFileTrait;

class PhotoUploadService extends BaseService
{
    use UploadFileTrait;

	public function __construct()
	{
	
	}

    public function uploadPhoto($photo, $prefix = '')
    {
        $folder = '/uploads/photos/';
        $name = $prefix.time();
        $filePath = $folder.$name.'.'.$photo->getClientOriginalExtension();
        
        return $this->uploadOne($photo, $folder, 'store', $name);
    }
}
