<?php

namespace App\Models\Advertisement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdsetTargets extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'adset_id', 'age', 'gender', 'location', 'language', 'connection_type', 'device_model', 'operation_system', 'operating_system_version', 'interest', 'targeting_expansion', 'auto_targeting'
    ];

    public function adset()
    {
        return $this->belongsTo(HistoryAdset::class, "adset_id", "server_adset_id");
    }

    public function historyAdsets()
    {
        return $this->hasMany(HistoryAdset::class, "adset_id", "server_adset_id");
    }
}
