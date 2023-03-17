<?php

namespace App\Models\Advertisement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Advertisement\AdAccount;

class AdAccountBalance extends Model
{
    use HasFactory;
    protected $table = "ad_account_balances";
    protected $fillable = ['balance', 'created_by', 'add_account_id', 'currency', 'payment_type', 'type'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }
    public function adAccount()
    {
        return $this->belongsTo(AdAccount::class, 'add_account_id');
    }
}
