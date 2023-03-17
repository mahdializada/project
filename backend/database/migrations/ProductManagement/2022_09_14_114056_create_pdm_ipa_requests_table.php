<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmIpaRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_ipa_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('ad_policy_violation', ['none', 'low', 'average', 'high']);
            $table->enum('work_priority', ['high', 'medium', 'low']);
            $table->boolean('is_publication_period_limited');
            $table->timestamp('publication_start_period');
            $table->timestamp('publication_end_period');
            $table->boolean('intensify_result_confirmation');
            $table->boolean('is_daily_activition_period_limited');
            $table->time('start_hour');
            $table->time('end_hour');
            $table->integer('prograssive');
            $table->enum('status', [
                'pending', 'reject', 'in_creation', 'in_testing', 'sales_moving', 'sales_unstable', 'stopped', 'under_developing', 'in_hold', 'canceld', 'delete'
            ]);
            $table->foreignUuid("ispp_request_id")->references("id")->on("pdm_ispp_requests");
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
        Schema::dropIfExists('pdm_ipa_requests');
    }
}
