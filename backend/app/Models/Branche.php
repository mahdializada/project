<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branche extends Model
{
    use HasFactory;
    protected $table='branches';
    protected $fillable = [
        'company_id',
        'location_id',
        'branche_name'
        
    ]; 
    public Function Company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public Function LocationBrunche()
    {
        return $this->belongsTo(Location::class);
    }

}
