<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmIsppActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_ispp_actions', function (Blueprint $table) {
            $table->id();
            $table->text('ispp_note');
            $table->enum('status', ['done', 'not_done']);
            $table->foreignUuid("action_id")->references("id")->on("pdm_actions");
            $table->foreignUuid("ispp_request_id")->references("id")->on("pdm_ispp_requests");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_ispp_actions');
    }
}
