<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStudyModelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_study_model', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("study_model_name");
            $table->unsignedInteger('study_sub_category_id');
            // $table->foreignUuid("study_sub_category_id")->constrained('product_study_sub_category')->cascadeOnUpdate()->cascadeOnDelete(); 

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
        Schema::dropIfExists('product_study_model');
    }
}
