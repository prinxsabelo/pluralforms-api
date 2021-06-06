<?php

namespace App\Models;
use App\Models\Form;
use App\Models\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
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
    public function Property()
    {
        return $this->hasOne(Property::class);
    }
}
