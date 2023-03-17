<?php

namespace App\Models\SingleSales;

use App\Models\Company;
use App\Models\Reason;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{

    use HasFactory, SoftDeletes, UuidTrait;
    protected $table = "actions_ssp";
     protected $fillable = ['code', 'company_id', 'actionable_type', 'work_priority', 'actionable_id', 'action_note', 'landing_page_url', 'status'];

    public function companies(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id')->select('id', 'name', 'logo');
    }
    public function dependable()
    {
        return $this->morphTo(Action::class, 'action_section_item_id');
    }

    public function classes()
    {
        return $this->hasMany(ActionClass::class, 'action_id');
    }


    public function reasons()
    {
        return $this->morphToMany(Reason::class, 'reasonable');
    }
}
