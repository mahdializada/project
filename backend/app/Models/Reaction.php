<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    protected $fillable = ['user_id', 'reaction_type', 'remark_id'];
    protected $hidden = ['created_at', 'updated_at'];
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
