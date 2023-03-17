<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory,  UuidTrait;
    protected $table = "comments_ssp";
    protected $fillable = ['comment', 'user_id','commentable_id', 'commentable_type'];

}
