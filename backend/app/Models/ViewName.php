<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewName extends Model
{

    use UuidTrait;

    protected $fillable = ["name", "columns", 'default', "page_name", "scope_type", "user_id", "company_id"];

    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getDefaultAttribute($value)
    {
        # code...
        return  $this->user_id == auth()->id() ? $value : false;
    }
    public function getColumnsAttribute($value)
    {
        return json_decode($value);
    }
}
