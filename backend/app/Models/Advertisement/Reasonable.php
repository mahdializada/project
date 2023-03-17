<?php

namespace App\Models\Advertisement;

use App\Models\Reason;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reasonable extends Model
{
    use HasFactory;

    public function reasons()
    {
        return $this->belongsTo(Reason::class, "reason_id");
    }

    public function reasonable()
    {
        return $this->morphTo();
    }
    public function creator()
    {
        return $this->belongsTo(User::class, "created_by");
    }
}
