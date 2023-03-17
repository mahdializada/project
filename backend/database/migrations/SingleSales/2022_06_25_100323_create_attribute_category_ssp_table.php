<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributeCategorySspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_category_ssp', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('category_id')->constrained("categories_ssp")->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('attribute_id')->constrained("attributes_ssp")->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('attribute_category_ssp');
    }
}
