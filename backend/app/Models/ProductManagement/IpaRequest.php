<?php

namespace App\Models\ProductManagement;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\Request;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class IpaRequest extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "pdm_ipa_requests";

    protected $fillable = [
        "ad_policy_violation", "work_priority", "is_publication_period_limited", "publication_start_period",
        "publication_end_period", "intensify_result_confirmation", "is_daily_activition_period_limited",
        "start_hour", "end_hour", "prograssive", "status", "ispp_request_id"
    ];

    /**
     * Get the isppRequest that owns the IpaRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function isppRequest(): BelongsTo
    {
        return $this->belongsTo(Request::class, 'ispp_request_id');
    }

    /**
     * The adPlacemnets that belong to the IpaRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function adPlacemnets(): BelongsToMany
    {
        return $this->belongsToMany(AdPlacement::class, 'pdm_ipa_ad_placemnets', 'ipa_request_id', 'ad_placement_id');
    }
}
