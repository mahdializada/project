<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmStudyEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_study_evaluations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('rating');
            $table->longText('comment');
            $table->foreignUuid("study_id")->references("id")->on("pdm_studies");
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
        Schema::dropIfExists('pdm_study_evaluations');
    }
}
