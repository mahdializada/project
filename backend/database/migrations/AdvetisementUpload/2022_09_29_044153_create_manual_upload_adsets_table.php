<?php

use App\Models\AdvetisementUpload\HistoryAdsetMU;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualUploadAdsetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_upload_adsets', function (Blueprint $table) {
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
            $table->enum('system_status', HistoryAdsetMU::$STATUSES)->default("ACTIVE");
            $table->string("start_time")->nullable();
            $table->string("end_time")->nullable();
            $table->string("server_created_at")->nullable();
            $table->string("server_updated_at")->nullable();
            $table->foreignUuid('campaign_id')->constrained("manual_upload_campaigns")->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('manual_upload_adsets');
    }
}
