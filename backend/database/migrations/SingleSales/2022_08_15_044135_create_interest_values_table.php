<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterestValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interest_values', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string('interest');
            $table->foreignUuid('category_id')->constrained('categories_ssp')->cascadeOnDelete()->cascadeOnUpdate(); 
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
        Schema::dropIfExists('interest_values');
    }
}
