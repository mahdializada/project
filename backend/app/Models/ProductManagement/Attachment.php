<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;
    use UuidTrait;

    protected $table = "pdm_attachments";

    protected $fillable = ["path", "file_type", "attachmentable_type", "attachmentable_id"];

    public function attachmentable()
    {
        return $this->morphTo();
    }
    public function getPathAttributes($value)
    {
        return env("APP_URL") .$value;
    }

}
