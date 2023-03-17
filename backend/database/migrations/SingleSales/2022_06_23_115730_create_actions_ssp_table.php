<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsSspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions_ssp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->nullOnDelete()->cascadeOnUpdate();
            $table->string('code')->unique();
            $table->string('actionable_type');
            $table->uuid('actionable_id');
            $table->enum('work_priority', ['high', 'low']);
            $table->enum('status', ['pending', 'in progress', 'done', 'archived', 'failed', 'not matching', 'cancelled', 'in hold'])->default('pending');
            $table->text('action_note')->nullable();
            $table->string('landing_page_url')->nullable();
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
        Schema::dropIfExists('action_ssp');
    }
}
