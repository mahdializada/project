<?php

namespace App\Models\SingleSales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use App\Models\Reason;
use App\Models\User;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, UuidTrait, SoftDeletes;



    protected $table = "brands_ssp";

    protected $fillable = ["name", "code","arabic_name","status", "is_approved", "country_id", "created_by"];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function attachment()
    {
        $this->primaryKey = "id";
        return $this->morphOne(Attachment::class, 'attachmentable');
    }
    public function reasons()
    {
        return $this->morphToMany(Reason::class, 'reasonable')->withTimestamps();
    }
}
