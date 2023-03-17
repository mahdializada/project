<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Supplier extends Model
{
    use HasFactory, UuidTrait, SoftDeletes;

    protected $fillable = [
        "code",
        "supplier_trading_name",
        "supplier_name",
        "email",
        "main_phone",
        "purchase_order_phone",
        "status",
        "website",
        "prepration_period",
        "supply_type",
        "commercial_type",
        "legal_type",
        "country_type",
        "note",
        // "label_id",
        "updated_by",
        "created_by"
    ];

    protected static $types = ["active", "inactive", "blocked", "pending", "removed", "warning"];

    public static function getTypes()
    {
        return Supplier::$types;
    }

    public function getCreatedAtAttribute($value): string
    {
        return Carbon::create($value)->toDateTimeString();
    }

    public function label()
    {
        return $this->belongsTo(Label::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, "updated_by");
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function sourcers()
    {
        return $this->belongsToMany(Sourcer::class, 'sourcer_supplier', 'supplier_id', 'sourcer_id');
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
    public function supplierbankaccounts()
    {
        return $this->hasMany(SupplierBankAccount::class);
    }

    // public function statuses()
    // {
    //     return $this->belongsToMany(Reason::class, "reason_supplier", "supplier_id", "reason_id")->withPivot("status", "changed_by")->withTimestamps();
    // }

    // public function changedBy()
    // {
    //     return $this->belongsToMany(User::class,"changed_by");
    // }
}
