<?php

namespace App\Services;

use App\Models\LoginLog;
use App\Models\User;
use App\Models\Notify;

class NotifyService extends BaseService
{

    protected $userService;
	public function __construct(UserService $userService)
	{
        $this->userService = $userService;
	}
    
    public function send($notify, $id = null)
    {
        $now = date('Y-m-d H:i:s');
        if ($id)
            $firebaseToken = User::where(['id' => $id])->pluck('fb_token')->all();
        else
            $firebaseToken = User::whereNotNull('fb_token')->pluck('fb_token')->all();
        
        $SERVER_API_KEY = 'AAAA-WIvaZE: APA91bGv0DsZkMGCV7g3xrffBGXlXm3jaOz8W7gGndbQ7oDdsnyeoZYqfkdSMeIcrIpkktrnKSJo_lJk-jPiVgGXMKrxb6hs_2hScut1';
        
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $notify['title'],
                "body" => $notify['body'],  
                "icon" => $notify['icon'],
            ],
            "data" => [
                "link" => $notify['link'],
                "pushed_at" => $now
            ]
        ];
        $dataString = json_encode($data);
    
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
               
        $response = curl_exec($ch);

        
    }
}
