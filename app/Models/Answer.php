<?php

namespace App\Models;
use App\Models\Question;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;   
    protected $primaryKey = 'a_id';
    protected $casts = [
        'submitted' => 'boolean'
    ];
    protected $fillable = [
        'form_id','q_id', 'answer','submitted','token'
    ];
    public function Question()
    {
        return $this->belongsTo(Question::class,'foreign_key', 'q_id');
    }
}