<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileHistory extends Model
{
    protected $fillable = ['user_id', 'media_id'];
    use HasFactory;
}
