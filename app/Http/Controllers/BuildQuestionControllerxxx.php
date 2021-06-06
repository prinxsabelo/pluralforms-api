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
       
            $properties = Property::where('q_id',$q_id)->first();
            $properties->shape = $request->properties['shape'];
            $properties->randomize = $request->properties['randomize'];
            $properties->required = $request->properties['required'];
            $properties->save();
            $indexArr=[];
            $control = false;
            // return $request->properties['choices'];
            foreach($request->properties['choices'] as $choice)
            {
               
                        // Choice::where('choice_id',$choice_id)->update([
                       
                        //     'label'=>$choice['label'],
                        //     'q_id' => $q_id
                        // ]);
                $choice_label = $choice['label'];
                if(isset($choice['choice_id']))
                {
                    $choice_id = $choice['choice_id'];
                   
                        $checkChoice = Choice::where('choice_id',$choice_id)->where('q_id',$q_id)->first();
                        if($checkChoice->count() > 0)
                        {
                            $checkChoice->label = $choice_label;
                            $checkChoice->update([
                                'label'=>$choice['label'],
                                'q_id' => $q_id
                            ]);
                        }                       
                }else{
            
                    $indexChoiceId = 0;
                    $check['index'] = $choice['index'];
                    $index = $choice['index'];
                    $check['choice_id'] = 0;
                    // $newChoice = Choice::firstOrCreate(['label' => $choice_label],['q_id' => $q_id]);
                    // $check['choice_id'] = $newChoice['choice_id'];
                    //Check index first before creating new choice..
                    
                    // echo $index." = ";
                    array_push($indexArr, $check);
                
                    
                 
                }
               
                // echo json_encode($xxx);
            }
            
            echo json_encode($indexArr);
            // $response['ok'] = true;
            // return response($response,201);
            // $question = $form->questions()->where('q_id',$q_id)->first();
            // return response($question);
        }else{
            return response("You cannot access form..",401);
        }
        // return response($request);
    }
}

