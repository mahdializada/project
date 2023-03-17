<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmIsppStudiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_ispp_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("study_id")->references("id")->on("pdm_studies");
            $table->foreignUuid("ispp_request_id")->references("id")->on("pdm_ispp_requests");
            $table->text('study_note');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_ispp_studies');
    }
}
