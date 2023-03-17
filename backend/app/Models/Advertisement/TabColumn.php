<?php

namespace App\Models\Advertisement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabColumn extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(ColumnCategory::class, 'category_id');
    }
}
