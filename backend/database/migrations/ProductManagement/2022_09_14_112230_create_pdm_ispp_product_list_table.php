<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmIsppProductListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_ispp_product_list', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid("ispp_request_id")->references("id")->on("pdm_ispp_requests");
            $table->foreignUuid("product_id")->references("id")->on("pdm_products");
            $table->enum('content_availability', ['availabe', 'not_availabe']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_ispp_product_list');
    }
}
