<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileDownload extends Model
{
    use HasFactory, UuidTrait;

    protected $table = 'file_downloads';
    public $timestamps = false;

    protected $fillable = ["downloadable_id", "downloadable_type", "user_id", "created_at"];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the parent downloadable model (file or folder).
     */
    public function downloadable()
    {
        return $this->morphTo();
    }
}
