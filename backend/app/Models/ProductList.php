<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductList extends Model
{
    use HasFactory, UuidTrait;
    protected $fillable = ['code', 'product_name', 'product_code'];

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_suppliers', 'product_list_id', 'supplier_id')->withPivot('product_cost');
    }
    public function attachment()
    {
        return $this->hasMany(ProductListAttachment::class);
    }
    public function attachments()
    {
        return $this->morphMany(CloudAttachment::class, 'attachmentable');
    }
}
