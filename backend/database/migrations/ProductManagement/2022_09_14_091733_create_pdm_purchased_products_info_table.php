<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmPurchasedProductsInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_purchased_products_info', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('order_no');
            $table->foreignUuid("product_requests_id")->references("id")->on("pdm_products_requests");
            $table->timestamp('arrival_time');
            $table->timestamp('purchase_date');
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
        Schema::dropIfExists('pdm_purchased_products_info');
    }
}
