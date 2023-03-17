<?php

namespace App\Models\ProductManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\Action;
use App\Models\ProductManagement\Request;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IsppAction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "pdm_ispp_actions";

    protected $fillable = ["ispp_note", "status", "action_id", "ispp_request_id"];

    /**
     * Get the action that owns the IsppAction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class, 'action_id');
    }

    /**
     * Get the request that owns the IsppAction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class, 'ispp_request_id');
    }
}
