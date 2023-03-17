<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReasonStatus extends Model
{
    use HasFactory, SoftDeletes;
    public $table = "reason_status";


    public function reason()
    {
        return $this->belongsTo(Reason::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

}
