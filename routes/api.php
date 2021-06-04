<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BuildQuestionController;

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

Route::get('/login/{provider}', [LoginController::class,'redirectToProvider']);
Route::get('/login/{provider}/callback',[LoginController::class,'handleCallbackProvider']);
Route::get('/logout',[LogoutController::class,'logout']);

Route::group(['middleware' => 'jwt.auth'], function() {
    Route::post('/user/forms',[FormController::class,'store']);
    Route::post('/user/forms/update',[FormController::class,'update']);
    Route::get('/user/forms',[FormController::class,'index']);
    Route::post('/user/form',[BuildQuestionController::class,'index']);
    // Route::post('/user/form',[])
});
