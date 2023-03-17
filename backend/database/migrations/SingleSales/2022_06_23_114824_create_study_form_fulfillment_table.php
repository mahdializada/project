<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudyFormFulfillmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_study_form_fulfillment', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->text('study_form_answer'); 
            $table->foreignUuid("study_form_components_id")->constrained('product_study_form_components')->cascadeOnUpdate()->cascadeOnDelete(); 
           
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
        Schema::dropIfExists('study_form_fulfillment');
    }
}
