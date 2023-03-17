<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Reason;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;


    protected $fillable = [
        "name",
        "status",
        "code",
        "note",
        "business_location_id",
        "created_by",
        "updated_by",
    ];

    protected $hidden = ["updated_at", 'pivot'];

    protected static $types = ["active", "inactive", "blocked", "pending", "removed"];

    public static function getTypes()
    {
        return Department::$types;
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reasons()
    {
        return $this->belongsToMany(Reason::class, 'reason_department', 'department_id', 'reason_id')->withPivot('status', 'changed_by')->withTimestamps();
    }

    public function changedBy()
    {
        return $this->belongsToMany(User::class, 'reason_department', 'department_id', 'changed_by');
    }

    public function industries()
    {
        return $this->belongsToMany(Industry::class, "department_industry");
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class);
    }
}
