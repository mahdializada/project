<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignRequestOrderUser extends Model
{
    use HasFactory;
    protected $table = 'design_request_order_user';
    protected $appends = ['user_can_edit'];

    protected $fillable = [
        "user_id", "design_request_order_id",
        "updated_by", "created_by", "task_prograss"
    ];

    public function getUserCanEditAttribute()
    {
        $authCan = auth()->user()->tokenCan("drva");
        $userCanEdit = $authCan || $this->attributes["user_id"] == auth()->id();
        return $userCanEdit;
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function designRequestOrder()
    {
        return $this->belongsTo(DesignRequestOrder::class);
    }
}
