<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReasonablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reasonables', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('reason_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('reasonable_id');
            $table->string('uuid');
            $table->string('reasonable_type');
            $table->string('tab')->default('all');
            $table->string('status')->default('inactive');
            $table->string('sub_system')->default('advertisement');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('reasonables');
    }
}
