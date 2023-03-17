<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductListAttachment extends Model
{
    use HasFactory;
    protected $table = "product_list_attachments";
    protected $fillable = ['product_list_id','path'];
}
