<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmTargetCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_target_cities', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("city_id")->references("id")->on("states");
            $table->string('targetable_type');
            $table->string('targetable_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_target_cities');
    }
}
