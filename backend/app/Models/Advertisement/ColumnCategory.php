<?php

namespace App\Models\Advertisement;

use App\Models\SubSystem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;

class ColumnCategory extends Model
{
    protected $guarded = [];
    use HasFactory, SoftDeletes;
    public function subSystems()
    {
        # code...
        return $this->belongsToMany(
            SubSystem::class,
            'column_category_sub_systems',
        );
    }
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}
