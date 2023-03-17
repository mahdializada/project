<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierBankAccount extends Model
{
    use HasFactory;
    protected $table="table_supplier_bank_accounts";
    protected $fillable = ['id','bank_name','bank_account_name','bank_account_number',
    'bank_account_number_iban','swift_code','address','supplier_id','created_at','updated_at'];
    public function supplier(){
        return $this->BelongsTo(Supplier::class);
     }
}
