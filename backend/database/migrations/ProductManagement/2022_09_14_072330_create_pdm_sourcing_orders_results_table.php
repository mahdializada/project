<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmSourcingOrdersResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_sourcing_orders_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('available_quantities');
            $table->double('cost');
            $table->enum('shipping_method', ['air', 'land', 'sea']);
            $table->timestamp('delivery_time');
            $table->enum('quantity_model', ['whole_sale', 'retail']);
            $table->double('shipping_cost');
            $table->text('product_note');
            $table->text('import_restrictions');
            $table->text('import_note');
            $table->text('sourcing_note');
            $table->integer('progressive');

            $table->foreignUuid("sourcing_order_product_id")->references("id")->on("pdm_sourcing_order_products");
            $table->uuid("supplier_id")->nullable();
            $table->foreign("supplier_id")->references("id")->on("suppliers_ssp");
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
        Schema::dropIfExists('pdm_sourcing_orders_results');
    }
}
