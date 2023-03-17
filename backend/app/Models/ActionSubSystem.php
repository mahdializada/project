<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionSubSystem extends Model
{
    use HasFactory;


    protected $fillable = ["action_id", "sub_system_id"];
    protected $hidden = ["pivot"];

    protected $table = "action_sub_system";

    public $timestamps = false;


    public function sub_system()
    {
        return $this->belongsTo(SubSystem::class);
    }

    public function action()
    {
        return $this->belongsTo(Action::class);
    }

    public function actions()
    {
        return $this->belongsToMany(Action::class);
    }

    public function teams()
    {
        return $this->belongsToMany(
            Team::class,
            'permission_team',
            'action_sub_system_id',
            'team_id',
        );
    }

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'permission_role',
            'action_sub_system_id',
            'role_id',
        );
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'permission_user',
            'action_sub_system_id',
            'user_id',
        );
    }
}
