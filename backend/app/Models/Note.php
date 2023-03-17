<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ['note', 'creator_id', "updated_by"];

    public function creator()
    {
        return $this->belongsTo(User::class, "creator_id");
    }


    public function users()
    {
        return $this->belongsToMany(User::class);
    }


}
