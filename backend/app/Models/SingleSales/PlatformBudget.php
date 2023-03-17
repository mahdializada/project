<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlatformBudget extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UuidTrait;

    protected $table = 'platform_budget_ssp';
    protected $guarded = ['created_at', 'updated_at'];
    protected $fillable = ['id','ipa_id','platform_name','platform_placement','budget','target_cpo'];

    public function ipa(){
        return $this->belongsTo(Ipa::class,'ipa_id');
    }
}
