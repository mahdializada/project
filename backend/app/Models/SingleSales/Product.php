<?php

namespace App\Models\SingleSales;

use App\Models\CloudAttachment;
use App\Models\Company;
use App\Models\File;
use App\Models\Reason;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SingleSales\Category;
use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes, UuidTrait;
    protected $table = 'products_ssp';
    protected $fillable = ['id', 'code', 'parent_sku', 'type', 'pcode', 'name','description','arabic_name','arabic_description','quantity','cost_per_unit' ,'is_published', 'status', 'category_id', 'supplier_id', 'brand_id', 'created_by'];
    public function company(){
        return $this->belongsToMany(Company::class,'company_products','product_id','company_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function reasons()
    {
        return $this->morphToMany(Reason::class, 'reasonable')->withTimestamps();
    }

    public function attachments()
    {
        return $this->morphMany(CloudAttachment::class, 'attachmentable');
    }


    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }
    public function InventorySsp()
    {
        return $this->hasMany(InventorySsp::class);
    }
    public function quantityReservationRequests()
    {
        return $this->belongsToMany(
            QuantityReservationRequest::class,
            'quantity_reservation_products',
            'product_id',
            'quantity_reservation_id',

        )->withPivot('quantity');
    }
}
