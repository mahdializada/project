<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIsppMultipleQuantityStyleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ispp_multiple_quantity_style', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("ispp_id")->constrained("ispp_ssp")->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger("number_of_items");
            $table->unsignedInteger("total_price");
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
        Schema::dropIfExists('ispp_multiple_quantity_style');
    }
}
