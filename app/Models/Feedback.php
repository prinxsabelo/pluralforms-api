<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    protected $primaryKey = 'feedback_id';
    protected $fillable = [
        'q_id','label','occupy'
    ];
    public function question()
    {
        return $this->belongsTo(Question::class,'foreign_key','q_id');
    }
}
