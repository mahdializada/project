<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department_team', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('team_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('department_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('department_team');
    }
}
