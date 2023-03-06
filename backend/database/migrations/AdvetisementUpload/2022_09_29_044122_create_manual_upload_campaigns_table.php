<?php

use App\Models\AdvetisementUpload\HistoryCampaignMU;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualUploadCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_upload_campaigns', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique();
            $table->string("campaign_id")->index();
            $table->string("name");
            $table->string("server_account_id");
            $table->string("status")->nullable();
            $table->string("objective_type")->nullable();
            $table->string("budget_mode")->nullable();
            $table->float("budget")->nullable();
            $table->string("campaign_type")->nullable();
            $table->string("delivery_status")->nullable();
            $table->string("objective")->nullable();
            $table->enum('system_status', HistoryCampaignMU::$STATUSES)->default("ACTIVE");
            $table->string("start_time")->nullable();
            $table->string("end_time")->nullable();
            $table->string("server_created_at")->nullable();
            $table->string("server_updated_at")->nullable();
            $table->foreignUuid("ad_account_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('manual_upload_campaigns');
    }
}
