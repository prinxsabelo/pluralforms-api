<?php

namespace App\Http\Controllers;
use App\Models\Form;
use App\Models\Question;
use App\Models\Property;
use App\Models\Choice;
use App\Models\Feedback;
use App\Models\UserFormLink;
use Illuminate\Http\Request;

class BuildQuestionController extends Controller
{
    //Showing all questions for form_id here..
    public function index(Request $request)
    {
        $user = auth()->user();
        $form_id = $request->form_id;
       
        //If form exists exists for user then fetch questions detail for form
        $form =UserFormLink::where('user_id',$user->id)->where('user_form_links.form_id',$form_id)
                            ->join('forms','forms.form_id','user_form_links.form_id')
                            ->first();
        if($form)
        {
            $questions = Question::where('form_id',$form_id)->get();
            foreach ($questions as $key => $question) 
            {
                //Trying to convert NULL to string here..
                if(!$question->title)
                {
                    $question->title = "";
                }
            
                $question->form_id = $form_id;
                
                $properties = Property::where('q_id',$question->q_id)->first();

                $choices = Choice::where('q_id',$question->q_id)->get();
                foreach($choices as $key => $choice)
                {
                    $choice->c_index = $key;
                }
                    $properties->choices = $choices;
                $properties->feedback = Feedback::where('q_id',$question->q_id)->get();
                $question->properties = $properties;
                
            }
            $form->questions = $questions;
            $response['form'] = $form;
            return response($response);
        }else{
            $response['message'] = "You cannot access form..";
            return response($response,401);
        }
          
       
    }

    //Creating new question here for form_id
    public function store(Request $request)
    {
        $user = auth()->user();
        $form_id = $request->form_id;
        $type = $request->type;
        //Check if form exists for before building question..
        $form_count = UserFormLink::where('user_id',$user->id)
                                ->join('forms','forms.form_id','user_form_links.form_id')->count();

        if($form_count > 0)
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
                ['q_id' => $q_id, 'occupy' => 'Yes', 'label' => '' ],
                ['q_id' => $q_id, 'occupy' => 'No', 'label' => '' ],
            ];
            $feedback = Feedback::insert($feedArr);
            $property->shape = "star";
            $property->choices = [];
            $property->feedback = $feedArr;
            $question->properties = $property;
            $question->title="";
            
            //Updating form column for updated_at here..
            $form = Form::where('form_id',$form_id)->first();

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
        //Firstly Check if user is a collaborator in the form he wants to edit first..
        $form_count = UserFormLink::where('user_id',$user->id)
                            ->join('forms','forms.form_id','user_form_links.form_id')->count();
        if($form_count > 0)
        {   

            $question = Question::where('q_id',$q_id)->first();
            $question->form_id = $form_id;
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
         
            $choices = $request->properties['choices'];
         
            if(count($choices) <= 5)
            {
                Choice::where('q_id',$q_id)->delete();
                foreach($choices as $choice)
                {
                        $label = $choice['label'];
                        Choice::create(['label' => $label,'q_id' => $q_id]);
                }
            }else{
                return response("Max choices are 5",401);
            }
            if($question->type == "YN")
            {
                $feedArr = $request->properties['feedback'];
                if(count($feedArr) == 2)
                {
                    $feedY = Feedback::where('q_id',$q_id)->where('occupy','Yes')->first();
                    $feedY->label = $feedArr[0]['label'];
                    $feedY->save();
                    $feedN = Feedback::where('q_id',$q_id)->where('occupy','No')->first();
                    $feedN->label = $feedArr[1]['label'];
                    $feedN->save();
                }
               
            }
            $upForm = Form::where('form_id',$form_id)->first();
            $upForm->status = "ACTIVE";
            $upForm->save();
            $upForm->touch();
            $response['ok'] = true;

            return response($response);
            
        }else{
            $response['message'] = "You cannot access form..";
            return response($response,401);
        }
    
    }
    public function delete(Request $request)
    {
        $user = auth()->user();
        $form_id = $request->form_id;
        $q_id = $request->q_id;
        //Firstly Check if user is a collaborator in the form he wants to edit first..
        $form_count =  UserFormLink::where('user_id',$user->id)
                                    ->where('user_form_links.form_id',$form_id)
                                    ->join('forms','forms.form_id','user_form_links.form_id')
                                    ->count();
        if($form_count > 0)
        {   
            Question::where('q_id',$q_id)->delete();
            //Updating form column for updated_at here..
            
            $upForm = Form::where('form_id',$form_id)->first();
            $upForm->touch();
            $response['ok'] = true;
            return $response;
        }   else{ 
            return response("You cannot access form..",401);
        }
    }

    public function copy(Request $request)
    {
        $fromQuestion =  Question::where('q_id',$request->q_id)->first();
        $newQuestion = Question::create([
            'title' => $fromQuestion->title,
            'type' => $fromQuestion->type,
            'form_id' => $fromQuestion->form_id,
        ]);
        $fromProperty = Property::where('q_id',$request->q_id)->first();
            Property::create([
                'shape' => $fromProperty->shape,
                'allow_multiple_selection' => $fromProperty->allow_multiple_selection,
                'required' => $fromProperty->required,
                'randomize' => $fromProperty->randomize,
                'q_id' => $newQuestion->q_id
            ]);
        $fromFeedback = Feedback::where('q_id',$request->q_id)->get();
        foreach ($fromFeedback as $key => $value) {
            Feedback::create([
               'q_id' => $newQuestion->q_id,
               'occupy' => $value->occupy,
               'label' => $value->label
           ]);
        }
       

        $properties = Property::where('q_id',$newQuestion->q_id)->first();

        $choices = Choice::where('q_id',$newQuestion->q_id)->get();
        foreach($choices as $key => $choice)
        {
            $choice->c_index = $key;
        }
        $properties->choices = $choices;
        $properties->feedback = Feedback::where('q_id',$newQuestion->q_id)->get();
        $newQuestion->properties = $properties;

        return $newQuestion;
    }
}

