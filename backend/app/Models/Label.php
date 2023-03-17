<?php

namespace App\Models;

use App\Models\Advertisement\HistoryAd;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use App\Models\LabelCategory;

class Label extends Model
{
    use HasFactory, UuidTrait, SoftDeletes;

    protected $fillable = ["title", "code", "label", "status", "color","system", "label_category_id", "tabs"];

    protected static $types = ["archive", "live", "removed"];

    public static function getTypes()
    {
        return Label::$types;
    }

    public function reasons()
    {
        return $this->belongsToMany(Reason::class, "reason_label", "label_id", "reason_id")->withPivot("status", "changed_by")->withTimestamps();
    }

    public function changedBy()
    {
        return $this->belongsToMany(User::class, "reason_label", "label_id", "changed_by");
    }

    public function subSystems()
    {
        return $this->belongsToMany(SubSystem::class, 'label_sub_systems');
    }
    public function labelCategory()
    {
        return $this->belongsTo(LabelCategory::class, 'label_category_id');
    }

    public function historyAd()
    {
        return $this->morphedByMany(HistoryAd::class, 'labelable', 'adset_id');
    }
}
