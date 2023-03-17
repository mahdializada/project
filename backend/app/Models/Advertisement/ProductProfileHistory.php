<?php

namespace App\Models\Advertisement;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductProfileHistory extends Model
{
    use HasFactory;
    protected $table = 'product_profile_histories';
    protected $fillable = ['column_name', 'changed_value', 'changed_by', 'profile_id', 'profit_type'];
    public function changedBy()
    {
        return $this->belongsTo(User::class, "changed_by");
    }
}
