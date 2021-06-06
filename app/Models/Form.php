<?php

namespace App\Models;

use App\Models\User;
use App\Models\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Form extends Model
{
    use HasFactory;
    protected $primaryKey = 'form_id';
    protected $fillable = [
        'title','avatar','begin_message','end_message'
    ];
    public function User()
    {
        return $this->belongsTo(User::class);
    }
    //One form can have multiple questions..
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    
}
