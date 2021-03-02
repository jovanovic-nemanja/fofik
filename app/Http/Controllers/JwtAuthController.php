<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

use App\Models\User;
use Socialite;

class JwtAuthController extends Controller
{
    //
    public $token = true;

    public function __construct()
    {
    }
    /**
     * login via google.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function googleProvider(Request $request)
    {
        $gToken = $request->get('access_token');
        $lang = $request->get('language');
        $deviceID = $request->get('device_id');
        $platform = $request->get('platform');

        try {
            $googleAccount = Socialite::driver('google')->userFromToken($gToken);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false, 
                'message' => 'You are an unauthroized user'
            ], 401);
        }

        $user = User::where(['social_id' => $googleAccount->id, 'social_site' => 'google'])->first();

        if (!$user) {
            $user = new User;
            $user->name = $googleAccount->getName();
            $user->email = $googleAccount->getEmail();
            $user->social_id = $googleAccount->getId();
            $user->social_site = 'google';
            $user->created_on = date('Y-m-d');
            $user->save();
        }
        $user->lang = $lang;
        $user->device_id = $deviceID;
        $user->platform = $platform;
        $user->update();

        if (!$token = auth('api')->attempt(true)) {
            return response()->json([
                'success' => false, 
                'message' => 'You are an unauthorized user'
            ]);
        }
        return $this->createNewToken($token);
    }
    /**
     * login via google.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function facebookProvider(Request $request)
    {
        $fbToken = $request->get('access_token');
        $lang = $request->get('language');
        $deviceID = $request->get('device_id');
        $platform = $request->get('platform');

        try {
            $facebookAccount = Socialite::driver('facebook')->userFromToken($fbToken);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false, 
                'message' => 'You are an unauthroized user'
            ], 401);
        }

        $user = User::where(['social_id' => $facebookAccount->id, 'social_site' => 'facebook'])->first();
        if (!$user) {
            $user = new User;
            $user->name = $facebookAccount->getName();
            $user->email = $facebookAccount->getEmail();
            $user->social_id = $facebookAccount->getId();
            $user->social_site = 'facebook';
            $user->created_on = date('Y-m-d');
            $user->save();
        }
        $user->lang = $lang;
        $user->device_id = $deviceID;
        $user->platform = $platform;
        $user->update();

        if (!$token = auth('api')->attempt(true)) {
            return response()->json([
                'success' => false, 
                'message' => 'You are an unauthorized user'
            ]);
        }
        return $this->createNewToken($token);
    }

    /**
     * signin user
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'device_id'     => 'required',
            'language'      => 'required',
            'platform'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $params = $request->all();
        $deviceID = $params['device_id'];
        $lang     = $params['language'];
        $platform = $params['platform'];

        $user = User::where(['device_id' => $deviceID])->first();
        if (!$user) {
            $user = new User;
            $user->device_id = $deviceID;
            $user->save();
        }
        $user->lang = $lang;
        $user->platform = $platform;
        
        auth('api')->factory()->setTTL(1000);
        
        if (!$token = auth('api')->attempt(['device_id' => $deviceID])) {
            return response()->json([
                'success' => false, 
                'message' => 'Authorization failed'
            ]);
        }
        $user->access_token = $token;
        $user->update();

        return $this->createNewToken($token);
    }

    /**
     * load personal data via social signin
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function socialProvider(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'access_token'  => 'required',
            'username'      => 'required',
            'email'         => 'required',  
            'social_id'     => 'required',
            'social_site'   => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $params = $request->all();
        $token = $params['access_token'];
        $name  = $params['username'];
        $email = $params['email'];
        $socialID = $params['social_id'];
        $socialSite = $params['social_site'];

        $user = User::where(['access_token' => $token])->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'invalid request'
            ]);
        }

        $user->name = $name;
        $user->email = $email;
        $user->social_id = $socialID;
        $user->social_site = $socialSite;
        
        $user->update();

        return response()->json([
            'success' => true
        ]);
    }
    /**
     * logout user.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function logout(Request $request)
    {
        auth('api')->logout();
        return response()->json([
            'success'=>true, 
            'message' => 'User successfully signed out', 
            'token' => $request->header('Authorization')
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request) {
        $new_token = auth('api')->refresh();
        return $this->createNewToken($new_token);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        $user = auth('api')->user();
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 5,
            'user' => $user
        ]);
    }

    public function firebaseToken(Request $request) 
    {
        auth('api')->user()->update(['fb_token' => $request->token]);
        return response()->json([
            'success' => true,
            'message' => 'token saved successfully'
        ]);
    }
}
