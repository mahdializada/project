<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SourcingResult extends Model
{
    use HasFactory, UuidTrait, SoftDeletes;

    protected $table = 'sourcing_results_ssp';
    protected $guarded = ['created_at', 'updated_at'];

    public function sourcingRequest()
    {
        return $this->belongsTo(SourcingRequest::class, 'sourcing_request_id');
    }
}
