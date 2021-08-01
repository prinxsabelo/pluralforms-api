<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BuildQuestionController;
use App\Http\Controllers\ReplyFormController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResponseController;
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

Route::group(['middleware' => ['web']], function () {
  Route::get('/login/{provider}', [LoginController::class,'redirectToProvider']);
  Route::get('/login/{provider}/callback',[LoginController::class,'handleCallbackProvider']);
  Route::get('/logout',[LogoutController::class,'logout']);
  
});
Route::group(['middleware' => 'jwt.auth'], function() {
    //Forms..
    Route::post('/user/forms',[FormController::class,'store']);
    Route::put('/user/forms/update',[FormController::class,'update']);
    Route::get('/user/forms',[FormController::class,'index']);
    Route::put('/user/forms/close',[FormController::class,'close']);
    Route::put('/user/forms/restore',[FormController::class,'restore']);
    Route::delete('/user/forms/delete',[FormController::class,'delete']);
    Route::post('/user/forms/copy',[FormController::class,'copy']);

    //Form Report here..
    Route::post('/user/forms/report',[ReportController::class,'report']);

    //Form Response here..
    Route::post('/user/forms/response',[ResponseController::class,'index']);
    Route::delete('/user/forms/response',[ResponseController::class,'delete']);


    //Building Questons here..
    Route::post('/user/form',[BuildQuestionController::class,'index']);
    Route::post('/user/form/build',[BuildQuestionController::class,'store']);
    Route::post('/user/form/build/copy',[BuildQuestionController::class,'copy']);
    Route::put('/user/form/build/update',[BuildQuestionController::class,'update']);
    Route::delete('/user/form/build/delete',[BuildQuestionController::class,'delete']);

  
});


//Answering Questions here..
Route::post('/reply',[ReplyFormController::class,'index']);
Route::post('/reply/build',[ReplyFormController::class,'store']);
Route::post('/reply/token',[ReplyFormController::class,'token']);
Route::post('/reply/submit',[ReplyFormController::class,'submit']);
Route::post('/reply/check',[ReplyFormController::class,'check']);


  //Form Response here..
Route::post('/user/forms/report',[ReportController::class,'index']);
Route::post('/user/forms/response',[ResponseController::class,'index']);