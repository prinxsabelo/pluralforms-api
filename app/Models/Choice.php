<?php

namespace App\Models;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory;
   
    protected $primaryKey = 'choice_id';
    protected $fillable = [
        'label','q_id'
    ];
    public function Question()
    {
        return $this->belongsTo(Question::class,'foreign_key', 'q_id');
    }
}
