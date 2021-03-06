<?php

namespace App\Services;

use App\Models\User;

use Mail;

class NotifyService extends BaseService
{

    protected $userService;
	public function __construct(UserService $userService)
	{
        $this->userService = $userService;
	}
    
    public function pushNotify($data, $id = null)
    {
        /*
        $now = date('Y-m-d H:i:s');
        if ($id)
            $firebaseToken = User::where(['id' => $id])->pluck('fb_token')->all();
        else
            $firebaseToken = User::whereNotNull('fb_token')->pluck('fb_token')->all();
        
        $SERVER_API_KEY = 'AAAA-WIvaZE: APA91bGv0DsZkMGCV7g3xrffBGXlXm3jaOz8W7gGndbQ7oDdsnyeoZYqfkdSMeIcrIpkktrnKSJo_lJk-jPiVgGXMKrxb6hs_2hScut1';
        
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $data['title'],
                "body" => $data['content'],
            ],
            "data" => [
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
        */
        OneSignal::sendNotificationToAll($data['content'], null, null, null, null, $data['title']);
    }
    public function sendEmail($data, $id = null)
    {
        if ($id)
            $mails = User::where(['id' => $id])->pluck('email')->all();
        else
            $mails = User::whereNotNull('email')->pluck('email')->all();
        // foreach ($mails as $mail)
        // {
        //     // \Mail::send('email.test', array(
        //     //     'subject' => $data['title'],
        //     //     'body' => $data['content'],
        //     // ), function($message) use ($data){
        //     //     $message->from('burcuhan@gmail.com', 'Fofik');
        //     //     $message->to('jovanovic.nemanja.1029@gmail.com', 'Admin')->subject($data['title']);
        //     // }); 
        //     $details = array (
        //         'subject' => $data['title'],
        //         'body' => $data['content']
        //     );
        //     Mail::to('jovanovic.nemanja.1029@gmail.com')->send(new \App\Mail\FofikMail($details));
        // }
        $params = [];
        $params['email_subject'] = $data['title'];
        $params['email_body'] = $data['content'];
        $params['include_email_tokens'] = $mails;
        OneSignal::sendNotificationCustom($params);
    }
}
