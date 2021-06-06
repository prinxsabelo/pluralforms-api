<?php

namespace App\Models;
use App\Models\Question;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $casts = [
        'allow_multiple_selection' => 'boolean',
        'required' => 'boolean',
        'randomize' => 'boolean'
      ];
    public $timestamps = false;
    protected $primaryKey = 'property_id';
    use HasFactory;
    protected $fillable = [
        'q_id','shape','allow_multiple_selection','required','randomize'
    ];
    public function Property()
    {
        return $this->hasOne(Question::class);
    }
}
