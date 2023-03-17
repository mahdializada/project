<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStudyResult extends Model
{
    protected $table = "product_study_result_ssp";
    protected $fillable = ['id', 'code', 'features', 'specification', 'strength_point', 'status', 'usages', 'target_audience', 'benefits', 'weaknesses', 'opportunities', 'threats', 'study_notes', 'seller_information', 'product_study_id'];

    use HasFactory, SoftDeletes, UuidTrait;

    public function productStudy(){
        return $this->belongsTo(ProductStudy::class,'product_study_id');
    }
}
