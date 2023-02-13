<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamRole extends Model
{
    use HasFactory;
    protected $table='team_roles';
    protected $fillable = [
        'team_id',
        'role_id'
        
    ]; 
}
