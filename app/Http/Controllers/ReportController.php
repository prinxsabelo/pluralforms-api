<?php

namespace App\Http\Controllers;
use App\Models\Question;
use App\Models\Answer;
use App\Models\AnswerDetail;
use App\Models\Choice;

use Illuminate\Http\Request;


class ReportController extends Controller
{
    public function index(Request $request)
    {
        $form_id = $request->form_id;
        // $form_id = 30;
        $sum['views'] = AnswerDetail::where('form_id',$form_id)->where('visited',true)->count();
        $sum['started'] = Answer::where('form_id',$form_id)->where('submitted',true)->distinct()->count('token');
        $counter = AnswerDetail::where('form_id',$form_id)->where('submitted',true)->count();
        if($counter == 0)
        {
            $sum['response'] = 0;
            $response['summary'] = $sum;
            $response['boxes'] = [];
            return $response;
        }else{
            $sum['response'] = AnswerDetail::where('form_id',$form_id)->where('submitted',true)->count();
        }
    
        $response['summary'] = $sum;
        
        $questions = Question::where('form_id',$form_id)->join('properties','properties.q_id','questions.q_id')->get(); 
        foreach ($questions as $key => $question) {
            $q_id = $question->q_id;
            $type = $question->type;
            if($type == "CHOICE")
            {
               
                $sum_answers = 0;
                if($question->allow_multiple_selection == 0)
                {
                    $choices = Choice::select('label')->where('q_id',$q_id)->get();
                    foreach ($choices as $key => $choice)
                    {
                        $label = $choice->label;
                        $answers_count =  Answer::select('answer')->where('answer',$label)->where('q_id',$q_id)->count();
                        $choice->value = 0;
                        $choice->percentage = 0;
                        $sum_answers += $answers_count;
                        $choice->answers =Answer::select('answer')->where('answer',$label)->where('q_id',$q_id)->get();
                        $choice->q_id = $q_id;
                    }
                    if($sum_answers == 0)
                    {
                        $choices =[];
                    }else{
                        foreach ($choices as $key => $choice)
                        {
                            $label = $choice->label;
                            $answers_count =  Answer::select('answer')->where('answer',$label)->where('q_id',$q_id)->count();
                            $choice->value = $answers_count;
                            $percentage = ($answers_count/$sum_answers)*100;
                            $choice->percentage = round($percentage,2);
                        }
                    }
                   
                   
                }else{
                    $choices = Choice::select('label')->where('q_id',$q_id)->get();
                    foreach ($choices as $key => $choice)
                    {
                        $label = $choice->label;
                        $answers_count =  Answer::where('q_id',$q_id)->whereJsonContains('answer',$label)->count();
                        $sum_answers += $answers_count;
                    }
                    if($sum_answers == 0)
                    {
                        $choices =[];
                    }else{
                        foreach ($choices as $key => $choice)
                        {
                            $label = $choice->label;
                            $answers_count =  Answer::where('q_id',$q_id)->whereJsonContains('answer',$label)->count();
                            $choice->value = $answers_count;
                            $percentage = ($answers_count/$sum_answers)*100;
                            $choice->percentage = round($percentage,2);
                            $choice->answers =Answer::select('answer')->where('answer',$label)->where('q_id',$q_id)->get();

                        }
                    }
                }
                $question->content = $choices;
                $summary['total'] = Answer::where('q_id',$q_id)->count();
                if($summary['total'] == 0)
                {
                    $summary['submitted'] = 0;
                }else{
                    $summary['submitted'] = $sum_answers;
                }
                $question['summary'] = $summary; 
               
            }
            else if($type == "RATING")
            {
                $sum_answers = 0;
                $choices = array(
                    array('label'=>'5','value'=>0),
                    array('label'=>'4','value'=>0),
                    array('label'=>'3','value'=>0),
                    array('label'=>'2','value'=>0),
                    array('label'=>'1','value'=>0),
                );
                foreach ($choices as $key => $choice)
                {
                    $label = $choice['label'];
                    $answers_count =  Answer::select('answer')->where('answer',$label)->where('q_id',$q_id)->count();
                  
                    $sum_answers += $answers_count;
                    // $choice->answers = $answers;
                }
                if($sum_answers == 0)
                {
                    $choices =[];
                }else{
                    foreach ($choices as $key => $choice) {
                        $label = $choice['label'];
                        $answers_count =  Answer::select('answer')->where('answer',$label)->where('q_id',$q_id)->count();
                        $choices[$key]['value'] = $answers_count;
                        $percentage = ($answers_count/$sum_answers)*100;
                        $choices[$key]['percentage'] = round($percentage,2);
                    }     
                }
               
                $question->content = $choices;
             
               
                $summary['total'] = Answer::where('q_id',$q_id)->count();
                if($summary['total'] == 0)
                {
                    $summary['submitted'] = 0;
                }else{
                    $summary['submitted'] = $sum_answers;
                }
                $question['summary'] = $summary; 
            }

            else if($type == "YN")
            {
                $sum_answers = 0;
                $choices = array(
                    array('label'=>'Yes','value'=>0),
                    array('label'=>'No','value'=>0),
                );
                foreach ($choices as $key => $choice)
                {
                    $label = $choice['label'];
                    $answers_count =  Answer::select('answer')->where('answer',$label)->where('q_id',$q_id)->count();
                    $sum_answers += $answers_count;
                 
                }
                if($sum_answers == 0)
                {
                    $choices =[];
                }else{
                    foreach ($choices as $key => $choice) 
                    {
                        $label = $choice['label'];
                        $answers_count =  Answer::select('answer')->where('answer',$label)->where('q_id',$q_id)->count();
                        $choices[$key]['value'] = $answers_count;
                        $percentage = ($answers_count/$sum_answers)*100;
                        $choices[$key]['percentage'] = round($percentage,2);
                    }    
                    $question->content = $choices;
                    $summary['submitted'] = $sum_answers;
                    $summary['total'] = Answer::where('q_id',$q_id)->count();
                    if($summary['total'] == 0)
                    {
                        $summary['submitted'] = 0;
                    }else{
                        $summary['submitted'] = $sum_answers;
                    }
                    $question['summary'] = $summary; 
                }
            }

            else if($type == "TEXT")
            {   
                    $answers = Answer::select('answer')->where('q_id',$q_id)->get();
                    $question['data'] = $answers;
                    $sum_answers = 0;
                    foreach ($answers as $key => $value) {
                        if($value->answer != null)
                        {
                            $sum_answers +=1;
                        }
                    }
                    $summary['submitted'] = $sum_answers;
                    $summary['total'] = Answer::where('q_id',$q_id)->count();
                    $question['summary'] = $summary; 
                    
            }
        }

      
        $response['boxes'] = $questions;
       
        return $response;
    }
   
}
    
?>