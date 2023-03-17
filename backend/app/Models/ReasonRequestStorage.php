<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReasonRequestStorage extends Model
{
    use HasFactory;

    protected $fillable = [
        "request_storage_id", "reason_id", "changed_by"
    ];

    public function reasons()
    {
        return $this->belongsTo(Reason::class, "reason_id");
    }

    public function changeBy()
    {
        return $this->belongsTo(User::class, "changed_by");
    }

    public function requestStorageId()
    {
        return $this->belongsTo(RequestStorage::class, 'request_storage_id');
    }
}
