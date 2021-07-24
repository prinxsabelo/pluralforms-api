<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerDetail extends Model
{
    protected $primaryKey = 'answer_detail_id';
    protected $casts = [
        'submitted' => 'boolean',
        'visited' => 'boolean'
    ];
    protected $fillable = [
        'form_id','submitted','token','visited','ref_id'
    ];
    use HasFactory;
}
