<?php

namespace App\Models\SingleSales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsppQuestioner extends Model
{
    use HasFactory;
    protected $table = 'ispp_questioners';
    protected $fillable = ['ispp_id','question_id','answer','answer_details'];
}
