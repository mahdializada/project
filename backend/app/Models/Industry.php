<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Industry extends Model
{
    use HasFactory, UuidTrait;
    protected $fillable = ['name'];


    protected $hidden = ["pivot"];


    public function departments()
    {
        return $this->belongsToMany(Department::class, "department_industry");
    }
}
