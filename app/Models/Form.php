<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserFormLink;
use App\Models\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Form extends Model
{
    use HasFactory;
    protected $primaryKey = 'form_id';
    protected $fillable = [
        'ref_id','title','avatar','begin_header','begin_desc','end_header','end_desc','user_id'
    ];
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    public function userForms()
    {
        return $this->hasMany(UserFormLink::class);
    }
    //One form can have multiple questions..
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    
}
