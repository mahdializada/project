<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIsppTargetSaleCountriesSsp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ispp_target_sale_countries_ssp', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('ispp_id')->constrained('ispp_ssp')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('ispp_target_sale_countries_ssp');
    }
}
