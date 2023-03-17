<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmIsppProductAdditionalInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_ispp_product_additional_info', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['description', 'product_content_and_advertisement_reference', 'financial_informationa_and_suggestion', 'competitor_advertiser_reference']);
            $table->text('reference_text');
            $table->foreignUuid("ispp_product_list_id")->references("id")->on("pdm_ispp_product_list");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_ispp_product_additional_info');
    }
}
