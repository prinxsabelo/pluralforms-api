<?php

namespace App\Http\Controllers;
use App\Models\Form;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Choice;
use App\Models\Feedback;
use Illuminate\Http\Request;

class ReplyFormController extends Controller
{
   
    public function index(Request $request)
    {
        $token = $request->token;
        $form_id = $request->form_id;

        $formCount = Form::where('form_id',$form_id)->count();
        if($formCount == 0)
        {
            $response['message'] = "Form not found..";
            return response($response,401);
        }
        
        $formCheckStatus = Form::where('form_id',$form_id)->where('status','ACTIVE')->count();
        if($formCheckStatus == 0)
        {
            $response['message'] = "Form is closed";
            return response($response,401);
        }  

        $form = Form::select('avatar','title','status','begin_header','begin_desc',
                                'end_header','end_desc')->where('form_id',$form_id)->first();


        $count = Answer::where('token',$token)->where('form_id',$form_id)->count();
        //No answers yet for respondent.. Thereby store default answers for respondent..
        if($count == 0)
        {
            $questions = Question::where('form_id',$form_id)->get();
            if($questions->count() == 0)
            {
                $response['message'] = "No Questions found for form you want to fill..";
                return $response;
            }
            foreach($questions as $question)
            {
                $q_id = $question['q_id'];
                Answer::create([
                    'q_id' => $q_id,
                    'form_id' => $form_id,
                    'token' => $token,
                ]);
            }
        }

        //Check if respondent have submitted answers to questions already..
        $countSubmitted = Answer::where('token',$token)->where('form_id',$form_id)->where('submitted',true)->count();
        $countQuestions = Question::where('form_id',$form_id)->count();

        //All answered submitted already.. Form filling done..
        // Check settings if multiple submission is allowed here and comment line function below if needed too..
        if($countQuestions == $countSubmitted)
        {
            $response['done'] = true;
            return $response;
        }
       
        //Fetch Pack of questions and answers for respondent to answer..
        $returnPack = Question::where('questions.form_id',$form_id)
                                    ->join('properties','properties.q_id','questions.q_id')
                                    ->join('answers','answers.q_id','questions.q_id')
                                    ->where('answers.token',$token)
                                    ->select(  
                                                'answers.a_id',
                                                'questions.q_id','questions.form_id',
                                                'title','type','shape',
                                                'allow_multiple_selection','required','randomize',
                                                'answer','submitted','token'
                                               )
                                    ->get();
        foreach ($returnPack as $question) 
        {
           
           $question->choices = Choice::where('q_id',$question->q_id)->select('label','choice_id')->get();
           $question->feedback = [];
            if($question->type == "YN")
            {
                $question->feedback = Feedback::where('q_id',$question->q_id)->select('q_id','occupy','label')->get();
            }
            if($question->type == "CHOICE" && $question->allow_multiple_selection && !$question->answer)
            {
                $question->answer = [];
            }
        }
        $form->arr = $returnPack;
        return $form;
    }
    public function store(Request $request)
    {
        $form_id = $request->form_id;
        $answer = $request->answer;
        $token = $request->token;
        $a_id = $request->a_id;
        $q_id = $request->q_id;
       $upAnswer = Answer::where('a_id',$a_id)->where('form_id',$form_id)->where('token',$token)->first();
        if($upAnswer)
        {
            $upAnswer->answer = $answer;
            $upAnswer->save();
            echo json_encode($upAnswer);
            $response['ok'] = true;
            return response($response);
        } else{
            $countQuestion = Question::where('form_id',$form_id)->where('q_id',$q_id)->count();
            if($countQuestion == 0)
            {
                $response['message'] = "You can not fill this form..";
                return response($response,401);
            }
            Answer::create([
                'q_id' => $q_id,
                'form_id' => $form_id,
                'token' => $token,
                'answer' => $answer
            ]);
        }
    }
}
