<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySocialMedia extends Model
{
    use HasFactory;
    protected $table='company_social_media';
    protected $fillable = [
        'socialMedia_id',
        'company_id',
        'link',
        'status'

    ];


}
