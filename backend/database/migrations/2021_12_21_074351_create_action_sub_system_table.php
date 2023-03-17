<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionSubSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_sub_system', function (Blueprint $table) {
            $table->id();

            //FOREIGN KEY CONSTRAINTS
            $table->foreignUuid('sub_system_id')->nullable()->constrained("sub_systems")->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('action_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('action_sub_system');
    }
}
