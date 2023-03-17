<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmStudiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_studies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('studiable_type');
            $table->string('studiable_id');
            $table->string('study_language');
            $table->enum('work_priority', ['high', 'medium', 'low']);
            $table->text('order_note');
            $table->text('study_recommendations');
            $table->enum('target_gender', ['male', 'female', 'all']);
            $table->integer('start_target_age');
            $table->integer('end_target_age');
            $table->text('target_note');
            $table->enum('status', ['pending', 'reject', 'in_study', 'in_study_ reivew', 'study_reject', 'complete', 'in_hold', 'cancel', 'delete']);


            $table->foreignUuid("company_id")->references("id")->on("companies");
            $table->foreignUuid("study_purpose_id")->references("id")->on("pdm_study_purposes");
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
        Schema::dropIfExists('pdm_studies');
    }
}
