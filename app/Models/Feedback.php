<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    protected $primaryKey = 'feedback_id';

    public function Question()
    {
        return $this->belongsTo(Question::class,'foreign_key','q_id');
    }
}
