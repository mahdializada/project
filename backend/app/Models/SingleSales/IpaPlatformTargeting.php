<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpaPlatformTargeting extends Model
{
    use HasFactory, UuidTrait; 
    protected $fillable = ['ipa_id','add_platform_id','target_gender','start_target_age','end_target_age','target_note','budget_type','budget','target_cost_per_order'];
    protected $table = 'ipa_platform_targeting';

    // protected static $target_gender = ['Male', "Female",'All']; 
    // public static function getTaregetGender()
    // {
    //     return IpaPlatformTargeting::$target_gender;
    // }
    protected static $budget_type = ['unlimited','daily', "weekly",'monthly']; 
    public static function getBudgetType()
    {
        return IpaPlatformTargeting::$budget_type;
    }
 
}
