<?php

namespace App\Http\Controllers;
use App\Models\Form;
use App\Models\Question;
use App\Models\Property;
use App\Models\Choice;
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
            $questions = Question::where('form_id',$form_id)->get();
            foreach ($questions as $question) 
            {
                //Trying to convert NULL to string here..
                if(!$question->title)
                {
                    $question->title = "";
                }
                $properties = Property::where('q_id',$question->q_id)->first();

                $properties->choices = Choice::where('q_id',$question->q_id)->get();
                $properties->feedbacks = [];
                $question->properties = $properties;
                
            }
            $form->questions = $questions;
            $response['form'] = $form;
            return response($response);
        }else{
            return response("You cannot access form..",401);
        }
       
    }
    public function store(Request $request)
    {
        $user = auth()->user();
        $form_id = $request->form_id;
        $type = $request->type;
        $form = $user->forms()->where('form_id',$form_id)->first();
        if($form->count() > 0)
        {   
            $question =  Question::create([
             'form_id' => $form_id,
             'type' => $type   
            ]);
           
            $q_id = $question->q_id;
            $properties = Property::create([
                'q_id' => $q_id
            ]);
            $properties->choices = [];
            $properties->feedbacks = [];
            $question->properties = $properties;
            
            
            //Updating form column for updated_at here..
            $form->touch();
            return response($question);
        }else{
            return response("You cannot access form..",401);
        }
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $form_id = $request->form_id;
        $q_id = $request->q_id;
        $form = $user->forms()->where('form_id',$form_id)->first();
        if($form->count() > 0)
        {   
            
            $question = Question::where('q_id',$q_id)->first();
            $question->title = $request->title;
            $question->type = $request->type;
            $question->save();
            $response['ok'] = true;
            $property = Property::where('q_id',$q_id)->first();
            if(isset($request->properties['shape']))  {   $property->shape = $request->properties['shape']; }
            if(isset($request->proprtries['randomize'])) {   $property->randomize = $request->properties['randomize']; }
            if(isset($request->properties['required'])) { $property->required = $request->properties['required']; }
            
            $property->save();
            $indexArr=[];
            $control = false;
            $choices = $request->properties['choices'];
            $checkChoices = Choice::where('q_id',$q_id)->get();
           
            
            if(count($choices) <= 5)
            {
                $questionChoices = Choice::where('q_id',$q_id)->delete();
                foreach($choices as $choice)
                {
                    $label = $choice['label'];
                    if($label)
                    {
                        $newChoice = Choice::create(['label' => $label,'q_id' => $q_id]);
                    }
                }
            }else{
                return response("Max choices are 5",401);
            }
            $response['ok'] = true;
            return response($response);
            
        }else{
            return response("You cannot access form..",401);
        }
        // return response($request);
    }
}

