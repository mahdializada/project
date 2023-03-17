<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLabelablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labelables', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('label_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('labelable_id');
            $table->string('labelable_type');
            $table->string('sub_system');
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate()->before('created_at');

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
        Schema::dropIfExists('labelables');
    }
}
