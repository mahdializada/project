<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIsppProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ispp_products', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('ispp_id')->nullable()->constrained('ispp_ssp')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer("product_id");
            $table->enum('content_availablity', ['availabe', 'not availabe']);
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
        Schema::dropIfExists('ispp_products');
    }
}
