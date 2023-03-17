<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    use HasFactory;
    protected $fillable = ['remark','sub_system', 'remarkable_id', 'remarkable_type', 'user_id', "type"];

    public function remarkable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
    public function reactions()
    {
        return $this->hasMany(Reaction::class, "remark_id");
    }
}
