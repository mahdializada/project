<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatformBudgetSspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform_budget_ssp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ipa_id')->constrained('ipa_ssp')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('platform_name');
            $table->json('platform_placement')->nullable();
            $table->string('budget');
            $table->string('target_cpo')->nullable();
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
        Schema::dropIfExists('platform_budgets_ssp');
    }
}
