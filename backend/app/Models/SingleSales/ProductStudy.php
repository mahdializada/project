<?php

namespace App\Models\SingleSales;

use App\Models\Company;
use App\Models\User;
use App\Models\Language;
use App\Models\CountryLanguage;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Reason;


class ProductStudy extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

    protected $table = 'product_study_ssp';
    protected $fillable = [
        'order_note',
        'study_recommendations',
        'company_id',
        'studyable_type',
        'studyable_id',
        'study_purpose_id',
        'study_language_id',
        'work_priority',
        'target_gender',
        'start_target_age',
        'end_target_age',
        'target_note_name',
        'target_desc',
        'progressive',
        'status'
    ];

//  "individual sales system department", "product advertising department
    // protected static $study_area_type = ["isp","ipa",'product'];
    // public static function getStudyAreaType()
    // {
    //     return ProductStudy::$study_area_type;
    // }

    protected static $work_priority = ["high", "medium",'low'];
    public static function getWorkPriority()
    {
        return ProductStudy::$work_priority;
    }
    protected static $types = ["new study",'rejected', "in study",'in study review','study reject', "in hold", "complete", "deleted", "cancelled"];

    public static function getTypes()
    {
        return ProductStudy::$types;
    }


    
    
    
    
  
    
    // protected $hidden = ['product_id', 'study_language_id', "created_by", "updated_by"];
    
    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by')->select(["id", "firstname", "lastname"]);
    }

    public function updatedBy(){
        return $this->belongsTo(User::class, 'updated_by')->select(["id", "firstname", "lastname"]);
    }

    public function language(){
        return $this->belongsTo(Language::class, 'study_language_id');
    }

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function studyable()
    {
        return $this->morphTo();
    }

    public function studyPorpose(){
        return $this->belongsTo(StudyPurpose::class, 'study_purpose_id');
    }
    
    public function requestAssinUser()
    {
        return $this->belongsToMany(User::class, 'product_study_assigned_users_ssp',  'product_study_id', 'user_id');
    }
}
