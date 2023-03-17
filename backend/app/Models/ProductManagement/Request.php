<?php

namespace App\Models\ProductManagement;

use App\Models\Company;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductManagement\Currency;
use App\Models\SingleSales\ProductSourceValue;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Request extends Model
{
    use HasFactory, UuidTrait;

    protected $table = "pdm_ispp_requests";

    protected $casts = [
        'display_languages' => 'array',
    ];

    protected $fillable = [
        "working_type", "work_priority", "is_group", "products_availability", "available_quantity",
        "is_product_seasonal", "season_or_event_name", "individual_sale_note", "products_note",
        "display_languages", "store_channel_note", "head_selling_prize", "financial_info_note",
        "status", "company_id", "product_source_value_id", "currency_id",
    ];

    /**
     * Get the company that owns the Request
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }


    /**
     * Get the productSourceValue that owns the Request
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productSourceValue(): BelongsTo
    {
        return $this->belongsTo(ProductSourceValue::class, 'product-source_value_id');
    }
    /**
     * Get the currency that owns the Request
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * The roles that belong to the Request
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function channelTemplates(): BelongsToMany
    {
        return $this->belongsToMany(SalesChannelTemplate::class, 'pdm_ispp_channels', 'ispp_request_id', 'sales_channel_template_id');
    }

    public function sellingGoals(): BelongsToMany
    {
        return $this->belongsToMany(SellingGoal::class, 'pdm_ispp_selling_goals', 'ispp_request_id', 'goal_id');
    }
}
