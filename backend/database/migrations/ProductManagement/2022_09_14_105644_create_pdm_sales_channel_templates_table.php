<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmSalesChannelTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_sales_channel_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->longText('template');
            $table->foreignUuid("sales_channel_id")->references("id")->on("pdm_sales_channels");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_sales_channel_templates');
    }
}
