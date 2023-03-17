<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignRequestRejectReason extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        "design_request_reject_id", "reason_id", 
    ];

    
    public function reasons()
    {
        return $this->belongsTo(Reason::class,"reason_id");
    }
}
