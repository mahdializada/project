<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileLimitionForUsers extends Model
{
    use UuidTrait, HasFactory;

    protected $table = 'file_limition_for_users';

    protected $fillable = [
        "user_id", "limited_size", "used_size", "documents_used", "videos_used", "images_used", "audios_used",
        "default_view","default_sorting" , "share_files_used"
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
