<?php

namespace App\Models\Advertisement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdAccountSubscribtion extends Model
{
    use HasFactory;
    protected $fillable = ['ad_account_id', 'subscribtion_id'];
}
