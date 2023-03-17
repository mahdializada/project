<?php

namespace App\Models\Advertisement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSystemTab extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    use HasFactory;
    public function columns()
    {
        # code...
        return $this->hasMany(TabColumn::class, 'tab_id');
    }
}
