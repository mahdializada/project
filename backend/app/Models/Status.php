<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ["status"];


    public $timestamps = false;

    public function statusName(){
        return $this->hasMany(Reason::class);
    }

}
