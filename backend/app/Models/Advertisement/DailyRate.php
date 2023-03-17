<?php

namespace App\Models\Advertisement;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyRate extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'daily_rate';
    protected $fillable = ['currency_from', 'currency_to', 'rate', 'status', 'created_by'];
    protected $status = ["active", "pending", 'inactive', 'deleted'];

    public function currencyFrom()
    {
        return $this->belongsTo(Currency::class, "currency_from");
    }

    public function currencyTo()
    {
        return $this->belongsTo(Currency::class, "currency_to");
    }

    public function creator()
    {
        return $this->belongsTo(User::class, "created_by");
    }
}
