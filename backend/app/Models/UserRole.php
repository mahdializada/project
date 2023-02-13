<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table='user_roles';
    protected $fillable = [
        'role_id',
        'user_id'
        
    ]; 
}
