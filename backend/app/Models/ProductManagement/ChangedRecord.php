<?php

namespace App\Models\ProductManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangedRecord extends Model
{
    use HasFactory;

    protected $table = "changed_records";

    protected $fillable = ["column_name", "changes", "user_id", "changeable_type", "changeable_id"];
    public function changeables()
    {
        return $this->morphTo();
    }
}
