<?php

namespace App\Models\SingleSales;

use App\Models\User;
use App\Models\Reason;
use App\Models\State;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SourcingRequest extends Model
{
    use SoftDeletes;
    use HasFactory, UuidTrait;
    protected $guarded = ['created_at', 'updated_at'];
    protected $table = 'sourcing_requests_ssp';
    protected $dates=['deleted_at'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getCodeAttribute($value)
    {
        return generateUniqueCode($value, "SR_", 4);
        // return "LBR_" . $value;
    }

    public function city()
    {
        return $this->belongsTo(State::class, 'state_id');
    }


    // public function receivingCountry()
    // {
    //     return $this->belongsTo(Country::class, 'receiving_country_id');
    // }



    public function reasons()
    {
        return $this->morphToMany(Reason::class, 'reasonable')->withTimestamps();
    }


    public function requestAssinUser()
    {
        return $this->belongsToMany(User::class, 'sourcing_request_assign_users',  'sourcing_request_id', 'user_id');
    }
}
