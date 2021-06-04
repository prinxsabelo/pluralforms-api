<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BuildQuestionController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = auth()->user();
        $form_id = $request->form_id;
        $form = $user->forms()->where('form_id',$form_id)->first();
        if($form->count() > 0)
        {   
            $form->questions = [];
            $response['form'] = $form;
            return response($response);
        }else{
            return response("You cannot access form..",401);
        }
       
    }
}
