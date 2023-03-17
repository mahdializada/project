<?php

namespace App\Models\Advertisement;

use App\Models\Label;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Labelable extends Model
{
    use HasFactory;
    // private $table = "labelables";

    protected $fillable = [
        "label_id", "labelable_id", 'labelable_type', "created_by", "created_at", 'status','sub_system', 'current_status', 'type'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function label()
    {
        return $this->belongsTo(Label::class, "label_id");
    }
}
