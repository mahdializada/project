<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class DesignRequestReject extends Model
{
    use HasFactory, UuidTrait;

    protected $table = 'design_request_reject';
   

    protected $fillable = [
        "status" ,"design_request_id","changed_by" 
    ];

    
    public function DesignRequestRejectReason()
    {
        return $this->hasMany(DesignRequestRejectReason::class);
    }
    
}
