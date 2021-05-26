<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SignupController extends Controller
{
    //
    public function store(Request $request)
    {

        $this->validate($request,[
            'name' => 'required|max:255',
            'email' => 'required',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->email)->first();
        if($user)
        {
            $response = ["message" => "Email exists already.."];
            return response($response, 422);
        }
        $user =  User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' =>  Hash::make($request->password)
        ]);
        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);
        if($token)
        {
            $response['ok'] = true;
            $response['user'] = $user;
            $response['token'] = $token;
        }
        return response($response, 200);
    }
}
