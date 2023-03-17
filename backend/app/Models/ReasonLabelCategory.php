<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReasonLabelCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'status',
        "reason_id",
        "label_categories_id",
        "changed_by", 
    ];
}
