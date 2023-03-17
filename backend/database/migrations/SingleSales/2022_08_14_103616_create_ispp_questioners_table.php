<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIsppQuestionersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ispp_questioners', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('ispp_id')->nullable()->constrained('ispp_ssp')->cascadeOnUpdate()->cascadeOnUpdate();
            $table->foreignId('question_id')->nullable()->constrained('questions_ssp')->cascadeOnUpdate()->cascadeOnUpdate();
            $table->boolean("answer")->nullable();
            $table->text("answer_details")->nullable();
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
        Schema::dropIfExists('ispp_questioners');
    }
}
