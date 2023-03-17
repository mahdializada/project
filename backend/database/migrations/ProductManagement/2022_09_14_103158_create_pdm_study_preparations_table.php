<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmStudyPreparationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_study_preparations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('prepartation_type', ['landig_page', 'text', 'file']);
            $table->longText('preparationt_content');
            $table->enum('status', ['content_filled', 'no_content']);
            $table->foreignUuid("study_id")->references("id")->on("pdm_studies");
            $table->foreignUuid("action_id")->references("id")->on("pdm_actions");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_study_preparations');
    }
}
