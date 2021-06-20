<?php

namespace App\Models;
use App\Models\Form;
use App\Models\Property;
use App\Models\Choice;
use App\Models\Feedback;
use App\Models\Answer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{

    protected $casts = [
        'allow_multiple_selection' => 'boolean',
        'required' => 'boolean',
        'randomize' => 'boolean',
        'submitted' => 'boolean'
    ];
    public $timestamps = false;
    protected $primaryKey = 'q_id';
    use HasFactory;
    protected $fillable = [
        'title','type','form_id'
    ];
    // Question belong to a form..
    public function Form()
    {
        return $this->belongsTo(Form::class,'foreign_key', 'form_id');
    }
    // One question possess one property..
    public function property()
    {
        return $this->hasOne(Property::class);
    }
    public function choices()
    {
        return $this->hasMany(Choice::class);
    }
    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }
    public function answer()
    {
        return $this->hasOne(Answer::class);
    }
}
