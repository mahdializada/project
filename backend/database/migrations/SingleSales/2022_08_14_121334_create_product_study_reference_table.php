<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStudyReferenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_study_reference', function (Blueprint $table) {
            $table->id();
            $table->string('reference_name');
            $table->text("reference_descprition");
            $table->foreignUuid('study_id')->constrained('product_study_ssp')->cascadeOnDelete()->cascadeOnUpdate(); 
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
        Schema::dropIfExists('product_study_reference');
    }
}
