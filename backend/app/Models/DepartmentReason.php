<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentReason extends Model
{
    use HasFactory, UuidTrait;

    protected $guarded = ["reason_id", "status_id", "department_id", "changed_by"];

    protected $hidden = ["updated_at"];

    protected $table = "reason_status_department";

    public function reasonStatus(){
        return $this->belongsTo(ReasonStatus::class);
    }

}
