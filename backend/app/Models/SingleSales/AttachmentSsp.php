<?php

namespace App\Models\SingleSales;

use App\Models\TempFile;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AttachmentSsp extends Model
{
    use HasFactory,UuidTrait;
    protected $table="attachments_ssp";
    protected $guarded = ['created_at','updated_at'];

    public function attachmentable():MorphTo{
        return $this->morphTo();
    }

}
