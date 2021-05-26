<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SignupController;
use App\Http\Controllers\Auth\SocialController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|

*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/signup',[SignupController::class,'store'])->name('signup');
Route::post('/login',[LoginController::class,'authenticate'])->name('login');
// Route::get('auth/facebook', [SocialController::class, 'facebookRedirect']);
// Route::get('auth/facebook/callback', [SocialController::class, 'loginWithFacebook']);
Route::get('/auth/{provider}', [SocialController::class,'redirectToProvider']);
Route::get('/auth/{provider}/callback',[SocialController::class,'handleCallbackProvider']);

