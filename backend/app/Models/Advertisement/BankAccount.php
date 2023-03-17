<?php

namespace App\Models\Advertisement;

use App\Models\User;
use App\Models\Advertisement\AdAccount;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;
   protected  $fillable=['card_number','owner','bank_name','user_id','add_account_id'];
   public function user()
   {
       return $this->belongsTo(User::class);
   }
   public function adAccount()
   {
       return $this->belongsTo(AdAccount::class,'add_account_id');
   }
   public function getCreatedAtAttribute($value): string
    {
        return Carbon::create($value)->toDateString();
    }
    public function getUpdatedAtAttribute($value): string
    {
        return Carbon::create($value)->toDateString();
    }
   
}
