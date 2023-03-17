<?php

use Illuminate\Support\Facades\Schema;
use App\Models\Advertisement\HistoryAd;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_ads', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique();
            $table->string("ad_id")->index();
            $table->string("ad_name");
            $table->string("server_adset_id")->index();
            $table->string("server_account_id")->index();
            $table->string("status")->nullable();
            $table->string("delivery_status")->nullable();
            $table->float("result")->nullable();
            $table->float("impressions")->nullable();
            $table->float("viewable_impressions")->nullable();
            $table->float("two_second_video_views")->nullable();
            $table->float("six_second_video_views")->nullable();
            $table->float("average_screen_time")->nullable();
            $table->float("video_views")->nullable();
            $table->float("view_completion")->nullable();
            $table->float("spend")->nullable();
            $table->float("clicks")->nullable();
            $table->float("total_page_views")->nullable();
            $table->float("story_opens")->nullable();
            $table->string("currency")->nullable();
            $table->float("reach")->nullable();
            $table->float("cost_per_fifteen_sec_video_view")->nullable();
            $table->string("frequency")->nullable();
            $table->string("objective")->nullable();
            $table->string("optimization_goal")->nullable();
            $table->enum('system_status', HistoryAd::$STATUSES)->default("ACTIVE");

            // CRM
            $table->integer("crm_total_orders")->default(0);
            $table->integer("crm_confirm")->nullable();
            $table->integer("crm_repeated")->nullable();
            $table->integer("crm_cancelled")->nullable();
            $table->integer("crm_pickedup")->nullable();
            $table->integer("crm_total_pickedup")->nullable();
            $table->integer("crm_logis_deliverd")->nullable();
            $table->integer("crm_logis_cancelled")->nullable();
            $table->integer("crm_total_sale")->nullable();
            $table->float("crm_product_cost")->nullable();
            $table->float("crm_delivery_cost")->nullable();

            // Google Analytics
            $table->integer("ga_total_users")->nullable();
            $table->integer("ga_new_users")->nullable();
            $table->integer("ga_user_engagement")->nullable();
            $table->integer("ga_sessions")->nullable();
            $table->integer("ga_engaged_sessions")->nullable();
            $table->integer("ga_page_views")->nullable();

            $table->string("start_time")->nullable();
            $table->string("end_time")->nullable();
            $table->string("server_created_at")->nullable();
            $table->string("server_updated_at")->nullable();
            $table->foreignUuid('adset_id')->constrained("history_adsets")->cascadeOnUpdate()->cascadeOnDelete();
            $table->date("data_date")->index();
            $table->timestamp("data_timestamp")->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_ads');
    }
}
