<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmIsppChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_ispp_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("sales_channel_template_id")->references("id")->on("pdm_sales_channel_templates");
            $table->foreignUuid("ispp_request_id")->references("id")->on("pdm_ispp_requests");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_ispp_channels');
    }
}
