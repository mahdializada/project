<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileShare extends Model
{
    use UuidTrait, HasFactory;

    protected $table = 'file_shares';

    protected $fillable = [
        "shared_by", "shared_to", "shareable_id",
        "shareable_type", "share_type", "date_range",
        "permission", "seen"
    ];



    public function sharedTo()
    {
        return $this->belongsTo(User::class, 'shared_to');
    }

    public function shatedBy()
    {
        return $this->belongsTo(User::class, 'shared_by');
    }

    /**
     * Get the parent shareable model (file or folder).
     */
    public function shareable()
    {
        return $this->morphTo();
    }
}
