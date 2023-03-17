<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpaAddPlatform extends Model
{
    use HasFactory,  UuidTrait; 
    protected $fillable = ['Platform_name','is_active'];
    
}
