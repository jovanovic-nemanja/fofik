<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use App\Traits\UploadFileTrait;

class PhotoUploadService extends BaseService
{
    use UploadFileTrait;

	public function __construct()
	{
	
	}

    public function uploadPhoto($photo, $prefix = 'uploads')
    {        
        $folder = $prefix.'/'.'photos'.'/';
        $name = uniqid();
        $filePath = $folder.$name.'.'.$photo->getClientOriginalExtension();
        
        return $this->uploadOne($photo, $folder, 'store', $name);
    }
}
