<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmProductQuestionersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_product_questioners', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("ispp_request_id")->references("id")->on("pdm_ispp_requests");
            $table->foreignUuid("question_id")->references("id")->on("pdm_product_questions");
            $table->boolean('answer');
            $table->string('answer_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_product_questioners');
    }
}
