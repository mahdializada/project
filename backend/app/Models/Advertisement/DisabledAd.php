<?php

namespace App\Models\Advertisement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisabledAd extends Model
{
    protected $fillable = ['ad_id', 'user_id'];
    use HasFactory;
}
