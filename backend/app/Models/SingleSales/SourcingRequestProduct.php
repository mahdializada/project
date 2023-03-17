<?php

namespace App\Models\SingleSales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourcingRequestProduct extends Model
{

    use HasFactory;
    protected $guarded = ['created_at', 'updated_at'];
    protected $table = 'sourcing_request_products';
}
