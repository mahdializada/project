<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $fillable = [
        'role',
        'status'
        
    ]; 
    public function teams(){
        return $this->belongsToMany(
            Team::class,
            "team_roles",
            "role_id",
            "team_id",
            
        );
    }
    public function Departments(){
        return $this->belongsToMany(
            Departments::class,
            "department_roles",
            "role_id",
            "department_id",
        );
    }
    public function users(){
        return $this->belongsToMany(
            User::class,
            "user_roles",
            "role_id",
            "user_id",
        );
    }
}
