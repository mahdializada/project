<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpaPlacementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ipa_placements', function (Blueprint $table) {
            $table->uuid("id")->primary();
            
            $table->foreignUuid('ipa_id')->constrained('ipa_ssp')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('ipa_add_placement_id')->constrained('ipa_add_placements')->cascadeOnUpdate()->cascadeOnDelete();

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
        Schema::dropIfExists('ipa_placements');
    }
}
