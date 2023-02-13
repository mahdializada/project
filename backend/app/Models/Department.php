<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $table='departments';
    protected $fillable = [
        'company_id',
        'logo',
        'department_name',
        'status'

    ];
    public function Company()
    {
        return $this->belongsTo(Company::class);
    }
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            "user_departments",
            "department_id", 
            "user_id",
        );
    }
    public function teams(){
        return $this->hasMany(Team::class);
    }
    public function roles(){
        return $this->belongsToMany(
            Role::class,
            "department_roles",
            "department_id",
            "role_id",
        );
    }
}
