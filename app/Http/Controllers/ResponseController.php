<?php

namespace App\Http\Controllers;
use App\Models\Question;
use App\Models\Answer;
use App\Models\AnswerDetail;
use App\Models\Choice;
use App\Models\Form;
use Illuminate\Http\Request;


class ResponseController extends Controller
{
    public function index(Request $request)
    {
        $form_id = $request->form_id;
        // $form_id = 2;
        $form = Form::where('form_id',$form_id)->select('title','form_id')->first();
        $questions=[];
        $questions = Question::where('form_id',$form_id)->join('properties','properties.q_id','questions.q_id')
                                ->select('title','type','questions.q_id')->get(); 
        $form->questions = $questions;
        $respondents = Answer::where('answer_details.form_id',$form_id)->where('answer_details.submitted',true)
                                ->join('answer_details','answer_details.form_id','answers.form_id')
                                ->distinct()
                                ->get('answer_details.token');
        foreach($respondents as $resp)
        {
            $resp->isChecked = false;
            $resp->submittedAt = Answer::where('token',$resp->token)->select('updated_at')->first()->updated_at;
            $arr=[];
            foreach($questions as $question)
            {   
                $answer_count = Question::where('questions.q_id',$question->q_id)->where('token',$resp->token)
                                        ->leftJoin('answers','answers.q_id','questions.q_id')
                                        ->leftJoin('properties','properties.q_id','questions.q_id')
                                        ->select('title','type','shape','answer','a_id','allow_multiple_selection')
                                        ->count();
                if($answer_count > 0)
                {
                    $answer = Question::where('questions.q_id',$question->q_id)->where('token',$resp->token)
                                        ->leftJoin('answers','answers.q_id','questions.q_id')
                                        ->leftJoin('properties','properties.q_id','questions.q_id')
                                        ->select('title','type','shape','answer','a_id','allow_multiple_selection')
                                        ->first();
                    if($answer && $answer->answer == null){
                        $answer->skipped = true;
                    }else{
                        $answer->skipped = false;
                    }
                    $answer->total = 5;
                    $arr[] = $answer;
                }
               
              
            }
            $resp->answers = $arr;
        }
        $form->respondents = $respondents;
        $response['form'] = $form;
        return $response;
    }
    public function delete(Request $request)
    {
        $control = false;
        $tokenArr =  $request->arr;
        foreach ($tokenArr as $key => $token) {
            $delControl =  Answer::where('token',$token)->delete();
            $delControl = AnswerDetail::where('token',$token)->delete();
            if($delControl)
            {
                $control = true;
            }
        }
        $response['ok'] = $control;
        return $response;
    }
}
?>