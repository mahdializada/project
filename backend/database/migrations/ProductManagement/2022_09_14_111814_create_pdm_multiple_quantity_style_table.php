<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmMultipleQuantityStyleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_multiple_quantity_style', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("ispp_request_id")->references("id")->on("pdm_ispp_requests");
            $table->integer('number_of_items');
            $table->double('total_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_multiple_quantity_style');
    }
}
