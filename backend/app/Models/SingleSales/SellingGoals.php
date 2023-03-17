<?php

namespace App\Models\SingleSales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellingGoals extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'selling_goals';
    protected $fillable = ['id','goals'];
}
