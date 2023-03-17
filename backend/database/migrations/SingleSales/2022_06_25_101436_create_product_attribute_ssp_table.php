<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAttributeSspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_attribute_ssp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->foreignUuid('product_id')->constrained("products_ssp")->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('attribute_id')->constrained("attributes_ssp")->cascadeOnUpdate()->cascadeOnDelete();
            // $table->foreignUuid('product_inventory_id')->constrained("product_inventory_ssp")->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('attribute_value')->nullable();
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
        Schema::dropIfExists('product_attribute_ssp');
    }
}
