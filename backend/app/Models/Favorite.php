<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $table = 'favorables';
    protected $fillable = ['favorable_id', 'favorable_type', 'user_id'];

    use HasFactory;
    public function favorable()
    {
        return $this->morphTo();
    }
}
