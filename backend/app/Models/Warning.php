<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warning extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ["title", "description", "user_id"];

    /**
     * Get the user that related to the warning.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    

}