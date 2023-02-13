<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    use HasFactory;

    protected $fillable=['system_name'];
    public function comapies(){
        return $this->belongsToMany(
           Company::class,
           "company_systems",
           "system_id",
           "company_id",
        );
    }
}
