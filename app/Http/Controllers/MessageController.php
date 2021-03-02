<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\NotifyService;

class MessageController extends Controller
{
    //
    protected $notifyService;
    public function __construct(NotifyService $notifyService)
    {
        $this->notifyService = $notifyService;
    }
    public function index()
    {
        return view('msg.index');
    }
    public function send(Request $request)
    {
        $params = $request->all();
        if ($params['type'] == 'M')
            $this->notifyService->sendEmail($params);
        else if ($params['type'] == 'PN')
            $this->notifyService->pushNotify($params);
        return response()->json([
            'success' => true
        ]);
    }
}
