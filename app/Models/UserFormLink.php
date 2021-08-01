<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFormLink extends Model
{
    protected $primaryKey = 'user_form_link_id';
    protected $fillable = [
        'user_id',
        'form_id',
        'admin'
    ];
    use HasFactory;
}
