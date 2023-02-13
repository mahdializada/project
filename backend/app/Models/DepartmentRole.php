<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentRole extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table='department_roles';
    protected $fillable = [
        'department_id',
        'role_id'
        
    ];
}
