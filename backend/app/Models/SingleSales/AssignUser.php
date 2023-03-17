<?php

namespace App\Models\SingleSales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignUser extends Model
{
    use HasFactory;
    protected $table = "assign_users";
    protected $guarded=['created_at','updated_at'];

    public function assignable(){
        return $this->morphTo();
    }
}
