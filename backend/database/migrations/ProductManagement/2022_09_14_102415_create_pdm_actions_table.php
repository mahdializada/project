<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('actionable_type');
            $table->string('actionable_id');
            $table->enum('work_priority', ['high', 'medium', 'low']);
            $table->text('action_note');
            $table->integer('prograssive');
            $table->enum('status', ['pending', 'in_progress', 'done', 'archived', 'failed', 'not_matching', 'cancel', 'delete', 'in_hold']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_actions');
    }
}
