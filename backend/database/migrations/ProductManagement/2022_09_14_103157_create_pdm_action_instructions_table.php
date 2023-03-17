<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmActionInstructionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_action_instructions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("action_id")->references("id")->on("pdm_actions");
            $table->foreignUuid("instuction_id")->references("id")->on("pdm_instructions");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_action_instructions');
    }
}
