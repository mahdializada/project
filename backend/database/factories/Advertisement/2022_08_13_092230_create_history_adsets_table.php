<?php

use App\Models\Advertisement\HistoryAdset;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryAdsetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_adsets', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("adset_id")->index();
            $table->string("code")->unique();
            $table->string("name");
            $table->string("server_campaign_id")->index();
            $table->string("status")->nullable();
            $table->string("delivery_status")->nullable();
            $table->float("daily_budget")->nullable();
            $table->string("currency")->nullable();
            $table->string("bid")->nullable();
            $table->string("optimization_goal")->nullable();
            $table->string("pixel_id")->nullable();
            $table->enum('system_status', HistoryAdset::$STATUSES)->default("ACTIVE");
            $table->string("start_time")->nullable();
            $table->string("end_time")->nullable();
            $table->string("server_created_at")->nullable();
            $table->string("server_updated_at")->nullable();
            $table->foreignUuid('campaign_id')->constrained("history_campaigns")->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('history_adsets');
    }
}
