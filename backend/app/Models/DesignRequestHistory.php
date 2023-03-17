<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DesignRequestHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "product_code",
        "product_name",
        "status",
        "priority",
        "company_id",
        "template_id",
        "order_type",
        "sales_note",
        "storybord_note",
        "design_note",
        "design_link",
        "landing_page_link",
        "updated_by",
        "created_by",
        "design_request_id",
        "file_url",
        "user_id",
        "code",
    ];

    public $table = "design_request_history";

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }
}
