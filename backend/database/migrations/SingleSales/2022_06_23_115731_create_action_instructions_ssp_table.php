<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionInstructionsSspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_instructions_ssp', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("action_id")->constrained("actions_ssp")->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId("action_class_id")->constrained("action_classes_ssp")->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid("product_study_id")->nullable()->constrained("product_study_ssp")->cascadeOnUpdate()->nullOnDelete();
            $table->text("details")->nullable();
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
        Schema::dropIfExists('action_instructions_ssp');
    }
}
