<?php

namespace App\Models;

use App\Traits\UuidTrait;
use App\Models\DesignRequestOrderFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DesignRequestOrder extends Model
{
    use HasFactory, UuidTrait;


    protected $fillable = [
        "order_type", "sales_note", "storybord_note",
        "design_note", "design_link", "landing_page_link",
        "updated_by", "created_by", "design_request_id", "file_url",
    ];


    public function getFileUrlAttribute($value): string
    {
        if($value){
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            }
            return  env("APP_URL") . Storage::url($value) ;
        }
        return "";
    }


    static public function getOrderType()
    {
        return ["scratch", "copy", "mix"];
    }

    /**
     * Get all of the designRequests for the DesignRequestOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function designRequest(): BelongsTo
    {
        return $this->belongsTo(DesignRequest::class);
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'design_request_order_user',
            'design_request_order_id',
            'user_id',
        );
    }

    public function files(){
        return $this->hasMany(DesignRequestOrderFile::class, 'design_request_order_id');
    }


    public function designRequestOrderUser()
    {
        return $this->hasMany(DesignRequestOrderUser::class, 'design_request_order_id');
    }
}
