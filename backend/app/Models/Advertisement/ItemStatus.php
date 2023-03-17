<?php

namespace App\Models\Advertisement;

use App\Models\User;
use App\Traits\UuidTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemStatus extends Model
{
    use HasFactory, UuidTrait;
    protected $fillable = ['item_code', 'country_id', 'item_status', 'isActive','end_date', 'color', 'created_by', 'created_at'];
    public function getCreatedAtAttribute($value): string
    {
        return Carbon::create($value)->toDateTimeString();
    }
    public function getEndDateAttribute($value)
    {
        if(!$value)
            $value = date('Y-m-d H:i:s');
            $dateTime = Carbon::parse($value);
            $d = $dateTime->subHours(4);
            return $d->format('Y-m-d H:i:s');
    }
    public function user()
    {
        return $this->belongsTo(User::class, "created_by");
    }

}
