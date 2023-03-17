<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory,  UuidTrait;
    protected $table = "replies_ssp";
    protected $fillable = ['reply', 'user_id','comment_id'];

}
