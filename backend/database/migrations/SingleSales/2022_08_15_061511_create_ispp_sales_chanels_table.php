<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIsppSalesChanelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ispp_sales_chanels', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("ispp_id")->constrained("ispp_ssp")->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId("sales_chanel_id")->constrained()->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('ispp_sales_chanels');
    }
}
