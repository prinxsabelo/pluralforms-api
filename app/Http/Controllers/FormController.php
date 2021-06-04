<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\User;
use Illuminate\Http\Request;

class FormController extends Controller
{
    //
    public function store(Request $request)
    {
        $user = auth()->user();
        $form =  $user->forms()->create($request->only('title'));
        $form = $form->refresh();
        $form->ok = true;
        return $form;
    }
    public function update(Request $request)
    {
            $user = auth()->user();
            $form_id = $request->form_id;
            $form = $user->forms()->where('form_id',$form_id)->first();
           
            if($request->title)  
                $form->title = $request->title;
            if($request->status)
                $form->status = $request->status;
            if($request->begin_message) 
                $form->begin_message = $request->begin_message;
            if($request->end_message)
                $form->end_message = $request->end_message;
            if($request->avatar)
            {
                $form->avatar = $request->avatar;
            }
            $form->save();
            return response($form);
          
       
    }
    public function index()
    {
        $user = auth()->user();
        $forms = $user->forms()->orderBy('created_at','DESC')->get();
        return response($forms);
    }
}
