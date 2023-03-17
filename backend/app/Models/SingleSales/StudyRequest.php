<?php

namespace App\Models\SingleSales;

use App\Models\Language;
use App\Models\User;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudyRequest extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        "product_id",
        'study_language_id',
        "study_reason",
        "status",
        'created_by',
        "updated_by",
    ];
    protected $hidden = ['product_id', 'study_language_id', "created_by", "updated_by"];


    protected $table = 'study_requests';

    protected static $types = ["pending", "in study", "in hold", "completed", "failed", "cancelled"];

    public static function getTypes()
    {
        return StudyRequest::$types;
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by')->select(["id", "firstname", "lastname"]);
    }

    public function updatedBy(){
        return $this->belongsTo(User::class, 'updated_by')->select(["id", "firstname", "lastname"]);
    }

    public function language(){
        return $this->belongsTo(Language::class, 'study_language_id');
    }

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

}
