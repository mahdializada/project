<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $table='companies';
    protected $fillable = [
        'id',
        'company_name',
        'email',
        'logo',
        'investment_type',
        'location_id',
        'status'

    ];
    public function Location()
    {
        return $this->belongsTo(Location::class);
    }

    public function socialmedias(){
        return $this->belongsToMany(
           SocialMedia::class,
           "company_social_media",
           "company_id",
            "social_media_id",
        );
    }
    public function systems(){
        return $this->belongsToMany(
           System::class,
           "company_systems",
           "company_id",
           "system_id",
        );
    }
    public function Departments()
    {
        return $this->hasMany(Department::class);
    }
}
