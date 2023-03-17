<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonChangesHistory extends Model
{
    use HasFactory;
    protected $guarded = ['created_at', 'updated_at'];

    public function changeable()
    {
        return $this->morphTo();
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
