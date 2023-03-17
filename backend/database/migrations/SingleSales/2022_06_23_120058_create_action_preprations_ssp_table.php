<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionPreprationsSspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_preprations_ssp', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('action_id')->constrained('actions_ssp')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string("content_request_id");
            $table->foreignId("action_class_id")->constrained("action_classes_ssp")->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('action_preprations_ssp');
    }
}
