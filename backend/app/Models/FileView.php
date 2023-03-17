<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileView extends Model
{
    use HasFactory, UuidTrait;
    public $timestamps = false;
    protected $table = 'file_views';

    protected $fillable = ["viewable_id", "viewable_type", "user_id", "created_at"];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function filesOrFolders(){

    }
    /**
     * Get the parent viewable model (file or folder).
     */
    public function viewable()
    {
        return $this->morphTo();
    }
}
