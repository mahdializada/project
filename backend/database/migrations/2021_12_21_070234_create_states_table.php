<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('states', function (Blueprint $table) {
            $table->uuid("id")->primary();

            $table->string("name");
            $table->string("state_code");
            $table->string("latitude")->nullable();
            $table->string("longitude")->nullable();

            //FOREIGN KEY CONSTRAINTS
            $table->foreignUuid('country_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();

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
        Schema::dropIfExists('states');
    }
}
