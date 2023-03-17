<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSspInterestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ssp_interest', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("interest_value_id")->references("id")->on("ssp_interest_values");
            $table->string('interestable_type');
            $table->string('interestable_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ssp_interest');
    }
}
