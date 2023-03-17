<?php

namespace App\Models\SingleSales;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;
    use UuidTrait;
    protected $table = "attachments_ssp";

    protected $fillable = ["name","size","path", "file_type", "attachmentable_type", "attachmentable_id"];

    public function attachmentable()
    {
        return $this->morphTo();
    }
    public function getPathAttributes($value)
    {
        return env("APP_URL") .$value;
    }
}
