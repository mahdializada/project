<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;

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
        return Team::$types;
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class);
    }


    public function reasons()
    {
        return $this->belongsToMany(Reason::class, 'reason_team', 'team_id', 'reason_id')->withPivot('status', 'changed_by')->withTimestamps();
    }


    public function changedBy()
    {
        return $this->belongsToMany(User::class, 'reason_team', 'team_id', 'changed_by');
    }

    public function departmentCompanyCountry()
    {
        return $this->departments()->with(['companies:id,name,logo', 'companies.countries:id,name,iso2']);
    }

    public function permissions()
    {
        return $this->belongsToMany(
            ActionSubSystem::class,
            "permission_team",
            "team_id",
            "action_sub_system_id"
        )
            ->with(['sub_system:id,name,scope', 'action:id,name', 'action.subActions:id,name']);
    }

    // public function permissions(){
    //     return
    // }


    // public function reasonStatusTeam()
    // {
    //     return $this->belongsToMany(ReasonStatus::class, "reason_status_team", "team_id", "reason_status_id")->withPivot('created_at', 'updated_at');
    // }

    // public function changes()
    // {
    //     return $this->belongsToMany(User::class, 'reason_status_team', 'team_id', 'changed_by');
    // }

    // public function reasonAndStatus()
    // {
    //     return $this->reasonStatusTeam()->with(['status', 'reason']);
    // }
}
