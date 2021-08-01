<?php

namespace App\Http\Controllers;
use App\Models\Question;
use App\Models\Answer;
use App\Models\AnswerDetail;
use App\Models\Form;
use App\Models\Choice;
use App\Models\Property;
use App\Models\User;
use App\Models\UserFormLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormController extends Controller
{
    //Creating new form here..
    public function store(Request $request)
    {
        $user = auth()->user();
        $ref_id =   Str::random(7);
        
        $form =  Form::create([
            'title' => $request->title,
            'ref_id' => $ref_id
        ]);

        $user_form = UserFormLink::create([
            'form_id' => $form->form_id,
            'user_id' => $user->id
        ]);

        // $form =  $user->forms()->create($request->only('title'));
        $form->no_questions = 0;
        $form = $form->refresh();
        $form->ok = true;
        return $form;
    }
    
    //Updating existing form here..
    public function update(Request $request)
    {
            $user = auth()->user();
            $form_id = $request->form_id;
            $form = Form::where('form_id',$form_id)->first();
            if($request->title)  
                $form->title = $request->title;
            if($request->status)
                $form->status = $request->status;
            if($request->begin_header) 
                $form->begin_header = $request->begin_header;
            if($request->begin_desc) 
                $form->begin_desc = $request->begin_desc;
            if($request->end_header)
                $form->end_header = $request->end_header;
            if($request->end_desc)
                $form->end_desc = $request->end_desc;
            if($request->avatar)
            {
                $form->avatar = $request->avatar;
            }
            
            $form->status = "ACTIVE";
            $form->save();
            $form->no_responses = Answer::where('form_id',$form_id)->distinct()->count('token');
            $form->no_questions =  Question::where('form_id',$form_id)->count();
            return response($form);
          
       
    }

    //Closing form here..
    public function close(Request $request)
    {
        $user = auth()->user();
        $userFormLinkCount = UserFormLink::where('user_id',$user->id)->where('form_id',$request->form_id)->count();
        if($userFormLinkCount > 0)
        {
            $form =  Form::where('form_id',$request->form_id)->first();
            $form->status = "CLOSED";
            $form->save();
            $form = $form->refresh();
            $form->no_questions =  Question::where('form_id',$request->form_id)->count();
            return $form;
        }else{
            $response['message'] = "You cannot access form..";
        }
  
    }

    //Form can be restored here..
    public function restore(Request $request)
    {
        $user = auth()->user();
        $userFormLinkCount = UserFormLink::where('user_id',$user->id)->where('form_id',$request->form_id)->count();
        if($userFormLinkCount > 0)
        {
            $form =  Form::where('form_id',$request->form_id)->first();
            $form->status = "ACTIVE";
            $form->save();
            $form = $form->refresh();
            $form->no_views = AnswerDetail::where('form_id',$form->form_id)->where('visited',true)->count();
            $form->no_questions =  Question::where('form_id',$request->form_id)->count();
            $form->no_responses = Answer::where('form_id',$form->form_id)->distinct()->count('token');
            return $form;
        }else{
            $response['message'] = "You cannot access form..";
        }
    }

    // Form can be deleted here..
    public function delete(Request $request)
    {   
        $user = auth()->user();
        $userFormLinkCount = UserFormLink::where('user_id',$user->id)->where('form_id',$request->form_id)->count();
        if($userFormLinkCount > 0)
        {
            $form =  Form::where('form_id',$request->form_id)->delete();
            if($form)
            {
                $response['ok'] = true;
                return $response;
            }
        }else{
            $response['message'] = "You cannot access form..";
        }
    }

    // Fetching all forms for user here..
    public function index()
    {
        $user = auth()->user();
       
        //First fetch out form_links for user..
  
        $user_forms = UserFormLink::where('user_id',$user->id)
                                ->join('forms','forms.form_id','user_form_links.form_id')
                                ->orderBy('user_form_links.created_at','DESC')->get();
 
        foreach($user_forms as $form)
        {
            $form_id = $form->form_id;
          
            $form->no_views = AnswerDetail::where('form_id',$form_id)->where('visited',true)->count();
            $form->no_responses = AnswerDetail::where('form_id',$form_id)->where('submitted',true)->count();
            $form->no_questions =  Question::where('form_id',$form_id)->count();
        }
        return response($user_forms);
       
    }


    //Copy up form here..
    public function copy(Request $request)
    {
        $user = auth()->user();
        $ref_id =   Str::random(7);
        $userFormLinkCount = UserFormLink::where('user_id',$user->id)->where('form_id',$request->form_id)->count();
        if($userFormLinkCount > 0)
        {
            $fromForm = Form::where('form_id',$request->form_id)->first();

            $newForm =  Form::create([
                'title' => $request->title,
                'ref_id' => $ref_id,
                'begin_header' => $fromForm->begin_header,
                'begin_desc' => $fromForm->begin_desc,
                'end_header' => $fromForm->end_header,
                'end_desc' => $fromForm->end_desc,
                'avatar' => $fromForm->avatar,
                'status' => $fromForm->status,
            ]);


            UserFormLink::create([
                'user_id' => $user->id,
                'form_id' => $newForm->form_id
            ]);

            $fromQuestions = Question::where('form_id',$request->form_id)->get();
            foreach($fromQuestions as $question)
            {
                
                $fromProperty = Property::where('q_id',$question->q_id)->first();
                $fromChoices = Choice::where('q_id',$question->q_id)->get();

                $newQuestion = Question::create([
                    'form_id' => $newForm->form_id,
                    'title' => $question->title,
                    'type' => $question->type 
                ]);

                Property::create([
                    'q_id' => $newQuestion->q_id,
                    'shape' => $fromProperty->shape,
                    'allow_multiple_selection' => $fromProperty->allow_multiple_selection,
                    'required' => $fromProperty->required,
                    'randomize' => $fromProperty->randomize
                ]);
                
                foreach($fromChoices as $choice)
                {
                    Choice::create([
                        'label' => $choice->label,
                        'q_id' => $newQuestion->q_id
                    ]);
                }
            }
            $newForm->no_responses = Answer::where('form_id',$newForm->form_id)->distinct()->count('token');
            $newForm->no_questions =  Question::where('form_id',$newForm->form_id)->count();
            return $newForm;
        }else{
            $response['message'] = "You cannot access form..";
        }
    }
}
