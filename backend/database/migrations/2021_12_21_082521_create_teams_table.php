<?php

use App\Models\Team;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique();
            $table->string("name")->unique();
            $table->enum("status", Team::getTypes())->default("pending");
            $table->integer("number_of_people")->default(0);
            $table->text("note");


            $table->enum("schedule_type", ["unlimited", "limited"]);
            $table->json("date_range")->nullable();
            $table->json("time_range")->nullable();

            $table->softDeletes();
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
        Schema::dropIfExists('teams');
    }
}
