<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    protected $table='teams';
    protected $fillable = [
        'team_name',
        'logo',
        'note',
        'status',
        'department_id'
        
    ]; 
    public function department(){
        return $this->belongsTo(Department::class);
    }
    public function users(){
        return $this->belongsToMany(
            User::class,
            "team_members",
            "team_id",
            "user_id",
            
        );
    }
    public function roles(){
        return $this->belongsToMany(
            Team::class,
            "team_roles",
            "team_id",
            "role_id",
            
        );
    }

}
