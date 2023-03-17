<?php

namespace App\Models\ProductManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformTargeting extends Model
{
    use HasFactory;

    protected $table = "pdm_platform_targeting";

    protected $fillable = [
        "target_gender", "start_target_age", "end_target_age", "target_note",
        "budget_type", "budget_time_type", "budget",
        "target_cost_per_order", "platform_id", "ipa_request_id"
    ];
}
