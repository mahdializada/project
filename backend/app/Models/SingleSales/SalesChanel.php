<?php

namespace App\Models\SingleSales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesChanel extends Model
{
    use HasFactory;
    protected $table="sales_chanels";
    protected $guarded = ['created_at', 'updated_at'];

    public function parent()
    {
        return $this->belongsTo(SalesChanel::class, 'parent_id');
    }
}
