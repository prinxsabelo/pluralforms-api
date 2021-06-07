<?php

namespace App\Http\Controllers;
use App\Models\Form;
use App\Models\Question;
use App\Models\Property;
use App\Models\Choice;
use App\Models\Feedback;
use Illuminate\Http\Request;

class BuildQuestionController extends Controller
{
    //Showing all questions for form_id here..
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
                $properties->feedback = Feedback::where('q_id',$question->q_id)->get();
                $question->properties = $properties;
                
            }
            $form->questions = $questions;
            $response['form'] = $form;
            return response($response);
        }else{
            return response("You cannot access form..",401);
        } 
    }

    //Creating new question here for form_id
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
            $property = Property::create([
                'q_id' => $q_id
            ]);
            $feedArr = [
                ['q_id' => $q_id, 'occupy' => 'YES', 'label' => '' ],
                ['q_id' => $q_id, 'occupy' => 'NO', 'label' => '' ],
            ];
            $feedback = Feedback::insert($feedArr);
            $property->choices = [];
            $property->feedback = $feedback;
            $question->properties = $property;
            
            
            //Updating form column for updated_at here..
            $form->touch();
            return response($question);
        }else{
            return response("You cannot access form..",401);
        }
    }

    //Creating updating question here for form_id
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

            if(isset($request->properties['shape']))  
            {
                $property->shape = $request->properties['shape']; 
            }
            if(isset($request->properties['randomize'])) 
            {  
                $property->randomize = $request->properties['randomize'];
            }
            if(isset($request->properties['required'])) 
            {
                $property->required = $request->properties['required'];
            }
            if(isset($request->properties['allow_multiple_selection'])) 
            {
                $property->allow_multiple_selection = $request->properties['allow_multiple_selection'];
            }
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
            if($question->type == "YN")
            {
                $feedArr = $request->properties['feedback'];
                if(count($feedArr) == 2)
                {
                    $feedY = Feedback::where('q_id',$q_id)->where('occupy','YES')->first();
                    $feedY->label = $feedArr[0]['label'];
                    $feedY->save();
                    $feedN = Feedback::where('q_id',$q_id)->where('occupy','No')->first();
                    $feedN->label = $feedArr[1]['label'];
                    $feedN->save();
                }
               
            }
       

            $form->touch();
            $response['ok'] = true;
            return response($response);
            
        }else{
            return response("You cannot access form..",401);
        }
    
    }
}

