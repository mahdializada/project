<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIsppSellingGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ispp_selling_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('ispp_id')->nullable()->constrained('ispp_ssp')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignid('selling_goal_id')->constrained("selling_goals")->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('ispp_selling_goals');
    }
}
