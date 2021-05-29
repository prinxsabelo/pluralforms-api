<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Laravel\Socialite\Facades\Socialite;


class LoginController extends Controller
{  
   
    public function redirectToProvider($provider)
    {
       
        return Response::json([
            'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl(),
        ]);
     
    }
    public function handleCallbackProvider($provider)
    {
        $user =  Socialite::driver($provider)->stateless()->user();
      
        if(isset($user->email))
        {
            return $this->LoginOrSignupAccount($user,$provider);
        }
        
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
    public function LoginOrSignupAccount($checkUser, $provider)
    {
        $user = User::where('email',$checkUser->getEmail())->first();
        if(!$user)
        {
            $user = User::create([
                        'name' => $checkUser->getName(),
                        'email' => $checkUser->getEmail(),
                        'avatar' => $checkUser->getAvatar(),
                        'provider' => $provider,
                        'provider_id' => $checkUser->getId(),
                        'password' => ''
                    ]);
        }else{
            $user->update([
                        'avatar' => $checkUser->getAvatar(),
                        'provider' => $provider,
                        'provider_id' => $checkUser->getId()
                    ]);
        }
        $token = Auth::login($user);
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user,
            'ok' => true
        ]);
      
        // return $this->respondWithToken($token);
     }
}
