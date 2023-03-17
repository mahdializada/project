<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReasonSubSystem extends Model
{
    use HasFactory;
    protected $table = 'reason_sub_system';
    protected $fillable = ["type", "sub_system_id", 'reason_id'];



    public function subsystem()
    {
        return $this->belongsTo(SubSystem::class, 'sub_system_id');
    }
    public function reasonTypes()
    {
        return $this->hasMany(ReasonType::class, 'reason_sub_system_id');
    }

    public function reason()
    {
        return $this->belongsTo(Reason::class,'reason_id');
    }
}
