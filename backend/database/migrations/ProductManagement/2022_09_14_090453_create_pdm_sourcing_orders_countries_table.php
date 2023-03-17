<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmSourcingOrdersCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_sourcing_orders_countries', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("sourcing_order_id")->references("id")->on("pdm_sourcing_orders");
            $table->foreignUuid("country_id")->references("id")->on("countries");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_sourcing_orders_countries');
    }
}
