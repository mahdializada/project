<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmTargetCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_target_countries', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("country_id")->references("id")->on("countries");
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
        Schema::dropIfExists('pdm_target_countries');
    }
}
