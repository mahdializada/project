<?php

namespace App\Models\ProductManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductQuestioner extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "pdm_product_questioners";

    protected $fillable = [
        "ispp_request_id", "question_id", "answer", "answer_details"
    ];
}
