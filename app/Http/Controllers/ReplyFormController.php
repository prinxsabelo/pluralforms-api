<?php

namespace App\Http\Controllers;
use App\Models\Form;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Property;
use App\Models\Choice;
use App\Models\Feedback;
use Illuminate\Http\Request;

class ReplyFormController extends Controller
{
   
    public function index(Request $request)
    {
        $token = $request->token;
        $form_id = $request->form_id;
        $count = Answer::where('token',$token)->where('form_id',$form_id)->count();
        //No answers yet for respondent.. Thereby store default answers for respondent..
        if($count == 0)
        {
            $questions = Question::where('form_id',$form_id)->get();
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
                                    ->select(   'questions.q_id','questions.form_id',
                                                'title','type','shape',
                                                'allow_multiple_selection','required','randomize',
                                                'answer','submitted','token'
                                               )
                                    ->get();
        foreach ($returnPack as $question) 
        {
           $question->choices = Choice::where('q_id',$question->q_id)->select('label')->get();
           $question->feedback = [];
            if($question->type == "YN")
            {
                $question->feedback = Feedback::where('q_id',$question->q_id)->select('q_id','occupy','label')->get();
            }
            
        }
        return $returnPack;
    }
}
