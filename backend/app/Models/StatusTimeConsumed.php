<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusTimeConsumed extends Model
{
    use HasFactory;
    protected $table = "status_time_consumed";
    public $timestamps = false;

    protected $fillable = ['design_request_id', 'status', 'storyboard_stage', 'design_stage', 'end_date', 'created_at'];
}
