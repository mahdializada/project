<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSspInterestValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ssp_interest_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('interest_value');
            $table->foreignUuid("interest_category_id")->references("id")->on("ssp_interest_categories");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ssp_interest_values');
    }
}
