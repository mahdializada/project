<?php

namespace App\Models;

use App\Models\SingleSales\Category;
use App\Models\SingleSales\Ispp;
use App\Models\SingleSales\Product;
use App\Models\SingleSales\Attribute;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Reason extends Model
{
    protected $hidden   = ["updated_at", "deleted_at"];
    // protected $fillable = ["title", "code", 'system_id', "tabs"];
    protected $fillable = ["title", "code", 'system_id'];

    use UuidTrait;
    use HasFactory;
    use SoftDeletes;
    public function changedBy()
    {
        return $this->belongsToMany(User::class, "changed_by");
    }

    public function subSystems()
    {
        return $this->belongsToMany(
            SubSystem::class,
            'reason_sub_system',
            'reason_id',
            'sub_system_id',
        )
        ->withPivot('type');
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function categories()
    {
        return $this->morphedByMany(Category::class, 'reasonable');
    }
    public function ispps()
    {
        return $this->morphedByMany(Ispp::class, 'reasonable');
    }

    public function actions()
    {
        return $this->morphedByMany(Action::class, 'reasonable');
    }

    public function products()
    {
        return $this->morphedByMany(Product::class, 'reasonable');
    }

    public function Attribute()
    {
        return $this->morphedByMany(Attribute::class, 'reasonable');
    }
    // public function reasonable()
    // {
    //     return $this->morphTo();
    // }
    public function country()
    {
        return $this->morphedByMany(Country::class, 'reasonable');
    }

    public function reasonSubSystems()
    {
        return $this->hasMany(ReasonSubSystem::class,'reason_id');
    }

}
