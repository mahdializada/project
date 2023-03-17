<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_inventory', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("sku")->unique();
            $table->string("pcode")->unique();
            $table->bigInteger('quantity');
            $table->bigInteger('price_per_unit');
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
        Schema::dropIfExists('pdm_inventory');
    }
}
