<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmProductAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('attribute_value');
            $table->foreignUuid("inventory_id")->references("id")->on("pdm_inventory");
            $table->foreignId("attribute_id")->references("id")->on("pdm_attributes");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_product_attributes');
    }
}
