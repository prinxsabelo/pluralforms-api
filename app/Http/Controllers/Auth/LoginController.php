<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    //
    public function authenticate(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $credentials = $request->only('email', 'password');
        $token = auth()->attempt($credentials);
        if(!$token)
        {
            if($user)
            {
                $response['message'] = "Check your password..";
                return;
            }else{
                $response['message'] = "Invalid Email from you..";
            }
            $response['ok'] = false;
            return response($response, 401);
        }
      
        $response['ok'] = true;
        $response['user'] = $user;
        $response['token'] = $token;
        return response($response,200);
    }
}
