<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmFormComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_form_components', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['short_text', 'long_text']);
            $table->longText('type_code'); //html code
            $table->string('label_name');
            $table->foreignUuid("study_model_id")->references("id")->on("pdm_study_model");
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
        Schema::dropIfExists('pdm_form_components');
    }
}
