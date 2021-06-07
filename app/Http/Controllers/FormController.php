<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\User;
use Illuminate\Http\Request;

class FormController extends Controller
{
    //Creating new form here..
    public function store(Request $request)
    {
        $user = auth()->user();
        $form =  $user->forms()->create($request->only('title'));
        $form = $form->refresh();
        $form->ok = true;
        return $form;
    }
    
    //Updating existing form here..
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
            $form->status = "active";
            $form->save();
            return response($form);
          
       
    }

    //Closing form here..
    public function close(Request $request)
    {
        $user = auth()->user();
        $form =  $user->forms()->where('form_id',$request->form_id)->first();
        $form->status = "closed";
        $form->save();
        $form = $form->refresh();
        return $form;
    }

    //Form can be restored here..
    public function restore(Request $request)
    {
        $user = auth()->user();
        $form =  $user->forms()->where('form_id',$request->form_id)->first();
        $form->status = "active";
        $form->save();
        $form = $form->refresh();
        return $form;
    }

    // Form can be deleted here..
    public function delete(Request $request)
    {
        $user = auth()->user();
        $form =  $user->forms()->where('form_id',$request->form_id)->delete();
        $response['ok'] = true;
        return $response;
    }

    // Fetching all forms for user here..
    public function index()
    {
        $user = auth()->user();
        $forms = $user->forms()->orderBy('created_at','DESC')->get();
        return response($forms);
    }
}
