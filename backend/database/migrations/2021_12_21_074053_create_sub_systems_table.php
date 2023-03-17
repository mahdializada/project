<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_systems', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("name")->unique();
            $table->string("phrase");
            $table->string("scope")->nullable();
            $table->string("has_status");
            $table->boolean("has_file")->default(0);

            $table->foreignUuid('system_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();

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
        Schema::dropIfExists('sub_systems');
    }
}
