<?php

namespace App\Models\SingleSales;

use App\Models\Reason;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ipa extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;
    protected $guarded = ['created_at', 'updated_at'];
    protected $fillable = ['code','ispp_id','ad_policy_violation', 'work_priority','status','publication_start_period','publication_end_period','intensify_result_confirmation','start_hour','end_hour','progressive'];
    protected $table = 'ipa_ssp';


    protected static $policy_violation = ["there is no",'low', "average",'high']; 
    public static function getPolicyViolation()
    {
        return Ipa::$policy_violation;
    }

    protected static $work_priority = ['low', "medium",'high']; 
    public static function getWorkPriority()
    {
        return Ipa::$work_priority;
    }

    protected static $status =  ['pending', 'rejected', 'in creation', 'in testing', 'sales moving', 'sales unstable','stopped', 'under developing','in hold', 'deleted', 'cancelled'];
    public static function getStatus()
    {
        return Ipa::$status;
    }







    public function actions()
    {
        return $this->morphMany(Action::class, 'dependable');
    }

    public function ispp(){
        return $this->belongsTo(Ispp::class,'ispp_id');
    }

    public function platforms(){
        return $this->hasMany(PlatformBudget::class,'ipa_id');
    }

    public function reasons()
    {
        return $this->morphToMany(Reason::class, 'reasonable')->withTimestamps();
    }
    public function studies()
    {
        return $this->morphMany(ProductStudy::class, 'studyable');
    }

}
