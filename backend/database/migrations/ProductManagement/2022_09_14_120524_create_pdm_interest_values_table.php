<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmInterestValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_interest_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('interest');
            $table->foreignUuid("interest_category_id")->references("id")->on("pdm_interest_categories");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_interest_values');
    }
}
