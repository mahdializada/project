<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmSourcingOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_sourcing_order_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('approx_quantity');
            $table->double('target_cost');
            $table->text('variation_note');
            $table->text('product_note');
            $table->longText('reference');
            $table->enum('status', ['canceled', 'deleted', 'to_be_found']);

            $table->foreignUuid("sourcing_order_id")->references("id")->on("pdm_sourcing_orders");
            $table->foreignUuid("product_id")->references("id")->on("pdm_products");
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
        Schema::dropIfExists('pdm_sourcing_order_products');
    }
}
