<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;
use App\Models\Label;

class LabelCategory extends Model
{
    use HasFactory,UuidTrait;
    protected $fillable = ["title"];

     public function label()
    {
        return $this->hasMany(Label::class);
    }
}
