<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, UuidTrait, SoftDeletes;

    protected $fillable = [
        "code",
        "name",
        "number_of_people",
        "note",
        "schedule_type",
        "time_range",
        "date_range",
        "created_by",
        "updated_by",
        "status"
    ];

    protected $hidden = ["pivot"];

    protected static $types = ["active", "inactive", "blocked", "pending", "removed", "warning"];

    public static function getTypes()
    {
        return Role::$types;
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class);
    }

    public function user()
    {
        return $this->belongsToMany(User::class);
    }

    // public function actionSubSystems()
    // {
    //     return $this->belongsToMany(
    //         ActionSubSystem::class,
    //         "permission_role",
    //         "role_id",
    //         "action_sub_system_id"
    //     )
    //         ->with(['sub_system:id,name,scope', 'action:id,name', 'action.subActions:id,name']);
    // }

    public function members()
    {
        return $this->belongsToMany(User::class);
    }

    public function reasons()
    {
        return $this->belongsToMany(Reason::class, 'reason_role', 'role_id', 'reason_id')->withPivot('status', 'changed_by')->withTimestamps();
    }

    public function departmentCompanyCountry()
    {
        return $this->departments()->with(['companies:id,name,logo', 'companies.countries:id,name,iso2']);
    }


    public function changedBy()
    {
        return $this->belongsToMany(User::class, 'reason_role', 'role_id', 'changed_by');
    }

    public function permissions()
    {
        return $this->belongsToMany(
            ActionSubSystem::class,
            "permission_role",
            "role_id",
            "action_sub_system_id"
        )->with([
            'sub_system:id,name,scope', 'action:id,name', 'action.subActions:id,name'
        ]);
    }
}
